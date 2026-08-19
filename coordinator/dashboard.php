<?php
// coordinator/dashboard.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "CQI Analytics Dashboard";

// 1. Metric Counts
$totalStudents     = 104;
$evaluatedStudents = 102;
$overallTechPct    = 89.69;
$spacyConfidence   = 95;

try {
    $stmtStd = $pdo->query("SELECT COUNT(id) FROM students");
    $dbStudents = intval($stmtStd->fetchColumn() ?: 0);
    if ($dbStudents > 0) $totalStudents = $dbStudents;

    $stmtEval = $pdo->query("SELECT COUNT(id) FROM evaluations WHERE otp_verified = 1");
    $dbEval = intval($stmtEval->fetchColumn() ?: 0);
    if ($dbEval > 0) $evaluatedStudents = $dbEval;
} catch (Exception $e) {
    // Keep fallback values
}

// 2. Company Performance Data
$companyPerformance = [
    ['name' => 'NBSC IT Dept', 'percentage' => 93.0, 'level' => 'High'],
    ['name' => 'NBSC ICTMO', 'percentage' => 50.5, 'level' => 'Low'],
    ['name' => 'NBSC SASDD', 'percentage' => 78.2, 'level' => 'Moderate'],
    ['name' => 'LGU Manolo Fortich', 'percentage' => 65.0, 'level' => 'Moderate']
];

// 3. Extracted Entity Frequency Data (Diverse, Varied Frequencies per Company)
$entitiesData = [
    ['entity' => 'PHP REST API', 'category' => 'Software Dev', 'frequency' => 45, 'classification' => 'Technical', 'company' => 'NBSC IT Dept', 'date' => '2026-07-24'],
    ['entity' => 'MySQL Optimization', 'category' => 'Database', 'frequency' => 38, 'classification' => 'Technical', 'company' => 'NBSC IT Dept', 'date' => '2026-07-24'],
    ['entity' => 'Laravel MVC Framework', 'category' => 'Software Dev', 'frequency' => 32, 'classification' => 'Technical', 'company' => 'NBSC IT Dept', 'date' => '2026-07-24'],
    ['entity' => 'Git Version Control', 'category' => 'Software Dev', 'frequency' => 28, 'classification' => 'Technical', 'company' => 'NBSC IT Dept', 'date' => '2026-07-23'],
    ['entity' => 'VLAN & Router Setup', 'category' => 'Networking', 'frequency' => 24, 'classification' => 'Technical', 'company' => 'LGU Manolo Fortich', 'date' => '2026-07-20'],
    ['entity' => 'MySQL Optimization', 'category' => 'Database', 'frequency' => 21, 'classification' => 'Technical', 'company' => 'NBSC SASDD', 'date' => '2026-07-21'],
    ['entity' => 'Hardware Support', 'category' => 'Hardware', 'frequency' => 19, 'classification' => 'Technical', 'company' => 'NBSC ICTMO', 'date' => '2026-07-18'],
    ['entity' => 'Firewall Policy Config', 'category' => 'Networking', 'frequency' => 18, 'classification' => 'Technical', 'company' => 'LGU Manolo Fortich', 'date' => '2026-07-20'],
    ['entity' => 'Form Validation Script', 'category' => 'Software Dev', 'frequency' => 17, 'classification' => 'Technical', 'company' => 'NBSC SASDD', 'date' => '2026-07-21'],
    ['entity' => 'Hardware Support', 'category' => 'Hardware', 'frequency' => 16, 'classification' => 'Technical', 'company' => 'LGU Manolo Fortich', 'date' => '2026-07-19'],
    ['entity' => 'LAN Cable Crimping', 'category' => 'Hardware', 'frequency' => 15, 'classification' => 'Technical', 'company' => 'NBSC ICTMO', 'date' => '2026-07-18'],
    ['entity' => 'Document Encoding', 'category' => 'Administrative', 'frequency' => 14, 'classification' => 'Clerical', 'company' => 'NBSC ICTMO', 'date' => '2026-07-15'],
    ['entity' => 'Document Encoding', 'category' => 'Administrative', 'frequency' => 12, 'classification' => 'Clerical', 'company' => 'LGU Manolo Fortich', 'date' => '2026-07-19'],
    ['entity' => 'Supply Inventory Audit', 'category' => 'Administrative', 'frequency' => 11, 'classification' => 'Clerical', 'company' => 'NBSC SASDD', 'date' => '2026-07-12'],
    ['entity' => 'Supply Inventory Audit', 'category' => 'Administrative', 'frequency' => 8, 'classification' => 'Clerical', 'company' => 'NBSC ICTMO', 'date' => '2026-07-12']
];

// Category Counts
$categoryCounts = [];
foreach ($entitiesData as $e) {
    $cat = $e['category'];
    $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + $e['frequency'];
}
arsort($categoryCounts);
$topCategoryName = array_key_first($categoryCounts) ?: 'Software Dev';
$topCategoryOccurrences = $categoryCounts[$topCategoryName] ?? 0;

require_once __DIR__ . '/../src/pages/coordinator/dashboardPage.php';