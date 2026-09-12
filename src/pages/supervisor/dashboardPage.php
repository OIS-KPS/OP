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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Supervisor Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1">

                <!-- 1. Top Header Welcome Card -->
                        

                <!-- 2. Balanced 3-Column Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    
                    <!-- Card 1: Assigned Interns -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-700 uppercase tracking-wider">Assigned Interns</span>
                            <p class="text-3xl font-black text-slate-950 tracking-tight"><?= (int)$totalInterns; ?></p>
                            <p class="text-xs font-semibold text-slate-600">Active students in department</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0F2854] border border-blue-200 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/></svg>
                        </div>
                    </div>

                    <!-- Card 2: Pending Reviews -->
                    <div class="bg-white rounded-2xl p-6 border <?= $totalPending > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/90'; ?> shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-700 uppercase tracking-wider">Pending Reports</span>
                            <p class="text-3xl font-black <?= $totalPending > 0 ? 'text-amber-600' : 'text-slate-950'; ?> tracking-tight"><?= (int)$totalPending; ?></p>
                            <p class="text-xs font-semibold text-slate-600">Weekly logs awaiting sign-off</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-300 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <!-- Card 3: Completed Final Evaluations -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-700 uppercase tracking-wider">Completed Evaluations</span>
                            <p class="text-3xl font-black text-emerald-600 tracking-tight"><?= (int)$totalEvaluated; ?></p>
                            <p class="text-xs font-semibold text-slate-600">Appraisals signed &amp; verified</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-300 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                </div>

                <!-- 3. Two-Column Dashboard Content Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">
                    
                    <!-- Left (3 Cols): Quick Review Queue -->
                    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                        <div class="p-6 border-b border-slate-200/70 flex justify-between items-center bg-slate-50/60">
                            <div>
                                <h3 class="text-xs font-black text-slate-900 tracking-wider uppercase">Action Required</h3>
                                <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Pending accomplishment reports awaiting verification</p>
                            </div>
                            <a href="review_reports.php?status=Pending" class="text-xs font-bold text-[#0F2854] hover:underline">
                                View Full Queue &rarr;
                            </a>
                        </div>

                        <?php if (!empty($pendingReports) && count($pendingReports) > 0): ?>
                            <div class="divide-y divide-slate-200/80">
                                <?php foreach ($pendingReports as $report): ?>
                                    <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-300">
                                                <?php if (!empty($report['student_avatar'])): ?>
                                                    <img src="<?= htmlspecialchars($report['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                <?php else: ?>
                                                    <?= strtoupper(substr($report['student_name'] ?? 'S', 0, 1)); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-950 text-sm tracking-tight truncate"><?= htmlspecialchars($report['student_name']); ?></p>
                                                <p class="text-[11px] text-slate-500 font-semibold">
                                                    Week <?= htmlspecialchars($report['week_number']); ?> &bull; <?= date("M d, Y \a\\t g:i A", strtotime($report['submitted_at'])); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <a href="review_reports.php?review_id=<?= (int)$report['report_id']; ?>&status=Pending" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all shrink-0 inline-flex items-center gap-1.5 cursor-pointer">
                                            <span>Review</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12 px-4 space-y-2">
                                <div class="w-10 h-10 bg-emerald-100/80 text-emerald-800 rounded-xl flex items-center justify-center mx-auto text-base font-black border border-emerald-300">
                                    ✓
                                </div>
                                <h4 class="text-sm font-black text-slate-900">All caught up</h4>
                                <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">No pending accomplishment reports waiting for review.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Right (2 Cols): Timeline Activity Feed -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                        <div class="border-b border-slate-200/70 pb-3">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Recent Activity Feed</h3>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Timeline of verified submissions and events</p>
                        </div>

                        <div class="relative pl-5 space-y-5 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            <?php if (!empty($recentActivities) && count($recentActivities) > 0): ?>
                                <?php foreach ($recentActivities as $activity): 
                                    $isApprove = ($activity['type'] === 'approved');
                                    $isReject = ($activity['type'] === 'rejected');
                                ?>
                                    <div class="relative">
                                        <!-- Dot Indicator -->
                                        <div class="absolute -left-5 top-1 w-2.5 h-2.5 rounded-full <?= $isApprove ? 'bg-emerald-600' : ($isReject ? 'bg-rose-600' : 'bg-amber-500'); ?> ring-4 ring-white"></div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-xs leading-snug"><?= $activity['title']; ?></p>
                                            <span class="text-[10px] font-bold text-slate-400 block mt-0.5"><?= $activity['time_ago']; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-xs text-slate-500 font-semibold italic py-2">No recent activity events recorded.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            </main>
        </div>
    </div>

</body>
</html>