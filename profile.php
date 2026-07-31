<?php
// profile.php
session_start();

require_once __DIR__ . '/config/db.php';

// TEMPORARY DEV MODE: Set a default student ID
$student_id = $_SESSION['student_id'] ?? 1;

try {
    $sql = "SELECT 
                s.id,
                s.name,
                s.email,
                s.student_number,
                s.program,
                s.avatar_url,
                c.name AS company_name,
                sup.name AS supervisor_name
            FROM students s
            LEFT JOIN companies c ON s.company_id = c.id
            LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
            WHERE s.id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Placeholder label fallback if DB row isn't populated yet
    if (!$student) {
        $student = [
            'name' => '[Student Full Name]',
            'email' => '[student.email@nbsc.edu.ph]',
            'student_number' => '[Student ID]',
            'program' => '[Program / Degree]',
            'company_name' => null,
            'supervisor_name' => null,
            'avatar_url' => null
        ];
    }
} catch (Exception $e) {
    // Placeholder label fallback if database connection/tables aren't ready
    $student = [
        'name' => '[Student Full Name]',
        'email' => '[student.email@nbsc.edu.ph]',
        'student_number' => '[Student ID]',
        'program' => '[Program / Degree]',
        'company_name' => null,
        'supervisor_name' => null,
        'avatar_url' => null
    ];
}

// Render the View Template
require_once __DIR__ . '/src/pages/student/profilePage.php';