<?php
// reports.php
session_start();

require_once __DIR__ . '/config/db.php';

// 🛑 Commented out auth guard temporarily so you can preview without login
/*
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
*/

$student_id = $_SESSION['student_id'] ?? 1;

// Default values so the page won't break if database tables aren't created/populated yet
$student = [
    'name' => 'Katelyn Coming',
    'student_number' => '20231053',
    'program' => 'BSIT'
];
$reports = [];

try {
    // 1. Fetch Student Profile
    $stmtStudent = $pdo->prepare("SELECT name, student_number, program FROM students WHERE id = ?");
    $stmtStudent->execute([$student_id]);
    $fetched = $stmtStudent->fetch();
    if ($fetched) $student = $fetched;

    // 2. Fetch Reports
    $stmtReports = $pdo->prepare("SELECT week_number, it_percent, clerical_percent, status, attachment_path, created_at FROM reports WHERE student_id = ? ORDER BY week_number ASC");
    $stmtReports->execute([$student_id]);
    $reports = $stmtReports->fetchAll();
} catch (Exception $e) {
    // Safely handles missing DB tables while testing
}

// 3. Dynamic Averages Calculation
$totalReportsCount = count($reports);
$overallIT = 0;
$overallClerical = 0;

if ($totalReportsCount > 0) {
    $sumIT = array_sum(array_column($reports, 'it_percent'));
    $sumClerical = array_sum(array_column($reports, 'clerical_percent'));
    
    $overallIT = round($sumIT / $totalReportsCount);
    $overallClerical = round($sumClerical / $totalReportsCount);
}

// 4. Load the view layout
require_once __DIR__ . '/src/pages/student/myReportsPage.php';