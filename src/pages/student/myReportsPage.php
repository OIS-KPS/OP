<!-- src/pages/student/myReportsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');
$flashMessage = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

$isEvaluated = !empty($student['evaluation_id']);
$isRequested = !empty($student['completion_requested']) && !$isEvaluated;
$nextWeekToSubmit = count($reports ?? []) + 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - Student Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    
    <style>
        /* Scoped Tab Styles to Prevent Global Hover Glitches */
        .filter-tab {
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease !important;
            transform: none !important;
        }
        .filter-tab:not(.active) {
            background-color: #ffffff !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
        }
        .filter-tab:not(.active):hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }
        .filter-tab.active {
            background-color: #0F2854 !important;
            color: #ffffff !important;
            border: 1px solid #0F2854 !important;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1">

                <!-- Alert Messages -->
                <?php if (!empty($flashMessage)): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black text-sm">✓</span>
                            <p class="font-bold text-xs"><?= htmlspecialchars($flashMessage); ?></p>
                        </div>
                        <a href="reports.php" class="text-xs font-bold text-emerald-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['submitted']) && $_GET['submitted'] === 'success'): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black text-sm">✓</span>
                            <div>
                                <p class="font-extrabold text-xs">Report Submitted</p>
                                <p class="text-[11px] text-emerald-800 font-medium">Your weekly accomplishment report has been uploaded and is waiting for review.</p>
                            </div>
                        </div>
                        <a href="reports.php" class="text-xs font-bold text-emerald-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <!-- 1. Conditional 486-Hour / Final Evaluation Notification Banner -->
                <?php if ($totalApproved >= 12 || $isRequested || $isEvaluated): ?>
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-slate-900 uppercase tracking-wider">486-Hour Internship Completion</span>
                                <?php if ($isEvaluated): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100/80 text-emerald-900 border border-emerald-300">Evaluation Forwarded</span>
                                <?php elseif ($isRequested): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100/80 text-amber-900 border border-amber-300">Evaluation Requested</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100/80 text-emerald-900 border border-emerald-300">Eligible for Final Evaluation</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs font-medium text-slate-600">
                                <?php if ($isEvaluated): ?>
                                    Your supervisor has completed and forwarded your final evaluation appraisal to the coordinator.
                                <?php elseif ($isRequested): ?>
                                    Your completion request has been submitted. Your supervisor will review and sign your appraisal.
                                <?php else: ?>
                                    You have completed your 12 weekly reports. Request your final performance evaluation from your supervisor.
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="shrink-0">
                            <?php if ($isEvaluated): ?>
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Completed &amp; Signed
                                </span>
                            <?php elseif ($isRequested): ?>
                                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Waiting for Supervisor
                                </span>
                            <?php else: ?>
                                <form method="POST" action="reports.php" onsubmit="return confirm('Confirm that you have completed rendering all required 486 hours of your internship. Your supervisor will be notified to evaluate you.');">
                                    <button type="submit" name="request_evaluation" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors cursor-pointer inline-flex items-center gap-2">
                                        <span>Request Final Evaluation</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 2. Status Summary Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-1">
                        <span class="block text-[10px] font-black text-slate-700 uppercase tracking-wider">Reports Submitted</span>
                        <p class="text-3xl font-black text-slate-950"><?= (int)$totalReportsCount; ?></p>
                        <p class="text-xs font-semibold text-slate-600 pt-0.5">Total accomplishment logs</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-1">
                        <span class="block text-[10px] font-black text-slate-700 uppercase tracking-wider">Reports Approved</span>
                        <p class="text-3xl font-black text-emerald-600"><?= (int)$totalApproved; ?></p>
                        <p class="text-xs font-semibold text-slate-600 pt-0.5">Verified by supervisor</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border <?= $totalRejected > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200/90'; ?> shadow-xs space-y-1">
                        <span class="block text-[10px] font-black text-slate-700 uppercase tracking-wider"><?= $totalRejected > 0 ? 'Needs Action' : 'Waiting for Review'; ?></span>
                        <p class="text-3xl font-black <?= $totalRejected > 0 ? 'text-rose-600' : 'text-amber-600'; ?>"><?= (int)($totalRejected > 0 ? $totalRejected : $totalPending); ?></p>
                        <p class="text-xs font-semibold text-slate-600 pt-0.5"><?= $totalRejected > 0 ? $totalRejected . ' report(s) flagged for revision' : 'Pending supervisor sign-off'; ?></p>
                    </div>
                </div>

                <!-- 3. Submission History Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
                    <!-- Table Top Action Bar -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 tracking-wider uppercase">Submission History</h3>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Chronological record of weekly accomplishment logs</p>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <?php if (!$isEvaluated && $totalApproved < 12): ?>
                                <a href="submit_report.php?week=<?= $nextWeekToSubmit; ?>" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    <span>Submit Week <?= $nextWeekToSubmit; ?></span>
                                </a>
                            <?php endif; ?>
                            
                            <span class="text-xs font-bold text-slate-800 bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
                                Sorted by Week
                            </span>
                        </div>
                    </div>

                    <!-- Clean Filter Tabs with Solid Color Rules -->
                    <div class="px-6 py-3 border-b border-slate-200/60 bg-white flex flex-wrap items-center gap-2">
                        <button type="button" onclick="filterReports('all', this)" class="filter-tab active px-3.5 py-1.5 rounded-lg text-xs font-bold cursor-pointer select-none">
                            All Logs (<?= (int)$totalReportsCount; ?>)
                        </button>
                        <button type="button" onclick="filterReports('pending', this)" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold cursor-pointer select-none">
                            Waiting for Review (<?= (int)$totalPending; ?>)
                        </button>
                        <button type="button" onclick="filterReports('approved', this)" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold cursor-pointer select-none">
                            Approved (<?= (int)$totalApproved; ?>)
                        </button>
                        <button type="button" onclick="filterReports('rejected', this)" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold cursor-pointer select-none">
                            Needs Changes (<?= (int)$totalRejected; ?>)
                        </button>
                    </div>

                    <?php if (!empty($reports)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Submitted Date &amp; Time</th>
                                        <th class="py-4 px-6">Review Status &amp; Remarks</th>
                                        <th class="py-4 px-6 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reportsTableBody" class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($reports as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                        $filePath = $report['file_path'] ?? '';
                                        $prevFilePath = $report['previous_file_path'] ?? '';
                                        $remarks = $report['supervisor_remarks'] ?? '';
                                        $dateSubmitted = $report['submitted_at'] ?? null;
                                        $approvedAt = $report['approved_at'] ?? null;
                                        $isApproved = ($status === 'approved');
                                        $isRejected = ($status === 'rejected');
                                    ?>
                                        <tr class="report-row hover:bg-slate-50 transition-colors <?= $isRejected ? 'bg-rose-50/20' : 'bg-white'; ?>" 
                                            data-status="<?= htmlspecialchars($status); ?>">
                                            
                                            <!-- Week Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] font-black text-xs flex items-center justify-center border border-slate-300 shrink-0">
                                                        W<?= htmlspecialchars($report['week_number']); ?>
                                                    </div>
                                                    <div>
                                                        <span class="font-extrabold text-slate-950 text-sm block">Week <?= htmlspecialchars($report['week_number']); ?></span>
                                                        <span class="block text-[10px] font-semibold text-slate-500">Accomplishment Log</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Submitted Date -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <span class="font-semibold text-xs text-slate-700">
                                                    <?= !empty($dateSubmitted) ? date("M d, Y \a\\t g:i A", strtotime($dateSubmitted)) : '—'; ?>
                                                </span>
                                            </td>

                                            <!-- Review Status & Remarks -->
                                            <td class="py-4 px-6 align-middle">
                                                <?php if ($isApproved): ?>
                                                    <div class="flex flex-col items-start gap-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                                Approved
                                                            </span>
                                                            <?php if (!empty($prevFilePath)): ?>
                                                                <button type="button" 
                                                                        onclick="openStudentRemarksModal('Week <?= htmlspecialchars($report['week_number']); ?>', '<?= htmlspecialchars(addslashes($remarks)); ?>', '<?= htmlspecialchars('/ICS-PORTAL/' . ltrim(str_replace('\\', '/', $prevFilePath), '/')); ?>')" 
                                                                        class="text-[10px] font-bold text-slate-600 hover:text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 transition-colors cursor-pointer">
                                                                    View Feedback Log
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if (!empty($approvedAt)): ?>
                                                            <span class="text-[11px] font-semibold text-slate-500 pl-0.5">
                                                                Approved: <?= date("M d, Y \a\\t g:i A", strtotime($approvedAt)); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif ($isRejected): ?>
                                                    <div class="space-y-1.5">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100/80 text-rose-900 border border-rose-300 text-xs font-bold shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                            Needs Changes
                                                        </span>

                                                        <?php if (!empty($remarks)): ?>
                                                            <div class="p-2.5 bg-rose-50 border border-rose-200 rounded-xl max-w-sm space-y-1">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-950 flex items-center gap-1">
                                                                        Supervisor Note
                                                                    </span>
                                                                    <button type="button" 
                                                                            onclick="openStudentRemarksModal('Week <?= htmlspecialchars($report['week_number']); ?>', '<?= htmlspecialchars(addslashes($remarks)); ?>', '<?= htmlspecialchars('/ICS-PORTAL/' . ltrim(str_replace('\\', '/', $filePath), '/')); ?>')" 
                                                                            class="text-[10px] font-bold text-rose-700 hover:underline cursor-pointer">
                                                                        View Note &rarr;
                                                                    </button>
                                                                </div>
                                                                <p class="text-xs text-rose-900 font-medium leading-relaxed line-clamp-1">
                                                                    <?= htmlspecialchars($remarks); ?>
                                                                </p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="space-y-1">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                            Waiting for Review
                                                        </span>
                                                        <?php if (!empty($prevFilePath)): ?>
                                                            <span class="block text-[10px] font-bold text-amber-800 pl-0.5">
                                                                Revised version submitted
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions Column -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap align-middle">
                                                <div class="flex flex-col items-end gap-1.5">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <!-- Latest File Link -->
                                                        <?php if (!empty($filePath)): ?>
                                                            <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim(str_replace('\\', '/', $filePath), '/')); ?>" target="_blank" class="px-3.5 py-1.5 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                                <span>View File</span>
                                                            </a>
                                                        <?php endif; ?>

                                                        <!-- Submit / Re-upload Button -->
                                                        <?php if (!$isApproved && !$isEvaluated): ?>
                                                            <a href="submit_report.php?week=<?= (int)$report['week_number']; ?>" class="px-3.5 py-1.5 <?= $isRejected ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#0F2854] hover:bg-blue-900'; ?> text-white text-xs font-bold rounded-xl transition-colors shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                                <span><?= $isRejected ? 'Upload Revision' : (!empty($filePath) ? 'Re-upload' : 'Submit'); ?></span>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Previous Flagged PDF Link -->
                                                    <?php if (!empty($prevFilePath)): ?>
                                                        <div class="text-[11px] font-semibold text-slate-500 pr-0.5 flex items-center gap-1">
                                                            <span>Previous Flagged:</span>
                                                            <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim(str_replace('\\', '/', $prevFilePath), '/')); ?>" target="_blank" class="text-rose-700 font-bold hover:underline inline-flex items-center gap-1">
                                                                <span>PDF</span>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-14 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📄
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No accomplishment reports logged</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto mb-3">Start tracking your weekly OJT progress by submitting your first report.</p>
                            <a href="submit_report.php?week=1" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors shadow-xs inline-block">
                                Submit Week 1 Report &rarr;
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <!-- Supervisor Feedback Modal -->
    <div id="studentRemarksModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xl max-w-lg w-full p-7 space-y-5">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <div>
                    <h3 id="modalWeekHeader" class="text-sm font-black text-slate-950 tracking-tight">Supervisor Feedback</h3>
                    <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Please review the instructions below before submitting a new file</p>
                </div>
                <button onclick="closeStudentRemarksModal()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-xs font-bold flex items-center justify-center border border-slate-300 transition-colors cursor-pointer">✕</button>
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Required Corrections</span>
                    <div class="p-3.5 bg-rose-50/80 border border-rose-200 rounded-xl text-rose-950 font-medium leading-relaxed whitespace-pre-wrap" id="modalRemarksContent"></div>
                </div>

                <div id="modalDocLinkContainer">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Flagged Report File</span>
                    <a id="modalDocLink" href="#" target="_blank" class="w-full p-3 bg-slate-50 hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 flex items-center justify-between transition-colors">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <span>Open Flagged Document (PDF)</span>
                        </span>
                        <span class="text-blue-900 font-extrabold text-[11px]">View File &rarr;</span>
                    </a>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200/70 flex justify-end">
                <button type="button" onclick="closeStudentRemarksModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
    function filterReports(category, buttonEl) {
        document.querySelectorAll('.filter-tab').forEach(btn => {
            btn.classList.remove('active');
        });
        buttonEl.classList.add('active');

        const rows = document.querySelectorAll('.report-row');
        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            if (category === 'all') {
                row.style.display = '';
            } else {
                row.style.display = (status === category) ? '' : 'none';
            }
        });
    }

    function openStudentRemarksModal(weekHeader, remarks, fileUrl) {
        document.getElementById('modalWeekHeader').textContent = weekHeader + ' - Supervisor Feedback';
        document.getElementById('modalRemarksContent').textContent = remarks || 'No specific comments provided.';
        
        const docLink = document.getElementById('modalDocLink');
        const docContainer = document.getElementById('modalDocLinkContainer');
        if (fileUrl && fileUrl !== '') {
            docLink.href = fileUrl;
            docContainer.classList.remove('hidden');
        } else {
            docContainer.classList.add('hidden');
        }

        document.getElementById('studentRemarksModal').classList.remove('hidden');
    }

    function closeStudentRemarksModal() {
        document.getElementById('studentRemarksModal').classList.add('hidden');
    }
    </script>
</body>
</html>