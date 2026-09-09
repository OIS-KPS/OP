<?php

session_start();
/* Load the database connection from the actual project structure. */
$dbCandidates = [
    __DIR__ . '/config/db.php',
    __DIR__ . '/src/config/db.php',
    __DIR__ . '/src/pages/config/db.php',
];
$dbLoaded = false;
foreach ($dbCandidates as $dbFile) {
    if (is_file($dbFile)) {
        require_once $dbFile;
        $dbLoaded = true;
        break;
    }
}
if (!$dbLoaded) {
    http_response_code(500);
    exit('Database configuration file was not found. Expected config/db.php or src/config/db.php.');
}
/* Support projects whose db.php exposes the PDO connection as $conn. */
if (!isset($pdo) && isset($conn) && $conn instanceof PDO) {
    $pdo = $conn;
}
if (!isset($pdo) || !($pdo instanceof PDO)) {
    http_response_code(500);
    exit('Database connection is not available. Please check config/db.php.');
}
/* ============================================================
 * 1. AUTHENTICATION
 * ============================================================ */
if (!isset($_SESSION['user_id'])) {
    header('Location: /ICS-PORTAL/auth/login.php');
    exit;
}
$userId = (int)$_SESSION['user_id'];
$reportId = filter_input(INPUT_GET, 'report_id', FILTER_VALIDATE_INT);
if (!$reportId) {
    http_response_code(400);
    exit('Invalid report ID.');
}
/* ============================================================
 * 2. LOAD REPORT
 * ============================================================
 *
 * IMPORTANT: This query matches the actual database schema.
 * The reports table contains file_path and submitted_at.
 * It does NOT contain attachment_path or created_at.
 * ============================================================ */
try {
    $sql = "
        SELECT
            r.id,
            r.student_id,
            r.week_number,
            r.file_path,
            r.ocr_activities,
            r.supervisor_remarks,
            r.status,
            r.submitted_at,
            s.student_number,
            s.program,
            s.section,
            u.name AS student_name
        FROM reports r
        INNER JOIN students s
            ON s.id = r.student_id
        INNER JOIN users u
            ON u.id = s.user_id
        WHERE r.id = :report_id
          AND s.user_id = :user_id
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':report_id' => $reportId,
        ':user_id' => $userId
    ]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('review_report report query: ' . $e->getMessage());
    http_response_code(500);
    exit('Database error while loading the report.');
}
if (!$report) {
    http_response_code(404);
    exit('Report not found or you do not have permission to view it.');
}
/* ============================================================
 * 3. DETERMINE PDF URL
 * ============================================================ */
$filePath = trim((string)($report['file_path'] ?? ''));
function buildPdfUrl(string $filePath): string
{
    $path = trim(str_replace('\\', '/', $filePath));
    if ($path === '') {
        return '';
    }
    // Already an external URL.
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    // Convert a Windows path inside the project to a browser path.
    $projectRoot = str_replace('\\', '/', __DIR__);
    $normalizedProjectRoot = rtrim($projectRoot, '/');
    $lowerPath = strtolower($path);
    $lowerRoot = strtolower($normalizedProjectRoot);
    if (str_starts_with($lowerPath, $lowerRoot . '/')) {
        $relative = substr($path, strlen($normalizedProjectRoot) + 1);
        return '/ICS-PORTAL/' . ltrim($relative, '/');
    }
    // If the stored path already contains the project folder, do not
    // prepend /ICS-PORTAL/ twice.
    if (preg_match('#^/?ICS-PORTAL/#i', $path)) {
        return '/' . ltrim($path, '/');
    }
    // Already an application-rooted browser path.
    if (str_starts_with($path, '/')) {
        return $path;
    }
    // Common database value: uploads/reports/file.pdf
    return '/ICS-PORTAL/' . ltrim($path, '/');
}
$pdfUrl = buildPdfUrl($filePath);
/* ============================================================
 * 4. RUN PYTHON EXTRACTION AND LOAD ENTITIES
 * ============================================================ */

