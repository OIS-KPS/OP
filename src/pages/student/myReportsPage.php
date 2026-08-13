<!-- src/pages/student/myReportsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS OJT Portal - My Reports</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Global Custom Stylesheet -->
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <!-- Right Side: Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Sticky Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Scrollable Body -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1">

                <!-- Success Alert Banner -->
                <?php if (isset($_GET['submitted']) && $_GET['submitted'] === 'success'): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-600 font-bold text-sm">✓</span>
                            <div>
                                <p class="font-bold text-xs">WAR Submitted Successfully!</p>
                                <p class="text-[11px] text-emerald-600">Your accomplishment report has been uploaded and is under review.</p>
                            </div>
                        </div>
                        <a href="reports.php" class="text-[11px] font-semibold text-emerald-700 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <!-- Error Alert Banner -->
                <?php if (!empty($_SESSION['error_message'])): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-600 font-bold text-sm">✕</span>
                            <p class="font-semibold text-xs"><?= htmlspecialchars($_SESSION['error_message']); ?></p>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <!-- Header Card with Dynamic Button -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Weekly Accomplishment Reports</h2>
                        <p class="text-slate-500 text-xs mt-0.5">
                            <?php if (empty($reports)): ?>
                                Start tracking your OJT progress by submitting your Week 1 report.
                            <?php else: ?>
                                Manage, track, and upload your weekly OJT accomplishment logs.
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php $nextWeekToSubmit = count($reports) + 1; ?>
                    <a href="submit_report.php?week=<?= $nextWeekToSubmit; ?>" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-medium rounded-xl text-xs transition-all shadow-xs flex items-center gap-1.5 whitespace-nowrap">
                        <span>+</span> Submit Week <?= $nextWeekToSubmit; ?> Report
                    </a>
                </div>

                <!-- Summary Overview Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total IT Tasks %</p>
                        <p class="text-xl font-extrabold text-[#0F2854] mt-1"><?= intval($overallIT ?? 0); ?>%</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Clerical Tasks %</p>
                        <p class="text-xl font-extrabold text-slate-700 mt-1"><?= intval($overallClerical ?? 0); ?>%</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Submitted</p>
                        <p class="text-xl font-extrabold text-emerald-600 mt-1">
                            <?= count($reports ?? []); ?> <?= count($reports ?? []) === 1 ? 'Week' : 'Weeks'; ?>
                        </p>
                    </div>
                </div>

                <!-- Reports Table Container -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 px-5 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-slate-900 text-sm">Submission History</h3>
                        <span class="text-[11px] text-slate-400 font-medium">Sorted by Week Number</span>
                    </div>

                    <?php if (!empty($reports) && count($reports) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Week</th>
                                        <th class="py-3 px-5">Date & Time Submitted</th>
                                        <th class="py-3 px-5">IT Task %</th>
                                        <th class="py-3 px-5">Clerical %</th>
                                        <th class="py-3 px-5">Status</th>
                                        <th class="py-3 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                                    <?php foreach ($reports as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                        $filePath = $report['file_path'] ?? $report['attachment_path'] ?? '';
                                        $dateSubmitted = $report['submitted_at'] ?? $report['created_at'] ?? null;
                                        $isApproved = ($status === 'approved');
                                    ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <!-- Week -->
                                            <td class="py-3 px-5 font-semibold text-slate-900">
                                                Week <?= htmlspecialchars($report['week_number']); ?>
                                            </td>

                                            <!-- Date & Time Submitted -->
                                            <td class="py-3 px-5 text-slate-600 font-medium">
                                                <?= !empty($dateSubmitted) ? date("M d, Y \a\\t g:i A", strtotime($dateSubmitted)) : '—'; ?>
                                            </td>

                                            <!-- IT % -->
                                            <td class="py-3 px-5 font-medium text-slate-700">
                                                <?= htmlspecialchars($report['it_percent'] ?? '0'); ?>%
                                            </td>

                                            <!-- Clerical % -->
                                            <td class="py-3 px-5 font-medium text-slate-500">
                                                <?= htmlspecialchars($report['clerical_percent'] ?? '0'); ?>%
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-3 px-5">
                                                <?php if ($isApproved): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200/50">
                                                        ● Approved
                                                    </span>
                                                <?php elseif ($status === 'pending'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-200/50">
                                                        ● Under Review
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[11px] font-medium border border-rose-200/50">
                                                        ● Needs Revision
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions -->
                                            <td class="py-3 px-5 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <?php if (!empty($filePath)): ?>
                                                        <a href="/ICS-PORTAL/<?= htmlspecialchars($filePath); ?>" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium rounded-lg border border-slate-200 transition-all">
                                                            View
                                                        </a>
                                                    <?php endif; ?>

                                                    <!-- Hide Re-upload button if the report is APPROVED -->
                                                    <?php if (!$isApproved): ?>
                                                        <a href="submit_report.php?week=<?= $report['week_number']; ?>" class="px-2.5 py-1 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-medium rounded-lg transition-all shadow-2xs">
                                                            <?= !empty($filePath) ? 'Re-upload' : 'Submit'; ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-semibold rounded-lg border border-emerald-200/60 inline-flex items-center gap-1">
                                                            Approved
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <!-- Empty State for Brand New Users -->
                        <div class="text-center py-12 px-4">
                            <div class="w-10 h-10 bg-blue-50 text-[#0F2854] rounded-xl flex items-center justify-center mx-auto mb-2 text-lg font-bold">
                                📊
                            </div>
                            <h4 class="text-sm font-semibold text-slate-800">No accomplishment reports logged</h4>
                            <p class="text-xs text-slate-500 max-w-xs mx-auto mt-0.5 mb-4">Start tracking your weekly OJT progress by submitting your first report.</p>
                            <a href="submit_report.php?week=1" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-medium rounded-xl text-xs transition-all shadow-xs inline-block">
                                Submit Week 1 Report →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>