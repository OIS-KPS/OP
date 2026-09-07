<?php
/*
 * ============================================================
 * review_report.php
 * ============================================================
 *
 * Standalone Student Report Review Page
 *
 * URL:
 * review_report.php?report_id=REPORT_ID
 *
 * Layout:
 *   LEFT  = PDF document
 *   RIGHT = Extracted entities
 *
 * Uses:
 *   - reports
 *   - students
 *   - users
 *   - report_entities
 *
 * The report_entities table is related to reports by:
 *   report_entities.report_id = reports.id
 */

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
 * 4. LOAD EXTRACTED ENTITIES
 * ============================================================
 *
 * This query uses only columns that actually exist in
 * report_entities according to the supplied SQL schema.
 * ============================================================ */

$entities = [];

try {
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
    $entityStmt->execute([
        ':report_id' => $reportId
    ]);

    $entities = $entityStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('review_report entity query: ' . $e->getMessage());
    $entities = [];
}

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Review Week <?= rr_e($weekNumber); ?> Report - OJT Portal
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"
    ></script>

    <link
        rel="stylesheet"
        href="/ICS-PORTAL/public/css/style.css"
    >

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .pdf-stage {
            background: #e8edf4;
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

        /*
         * Invisible text layer placed directly over the PDF.
         * The canvas remains untouched, so the original PDF
         * appearance is preserved.
         */
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

            <!-- =================================================
                 TOP HEADER
            ================================================== -->

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
                                   border-slate-200 bg-slate-50
                                   text-slate-600 hover:bg-slate-100
                                   hover:text-[#0F2854] transition"
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
                                       tracking-wider text-[#0F2854]"
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


            <!-- =================================================
                 MAIN REVIEW AREA
            ================================================== -->

            <div
                class="grid min-h-0 grid-cols-1
                       xl:grid-cols-[minmax(0,1.55fr)_minmax(330px,.7fr)]
                       rounded-2xl border border-slate-200/80
                       bg-white shadow-xs overflow-hidden"
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
                               overflow-auto p-3 sm:p-5"
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
                    class="flex min-h-[500px] min-w-0 flex-col bg-white"
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
                                Verified terms found in this report.
                            </p>

                        </div>

                        <span
                            id="entityCount"
                            class="rounded-full bg-blue-50 px-2.5 py-1
                                   text-[10px] font-bold text-[#0F2854]"
                        >
                            <?= count($entities); ?>
                            <?= count($entities) === 1 ? 'entity' : 'entities'; ?>
                        </span>

                    </div>


                    <div
                        class="border-b border-slate-100 p-4"
                    >

                        <div class="relative">

                            <svg
                                class="pointer-events-none absolute left-3
                                       top-1/2 h-3.5 w-3.5 -translate-y-1/2
                                       text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    cx="11"
                                    cy="11"
                                    r="7"
                                />
                                <path
                                    d="m20 20-4-4"
                                />
                            </svg>

                            <input
                                id="entitySearch"
                                type="search"
                                placeholder="Search entities…"
                                class="w-full rounded-lg border
                                       border-slate-200 bg-slate-50
                                       py-2 pl-9 pr-3 text-xs outline-none
                                       focus:border-[#0F2854]
                                       focus:ring-2 focus:ring-blue-100"
                            >

                        </div>

                    </div>


                    <div
                        id="entityList"
                        class="thin-scrollbar min-h-0 flex-1
                               overflow-y-auto p-4 space-y-2"
                    ></div>


                    <div
                        class="border-t border-slate-100
                               bg-slate-50/60 p-4"
                    >

                        <p
                            class="text-[10px] leading-relaxed
                                   text-slate-500"
                        >
                            <strong class="text-slate-700">
                                Highlight behavior:
                            </strong>

                            Click an extracted entity to highlight its
                            matching text in the PDF. Click the same
                            entity again to remove the highlight.
                        </p>

                    </div>

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
    const entitySearch = document.getElementById('entitySearch');
    const entityCount = document.getElementById('entityCount');

    const zoomIn = document.getElementById('zoomIn');
    const zoomOut = document.getElementById('zoomOut');
    const zoomValue = document.getElementById('zoomValue');

    let zoom = 1;
    let pdfDocument = null;
    let activeEntityKey = null;

    /*
     * ============================================================
     * PDF.JS SETUP
     * ============================================================
     */

    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }


    /*
     * ============================================================
     * HELPERS
     * ============================================================
     */

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
                'canonical_name',
                'name',
                'entity',
                'label'
            ],
            `Entity ${index + 1}`
        );
    }

    function getEntityKey(entity, index) {
        /*
         * Include the index so two identical extracted terms can
         * still be selected independently.
         */
        return normalize(getEntityName(entity, index))
            + '::'
            + index;
    }


    /*
     * ============================================================
     * ENTITY RENDERING
     * ============================================================
     */

    function renderEntities() {
        if (!entityList) return;

        const search = normalize(
            entitySearch ? entitySearch.value : ''
        );

        const allEntities = Array.isArray(report.entities)
            ? report.entities
            : [];

        const filtered = allEntities
            .map((entity, index) => ({
                entity,
                index
            }))
            .filter(({ entity, index }) => {
                const name = getEntityName(entity, index);
                return normalize(name).includes(search);
            });

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
                    'activity_type',
                    'category',
                    'type'
                ],
                'Unclassified'
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

            const source = getValue(
                entity,
                [
                    'source'
                ],
                ''
            );

            const confidenceRaw = getValue(
                entity,
                [
                    'confidence_score',
                    'confidence'
                ],
                ''
            );

            let confidence = '—';

            if (
                confidenceRaw !== '' &&
                !isNaN(Number(confidenceRaw))
            ) {
                const number = Number(confidenceRaw);

                confidence =
                    `${Math.round(
                        number <= 1
                            ? number * 100
                            : number
                    )}%`;
            }

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
                                       ? 'bg-blue-50 text-blue-700'
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
                        Confidence: ${confidence}
                    </span>

                    ${
                        source
                            ? `<span>${escapeHtml(source)}</span>`
                            : ''
                    }

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


    /*
     * ============================================================
     * PDF HIGHLIGHTING
     * ============================================================
     */

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

            /*
             * Exact matching is preferred.
             *
             * Partial matching is only used when a PDF.js text
             * item contains the complete entity.
             *
             * This prevents very short entities from highlighting
             * unrelated text as aggressively as possible.
             */
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


    /*
     * ============================================================
     * PDF RENDERING
     * ============================================================
     */

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

            /*
             * Reapply the selected entity after a zoom.
             */
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


    /*
     * ============================================================
     * ZOOM
     * ============================================================
     */

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


    /*
     * ============================================================
     * SEARCH
     * ============================================================
     */

    entitySearch?.addEventListener(
        'input',
        renderEntities
    );


    /*
     * ============================================================
     * INITIALIZE
     * ============================================================
     */

    renderEntities();
    renderPdf();

})();
</script>

</body>
</html>
