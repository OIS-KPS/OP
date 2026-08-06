<?php
// coordinator/view_report.php
session_start();

require_once __DIR__ . '/../config/db.php';

$pageTitle = "Report Inspection & CQI Analysis";

// Get Report ID from URL
$reportId = $_GET['id'] ?? null;

if (!$reportId) {
    header("Location: approved_reports.php");
    exit;
}

// Simulated Database Record for the selected Approved WAR
// In production, fetch this dynamically using $reportId from your SQL database
$report = [
    'id' => $reportId,
    'student_id' => 1,
    'student_name' => 'Katelyn Coming',
    'student_number' => '20231053',
    'program' => 'BSIT',
    'company_name' => 'ICS IT Dept',
    'department' => 'Software & Network Lab',
    'supervisor_name' => 'Engr. John Doe',
    'supervisor_email' => 'johndoe@company.com',
    'week_number' => 2,
    'hours_logged' => 40,
    'approved_at' => '2026-07-24 16:30:00',
    'pdf_file' => 'WAR_Week2_Katelyn.pdf',
    'summary_text' => 'Developed PHP REST API endpoints for student registration, optimized MySQL queries, configured router VLAN settings, and tested database connection pools.',
    'entities' => [
        'technical' => ['PHP REST API', 'MySQL Optimization', 'VLAN Configuration', 'Database Pooling'],
        'clerical'  => []
    ],
    'clerical_ratio' => 0, // 0% clerical, 100% IT
    'supervisor_remarks' => 'Exceptional progress on the backend architecture. All tasks align directly with BSIT networking and web development competencies.',
    'history' => [
        ['week' => 1, 'hours' => 40, 'approved_at' => '2026-07-17 16:00:00', 'clerical_ratio' => 0, 'summary' => 'Orientation, Git repository setup, local web server configuration.'],
        ['week' => 2, 'hours' => 40, 'approved_at' => '2026-07-24 16:30:00', 'clerical_ratio' => 0, 'summary' => 'Developed PHP REST API endpoints and optimized MySQL queries.']
    ]
];

require_once __DIR__ . '/../src/pages/coordinator/viewReportPage.php';