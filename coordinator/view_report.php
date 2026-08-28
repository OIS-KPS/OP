<?php
// coordinator/view_report.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header('Location: ../auth/login.php');
    exit();
}

$pageTitle = 'Report Inspection & Entities';
$reportId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$forceExtraction = isset($_GET['extract']) && $_GET['extract'] === '1';

if ($reportId <= 0) {
    header('Location: approved_reports.php');
    exit();
}

/**
 * Resolve the PDF path stored in reports.file_path to a local file.
 * The schema stores paths such as /uploads/reports/report.pdf, while the
 * application may be installed below a project directory.
 */
function resolveReportPdfPath(string $storedPath): ?string
{
    $storedPath = trim(str_replace('\\', '/', $storedPath));
    if ($storedPath === '') {
        return null;
    }

    $projectRoot = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
    $relativePath = ltrim($storedPath, '/');
    $relativePath = preg_replace('#^ICS-PORTAL/#i', '', $relativePath);

    $candidates = [
        $storedPath,
        $projectRoot . '/' . $relativePath,
        $projectRoot . '/public/' . $relativePath,
    ];

    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
        $candidates[] = $documentRoot . '/' . $relativePath;
    }

    foreach ($candidates as $candidate) {
        $realPath = realpath($candidate);
        if ($realPath !== false && is_file($realPath) && is_readable($realPath)) {
            return $realPath;
        }
    }

    return null;
}

/**
 * Locate the Python extractor. Adjust the first candidate if the script has
 * a different filename in the project.
 */
function findEntityExtractor(): ?string
{
    $candidates = [
        __DIR__ . '/../python/entity_extractor.py',
        __DIR__ . '/../python/extract_entities.py',
        __DIR__ . '/../scripts/entity_extractor.py',
        __DIR__ . '/../src/python/entity_extractor.py',
        __DIR__ . '/../entity_extractor.py',
    ];

    foreach ($candidates as $candidate) {
        if (is_file($candidate) && is_readable($candidate)) {
            return $candidate;
        }
    }

    return null;
}

/**
 * Execute the extractor and decode its JSON response.
 * The Python extractor must return {"success": true, "entities": [...]}.
 */
function runEntityExtractor(string $pdfPath): array
{
    $extractor = findEntityExtractor();
    if ($extractor === null) {
        return [
            'success' => false,
            'error' => 'The spaCy entity extractor was not found. Place entity_extractor.py in the python directory.'
        ];
    }

    $command = 'python3 ' . escapeshellarg($extractor) . ' ' . escapeshellarg($pdfPath);
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptors, $pipes);
    if (!is_resource($process)) {
        return [
            'success' => false,
            'error' => 'Unable to start the spaCy entity extractor.'
        ];
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    $payload = json_decode(trim($stdout), true);
    if (!is_array($payload)) {
        return [
            'success' => false,
            'error' => 'The extractor returned invalid JSON.',
            'details' => trim($stderr ?: $stdout)
        ];
    }

    if ($exitCode !== 0 || empty($payload['success'])) {
        return [
            'success' => false,
            'error' => $payload['error'] ?? 'The spaCy extractor failed.',
            'details' => $payload['details'] ?? trim($stderr)
        ];
    }

    return $payload;
}

/**
 * Persist only entities verified by the Python extractor against
 * predefined_entities. Existing automatic rows are replaced; manually added
 * rows are preserved because the schema has no manual-row flag.
 */
