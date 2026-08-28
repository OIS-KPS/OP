<!-- src/pages/coordinator/approvedReportsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');

// Build query string for export button
$exportParams = http_build_query([
    'week'       => $selectedWeek ?? 'all',
    'company_id' => $selectedCompany ?? 'all',
    'search'     => $searchQuery ?? ''
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accomplishment Reports - Coordinator Portal</title>
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
<body class="bg-slate-100/70 text-slate-900 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Workspace -->
            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Header Actions Card -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-300 shadow-xs">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Student Accomplishment Reports</h1>
                        <p class="text-xs font-semibold text-slate-600 mt-1">
                            Weekly accomplishment reports reviewed and approved by company supervisors.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <?= count($filteredWars ?? []); ?> Total <?= count($filteredWars ?? []) === 1 ? 'Report' : 'Reports'; ?>
                        </span>

                        <!-- Download Summary Menu -->
                        <div class="relative inline-block text-left">
                            <button type="button" onclick="toggleExportMenu()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                                <span>Export Reports</span>
                                <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>

                            <div id="exportMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-300 z-50 text-xs overflow-hidden p-1.5">
                                <div class="px-3 py-1.5 text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">
                                    File Format
                                </div>
                                <a href="export_summary.php?format=csv&<?= $exportParams; ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors">
                                    <span class="text-emerald-700 font-extrabold text-sm">📊</span>
                                    <span>Excel CSV (.csv)</span>
                                </a>
                                <a href="export_summary.php?format=pdf&<?= $exportParams; ?>" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors">
                                    <span class="text-rose-700 font-extrabold text-sm">📄</span>
                                    <span>Print / PDF Document</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instant Filter & Search Toolbar (Auto-Filters on Change/Typing) -->
                <form id="filterForm" method="GET" action="approved_reports.php" class="bg-white p-4 rounded-2xl border border-slate-300 shadow-xs flex flex-col md:flex-row items-center gap-3 text-xs">
                    
                    <!-- Real-time Search Box -->
                    <div class="relative flex-1 w-full">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input 
                            type="text" 
                            id="searchInput"
                            name="search" 
                            value="<?= htmlspecialchars($searchQuery ?? ''); ?>" 
                            placeholder="Type student name or ID to filter instantly..." 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-all"
                        >
                    </div>

                    <!-- Filter Week (Instant Submit) -->
                    <div class="w-full md:w-44">
                        <select name="week" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                            <option value="all" <?= ($selectedWeek ?? 'all') === 'all' ? 'selected' : ''; ?>>All Weeks</option>
                            <?php for ($i = 1; $i <= 16; $i++): ?>
                                <option value="<?= $i; ?>" <?= ($selectedWeek ?? '') == $i ? 'selected' : ''; ?>>Week <?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Filter Host Company (Instant Submit) -->
                    <div class="w-full md:w-60">
                        <select name="company_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                            <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Companies</option>
                            <?php foreach ($companiesList as $comp): ?>
                                <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($comp['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!empty($searchQuery) || ($selectedWeek ?? 'all') !== 'all' || ($selectedCompany ?? 'all') !== 'all'): ?>
                        <div class="w-full md:w-auto">
                            <a href="approved_reports.php" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl border border-slate-300 transition-all inline-block text-center whitespace-nowrap">
                                Reset Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- Reports Table Card (High Contrast) -->
                <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-200 bg-slate-50/60">
                        <h3 class="text-xs font-extrabold text-slate-900 tracking-wider uppercase">Verified Submissions</h3>
                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Chronological record of approved weekly reports</p>
                    </div>

                    <?php if (!empty($filteredWars)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-extrabold">
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Host Company & Supervisor</th>
                                        <th class="py-4 px-6">Timeline</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-slate-900 font-medium">
                                    <?php foreach ($filteredWars as $war): 
                                        $submittedTime = !empty($war['submitted_at']) ? strtotime($war['submitted_at']) : null;
                                        $approvedTime  = !empty($war['approved_at']) ? strtotime($war['approved_at']) : (!empty($war['updated_at']) ? strtotime($war['updated_at']) : $submittedTime);
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors group">
                                            
                                            <!-- Week Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#0F2854] font-extrabold text-xs flex items-center justify-center border border-blue-200 group-hover:bg-[#0F2854] group-hover:text-white group-hover:border-[#0F2854] transition-all duration-200">
                                                    W<?= htmlspecialchars($war['week_number']); ?>
                                                </div>
                                            </td>

                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-extrabold text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($war['student_avatar'])): ?>
                                                            <img src="<?= htmlspecialchars($war['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($war['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($war['student_name']); ?></p>
                                                        <p class="text-[11px] text-slate-500 font-medium">ID: <?= htmlspecialchars($war['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($war['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Company & Supervisor Details -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <p class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($war['company_name'] ?? 'Host Company'); ?></p>
                                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                                    Supervisor: <span class="font-semibold text-slate-700"><?= !empty($war['supervisor_name']) ? htmlspecialchars($war['supervisor_name']) : 'Assigned Supervisor'; ?></span>
                                                </p>
                                            </td>

                                            <!-- Submitted & Approved Timeline -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5 text-[11px]">
                                                        <span class="text-slate-600 font-bold">Submitted:</span>
                                                        <span class="text-slate-900 font-extrabold"><?= $submittedTime ? date("M d, Y \a\\t g:i A", $submittedTime) : '—'; ?></span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-[11px]">
                                                        <span class="text-emerald-700 font-bold">Approved:</span>
                                                        <span class="text-emerald-800 font-extrabold"><?= $approvedTime ? date("M d, Y \a\\t g:i A", $approvedTime) : '—'; ?></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <a href="view_report.php?id=<?= $war['id']; ?>" class="px-4 py-2 bg-slate-100 hover:bg-[#0F2854] hover:text-white text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-all inline-flex items-center gap-1.5 group/btn cursor-pointer">
                                                    <span>View Report</span>
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/btn:translate-x-0.5 text-slate-700 group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-bold border border-slate-300">
                                📋
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">No approved reports found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">There are no accomplishment reports matching your search or filter.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <!-- Export Menu & Realtime Search Debounce Script -->
    <script>
        function toggleExportMenu() {
            const menu = document.getElementById('exportMenu');
            if (menu) menu.classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            const btn = e.target.closest('button');
            if (menu && !menu.contains(e.target) && (!btn || !btn.textContent.includes('Export Reports'))) {
                menu.classList.add('hidden');
            }
        });

        // Search Input Auto-Submit (Debounced by 500ms so the user can finish typing)
        const searchInput = document.getElementById('searchInput');
        const filterForm  = document.getElementById('filterForm');
        let searchTimeout = null;

        if (searchInput && filterForm) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterForm.submit();
                }, 500);
            });
        }
    </script>
</body>
</html>