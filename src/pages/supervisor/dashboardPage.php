<!-- src/pages/supervisor/dashboardPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard - ICS OJT Portal</title>
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
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Supervisor Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Content Canvas -->
            <main class="p-6 md:p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1">

                <!-- 2. Polished Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    
                    <!-- Interns Metric Card -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition-colors">
                        <div class="space-y-1">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Assigned Interns</span>
                            <p class="text-3xl font-extrabold text-slate-900 tracking-tight"><?= intval($totalInterns ?? 0); ?></p>
                            <p class="text-xs font-medium text-slate-500">Students active in this cycle</p>
                        </div>
                        <div class="w-13 h-13 rounded-2xl bg-blue-50 text-[#0F2854] border border-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                    </div>

                    <!-- Pending Approvals Metric Card -->
                    <div class="bg-white rounded-2xl p-6 border <?= !empty($totalPending) && $totalPending > 0 ? 'border-amber-200/80 bg-gradient-to-br from-white to-amber-50/20' : 'border-slate-200/80'; ?> shadow-xs flex items-center justify-between transition-colors">
                        <div class="space-y-1">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Approvals</span>
                            <div class="flex items-baseline gap-2">
                                <p class="text-3xl font-extrabold text-amber-600 tracking-tight"><?= intval($totalPending ?? count($pendingReports ?? [])); ?></p>
                                <?php if (!empty($totalPending) && $totalPending > 0): ?>
                                    <span class="text-[11px] font-bold text-amber-700 bg-amber-100/70 px-2 py-0.5 rounded-md">Action needed</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs font-medium text-slate-500">Weekly logs awaiting your sign-off</p>
                        </div>
                        <div class="w-13 h-13 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                </div>

                <!-- 3. Pending Weekly Reports Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/40">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Pending Accomplishment Reports</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Submitted logs requiring verification and feedback</p>
                        </div>
                        <span class="text-xs font-bold text-slate-700 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-2xs">
                            <?= count($pendingReports ?? []); ?> Pending
                        </span>
                    </div>

                    <?php if (!empty($pendingReports) && count($pendingReports) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Submitted Date & Time</th>
                                        <th class="py-4 px-6">Review Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php foreach ($pendingReports as $report): ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors group">
                                            
                                            <!-- Intern Identity -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-200/80 group-hover:border-[#0F2854] transition-colors">
                                                        <?php if (!empty($report['student_avatar'])): ?>
                                                            <img src="<?= htmlspecialchars($report['student_avatar']); ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($report['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($report['student_name']); ?></p>
                                                        <p class="text-[11px] text-slate-400 font-medium">ID: <?= htmlspecialchars($report['student_number'] ?? 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Week Chip -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-[#0F2854] font-bold text-xs border border-slate-200">
                                                    Week <?= htmlspecialchars($report['week_number'] ?? '1'); ?>
                                                </span>
                                            </td>

                                            <!-- Date Submitted -->
                                            <td class="py-4 px-6 text-slate-600 font-medium whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                                    <span><?= !empty($report['submitted_at']) ? date("M d, Y \a\\t g:i A", strtotime($report['submitted_at'])) : '—'; ?></span>
                                                </div>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Awaiting Review
                                                </span>
                                            </td>

                                            <!-- Actions -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <?php if (!empty($report['file_path'])): ?>
                                                        <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/')); ?>" 
                                                           target="_blank" 
                                                           class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 shadow-2xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                            <span>View File</span>
                                                        </a>
                                                    <?php endif; ?>

                                                    <a href="review_reports.php?id=<?= $report['id'] ?? ''; ?>" class="px-4 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-all shadow-xs inline-flex items-center gap-1.5">
                                                        <span>Review</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-14 px-4 space-y-2">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-bold border border-emerald-100">
                                ✓
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">All caught up!</h4>
                            <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto">There are currently no accomplishment reports awaiting your review.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 4. Connected Timeline Activity Feed -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Recent Activity Feed</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Timeline of recent report submissions and verification events</p>
                        </div>
                    </div>

                    <div class="relative pl-6 space-y-5 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        <?php if (!empty($recentActivities) && count($recentActivities) > 0): ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="relative flex items-start justify-between gap-4">
                                    <!-- Timeline Dot -->
                                    <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-[#0F2854] ring-4 ring-white"></div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-xs leading-snug"><?= htmlspecialchars($activity['title'] ?? ''); ?></p>
                                        <p class="text-[11px] font-medium text-slate-400 mt-0.5"><?= htmlspecialchars($activity['description'] ?? 'Submitted weekly accomplishment log'); ?></p>
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-400 shrink-0"><?= htmlspecialchars($activity['time_ago'] ?? ''); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-xs text-slate-400 italic py-1">No recent submission activities recorded.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </main>
        </div>
    </div>

</body>
</html>