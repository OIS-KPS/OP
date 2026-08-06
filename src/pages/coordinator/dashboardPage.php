<!-- src/pages/coordinator/dashboardPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CQI Analytics Dashboard - OJT Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Coordinator Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Right Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Dynamic Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Content Area -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-6 flex-1 relative">

                <!-- Header Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Curriculum Continuous Quality Improvement (CQI) Dashboard</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Real-time analytical insights derived from NLP entity extraction on supervisor-approved WARs.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-blue-50 text-[#0F2854] font-bold rounded-full border border-blue-200 text-[11px]">
                            🎓 BSIT Cohort 2026 Analytics
                        </span>
                    </div>
                </div>

                <!-- 🎛️ DYNAMIC CQI SCOPE FILTER BAR -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4 text-xs">
                    <form method="GET" class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                        
                        <!-- Filter 1: Host Agency -->
                        <div class="flex items-center gap-2">
                            <label class="font-bold text-slate-700">Filter Agency:</label>
                            <select name="company_filter" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= ($_GET['company_filter'] ?? 'all') === 'all' ? 'selected' : ''; ?>>All Host Offices</option>
                                <option value="ICS IT Dept" <?= ($_GET['company_filter'] ?? '') === 'ICS IT Dept' ? 'selected' : ''; ?>>ICS IT Dept</option>
                                <option value="LGU Manolo Fortich" <?= ($_GET['company_filter'] ?? '') === 'LGU Manolo Fortich' ? 'selected' : ''; ?>>LGU Manolo Fortich</option>
                            </select>
                        </div>

                        <!-- Filter 2: Week Milestone -->
                        <div class="flex items-center gap-2">
                            <label class="font-bold text-slate-700">Week Range:</label>
                            <select name="week_filter" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all">Entire Semester (Weeks 1 - 12)</option>
                                <option value="1-6">Weeks 1 - 6 (First Half)</option>
                                <option value="7-12">Weeks 7 - 12 (Second Half)</option>
                            </select>
                        </div>

                        <!-- Filter 3: Category Focus -->
                        <div class="flex items-center gap-2">
                            <label class="font-bold text-slate-700">Competency Focus:</label>
                            <select name="type_filter" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all">All Extracted Activities</option>
                                <option value="technical">Technical IT Skills Only</option>
                                <option value="clerical">Clerical/Administrative Only</option>
                            </select>
                        </div>

                    </form>

                    <div class="text-[11px] font-bold text-slate-400">
                        Priority Trigger: <span class="text-rose-600 font-extrabold">≥ 50% Clerical = High Priority</span>
                    </div>
                </div>

                <!-- Summary Metric Cards (4 Grid) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Metric 1 -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Approved Reports</p>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-slate-900"><?= $totalReports; ?></span>
                            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">100% Verified</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Total WARs analyzed by engine</p>
                    </div>

                    <!-- Metric 2 -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Extracted Entities</p>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-[#0F2854]"><?= $totalEntities; ?></span>
                            <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">Parsed Tags</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Activity competencies identified</p>
                    </div>

                    <!-- Metric 3 -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Core IT Task Ratio</p>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-emerald-600"><?= $overallTechPct; ?>%</span>
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">High Alignment</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Technical competency tasks</p>
                    </div>

                    <!-- Metric 4 -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Placement Quality Alerts</p>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-rose-600"><?= $flaggedOfficesCount; ?> Office</span>
                            <span class="text-[10px] font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">🔴 High Clerical</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Offices with ≥ 50% non-IT work</p>
                    </div>

                </div>

                <!-- 🚨 SIDE-BY-SIDE EASY-TO-READ PRIORITY INTERVENTION TABLES -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- TABLE 1: STUDENTS DOING MOSTLY CLERICAL WORK -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="p-4 border-b border-slate-100 bg-rose-50/40 flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                        <span class="text-rose-600">🔴</span> Priority Interns (Needs IT Task Re-assignment)
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Students logging high clerical or non-IT activities</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">1 Flagged</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                            <th class="py-2.5 px-4">Student Name</th>
                                            <th class="py-2.5 px-4">Host Office</th>
                                            <th class="py-2.5 px-4">Clerical Load</th>
                                            <th class="py-2.5 px-4 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        
                                        <!-- High Priority Intern Row -->
                                        <tr class="hover:bg-slate-50/80">
                                            <td class="py-3 px-4 font-bold text-slate-900">
                                                Pauline May Coming
                                                <p class="text-[10px] text-slate-400 font-normal">ID: 20231054</p>
                                            </td>
                                            <td class="py-3 px-4 text-slate-600 font-medium">LGU Manolo Fortich</td>
                                            <td class="py-3 px-4 font-extrabold text-rose-600">100% Clerical</td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">🔴 Urgent</span>
                                            </td>
                                        </tr>

                                        <!-- Medium Priority Intern Row -->
                                        <tr class="hover:bg-slate-50/80">
                                            <td class="py-3 px-4 font-bold text-slate-900">
                                                Sander Perejan
                                                <p class="text-[10px] text-slate-400 font-normal">ID: 20231055</p>
                                            </td>
                                            <td class="py-3 px-4 text-slate-600 font-medium">ICS IT Dept</td>
                                            <td class="py-3 px-4 font-bold text-amber-600">25% Clerical</td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-md">🟡 Monitor</span>
                                            </td>
                                        </tr>

                                        <!-- Optimal Intern Row -->
                                        <tr class="hover:bg-slate-50/80">
                                            <td class="py-3 px-4 font-bold text-slate-900">
                                                Katelyn Coming
                                                <p class="text-[10px] text-slate-400 font-normal">ID: 20231053</p>
                                            </td>
                                            <td class="py-3 px-4 text-slate-600 font-medium">ICS IT Dept</td>
                                            <td class="py-3 px-4 font-bold text-emerald-600">0% Clerical</td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-md">🟢 Optimal</span>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE 2: OFFICES GIVING THE MOST CLERICAL WORK -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="p-4 border-b border-slate-100 bg-amber-50/40 flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>🏢</span> Host Agencies Giving Most Non-IT Tasks
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Offices evaluated for partner agreement review</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">1 Office Exceeded Threshold</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                            <th class="py-2.5 px-4">Partner Host Office</th>
                                            <th class="py-2.5 px-4">Branch/Dept</th>
                                            <th class="py-2.5 px-4">Avg Clerical Ratio</th>
                                            <th class="py-2.5 px-4 text-right">CQI Flag</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        
                                        <!-- Flagged Agency Row -->
                                        <tr class="hover:bg-slate-50/80">
                                            <td class="py-3 px-4 font-bold text-slate-900">LGU Manolo Fortich</td>
                                            <td class="py-3 px-4 text-slate-500">MIS Branch</td>
                                            <td class="py-3 px-4 font-extrabold text-rose-600">75% Clerical</td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">High Non-IT Work</span>
                                            </td>
                                        </tr>

                                        <!-- Optimal Agency Row -->
                                        <tr class="hover:bg-slate-50/80">
                                            <td class="py-3 px-4 font-bold text-slate-900">ICS IT Dept</td>
                                            <td class="py-3 px-4 text-slate-500">Software Lab</td>
                                            <td class="py-3 px-4 font-bold text-emerald-600">12% Clerical</td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-md">Strong IT Focus</span>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Donut Chart -->
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-4">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">Task Activity Breakdown</h3>
                            <p class="text-[11px] text-slate-400">Technical IT vs. Clerical/Non-IT activities</p>
                        </div>
                        <div class="relative flex items-center justify-center p-2">
                            <canvas id="taskDistributionChart" class="max-h-[220px]"></canvas>
                        </div>
                        <div class="flex items-center justify-around text-xs border-t border-slate-100 pt-3">
                            <div class="flex items-center gap-1.5 font-bold text-[#0F2854]">
                                <span class="w-3 h-3 rounded-full bg-[#0F2854]"></span>
                                <span>Core IT (<?= $overallTechPct; ?>%)</span>
                            </div>
                            <div class="flex items-center gap-1.5 font-bold text-rose-500">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span>Clerical (<?= $overallClericalPct; ?>%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bar Chart -->
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-4 lg:col-span-2">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">Top In-Demand Technical Competencies</h3>
                            <p class="text-[11px] text-slate-400">Frequency of technical skills utilized across partner agencies</p>
                        </div>
                        <div>
                            <canvas id="topSkillsChart" class="max-h-[220px]"></canvas>
                        </div>
                    </div>

                </div>

            </main>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script>
        // Donut Chart Render
        const ctxDonut = document.getElementById('taskDistributionChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Core IT Technical', 'Clerical / Non-IT'],
                datasets: [{
                    data: [<?= $overallTechPct; ?>, <?= $overallClericalPct; ?>],
                    backgroundColor: ['#0F2854', '#F43F5E'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Bar Chart Render
        const ctxBar = document.getElementById('topSkillsChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($topITSkills)); ?>,
                datasets: [{
                    label: 'Frequency Extracted',
                    data: <?= json_encode(array_values($topITSkills)); ?>,
                    backgroundColor: '#0F2854',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</body>
</html>