<?php
// supervisor/view_report.php
// Supervisor report workspace - same layout as the coordinator's view_report.

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../src/services/entity_extraction.php';
require_once __DIR__ . '/../src/services/report_workspace.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header('Location: ../auth/login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];

$stmtSupervisor = $pdo->prepare('SELECT id FROM supervisors WHERE user_id = ? LIMIT 1');
$stmtSupervisor->execute([$userId]);
$supervisorId = (int) ($stmtSupervisor->fetchColumn() ?: 0);

if ($supervisorId <= 0) {
    die('Supervisor profile not found. Please contact administrator.');
}

$_SESSION['supervisor_id'] = $supervisorId;

$studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
$requestedReportId = isset($_GET['report_id']) ? (int) $_GET['report_id'] : 0;
if ($requestedReportId <= 0 && isset($_GET['id'])) {
    $requestedReportId = (int) $_GET['id'];
}

if ($studentId <= 0 && $requestedReportId > 0) {
    $studentId = reportWorkspaceStudentIdForReport($pdo, $requestedReportId);
}

/**
 * Resolve PDF path.
 *
 * Moved to src/services/report_workspace.php. This file and
 * coordinator/view_report.php each had their own copy with a different return type
 * and a different set of fallback candidates, and the background extraction worker
 * needs the same resolution logic from outside the web context.
 *
 * Note the shared version returns null on failure rather than false, so the
 * comparison at the call site below changed with it.
 */

/**
 * A supervisor may only inspect interns assigned to them.
 */
function supervisorOwnsStudent(PDO $pdo, int $supervisorId, int $studentId): bool
{
    if ($supervisorId <= 0 || $studentId <= 0) {
        return false;
    }

    $stmt = $pdo->prepare('SELECT id FROM students WHERE id = ? AND supervisor_id = ? LIMIT 1');
    $stmt->execute([$studentId, $supervisorId]);

    return (bool) $stmt->fetchColumn();
}

