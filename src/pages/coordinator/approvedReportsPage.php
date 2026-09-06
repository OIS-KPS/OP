<!-- src/pages/coordinator/approvedReportsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');

// Build query string for export button
$exportParams = http_build_query([
    'week'       => $selectedWeek ?? 'all',
    'company_id' => $selectedCompany ?? 'all',
    'section'    => $selectedSection ?? 'all',
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
<body class="bg-[#F8FAFC] text-slate-800 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Workspace -->
            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Unified Reports Container -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    
                    <!-- 1. Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug">Weekly Accomplishment Reports</h1>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">
                                Verified submissions and supervisor approvals in chronological order.
                            </p>
                        </div>

                        <!-- Header Actions -->
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <?= count($filteredWars ?? []); ?> Total <?= count($filteredWars ?? []) === 1 ? 'Report' : 'Reports'; ?>
                            </span>

                            <!-- Download Summary Menu -->
                            <div class="relative inline-block text-left">
                                <button type="button" onclick="toggleExportMenu()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-semibold rounded-xl text-xs transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <span>Export Reports</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>

                                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 text-xs overflow-hidden p-1.5">
                                    <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        File Format
                                    </div>
                                    <a href="export_summary.php?format=csv&<?= $exportParams; ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 font-medium transition-colors">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span>Excel CSV (.csv)</span>
                                    </a>
                                    <a href="export_summary.php?format=pdf&<?= $exportParams; ?>" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 font-medium transition-colors">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.127-.37-2.316-.37-3.529C6.35 5.51 10.02 2 14.55 2c4.53 0 8.2 3.51 8.2 8.3 0 1.213-.13 2.402-.37 3.529M3.5 18.25h17M8.5 22h7"/></svg>
                                        <span>Print / PDF Document</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Integrated Filter & Live Search Toolbar -->
                    <div class="p-4 border-b border-slate-100 bg-slate-50/40">
                        <form id="filterForm" method="GET" action="approved_reports.php" class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                            
                            <!-- Live Search Input -->
                            <div class="relative flex-1 w-full">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input 
                                    type="text" 
                                    id="searchInput"
                                    name="search" 
                                    value="<?= htmlspecialchars($searchQuery ?? ''); ?>" 
                                    placeholder="Search by student name or ID number..." 
                                    class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0F2854]"
                                >
                            </div>

                            <!-- Filter Section -->
                            <div class="w-full md:w-36">
                                <select name="section" onchange="this.form.submit()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                    <option value="all" <?= ($selectedSection ?? 'all') === 'all' ? 'selected' : ''; ?>>All Sections</option>
                                    <?php foreach ($activeSections as $sec): ?>
                                        <option value="<?= htmlspecialchars($sec); ?>" <?= ($selectedSection ?? '') === $sec ? 'selected' : ''; ?>>
                                            Section <?= htmlspecialchars($sec); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Week -->
                            <div class="w-full md:w-36">
                                <select name="week" onchange="this.form.submit()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                    <option value="all" <?= ($selectedWeek ?? 'all') === 'all' ? 'selected' : ''; ?>>All Weeks</option>
                                    <?php for ($i = 1; $i <= 16; $i++): ?>
                                        <option value="<?= $i; ?>" <?= ($selectedWeek ?? '') == $i ? 'selected' : ''; ?>>Week <?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <!-- Filter Host Company -->
                            <div class="w-full md:w-52">
                                <select name="company_id" onchange="this.form.submit()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                    <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Companies</option>
                                    <?php foreach ($companiesList as $comp): ?>
                                        <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($comp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if (!empty($searchQuery) || ($selectedSection ?? 'all') !== 'all' || ($selectedWeek ?? 'all') !== 'all' || ($selectedCompany ?? 'all') !== 'all'): ?>
                                <div class="w-full md:w-auto">
                                    <a href="approved_reports.php" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl border border-slate-200 transition-all inline-block text-center whitespace-nowrap">
                                        Reset
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- 3. Submissions Table -->
                    <?php if (!empty($filteredWars)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Section</th>
                                        <th class="py-4 px-6">Host Company & Supervisor</th>
                                        <th class="py-4 px-6">Timeline</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php foreach ($filteredWars as $war): 
                                        $submittedTime = !empty($war['submitted_at']) ? strtotime($war['submitted_at']) : null;
                                        $approvedTime  = !empty($war['approved_at']) ? strtotime($war['approved_at']) : (!empty($war['updated_at']) ? strtotime($war['updated_at']) : $submittedTime);
                                    ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors group war-row">
                                            
                                            <!-- Week Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] font-semibold text-xs flex items-center justify-center border border-slate-200/80 group-hover:bg-[#0F2854] group-hover:text-white group-hover:border-[#0F2854] transition-all duration-200">
                                                    W<?= htmlspecialchars($war['week_number']); ?>
                                                </div>
                                            </td>

                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-200/80 group-hover:border-[#0F2854] transition-colors">
                                                        <?php if (!empty($war['student_avatar'])): ?>
                                                            <img src="<?= htmlspecialchars($war['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($war['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800 text-sm intern-name"><?= htmlspecialchars($war['student_name']); ?></p>
                                                        <p class="text-[11px] text-slate-500 font-medium intern-id">ID: <?= htmlspecialchars($war['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($war['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Section Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200">
                                                    Sec <?= htmlspecialchars($war['section'] ?? 'A'); ?>
                                                </span>
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
                                                        <span class="text-slate-500 font-medium">Submitted:</span>
                                                        <span class="text-slate-700 font-semibold"><?= $submittedTime ? date("M d, Y \a\\t g:i A", $submittedTime) : '—'; ?></span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-[11px]">
                                                        <span class="text-slate-500 font-medium">Approved:</span>
                                                        <span class="text-emerald-700 font-semibold"><?= $approvedTime ? date("M d, Y \a\\t g:i A", $approvedTime) : '—'; ?></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <a href="view_report.php?id=<?= $war['id']; ?>" class="px-3.5 py-1.5 bg-slate-100 hover:bg-[#0F2854] hover:text-white text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 shadow-2xs transition-all inline-flex items-center gap-1.5 group/btn cursor-pointer">
                                                    <span>View Report</span>
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/btn:translate-x-0.5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-3">
                            <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto border border-slate-200">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">No approved reports found</h4>
                                <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto mt-0.5">There are no accomplishment reports matching your filter criteria.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- Scripts -->
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

        // Instant Client-Side Search
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.war-row');

                rows.forEach(function(row) {
                    const name = row.querySelector('.intern-name')?.textContent.toLowerCase() || '';
                    const id   = row.querySelector('.intern-id')?.textContent.toLowerCase() || '';
                    
                    if (name.includes(query) || id.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>