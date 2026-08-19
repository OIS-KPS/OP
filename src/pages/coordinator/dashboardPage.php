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
                                <p class="text-[11px] text-slate-400">Percentage of IT-related tasks per company • <span class="text-blue-600 font-semibold cursor-pointer">Click a bar to filter entities</span></p>
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">High ≥ 80%</span>
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Mod 60–79%</span>
                                <span class="px-2 py-0.5 rounded-md bg-red-100 text-red-900 border border-red-300">Low &lt; 60%</span>
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
                                <span class="font-bold text-slate-700">89.7%</span>
                            </div>
                            <div class="flex items-center justify-between font-semibold text-slate-700 text-[11px]">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Clerical / Non-IT</span>
                                <span class="font-bold text-slate-700">10.3%</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 3. Entity Frequency Horizontal Bar Chart Section -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs bg-white">
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 tracking-tight">Entity Frequency Analysis</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Frequency breakdown grouped by category</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Classification Color Legends -->
                            <div class="flex items-center gap-3 text-[11px] font-semibold mr-1">
                                <span class="flex items-center gap-1.5 text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#0F2854]"></span> Technical
                                </span>
                                <span class="flex items-center gap-1.5 text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Clerical
                                </span>
                            </div>

                            <!-- Clean Dropdown & Date Filters -->
                            <div class="flex flex-wrap items-center gap-2">
                                <select id="companyFilter" onchange="renderEntityChart()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium focus:outline-none focus:border-[#0F2854]">
                                    <option value="all">All Companies</option>
                                    <?php foreach ($companyPerformance as $cp): ?>
                                        <option value="<?= htmlspecialchars(strtolower($cp['name'])); ?>"><?= htmlspecialchars($cp['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select id="typeFilter" onchange="renderEntityChart()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium focus:outline-none focus:border-[#0F2854]">
                                    <option value="all">All Types</option>
                                    <option value="technical">Technical</option>
                                    <option value="clerical">Clerical</option>
                                </select>

                                <select id="catFilter" onchange="renderEntityChart()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium focus:outline-none focus:border-[#0F2854]">
                                    <option value="all">All Categories</option>
                                    <?php foreach (array_keys($categoryCounts) as $cat): ?>
                                        <option value="<?= htmlspecialchars(strtolower($cat)); ?>"><?= htmlspecialchars($cat); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="flex items-center gap-1">
                                    <input type="date" id="dateFilter" onchange="renderEntityChart()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium">
                                    <button type="button" onclick="clearDateFilter()" title="Clear date filter" class="text-slate-400 hover:text-slate-600 p-1.5 text-xs font-bold">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Unified Chart Container -->
                    <div id="entityChartWrapper" class="p-6">
                        <div id="chartContainer" class="relative w-full" style="min-height: 280px;">
                            <canvas id="entityFrequencyChart"></canvas>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="entityEmptyState" class="hidden p-12 text-center">
                        <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center mx-auto mb-2 text-base font-bold">🔍</div>
                        <h3 class="text-xs font-bold text-slate-800">No matching entities found</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Try adjusting your filters or date range.</p>
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
        // Bar Chart (IT Task Performance by Company)
        const companies = <?= json_encode($companyPerformance); ?>;
        const ctxBar = document.getElementById('companyTaskBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: companies.map(c => c.name),
                datasets: [{
                    label: 'IT Task %',
                    data: companies.map(c => Number(c.percentage)),
                    backgroundColor: companies.map(c => c.percentage >= 80 ? '#059669' : (c.percentage >= 60 ? '#D97706' : '#991B1B')),
                    hoverBackgroundColor: companies.map(c => c.percentage >= 80 ? '#047857' : (c.percentage >= 60 ? '#B45309' : '#7F1D1D')),
                    borderRadius: 6,
                    barThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onHover: (event, chartElement) => {
                    const target = event.native ? event.native.target : (event.chart ? event.chart.canvas : null);
                    if (target) {
                        target.style.cursor = chartElement && chartElement.length > 0 ? 'pointer' : 'default';
                    }
                },
                onClick: (event, elements) => {
                    if (elements && elements.length > 0) {
                        const clickedIndex = elements[0].index;
                        const clickedCompany = companies[clickedIndex];
                        const companySelect = document.getElementById('companyFilter');
                        if (!companySelect || !clickedCompany) return;

                        const targetValue = clickedCompany.name.toLowerCase();
                        // Toggle filter: if already selected, reset to 'all'; otherwise select clicked company
                        if (companySelect.value === targetValue) {
                            companySelect.value = 'all';
                        } else {
                            companySelect.value = targetValue;
                        }

                        // Re-render the Entity Frequency Chart with the new company filter
                        renderEntityChart();

                        // Smoothly scroll down to the Entity Frequency chart
                        const entityWrapper = document.getElementById('entityChartWrapper');
                        if (entityWrapper) {
                            entityWrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y !== null ? context.parsed.y : context.raw;
                                const company = companies[context.dataIndex];
                                if (company && company.level) {
                                    return [
                                        ` ${label}: ${value}%`,
                                        ` Level: ${company.level}`
                                    ];
                                }
                                return ` ${label}: ${value}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#334155' }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: v => v + '%',
                            font: { family: 'Inter', size: 11 },
                            color: '#334155'
                        },
                        grid: { color: '#F1F5F9' }
                    }
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

        // 3. Entity Frequency Horizontal Bar Chart (Unified Single Chart with Category Background & Label Indicators)
        const rawEntitiesData = <?= json_encode($entitiesData); ?>;
        let entityChartInstance = null;

        function clearDateFilter() {
            document.getElementById('dateFilter').value = '';
            renderEntityChart();
        }

        // Custom Chart.js plugin to draw background bands and category indicator labels for each category group
        const categoryGroupIndicatorPlugin = {
            id: 'categoryGroupIndicator',
            beforeDraw(chart) {
                const { ctx, chartArea, scales: { y } } = chart;
                const items = chart.config._entityItems;
                if (!items || items.length === 0 || !y || !chartArea) return;

                const { top, bottom } = chartArea;

                // Identify contiguous category groups
                const groups = [];
                let currentGroup = null;

                items.forEach((item, index) => {
                    if (!currentGroup || currentGroup.category !== item.category) {
                        if (currentGroup) groups.push(currentGroup);
                        currentGroup = {
                            category: item.category,
                            startIndex: index,
                            endIndex: index,
                            totalFreq: item.frequency
                        };
                    } else {
                        currentGroup.endIndex = index;
                        currentGroup.totalFreq += item.frequency;
                    }
                });
                if (currentGroup) groups.push(currentGroup);

                ctx.save();
                const step = items.length > 1 ? (bottom - top) / items.length : bottom - top;
                const halfStep = step / 2;

                groups.forEach((group, gIdx) => {
                    const topY = y.getPixelForValue(group.startIndex) - halfStep + 2;
                    const bottomY = y.getPixelForValue(group.endIndex) + halfStep - 2;
                    const groupHeight = Math.max(bottomY - topY, step);

                    // 1. Draw subtle alternating category background strip BEFORE any text or axes are rendered
                    const isEven = gIdx % 2 === 0;
                    ctx.fillStyle = isEven ? '#F8FAFC' : '#F1F5F9';
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(4, topY, chart.width - 8, groupHeight, 8);
                    } else {
                        ctx.rect(4, topY, chart.width - 8, groupHeight);
                    }
                    ctx.fill();

                    // 2. Draw left vertical accent line for the category group
                    ctx.fillStyle = '#0F2854';
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(4, topY + 4, 3, groupHeight - 8, 2);
                    } else {
                        ctx.rect(4, topY + 4, 3, groupHeight - 8);
                    }
                    ctx.fill();
                });
                ctx.restore();
            },
            afterDraw(chart) {
                const { ctx, chartArea, scales: { y } } = chart;
                const items = chart.config._entityItems;
                if (!items || items.length === 0 || !y || !chartArea) return;

                const { top, bottom } = chartArea;

                const groups = [];
                let currentGroup = null;

                items.forEach((item, index) => {
                    if (!currentGroup || currentGroup.category !== item.category) {
                        if (currentGroup) groups.push(currentGroup);
                        currentGroup = {
                            category: item.category,
                            startIndex: index,
                            endIndex: index,
                            totalFreq: item.frequency
                        };
                    } else {
                        currentGroup.endIndex = index;
                        currentGroup.totalFreq += item.frequency;
                    }
                });
                if (currentGroup) groups.push(currentGroup);

                ctx.save();
                const step = items.length > 1 ? (bottom - top) / items.length : bottom - top;
                const halfStep = step / 2;

                groups.forEach((group) => {
                    const topY = y.getPixelForValue(group.startIndex) - halfStep + 2;

                    // Draw Category Indicator Badge in top-right of group band
                    const badgeText = `📁 ${group.category} (${group.totalFreq}x)`;
                    ctx.font = '600 10px Inter, sans-serif';
                    const textMetrics = ctx.measureText(badgeText);
                    const badgeWidth = textMetrics.width + 16;
                    const badgeHeight = 20;
                    const badgeX = chart.width - badgeWidth - 14;
                    const badgeY = topY + 6;

                    // Pill background
                    ctx.fillStyle = '#FFFFFF';
                    ctx.strokeStyle = '#CBD5E1';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(badgeX, badgeY, badgeWidth, badgeHeight, 6);
                    } else {
                        ctx.rect(badgeX, badgeY, badgeWidth, badgeHeight);
                    }
                    ctx.fill();
                    ctx.stroke();

                    // Pill text
                    ctx.fillStyle = '#334155';
                    ctx.fillText(badgeText, badgeX + 8, badgeY + 14);
                });
                ctx.restore();
            }
        };

        function renderEntityChart() {
            if (entityChartInstance) {
                entityChartInstance.destroy();
                entityChartInstance = null;
            }

            const company = document.getElementById('companyFilter').value;
            const type = document.getElementById('typeFilter').value;
            const cat = document.getElementById('catFilter').value;
            const date = document.getElementById('dateFilter').value;

            // Filter raw data
            let filtered = rawEntitiesData.filter(item => {
                const itemComp = (item.company || '').toLowerCase();
                const matchComp = (company === 'all' || itemComp === company.toLowerCase());
                const matchType = (type === 'all' || item.classification.toLowerCase() === type.toLowerCase());
                const matchCat = (cat === 'all' || item.category.toLowerCase() === cat.toLowerCase());
                const matchDate = (!date || item.date === date);
                return matchComp && matchType && matchCat && matchDate;
            });

            // Group filtered items by category so contiguous categories stay together
            filtered.sort((a, b) => a.category.localeCompare(b.category));

            const wrapper = document.getElementById('entityChartWrapper');
            const emptyState = document.getElementById('entityEmptyState');
            const container = document.getElementById('chartContainer');

            if (filtered.length === 0) {
                wrapper.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            wrapper.classList.remove('hidden');
            emptyState.classList.add('hidden');

            // Dynamically size container height based on total horizontal bars
            const calculatedHeight = Math.max(filtered.length * 56 + 40, 240);
            container.style.height = `${calculatedHeight}px`;

            const canvas = document.getElementById('entityFrequencyChart');
            const ctx = canvas.getContext('2d');

            const maxVal = Math.max(...filtered.map(i => i.frequency), 10);
            const suggestedMax = Math.ceil(maxVal * 1.15);

            entityChartInstance = new Chart(ctx, {
                type: 'bar',
                plugins: [categoryGroupIndicatorPlugin],
                data: {
                    labels: filtered.map(item => [item.entity, `${item.category} • ${item.company ? item.company + ' • ' : ''}${item.date}`]),
                    datasets: [{
                        label: 'Frequency',
                        data: filtered.map(item => item.frequency),
                        backgroundColor: filtered.map(item => item.classification.toLowerCase() === 'technical' ? '#0F2854' : '#F43F5E'),
                        hoverBackgroundColor: filtered.map(item => item.classification.toLowerCase() === 'technical' ? '#0F2854' : '#F43F5E'),
                        borderRadius: 6,
                        barThickness: 22
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { right: 20, left: 10 }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Inter', size: 11 },
                            callbacks: {
                                title: function(context) {
                                    const item = filtered[context[0].dataIndex];
                                    return item.entity;
                                },
                                label: function(context) {
                                    const item = filtered[context.dataIndex];
                                    return [
                                        ` Frequency: ${item.frequency}x`,
                                        ` Company: ${item.company || 'N/A'}`,
                                        ` Category: ${item.category}`,
                                        ` Classification: ${item.classification}`,
                                        ` Date: ${item.date}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            suggestedMax: suggestedMax,
                            grid: { color: '#F1F5F9' },
                            ticks: {
                                font: { family: 'Inter', size: 10 },
                                color: '#334155',
                                stepSize: 10,
                                callback: v => v + 'x'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Inter', size: 11, weight: '600' },
                                color: '#334155',
                                autoSkip: false
                            }
                        }
                    }
                }
            });

            // Store items on chart config for plugin access
            entityChartInstance.config._entityItems = filtered;
            entityChartInstance.update();
        }

        // Initial render on page load
        renderEntityChart();
    </script>

</body>
</html>