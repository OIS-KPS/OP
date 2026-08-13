<?php
// reports.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Default fallback values
$student = [
    'name' => $_SESSION['user_name'] ?? 'Student',
    'student_number' => 'N/A',
    'program' => 'BSIT'
];
$reports = [];

try {
    // 1. Fetch Student Profile by joining students with users table
    $stmtStudent = $pdo->prepare("
        SELECT u.name, s.id AS student_id, s.student_number, s.program 
        FROM students s 
        JOIN users u ON s.user_id = u.id 
        WHERE u.id = ?
    ");
    $stmtStudent->execute([$userId]);
    $fetchedStudent = $stmtStudent->fetch(PDO::FETCH_ASSOC);

    if ($fetchedStudent) {
        $student = $fetchedStudent;
        $studentId = $fetchedStudent['student_id'];
        $_SESSION['student_id'] = $studentId; // Sync session identifier
    } else {
        $studentId = $_SESSION['student_id'] ?? 1;
    }

    // 2. Fetch Reports using official 3NF schema (file_path, submitted_at)
    $stmtReports = $pdo->prepare("
        SELECT id, week_number, file_path, ocr_activities, status, submitted_at 
        FROM reports 
        WHERE student_id = ? 
        ORDER BY week_number ASC
    ");
    $stmtReports->execute([$studentId]);
    $reports = $stmtReports->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Log error safely while maintaining UI stability
    error_log("Database Error in reports.php: " . $e->getMessage());
}

// 3. Dynamic Summary Stats
$totalReportsCount = count($reports);
$totalApproved = 0;
$totalPending = 0;

foreach ($reports as $r) {
    $status = strtolower($r['status'] ?? 'pending');
    if ($status === 'approved') {
        $totalApproved++;
    } elseif ($status === 'pending') {
        $totalPending++;
    }
}

// 4. Load the view layout template
require_once __DIR__ . '/src/pages/student/myReportsPage.php';