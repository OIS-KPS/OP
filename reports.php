<?php
// reports.php
session_start();
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/report_deadlines.php';
require_once __DIR__ . '/config/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

// CSRF Guard: covers both evaluation-request branches below. Failures surface
// in error_message, which myReportsPage renders as a red banner.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify('reports.php', 'error_message');
}

$sessionUserId = (int)$_SESSION['user_id'];
$reports = [];
$student = null;

try {
    $stmtStudent = $pdo->prepare("
        SELECT 
            s.id, 
            s.student_number, 
            s.program, 
            s.company_id,
            s.completion_requested,
            s.evaluation_triggered, 
            s.ojt_start_date,
            e.id AS evaluation_id,
            e.final_score
        FROM students s
        LEFT JOIN evaluations e ON e.student_id = s.id
        WHERE s.user_id = ?
        LIMIT 1
    ");
    $stmtStudent->execute([$sessionUserId]);
    $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $studentId = (int)$student['id'];
        $_SESSION['student_id'] = $studentId;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_evaluation'])) {
            if (empty($student['evaluation_id']) && empty($student['completion_requested'])) {
                $stmtReq = $pdo->prepare("UPDATE students SET completion_requested = 1 WHERE id = ?");
                $stmtReq->execute([$studentId]);
                $_SESSION['flash_message'] = "Your final evaluation request has been submitted to the OJT Coordinator.";
            }
            header("Location: reports.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_evaluation_request'])) {
            if (empty($student['evaluation_id']) && !empty($student['completion_requested']) && empty($student['evaluation_triggered'])) {
                $stmtCancel = $pdo->prepare("UPDATE students SET completion_requested = 0 WHERE id = ?");
                $stmtCancel->execute([$studentId]);
                $_SESSION['flash_message'] = "Your final evaluation request has been cancelled.";
            }
            header("Location: reports.php");
            exit();
        }

        $stmtReports = $pdo->prepare("
            SELECT 
                id, 
                week_number, 
                file_path, 
                previous_file_path,
                ocr_activities, 
                supervisor_remarks,
                status, 
                is_late,
                submitted_at, 
                approved_at,
                updated_at,
                deadline_date
            FROM reports 
            WHERE student_id = ? 
            ORDER BY week_number ASC
        ");
        $stmtReports->execute([$studentId]);
        $reports = $stmtReports->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
} catch (PDOException $e) {
    error_log("Database Error in reports.php: " . $e->getMessage());
    $reports = [];
}

$totalReportsCount = count($reports);
$totalApproved = 0;
$totalPending = 0;
$totalRejected = 0;

foreach ($reports as $r) {
    $status = strtolower($r['status'] ?? 'pending');
    if ($status === 'approved') {
        $totalApproved++;
    } elseif ($status === 'rejected') {
        $totalRejected++;
    } else {
        $totalPending++;
    }
}

$ojtStart = isset($student) && $student ? warOjtStart($student) : null;
$currentWeek = $ojtStart ? warCurrentWeek($ojtStart) : null;
$hasCurrentWeekReport = false;
foreach ($reports as $r) {
    if ($currentWeek !== null && (int)($r['week_number'] ?? 0) === $currentWeek) {
        $hasCurrentWeekReport = true;
    }
}
$deadlineNotice = warDeadlineNotice($ojtStart, $currentWeek, $hasCurrentWeekReport);

require_once __DIR__ . '/src/pages/student/myReportsPage.php';