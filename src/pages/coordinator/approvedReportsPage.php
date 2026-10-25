<!-- src/pages/coordinator/approvedReportsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');

$selectedSection = $selectedSection ?? 'all';
$selectedCompany = $selectedCompany ?? 'all';
$searchQuery     = $searchQuery ?? '';
$courseCodeOptions = $courseCodeOptions ?? section_course_codes();

// Base query string without section restriction for the "All" export option
$exportParamsAll = http_build_query([
    'company_id' => $selectedCompany,
    'section'    => 'all',
    'search'     => $searchQuery
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
    <link rel="stylesheet" href="<?= BASE_PATH; ?>/public/css/style.css">
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
                            <span
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs"
                                title="Students matching the filters below, across every page"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                                <?= (int)$totalFiltered; ?> Total <?= (int)$totalFiltered === 1 ? 'Student' : 'Students'; ?>
                            </span>

                            <?php if ($selectedSection !== 'all'): ?>
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-50 text-[#0F2854] border border-[#0F2854]/30 text-xs font-bold">
                                    <span>Course Code:</span>
                                    <?= htmlspecialchars($selectedSection); ?>
                                </span>
                            <?php endif; ?>

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
                                        <span>Export All Course Codes (CSV)</span>
                                    </a>

                                    <div class="my-1 border-t border-slate-200"></div>

                                    <a href="export_summary.php?format=pdf&<?= $exportParamsAll; ?>" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors">
                                        <span>Print Summary / PDF</span>
                                    </a>

                                    <div class="my-1 border-t border-slate-200"></div>
                                    <div class="px-3 py-1 text-[10px] font-black text-slate-400 uppercase tracking-wider">Export by Course Code</div>

                                    <!-- Dynamic Course Code Export Links -->
                                    <?php foreach ($courseCodeOptions as $sec): 
                                        $secParams = http_build_query([
                                            'company_id' => $selectedCompany,
                                            'section'    => $sec,
                                            'search'     => $searchQuery
                                        ]);
                                    ?>
                                        <a href="export_summary.php?format=csv&<?= $secParams; ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition-colors">
<span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                            <span><?= htmlspecialchars($sec); ?> (CSV)</span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Integrated Filter & Live Search Toolbar -->
                    <div class="p-4 border-b border-slate-200/70 bg-white">
                        <form id="filterForm" method="GET" action="approved_reports.php" class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                            <!-- Live Search Input -->
                            <div class="flex w-full min-w-0 flex-1 items-center gap-2">
                              <div class="relative min-w-0 flex-1">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input 
                                    type="text" 
                                    id="searchInput"
                                    name="search" 
                                    value="<?= htmlspecialchars($searchQuery); ?>" 
                                    placeholder="Search all students by name or ID number..." 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                >
                              </div>
                              <button type="submit" class="shrink-0 rounded-xl bg-[#0F2854] px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-300">Search</button>
                            </div>

                            <!-- Filter by Course Code -->
                            <div class="w-full md:w-52">
                                <select 
                                    name="section" 
                                    onchange="this.form.submit()" 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer"
                                >
                                    <option value="all" <?= $selectedSection === 'all' ? 'selected' : ''; ?>>All Course Codes</option>
                                    <?php foreach ($courseCodeOptions as $sec): ?>
                                        <option value="<?= htmlspecialchars($sec); ?>" <?= $selectedSection === $sec ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($sec); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Host Company -->
                            <div class="w-full md:w-56">
                                <select name="company_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer">
                                    <option value="all" <?= $selectedCompany === 'all' ? 'selected' : ''; ?>>All Companies</option>
                                    <?php foreach ($companiesList as $comp): ?>
                                        <option value="<?= (int)$comp['id']; ?>" <?= $selectedCompany === (string)$comp['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($comp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            </form>
                    </div>

                    <!-- 3. Students Summary Table -->
                    <?php if (!empty($filteredStudents)): ?>
                        <!-- overflow-x-auto, not overflow-hidden: the previous wrapper clipped
                             anything wider than the card. The min-width keeps the columns
                             readable on narrow screens by scrolling rather than squashing. -->
                        <div class="w-full overflow-x-auto">
                            <table class="w-full min-w-[900px] table-fixed text-left border-collapse text-xs">
                                <colgroup>
                                    <col class="w-[24%]">
                                    <col class="w-[8%]">
                                    <col class="w-[25%]">
                                    <col class="w-[15%]">
                                    <col class="w-[16%]">
                                    <col class="w-[12%]">
                                </colgroup>
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-4">Student Intern</th>
                                        <th class="py-4 px-4">Course Code</th>
                                        <th class="py-4 px-4">Host Company &amp; Supervisor</th>
                                        <th class="py-4 px-4">Approved Reports</th>
                                        <th class="py-4 px-4">IT vs Clerical Ratio</th>
                                        <th class="py-4 px-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($filteredStudents as $student): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-4 align-middle">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($student['student_avatar'])): ?>
                                                            <img src="<?= htmlspecialchars($student['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($student['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="font-extrabold text-slate-950 text-sm break-words"><?= htmlspecialchars($student['student_name']); ?></p>
                                                        <p class="text-[11px] text-slate-600 font-semibold">ID: <?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Course Code Badge -->
                                            <td class="py-4 px-4 whitespace-nowrap align-middle">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                    <?= htmlspecialchars($student['section'] ?? 'IT44A'); ?>
                                                </span>
                                            </td>

                                            <!-- Company & Supervisor Details -->
                                            <td class="py-4 px-4 align-middle">
                                                <p class="font-bold text-slate-900 text-xs break-words"><?= htmlspecialchars($student['company_name'] ?? 'Host Company'); ?></p>
                                                <p class="text-[11px] text-slate-600 font-medium mt-0.5 break-words">
                                                    Office: <span class="font-bold text-slate-800"><?= htmlspecialchars($student['office_name'] ?? 'Unassigned'); ?></span>
                                                </p>
                                                <p class="text-[11px] text-slate-600 font-medium mt-0.5 break-words">
                                                    Supervisor: <span class="font-bold text-slate-800"><?= !empty($student['supervisor_name']) ? htmlspecialchars($student['supervisor_name']) : 'Unassigned'; ?></span>
                                                </p>
                                            </td>

                                            <!-- Reports Count Badge -->
                                            <td class="py-4 px-4 align-middle">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-blue-50 text-[#0F2854] border border-[#0F2854]/30 font-bold text-[10px] leading-tight max-w-full">
                                                    <span><?= (int)$student['approved_reports_count']; ?> Submitted / Approved</span>
                                                    <?php if (!empty($student['late_approved_count'])): ?>
                                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-orange-100 text-orange-900 border border-orange-300 shrink-0">
                                                            <?= (int)$student['late_approved_count']; ?> Late
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                            </td>

                                            <!-- IT vs Clerical Ratio Progress Bar -->
                                            <td class="py-4 px-4 align-middle">
                                                <?php if ($student['total_entities'] > 0): ?>
                                                    <div class="space-y-1">
                                                        <div class="flex items-center justify-between text-[10px] font-bold">
                                                            <span class="text-[#0F2854]">IT: <?= $student['it_percentage']; ?>%</span>
                                                            <span class="text-slate-600">Clerical: <?= $student['clerical_percentage']; ?>%</span>
                                                        </div>
                                                        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden flex shadow-inner">
                                                            <div class="bg-[#0F2854] h-full" style="width: <?= $student['it_percentage']; ?>%"></div>
                                                            <div class="bg-slate-400 h-full" style="width: <?= $student['clerical_percentage']; ?>%"></div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-[11px] font-semibold text-slate-400 italic">No extracted entities</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button (Reports Link) -->
                                            <td class="py-4 px-4 text-right whitespace-nowrap align-middle">
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


                        <!-- Pagination Footer -->
                        <?php if ($totalPages > 1):
                            // Every filter has to ride along, or the pager would drop the
                            // coordinator back onto an unfiltered list mid-search.
                            $baseQuery = 'search=' . urlencode($searchQuery)
                                . '&section=' . urlencode($selectedSection)
                                . '&company_id=' . urlencode($selectedCompany);

                            $firstShown = $totalFiltered > 0 ? (($page - 1) * $perPage) + 1 : 0;
                            $lastShown  = $totalFiltered > 0 ? min($page * $perPage, $totalFiltered) : 0;
                        ?>
                            <div class="px-4 py-3 border-t border-slate-200/70 bg-slate-50/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <p class="text-[11px] font-semibold text-slate-600">
                                    Showing <span class="font-black text-slate-900"><?= $firstShown; ?></span>&ndash;<span class="font-black text-slate-900"><?= $lastShown; ?></span>
                                    of <span class="font-black text-slate-900"><?= (int)$totalFiltered; ?></span>
                                    &middot; Page <?= (int)$page; ?> of <?= (int)$totalPages; ?>
                                </p>

                                <div class="flex items-center gap-2">
                                    <?php if ($page > 1): ?>
                                        <a href="approved_reports.php?<?= $baseQuery; ?>&page=<?= $page - 1; ?>"
                                           class="px-3 py-1.5 text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl transition-colors inline-flex items-center gap-1 cursor-pointer">
                                            <span>&lsaquo;</span> Prev
                                        </a>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 text-xs font-bold text-slate-400 bg-white border border-slate-200 rounded-xl inline-flex items-center gap-1 opacity-60">
                                            <span>&lsaquo;</span> Prev
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <a href="approved_reports.php?<?= $baseQuery; ?>&page=<?= $page + 1; ?>"
                                           class="px-3 py-1.5 text-xs font-bold text-white bg-[#0F2854] hover:bg-blue-900 border border-[#0F2854] rounded-xl transition-colors inline-flex items-center gap-1 cursor-pointer">
                                            Next <span>&rsaquo;</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 text-xs font-bold text-slate-400 bg-white border border-slate-200 rounded-xl inline-flex items-center gap-1 opacity-60">
                                            Next <span>&rsaquo;</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📄
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No students found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-md mx-auto">
                                <?php if ($selectedSection !== 'all' || $selectedCompany !== 'all' || $searchQuery !== ''): ?>
                                    No student with an approved report matches the selected
                                    <?= $selectedSection !== 'all' ? 'course code (' . htmlspecialchars($selectedSection) . ')' : '' ?>
                                    <?= $selectedCompany !== 'all' ? 'company' : '' ?>
                                    <?= $searchQuery !== '' ? 'search term' : '' ?>.
                                    Adjust the filters above to see more students.
                                <?php else: ?>
                                    No student has an approved weekly accomplishment report yet.
                                <?php endif; ?>
                            </p>
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

    </script>
</body>
</html>
