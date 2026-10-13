<?php
// coordinator/view_report.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/report_workspace.php';
require_once __DIR__ . '/../src/services/entity_extraction.php';
require_once __DIR__ . '/../config/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header('Location: ../auth/login.php');
    exit();
}

// CSRF Guard: covers the whole POST surface of this controller.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify(__DIR__ . '/view_report.php');
}

$pageTitle = 'Report Inspection & Entities';

// Support both ?student_id=X (new student workspace) and ?id=Y (legacy report view)
$studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
$reportId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$selectedReportId = isset($_GET['report_id']) ? (int) $_GET['report_id'] : 0;
$forceExtraction = isset($_GET['extract']) && $_GET['extract'] === '1';

if ($studentId <= 0 && ($reportId > 0 || $selectedReportId > 0)) {
    $studentId = reportWorkspaceStudentIdForReport(
        $pdo,
        $selectedReportId > 0 ? $selectedReportId : $reportId
    );
}

if ($studentId <= 0 && $reportId <= 0 && $selectedReportId <= 0) {
    header('Location: approved_reports.php');
    exit();
}

/**
 * Resolve the PDF path stored in reports.file_path to a local file.
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
 * Locate the Python extractor.
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
 * Persist only entities verified by the Python extractor against predefined_entities.
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
        $activityType = in_array($activityType, $allowedActivityTypes, true) ? $activityType : 'Other';

        $itRelated = strtolower(trim((string) ($entity['it_related'] ?? 'unknown')));
        $itRelated = in_array($itRelated, $allowedItValues, true) ? $itRelated : 'unknown';

        $source = trim((string) ($entity['source'] ?? 'predefined'));
        $source = in_array($source, $allowedSources, true) ? $source : 'predefined';

        $confidence = null;
        if (isset($entity['confidence_score']) && is_numeric($entity['confidence_score'])) {
            $confidence = max(0.00, min(100.00, (float) $entity['confidence_score']));
        }

        $key = strtolower($canonicalName . '|' . $activityType);
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;

        $rows[] = [$entityName, $canonicalName, $category, $activityType, $itRelated, $source, $confidence];
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
                 activity_type, it_related, source, confidence_score, is_archived)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)'
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

// Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $targetReportId = (int) ($_POST['report_id'] ?? $reportId);

    if ($action === 'add_entity' && $targetReportId > 0) {
        $entityName = trim((string) ($_POST['entity_name'] ?? ''));

        // The modal offers exactly two choices: IT Related or Clerical.
        // This handler previously read $_POST['classification'], a field no
        // form ever posts (the select is named it_related), so the fallback
        // always applied and every manual add was saved as Software / IT --
        // silently contradicting the coordinator's selection and inflating
        // the IT percentage in this panel and in CQI reporting.
        $itRelated = (trim((string) ($_POST['it_related'] ?? 'yes')) === 'no') ? 'no' : 'yes';

        // activity_type stays populated for the reporting queries, which all
        // compute IT as activity_type != 'Clerical'. Software vs Hardware is
        // never distinguished downstream, so IT collapses to 'Other'.
        $activityType = ($itRelated === 'no') ? 'Clerical' : 'Other';
        $category = 'Other';

        // The modal has always posted an Aliases field but nothing ever read it.
        // report_entities has no aliases column, so it is only used below for the
        // optional dictionary write -- where aliases do matter, because they are
        // what the Python extractor matches against.
        $aliases = trim((string) ($_POST['aliases'] ?? ''));

        // Opt-in: off by default. A term reaches the global dictionary only when
        // the coordinator deliberately ticks the box, because a dictionary entry
        // overrides the spaCy model and terms.csv on every future extraction.
        $addToDictionary = isset($_POST['add_to_dictionary'])
            && $_POST['add_to_dictionary'] === '1';

        $reportEntitySaved = false;

        if ($entityName !== '') {
            try {
                // Check if this entity already exists for this report to prevent database duplicates
                $stmtCheck = $pdo->prepare(
                    'SELECT id, is_archived FROM report_entities 
                     WHERE report_id = ? AND LOWER(entity_name) = LOWER(?) LIMIT 1'
                );
                $stmtCheck->execute([$targetReportId, $entityName]);
                $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    if ((int)$existing['is_archived'] === 1) {
                        // If it was previously archived, restore it instead of creating a duplicate!
                        $stmtRestore = $pdo->prepare('UPDATE report_entities SET is_archived = 0, activity_type = ?, it_related = ? WHERE id = ?');
                        $stmtRestore->execute([$activityType, $itRelated, $existing['id']]);
                        $reportEntitySaved = true;
                        $_SESSION['flash_success'] = 'Entity was in archive and has been successfully restored!';
                    } else {
                        $_SESSION['flash_error'] = 'This entity already exists in this week\'s report list.';
                    }
                } else {
                    // Safe to insert new manual entity
                    $stmtInsert = $pdo->prepare(
                        'INSERT INTO report_entities
                            (report_id, entity_name, canonical_name, category,
                             activity_type, it_related, source, confidence_score, is_archived)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)'
                    );
                    $stmtInsert->execute([$targetReportId, $entityName, $entityName, $category, $activityType, $itRelated, 'manual_addition', 100.00]);
                    $reportEntitySaved = true;
                    $_SESSION['flash_success'] = 'Missing entity added successfully.';
                }
            } catch (Throwable $exception) {
                $_SESSION['flash_error'] = 'Failed to add entity: ' . userFacingError($exception, 'view_report.php:add_entity');
            }

            // Opt-in dictionary write. Kept in its own try/catch so a problem
            // here can never undo the report-level entity that was just saved.
            if ($addToDictionary && $reportEntitySaved) {
                try {
                    $stmtDict = $pdo->prepare(
                        'INSERT INTO predefined_entities
                            (entity_name, aliases, category, activity_type, it_related)
                         VALUES (?, ?, ?, ?, ?)'
                    );
                    $stmtDict->execute([$entityName, $aliases ?: null, 'Other', $activityType, $itRelated]);

                    $_SESSION['flash_success'] .=
                        ' It was also added to the Entity Dictionary.';
                } catch (PDOException $dictException) {
                    if (str_contains($dictException->getMessage(), 'Duplicate')) {
                        // entity_name is UNIQUE. The report entity is already
                        // saved, so this is a notice rather than an error.
                        $_SESSION['flash_success'] .=
                            ' It is already in the Entity Dictionary, so the dictionary was left unchanged.';
                    } else {
                        $_SESSION['flash_success'] .=
                            ' It could not be added to the Entity Dictionary.';
                        error_log(
                            'Dictionary add failed for "' . $entityName . '": '
                            . $dictException->getMessage()
                        );
                    }
                }
            }
        }
    } elseif ($action === 'delete_entity') {
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        if ($entityId > 0) {
            try {
                // Scoped to the student whose report is open, so a stale tab or a
                // hand-crafted entity_id cannot touch another student's record.
                $stmtArchive = $pdo->prepare('UPDATE report_entities
                                              SET is_archived = 1
                                              WHERE id = ?
                                                AND report_id IN (SELECT id FROM reports WHERE student_id = ?)');
                $stmtArchive->execute([$entityId, $studentId]);
                if ($stmtArchive->rowCount() === 0) {
                    $_SESSION['flash_error'] = 'That entity does not belong to this student report.';
                } else {
                    $_SESSION['flash_success'] = 'Entity archived successfully.';
                }
            } catch (Throwable $exception) {
                error_log('Archive entity error: ' . $exception->getMessage());
                $_SESSION['flash_error'] = 'Failed to archive entity: ' . userFacingError($exception, 'view_report.php:delete_entity');
            }
        }
    } elseif ($action === 'restore_entity') {
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        if ($entityId > 0) {
            try {
                $stmtRestore = $pdo->prepare('UPDATE report_entities
                                              SET is_archived = 0
                                              WHERE id = ?
                                                AND report_id IN (SELECT id FROM reports WHERE student_id = ?)');
                $stmtRestore->execute([$entityId, $studentId]);
                if ($stmtRestore->rowCount() === 0) {
                    $_SESSION['flash_error'] = 'That entity does not belong to this student report.';
                } else {
                    $_SESSION['flash_success'] = 'Entity restored successfully.';
                }
            } catch (Throwable $exception) {
                $_SESSION['flash_error'] = 'Failed to restore entity: ' . userFacingError($exception, 'view_report.php:restore_entity');
            }
        }
    } elseif ($action === 'permanent_delete_entity') {
        // Permanently delete from the database
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        if ($entityId > 0) {
            try {
                $stmtPermDel = $pdo->prepare('DELETE FROM report_entities
                                              WHERE id = ?
                                                AND report_id IN (SELECT id FROM reports WHERE student_id = ?)');
                $stmtPermDel->execute([$entityId, $studentId]);
                if ($stmtPermDel->rowCount() === 0) {
                    $_SESSION['flash_error'] = 'That entity does not belong to this student report.';
                } else {
                    $_SESSION['flash_success'] = 'Entity permanently deleted.';
                }
            } catch (Throwable $exception) {
                $_SESSION['flash_error'] = 'Failed to permanently delete entity: ' . userFacingError($exception, 'view_report.php:permanent_delete_entity');
            }
        }
    } elseif ($action === 'update_entity_type') {
        $entityId = (int) ($_POST['entity_id'] ?? 0);

        // Two choices only: IT Related or Clerical. Software/Hardware are not
        // selectable because no reporting query distinguishes them -- IT is
        // computed as activity_type != 'Clerical'.
        $newItRelated = (trim((string) ($_POST['it_related'] ?? 'yes')) === 'no') ? 'no' : 'yes';
        if ($entityId > 0) {
            try {
                $newActivityType = ($newItRelated === 'no') ? 'Clerical' : 'Other';
                $stmtUpd = $pdo->prepare("UPDATE report_entities
                                              SET activity_type = ?, it_related = ?
                                              WHERE id = ?
                                                AND report_id IN (SELECT id FROM reports WHERE student_id = ?)");
                $stmtUpd->execute([$newActivityType, $newItRelated, $entityId, $studentId]);
                if ($stmtUpd->rowCount() === 0) {
                    $_SESSION['flash_error'] = 'That entity does not belong to this student report.';
                } else {
                    $_SESSION['flash_success'] = 'Entity classification updated successfully.';
                }
            } catch (Throwable $e) {
                $_SESSION['flash_error'] = 'Failed to update classification: ' . userFacingError($e, 'view_report.php:update_entity_type');
            }
        }
    } 

    $redirectUrl = 'view_report.php?student_id=' . $studentId . ($targetReportId > 0 ? '&report_id=' . $targetReportId : '');
    header('Location: ' . $redirectUrl);
    exit();
}

$student = null;
$reportsList = [];
$activeReport = null;
$allEntities = [];
$weekEntities = [];
$archivedEntities = [];
$itPct = 0;
$clericalPct = 0;
$technicalCount = 0;
$clericalCount = 0;
$weekItPct = 0;
$weekClericalPct = 0;
$weekTotalCount = 0;
$pdfUrl = '';

try {
    $workspace = reportWorkspaceBuild($pdo, $studentId, [
        'reportId' => $selectedReportId > 0 ? $selectedReportId : $reportId,
        'status' => 'approved',
    ]);

    $student = $workspace['student'];

    if (!$student) {
        header('Location: approved_reports.php');
        exit();
    }

    $reportsList = $workspace['reportsList'];
    $activeReport = $workspace['activeReport'];
    $allEntities = $workspace['allEntities'];
    $weekEntities = $workspace['weekEntities'];
    $archivedEntities = $workspace['archivedEntities'];
    $itPct = $workspace['itPct'];
    $clericalPct = $workspace['clericalPct'];
    $technicalCount = $workspace['technicalCount'];
    $clericalCount = $workspace['clericalCount'];
    $weekItPct = $workspace['weekItPct'];
    $weekClericalPct = $workspace['weekClericalPct'];
    $weekTotalCount = $workspace['weekTotalCount'];
    $pdfUrl = $workspace['pdfUrl'];

    // Auto-extract entities on every view to pick up new predefined entities
    if (!empty($activeReport) && (int)$activeReport['id'] > 0) {
        $reportIdActive = (int)$activeReport['id'];
        $pdfPath = resolveReportPdfPath($activeReport['file_path'] ?? '');
        if ($pdfPath !== false) {
            extractEntitiesFromReport($pdfPath, $reportIdActive, $pdo, true);

            // Rebuild workspace to get updated entities
            $workspace = reportWorkspaceBuild($pdo, $studentId, [
                'reportId' => $selectedReportId > 0 ? $selectedReportId : $reportId,
                'status' => 'approved',
            ]);
            $student = $workspace['student'];
            $reportsList = $workspace['reportsList'];
            $activeReport = $workspace['activeReport'];
            $allEntities = $workspace['allEntities'];
            $weekEntities = $workspace['weekEntities'];
            $archivedEntities = $workspace['archivedEntities'];
            $itPct = $workspace['itPct'];
            $clericalPct = $workspace['clericalPct'];
            $technicalCount = $workspace['technicalCount'];
            $clericalCount = $workspace['clericalCount'];
            $weekItPct = $workspace['weekItPct'];
            $weekClericalPct = $workspace['weekClericalPct'];
            $weekTotalCount = $workspace['weekTotalCount'];
            $pdfUrl = $workspace['pdfUrl'];
        }
    }

    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        reportWorkspaceExportCsv($student, $reportsList, $allEntities);
        exit();
    }
} catch (Throwable $exception) {
    error_log('Error in coordinator/view_report.php: ' . $exception->getMessage());
    $allEntities = [];
}

require_once __DIR__ . '/../src/pages/coordinator/viewReportPage.php';