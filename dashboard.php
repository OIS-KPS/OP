<?php
// dashboard.php
session_start();

require_once __DIR__ . '/config/db.php';

// TEMPORARY DEV MODE: Default session ID for local testing
$student_id = $_SESSION['student_id'] ?? 1;

try {
    // 1. Fetch Student Info
    $stmt = $pdo->prepare("SELECT id, name, student_number, program FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Placeholder label fallback if DB row isn't populated yet
    if (!$student) {
        $student = [
            'name'           => '[Student Full Name]',
            'student_number' => '[Student ID]',
            'program'        => '[Program / Degree]'
        ];
    }

    // 2. Fetch Weekly Reports History & Aggregates
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
        'name'           => '[Student Full Name]',
        'student_number' => '[Student ID]',
        'program'        => '[Program / Degree]'
    ];
    $reports        = [];
    $totalSubmitted = 0;
    $totalApproved  = 0;
    $totalPending   = 0;
    $nextWeek       = 1;
}

// Render the View Template
require_once __DIR__ . '/src/pages/student/dashboardPage.php';