<!-- src/pages/supervisor/dashboardPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard - ICS OJT Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Supervisor Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Side Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Sticky Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scrollable Body -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1">

                <!-- Welcome Greeting Banner Card (Matches Student UI Style) -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#0F2854]/5 border border-[#0F2854]/10 flex items-center justify-center text-[#0F2854] text-base font-bold shrink-0">
                            <?= !empty($supervisor['name']) && $supervisor['name'] !== '[Supervisor Full Name]' ? strtoupper(substr($supervisor['name'], 0, 1)) : 'S'; ?>
                        </div>
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug">Welcome back, <?= htmlspecialchars($supervisor['name'] ?? '[Supervisor Full Name]'); ?>!</h1>
                            <p class="text-slate-500 text-xs mt-0.5">
                                Overview of assigned interns and pending accomplishment report reviews.
                            </p>
                        </div>
                    </div>
                    <div class="px-3 py-1 rounded-full bg-blue-50 text-[#0F2854] border border-blue-200/60 text-[11px] font-semibold tracking-wide">
                        ● Host Supervisor
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Assigned Interns</p>
                        <p class="text-xl font-extrabold text-slate-900 mt-1"><?= intval($totalInterns ?? 0); ?></p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pending Approvals</p>
                        <p class="text-xl font-extrabold text-amber-500 mt-1"><?= intval($totalPending ?? 0); ?></p>
                    </div>
                </div>

                <!-- Table: Pending Weekly Accomplishment Reports -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 px-5 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Pending Weekly Accomplishment Reports</h3>
                            <p class="text-slate-400 text-[11px] mt-0.5">Review and verify weekly logs submitted by assigned students</p>
                        </div>
                        <a href="review_reports.php" class="text-[11px] font-semibold text-[#0F2854] hover:underline">View All →</a>
                    </div>

                    <?php if (!empty($pendingReports) && count($pendingReports) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Intern</th>
                                        <th class="py-3 px-5">Submitted</th>
                                        <th class="py-3 px-5">Week</th>
                                        <th class="py-3 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                                    <?php foreach ($pendingReports as $report): ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <!-- Intern Name -->
                                            <td class="py-3 px-5 font-semibold text-slate-900">
                                                <?= htmlspecialchars($report['student_name'] ?? '[Intern Name]'); ?>
                                            </td>

                                            <!-- Submitted Date -->
                                            <td class="py-3 px-5 text-slate-500">
                                                <?= !empty($report['created_at']) ? date("M d, Y", strtotime($report['created_at'])) : '—'; ?>
                                            </td>

                                            <!-- Week Number -->
                                            <td class="py-3 px-5 font-semibold text-slate-700">
                                                Week <?= htmlspecialchars($report['week_number'] ?? '1'); ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-3 px-5 text-right">
                                                <a href="review_report.php?id=<?= $report['id']; ?>" class="px-3.5 py-1 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-medium rounded-xl transition-all shadow-2xs inline-block">
                                                    Review Report
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 px-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 text-base font-bold">📝</div>
                            <h4 class="text-sm font-semibold text-slate-800">No pending reports</h4>
                            <p class="text-xs text-slate-500 max-w-xs mx-auto mt-0.5">There are currently no accomplishment reports awaiting review.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Activities Section -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-3">
                    <div class="border-b border-slate-100 pb-2.5">
                        <h3 class="text-sm font-bold text-slate-900">Recent Activities</h3>
                        <p class="text-slate-400 text-[11px] mt-0.5">Latest logs and submission activity from your interns</p>
                    </div>

                    <div class="space-y-3 pt-1">
                        <?php if (!empty($recentActivities) && count($recentActivities) > 0): ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="flex items-start gap-2.5 text-xs text-slate-600">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <div>
                                        <p class="font-medium text-slate-800"><?= htmlspecialchars($activity['title']); ?></p>
                                        <p class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($activity['time_ago']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-start gap-2.5 text-xs text-slate-600">
                                <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                <div>
                                    <p class="font-medium text-slate-800">[Intern Name] submitted Week 3 report</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">2 hours ago</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 text-xs text-slate-600">
                                <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                <div>
                                    <p class="font-medium text-slate-800">You approved Week 2 report of [Intern Name]</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">3 hours ago</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </main>
        </div>
    </div>

</body>
</html>