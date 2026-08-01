<?php
// supervisor/review_reports.php
session_start();

require_once __DIR__ . '/../config/db.php';

$supervisor_id = $_SESSION['supervisor_id'] ?? 1;
$filter_status = $_GET['status'] ?? 'All';
$active_report_id = isset($_GET['review_id']) ? intval($_GET['review_id']) : null;

$message = '';
$error   = '';

// Initialize sample data session if not present
if (!isset($_SESSION['dev_reports'])) {
    $_SESSION['dev_reports'] = [
        [
            'id' => 101,
            'student_id' => 1,
            'student_name' => 'Katelyn Coming',
            'student_number' => '20231053',
            'program' => 'BSIT',
            'week_number' => 3,
            'total_submitted_count' => 3,
            'it_percent' => 85,
            'clerical_percent' => 15,
            'status' => 'Pending',
            'remarks' => '',
            'attachment_path' => 'uploads/reports/sample.pdf',
            'created_at' => '2026-07-14 21:36:00' // Submitted later
        ],
        [
            'id' => 102,
            'student_id' => 2,
            'student_name' => 'Pauline May Coming',
            'student_number' => '20231054',
            'program' => 'BSIT',
            'week_number' => 3,
            'total_submitted_count' => 3,
            'it_percent' => 70,
            'clerical_percent' => 30,
            'status' => 'Pending',
            'remarks' => '',
            'attachment_path' => 'uploads/reports/sample.pdf',
            'created_at' => '2026-07-14 18:20:00' // Submitted earlier (Should be #1 in pending queue!)
        ],
        [
            'id' => 103,
            'student_id' => 3,
            'student_name' => 'Sander Perejan',
            'student_number' => '20231055',
            'program' => 'BSIT',
            'week_number' => 2,
            'total_submitted_count' => 2,
            'it_percent' => 90,
            'clerical_percent' => 10,
            'status' => 'Approved',
            'remarks' => 'Verified. Great progress on network configuration.',
            'attachment_path' => 'uploads/reports/sample.pdf',
            'created_at' => '2026-07-10 14:15:00'
        ]
    ];
}

// Handle Form POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_report_id'])) {
    $report_id = intval($_POST['action_report_id']);
    $action    = $_POST['status'] ?? '';
    $remarks   = trim($_POST['supervisor_remarks'] ?? '');

    if (in_array($action, ['Approved', 'Needs Revision'])) {
        try {
            $updateSql = "UPDATE reports SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$action, $remarks, $report_id]);
        } catch (Exception $e) {}

        foreach ($_SESSION['dev_reports'] as &$devRep) {
            if ($devRep['id'] == $report_id) {
                $devRep['status']  = $action;
                $devRep['remarks'] = $remarks;
                break;
            }
        }

        $studentName = $_POST['student_name'] ?? 'Student';
        $message = "Report for {$studentName} marked as " . ($action === 'Approved' ? 'Approved' : 'Needs Revision') . "!";
        $active_report_id = null;
    }
}

try {
    $stmt = $pdo->prepare("SELECT sup.id, sup.name, c.name AS company_name FROM supervisors sup LEFT JOIN companies c ON sup.company_id = c.id WHERE sup.id = ?");
    $stmt->execute([$supervisor_id]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['name' => 'Supervisor', 'company_name' => 'NBSC Host Company'];

    // SQL WITH SMART SORTING: Pending first (ASC submission date), then others
    $whereClause = "WHERE s.supervisor_id = ?";
    $params = [$supervisor_id];

    if (in_array($filter_status, ['Pending', 'Approved', 'Needs Revision'])) {
        $whereClause .= " AND r.status = ?";
        $params[] = $filter_status;
    }

    $reportsSql = "
        SELECT 
            r.id,
            r.week_number,
            r.it_percent,
            r.clerical_percent,
            r.status,
            r.remarks,
            r.attachment_path,
            r.created_at,
            s.id AS student_id,
            s.name AS student_name,
            s.student_number,
            s.program,
            (SELECT COUNT(*) FROM reports WHERE student_id = s.id) AS total_submitted_count
        FROM reports r
        JOIN students s ON r.student_id = s.id
        {$whereClause}
        ORDER BY 
            CASE WHEN r.status = 'Pending' THEN 1 ELSE 2 END,
            r.created_at ASC
    ";

    $reportsStmt = $pdo->prepare($reportsSql);
    $reportsStmt->execute($params);
    $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    $supervisor = ['name' => 'Supervisor', 'company_name' => 'NBSC Host Company'];
    $reports = [];
}

// FALLBACK SORTING FOR SAMPLE DEV SESSION DATA
if (empty($reports)) {
    $data = $_SESSION['dev_reports'];

    if ($filter_status !== 'All') {
        $data = array_filter($data, function($item) use ($filter_status) {
            return $item['status'] === $filter_status;
        });
    }

    // Sort: Pending first (Oldest submission date first), then Approved/Revisions
    usort($data, function($a, $b) {
        if ($a['status'] === 'Pending' && $b['status'] !== 'Pending') return -1;
        if ($a['status'] !== 'Pending' && $b['status'] === 'Pending') return 1;
        return strtotime($a['created_at']) <=> strtotime($b['created_at']);
    });

    $reports = array_values($data);
}

// Modal Data Lookup
$activeReport = null;
if ($active_report_id) {
    foreach ($reports as $rep) {
        if ($rep['id'] == $active_report_id) {
            $activeReport = $rep;
            break;
        }
    }
    if (!$activeReport && isset($_SESSION['dev_reports'])) {
        foreach ($_SESSION['dev_reports'] as $rep) {
            if ($rep['id'] == $active_report_id) {
                $activeReport = $rep;
                break;
            }
        }
    }
}

require_once __DIR__ . '/../src/pages/supervisor/reviewReportsPage.php';