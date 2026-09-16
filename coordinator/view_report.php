<?php
// coordinator/view_report.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header('Location: ../auth/login.php');
    exit();
}

$pageTitle = 'Report Inspection & Entities';

// Support both ?student_id=X (new student workspace) and ?id=Y (legacy report view)
$studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
$reportId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$selectedReportId = isset($_GET['report_id']) ? (int) $_GET['report_id'] : 0;
$forceExtraction = isset($_GET['extract']) && $_GET['extract'] === '1';

if ($studentId <= 0 && ($reportId > 0 || $selectedReportId > 0)) {
    $lookupId = $selectedReportId > 0 ? $selectedReportId : $reportId;
    $stmtR = $pdo->prepare("SELECT student_id FROM reports WHERE id = ? LIMIT 1");
    $stmtR->execute([$lookupId]);
    $studentId = (int) ($stmtR->fetchColumn() ?: 0);
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
        $category = trim((string) ($_POST['category'] ?? 'Other')) ?: 'Other';
        $classification = trim((string) ($_POST['classification'] ?? 'Software'));
        $activityType = in_array($classification, ['Software', 'Hardware', 'Clerical', 'Other'], true) ? $classification : 'Other';
        $itRelated = $activityType === 'Clerical' ? 'no' : 'yes';

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
                    $_SESSION['flash_success'] = 'Missing entity added successfully.';
                }
            } catch (Throwable $exception) {
                $_SESSION['flash_error'] = 'Failed to add entity: ' . $exception->getMessage();
            }
        }
    } elseif ($action === 'delete_entity') {
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        if ($entityId > 0) {
            try {
                $stmtArchive = $pdo->prepare('UPDATE report_entities SET is_archived = 1 WHERE id = ?');
                $stmtArchive->execute([$entityId]);
                $_SESSION['flash_success'] = 'Entity archived successfully.';
            } catch (Throwable $exception) {
                error_log('Archive entity error: ' . $exception->getMessage());
            }
        }
    } elseif ($action === 'restore_entity') {
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        if ($entityId > 0) {
            try {
                $stmtRestore = $pdo->prepare('UPDATE report_entities SET is_archived = 0 WHERE id = ?');
                $stmtRestore->execute([$entityId]);
                $_SESSION['flash_success'] = 'Entity restored successfully.';
            } catch (Throwable $exception) {
                $_SESSION['flash_error'] = 'Failed to restore entity: ' . $exception->getMessage();
            }
        }
    } elseif ($action === 'permanent_delete_entity') {
        // Permanently delete from the database
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        if ($entityId > 0) {
            try {
                $stmtPermDel = $pdo->prepare('DELETE FROM report_entities WHERE id = ?');
                $stmtPermDel->execute([$entityId]);
                $_SESSION['flash_success'] = 'Entity permanently deleted.';
            } catch (Throwable $exception) {
                $_SESSION['flash_error'] = 'Failed to permanently delete entity: ' . $exception->getMessage();
            }
        }
    } elseif ($action === 'update_entity_type') {
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        $newActivityType = trim((string) ($_POST['activity_type'] ?? 'Software'));
        if (in_array($newActivityType, ['Software', 'Hardware', 'Clerical', 'Other'], true) && $entityId > 0) {
            try {
                $newItRelated = ($newActivityType === 'Clerical') ? 'no' : 'yes';
                $stmtUpd = $pdo->prepare("UPDATE report_entities SET activity_type = ?, it_related = ? WHERE id = ?");
                $stmtUpd->execute([$newActivityType, $newItRelated, $entityId]);
                $_SESSION['flash_success'] = 'Entity classification updated successfully.';
            } catch (Throwable $e) {
                $_SESSION['flash_error'] = 'Failed to update classification: ' . $e->getMessage();
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
$rawAllEntities = [];
$allEntities = [];
$weekEntities = [];
$archivedEntities = [];
$itPct = 0;
$clericalPct = 0;
$technicalCount = 0;
$clericalCount = 0;

try {
    if ($studentId > 0) {
        $stmtStudent = $pdo->prepare(
            'SELECT 
                s.id AS student_id, s.student_number, s.program, COALESCE(s.section, "A") AS section,
                u.name AS student_name, u.email AS student_email, u.avatar_url AS student_avatar,
                c.name AS company_name, c.department AS company_dept,
                u_sup.name AS supervisor_name
             FROM students s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN companies c ON s.company_id = c.id
             LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
             LEFT JOIN users u_sup ON sup.user_id = u_sup.id
             WHERE s.id = ?
             LIMIT 1'
        );
        $stmtStudent->execute([$studentId]);
        $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);
    }

    if (!$student) {
        header('Location: approved_reports.php');
        exit();
    }

    $stmtReports = $pdo->prepare(
        'SELECT id, week_number, file_path, status, submitted_at, approved_at, updated_at
         FROM reports
         WHERE student_id = ?
         ORDER BY week_number ASC'
    );
    $stmtReports->execute([$studentId]);
    $reportsList = $stmtReports->fetchAll(PDO::FETCH_ASSOC) ?: [];

    if (!empty($reportsList)) {
        if ($selectedReportId > 0 || $reportId > 0) {
            $targetId = $selectedReportId > 0 ? $selectedReportId : $reportId;
            foreach ($reportsList as $rep) {
                if ((int)$rep['id'] === $targetId) {
                    $activeReport = $rep;
                    break;
                }
            }
        }
        if (!$activeReport) {
            $activeReport = end($reportsList);
            reset($reportsList);
        }
    }

    $reportIds = array_column($reportsList, 'id');
    if (!empty($reportIds)) {
        $placeholders = implode(',', array_fill(0, count($reportIds), '?'));
        
        // Fetch Active Entities
        $stmtEnt = $pdo->prepare(
            "SELECT 
                re.id, re.report_id, re.entity_name, re.canonical_name, re.category,
                re.activity_type, re.activity_type AS classification,
                re.it_related, re.source, re.confidence_score, re.created_at,
                r.week_number
             FROM report_entities re
             JOIN reports r ON re.report_id = r.id
             WHERE re.report_id IN ($placeholders) AND re.is_archived = 0
             ORDER BY r.week_number ASC, re.id DESC"
        );
        $stmtEnt->execute($reportIds);
        $rawAllEntities = $stmtEnt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Fetch Archived Entities
        $stmtArch = $pdo->prepare(
            "SELECT 
                re.id, re.report_id, re.entity_name, re.category, re.activity_type, r.week_number
             FROM report_entities re
             JOIN reports r ON re.report_id = r.id
             WHERE re.report_id IN ($placeholders) AND re.is_archived = 1
             ORDER BY r.week_number ASC, re.id DESC"
        );
        $stmtArch->execute($reportIds);
        $archivedEntities = $stmtArch->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // REMOVE REDUNDANCY / DUPLICATE ENTITIES PER WEEK
    $seenWeekEntities = [];
    foreach ($rawAllEntities as $entity) {
        $weekNum = (int) ($entity['week_number'] ?? 0);
        $cleanName = strtolower(trim($entity['entity_name'] ?? ''));
        $dedupKey = $weekNum . '|' . $cleanName;

        if (!isset($seenWeekEntities[$dedupKey])) {
            $seenWeekEntities[$dedupKey] = true;
            $allEntities[] = $entity;
        }
    }

   // Handle CSV Export Request (Prioritizing Weekly Report Percentages)
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        $safeStudentName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $student['student_name'] ?? 'student');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $safeStudentName . '_weekly_reports_summary.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Week Number', 'Report Status', 'Submitted Date', 'IT Percentage (%)', 'Clerical Percentage (%)']);
        
        // Loop through all reports and compute each week's specific percentage
        foreach ($reportsList as $rep) {
            $repId = (int)$rep['id'];
            $wTech = 0;
            $wCler = 0;
            
            foreach ($allEntities as $ent) {
                if ((int)$ent['report_id'] === $repId) {
                    $actType = strtolower(trim((string)($ent['activity_type'] ?? '')));
                    if ($actType === 'clerical') {
                        $wCler++;
                    } elseif ($actType !== '') {
                        $wTech++;
                    }
                }
            }
            
            $wTotal = $wTech + $wCler;
            $wItPct = $wTotal > 0 ? round(($wTech / $wTotal) * 100, 1) : 0.0;
            $wClerPct = $wTotal > 0 ? round(($wCler / $wTotal) * 100, 1) : 0.0;
            
            fputcsv($output, [
                'Week ' . (int)$rep['week_number'],
                ucfirst($rep['status'] ?? 'N/A'),
                $rep['submitted_at'] ?? 'N/A',
                $wItPct . '%',
                $wClerPct . '%'
            ]);
        }
        
        fclose($output);
        exit();
    }

    $activeReportId = $activeReport ? (int)$activeReport['id'] : 0;
    $weekEntities = array_filter($allEntities, function($e) use ($activeReportId) {
        return (int)$e['report_id'] === $activeReportId;
    });

    foreach ($allEntities as &$entity) {
        $activityType = strtolower(trim((string) ($entity['activity_type'] ?? '')));
        $itRelated = strtolower(trim((string) ($entity['it_related'] ?? 'unknown')));

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

    $weekTechCount = 0;
    $weekClericalCount = 0;
    foreach ($weekEntities as $ent) {
        $actType = strtolower(trim((string) ($ent['activity_type'] ?? '')));
        if ($actType === 'clerical') {
            $weekClericalCount++;
        } elseif ($actType !== '') {
            $weekTechCount++;
        }
    }
    
    $weekTotalCount = $weekTechCount + $weekClericalCount;
    $weekItPct = 0;
    $weekClericalPct = 0;
    if ($weekTotalCount > 0) {
        $weekItPct = round(($weekTechCount / $weekTotalCount) * 100, 1);
        $weekClericalPct = round(($weekClericalCount / $weekTotalCount) * 100, 1);
    }

} catch (Throwable $exception) {
    error_log('Error in coordinator/view_report.php: ' . $exception->getMessage());
    $allEntities = [];
}

require_once __DIR__ . '/../src/pages/coordinator/viewReportPage.php';