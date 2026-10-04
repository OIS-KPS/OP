<!-- src/pages/coordinator/approvedReportsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');

// Base query string without section restriction for the "All" export option
$exportParamsAll = http_build_query([
    'week'       => $selectedWeek ?? 'all',
    'company_id' => $selectedCompany ?? 'all',
    'section'    => 'all',
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Workspace -->
            <main class="p-8 max-w-[1500px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Unified Reports Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
                    <!-- 1. Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                        <div>
                            <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">Weekly Accomplishment Reports</h1>
                            <p class="text-xs font-semibold text-slate-600 mt-1">
                                Consolidated student submissions and analytics summary.
                            </p>
                        </div>

                        <!-- Header Actions -->
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <?= count($filteredStudents ?? []); ?> Total <?= count($filteredStudents ?? []) === 1 ? 'Student' : 'Students'; ?>
                            </span>

                            <!-- Download Summary Menu with Dynamic Section Options -->
                            <div class="relative inline-block text-left">
                                <button type="button" onclick="toggleExportMenu()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <span>Export Reports</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>

                                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-slate-300 z-50 text-xs overflow-hidden p-1.5">
                                    <div class="px-3 py-1.5 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                        Export Options
                                    </div>
                                    
                                    <!-- All Sections Export -->
                                    <a href="export_summary.php?format=csv&<?= $exportParamsAll; ?>" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span>Export All Sections (CSV)</span>
                                    </a>

                                    <div class="my-1 border-t border-slate-200"></div>
                                    <div class="px-3 py-1 text-[10px] font-black text-slate-400 uppercase tracking-wider">Export by Section</div>

                                    <!-- Dynamic Section Export Links -->
                                    <?php foreach ($activeSections as $sec): 
                                        $secParams = http_build_query([
                                            'week'       => $selectedWeek ?? 'all',
                                            'company_id' => $selectedCompany ?? 'all',
                                            'section'    => $sec,
                                            'search'     => $searchQuery ?? ''
                                        ]);
                                    ?>
                                        <a href="export_summary.php?format=csv&<?= $secParams; ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                            <span>Section <?= htmlspecialchars($sec); ?> (CSV)</span>
                                        </a>
                                    <?php endforeach; ?>

                                    <div class="my-1 border-t border-slate-200"></div>
                                    <a href="export_summary.php?format=pdf&<?= $exportParamsAll; ?>" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-xl text-rose-700 hover:bg-rose-50 font-bold transition-colors">
                                        <span>Print / PDF Document</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Tabs Bar (Includes All Sections & Dynamic Section Tabs) -->
                    <div class="px-6 pt-4 bg-white border-b border-slate-200/70 flex items-center gap-2 overflow-x-auto">
                        <span class="text-xs font-bold text-slate-500 mr-2 uppercase tracking-wider">Sections:</span>
                        
                        <a href="approved_reports.php?section=all&company_id=<?= htmlspecialchars($selectedCompany); ?>&search=<?= htmlspecialchars($searchQuery); ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all border <?= ($selectedSection === 'all') ? 'bg-[#0F2854] text-white border-[#0F2854] shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300'; ?>">
                            All Sections
                        </a>

                        <?php foreach ($activeSections as $sec): 
                            $isTabActive = ($selectedSection === $sec);
                        ?>
                            <a href="approved_reports.php?section=<?= htmlspecialchars($sec); ?>&company_id=<?= htmlspecialchars($selectedCompany); ?>&search=<?= htmlspecialchars($searchQuery); ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all border <?= $isTabActive ? 'bg-[#0F2854] text-white border-[#0F2854] shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300'; ?>">
                                Section <?= htmlspecialchars($sec); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- 2. Integrated Filter & Live Search Toolbar -->
                    <div class="p-4 border-b border-slate-200/70 bg-white">
                        <form id="filterForm" method="GET" action="approved_reports.php" class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                            <input type="hidden" name="section" value="<?= htmlspecialchars($selectedSection); ?>">
                            
                            <!-- Live Search Input -->
                            <div class="relative flex-1 w-full">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input 
                                    type="text" 
                                    id="searchInput"
                                    name="search" 
                                    value="<?= htmlspecialchars($searchQuery ?? ''); ?>" 
                                    placeholder="Search by student name or ID number..." 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                >
                            </div>

                            <!-- Filter Host Company -->
                            <div class="w-full md:w-56">
                                <select name="company_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer">
                                    <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Companies</option>
                                    <?php foreach ($companiesList as $comp): ?>
                                        <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($comp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if (!empty($searchQuery) || ($selectedCompany ?? 'all') !== 'all'): ?>
                                <div class="w-full md:w-auto">
                                    <a href="approved_reports.php?section=<?= htmlspecialchars($selectedSection); ?>" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl border border-slate-300 transition-colors inline-block text-center whitespace-nowrap">
                                        Reset
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- 3. Students Summary Table -->
                    <?php if (!empty($filteredStudents)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Section</th>
                                        <th class="py-4 px-6">Host Company &amp; Supervisor</th>
                                        <th class="py-4 px-6">Approved Reports</th>
                                        <th class="py-4 px-6">IT vs Clerical Ratio</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($filteredStudents as $student): ?>
                                        <tr class="hover:bg-slate-50 transition-colors student-row">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($student['student_avatar'])): ?>
                                                            <img src="<?= htmlspecialchars($student['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($student['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-extrabold text-slate-950 text-sm intern-name"><?= htmlspecialchars($student['student_name']); ?></p>
                                                        <p class="text-[11px] text-slate-600 font-semibold intern-id">ID: <?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Section Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                    Sec <?= htmlspecialchars($student['section'] ?? 'A'); ?>
                                                </span>
                                            </td>

                                            <!-- Company & Supervisor Details -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <p class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($student['company_name'] ?? 'Host Company'); ?></p>
                                                <p class="text-[11px] text-slate-600 font-medium mt-0.5">
                                                    Supervisor: <span class="font-bold text-slate-800"><?= !empty($student['supervisor_name']) ? htmlspecialchars($student['supervisor_name']) : 'Unassigned'; ?></span>
                                                </p>
                                            </td>

                                            <!-- Reports Count Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-[#0F2854] border border-blue-200 font-bold text-xs">
                                                    <?= (int)$student['approved_reports_count']; ?> Submitted / Approved
                                                </span>
                                            </td>

                                            <!-- IT vs Clerical Ratio Progress Bar -->
                                            <td class="py-4 px-6 align-middle min-w-[180px]">
                                                <?php if ($student['total_entities'] > 0): ?>
                                                    <div class="space-y-1">
                                                        <div class="flex items-center justify-between text-[10px] font-bold">
                                                            <span class="text-[#0F2854]">IT: <?= $student['it_percentage']; ?>%</span>
                                                            <span class="text-rose-700">Clerical: <?= $student['clerical_percentage']; ?>%</span>
                                                        </div>
                                                        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden flex shadow-inner">
                                                            <div class="bg-[#0F2854] h-full" style="width: <?= $student['it_percentage']; ?>%"></div>
                                                            <div class="bg-rose-500 h-full" style="width: <?= $student['clerical_percentage']; ?>%"></div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-[11px] font-semibold text-slate-400 italic">No extracted entities</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button (Reports Link) -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap align-middle">
                                                <a href="view_report.php?student_id=<?= (int)$student['student_id']; ?>" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-2xs transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                                    <span>Reports</span>
                                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📄
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No students found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">There are no students matching your criteria.</p>
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
                const rows = document.querySelectorAll('.student-row');

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