<?php
// supervisor/view_report.php
// Supervisor report workspace - same layout as the coordinator's view_report.

session_start();

require_once __DIR__ . '/../config/db.php';
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

/**
 * Apply a supervisor verdict to one of their intern's reports.
 */
function applySupervisorVerdict(PDO $pdo, int $supervisorId, int $reportId, string $action, string $remarks): string
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
        return 'This report does not belong to one of your interns.';
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

        return "Week {$weekNumber} report for {$studentName} was approved.";
    }

    if (trim($remarks) === '') {
        return 'Please describe the changes the intern needs to make.';
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

    return "Change request sent to {$studentName} for Week {$weekNumber}.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $targetReportId = (int) ($_POST['report_id'] ?? 0);

    if ($targetReportId <= 0) {
        $targetReportId = $requestedReportId;
    }

    $redirectUrl = 'view_report.php?student_id=' . $studentId
        . ($targetReportId > 0 ? '&report_id=' . $targetReportId : '');

    if ($action === 'approve' || $action === 'reject') {
        try {
            $notice = applySupervisorVerdict(
                $pdo,
                $supervisorId,
                $targetReportId,
                $action,
                (string) ($_POST['supervisor_remarks'] ?? '')
            );

            if ($action === 'approve' || trim((string) ($_POST['supervisor_remarks'] ?? '')) !== '') {
                $_SESSION['flash_success'] = $notice;
            } else {
                $_SESSION['flash_error'] = $notice;
            }
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
    error_log('Error loading report workspace in supervisor/view_report.php: ' . $exception->getMessage());
}

require_once __DIR__ . '/../src/pages/supervisor/viewReportPage.php';
