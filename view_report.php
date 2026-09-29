<?php
// view_report.php
// Student report workspace - same layout as the coordinator's view_report.

session_start();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/src/services/report_workspace.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header('Location: auth/login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$requestedReportId = isset($_GET['report_id']) ? (int) $_GET['report_id'] : 0;
if ($requestedReportId <= 0 && isset($_GET['id'])) {
    $requestedReportId = (int) $_GET['id'];
}

$student = null;
$studentId = 0;
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

try {
    $stmtStudent = $pdo->prepare('SELECT id FROM students WHERE user_id = ? LIMIT 1');
    $stmtStudent->execute([$userId]);
    $studentId = (int) ($stmtStudent->fetchColumn() ?: 0);
} catch (Throwable $exception) {
    error_log('Error resolving student in view_report.php: ' . $exception->getMessage());
}

if ($studentId <= 0) {
    header('Location: reports.php');
    exit();
}

// A report_id may only ever point at one of the signed-in student's own reports.
if ($requestedReportId > 0 && reportWorkspaceStudentIdForReport($pdo, $requestedReportId) !== $studentId) {
    $requestedReportId = 0;
}

try {
    $workspace = reportWorkspaceBuild($pdo, $studentId, [
        'reportId' => $requestedReportId,
        'status' => null,
    ]);

    if ($workspace['student'] === null) {
        header('Location: reports.php');
        exit();
    }

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
    error_log('Error loading report workspace in view_report.php: ' . $exception->getMessage());
}

require_once __DIR__ . '/src/pages/student/viewReportPage.php';