function persistExtractedEntities(PDO $pdo, int $reportId, array $entities): int
{
    $allowedActivityTypes = ['Software', 'Hardware', 'Clerical', 'Other'];
    $allowedItValues = ['yes', 'no', 'unknown'];
    $allowedSources = ['spacy', 'predefined', 'spacy_predefined'];
    $seen = [];
    $rows = [];

    foreach ($entities as $entity) {
        if (!is_array($entity)) {
            continue;
        }

        $entityName = trim((string) ($entity['entity_name'] ?? $entity['entity'] ?? ''));
        if ($entityName === '') {
            continue;
        }

        $canonicalName = trim((string) ($entity['canonical_name'] ?? $entityName));
        $category = trim((string) ($entity['category'] ?? 'Other')) ?: 'Other';
        $activityType = trim((string) ($entity['activity_type'] ?? 'Other'));
        $activityType = in_array($activityType, $allowedActivityTypes, true)
            ? $activityType
            : 'Other';

        $itRelated = strtolower(trim((string) ($entity['it_related'] ?? 'unknown')));
        $itRelated = in_array($itRelated, $allowedItValues, true)
            ? $itRelated
            : 'unknown';

        $source = trim((string) ($entity['source'] ?? 'predefined'));
        $source = in_array($source, $allowedSources, true) ? $source : 'predefined';

        // Preserve a real extractor confidence only when it was supplied.
        // Do not invent a 100% confidence value.
        $confidence = null;
        if (isset($entity['confidence_score']) && is_numeric($entity['confidence_score'])) {
            $confidence = max(0.00, min(100.00, (float) $entity['confidence_score']));
        }

        $key = strtolower($canonicalName . '|' . $activityType);
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;

        $rows[] = [
            $entityName,
            $canonicalName,
            $category,
            $activityType,
            $itRelated,
            $source,
                $confidence
        ];
    }

    $pdo->beginTransaction();
    try {
        $delete = $pdo->prepare(
            "DELETE FROM report_entities
             WHERE report_id = ?
               AND source IN ('predefined', 'spacy_predefined')"
        );
        $delete->execute([$reportId]);

        $insert = $pdo->prepare(
            'INSERT INTO report_entities
                (report_id, entity_name, canonical_name, category,
                 activity_type, it_related, source, confidence_score)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        foreach ($rows as $row) {
            $insert->execute([
                $reportId,
                $row[0],
                $row[1],
                $row[2],
                $row[3],
                $row[4],
                $row[5],
                $row[6]
            ]);
        }

        $pdo->commit();
        return count($rows);
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}

// Coordinator manually adds an entity. The schema uses activity_type, not
// classification, so the submitted classification is mapped to that enum.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_entity') {
    $entityName = trim((string) ($_POST['entity_name'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? 'Other')) ?: 'Other';
    $classification = trim((string) ($_POST['classification'] ?? 'Software'));
    $activityType = in_array($classification, ['Software', 'Hardware', 'Clerical', 'Other'], true)
        ? $classification
        : 'Other';
    $itRelated = $activityType === 'Clerical' ? 'no' : 'yes';
    

    if ($entityName !== '') {
        try {
            $stmtInsert = $pdo->prepare(
                'INSERT INTO report_entities
                    (report_id, entity_name, canonical_name, category,
                     activity_type, it_related, source, confidence_score)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmtInsert->execute([
                $reportId,
                $entityName,
                $entityName,
                $category,
                $activityType,
                $itRelated,
                'spacy_predefined',
                null
            ]);
            $_SESSION['flash_success'] = 'Entity added successfully.';
        } catch (Throwable $exception) {
            $_SESSION['flash_error'] = 'Failed to add entity: ' . $exception->getMessage();
        }
    }

    header('Location: view_report.php?id=' . $reportId);
    exit();
}

// Coordinator deletes a row belonging to this report only.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_entity') {
    $entityId = (int) ($_POST['entity_id'] ?? 0);
    if ($entityId > 0) {
        try {
            $stmtDelete = $pdo->prepare(
                'DELETE FROM report_entities WHERE id = ? AND report_id = ?'
            );
            $stmtDelete->execute([$entityId, $reportId]);
        } catch (Throwable $exception) {
            error_log('Delete entity error: ' . $exception->getMessage());
        }
    }

    header('Location: view_report.php?id=' . $reportId);
    exit();
}

$report = null;
$extractedEntities = [];
$itPct = 0;
$clericalPct = 0;
$technicalCount = 0;
$clericalCount = 0;

try {
    $stmt = $pdo->prepare(
        'SELECT
            r.id, r.student_id, r.week_number, r.file_path,
            r.status, r.submitted_at,
            s.student_number, s.program,
            u.name AS student_name, u.email AS student_email,
            c.name AS company_name, c.department AS company_dept,
            u_sup.name AS supervisor_name
         FROM reports r
         JOIN students s ON r.student_id = s.id
         JOIN users u ON s.user_id = u.id
         LEFT JOIN companies c ON s.company_id = c.id
         LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
         LEFT JOIN users u_sup ON sup.user_id = u_sup.id
         WHERE r.id = ?
         LIMIT 1'
    );
    $stmt->execute([$reportId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        header('Location: approved_reports.php');
        exit();
    }

    // Extract automatically on first inspection, or when ?extract=1 is used.
    $stmtAutoCount = $pdo->prepare(
        "SELECT COUNT(*) FROM report_entities
         WHERE report_id = ?
           AND source IN ('predefined', 'spacy_predefined')"
    );
    $stmtAutoCount->execute([$reportId]);
    $automaticEntityCount = (int) $stmtAutoCount->fetchColumn();

    if ($forceExtraction || $automaticEntityCount === 0) {
        $pdfPath = resolveReportPdfPath((string) ($report['file_path'] ?? ''));
        if ($pdfPath === null) {
            $_SESSION['flash_error'] = 'The report PDF could not be found on the server.';
        } else {
            $extraction = runEntityExtractor($pdfPath);
            if (!empty($extraction['success'])) {
                $savedCount = persistExtractedEntities(
                    $pdo,
                    $reportId,
                    is_array($extraction['entities'] ?? null) ? $extraction['entities'] : []
                );
                $_SESSION['flash_success'] = "spaCy verification completed: {$savedCount} predefined entities saved.";
            } else {
                $_SESSION['flash_error'] = $extraction['error'] ?? 'Entity extraction failed.';
                if (!empty($extraction['details'])) {
                    error_log('Entity extraction details: ' . $extraction['details']);
                }
            }
        }
    }

    // activity_type is the schema column. classification is returned as a
    // compatibility alias for existing coordinator page templates.
    $stmtEnt = $pdo->prepare(
        'SELECT
            id, entity_name, canonical_name, category,
            activity_type, activity_type AS classification,
            it_related, source, created_at
         FROM report_entities
         WHERE report_id = ?
         ORDER BY id DESC'
    );
    $stmtEnt->execute([$reportId]);
    $extractedEntities = $stmtEnt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    foreach ($extractedEntities as &$entity) {
        $activityType = strtolower(trim((string) ($entity['activity_type'] ?? '')));
        $itRelated = strtolower(trim((string) ($entity['it_related'] ?? 'unknown')));

        // These labels are derived from the database, not guessed in the view.
        $entity['classification_label'] = $activityType === 'clerical'
            ? 'Clerical'
            : ($activityType !== '' ? 'Technical' : 'Unclassified');
        $entity['it_related_label'] = match ($itRelated) {
            'yes' => 'IT Related',
            'no' => 'Non-IT',
            default => 'Unknown'
        };

        if ($activityType === 'clerical') {
            $clericalCount++;
        } elseif ($activityType !== '') {
            $technicalCount++;
        }
    }
    unset($entity);

    $totalEntities = $technicalCount + $clericalCount;
    if ($totalEntities > 0) {
        $itPct = round(($technicalCount / $totalEntities) * 100, 1);
        $clericalPct = round(($clericalCount / $totalEntities) * 100, 1);
    }
} catch (Throwable $exception) {
    error_log('Error in coordinator/view_report.php: ' . $exception->getMessage());
    $extractedEntities = [];
}

require_once __DIR__ . '/../src/pages/coordinator/viewReportPage.php';
