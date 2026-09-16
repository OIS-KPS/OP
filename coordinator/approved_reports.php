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
$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedSection = $_GET['section'] ?? 'all'; // Default to 'all'
$searchQuery     = trim($_GET['search'] ?? '');

$filteredStudents = [];
$companiesList  = [];
$activeSections = [];

try {
    // Distinct Companies
    $stmtCompanies = $pdo->query("SELECT id, name FROM companies WHERE name IS NOT NULL AND name != '' ORDER BY name ASC");
    $companiesList = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Distinct Sections
    $stmtSec = $pdo->query("SELECT DISTINCT COALESCE(NULLIF(section, ''), 'A') AS sec FROM students ORDER BY sec ASC");
    $activeSections = $stmtSec->fetchAll(PDO::FETCH_COLUMN) ?: ['A', 'B', 'C'];

    // 3. Build Query Filters
    $whereClauses = ["u.status = 'active'"];
    $params = [];

    if ($selectedCompany !== 'all' && is_numeric($selectedCompany) && intval($selectedCompany) > 0) {
        $whereClauses[] = "c.id = :comp_id";
        $params['comp_id'] = intval($selectedCompany);
    }

    if ($selectedSection !== 'all' && $selectedSection !== '') {
        $whereClauses[] = "COALESCE(s.section, 'A') = :sec";
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
            s.id AS student_id,
            s.student_number,
            s.program,
            COALESCE(s.section, 'A') AS section,
            u.name AS student_name,
            u.email AS student_email,
            u.avatar_url AS student_avatar,
            COALESCE(c.name, 'Host Company') AS company_name,
            u_sup.name AS supervisor_name,
            (SELECT COUNT(id) FROM reports WHERE student_id = s.id AND status = 'approved') AS approved_reports_count,
            SUM(CASE WHEN re.activity_type != 'Clerical' AND re.activity_type != '' AND re.is_archived = 0 THEN 1 ELSE 0 END) AS tech_count,
            SUM(CASE WHEN re.activity_type = 'Clerical' AND re.is_archived = 0 THEN 1 ELSE 0 END) AS clerical_count
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        LEFT JOIN reports r ON r.student_id = s.id
        LEFT JOIN report_entities re ON re.report_id = r.id
        {$whereSql}
        GROUP BY s.id
        ORDER BY s.section ASC, SUBSTRING_INDEX(u.name, ' ', -1) ASC, u.name ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rawStudents = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Compute Percentages
    foreach ($rawStudents as $st) {
        $tCount = (int)($st['tech_count'] ?? 0);
        $cCount = (int)($st['clerical_count'] ?? 0);
        $sumEntities = $tCount + $cCount;

        $st['it_percentage'] = $sumEntities > 0 ? round(($tCount / $sumEntities) * 100, 1) : 0.0;
        $st['clerical_percentage'] = $sumEntities > 0 ? round(($cCount / $sumEntities) * 100, 1) : 0.0;
        $st['total_entities'] = $sumEntities;

        $filteredStudents[] = $st;
    }

} catch (PDOException $e) {
    error_log("Approved Reports Error: " . $e->getMessage());
    $filteredStudents = [];
}

require_once __DIR__ . '/../src/pages/coordinator/approvedReportsPage.php';