<?php
// coordinator/evaluations.php
session_start();

require_once __DIR__ . '/../config/db.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Final Student Evaluations";

// 2. Active Filter Inputs
$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedStatus  = $_GET['status'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');
$viewEvalId      = isset($_GET['view_id']) ? intval($_GET['view_id']) : null;

$filteredEvals  = [];
$companiesList  = [];
$activeEval     = null;
$totalCount     = 0;
$completedCount = 0;
$pendingCount   = 0;

try {
    // 3. Fetch Host Companies for the Filter Dropdown
    $stmtCompanies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC");
    $companiesList = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 4. Fetch All BSIT Interns with Evaluation Status (LEFT JOIN evaluations)
    $sql = "
        SELECT 
            s.id AS student_id,
            s.student_number,
            s.program,
            u.name AS student_name,
            u.email AS student_email,
            c.id AS company_id,
            c.name AS company_name,
            u_sup.name AS supervisor_name,
            u_sup.email AS supervisor_email,
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
        WHERE 1=1
    ";

    $params = [];

    // Filter by Company
    if ($selectedCompany !== 'all' && is_numeric($selectedCompany)) {
        $sql .= " AND c.id = :comp_id ";
        $params['comp_id'] = intval($selectedCompany);
    }

    // Filter by Status
    if ($selectedStatus === 'Completed') {
        $sql .= " AND e.id IS NOT NULL AND e.otp_verified = 1 ";
    } elseif ($selectedStatus === 'Pending') {
        $sql .= " AND (e.id IS NULL OR e.otp_verified = 0) ";
    }

    // Search Query (Student Name or Student Number)
    if (!empty($searchQuery)) {
        $sql .= " AND (u.name LIKE :search OR s.student_number LIKE :search) ";
        $params['search'] = "%{$searchQuery}%";
    }

    $sql .= " ORDER BY u.name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filteredEvals = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 5. Compute Cohort Summary Metrics
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

    // 6. Fetch Active Evaluation for Inspection Modal
    if ($viewEvalId) {
        foreach ($filteredEvals as $ev) {
            if (intval($ev['eval_id'] ?? 0) === $viewEvalId || intval($ev['student_id']) === $viewEvalId) {
                $activeEval = $ev;
                break;
            }
        }
    }

} catch (PDOException $e) {
    error_log("Database Error in coordinator/evaluations.php: " . $e->getMessage());
    $filteredEvals = [];
}

require_once __DIR__ . '/../src/pages/coordinator/evaluationsPage.php';