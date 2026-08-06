<?php
// coordinator/evaluations.php
session_start();

require_once __DIR__ . '/../config/db.php';

$pageTitle = "Final Student Evaluations";

// Active Filters
$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedStatus  = $_GET['status'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');
$viewEvalId      = $_GET['view_id'] ?? null;

// Mock Dataset for Coordinator Final Evaluations View
$allEvaluations = [
    [
        'id' => 201,
        'student_id' => 1,
        'student_name' => 'Katelyn Coming',
        'student_number' => '20231053',
        'program' => 'BSIT',
        'company_name' => 'ICS IT Dept',
        'supervisor_name' => 'Engr. John Doe',
        'supervisor_email' => 'johndoe@company.com',
        'status' => 'Completed',
        'final_score' => 96.5,
        'grade_equivalent' => '1.0 (Excellent)',
        'submitted_at' => '2026-07-28 14:15:00',
        'otp_verified' => true,
        'otp_signed_at' => '2026-07-28 14:15:02',
        'otp_ip_address' => '110.54.128.42',
        'scores' => [
            'technical' => 98,
            'work_ethics' => 95,
            'communication' => 96,
            'punctuality' => 97
        ],
        'feedback' => 'Katelyn demonstrated exceptional proficiency in full-stack web development and database management. She completed all assigned lab tasks independently and assisted junior staff.'
    ],
    [
        'id' => 202,
        'student_id' => 2,
        'student_name' => 'Pauline May Coming',
        'student_number' => '20231054',
        'program' => 'BSIT',
        'company_name' => 'LGU Manolo Fortich',
        'supervisor_name' => 'Jane Smith',
        'supervisor_email' => 'janesmith@lgu.gov.ph',
        'status' => 'Completed',
        'final_score' => 88.0,
        'grade_equivalent' => '1.75 (Very Good)',
        'submitted_at' => '2026-07-27 16:40:00',
        'otp_verified' => true,
        'otp_signed_at' => '2026-07-27 16:40:05',
        'otp_ip_address' => '120.28.64.18',
        'scores' => [
            'technical' => 82,
            'work_ethics' => 94,
            'communication' => 90,
            'punctuality' => 86
        ],
        'feedback' => 'Pauline is very diligent, punctual, and reliable with office administrative tasks. We recommend exposing her to more technical IT infrastructure projects in future roles.'
    ],
    [
        'id' => 203,
        'student_id' => 3,
        'student_name' => 'Sander Perejan',
        'student_number' => '20231055',
        'program' => 'BSIT',
        'company_name' => 'ICS IT Dept',
        'supervisor_name' => 'Engr. John Doe',
        'supervisor_email' => 'johndoe@company.com',
        'status' => 'Pending',
        'final_score' => null,
        'grade_equivalent' => 'Pending',
        'submitted_at' => null,
        'otp_verified' => false,
        'otp_signed_at' => null,
        'otp_ip_address' => null,
        'scores' => [],
        'feedback' => null
    ]
];

// Apply Search & Filter Logic
$filteredEvals = array_filter($allEvaluations, function($e) use ($selectedCompany, $selectedStatus, $searchQuery) {
    if ($selectedCompany !== 'all' && $e['company_name'] !== $selectedCompany) return false;
    if ($selectedStatus !== 'all' && $e['status'] !== $selectedStatus) return false;
    if ($searchQuery !== '') {
        $term = strtolower($searchQuery);
        $nameMatch = str_contains(strtolower($e['student_name']), $term);
        $idMatch   = str_contains(strtolower($e['student_number']), $term);
        if (!$nameMatch && !$idMatch) return false;
    }
    return true;
});

// Calculate Cohort Statistics
$completedCount = count(array_filter($allEvaluations, fn($e) => $e['status'] === 'Completed'));
$pendingCount   = count(array_filter($allEvaluations, fn($e) => $e['status'] === 'Pending'));
$totalCount     = count($allEvaluations);

// Active Evaluation for Modal View
$activeEval = null;
if ($viewEvalId) {
    foreach ($allEvaluations as $e) {
        if ($e['id'] == $viewEvalId) {
            $activeEval = $e;
            break;
        }
    }
}

require_once __DIR__ . '/../src/pages/coordinator/evaluationsPage.php';