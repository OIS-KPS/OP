<?php
// supervisor/profile.php
session_start();

require_once __DIR__ . '/../config/db.php';

// -------------------------------------------------------------
// 1. Authorization Guard
// -------------------------------------------------------------
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId       = (int)$_SESSION['user_id'];
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError   = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// -------------------------------------------------------------
// 2. Handle Profile Update (job title + contact number only)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $jobTitle      = trim($_POST['job_title'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');

    $error = '';
    if (mb_strlen($jobTitle) > 150) {
        $error = 'Job title must be 150 characters or fewer.';
    } elseif ($contactNumber !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $contactNumber)) {
        $error = 'Contact number must be 7-20 characters and contain only digits, +, -, spaces or parentheses.';
    }

    if ($error !== '') {
        $_SESSION['flash_error'] = $error;
    } else {
        try {
            $stmtUpd = $pdo->prepare("UPDATE supervisors SET job_title = ?, contact_number = ? WHERE user_id = ?");
            $stmtUpd->execute([
                $jobTitle !== '' ? $jobTitle : null,
                $contactNumber !== '' ? $contactNumber : null,
                $userId
            ]);

            logActivity($pdo, $userId, 'supervisor', 'SUPERVISOR_PROFILE_UPDATED', 'Supervisor updated their job title and contact number.');
            $_SESSION['flash_success'] = 'Your profile has been updated.';
        } catch (Exception $e) {
            error_log("Database Error updating supervisor/profile.php: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Could not save your changes. Please try again.';
        }
    }

    header("Location: profile.php");
    exit();
}

// Safe defaults (used if the database lookup fails)
$profile = [
    'supervisor_id'  => 0,
    'name'           => $_SESSION['user_name'] ?? 'Supervisor',
    'email'          => $_SESSION['email'] ?? '',
    'avatar_url'     => $_SESSION['user_picture'] ?? null,
    'status'         => 'active',
    'company_name'   => null,
    'company_address' => null,
    'job_title'      => null,
    'contact_number' => null,
];

try {
    // -------------------------------------------------------------
    // 3. Fetch Supervisor, User & Host Company
    // -------------------------------------------------------------
    $stmt = $pdo->prepare("
        SELECT
            s.id AS supervisor_id,
            s.job_title,
            s.contact_number,
            u.name,
            u.email,
            u.avatar_url,
            u.status,
            c.name AS company_name,
            c.address AS company_address
        FROM supervisors s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        WHERE s.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $profile = array_merge($profile, $row);
        $_SESSION['supervisor_id'] = (int)$row['supervisor_id'];
    }
} catch (Exception $e) {
    error_log("Database Error in supervisor/profile.php: " . $e->getMessage());
}

$supervisor = [
    'name'         => $profile['name'],
    'company_name' => !empty($profile['company_name']) ? $profile['company_name'] : 'Host Company'
];

require_once __DIR__ . '/../src/pages/supervisor/profilePage.php';
