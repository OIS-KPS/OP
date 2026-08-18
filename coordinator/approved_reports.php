<?php
// coordinator/approved_reports.php
session_start();

require_once __DIR__ . '/../config/db.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Approved WARs";

// 2. Filter Inputs
$selectedWeek    = $_GET['week'] ?? 'all';
$selectedCompany = $_GET['company_id'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');

$filteredWars  = [];
$companiesList = [];

try {
    // 3. Fetch Host Companies for Filter Dropdown
    $stmtCompanies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC");
    $companiesList = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 4. Schema-Accurate Query for Approved Reports
    $sql = "
        SELECT 
            r.id,
            r.student_id,
            r.week_number,
            r.file_path,
            r.ocr_activities,
            r.status,
            r.submitted_at,
            s.student_number,
            s.program,
            u.name AS student_name,
            u.email AS student_email,
            c.id AS company_id,
            c.name AS company_name,
            u_sup.name AS supervisor_name
        FROM reports r
        JOIN students s ON r.student_id = s.id
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        WHERE LOWER(r.status) = 'approved'
    ";

    $params = [];

    // Week Filter
    if ($selectedWeek !== 'all' && is_numeric($selectedWeek)) {
        $sql .= " AND r.week_number = :week ";
        $params['week'] = intval($selectedWeek);
    }

    // Company Filter
    if ($selectedCompany !== 'all' && is_numeric($selectedCompany)) {
        $sql .= " AND c.id = :comp_id ";
        $params['comp_id'] = intval($selectedCompany);
    }

    // Search Filter (Student Name or Student Number)
    if (!empty($searchQuery)) {
        $sql .= " AND (u.name LIKE :search OR s.student_number LIKE :search) ";
        $params['search'] = "%{$searchQuery}%";
    }

    $sql .= " ORDER BY r.week_number DESC, r.submitted_at DESC, u.name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filteredWars = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (PDOException $e) {
    error_log("Database Error in coordinator/approved_reports.php: " . $e->getMessage());
    $filteredWars = [];
}

require_once __DIR__ . '/../src/pages/coordinator/approvedReportsPage.php';