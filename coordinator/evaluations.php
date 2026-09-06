<?php
// coordinator/evaluations.php
session_start();

require_once __DIR__ . '/../config/db.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Final Evaluations";

// 2. Filter Inputs
$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedStatus  = $_GET['status'] ?? 'all';
$selectedSection = $_GET['section'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');
$viewEvalId      = isset($_GET['view_id']) ? intval($_GET['view_id']) : null;

$filteredEvals  = [];
$companiesList  = [];
$activeSections = [];
$activeEval     = null;
$totalCount     = 0;
$completedCount = 0;
$pendingCount   = 0;

try {
    // Distinct Companies
    $stmtCompanies = $pdo->query("SELECT id, name FROM companies WHERE name IS NOT NULL AND name != '' ORDER BY name ASC");
    $companiesList = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Distinct Sections
    $stmtSec = $pdo->query("SELECT DISTINCT COALESCE(NULLIF(section, ''), 'A') AS sec FROM students ORDER BY sec ASC");
    $activeSections = $stmtSec->fetchAll(PDO::FETCH_COLUMN) ?: ['A', 'B', 'C'];

    // 3. Build Query Filters
    $whereClauses = ["1=1"];
    $params = [];

    if ($selectedCompany !== 'all' && is_numeric($selectedCompany) && intval($selectedCompany) > 0) {
        $whereClauses[] = "c.id = :comp_id";
        $params['comp_id'] = intval($selectedCompany);
    }

    if ($selectedSection !== 'all') {
        $whereClauses[] = "s.section = :sec";
        $params['sec'] = $selectedSection;
    }

    if ($selectedStatus === 'Completed') {
        $whereClauses[] = "e.id IS NOT NULL AND e.otp_verified = 1";
    } elseif ($selectedStatus === 'Pending') {
        $whereClauses[] = "(e.id IS NULL OR e.otp_verified = 0)";
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
            c.id AS company_id,
            COALESCE(c.name, 'Unassigned') AS company_name,
            COALESCE(u_sup.name, 'Pending Assignment') AS supervisor_name,
            e.id AS eval_id,
            e.technical_score,
            e.work_ethics_score,
            e.communication_score,
            e.punctuality_score,
            e.final_score,
            e.grade_equivalent,
            e.feedback,
            e.otp_verified,
            e.otp_signed_at,
            e.otp_ip_address,
            CASE 
                WHEN e.id IS NOT NULL AND e.otp_verified = 1 THEN 'Completed'
                ELSE 'Pending'
            END AS status
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        LEFT JOIN evaluations e ON s.id = e.student_id
        {$whereSql}
        ORDER BY s.section ASC, u.name ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filteredEvals = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 4. Metric Counts
    $stmtTotals = $pdo->query("
        SELECT 
            COUNT(s.id) AS total_interns,
            SUM(CASE WHEN e.id IS NOT NULL AND e.otp_verified = 1 THEN 1 ELSE 0 END) AS completed_evals,
            SUM(CASE WHEN e.id IS NULL OR e.otp_verified = 0 THEN 1 ELSE 0 END) AS pending_evals
        FROM students s
        LEFT JOIN evaluations e ON s.id = e.student_id
    ");
    $stats = $stmtTotals->fetch(PDO::FETCH_ASSOC);

    $totalCount     = intval($stats['total_interns'] ?? 0);
    $completedCount = intval($stats['completed_evals'] ?? 0);
    $pendingCount   = intval($stats['pending_evals'] ?? 0);

    // 5. Modal Record Loader
    if ($viewEvalId) {
        foreach ($filteredEvals as $ev) {
            if (intval($ev['eval_id'] ?? 0) === $viewEvalId || intval($ev['student_id']) === $viewEvalId) {
                $activeEval = $ev;
                break;
            }
        }
    }

} catch (PDOException $e) {
    error_log("Evaluations Error: " . $e->getMessage());
    $filteredEvals = [];
}

require_once __DIR__ . '/../src/pages/coordinator/evaluationsPage.php';