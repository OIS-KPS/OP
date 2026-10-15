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
    $pdfUrl = $workspace['pdfUrl'];

    // No auto-extraction here, unlike coordinator/view_report.php and
    // supervisor/view_report.php. Extraction re-runs the transformer over the
    // PDF on every view and rewrites report_entities; a student has no entity
    // panel to feed and no authority over those rows, so their page load should
    // not pay for the model or write to the table. Whatever the coordinator last
    // extracted is still read by reportWorkspaceBuild() but never rendered.
} catch (Throwable $exception) {
    error_log('Error loading report workspace in view_report.php: ' . $exception->getMessage());
}

require_once __DIR__ . '/src/pages/student/viewReportPage.php';
