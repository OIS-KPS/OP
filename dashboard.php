<?php
// dashboard.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// 1. Fetch Student Info & Evaluation State
$stmtStudent = $pdo->prepare("
    SELECT 
        s.*, 
        u.name AS student_name,
        e.id AS evaluation_id,
        e.final_score
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN evaluations e ON e.student_id = s.id
    WHERE s.user_id = ?
    LIMIT 1
");
$stmtStudent->execute([$userId]);
$student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found. Please log in again.");
}

$studentId = (int)$student['id'];
$_SESSION['student_id'] = $studentId;

// 2. Fetch All Reports
$stmtReports = $pdo->prepare("
    SELECT * FROM reports 
    WHERE student_id = ? 
    ORDER BY week_number ASC
");
$stmtReports->execute([$studentId]);
$reports = $stmtReports->fetchAll(PDO::FETCH_ASSOC) ?: [];

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

// 4. Calculate Next Due Week
$nextWeek = 1;
while (in_array($nextWeek, $submittedWeeks)) {
    $nextWeek++;
}

// 5. Calculate OJT Target Progress (12 Weeks standard cohort target)
$targetWeeks = 12;
$progressPercentage = min(100, round(($totalApproved / $targetWeeks) * 100));

// 6. 2 Most Recent Reports for Snapshot Feed
$recentReports = array_slice(array_reverse($reports), 0, 2);

// Render Dashboard View
require_once __DIR__ . '/src/pages/student/dashboardPage.php';