if (!supervisorOwnsStudent($pdo, $supervisorId, $studentId)) {
    header('Location: review_reports.php');
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
$weekItPct = 0;
$weekClericalPct = 0;
$weekTotalCount = 0;
$pdfUrl = '';
$pageError = '';

/*
 * Background-extraction state for the view. Seeded empty because the block
 * below only fills them in when there is an active report to extract, and the
 * view renders either way.
 */
$extractionStatus = [
    'report_id'    => 0,
    'extracted_at' => null,
    'started_at'   => null,
    'error'        => null,
    'pending'      => false,
];
$extractionPending = false;

/**
 * Apply a supervisor verdict to one of their intern's reports.
 *
 * Returns ['ok' => bool, 'message' => string] rather than a bare string: the
 * caller used to re-guess success from the POST payload, which reported an
 * ownership failure or an empty remarks box as a success banner.
 */
function applySupervisorVerdict(PDO $pdo, int $supervisorId, int $reportId, string $action, string $remarks): array
{
    $stmt = $pdo->prepare(
        'SELECT r.week_number, u.name AS student_name
         FROM reports r
         JOIN students s ON s.id = r.student_id
         JOIN users u ON u.id = s.user_id
         WHERE r.id = ? AND s.supervisor_id = ?
         LIMIT 1'
    );
    $stmt->execute([$reportId, $supervisorId]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$target) {
        return [
            'ok' => false,
            'message' => 'This report does not belong to one of your interns.'
        ];
    }

    $weekNumber = $target['week_number'];
    $studentName = $target['student_name'];

    if ($action === 'approve') {
        $pdo->prepare(
            "UPDATE reports
             SET status = 'approved', supervisor_remarks = NULL, approved_at = NOW(), updated_at = NOW()
             WHERE id = ?"
        )->execute([$reportId]);

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            'supervisor',
            'REPORT_APPROVAL',
            "Supervisor approved Week {$weekNumber} accomplishment report for {$studentName}."
        );

        return [
            'ok' => true,
            'message' => "Week {$weekNumber} report for {$studentName} was approved."
        ];
    }

    if (trim($remarks) === '') {
        return [
            'ok' => false,
            'message' => 'Please describe the changes the intern needs to make.'
        ];
    }

    $pdo->prepare(
        "UPDATE reports
         SET status = 'rejected', supervisor_remarks = ?, updated_at = NOW()
         WHERE id = ?"
    )->execute([trim($remarks), $reportId]);

    logActivity(
        $pdo,
        $_SESSION['user_id'],
        'supervisor',
        'REPORT_REVISION',
        "Supervisor requested changes on Week {$weekNumber} accomplishment report for {$studentName}."
    );

    return [
        'ok' => true,
        'message' => "Change request sent to {$studentName} for Week {$weekNumber}."
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $targetReportId = (int) ($_POST['report_id'] ?? 0);

    if ($targetReportId <= 0) {
        $targetReportId = $requestedReportId;
    }

    $redirectUrl = 'view_report.php?student_id=' . $studentId
        . ($targetReportId > 0 ? '&report_id=' . $targetReportId : '');

    // CSRF Guard: the verdict form in src/pages/shared/report_workspace.php
    // already posts a token. Placed after $redirectUrl so a rejection lands the
    // supervisor back on the same week, and before any verdict is written.
    csrf_verify($redirectUrl);

    if ($action === 'approve' || $action === 'reject') {
        try {
            $result = applySupervisorVerdict(
                $pdo,
                $supervisorId,
                $targetReportId,
                $action,
                (string) ($_POST['supervisor_remarks'] ?? '')
            );

            $_SESSION[$result['ok'] ? 'flash_success' : 'flash_error'] = $result['message'];
        } catch (Throwable $exception) {
            error_log('Error updating report in supervisor/view_report.php: ' . $exception->getMessage());
            $_SESSION['flash_error'] = 'Unable to update the report right now.';
        }
    }

    header('Location: ' . $redirectUrl);
    exit();
}

if ($requestedReportId > 0 && reportWorkspaceStudentIdForReport($pdo, $requestedReportId) !== $studentId) {
    $requestedReportId = 0;
}

try {
    $workspace = reportWorkspaceBuild($pdo, $studentId, [
        'reportId' => $requestedReportId,
        'status' => null,
    ]);

    $student = $workspace['student'];
    $reportsList = $workspace['reportsList'];
    $activeReport = $workspace['activeReport'];
    $allEntities = $workspace['allEntities'];
    $weekEntities = $workspace['weekEntities'];
    $archivedEntities = $workspace['archivedEntities'];
    $itPct = $workspace['itPct'];
    $clericalPct = $workspace['clericalPct'];
    $weekItPct = $workspace['weekItPct'];
    $weekClericalPct = $workspace['weekClericalPct'];
    $weekTotalCount = $workspace['weekTotalCount'];
    $pdfUrl = $workspace['pdfUrl'];
} catch (Throwable $exception) {
    /*
     * The workspace is the page; if it cannot be built there is nothing left
     * to render, so surface the reason instead of showing an empty shell. The
     * banner is read (and unset) by src/pages/shared/report_workspace.php.
     */
    error_log('Error loading report workspace in supervisor/view_report.php: ' . $exception->getMessage());

    $pageError = 'Unable to load this report right now. Please try again later.';

    $_SESSION['flash_error'] = $pageError;
}

/*
 * Extract only when this report has never been extracted, or when the Entity
 * Dictionary has changed since the last run. Previously this ran on EVERY view
 * of a report, costing several seconds per click for a result that almost
 * never changed.
 *
 * Supervisors have no manual re-extract control; that is coordinator-only.
 *
 * Kept out of the workspace try/catch on purpose: a FastAPI outage should
 * never cost the supervisor the page, only the refreshed entity list.
 */
if (!empty($activeReport) && (int) $activeReport['id'] > 0 && $pageError === '') {
    $reportIdActive = (int) $activeReport['id'];

    // Seeded here so the view always has them, whichever branch is taken below.
    $extractionStatus = entityExtractionReportStatus($pdo, $reportIdActive);
    $extractionPending = false;

    $pdfPath = resolveReportPdfPath($activeReport['file_path'] ?? '');

    if ($pdfPath === null) {
        // Only worth warning about if extraction would otherwise have run.
        if (reportNeedsEntityExtraction($pdo, $reportIdActive)) {
            $_SESSION['flash_error'] =
                'The uploaded PDF for this report could not be found on the server. ' .
                'Entity extraction was skipped.';
        }

        error_log(
            'Missing report PDF for report ' . $reportIdActive .
            ' (stored path: ' . ($activeReport['file_path'] ?? 'empty') . ')'
        );
    } else {
        try {
            /*
             * $async = true. Extraction is a transformer inference that takes
             * seconds, and doing it inline made the supervisor wait on a spinner
             * for a list they may not have been looking at. The work now goes to
             * src/cli/extract_report.php and this page renders straight away;
             * the entities appear on refresh, and viewReportPage.php polls for
             * them via api/report_entities_status.php.
             *
             * Nothing is lost if the worker cannot be spawned -- the service
             * falls back to running inline, so 'ran' is still true and the
             * workspace is still rebuilt below.
             */
            $extraction = ensureReportEntitiesExtracted(
                $pdfPath,
                $reportIdActive,
                $pdo,
                false,
                true
            );

            // Feeds the "analyzing" banner in the view.
            $extractionPending = !empty($extraction['pending']);

            // Re-read only when extraction actually ran. On a skipped or
            // deferred run the stored rows are unchanged, so the workspace read
            // at the top of this request is already correct.
            if (!empty($extraction['ran'])) {
                $workspace = reportWorkspaceBuild($pdo, $studentId, [
                    'reportId' => $requestedReportId,
                    'status' => null,
                ]);

                $student = $workspace['student'];
                $reportsList = $workspace['reportsList'];
                $activeReport = $workspace['activeReport'];
                $allEntities = $workspace['allEntities'];
                $weekEntities = $workspace['weekEntities'];
                $archivedEntities = $workspace['archivedEntities'];
                $itPct = $workspace['itPct'];
                $clericalPct = $workspace['clericalPct'];
                $weekItPct = $workspace['weekItPct'];
                $weekClericalPct = $workspace['weekClericalPct'];
                $weekTotalCount = $workspace['weekTotalCount'];
                $pdfUrl = $workspace['pdfUrl'];
            }
        } catch (Throwable $exception) {
            error_log(
                'Error extracting entities for report ' . $reportIdActive . ': ' .
                $exception->getMessage()
            );

            $_SESSION['flash_error'] =
                'Entity extraction failed, so the entity list may be out of date. ' .
                'The report itself is still shown below.';
        }
    }

    /*
     * Re-read after the attempt. A spawn that fails, a job that is already in
     * flight, and a fresh report all resolve to a different status than the one
     * seeded above, and the banner in the view is built from this.
     */
    $extractionStatus = entityExtractionReportStatus($pdo, $reportIdActive);
}

require_once __DIR__ . '/../src/pages/supervisor/viewReportPage.php';
