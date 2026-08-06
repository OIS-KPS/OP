<?php
// coordinator/dashboard.php
session_start();

require_once __DIR__ . '/../config/db.php';

$pageTitle = "CQI Analytics Dashboard";

// Data aggregation for CQI Analytics (Mock dataset for UI dev)
$totalReports       = 42;
$totalEntities      = 156;
$overallTechPct     = 72; // 72% Technical
$overallClericalPct = 28; // 28% Clerical
$flaggedOfficesCount = 1; // 1 Office exceeded 50% clerical threshold

// Frequency counts of top extracted IT skills
$topITSkills = [
    'Web Development (PHP/HTML)' => 45,
    'Database & SQL Queries'     => 38,
    'PC Hardware Support'        => 22,
    'Network Cabling & VLAN'     => 18,
    'OS & Software Setup'        => 12
];

// Department / Company Task Ratio Comparison
$companyRatios = [
    [
        'company' => 'ICS IT Dept',
        'department' => 'Software & Network Lab',
        'interns' => 2,
        'tech_pct' => 88,
        'clerical_pct' => 12,
        'status' => 'Optimal'
    ],
    [
        'company' => 'LGU Manolo Fortich',
        'department' => 'Management Information Systems (MIS)',
        'interns' => 1,
        'tech_pct' => 25,
        'clerical_pct' => 75,
        'status' => 'Flagged' // Exceeds 50% clerical threshold
    ]
];

require_once __DIR__ . '/../src/pages/coordinator/dashboardPage.php';