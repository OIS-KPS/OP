<?php
// coordinator/approved_reports.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Active Filters
$selectedWeek    = $_GET['week'] ?? 'all';
$selectedCompany = $_GET['company_id'] ?? 'all';
$viewReportId    = $_GET['view_id'] ?? null;

// Simulated DB Dataset of Approved WARs for 150+ Scale Development
$allApprovedWars = [
    [
        'id' => 101,
        'student_id' => 1,
        'student_name' => 'Katelyn Coming',
        'student_number' => '20231053',
        'company_name' => 'ICS IT Dept',
        'supervisor_name' => 'Engr. John Doe',
        'week_number' => 2,
        'hours_logged' => 40,
        'approved_at' => '2026-07-24 16:30:00',
        'pdf_file' => 'WAR_Week2_Katelyn.pdf',
        'summary_text' => 'Configured MySQL database connections in PHP, designed REST API endpoints, and set up local server virtual hosts.',
        'entities' => [
            'technical' => ['PHP Development', 'MySQL Queries', 'REST API', 'Virtual Hosts'],
            'clerical'  => []
        ],
        'clerical_ratio' => 0
    ],
    [
        'id' => 102,
        'student_id' => 2,
        'student_name' => 'Pauline May Coming',
        'student_number' => '20231054',
        'company_name' => 'LGU Manolo Fortich',
        'supervisor_name' => 'Jane Smith',
        'week_number' => 2,
        'hours_logged' => 40,
        'approved_at' => '2026-07-24 15:10:00',
        'pdf_file' => 'WAR_Week2_Pauline.pdf',
        'summary_text' => 'Encoded voter registration records in Excel, photocopied incoming department memos, and filed paper folders in cabinet.',
        'entities' => [
            'technical' => [],
            'clerical'  => ['Excel Data Entry', 'Photocopying Memos', 'Document Filing']
        ],
        'clerical_ratio' => 100
    ],
    [
        'id' => 103,
        'student_id' => 3,
        'student_name' => 'Sander Perejan',
        'student_number' => '20231055',
        'company_name' => 'ICS IT Dept',
        'supervisor_name' => 'Engr. John Doe',
        'week_number' => 2,
        'hours_logged' => 40,
        'approved_at' => '2026-07-24 17:00:00',
        'pdf_file' => 'WAR_Week2_Sander.pdf',
        'summary_text' => 'Crimped Ethernet cables for Room 204 network setup, installed Windows 11 on 3 PCs, and logged daily IT maintenance logs.',
        'entities' => [
            'technical' => ['Network Cabling', 'Windows OS Installation', 'PC Troubleshooting'],
            'clerical'  => ['Data Entry']
        ],
        'clerical_ratio' => 25
    ]
];

// Filter Logic
$filteredWars = array_filter($allApprovedWars, function($w) use ($selectedWeek, $selectedCompany) {
    if ($selectedWeek !== 'all' && (int)$w['week_number'] !== (int)$selectedWeek) return false;
    if ($selectedCompany !== 'all' && $w['company_name'] !== $selectedCompany) return false;
    return true;
});

// If viewing a specific report details modal
$activeReport = null;
if ($viewReportId) {
    foreach ($allApprovedWars as $w) {
        if ($w['id'] == $viewReportId) {
            $activeReport = $w;
            break;
        }
    }
}

require_once __DIR__ . '/../src/pages/coordinator/approvedReportsPage.php';