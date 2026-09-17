<?php
// coordinator/dashboard.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header('Location: ../auth/login.php');
    exit();
}

$pageTitle = 'CQI Analytics Dashboard';

// Shared data layer: also reused by coordinator/export_cqi.php so the exported
// CQI summary always matches the on-screen dashboard.
require_once __DIR__ . '/../src/services/cqi_dashboard_data.php';

require_once __DIR__ . '/../src/pages/coordinator/dashboardPage.php';
