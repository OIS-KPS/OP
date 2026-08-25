<?php
// coordinator/audit_logs.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$logs = [];

try {
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.role,
            a.action,
            a.description,
            a.ip_address,
            a.created_at,
            u.name AS user_name,
            u.email AS user_email
        FROM audit_logs a
        LEFT JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
        LIMIT 100
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    error_log("Failed to fetch audit logs: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/coordinator/auditLogsPage.php';