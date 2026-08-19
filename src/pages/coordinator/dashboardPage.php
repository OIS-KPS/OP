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
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-6 max-w-7xl w-full mx-auto space-y-6 flex-1">

                <!-- Top Header Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">CQI Analytics Dashboard</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Task analysis and skill extraction from approved reports</p>
                    </div>

                    <div class="flex items-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50/80 text-[#0F2854] font-bold rounded-xl border border-blue-200/60 text-xs shadow-2xs">
                            <span class="text-blue-600">✦</span>
                            <span>spaCy Confidence: <?= $spacyConfidence; ?>%</span>
                        </span>
                    </div>
                </div>

                <!-- 1. Metric Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    
                    <!-- Card 1: Total Students -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Students</p>
                        <div class="my-1.5">
                            <span class="text-2xl font-extrabold text-slate-900"><?= $totalStudents; ?></span>
                        </div>
                        <p class="text-[11px] text-slate-400">Enrolled interns</p>
                    </div>

                    <!-- Card 2: Evaluated Students -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Evaluated Students</p>
                        <div class="my-1.5">
                            <span class="text-2xl font-extrabold text-emerald-600"><?= $evaluatedStudents; ?></span>
                        </div>
                        <p class="text-[11px] text-slate-400">Completed evaluations</p>
                    </div>

                    <!-- Card 3: Overall IT Percentage -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Overall IT Percentage</p>
                        <div class="my-1.5">
                            <span class="text-2xl font-extrabold text-[#0F2854]"><?= $overallTechPct; ?>%</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Technical task ratio</p>
                    </div>

                    <!-- Card 4: Top Category (Clean Baseline Alignment) -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Top Category</p>
                        <div class="my-1.5 flex items-baseline justify-between gap-2">
                            <span class="text-lg font-extrabold text-indigo-700 truncate" title="<?= htmlspecialchars($topCategoryName); ?>">
                                <?= htmlspecialchars($topCategoryName); ?>
                            </span>
                            <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200/70 rounded-lg text-xs font-mono font-bold shrink-0">
                                <?= $topCategoryOccurrences; ?>x
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400">Most frequent skill</p>
                    </div>

                </div>

                <!-- 2. Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Bar Chart -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4 flex flex-col justify-between">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xs font-bold text-slate-900">Company IT Task Analysis</h2>
                                <p class="text-[11px] text-slate-400">Percentage of IT-related tasks per company</p>
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">High ≥ 80%</span>
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Mod 60–79%</span>
                                <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200">Low &lt; 60%</span>
                            </div>
                        </div>
                        <div class="h-60 relative">
                            <canvas id="companyTaskBarChart"></canvas>
                        </div>
                    </div>

                    <!-- Donut Chart -->
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4 flex flex-col justify-between">
                        <div>
                            <h2 class="text-xs font-bold text-slate-900">Activity Distribution</h2>
                            <p class="text-[11px] text-slate-400">Technical vs. clerical tasks</p>
                        </div>
                        <div class="relative flex items-center justify-center p-2 h-44">
                            <canvas id="activityDonutChart"></canvas>
                        </div>
                        <div class="space-y-1.5 text-xs border-t border-slate-100 pt-3">
                            <div class="flex items-center justify-between font-semibold text-slate-700 text-[11px]">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#0F2854]"></span> Technical / IT-Related</span>
                                <span class="font-bold text-[#0F2854]">89.7%</span>
                            </div>
                            <div class="flex items-center justify-between font-semibold text-slate-700 text-[11px]">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Clerical / Non-IT</span>
                                <span class="font-bold text-rose-600">10.3%</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 3. Entity Frequency Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs bg-white">
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 tracking-tight">Entity Frequency Analysis</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Extracted tasks grouped by category and classified by NLP engine</p>
                        </div>

                        <!-- Clean Dropdown Filters -->
                        <div class="flex flex-wrap items-center gap-2">
                            <select id="typeFilter" onchange="filterEntities()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium focus:outline-none focus:border-[#0F2854]">
                                <option value="all">All Types</option>
                                <option value="technical">Technical</option>
                                <option value="clerical">Clerical</option>
                            </select>

                            <select id="catFilter" onchange="filterEntities()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium focus:outline-none focus:border-[#0F2854]">
                                <option value="all">All Categories</option>
                                <?php foreach (array_keys($categoryCounts) as $cat): ?>
                                    <option value="<?= htmlspecialchars(strtolower($cat)); ?>"><?= htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <input type="date" id="dateFilter" onchange="filterEntities()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                    <th class="py-3 px-6">Extracted Entity</th>
                                    <th class="py-3 px-6">Category</th>
                                    <th class="py-3 px-6 text-center">Frequency</th>
                                    <th class="py-3 px-6 text-center">Classification</th>
                                    <th class="py-3 px-6 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody id="entityTableBody" class="divide-y divide-slate-100 text-slate-700">
                                <?php foreach ($entitiesData as $row): ?>
                                    <tr class="entity-row hover:bg-slate-50/70 transition-colors" 
                                        data-type="<?= strtolower($row['classification']); ?>" 
                                        data-cat="<?= strtolower($row['category']); ?>" 
                                        data-date="<?= $row['date']; ?>">
                                        
                                        <!-- Entity Name -->
                                        <td class="py-3.5 px-6 font-bold text-slate-900">
                                            <?= htmlspecialchars($row['entity']); ?>
                                        </td>

                                        <!-- Category Badge -->
                                        <td class="py-3.5 px-6">
                                            <span class="inline-block px-2.5 py-0.5 bg-slate-100/80 text-slate-600 rounded-md font-semibold text-[11px]">
                                                <?= htmlspecialchars($row['category']); ?>
                                            </span>
                                        </td>

                                        <!-- Frequency Counter Badge (Centered) -->
                                        <td class="py-3.5 px-6 text-center">
                                            <span class="inline-block px-2.5 py-0.5 bg-blue-50 text-[#0F2854] border border-blue-200/60 rounded-lg text-xs font-mono font-bold">
                                                <?= $row['frequency']; ?>x
                                            </span>
                                        </td>

                                        <!-- Classification Badge (Centered) -->
                                        <td class="py-3.5 px-6 text-center">
                                            <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold border <?= $row['classification'] === 'Technical' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'; ?>">
                                                <?= htmlspecialchars($row['classification']); ?>
                                            </span>
                                        </td>

                                        <!-- Date (Aligned Right) -->
                                        <td class="py-3.5 px-6 text-right text-slate-500 text-[11px] font-mono">
                                            <?= htmlspecialchars($row['date']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. CQI Summary & Action Plan (Accent Highlight Card) -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 border-l-4 border-l-[#0F2854] shadow-xs space-y-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📋</span>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">CQI Summary & Action Plan</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1 text-slate-700">
                        <div class="space-y-2">
                            <p class="leading-relaxed">• <strong>ICS IT Dept</strong> has the highest IT task ratio at <strong class="text-emerald-700 font-bold">93.0%</strong>.</p>
                            <p class="leading-relaxed">• <strong>LGU Manolo Fortich</strong> recorded the lowest IT task ratio at <strong class="text-amber-700 font-bold">65.0%</strong>.</p>
                        </div>
                        <div class="space-y-2">
                            <p class="leading-relaxed">• <strong>"<?= htmlspecialchars($topCategoryName); ?>"</strong> is the most frequent category with <strong class="text-[#0F2854]"><?= $topCategoryOccurrences; ?> occurrences</strong>.</p>
                            <p class="leading-relaxed">• <strong class="text-rose-600">Action Plan:</strong> Coordinate with LGU Manolo Fortich to increase student tasks in IT infrastructure and software development.</p>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Bar Chart
        const companies = <?= json_encode($companyPerformance); ?>;
        const ctxBar = document.getElementById('companyTaskBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: companies.map(c => c.name),
                datasets: [{
                    label: 'IT Task %',
                    data: companies.map(c => c.percentage),
                    backgroundColor: companies.map(c => c.percentage >= 80 ? '#059669' : (c.percentage >= 60 ? '#D97706' : '#E11D48')),
                    borderRadius: 6,
                    barThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#F1F5F9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Donut Chart
        const ctxDonut = document.getElementById('activityDonutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Technical', 'Clerical'],
                datasets: [{
                    data: [89.7, 10.3],
                    backgroundColor: ['#0F2854', '#F43F5E'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });

        // Filter Rows
        function filterEntities() {
            const type = document.getElementById('typeFilter').value;
            const cat = document.getElementById('catFilter').value;
            const date = document.getElementById('dateFilter').value;
            const rows = document.querySelectorAll('.entity-row');

            rows.forEach(row => {
                const matchType = (type === 'all' || row.dataset.type === type);
                const matchCat = (cat === 'all' || row.dataset.cat === cat);
                const matchDate = (!date || row.dataset.date === date);

                row.style.display = (matchType && matchCat && matchDate) ? '' : 'none';
            });
        }
    </script>

</body>
</html>