<?php
// coordinator/approved_reports.php
session_start();

require_once __DIR__ . '/../config/db.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Accomplishment Reports";

// 2. Filter Inputs
$selectedWeek    = $_GET['week'] ?? 'all';
$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedSection = $_GET['section'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');

$filteredWars   = [];
$companiesList  = [];
$activeSections = [];

try {
    // Distinct Companies
    $stmtCompanies = $pdo->query("SELECT id, name FROM companies WHERE name IS NOT NULL AND name != '' ORDER BY name ASC");
    $companiesList = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Distinct Sections
    $stmtSec = $pdo->query("SELECT DISTINCT COALESCE(NULLIF(section, ''), 'A') AS sec FROM students ORDER BY sec ASC");
    $activeSections = $stmtSec->fetchAll(PDO::FETCH_COLUMN) ?: ['A', 'B', 'C'];

    // 3. Build Query Filters (Reports with status = 'approved')
    $whereClauses = ["r.status = 'approved'"];
    $params = [];

    if ($selectedWeek !== 'all' && is_numeric($selectedWeek)) {
        $whereClauses[] = "r.week_number = :week";
        $params['week'] = intval($selectedWeek);
    }

    if ($selectedCompany !== 'all' && is_numeric($selectedCompany) && intval($selectedCompany) > 0) {
        $whereClauses[] = "c.id = :comp_id";
        $params['comp_id'] = intval($selectedCompany);
    }

    if ($selectedSection !== 'all') {
        $whereClauses[] = "s.section = :sec";
        $params['sec'] = $selectedSection;
    }

    if ($searchQuery !== '') {
        $whereClauses[] = "(LOWER(u.name) LIKE :search_name OR LOWER(s.student_number) LIKE :search_num)";
        $searchParam = '%' . strtolower($searchQuery) . '%';
        $params['search_name'] = $searchParam;
        $params['search_num']  = $searchParam;
    }

    $whereSql = "WHERE " . implode(' AND ', $whereClauses);

    $sql = "
        SELECT 
            r.id,
            r.student_id,
            r.week_number,
            r.file_path,
            r.status,
            r.submitted_at,
            r.approved_at,
            r.updated_at,
            s.student_number,
            s.program,
            COALESCE(s.section, 'A') AS section,
            u.name AS student_name,
            u.email AS student_email,
            u.avatar_url AS student_avatar,
            COALESCE(c.name, 'Host Company') AS company_name,
            u_sup.name AS supervisor_name
        FROM reports r
        JOIN students s ON r.student_id = s.id
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        {$whereSql}
        ORDER BY r.week_number DESC, r.submitted_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filteredWars = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (PDOException $e) {
    error_log("Approved Reports Error: " . $e->getMessage());
    $filteredWars = [];
}

require_once __DIR__ . '/../src/pages/coordinator/approvedReportsPage.php';