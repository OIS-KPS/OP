<?php
// supervisor/dashboard.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

$supervisor = [
    'name' => $_SESSION['user_name'] ?? 'Supervisor',
    'company_name' => 'Host Company'
];

$totalInterns = 0;
$totalPending = 0;
$totalNeedsChanges = 0;
$totalEvaluated = 0;
$pendingReports = [];
$recentActivities = [];

try {
    // 1. Fetch Supervisor Profile & Company Info
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
        LIMIT 1
    ");
    $stmtSup->execute([$userId]);
    $supData = $stmtSup->fetch(PDO::FETCH_ASSOC);

    if ($supData) {
        $supervisor_id = (int)$supData['supervisor_id'];
        $_SESSION['supervisor_id'] = $supervisor_id;
        $supervisor['name'] = $supData['supervisor_name'];
        if (!empty($supData['company_name'])) {
            $supervisor['company_name'] = $supData['company_name'];
        }
    } else {
        $supervisor_id = (int)($_SESSION['supervisor_id'] ?? 0);
    }

    if ($supervisor_id > 0) {
        // 2. Fetch Metric Counts
        $internsStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE supervisor_id = ?");
        $internsStmt->execute([$supervisor_id]);
        $totalInterns = (int)$internsStmt->fetchColumn();

        $pendingStmt = $pdo->prepare("
            SELECT COUNT(r.id) 
            FROM reports r
            JOIN students s ON r.student_id = s.id
            WHERE s.supervisor_id = ? AND LOWER(r.status) = 'pending'
        ");
        $pendingStmt->execute([$supervisor_id]);
        $totalPending = (int)$pendingStmt->fetchColumn();

        $needsChangesStmt = $pdo->prepare("
            SELECT COUNT(r.id) 
            FROM reports r
            JOIN students s ON r.student_id = s.id
            WHERE s.supervisor_id = ? AND LOWER(r.status) = 'rejected'
        ");
        $needsChangesStmt->execute([$supervisor_id]);
        $totalNeedsChanges = (int)$needsChangesStmt->fetchColumn();

        $evalStmt = $pdo->prepare("SELECT COUNT(*) FROM evaluations WHERE supervisor_id = ?");
        $evalStmt->execute([$supervisor_id]);
        $totalEvaluated = (int)$evalStmt->fetchColumn();

        // 3. Fetch Pending & Flagged Reports Queue (Ordered chronologically)
        $reportsStmt = $pdo->prepare("
            SELECT 
                r.id AS report_id,
                r.week_number,
                r.file_path,
                r.previous_file_path,
                r.supervisor_remarks,
                r.status,
                r.submitted_at,
                u_student.name AS student_name,
                u_student.avatar_url AS student_avatar,
                s.student_number
            FROM reports r
            JOIN students s ON r.student_id = s.id
            JOIN users u_student ON s.user_id = u_student.id
            WHERE s.supervisor_id = ? AND LOWER(r.status) IN ('pending', 'rejected')
            ORDER BY COALESCE(r.updated_at, r.submitted_at) DESC
            LIMIT 6
        ");
        $reportsStmt->execute([$supervisor_id]);
        $pendingReports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 4. Activity Feed (Chronological events across all supervised students)
        $actStmt = $pdo->prepare("
            SELECT 
                r.id,
                r.week_number,
                r.status,
                r.submitted_at,
                r.approved_at,
                r.updated_at,
                r.previous_file_path,
                u_student.name AS student_name
            FROM reports r
            JOIN students s ON r.student_id = s.id
            JOIN users u_student ON s.user_id = u_student.id
            WHERE s.supervisor_id = ?
            ORDER BY COALESCE(r.approved_at, r.updated_at, r.submitted_at) DESC
            LIMIT 6
        ");
        $actStmt->execute([$supervisor_id]);
        $activityLogs = $actStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($activityLogs as $log) {
            $status = strtolower($log['status'] ?? 'pending');
            $hasRevision = !empty($log['previous_file_path']);
            $timestamp = !empty($log['approved_at']) ? $log['approved_at'] : (!empty($log['updated_at']) ? $log['updated_at'] : $log['submitted_at']);
            $timeAgo = !empty($timestamp) ? date("M d, Y &bull; g:i A", strtotime($timestamp)) : 'Recently';

            if ($status === 'approved') {
                $title = "Approved Week " . $log['week_number'] . " report for " . htmlspecialchars($log['student_name']);
                $type = 'approved';
            } elseif ($status === 'rejected') {
                $title = "Requested changes on Week " . $log['week_number'] . " report for " . htmlspecialchars($log['student_name']);
                $type = 'rejected';
            } else {
                $title = htmlspecialchars($log['student_name']) . " submitted " . ($hasRevision ? "revised " : "") . "Week " . $log['week_number'] . " report";
                $type = 'pending';
            }

            $recentActivities[] = [
                'title'    => $title,
                'time_ago' => $timeAgo,
                'type'     => $type
            ];
        }
    }

} catch (Exception $e) {
    error_log("Database Error in supervisor/dashboard.php: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/supervisor/dashboardPage.php';