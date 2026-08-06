<?php
// profile.php
session_start();

require_once __DIR__ . '/config/db.php';

// 1. Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

try {
    // 2. Fetch Student details including company/supervisor relationships if joined
    $stmt = $pdo->prepare("SELECT id, name, student_number, program, email, company_name, supervisor_name, status FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Extract Student ID fallback from email
    $userEmail   = $student['email'] ?? $_SESSION['email'] ?? '';
    $extractedId = (!empty($userEmail) && str_contains($userEmail, '@')) ? explode('@', $userEmail)[0] : 'N/A';
    
    $studentIdNum = !empty($student['student_number']) ? $student['student_number'] : $extractedId;

    if (!$student) {
        $student = [
            'name'            => $_SESSION['user_name'] ?? 'Student',
            'email'           => $userEmail,
            'student_number'  => $studentIdNum,
            'program'         => 'BSIT',
            'company_name'    => null,
            'supervisor_name' => null,
            'status'          => 'Pending Assignment'
        ];
    } else {
        $student['student_number'] = $studentIdNum;
        $student['program']        = !empty($student['program']) ? $student['program'] : 'BSIT';
    }

} catch (Exception $e) {
    $student = [
        'name'            => $_SESSION['user_name'] ?? 'Student',
        'email'           => $_SESSION['email'] ?? '',
        'student_number'  => !empty($_SESSION['email']) ? explode('@', $_SESSION['email'])[0] : 'N/A',
        'program'         => 'BSIT',
        'company_name'    => null,
        'supervisor_name' => null,
        'status'          => 'Pending Assignment'
    ];
}

// Render the View Template
require_once __DIR__ . '/src/pages/student/profilePage.php';