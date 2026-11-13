<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_PATH . '/dashboard.php');

if (isset($_GET['all']) && $_GET['all'] === '1') {
    markAllNotificationsRead($pdo, $userId);
} elseif (isset($_GET['id'])) {
    markNotificationRead($pdo, (int) $_GET['id'], $userId);
} elseif (isset($_GET['notif_id'])) {
    markNotificationRead($pdo, (int) $_GET['notif_id'], $userId);
}

header('Location: ' . $redirect);
exit();