<?php
// supervisor/dashboard.php
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

$supervisor = [
    'name' => $_SESSION['user_name'] ?? 'Supervisor',
    'company_name' => 'Host Company'
];

$totalInterns     = 0;
$totalPending     = 0;
$pendingReports   = [];
$recentActivities = [];

try {
    // -------------------------------------------------------------
    // 2. Fetch Supervisor Profile & Company Info
    // -------------------------------------------------------------
    $stmtSup = $pdo->prepare("
        SELECT 
            s.id AS supervisor_id,
            u.name AS supervisor_name,
            u.avatar_url,
            c.name AS company_name
        FROM supervisors s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        WHERE s.user_id = ?
    ");
    $stmtSup->execute([$userId]);
    $supData = $stmtSup->fetch(PDO::FETCH_ASSOC);

    if ($supData) {
        $supervisor_id = $supData['supervisor_id'];
        $_SESSION['supervisor_id'] = $supervisor_id;
        $supervisor['name'] = $supData['supervisor_name'];
        if (!empty($supData['company_name'])) {
            $supervisor['company_name'] = $supData['company_name'];
        }
    } else {
        $supervisor_id = $_SESSION['supervisor_id'] ?? null;
    }

    if ($supervisor_id) {
        // -------------------------------------------------------------
        // 3. Fetch Total Assigned Interns Count
        // -------------------------------------------------------------
        $internsStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE supervisor_id = ?");
        $internsStmt->execute([$supervisor_id]);
        $totalInterns = (int)$internsStmt->fetchColumn();

        // -------------------------------------------------------------
        // 4. Fetch Pending Accomplishment Reports for Assigned Interns
        // -------------------------------------------------------------
        $reportsSql = "
            SELECT 
                r.id AS report_id,
                r.week_number,
                r.file_path,
                r.ocr_activities,
                r.status,
                r.submitted_at,
                u_student.name AS student_name,
                u_student.email AS student_email,
                u_student.avatar_url AS student_avatar,
                s.student_number
            FROM reports r
            JOIN students s ON r.student_id = s.id
            JOIN users u_student ON s.user_id = u_student.id
            WHERE s.supervisor_id = ? AND LOWER(r.status) = 'pending'
            ORDER BY r.submitted_at DESC
        ";
        
        $reportsStmt = $pdo->prepare($reportsSql);
        $reportsStmt->execute([$supervisor_id]);
        $pendingReports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalPending   = count($pendingReports);

        // -------------------------------------------------------------
        // 5. Generate Dynamic Activity Feed from All Intern Submissions
        // -------------------------------------------------------------
        $actStmt = $pdo->prepare("
            SELECT 
                r.week_number,
                r.status,
                r.submitted_at,
                u_student.name AS student_name
            FROM reports r
            JOIN students s ON r.student_id = s.id
            JOIN users u_student ON s.user_id = u_student.id
            WHERE s.supervisor_id = ?
            ORDER BY r.submitted_at DESC
            LIMIT 5
        ");
        $actStmt->execute([$supervisor_id]);
        $activityLogs = $actStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($activityLogs as $log) {
            $status = strtolower($log['status']);
            $timeAgo = date("M d, Y - h:i A", strtotime($log['submitted_at']));
            
            if ($status === 'pending') {
                $title = htmlspecialchars($log['student_name']) . " submitted Week " . $log['week_number'] . " report";
            } elseif ($status === 'approved') {
                $title = "You approved Week " . $log['week_number'] . " report of " . htmlspecialchars($log['student_name']);
            } else {
                $title = "Week " . $log['week_number'] . " report of " . htmlspecialchars($log['student_name']) . " flagged for revision";
            }

            $recentActivities[] = [
                'title' => $title,
                'time_ago' => $timeAgo
            ];
        }
    }

} catch (Exception $e) {
    error_log("Database Error in supervisor/dashboard.php: " . $e->getMessage());
}

// Render View Template
require_once __DIR__ . '/../src/pages/supervisor/dashboardPage.php';