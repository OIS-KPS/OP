<?php
// dashboard.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// 1. Fetch Student Info & Primary Key
$stmtStudent = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmtStudent->execute([$userId]);
$student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found. Please log in again.");
}

$studentId = $student['id'];
$_SESSION['student_id'] = $studentId; // Ensure session is updated

// Fetch User Name for Display
$stmtUser = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$student['name'] = $user['name'] ?? 'Student';

// 2. Fetch All Reports for this Student
$stmtReports = $pdo->prepare("
    SELECT * FROM reports 
    WHERE student_id = ? 
    ORDER BY week_number ASC
");
$stmtReports->execute([$studentId]);
$reports = $stmtReports->fetchAll(PDO::FETCH_ASSOC);

// 3. Calculate Summary Statistics
$totalSubmitted = count($reports);
$totalApproved  = 0;
$totalPending   = 0;
$submittedWeeks = [];

foreach ($reports as $r) {
    $submittedWeeks[] = (int)$r['week_number'];
    $status = strtolower($r['status']);
    if ($status === 'approved') {
        $totalApproved++;
    } elseif ($status === 'pending') {
        $totalPending++;
    }
}

// 4. Calculate Next Due Week (e.g., if Week 1 submitted, Next Week is 2)
$nextWeek = 1;
while (in_array($nextWeek, $submittedWeeks)) {
    $nextWeek++;
}

// Render Dashboard View
require_once __DIR__ . '/src/pages/student/dashboardPage.php';