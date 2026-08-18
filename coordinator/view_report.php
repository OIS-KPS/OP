<?php
// coordinator/view_report.php
session_start();

require_once __DIR__ . '/../config/db.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Report Inspection & Accomplishments";

// 2. Validate Report ID parameter
$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($reportId <= 0) {
    header("Location: approved_reports.php");
    exit();
}

$report = null;
$studentHistory = [];

try {
    // 3. Fetch Primary Report Details (Aligned with nbsc_ojt Schema)
    $stmt = $pdo->prepare("
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
            u.avatar_url AS student_avatar,
            c.name AS company_name,
            c.department AS company_dept,
            u_sup.name AS supervisor_name,
            u_sup.email AS supervisor_email
        FROM reports r
        JOIN students s ON r.student_id = s.id
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        WHERE r.id = ?
        LIMIT 1
    ");
    $stmt->execute([$reportId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        $_SESSION['flash_error'] = "The requested weekly report could not be found.";
        header("Location: approved_reports.php");
        exit();
    }

    // 4. Fetch All Weekly Submissions from this Student (For the History Timeline)
    $stmtHist = $pdo->prepare("
        SELECT 
            id, 
            week_number, 
            status, 
            submitted_at, 
            ocr_activities,
            file_path
        FROM reports 
        WHERE student_id = ? 
        ORDER BY week_number ASC
    ");
    $stmtHist->execute([$report['student_id']]);
    $studentHistory = $stmtHist->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (PDOException $e) {
    error_log("Database Error in coordinator/view_report.php: " . $e->getMessage());
    $_SESSION['flash_error'] = "Database query error: " . $e->getMessage();
    header("Location: approved_reports.php");
    exit();
}

require_once __DIR__ . '/../src/pages/coordinator/viewReportPage.php';