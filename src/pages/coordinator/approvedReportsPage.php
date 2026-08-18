<!-- src/pages/coordinator/approvedReportsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved WARs - OJT Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <!-- Main Workspace -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Page Header Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Approved Student Weekly Reports</h1>
                        <p class="text-slate-500 text-xs mt-0.5">View Weekly Activity Reports (WARs) verified and approved by company supervisors.</p>
                    </div>

                    <!-- Top Action Buttons -->
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 font-bold rounded-full border border-emerald-200 text-[11px] flex items-center gap-1.5 shadow-2xs">
                            <span>✓</span> Approved Reports Only
                        </span>

                        <!-- DOWNLOAD SUMMARY DROPDOWN MENU -->
                        <div class="relative inline-block text-left">
                            <button type="button" onclick="toggleExportMenu()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                <span>📥</span> Download All Reports Summary ▾
                            </button>

                            <div id="exportMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-slate-200 z-50 text-xs overflow-hidden">
                                <div class="p-2 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    Select Format
                                </div>
                                <a href="export_summary.php?format=pdf" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 font-semibold transition-colors">
                                    <span class="text-rose-500 font-bold">📄</span> PDF Document (.pdf)
                                </a>
                                <a href="export_summary.php?format=csv" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 font-semibold transition-colors">
                                    <span class="text-emerald-600 font-bold">📊</span> CSV / Excel Spreadsheet (.csv)
                                </a>
                                <a href="export_summary.php?format=word" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 font-semibold transition-colors">
                                    <span class="text-blue-600 font-bold">📝</span> Word Document (.docx)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search Toolbar -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4 text-xs">
                    
                    <form method="GET" action="approved_reports.php" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        
                        <!-- Search Box -->
                        <div class="relative min-w-[220px]">
                            <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                            <input 
                                type="text" 
                                name="search" 
                                value="<?= htmlspecialchars($searchQuery ?? ''); ?>" 
                                placeholder="Search student name or ID..." 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#0F2854]"
                            >
                        </div>

                        <!-- Filter Week -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Week:</label>
                            <select name="week" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= ($selectedWeek ?? 'all') === 'all' ? 'selected' : ''; ?>>All Weeks (1 - 12)</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($selectedWeek ?? '') == $i ? 'selected' : ''; ?>>Week <?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Filter Host Office -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Host Office:</label>
                            <select name="company_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Host Offices</option>
                                <?php foreach ($companiesList as $comp): ?>
                                    <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($comp['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all cursor-pointer">Filter</button>

                        <?php if (!empty($searchQuery) || ($selectedWeek ?? 'all') !== 'all' || ($selectedCompany ?? 'all') !== 'all'): ?>
                            <a href="approved_reports.php" class="text-slate-400 hover:text-slate-600 font-semibold px-1 self-center">Reset</a>
                        <?php endif; ?>
                    </form>

                    <div class="text-slate-400 font-semibold text-[11px]">
                        Total Reports: <span class="text-slate-800 font-extrabold"><?= count($filteredWars ?? []); ?></span>
                    </div>
                </div>

                <!-- Reports Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                    <th class="py-3.5 px-5">Week</th>
                                    <th class="py-3.5 px-5">Student Intern</th>
                                    <th class="py-3.5 px-5">Host Office / Supervisor</th>
                                    <th class="py-3.5 px-5">Accomplishment Excerpt</th>
                                    <th class="py-3.5 px-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php if (empty($filteredWars)): ?>
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                            <div class="text-2xl mb-1">📄</div>
                                            <p class="font-semibold text-slate-600">No approved weekly reports found.</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Ensure your supervisor account has approved submitted reports (`status = 'approved'`).</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($filteredWars as $war): ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            
                                            <!-- Week Badge -->
                                            <td class="py-3.5 px-5 font-bold whitespace-nowrap">
                                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-[#0F2854] border border-blue-100 font-bold text-xs inline-block shadow-2xs">
                                                    Week <?= htmlspecialchars($war['week_number']); ?>
                                                </span>
                                            </td>

                                            <!-- Student Name & ID -->
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <p class="font-bold text-slate-900"><?= htmlspecialchars($war['student_name']); ?></p>
                                                <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($war['student_number'] ?? 'N/A'); ?></p>
                                            </td>

                                            <!-- Host Office & Supervisor -->
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($war['company_name'] ?? 'Host Agency'); ?></p>
                                                <p class="text-[11px] text-slate-400">
                                                    Verified by <?= !empty($war['supervisor_name']) ? htmlspecialchars($war['supervisor_name']) : 'Assigned Supervisor'; ?>
                                                </p>
                                            </td>

                                            <!-- Accomplishment Excerpt (OCR / Summary) -->
                                            <td class="py-3.5 px-5 max-w-xs">
                                                <p class="text-slate-600 line-clamp-2 text-[11px]">
                                                    <?= !empty($war['ocr_activities']) ? htmlspecialchars(substr($war['ocr_activities'], 0, 120)) . '...' : '<span class="italic text-slate-400">Accomplishment report attached.</span>'; ?>
                                                </p>
                                            </td>

                                            <!-- View Report Button -->
                                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                                <a href="view_report.php?id=<?= $war['id']; ?>" class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-semibold rounded-xl transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                                    <span>View Report →</span>
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Export Menu Toggle Script -->
    <script>
        function toggleExportMenu() {
            const menu = document.getElementById('exportMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        window.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            const btn = e.target.closest('button');
            if (menu && !menu.contains(e.target) && (!btn || !btn.textContent.includes('Download All Reports Summary'))) {
                menu.classList.add('hidden');
            }
        });
    </script>
<?php include __DIR__ . '/../../components/password_change_popup.php'; ?>
</body>
</html>