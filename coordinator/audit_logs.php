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
$totalLogs   = 0;
$totalPages  = 1;
$page        = 1;
$perPage     = 25;
$roleFilter  = 'all';
$searchQuery = '';

try {
    // --- Filters (moved server-side so they work across paginated results) ---

    $allowedRoles = ['all', 'student', 'supervisor', 'coordinator'];

    $roleFilter = (string)($_GET['role'] ?? 'all');

    if (!in_array($roleFilter, $allowedRoles, true)) {
        $roleFilter = 'all';
    }

    $searchQuery = trim((string)($_GET['search'] ?? ''));

    $where  = [];
    $params = [];

    if ($roleFilter !== 'all') {
        $where[]        = 'a.role = :role';
        $params['role'] = $roleFilter;
    }

    if ($searchQuery !== '') {
        $where[] = "(u.name LIKE :q_name OR u.email LIKE :q_email OR a.action LIKE :q_action OR a.description LIKE :q_desc)";
        $like    = '%' . $searchQuery . '%';
        $params['q_name']   = $like;
        $params['q_email']  = $like;
        $params['q_action'] = $like;
        $params['q_desc']   = $like;
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    // --- Total matching rows (drives the pager) ---

    $countStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM audit_logs a
        LEFT JOIN users u ON a.user_id = u.id
        $whereSql
    ");
    $countStmt->execute($params);
    $totalLogs = (int)$countStmt->fetchColumn();

    $totalPages = max(1, (int)ceil($totalLogs / $perPage));

    // --- Requested page, clamped into range ---

    $page = max(1, (int)($_GET['page'] ?? 1));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;

    $sqlLogs = "
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
        $whereSql
        ORDER BY a.created_at DESC
        LIMIT :lim OFFSET :off
    ";

    $stmtLogs = $pdo->prepare($sqlLogs);

    // EMULATE_PREPARES is off, so LIMIT/OFFSET must be bound as integers.
    $stmtLogs->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmtLogs->bindValue(':off', $offset, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $stmtLogs->bindValue(':' . $key, $value);
    }

    $stmtLogs->execute();
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    error_log("Failed to fetch audit logs: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/coordinator/auditLogsPage.php';