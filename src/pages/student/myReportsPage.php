<!-- src/pages/student/myReportsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - OJT Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1">

                <!-- Alert Messages -->
                <?php if (isset($_GET['submitted']) && $_GET['submitted'] === 'success'): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-600 font-bold text-sm">✓</span>
                            <div>
                                <p class="font-bold text-xs">Report Submitted</p>
                                <p class="text-[11px] text-emerald-700">Your weekly accomplishment report has been uploaded and is waiting for review.</p>
                            </div>
                        </div>
                        <a href="reports.php" class="text-xs font-semibold text-emerald-700 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['error_message'])): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-600 font-bold text-sm">✕</span>
                            <p class="font-semibold text-xs"><?= htmlspecialchars($_SESSION['error_message']); ?></p>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <!-- Header Actions Card -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Weekly Accomplishment Reports</h1>
                        <p class="text-xs font-medium text-slate-500 mt-1">Manage and track your weekly OJT accomplishment logs.</p>
                    </div>

                    <?php $nextWeekToSubmit = count($reports ?? []) + 1; ?>
                    <a href="submit_report.php?week=<?= $nextWeekToSubmit; ?>" class="px-5 py-3 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-all shadow-xs flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>+</span> Submit Week <?= $nextWeekToSubmit; ?> Report
                    </a>
                </div>

                <!-- Status Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-1">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reports Submitted</span>
                        <p class="text-3xl font-extrabold text-slate-900"><?= $totalReportsCount ?? count($reports ?? []); ?></p>
                        <p class="text-xs font-medium text-slate-500 pt-0.5">Total accomplishment logs</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-1">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reports Approved</span>
                        <p class="text-3xl font-extrabold text-emerald-600"><?= $totalApproved ?? 0; ?></p>
                        <p class="text-xs font-medium text-slate-500 pt-0.5">Verified by supervisor</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-1">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Waiting for Review</span>
                        <p class="text-3xl font-extrabold text-amber-600"><?= $totalPending ?? 0; ?></p>
                        <p class="text-xs font-medium text-slate-500 pt-0.5">Pending review</p>
                    </div>
                </div>

                <!-- Reports Table Container -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/40">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Submission History</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Chronological record of weekly accomplishment logs</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1 rounded-lg border border-slate-200/70 shadow-2xs">
                            Sorted by Week
                        </span>
                    </div>

                    <?php if (!empty($reports)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Submitted Date & Time</th>
                                        <th class="py-4 px-6">Review Status</th>
                                        <th class="py-4 px-6 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php foreach ($reports as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                        $filePath = $report['file_path'] ?? '';
                                        $dateSubmitted = $report['submitted_at'] ?? null;
                                        $isApproved = ($status === 'approved');
                                    ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors group">
                                            
                                            <!-- 1. Week Indicator Chip -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] font-bold text-xs flex items-center justify-center border border-slate-200/80 group-hover:bg-[#0F2854] group-hover:text-white group-hover:border-[#0F2854] transition-all duration-200">
                                                        W<?= htmlspecialchars($report['week_number']); ?>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-slate-900 text-sm">Week <?= htmlspecialchars($report['week_number']); ?></span>
                                                        <span class="block text-[10px] font-semibold text-slate-400">Accomplishment Log</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- 2. Date & Time Submitted -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-2 text-slate-600">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                                    </svg>
                                                    <span class="font-medium text-xs">
                                                        <?= !empty($dateSubmitted) ? date("M d, Y \a\\t g:i A", strtotime($dateSubmitted)) : '—'; ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- 3. Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isApproved): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Approved
                                                    </span>
                                                <?php elseif ($status === 'rejected'): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                        Needs Changes
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Waiting for Review
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- 4. Actions -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <?php if (!empty($filePath)): ?>
                                                        <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim($filePath, '/')); ?>" target="_blank" class="px-3.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200/90 shadow-2xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                            <span>View File</span>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (!$isApproved): ?>
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
                            <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center mx-auto text-lg font-bold border border-slate-200">
                                📄
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">No accomplishment reports logged</h4>
                            <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto mb-3">Start tracking your weekly OJT progress by submitting your first report.</p>
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