function rr_local_pdf_path(string $filePath): string
{
    $path = trim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filePath));
    if ($path === '') return '';

    $projectRoot = __DIR__;
    $candidates = [
        $path,
        $projectRoot . DIRECTORY_SEPARATOR . ltrim($path, '/\\'),
    ];

    foreach ($candidates as $candidate) {
        $realPath = realpath($candidate);
        if ($realPath && is_file($realPath)) return $realPath;
    }

    return '';
}

function rr_run_python_extractor(string $pdfPath): array
{
    $scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'python' . DIRECTORY_SEPARATOR . 'entity_extractor.py';
    if (!is_file($scriptPath)) {
        throw new RuntimeException('Python extractor was not found at /ICS-PORTAL/python/entity_extractor.py.');
    }

    $localPdf = rr_local_pdf_path($pdfPath);
    if ($localPdf === '') {
        throw new RuntimeException('The submitted PDF file could not be found on the server.');
    }

    $pythonBinary = getenv('PYTHON_BINARY') ?: (PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3');
    $command = escapeshellarg($pythonBinary) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($localPdf);
    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptorSpec, $pipes, __DIR__);
    if (!is_resource($process)) throw new RuntimeException('Unable to start the Python extractor.');

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    $result = json_decode(trim((string)$stdout), true);
    if (!is_array($result)) {
        throw new RuntimeException('The Python extractor returned invalid JSON: ' . trim((string)$stderr));
    }
    if ($exitCode !== 0 || empty($result['success'])) {
        throw new RuntimeException((string)($result['error'] ?? trim((string)$stderr) ?: 'Python entity extraction failed.'));
    }

    return is_array($result['entities'] ?? null) ? $result['entities'] : [];
}

$entities = [];

/**
 * Load the entity catalog from the actual predefined_entities table.
 *
 * The SQL schema defines:
 *   entity_name, aliases, category, activity_type, it_related
 *
 * The catalog is therefore the single source of truth for classification.
 */
