<?php
// supervisor/dashboard.php
session_start();

require_once __DIR__ . '/../config/db.php';

// TEMPORARY DEV MODE: Default supervisor ID
$supervisor_id = $_SESSION['supervisor_id'] ?? 1;

try {
    // 1. Fetch Supervisor Details
    $stmt = $pdo->prepare("
        SELECT sup.id, sup.name, c.name AS company_name 
        FROM supervisors sup 
        LEFT JOIN companies c ON sup.company_id = c.id 
        WHERE sup.id = ?
    ");
    $stmt->execute([$supervisor_id]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supervisor) {
        $supervisor = [
            'name' => '[Supervisor Full Name]',
            'company_name' => '[Host Company Name]'
        ];
    }

    // 2. Fetch Total Assigned Interns Count
    $internsStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE supervisor_id = ?");
    $internsStmt->execute([$supervisor_id]);
    $totalInterns = $internsStmt->fetchColumn() ?: 0;

    // 3. Fetch Pending Accomplishment Reports
    $reportsSql = "
        SELECT 
            r.id AS report_id,
            r.week_number,
            r.file_path,
            r.ocr_activities,
            r.status,
            r.submitted_at,
            u_student.name AS student_name,
            u_student.avatar_url AS student_avatar,
            s.student_number
        FROM reports r
        JOIN students s ON r.student_id = s.id
        JOIN users u_student ON s.user_id = u_student.id
        WHERE s.supervisor_id = :supervisor_id
        ORDER BY r.submitted_at DESC;
    ";
    
    $reportsStmt = $pdo->prepare($reportsSql);
    $reportsStmt->execute([$supervisor_id]);
    $pendingReports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $totalPending   = count($pendingReports);

    // 4. Placeholder Activity Feed Data
    $recentActivities = [
        [
            'title' => '[Intern Name] submitted Week 3 report',
            'time_ago' => '2 hours ago'
        ],
        [
            'title' => 'You approved Week 2 report of [Intern Name]',
            'time_ago' => '3 hours ago'
        ]
    ];

} catch (Exception $e) {
    // Fallback for early dev before DB tables are seeded
    $supervisor = [
        'name' => '[Supervisor Full Name]',
        'company_name' => '[Host Company Name]'
    ];
    $totalInterns     = 0;
    $totalPending     = 0;
    $pendingReports   = [];
    $recentActivities = [];
}

// Render the View Template
require_once __DIR__ . '/../src/pages/supervisor/dashboardPage.php';