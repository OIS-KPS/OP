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

                <!-- 2. Conditional 486-Hour / Final Evaluation Notification Banner -->
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
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Waiting for Supervisor
                                </span>
                            <?php else: ?>
                                <form method="POST" action="reports.php" onsubmit="return confirm('Confirm that you have completed rendering all required 486 hours of your internship. Your supervisor will be notified to evaluate you.');">
                                    <button type="submit" name="request_evaluation" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer inline-flex items-center gap-2">
                                        <span>Request Final Evaluation</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 3. Status Summary Metric Cards -->
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

                    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-1">
                        <span class="block text-[10px] font-black text-slate-700 uppercase tracking-wider">Waiting for Review</span>
                        <p class="text-3xl font-black text-amber-600"><?= (int)$totalPending; ?></p>
                        <p class="text-xs font-semibold text-slate-600 pt-0.5">Pending review</p>
                    </div>
                </div>

                <!-- 4. Reports Table Container with Integrated Action Button -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 tracking-wider uppercase">Submission History</h3>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Chronological record of weekly accomplishment logs</p>
                        </div>

                        <!-- Direct Table Action: Submit Next Report -->
                        <div class="flex items-center gap-2">
                            <?php if (!$isEvaluated && $totalApproved < 12): ?>
                                <a href="submit_report.php?week=<?= $nextWeekToSubmit; ?>" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-all shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    <span>Submit Week <?= $nextWeekToSubmit; ?></span>
                                </a>
                            <?php endif; ?>
                            
                            <span class="text-xs font-bold text-slate-800 bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
                                Sorted by Week
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($reports)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Submitted Date &amp; Time</th>
                                        <th class="py-4 px-6">Review Status</th>
                                        <th class="py-4 px-6 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($reports as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                        $filePath = $report['file_path'] ?? '';
                                        $dateSubmitted = $report['submitted_at'] ?? null;
                                        $approvedAt = $report['approved_at'] ?? null;
                                        $isApproved = ($status === 'approved');
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors group">
                                            
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] font-bold text-xs flex items-center justify-center border border-slate-300 group-hover:bg-[#0F2854] group-hover:text-white group-hover:border-[#0F2854] transition-all duration-200">
                                                        W<?= htmlspecialchars($report['week_number']); ?>
                                                    </div>
                                                    <div>
                                                        <span class="font-extrabold text-slate-950 text-sm">Week <?= htmlspecialchars($report['week_number']); ?></span>
                                                        <span class="block text-[10px] font-semibold text-slate-500">Accomplishment Log</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="font-semibold text-xs text-slate-700">
                                                    <?= !empty($dateSubmitted) ? date("M d, Y \a\\t g:i A", strtotime($dateSubmitted)) : '—'; ?>
                                                </span>
                                            </td>

                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isApproved): ?>
                                                    <div class="flex flex-col items-start gap-1">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                            Approved
                                                        </span>
                                                        <?php if (!empty($approvedAt)): ?>
                                                            <span class="text-[11px] font-semibold text-slate-500 pl-0.5">
                                                                <?= date("M d, Y \a\\t g:i A", strtotime($approvedAt)); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif ($status === 'rejected'): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100/80 text-rose-900 border border-rose-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                        Needs Changes
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        Waiting for Review
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <?php if (!empty($filePath)): ?>
                                                        <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim(str_replace('\\', '/', $filePath), '/')); ?>" target="_blank" class="px-3.5 py-1.5 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                            <span>View File</span>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (!$isApproved && !$isEvaluated): ?>
                                                        <a href="submit_report.php?week=<?= $report['week_number']; ?>" class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-all shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                            <span><?= !empty($filePath) ? 'Re-upload' : 'Submit'; ?></span>
                                                        </a>
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
                            <a href="submit_report.php?week=1" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-all shadow-xs inline-block">
                                Submit Week 1 Report &rarr;
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>