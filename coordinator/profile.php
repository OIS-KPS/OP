<?php
// coordinator/profile.php
session_start();

require_once __DIR__ . '/../config/db.php';

// -------------------------------------------------------------
// 1. Authorization Guard
// -------------------------------------------------------------
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Safe defaults (used if the database lookup fails)
$profile = [
    'name'       => $_SESSION['user_name'] ?? 'Coordinator',
    'email'      => $_SESSION['email'] ?? '',
    'avatar_url' => $_SESSION['user_picture'] ?? null,
    'status'     => 'active',
    'created_at' => null,
];

try {
    $stmt = $pdo->prepare("
        SELECT name, email, avatar_url, status, created_at
        FROM users
        WHERE id = ? AND role = 'coordinator'
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $profile = array_merge($profile, $row);
    }
} catch (Exception $e) {
    error_log("Database Error in coordinator/profile.php: " . $e->getMessage());
}

// Last sign-in (from audit logs)
$lastSignIn = null;
try {
    $stmtLast = $pdo->prepare("
        SELECT MAX(created_at)
        FROM audit_logs
        WHERE user_id = ? AND action IN ('USER_LOGIN', 'GOOGLE_LOGIN')
    ");
    $stmtLast->execute([$userId]);
    $lastSignIn = $stmtLast->fetchColumn() ?: null;
} catch (Exception $e) {
    error_log("Database Error (last sign-in) in coordinator/profile.php: " . $e->getMessage());
}


$pageTitle = 'My Profile';

require_once __DIR__ . '/../src/pages/coordinator/profilePage.php';
