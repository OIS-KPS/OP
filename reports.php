<?php
// reports.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$sessionUserId = $_SESSION['user_id'];
$reports = [];

try {
    // 1. Get the student_id from students table using the logged in user_id
    $stmtStudent = $pdo->prepare("SELECT id FROM students WHERE user_id = ? LIMIT 1");
    $stmtStudent->execute([$sessionUserId]);
    $studentId = $stmtStudent->fetchColumn();

    // 2. Fetch all reports matching the database schema
    if ($studentId) {
        $_SESSION['student_id'] = $studentId;

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

// 3. Impartial Summary Stats
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

// 4. Load View Template
require_once __DIR__ . '/src/pages/student/myReportsPage.php';