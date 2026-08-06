<?php
// dashboard.php
session_start();

require_once __DIR__ . '/config/db.php';

// 1. Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

try {
    // 2. Fetch Student Info (Including email)
    $stmt = $pdo->prepare("SELECT id, name, student_number, program, email FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Extract Student ID from email if student_number is empty
    $userEmail = $student['email'] ?? $_SESSION['email'] ?? '';
    
    if (!empty($userEmail) && str_contains($userEmail, '@')) {
        // Splitting "20231053@nbsc.edu.ph" gives "20231053"
        $extractedId = explode('@', $userEmail)[0];
    } else {
        $extractedId = 'N/A';
    }

    // Determine final student number (DB value -> Extracted Email value -> Fallback)
    $finalStudentNumber = !empty($student['student_number']) ? $student['student_number'] : $extractedId;

    if (!$student) {
        $student = [
            'name'           => $_SESSION['user_name'] ?? 'Student',
            'student_number' => $finalStudentNumber,
            'program'        => 'BSIT'
        ];
    } else {
        $student['student_number'] = $finalStudentNumber;
        $student['program']        = !empty($student['program']) ? $student['program'] : 'BSIT';
    }

    // 4. Fetch Reports
    $reportsStmt = $pdo->prepare("SELECT * FROM reports WHERE student_id = ? ORDER BY week_number ASC");
    $reportsStmt->execute([$student_id]);
    $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $totalSubmitted = count($reports);
    $totalApproved  = count(array_filter($reports, fn($r) => $r['status'] === 'Approved'));
    $totalPending   = count(array_filter($reports, fn($r) => $r['status'] === 'Pending'));
    $nextWeek       = $totalSubmitted + 1;

} catch (Exception $e) {
    $student = [
        'name'           => $_SESSION['user_name'] ?? 'Student',
        'student_number' => !empty($_SESSION['email']) ? explode('@', $_SESSION['email'])[0] : 'N/A',
        'program'        => 'BSIT'
    ];
    $reports        = [];
    $totalSubmitted = 0;
    $totalApproved  = 0;
    $totalPending   = 0;
    $nextWeek       = 1;
}

// Render View
require_once __DIR__ . '/src/pages/student/dashboardPage.php';