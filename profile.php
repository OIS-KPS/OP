<?php
// profile.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$sessionUserId = (int)$_SESSION['user_id'];
$flashSuccess  = $_SESSION['flash_success'] ?? '';
$flashError    = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

try {
    // Correct relational query mapped directly to the nbsc_ojt schema
    $stmt = $pdo->prepare("
        SELECT 
            s.id AS student_table_id,
            u.id AS user_id,
            u.name AS student_name,
            u.email AS student_email,
            u.avatar_url,
            s.student_number,
            s.program,
            s.section,
            c.name AS company_name,
            c.address AS company_address,
            o.name AS office_name,
            sup_user.name AS supervisor_name,
            sup_user.email AS supervisor_email
        FROM users u
        INNER JOIN students s ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN offices o ON s.office_id = o.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users sup_user ON sup.user_id = sup_user.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$sessionUserId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $userEmail = $_SESSION['email'] ?? '';
        $student = [
            'student_table_id'          => 0,
            'user_id'                   => $sessionUserId,
            'student_name'              => $_SESSION['user_name'] ?? 'Student',
            'student_email'             => $userEmail,
            'avatar_url'                => null,
            'student_number'            => explode('@', $userEmail)[0] ?? 'N/A',
            'program'                   => 'BSIT',
            'company_name'              => null,
            'supervisor_name'           => null,
            'supervisor_email'          => null
        ];
    }

} catch (Exception $e) {
    error_log("Profile Page Error: " . $e->getMessage());
    $student = [
        'student_name'    => 'Student',
        'student_email'   => $_SESSION['email'] ?? '',
        'student_number'  => 'N/A',
        'program'         => 'BSIT',
        'company_name'    => null,
        'supervisor_name' => null
    ];
}

require_once __DIR__ . '/src/pages/student/profilePage.php';