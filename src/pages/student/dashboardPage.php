<!-- src/pages/student/dashboardPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS OJT Portal - Student Dashboard</title>
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

            <!-- Reusable Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scrollable Body -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1">

                <!-- Welcome Greeting Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-3">
                    <div class="flex items-center gap-3">
                        <!-- Dynamic Avatar: Profile Picture OR Initial -->
                        <div class="w-10 h-10 rounded-xl bg-[#0F2854]/5 border border-[#0F2854]/10 flex items-center justify-center text-[#0F2854] text-base font-bold shrink-0 overflow-hidden">
                            <?php if (!empty($_SESSION['user_picture'])): ?>
                                <img src="<?= htmlspecialchars($_SESSION['user_picture']); ?>" alt="Profile" class="w-full h-full object-cover" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <?= !empty($student['name']) ? strtoupper(substr($student['name'], 0, 1)) : 'S'; ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug">Welcome back, <?= htmlspecialchars($student['name'] ?? 'Student'); ?>!</h1>
                            <p class="text-slate-500 text-xs mt-0.5">
                                ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?></span> • 
                                Program: <span class="font-semibold text-slate-700"><?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-semibold tracking-wide">
                        ● Intern Active
                    </div>
                </div>

                <!-- Primary Action Banner -->
                <div class="bg-[#0F2854] rounded-2xl p-5 text-white shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 bg-blue-500/20 text-blue-200 rounded-md text-[10px] font-semibold uppercase tracking-wider mb-1.5 border border-blue-400/20">
                            Action Required
                        </span>
                        <h2 class="text-base font-bold leading-tight">Week <?= htmlspecialchars($nextWeek ?? '1'); ?> Report Due</h2>
                        <p class="text-slate-300 text-xs mt-0.5">Please submit your weekly accomplishment log to stay on track with your internship hours.</p>
                    </div>
                    <a href="submit_report.php?week=<?= htmlspecialchars($nextWeek ?? '1'); ?>" 
                        class="px-4 py-2 bg-[#2563EB] hover:bg-white text-white hover:text-[#0F2854] font-medium rounded-xl text-xs transition-all duration-200 shadow-xs whitespace-nowrap">
                        Submit Week <?= htmlspecialchars($nextWeek ?? '1'); ?> Report →
                    </a>
                </div>

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Reports Submitted</p>
                        <p class="text-xl font-extrabold text-slate-900 mt-1"><?= intval($totalSubmitted ?? 0); ?></p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Reports Approved</p>
                        <p class="text-xl font-extrabold text-emerald-600 mt-1"><?= intval($totalApproved ?? 0); ?></p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pending Approval</p>
                        <p class="text-xl font-extrabold text-amber-500 mt-1"><?= intval($totalPending ?? 0); ?></p>
                    </div>
                </div>

                <!-- Weekly Accomplishment Log Section -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 px-5 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Weekly Accomplishment Log</h3>
                        <p class="text-slate-400 text-[11px] mt-0.5">Summary of submitted reports and supervisor review status</p>
                    </div>

                    <?php if (!empty($reports) && count($reports) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Week</th>
                                        <th class="py-3 px-5">Date Submitted</th>
                                        <th class="py-3 px-5">Status</th>
                                        <th class="py-3 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                                    <?php foreach ($reports as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                    ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3 px-5 font-semibold text-slate-900">Week <?= htmlspecialchars($report['week_number']); ?></td>
                                            <td class="py-3 px-5 text-slate-500">
                                                <?= !empty($report['submitted_at']) ? date("M d, Y", strtotime($report['submitted_at'])) : '—'; ?>
                                            </td>
                                            <td class="py-3 px-5">
                                                <?php if ($status === 'approved'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200/50">● Approved</span>
                                                <?php elseif ($status === 'pending'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-200/50">● Under Review</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[11px] font-medium border border-rose-200/50">● Revision Needed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 px-5 text-right">
                                                <?php if (!empty($report['file_path'])): ?>
                                                    <a href="/ICS-PORTAL/<?= htmlspecialchars($report['file_path']); ?>" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                                                        View File
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-slate-400">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 px-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 text-base font-bold">📝</div>
                            <h4 class="text-sm font-semibold text-slate-800">No reports submitted yet</h4>
                            <p class="text-xs text-slate-500 max-w-xs mx-auto mt-0.5 mb-3">You haven't logged any weekly reports. Use the button above to make your first submission.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>