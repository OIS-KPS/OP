<?php
// dashboard.php
session_start();

require_once __DIR__ . '/config/db.php';

// 1. Auth Guard: Ensure the user is logged in and is a student
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

// 2. Get the logged-in student's ID from session
$student_id = $_SESSION['user_id'];

try {
    // 3. Fetch Student Info
    $stmt = $pdo->prepare("SELECT id, name, student_number, program FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fallback if record is missing in DB
    if (!$student) {
        $student = [
            'name'           => $_SESSION['user_name'] ?? 'Student',
            'student_number' => 'N/A',
            'program'        => 'BSIT'
        ];
    }

    // 4. Fetch Weekly Reports History & Aggregates
    $reportsStmt = $pdo->prepare("SELECT * FROM reports WHERE student_id = ? ORDER BY week_number ASC");
    $reportsStmt->execute([$student_id]);
    $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Calculate Dynamic Metrics
    $totalSubmitted = count($reports);
    $totalApproved  = count(array_filter($reports, fn($r) => $r['status'] === 'Approved'));
    $totalPending   = count(array_filter($reports, fn($r) => $r['status'] === 'Pending'));
    $nextWeek       = $totalSubmitted + 1;

} catch (Exception $e) {
    // Graceful error handle if DB/tables are not ready yet
    $student = [
        'name'           => $_SESSION['user_name'] ?? 'Student',
        'student_number' => 'N/A',
        'program'        => 'BSIT'
    ];
    $reports        = [];
    $totalSubmitted = 0;
    $totalApproved  = 0;
    $totalPending   = 0;
    $nextWeek       = 1;
}

// Render the View Template
require_once __DIR__ . '/src/pages/student/dashboardPage.php';