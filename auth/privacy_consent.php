<?php
// privacy_consent.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Ensure user is logged in via Google OAuth, otherwise bounce back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Check if they already consented so they can't bypass via URL tampering
$stmtCheck = $pdo->prepare("SELECT privacy_consent FROM users WHERE id = ? LIMIT 1");
$stmtCheck->execute([$userId]);
$hasConsented = (int)$stmtCheck->fetchColumn();

if ($hasConsented === 1) {
    redirectUserByRole($_SESSION['role'] ?? 'student');
}

// Handle form submission when they click "I ACCEPT"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_consent'])) {
    $stmtUpdate = $pdo->prepare("UPDATE users SET privacy_consent = 1 WHERE id = ?");
    $stmtUpdate->execute([$userId]);
    
    redirectUserByRole($_SESSION['role'] ?? 'student');
}
/*
 * Role-aware "Information collected" copy.
 *
 * This gate runs for all three roles, but the paragraph was written only for an
 * intern. It promised staff a "Student ID" that exists solely on the students
 * table, credited the Weekly Accomplishment Reports to "the intern" when a
 * coordinator administers all of them and a supervisor reviews only their
 * assigned interns', and framed it all as "your" information -- the wrong half
 * of the deal for staff, whose access extends to other people's records. The
 * coordinator administers this portal, so their line says so plainly.
 *
 * Echoed without escaping: hardcoded literals carrying <strong> markup only.
 */
$consentRole = strtolower((string) ($_SESSION['role'] ?? 'student'));

$informationCollected = [
    'student' => 'your <strong>full name, institutional email address, student number, '
        . 'program and section, profile picture, OJT start date, and the '
        . '<strong>Weekly Accomplishment Reports</strong> you submit',

    'supervisor' => 'your <strong>full name, institutional email address, and company '
        . 'affiliation</strong>, together with the <strong>final performance appraisals</strong> '
        . 'you sign. As a reviewer you also have access to the names and '
        . '<strong>Weekly Accomplishment Reports</strong> of the interns assigned to you',

    'coordinator' => 'your <strong>full name and institutional email address</strong>. As '
        . 'portal administrator you also manage all user accounts and partner '
        . 'companies, including permanently deleting them, import student records in '
        . 'bulk, review the portal audit log, and access the academic record, '
        . '<strong>Weekly Accomplishment Report</strong> and internship evaluation of '
        . 'every intern',
];

$informationCollected = $informationCollected[$consentRole] ?? $informationCollected['student'];


function redirectUserByRole($role) {
    switch (strtolower($role)) {
        case 'coordinator':
            header("Location: ../coordinator/dashboard.php");
            break;
        case 'supervisor':
            header("Location: ../supervisor/dashboard.php");
            break;
        case 'student':
        default:
            header("Location: ../dashboard.php");
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Privacy &amp; Confidentiality Consent &mdash; LaunchPad</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-slate-800 antialiased min-h-screen flex items-center justify-center p-6 select-none">

    <div class="max-w-xl w-full bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-[#0F2854] px-8 py-6 text-center text-white space-y-1">
            <p class="text-[11px] font-bold tracking-widest uppercase text-blue-300">NBSC - Institute for Computer Studies</p>
            <h1 class="text-lg font-extrabold tracking-tight">LaunchPad — Data Privacy Notice</h1>
        </div>

        <!-- Content Body -->
        <div class="p-8 space-y-6 text-xs text-slate-700 leading-relaxed font-normal">
            
            <div class="space-y-2">
                <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Data & Confidentiality Consent</h2>
                <p class="text-justify">
                    <strong>Information collected.</strong> Through this portal the system collects <?= $informationCollected; ?>, which is viewed and managed by the <strong>Institute for Computer Studies (ICS)</strong>.
                </p>
            </div>

            <div class="space-y-2">
                <p class="text-justify">
                    <strong>How it is used.</strong> I acknowledge and understand that the NBSC - Institute for Computer Studies (ICS) may use this data solely for academic, administrative, monitoring, and regulatory OJT purposes. The department may also disclose necessary information to authorized partner companies, industry supervisors, or government agencies when required by applicable institutional policies or legal frameworks.
                </p>
            </div>

            <!-- Highlight Consent Box -->
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl text-slate-900 font-medium">
                <p class="text-justify">
                    <strong>Your consent.</strong> By tapping <strong>"I ACCEPT"</strong> below, I voluntarily consent for the NBSC-ICS to collect, use, process, share, and retain my personal and academic information in compliance with the Data Privacy Act of 2012 and other relevant legal frameworks.
                </p>
            </div>

            <p class="text-[11px] text-slate-400 text-center">
                Questions or concerns about your data privacy? Contact the Data Privacy Officer at <span class="font-semibold text-slate-600">ics.internportal@gmail.com</span>
            </p>

            <!-- Action Form -->
            <form action="privacy_consent.php" method="POST" class="pt-2">
                <button 
                    type="submit" 
                    name="accept_consent" 
                    class="w-full py-3.5 bg-[#0F2854] hover:bg-blue-900 text-white font-extrabold rounded-2xl text-xs tracking-wider uppercase transition-all shadow-md cursor-pointer"
                >
                    I Accept
                </button>
            </form>

        </div>
    </div>

</body>
</html>
