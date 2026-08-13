<?php
// supervisor/review_reports.php
session_start();

require_once __DIR__ . '/../config/db.php';

// -------------------------------------------------------------
// 1. Authorization Guard
// -------------------------------------------------------------
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = $_SESSION['review_message'] ?? null;
unset($_SESSION['review_message']);

// Fetch supervisor_id from session or database
$stmtSup = $pdo->prepare("SELECT id FROM supervisors WHERE user_id = ?");
$stmtSup->execute([$userId]);
$supervisorRecord = $stmtSup->fetch(PDO::FETCH_ASSOC);

if (!$supervisorRecord) {
    die("Supervisor profile not found. Please contact administrator.");
}

$supervisorId = $supervisorRecord['id'];
$_SESSION['supervisor_id'] = $supervisorId;

// -------------------------------------------------------------
// 2. Handle Supervisor Approval or Revision Form Post
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_report_id'])) {
    $reportId = intval($_POST['action_report_id']);
    $newStatus = strtolower(trim($_POST['status'] ?? 'pending'));
    $remarks = trim($_POST['supervisor_remarks'] ?? '');
    $studentName = $_POST['student_name'] ?? 'Student';

    try {
        // Update report status in the database
        $updateStmt = $pdo->prepare("
            UPDATE reports r
            JOIN students s ON r.student_id = s.id
            SET r.status = ?, r.ocr_activities = CASE WHEN ? != '' THEN ? ELSE r.ocr_activities END
            WHERE r.id = ? AND s.supervisor_id = ?
        ");
        $updateStmt->execute([$newStatus, $remarks, $remarks, $reportId, $supervisorId]);

        $_SESSION['review_message'] = "Report for {$studentName} updated to " . ucfirst($newStatus) . " successfully!";
        header("Location: review_reports.php" . (isset($_GET['status']) ? '?status=' . $_GET['status'] : ''));
        exit();
    } catch (Exception $e) {
        error_log("Error updating report status: " . $e->getMessage());
    }
}

// -------------------------------------------------------------
// 3. Status Filter Processing
// -------------------------------------------------------------
$filter_status = $_GET['status'] ?? 'All';
$validStatuses = ['All', 'Pending', 'Approved', 'Needs Revision'];
if (!in_array($filter_status, $validStatuses)) {
    $filter_status = 'All';
}

// Build dynamic WHERE clause based on selected filter tab
$whereSQL = " WHERE s.supervisor_id = :supervisor_id ";
if ($filter_status !== 'All') {
    if ($filter_status === 'Needs Revision') {
        $whereSQL .= " AND LOWER(r.status) IN ('rejected', 'needs revision') ";
    } else {
        $whereSQL .= " AND LOWER(r.status) = :status ";
    }
}

// Query queued reports
$reportsSql = "
    SELECT 
        r.id,
        r.student_id,
        r.week_number,
        r.file_path,
        r.file_path AS attachment_path,
        r.ocr_activities,
        r.ocr_activities AS remarks,
        r.status,
        r.submitted_at,
        r.submitted_at AS created_at,
        u.name AS student_name,
        u.avatar_url AS student_avatar,
        s.student_number,
        s.program
    FROM reports r
    JOIN students s ON r.student_id = s.id
    JOIN users u ON s.user_id = u.id
    {$whereSQL}
    ORDER BY CASE WHEN LOWER(r.status) = 'pending' THEN 0 ELSE 1 END, r.submitted_at DESC
";

$stmtReports = $pdo->prepare($reportsSql);
$params = ['supervisor_id' => $supervisorId];
if ($filter_status !== 'All' && $filter_status !== 'Needs Revision') {
    $params['status'] = strtolower($filter_status);
}
$stmtReports->execute($params);
$reports = $stmtReports->fetchAll(PDO::FETCH_ASSOC) ?: [];

// -------------------------------------------------------------
// 4. Active Report Evaluation Modal Handler
// -------------------------------------------------------------
$review_id = isset($_GET['review_id']) ? intval($_GET['review_id']) : null;
$activeReport = null;

if ($review_id) {
    foreach ($reports as $rep) {
        if (intval($rep['id']) === $review_id) {
            $activeReport = $rep;
            break;
        }
    }
    
    // If not found in filtered reports, fetch directly
    if (!$activeReport) {
        $singleStmt = $pdo->prepare("
            SELECT 
                r.id, r.student_id, r.week_number, r.file_path, r.file_path AS attachment_path,
                r.ocr_activities, r.ocr_activities AS remarks, r.status, r.submitted_at, r.submitted_at AS created_at,
                u.name AS student_name, u.avatar_url AS student_avatar, s.student_number, s.program
            FROM reports r
            JOIN students s ON r.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE r.id = ? AND s.supervisor_id = ?
        ");
        $singleStmt->execute([$review_id, $supervisorId]);
        $activeReport = $singleStmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Render View Template
require_once __DIR__ . '/../src/pages/supervisor/reviewReportsPage.php';