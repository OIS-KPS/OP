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
            sup_user.name AS supervisor_name,
            sup_user.email AS supervisor_email,
            s.requested_company_name,
            s.requested_supervisor_name,
            s.requested_supervisor_email,
            s.placement_request_status,
            s.placement_rejection_reason
        FROM users u
        INNER JOIN students s ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
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
            'supervisor_email'          => null,
            'placement_request_status'  => 'none',
            'requested_company_name'    => null,
            'requested_supervisor_name' => null,
            'requested_supervisor_email'=> null
        ];
    }

    // Handle Placement Request Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_placement_update') {
        $companyName     = trim($_POST['company_name'] ?? '');
        $supervisorName  = trim($_POST['supervisor_name'] ?? '');
        $supervisorEmail = trim($_POST['supervisor_email'] ?? '');

        if ($companyName === '') {
            $_SESSION['flash_error'] = 'Company or Agency name is required.';
            header("Location: profile.php");
            exit();
        }

        $stmtReq = $pdo->prepare("
            UPDATE students 
            SET 
                requested_company_name = ?,
                requested_supervisor_name = ?,
                requested_supervisor_email = ?,
                placement_request_status = 'pending',
                placement_rejection_reason = NULL
            WHERE user_id = ?
        ");
        $stmtReq->execute([
            $companyName,
            $supervisorName !== '' ? $supervisorName : null,
            $supervisorEmail !== '' ? $supervisorEmail : null,
            $sessionUserId
        ]);

        $_SESSION['flash_success'] = 'Your internship placement request has been submitted to the OJT Coordinator.';
        header("Location: profile.php");
        exit();
    }

} catch (Exception $e) {
    error_log("Profile Page Error: " . $e->getMessage());
    $student = [
        'student_name'             => 'Student',
        'student_email'            => $_SESSION['email'] ?? '',
        'student_number'           => 'N/A',
        'program'                  => 'BSIT',
        'company_name'             => null,
        'supervisor_name'          => null,
        'placement_request_status' => 'none'
    ];
}

require_once __DIR__ . '/src/pages/student/profilePage.php';