<?php
// reports.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$sessionUserId = (int)$_SESSION['user_id'];
$reports = [];
$student = null;

try {
    // 1. Get student profile & evaluation state
    $stmtStudent = $pdo->prepare("
        SELECT 
            s.id, 
            s.student_number, 
            s.program, 
            s.completion_requested,
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

        // 2. Handle Final Evaluation Request Form
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_evaluation'])) {
            if (empty($student['evaluation_id']) && empty($student['completion_requested'])) {
                $stmtReq = $pdo->prepare("UPDATE students SET completion_requested = 1 WHERE id = ?");
                $stmtReq->execute([$studentId]);
                $_SESSION['flash_message'] = "Your final evaluation request has been submitted to your supervisor.";
            }
            header("Location: reports.php");
            exit();
        }

        // 3. Fetch all reports
        $stmtReports = $pdo->prepare("
            SELECT 
                id, 
                week_number, 
                file_path, 
                ocr_activities,
                status, 
                submitted_at 
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

// 4. Calculate Summary Statistics
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

// Load View Template
require_once __DIR__ . '/src/pages/student/myReportsPage.php';