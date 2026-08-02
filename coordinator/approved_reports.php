<?php
// coordinator/approved_reports.php
session_start();

require_once __DIR__ . '/../config/db.php';

$pageTitle = "Approved WARs";

// Active Filters
$selectedWeek    = $_GET['week'] ?? 'all';
$selectedCompany = $_GET['company_id'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');
$viewReportId    = $_GET['view_id'] ?? null;
$historyWeek     = $_GET['history_week'] ?? null;

// Simulated Approved WAR Dataset
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
        'summary_text' => 'Developed PHP REST API endpoints for student registration, optimized MySQL queries, configured router VLAN settings, and tested database connection pools.',
        'entities' => [
            'technical' => ['PHP REST API', 'MySQL Optimization', 'VLAN Configuration', 'Database Pooling'],
            'clerical'  => []
        ],
        'clerical_ratio' => 0,
        'history' => [
            ['week' => 1, 'hours' => 40, 'approved_at' => '2026-07-17 16:00:00', 'clerical_ratio' => 0, 'summary' => 'Orientation, Git repository setup, local web server configuration.']
        ]
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
        'summary_text' => 'Encoded voter registration records into Excel spreadsheets, filed paper documents in cabinet, reformatted office desktop PC, and photocopied incoming department memos.',
        'entities' => [
            'technical' => ['Desktop OS Reformatting'],
            'clerical'  => ['Excel Data Entry', 'Document Filing', 'Photocopying Memos']
        ],
        'clerical_ratio' => 75,
        'history' => [
            ['week' => 1, 'hours' => 40, 'approved_at' => '2026-07-17 14:30:00', 'clerical_ratio' => 100, 'summary' => 'Sorted incoming mail, filed department folders, and conducted physical inventory of office paper supplies.']
        ]
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
        'summary_text' => 'Performed network cable crimping in Room 204, installed Windows 11 on 3 computer lab PCs, repaired faulty RAM, and logged daily IT support tickets.',
        'entities' => [
            'technical' => ['Network Cabling', 'Windows OS Setup', 'Hardware Maintenance'],
            'clerical'  => ['Data Entry Logs']
        ],
        'clerical_ratio' => 25,
        'history' => [
            ['week' => 1, 'hours' => 40, 'approved_at' => '2026-07-17 15:00:00', 'clerical_ratio' => 20, 'summary' => 'Hardware inventory audit and network cable testing in Computer Lab 1.']
        ]
    ]
];

// Filter Logic
$filteredWars = array_filter($allApprovedWars, function($w) use ($selectedWeek, $selectedCompany, $searchQuery) {
    if ($selectedWeek !== 'all' && (int)$w['week_number'] !== (int)$selectedWeek) return false;
    if ($selectedCompany !== 'all' && $w['company_name'] !== $selectedCompany) return false;
    if ($searchQuery !== '') {
        $term = strtolower($searchQuery);
        $nameMatch = str_contains(strtolower($w['student_name']), $term);
        $idMatch   = str_contains(strtolower($w['student_number']), $term);
        if (!$nameMatch && !$idMatch) return false;
    }
    return true;
});

// Active Report Detail Selection
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