function rr_load_predefined_entities(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT
            id,
            entity_name,
            aliases,
            category,
            activity_type,
            it_related
        FROM predefined_entities
        ORDER BY CHAR_LENGTH(entity_name) DESC, entity_name ASC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function rr_normalize_entity_text(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace(
        ["\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", "\u{2013}", "\u{2014}"],
        ["'", "'", '"', '"', "-", "-"],
        $text
    );

    $text = preg_replace('/\s+/u', ' ', trim($text));
    return function_exists('mb_strtolower')
        ? mb_strtolower($text, 'UTF-8')
        : strtolower($text);
}

/**
 * Build a lookup containing both canonical names and aliases.
 *
 * Example from the supplied SQL:
 *   PHP -> aliases: php programming, php development, php system
 *   Data Entry -> aliases: data encoding, encoding, encode data
 */
function rr_build_entity_catalog(array $rows): array
{
    $catalog = [];

    foreach ($rows as $row) {
        $canonical = trim((string)($row['entity_name'] ?? ''));
        if ($canonical === '') {
            continue;
        }

        $values = [$canonical];

        $aliases = trim((string)($row['aliases'] ?? ''));
        if ($aliases !== '') {
            foreach (preg_split('/\|/u', $aliases) as $alias) {
                $alias = trim($alias);
                if ($alias !== '') {
                    $values[] = $alias;
                }
            }
        }

        foreach ($values as $term) {
            $normalized = rr_normalize_entity_text($term);
            if ($normalized === '') {
                continue;
            }

            /*
             * Keep the longest catalog term when two catalog entries
             * normalize to the same value.
             */
            if (
                !isset($catalog[$normalized]) ||
                strlen($term) > strlen((string)($catalog[$normalized]['matched_term'] ?? ''))
            ) {
                $catalog[$normalized] = [
                    'id'            => (int)$row['id'],
                    'entity_name'   => $canonical,
                    'matched_term'  => $term,
                    'category'      => (string)($row['category'] ?? 'Other'),
                    'activity_type' => in_array(
                        $row['activity_type'] ?? 'Other',
                        ['Software', 'Hardware', 'Clerical', 'Other'],
                        true
                    ) ? $row['activity_type'] : 'Other',
                    'it_related'    => in_array(
                        $row['it_related'] ?? 'unknown',
                        ['yes', 'no'],
                        true
                    ) ? $row['it_related'] : 'unknown',
                ];
            }
        }
    }

    return $catalog;
}

/**
 * Match a spaCy result against the SQL predefined catalog.
 *
 * Matching checks:
 * 1. matched_term
 * 2. entity_name
 * 3. entity
 * 4. label
 *
 * This prevents Python output from replacing the category/activity
 * classification stored in predefined_entities.
 */
function rr_match_predefined_entity(array $entity, array $catalog): ?array
{
    $candidateKeys = [
        'matched_term',
        'entity_name',
        'entity',
        'text',
        'label',
        'name',
    ];

    foreach ($candidateKeys as $key) {
        if (!isset($entity[$key])) {
            continue;
        }

        $value = trim((string)$entity[$key]);
        if ($value === '') {
            continue;
        }

        $normalized = rr_normalize_entity_text($value);

        if (isset($catalog[$normalized])) {
            return $catalog[$normalized];
        }

        /*
         * Some extractors return a longer phrase containing the
         * predefined term. Check catalog terms using Unicode-safe
         * boundary matching.
         */
        foreach ($catalog as $catalogTerm => $definition) {
            if (mb_strlen($catalogTerm, 'UTF-8') < 3) {
                continue;
            }

            $pattern = '/(?<![\p{L}\p{N}])' .
                preg_quote($catalogTerm, '/') .
                '(?![\p{L}\p{N}])/iu';

            if (preg_match($pattern, $normalized)) {
                return $definition;
            }
        }
    }

    return null;
}

/**
 * Convert Python extraction output to the exact report_entities schema.
 *
 * One database row is created per unique entity. The old implementation
 * inserted the same entity repeatedly according to "frequency", but the
 * supplied SQL schema has no frequency column. Keeping one row per entity
 * avoids duplicate cards and duplicate highlights.
 */
function rr_prepare_report_entities(
    array $pythonEntities,
    array $catalog
): array {
    $prepared = [];
    $seen = [];

    foreach ($pythonEntities as $entity) {
        if (!is_array($entity)) {
            continue;
        }

        $matched = rr_match_predefined_entity($entity, $catalog);

        if ($matched !== null) {
            $entityName = $matched['matched_term'];
            $canonical = $matched['entity_name'];
            $category = $matched['category'];
            $activityType = $matched['activity_type'];
            $itRelated = $matched['it_related'];
            $source = 'spacy_predefined';
        } else {
            $entityName = trim((string)(
                $entity['matched_term']
                ?? $entity['entity_name']
                ?? $entity['entity']
                ?? $entity['text']
                ?? $entity['label']
                ?? ''
            ));

            if ($entityName === '') {
                continue;
            }

            $canonical = trim((string)(
                $entity['canonical_name']
                ?? $entity['entity_name']
                ?? $entity['entity']
                ?? $entityName
            ));

            $category = trim((string)($entity['category'] ?? 'Other'));
            $activityType = in_array(
                $entity['activity_type'] ?? 'Other',
                ['Software', 'Hardware', 'Clerical', 'Other'],
                true
            ) ? $entity['activity_type'] : 'Other';

            $itRelated = in_array(
                $entity['it_related'] ?? 'unknown',
                ['yes', 'no', 'unknown'],
                true
            ) ? $entity['it_related'] : 'unknown';

            $source = 'spacy';
        }

        $uniqueKey = rr_normalize_entity_text($canonical);

        if ($uniqueKey === '' || isset($seen[$uniqueKey])) {
            continue;
        }

        $seen[$uniqueKey] = true;

        $prepared[] = [
            'entity_name'     => $entityName,
            'canonical_name'  => $canonical,
            'category'        => $category !== '' ? $category : 'Other',
            'activity_type'   => $activityType,
            'it_related'      => $itRelated,
            'source'          => $source,
        ];
    }

    return $prepared;
}

try {
    /*
     * Always read existing entities first.
     * Add ?extract=1 to rebuild them from the current PDF.
     */
    $entitySql = "
        SELECT
            id,
            report_id,
            entity_name,
            canonical_name,
            category,
            activity_type,
            it_related,
            source,
            confidence_score,
            created_at
        FROM report_entities
        WHERE report_id = :report_id
        ORDER BY entity_name ASC, id ASC
    ";

    $entityStmt = $pdo->prepare($entitySql);
    $entityStmt->execute([':report_id' => $reportId]);
    $entities = $entityStmt->fetchAll(PDO::FETCH_ASSOC);

    $forceExtract = isset($_GET['extract']) && $_GET['extract'] === '1';

    if ($forceExtract || !$entities) {
        /*
         * IMPORTANT:
         * The predefined catalog comes directly from the SQL table.
         * No hard-coded PHP/MySQL/Java/etc. list is used here.
         */
        $predefinedRows = rr_load_predefined_entities($pdo);
        $catalog = rr_build_entity_catalog($predefinedRows);

        if (!$catalog) {
            throw new RuntimeException(
                'No predefined entities were found in the predefined_entities table.'
            );
        }

        $pythonEntities = rr_run_python_extractor($filePath);
        $preparedEntities = rr_prepare_report_entities(
            $pythonEntities,
            $catalog
        );

        $pdo->beginTransaction();

        $deleteStmt = $pdo->prepare(
            'DELETE FROM report_entities WHERE report_id = :report_id'
        );
        $deleteStmt->execute([':report_id' => $reportId]);

        $insertStmt = $pdo->prepare("
            INSERT INTO report_entities
            (
                report_id,
                entity_name,
                canonical_name,
                category,
                activity_type,
                it_related,
                source,
                confidence_score
            )
            VALUES
            (
                :report_id,
                :entity_name,
                :canonical_name,
                :category,
                :activity_type,
                :it_related,
                :source,
                :confidence_score
            )
        ");

        foreach ($preparedEntities as $entity) {
            $insertStmt->execute([
                ':report_id'       => $reportId,
                ':entity_name'     => $entity['entity_name'],
                ':canonical_name'  => $entity['canonical_name'],
                ':category'        => $entity['category'],
                ':activity_type'   => $entity['activity_type'],
                ':it_related'      => $entity['it_related'],
                ':source'          => $entity['source'],
                ':confidence_score'=> 100.00,
            ]);
        }

        $pdo->commit();

        $entityStmt->execute([':report_id' => $reportId]);
        $entities = $entityStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        'review_report entity extraction: ' .
        $e->getMessage()
    );

    $entities = [];
}
/* Add ?extract=1 to force a fresh Python extraction after catalog changes. */

/* ============================================================
 * 5. HELPERS
 * ============================================================ */
function rr_e($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}
function entityValue(array $entity, array $keys, $fallback = '')
{
    foreach ($keys as $key) {
        if (
            array_key_exists($key, $entity) &&
            $entity[$key] !== null &&
            $entity[$key] !== ''
        ) {
            return $entity[$key];
        }
    }
    return $fallback;
}
/*
 * JSON is placed in a script tag using json_encode with the
 * appropriate escaping flags.
 */
$entityJson = json_encode(
    array_values($entities),
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);
if ($entityJson === false) {
    $entityJson = '[]';
}
$weekNumber = (string)($report['week_number'] ?? '—');
$submittedAt = $report['submitted_at'] ?? null;
if ($submittedAt) {
    $formattedDate = date(
        'M d, Y \a\t g:i A',
        strtotime($submittedAt)
    );
} else {
    $formattedDate = '—';
}
$status = strtolower((string)($report['status'] ?? 'pending'));
$statusLabel = 'Waiting for Review';
$statusClass = 'bg-amber-50 text-amber-700 border-amber-200';
if ($status === 'approved') {
    $statusLabel = 'Approved';
    $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
} elseif ($status === 'rejected') {
    $statusLabel = 'Needs Changes';
    $statusClass = 'bg-rose-50 text-rose-700 border-rose-200';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Review Week <?= rr_e($weekNumber); ?> Report - OJT Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .pdf-stage {
            background: #e8edf4;
            overflow-y: auto;
            overflow-x: auto;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
        }
        .pdf-page {
            position: relative;
            margin: 0 auto 1.25rem;
            width: fit-content;
            max-width: 100%;
            background: white;
            box-shadow: 0 8px 24px rgba(15, 40, 84, .12);
        }
        .pdf-page canvas {
            display: block;
            max-width: 100%;
            height: auto;
        }

        .pdf-text-layer {
            position: absolute;
            inset: 0;
            overflow: hidden;
            line-height: 1;
            user-select: text;
        }
        .pdf-text-layer span {
            position: absolute;
            color: transparent;
            white-space: pre;
            cursor: text;
            transform-origin: 0 0;
            border-radius: 3px;
        }
        .pdf-text-layer span.entity-highlight {
            color: transparent;
            background: rgba(250, 204, 21, .68);
            box-shadow: 0 0 0 1px rgba(180, 83, 9, .28);
        }
        .entity-card {
            transition:
                border-color .15s ease,
                background-color .15s ease,
                transform .15s ease;
        }
        .entity-card:hover {
            transform: translateY(-1px);
        }
        .entity-card.active {
            border-color: #f59e0b;
            background: #fffbeb;
        }
        .thin-scrollbar::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        .thin-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
        .pdf-loading {
            min-height: 300px;
        }
        @media (max-width: 1279px) {
            .pdf-stage {
                min-height: 55vh;
            }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">
<div class="flex min-h-screen">
    <?php include __DIR__ . '/src/components/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <?php include __DIR__ . '/src/components/header.php'; ?>
        <main
            class="p-4 sm:p-6 lg:p-8 max-w-[1600px] w-full mx-auto space-y-5 flex-1"
        >

            <div
                class="bg-white rounded-2xl border border-slate-200/80
                       shadow-xs overflow-hidden"
            >
                <div
                    class="p-5 sm:p-6 flex flex-col lg:flex-row
                           lg:items-center lg:justify-between gap-4"
                >
                    <div class="flex items-start gap-3">
                        <a
                            href="/ICS-PORTAL/dashboard.php"
                            class="flex h-10 w-10 shrink-0 items-center
                                   justify-center rounded-xl border
                                   border-emerald-200 bg-emerald-50/40
                                   text-slate-600 hover:bg-slate-100
                                   hover:text-emerald-700 transition"
                            aria-label="Back to dashboard"
                            title="Back to Dashboard"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 12H5m7 7-7-7 7-7"
                                />
                            </svg>
                        </a>
                        <div>
                            <p
                                class="text-[10px] font-bold uppercase
                                       tracking-wider text-emerald-700"
                            >
                                Report Review
                            </p>
                            <h1
                                class="mt-1 text-base sm:text-lg font-bold
                                       text-slate-900"
                            >
                                Week <?= rr_e($weekNumber); ?>
                                Accomplishment Report
                            </h1>
                            <p
                                class="mt-1 text-xs font-medium
                                       text-slate-500"
                            >
                                Submitted: <?= rr_e($formattedDate); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="px-3 py-1.5 rounded-full border
                                   text-[10px] font-bold <?= rr_e($statusClass); ?>"
                        >
                            <?= rr_e($statusLabel); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div
                class="grid min-h-0 grid-cols-1
                       xl:grid-cols-[minmax(0,1.55fr)_minmax(330px,.7fr)]
                                              rounded-2xl border border-slate-200/80
                       bg-white shadow-xs overflow-hidden
                       xl:h-[calc(100vh-230px)]
                       xl:max-h-[calc(100vh-230px)]"
            >
                <!-- =================================================
                     LEFT: PDF
                ================================================== -->
                <section
                    class="min-w-0 min-h-0 border-b
                           xl:border-b-0 xl:border-r border-slate-200"
                >
                    <div
                        class="flex items-center justify-between gap-3
                               border-b border-slate-100 p-4 sm:p-5"
                    >
                        <div class="min-w-0">
                            <h2
                                class="text-xs font-bold text-slate-900"
                            >
                                PDF Content
                            </h2>
                            <p
                                class="mt-1 text-[11px] text-slate-500"
                            >
                                Original report document
                            </p>
                        </div>
                        <div
                            class="flex shrink-0 items-center gap-2"
                        >
                            <button
                                type="button"
                                id="zoomOut"
                                class="h-8 w-8 rounded-lg border
                                       border-slate-200 text-lg font-bold
                                       text-slate-600 hover:bg-slate-50"
                                aria-label="Zoom out"
                            >
                                −
                            </button>
                            <span
                                id="zoomValue"
                                class="w-12 text-center text-[11px]
                                       font-semibold text-slate-500"
                            >
                                100%
                            </span>
                            <button
                                type="button"
                                id="zoomIn"
                                class="h-8 w-8 rounded-lg border
                                       border-slate-200 text-lg font-bold
                                       text-slate-600 hover:bg-slate-50"
                                aria-label="Zoom in"
                            >
                                +
                            </button>
                        </div>
                    </div>
                    <div
                        id="pdfStage"
                        class="pdf-stage thin-scrollbar
                               h-[calc(100vh-230px)]
                               min-h-[500px]
                               max-h-[calc(100vh-230px)]
                               overflow-y-auto overflow-x-auto
                               overscroll-contain p-3 sm:p-5"
                    >
                        <div
                            id="pdfLoading"
                            class="pdf-loading flex items-center
                                   justify-center text-xs text-slate-500"
                        >
                            Loading PDF…
                        </div>
                        <div
                            id="pdfPages"
                            class="space-y-5"
                        ></div>
                    </div>
                </section>
                <!-- =================================================
                     RIGHT: EXTRACTED ENTITIES
                ================================================== -->
                <aside
                                        class="flex min-h-0 min-w-0 flex-col bg-white
                           xl:h-full"
                >
                    <div
                        class="flex items-center justify-between gap-3
                               border-b border-slate-100 p-4 sm:p-5"
                    >
                        <div>
                            <h2
                                class="text-xs font-bold text-slate-900"
                            >
                                Extracted Entities
                            </h2>
                            <p
                                class="mt-1 text-[11px] text-slate-500"
                            >
                                Entities extracted from this report.
                            </p>
                        </div>
                        <span
                            id="entityCount"
                            class="rounded-full bg-emerald-50 px-2.5 py-1
                                   text-[10px] font-bold text-emerald-700"
                        >
                            <?= count($entities); ?>
                            <?= count($entities) === 1 ? 'entity' : 'entities'; ?>
                        </span>
                    </div>
                    <div
                        id="entityList"
                                                class="thin-scrollbar min-h-0 flex-1
                               h-[420px] max-h-[55vh]
                               overflow-y-auto overscroll-contain p-4 space-y-2
                               xl:h-auto xl:max-h-none"
                    ></div>
                </aside>
            </div>
        </main>
    </div>
</div>
<script>
(() => {
    'use strict';
    const report = {
        id: <?= json_encode((string)$reportId); ?>,
        title: <?= json_encode(
            'Week ' . $weekNumber . ' Accomplishment Report',
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ); ?>,
        fileUrl: <?= json_encode(
            $pdfUrl,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ); ?>,
        entities: <?= $entityJson; ?>
    };
    const pdfStage = document.getElementById('pdfStage');
    const pdfPages = document.getElementById('pdfPages');
    const pdfLoading = document.getElementById('pdfLoading');
    const entityList = document.getElementById('entityList');
    const entityCount = document.getElementById('entityCount');
    const zoomIn = document.getElementById('zoomIn');
    const zoomOut = document.getElementById('zoomOut');
    const zoomValue = document.getElementById('zoomValue');
    let zoom = 1;
    let pdfDocument = null;
    let activeEntityKey = null;

    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(
            /[&<>'"]/g,
            character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#039;',
                '"': '&quot;'
            })[character]
        );
    }
    function getValue(entity, keys, fallback = '') {
        for (const key of keys) {
            if (
                entity &&
                entity[key] !== undefined &&
                entity[key] !== null &&
                entity[key] !== ''
            ) {
                return entity[key];
            }
        }
        return fallback;
    }
    function normalize(text) {
        return String(text || '')
            .normalize('NFKC')
            .toLowerCase()
            .replace(/[\u2018\u2019]/g, "'")
            .replace(/[\u201C\u201D]/g, '"')
            .replace(/\s+/g, ' ')
            .trim();
    }
    function getEntityName(entity, index) {
        return getValue(
            entity,
            [
                'entity_name',
                                'name',
                'entity',
                'label'
            ],
            `Entity ${index + 1}`
        );
    }
    function getEntityKey(entity, index) {

        return normalize(getEntityName(entity, index))
            + '::'
            + index;
    }

    function renderEntities() {
        if (!entityList) return;
        const allEntities = Array.isArray(report.entities)
            ? report.entities
            : [];
        const filtered = allEntities.map((entity, index) => ({
            entity,
            index
        }));
        if (entityCount) {
            entityCount.textContent =
                `${allEntities.length} ${
                    allEntities.length === 1
                        ? 'entity'
                        : 'entities'
                }`;
        }
        entityList.innerHTML = '';
        if (!filtered.length) {
            entityList.innerHTML = `
                <div class="py-10 text-center text-xs text-slate-500">
                    No extracted entities found for this report.
                </div>
            `;
            return;
        }
        filtered.forEach(({ entity, index }) => {
            const name = getEntityName(entity, index);
            const category = getValue(
                entity,
                [
                    'category',
                    'type'
                ],
                'Other'
            );

            const activityType = getValue(
                entity,
                [
                    'activity_type'
                ],
                'Other'
            );
            const itRelated = String(
                getValue(
                    entity,
                    [
                        'it_related',
                        'is_it_related'
                    ],
                    ''
                )
            ).toLowerCase();
            const key = getEntityKey(entity, index);
            const isIT =
                itRelated === 'yes' ||
                itRelated === '1' ||
                itRelated === 'true';
            const card = document.createElement('button');
            card.type = 'button';
            card.className =
                'entity-card w-full text-left rounded-xl ' +
                'border border-slate-200 p-3 bg-white';
            card.dataset.entityKey = key;
            if (activeEntityKey === key) {
                card.classList.add('active');
            }
            card.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <span
                        class="text-xs font-bold text-slate-800
                               break-words"
                    >
                        ${escapeHtml(name)}
                    </span>
                    <span
                        class="shrink-0 rounded-full px-2 py-0.5
                               text-[9px] font-bold ${
                                   isIT
                                       ? 'bg-emerald-50 text-emerald-700'
                                       : 'bg-slate-100 text-slate-600'
                               }"
                    >
                        ${isIT ? 'IT-related' : 'Other'}
                    </span>
                </div>
                <div
                    class="mt-2 flex flex-wrap gap-x-3 gap-y-1
                           text-[10px] text-slate-500"
                >
                    <span>
                        ${escapeHtml(category)}
                    </span>
                    <span>
                        ${escapeHtml(activityType)}
                    </span>
                </div>
            `;
            card.addEventListener('click', () => {
                if (activeEntityKey === key) {
                    activeEntityKey = null;
                    clearHighlights();
                } else {
                    activeEntityKey = key;
                    highlightEntity(name);
                }
                document
                    .querySelectorAll('.entity-card')
                    .forEach(cardElement => {
                        cardElement.classList.toggle(
                            'active',
                            cardElement.dataset.entityKey ===
                                activeEntityKey
                        );
                    });
            });
            entityList.appendChild(card);
        });
    }

    function clearHighlights() {
        document
            .querySelectorAll(
                '.pdf-text-layer span.entity-highlight'
            )
            .forEach(span => {
                span.classList.remove(
                    'entity-highlight'
                );
            });
    }
    function highlightEntity(entityName) {
        clearHighlights();
        const target = normalize(entityName);
        if (!target) return;
        const spans =
            document.querySelectorAll(
                '.pdf-text-layer span'
            );
        spans.forEach(span => {
            const text = normalize(
                span.textContent
            );
 
            if (
                text &&
                (
                    text === target ||
                    (
                        target.length >= 3 &&
                        text.includes(target)
                    )
                )
            ) {
                span.classList.add(
                    'entity-highlight'
                );
            }
        });
    }

    async function renderPdf() {
        pdfPages.innerHTML = '';
        clearHighlights();
        if (!report.fileUrl) {
            pdfLoading.textContent =
                'No PDF file is attached to this report.';
            pdfLoading.classList.remove('hidden');
            return;
        }
        if (!window.pdfjsLib) {
            pdfLoading.textContent =
                'PDF viewer could not be loaded. Please refresh the page.';
            pdfLoading.classList.remove('hidden');
            return;
        }
        pdfLoading.textContent = 'Loading PDF…';
        pdfLoading.classList.remove('hidden');
        try {
            pdfDocument = await pdfjsLib
                .getDocument({
                    url: report.fileUrl
                })
                .promise;
            for (
                let pageNumber = 1;
                pageNumber <= pdfDocument.numPages;
                pageNumber++
            ) {
                const page =
                    await pdfDocument.getPage(
                        pageNumber
                    );
                const viewport =
                    page.getViewport({
                        scale: zoom
                    });
                const wrapper =
                    document.createElement('div');
                wrapper.className =
                    'pdf-page';
                wrapper.style.width =
                    `${viewport.width}px`;
                wrapper.style.height =
                    `${viewport.height}px`;
                const canvas =
                    document.createElement('canvas');
                const context =
                    canvas.getContext(
                        '2d',
                        {
                            alpha: false
                        }
                    );
                canvas.width =
                    Math.ceil(viewport.width);
                canvas.height =
                    Math.ceil(viewport.height);
                canvas.setAttribute(
                    'aria-label',
                    `PDF page ${pageNumber}`
                );
                const textLayer =
                    document.createElement('div');
                textLayer.className =
                    'pdf-text-layer';
                textLayer.style.width =
                    `${viewport.width}px`;
                textLayer.style.height =
                    `${viewport.height}px`;
                wrapper.appendChild(canvas);
                wrapper.appendChild(textLayer);
                pdfPages.appendChild(wrapper);
                await page.render({
                    canvasContext: context,
                    viewport
                }).promise;
                const textContent =
                    await page.getTextContent();
                textContent.items.forEach(
                    item => {
                        const span =
                            document.createElement(
                                'span'
                            );
                        span.textContent =
                            item.str;
                        const tx =
                            pdfjsLib.Util.transform(
                                viewport.transform,
                                item.transform
                            );
                        const angle =
                            Math.atan2(
                                tx[1],
                                tx[0]
                            );
                        const scaleX =
                            Math.sqrt(
                                tx[0] * tx[0] +
                                tx[1] * tx[1]
                            );
                        const scaleY =
                            Math.sqrt(
                                tx[2] * tx[2] +
                                tx[3] * tx[3]
                            );
                        span.style.left =
                            `${tx[4]}px`;
                        span.style.top =
                            `${tx[5] - scaleY}px`;
                        span.style.fontSize =
                            `${scaleY}px`;
                        span.style.transform =
                            `rotate(${angle}rad) ` +
                            `scaleX(${
                                scaleX /
                                Math.max(
                                    scaleY,
                                    1
                                )
                            })`;
                        textLayer.appendChild(
                            span
                        );
                    }
                );
            }
            pdfLoading.classList.add('hidden');
            if (zoomValue) {
                zoomValue.textContent =
                    `${Math.round(zoom * 100)}%`;
            }

            if (activeEntityKey) {
                const index =
                    Number(
                        activeEntityKey
                            .split('::')
                            .pop()
                    );
                const entity =
                    report.entities[index];
                if (entity) {
                    highlightEntity(
                        getEntityName(
                            entity,
                            index
                        )
                    );
                }
            }
        } catch (error) {
            console.error(
                'PDF rendering error:',
                error
            );
            pdfPages.innerHTML = '';
            pdfLoading.textContent =
                'Unable to load the PDF. Check that the uploaded file exists and is accessible from the browser.';
            pdfLoading.classList.remove(
                'hidden'
            );
        }
    }

    zoomIn?.addEventListener('click', () => {
        zoom = Math.min(
            2,
            Number(
                (zoom + 0.1).toFixed(2)
            )
        );
        renderPdf();
    });
    zoomOut?.addEventListener('click', () => {
        zoom = Math.max(
            0.6,
            Number(
                (zoom - 0.1).toFixed(2)
            )
        );
        renderPdf();
    });
    renderEntities();
    renderPdf();
})();

</script>
</body>
</html>