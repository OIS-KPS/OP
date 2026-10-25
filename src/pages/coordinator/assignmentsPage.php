<!-- src/pages/coordinator/assignmentsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Assignments - OJT Portal</title>
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
            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Alert Messages -->
                <?php if (!empty($success)): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black">✓</span>
                            <span><?= htmlspecialchars($success); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 font-black hover:text-emerald-950 cursor-pointer">✕</button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="bg-rose-50 border border-rose-300 text-rose-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-700 font-black">✕</span>
                            <span><?= htmlspecialchars($error); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 font-black hover:text-rose-950 cursor-pointer">✕</button>
                    </div>
                <?php endif; ?>

                <!-- Unified Assignments Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
                    <!-- 1. Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                        <div>
                            <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">Student Placements</h1>
                            <p class="text-xs font-semibold text-slate-600 mt-1">
                                Manage placements for many interns at once.
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center gap-3 shrink-0">
                            <button type="button" onclick="openAssignModal()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer">
                                <span>Assign Placement</span>
                            </button>
                        </div>
                    </div>

                        <!-- 2. Integrated Filter Tabs & Live Search Toolbar -->
                        <div class="p-4 border-b border-slate-200/70 bg-white">

                            <!-- Status Filter Tabs -->
                            <div class="flex items-center gap-2 overflow-visible flex-wrap">
                                <!-- All Interns + Sections Dropdown -->
                                <div class="relative inline-block text-left">
                                    <div class="inline-flex items-center">
                                        <?php
                                        /*
                                         * Status tabs are links, not buttons. They used to be
                                         * <button onclick="filterByStatus(...)"> filtering in JS,
                                         * which could only ever see the rows on screen. Now that
                                         * the list is paginated and the filter is server-side, each
                                         * tab is just a URL -- so the filtered view is shareable and
                                         * survives a reload, and filterByStatus() could be deleted.
                                         *
                                         * PHP closures do NOT inherit the enclosing scope, so
                                         * these read their inputs through an explicit `use`.
                                         */
                                        $statusLink = static function (string $value) use ($selectedSection, $searchQuery): string {
                                            $params = ['section' => $selectedSection, 'status' => $value];
                                            if ($searchQuery !== '') {
                                                $params['search'] = $searchQuery;
                                            }
                                            return 'assignments.php?' . http_build_query($params);
                                        };
                                        // Course-code menu: the mirror image, so switching block
                                        // keeps the status tab and the search instead of resetting.
                                        $sectionLink = static function (string $value) use ($selectedStatus, $searchQuery): string {
                                            $params = ['section' => $value, 'status' => $selectedStatus];
                                            if ($searchQuery !== '') {
                                                $params['search'] = $searchQuery;
                                            }
                                            return 'assignments.php?' . http_build_query($params);
                                        };
                                        $isTab = static fn(string $value): bool => ($selectedStatus === $value);
                                        ?>
                                        <a href="<?= $statusLink('all'); ?>" id="allFilterTab" class="filter-tab px-3.5 py-1.5 text-xs font-bold inline-flex items-center gap-2 cursor-pointer <?= $isTab('all') ? 'bg-[#0F2854] text-white rounded-l-lg rounded-r-none border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 rounded-l-lg rounded-r-none border border-slate-300'; ?>">
                                            <span>All Interns<?= ($selectedSection ?? 'all') !== 'all' ? ' · ' . htmlspecialchars($selectedSection) : ''; ?></span>
                                            <span class="ml-0.5 px-2 py-0.5 rounded-full text-[10px] font-black <?= $isTab('all') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= (int)$totalFiltered; ?></span>
                                        </a>
                                        <button type="button" onclick="toggleSectionsMenu(event)" class="px-2 py-1.5 cursor-pointer rounded-r-lg rounded-l-none border border-l-0 <?= $isTab('all') ? 'border-[#0F2854] bg-[#0F2854] text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'; ?>" aria-label="Toggle sections">
                                            <svg id="sectionsMenuChevron" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                        </button>
                                    </div>

                                    <div id="sectionsMenu" class="hidden absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-slate-300 z-50 text-xs overflow-hidden p-1.5">
                                        <a href="<?= $sectionLink('all'); ?>" class="block px-3 py-2 rounded-xl font-bold transition-colors <?= ($selectedSection ?? 'all') === 'all' ? 'bg-[#0F2854] text-white' : 'text-slate-800 hover:bg-slate-100'; ?>">
                                            All Course Codes
                                        </a>
                                        <?php foreach ($activeSections as $sec): ?>
                                            <a href="<?= $sectionLink($sec); ?>" class="block px-3 py-2 rounded-xl font-bold transition-colors <?= ($selectedSection ?? '') === $sec ? 'bg-[#0F2854] text-white' : 'text-slate-800 hover:bg-slate-100'; ?>">
                                                <?= htmlspecialchars($sec); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <a href="<?= $statusLink('assigned'); ?>" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= $isTab('assigned') ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?> cursor-pointer">
                                    <span>Assigned</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300"><?= (int)$assignedTotal; ?></span>
                                </a>
                                <a href="<?= $statusLink('unassigned'); ?>" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= $isTab('unassigned') ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?> cursor-pointer">
                                    <span>Unassigned</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300"><?= (int)$unassignedTotal; ?></span>
                                </a>
                            </div>
                        </div>

                        <!-- Full-Width Search -->
                        <!-- A real GET form now. The input previously had no name attribute and
                             sat outside any form, so filtering happened entirely in JS and could
                             only ever see the rows on screen. The course code and status tab
                             ride along as hidden inputs so searching does not reset them. -->
                        <form id="assignmentFilterForm" method="GET" action="assignments.php" class="p-4 border-b border-slate-200/70 bg-white">
                            <input type="hidden" name="section" value="<?= htmlspecialchars($selectedSection ?? 'all'); ?>">
                            <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus ?? 'all'); ?>">
                            <div class="flex w-full items-center gap-2">
                              <div class="relative min-w-0 flex-1">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input
                                    type="text"
                                    id="assignmentSearchInput"
                                    name="search"
                                    value="<?= htmlspecialchars($searchQuery ?? ''); ?>"
                                    placeholder="Search all interns by name, ID, company or supervisor..."
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                >
                              </div>
                              <button type="submit" class="shrink-0 rounded-xl bg-[#0F2854] px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-300">Search</button>
                            </div>
                        </form>

                    <!-- 3. Placement Table -->
                    <?php if (!empty($students)): ?>
                        <!-- overflow-x-auto, not overflow-hidden: the previous wrapper clipped
                             anything wider than the card. -->
                        <div class="w-full overflow-x-auto">
                            <table class="w-full min-w-[900px] table-fixed text-left border-collapse text-xs">
                                <colgroup>
                                    <col class="w-[24%]">
                                    <col class="w-[9%]">
                                    <col class="w-[15%]">
                                    <col class="w-[17%]">
                                    <col class="w-[16%]">
                                    <col class="w-[19%]">
                                </colgroup>
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-4">Student Intern</th>
                                        <th class="py-4 px-4">Course Code</th>
                                        <th class="py-4 px-4">Status</th>
                                        <th class="py-4 px-4">Company</th>
                                        <th class="py-4 px-4">Supervisor</th>
                                        <th class="py-4 px-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($students as $s): 
                                        $hasCompany   = !empty($s['company_id']);
                                        $hasSupervisor = !empty($s['supervisor_id']);
                                        $hasPlacement = $hasCompany && $hasSupervisor;
                                        $rowStatus    = $hasPlacement ? 'assigned' : 'unassigned';
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            
                                            <!-- Student Info -->
                                            <td class="py-4 px-4 align-middle">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($s['avatar_url'])): ?>
                                                            <img src="<?= htmlspecialchars($s['avatar_url']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($s['name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="font-extrabold text-slate-950 text-sm break-words"><?= htmlspecialchars($s['name']); ?></p>
                                                        <p class="text-[11px] text-slate-600 font-semibold mt-0.5">ID: <?= htmlspecialchars($s['student_number'] ?? 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Course Code Badge -->
                                            <td class="py-4 px-4 whitespace-nowrap align-middle">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                    <?= htmlspecialchars($s['section'] ?? 'IT44A'); ?>
                                                </span>
                                            </td>

                                            <!-- Assignment Status -->
                                            <td class="py-4 px-4 align-middle">
                                                <?php if ($hasPlacement): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Assigned
                                                    </span>
                                                    <p class="text-[10px] text-slate-500 font-semibold mt-1">Company &amp; supervisor linked</p>
                                                <?php elseif ($hasCompany || $hasSupervisor): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-100/80 text-sky-900 border border-sky-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span> Partially Assigned
                                                    </span>
                                                    <p class="text-[10px] text-slate-500 font-semibold mt-1"><?= $hasCompany ? 'Missing supervisor' : 'Missing company'; ?></p>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Not Assigned
                                                    </span>
                                                    <p class="text-[10px] text-slate-500 font-semibold mt-1">Needs company &amp; supervisor</p>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Host Company -->
                                            <td class="py-4 px-4 align-middle">
                                                <?php if (!empty($s['company_name'])): ?>
                                                    <p class="font-bold text-slate-900 text-xs break-words"><?= htmlspecialchars($s['company_name']); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-medium mt-0.5 break-words"><?= htmlspecialchars($s['office_name'] ?? $s['company_dept'] ?? 'Main Office'); ?></p>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Unassigned
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Assigned Supervisor -->
                                            <td class="py-4 px-4 align-middle">
                                                <?php if (!empty($s['supervisor_name'])): ?>
                                                    <p class="font-bold text-slate-900 text-xs break-words"><?= htmlspecialchars($s['supervisor_name']); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-medium mt-0.5">Company Supervisor</p>
                                                <?php else: ?>
                                                    <span class="text-slate-500 text-xs font-semibold italic">Pending Assignment</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-4 text-right whitespace-nowrap align-middle">
                                                <button 
                                                    type="button"
                                                    onclick="quickAssign(<?= $s['id']; ?>, '<?= htmlspecialchars(addslashes($s['name']), ENT_QUOTES, 'UTF-8'); ?>', <?= intval($s['office_id'] ?? 0); ?>)" 
                                                    class="px-3.5 py-1.5 text-xs font-bold <?= $hasPlacement ? 'text-slate-800 bg-white hover:bg-slate-100 border border-slate-300' : 'text-white bg-[#0F2854] hover:bg-blue-900 border border-[#0F2854]'; ?> rounded-xl shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                                                    <span><?= $hasPlacement ? 'Edit Link' : 'Assign Now →' ?></span>
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                    <?php /* The old <tr id="noResultsRow"> row is gone. It was
                                             toggled by applyCombinedFilter() when the JS filter
                                             hid everything; the empty state is now rendered by
                                             PHP below. */ ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <?php if ($totalPages > 1):
                            $baseQuery = 'section=' . urlencode($selectedSection)
                                . '&status=' . urlencode($selectedStatus)
                                . '&search=' . urlencode($searchQuery ?? '');

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
                                        <a href="assignments.php?<?= $baseQuery; ?>&page=<?= $page - 1; ?>"
                                           class="px-3 py-1.5 text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl transition-colors inline-flex items-center gap-1 cursor-pointer">
                                            <span>&lsaquo;</span> Prev
                                        </a>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 text-xs font-bold text-slate-400 bg-white border border-slate-200 rounded-xl inline-flex items-center gap-1 opacity-60">
                                            <span>&lsaquo;</span> Prev
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <a href="assignments.php?<?= $baseQuery; ?>&page=<?= $page + 1; ?>"
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
                                👥
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No students found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">
                                <?php if (($selectedStatus ?? 'all') !== 'all' || ($searchQuery ?? '') !== '' || ($selectedSection ?? 'all') !== 'all'): ?>
                                    No intern matches the current status tab, course code or search term.
                                    <?php if (($selectedStatus ?? 'all') !== 'all'): ?>
                                        <a href="<?= $statusLink('all'); ?>" class="text-[#0F2854] font-bold hover:underline">Clear filters</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    There are no registered students under this course code.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- MODAL: LINK STUDENT TO COMPANY & SUPERVISOR -->
    <div id="assignModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-2xl w-full overflow-hidden p-6 space-y-4 relative my-auto">
            
            <button type="button" onclick="closeModal('assignModal')" class="absolute top-5 right-5 w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>

            <div class="border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Assign Student Placement</h3>
                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Tick every intern you need in one go, pick the office, and they are all linked at once.</p>
            </div>

            <form method="POST" action="assignments.php" class="space-y-4 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="assign_student" value="1">
                <input type="hidden" id="actionType" name="action_type" value="assign">

                <!-- Selected students are posted as student_ids[] from the right panel -->
                <div id="selectedIdInputs"></div>

                <!-- 1. Course Code Filter -->
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700 mb-1">Course Code</label>
                    <select id="courseCodeSelect" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        <?php foreach ($activeSections as $sec): ?>
                            <option value="<?= htmlspecialchars($sec); ?>" <?= ($selectedSection ?? 'all') === $sec ? 'selected' : ''; ?>><?= htmlspecialchars($sec); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Two-Panel Student Picker -->
                <div class="space-y-1">
                    <div class="grid grid-cols-2 gap-3">

                        <!-- LEFT PANEL: available students of the selected course code -->
                        <div class="border border-slate-300 rounded-xl overflow-hidden flex flex-col h-80">
                            <div class="px-3 py-2 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 shrink-0">
                                <span class="font-black text-slate-800 text-[11px] uppercase tracking-wider">Interns</span>
                                <span id="availableCount" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-300">0</span>
                            </div>

                            <!-- Panel-scoped search + placement status filter, side by side -->
                            <div class="px-2 py-2 border-b border-slate-200 bg-white flex items-center gap-2 shrink-0">
                                <div class="relative flex-1 min-w-0">
                                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                    <input
                                        type="text"
                                        id="pickerSearchInput"
                                        autocomplete="off"
                                        placeholder="Search name or ID..."
                                        class="w-full bg-slate-50 border border-slate-300 rounded-lg pl-8 pr-7 py-1.5 text-[11px] font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                    >
                                    <button type="button" id="pickerSearchClear" onclick="clearPickerSearch()" class="hidden absolute right-1.5 top-1/2 -translate-y-1/2 px-1 text-slate-400 hover:text-slate-700 font-black text-[11px] cursor-pointer" title="Clear search" aria-label="Clear search">✕</button>
                                </div>

                                <!-- Status filter: opens on the interns that still need a host -->
                                <select
                                    id="placementStatusFilter"
                                    class="w-28 shrink-0 bg-slate-50 border border-slate-300 rounded-lg px-2 py-1.5 text-[11px] font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer"
                                >
                                    <option value="unassigned" selected>Unassigned</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="all">All</option>
                                </select>
                            </div>

                            <!-- Fixed-height scroll area: the panel never grows, only this list scrolls -->
                            <div id="availableList" class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-1.5 space-y-1"></div>
                        </div>

                        <!-- RIGHT PANEL: students queued for placement -->
                        <div class="border border-[#0F2854] rounded-xl overflow-hidden flex flex-col h-80">
                            <div class="px-3 py-2 bg-[#0F2854] border-b border-[#0F2854] flex items-center justify-between gap-2 shrink-0">
                                <span class="font-black text-white text-[11px] uppercase tracking-wider">Selected</span>
                                <span id="selectedCount" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white">0</span>
                            </div>
                            <div id="selectedList" class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-1.5 space-y-1"></div>
                        </div>

                    </div>
                    <p class="text-[11px] text-slate-500">The Interns panel opens on the unassigned ones — switch the filter to <strong>Assigned</strong> or <strong>All</strong> to re-link somebody. Selections carry across course codes, so one batch can span several blocks.</p>
                </div>

                <!-- 3. Select Company Office -->
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700 mb-1">Company Office</label>
                    <select id="officeSelect" name="office_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                        <option value="" disabled selected>-- Choose Company Office --</option>
                        <?php
                        $lastCompany = null;
                        foreach ($offices as $off):
                            if ($lastCompany !== $off['company_name']):
                                if ($lastCompany !== null): ?></optgroup><?php endif; ?>
                                <optgroup label="<?= htmlspecialchars($off['company_name']); ?>">
                                <?php $lastCompany = $off['company_name']; ?>
                            <?php endif; ?>
                            <option value="<?= $off['id']; ?>"><?= htmlspecialchars($off['office_name']); ?></option>
                        <?php endforeach; ?>
                        <?php if ($lastCompany !== null): ?></optgroup><?php endif; ?>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Each office has exactly one supervisor; every intern ticked above is linked to that supervisor.</p>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-200/70">
                    <button type="button" id="unassignBtn" onclick="submitUnassign()" class="hidden text-rose-700 hover:text-rose-950 font-bold text-xs cursor-pointer">
                        Unlink Placement
                    </button>
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" onclick="closeModal('assignModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" id="assignSubmitBtn" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold transition-colors shadow-xs cursor-pointer">Save Placement</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        const allSupervisors = <?= json_encode($supervisors ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        /* ---------------------------------------------------------------
         * Two-panel placement picker
         *
         * Left panel  : active interns of the course code chosen in the
         *               dropdown, each with a checkbox.
         * Right panel : interns queued for placement, each with an ✕.
         * The right panel owns one hidden student_ids[] input per intern, so
         * the form posts exactly the queued selection.
         * ------------------------------------------------------------- */
        const roster = <?= json_encode($rosterStudents ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const selectedStudents = new Map();

        const courseCodeSelect  = document.getElementById('courseCodeSelect');
        const pickerSearchInput = document.getElementById('pickerSearchInput');
        const pickerSearchClear = document.getElementById('pickerSearchClear');
        const placementStatusFilter = document.getElementById('placementStatusFilter');
        const availableList     = document.getElementById('availableList');
        const selectedList     = document.getElementById('selectedList');
        const availableCount   = document.getElementById('availableCount');
        const selectedCount    = document.getElementById('selectedCount');
        const selectedIdInputs = document.getElementById('selectedIdInputs');

        function initials(name) {
            return (name || 'S').trim().charAt(0).toUpperCase();
        }

        // Roster values are rendered with innerHTML below, so every field is escaped.
        function esc(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function pickerRowMarkup(stu) {
            const statusTag = isPlaced(stu)
                ? '<span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200 font-black text-[9px] uppercase">Assigned</span>'
                : '<span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-200 font-black text-[9px] uppercase">Unassigned</span>';

            const avatar = stu.avatar_url
                ? '<img src="' + esc(stu.avatar_url) + '" class="w-full h-full object-cover" alt="">'
                : esc(initials(stu.name));

            return '' +
                '<div class="flex items-center gap-2 px-2 py-1.5 rounded-lg border border-slate-200 bg-white hover:border-slate-300 transition-colors">' +
                    '<input type="checkbox" class="picker-check h-4 w-4 accent-[#0F2854] shrink-0 cursor-pointer" value="' + stu.id + '">' +
                    '<span class="w-6 h-6 rounded-md bg-slate-100 text-[#0F2854] border border-slate-300 flex items-center justify-center font-black text-[10px] shrink-0 overflow-hidden">' + avatar + '</span>' +
                    '<span class="min-w-0 flex-1">' +
                        '<span class="block font-extrabold text-slate-950 text-[11px] leading-tight truncate">' + esc(stu.name) + '</span>' +
                        '<span class="block text-[10px] font-semibold text-slate-500 leading-tight truncate">ID: ' + esc(stu.student_number || 'N/A') + ' · ' + esc(stu.section) + '</span>' +
                    '</span>' +
                    statusTag +
                '</div>';
        }

        function emptyStateMarkup(message) {
            return '<p class="px-3 py-6 text-center text-[11px] font-semibold text-slate-400 italic">' + message + '</p>';
        }

        // An intern counts as assigned only when BOTH the company and its supervisor
// are linked, so a half-linked row still shows up under "Unassigned" as work
// left to do.
function isPlaced(stu) {
            return !!(stu.company_id && stu.supervisor_id);
        }

        function renderAvailable() {
            const course = courseCodeSelect ? courseCodeSelect.value : '';
            const query = pickerSearchInput ? pickerSearchInput.value.toLowerCase().trim() : '';
            const status = placementStatusFilter ? placementStatusFilter.value : 'unassigned';

            const pool = roster.filter(s => {
                if (s.section !== course || selectedStudents.has(s.id)) return false;

                if (status === 'unassigned' && isPlaced(s)) return false;
                if (status === 'assigned' && !isPlaced(s)) return false;

                if (!query) return true;
                // Match on name, student number and course code so a partial ID
                // or surname is enough to narrow the list.
                return (s.name || '').toLowerCase().includes(query)
                    || String(s.student_number || '').toLowerCase().includes(query)
                    || String(s.section || '').toLowerCase().includes(query);
            });

            const emptyMessage = query
                ? 'No intern in this course code matches "' + esc(query) + '".'
                : (status === 'unassigned'
                    ? 'Every intern in this course code is already assigned.'
                    : (status === 'assigned'
                        ? 'No assigned intern left in this course code.'
                        : 'No interns left in this course code.'));

            availableList.innerHTML = pool.map(pickerRowMarkup).join('')
                + (pool.length ? '' : emptyStateMarkup(emptyMessage));

            availableCount.textContent = pool.length;

            if (pickerSearchClear) {
                pickerSearchClear.classList.toggle('hidden', query === '');
            }

            availableList.querySelectorAll('.picker-check').forEach(box => {
                box.addEventListener('change', function () {
                    if (!this.checked) return;
                    const stu = roster.find(s => s.id === parseInt(this.value));
                    if (stu) selectedStudents.set(stu.id, stu);
                    renderAvailable();
                    renderSelected();
                });
            });
        }

        function clearPickerSearch() {
            if (pickerSearchInput) {
                pickerSearchInput.value = '';
                pickerSearchInput.focus();
            }
            renderAvailable();
        }

        function renderSelected() {
            const list = Array.from(selectedStudents.values());

            selectedList.innerHTML = (list.length ? list.map(stu => '' +
                '<div class="flex items-center gap-2 px-2 py-1.5 rounded-lg border border-blue-200 bg-blue-50/60">' +
                    '<span class="w-6 h-6 rounded-md bg-white text-[#0F2854] border border-slate-300 flex items-center justify-center font-black text-[10px] shrink-0 overflow-hidden">' +
                        (stu.avatar_url ? '<img src="' + esc(stu.avatar_url) + '" class="w-full h-full object-cover" alt="">' : esc(initials(stu.name))) +
                    '</span>' +
                    '<span class="min-w-0 flex-1">' +
                        '<span class="block font-extrabold text-slate-950 text-[11px] leading-tight truncate">' + esc(stu.name) + '</span>' +
                        '<span class="block text-[10px] font-semibold text-slate-500 leading-tight truncate">ID: ' + esc(stu.student_number || 'N/A') + ' · ' + esc(stu.section) + '</span>' +
                    '</span>' +
                    '<button type="button" class="picker-remove px-1.5 py-0.5 rounded-md text-rose-700 hover:bg-rose-100 border border-transparent hover:border-rose-200 font-black text-[11px] cursor-pointer" data-id="' + stu.id + '" title="Remove from selection" aria-label="Remove ' + esc(stu.name) + '">✕</button>' +
                '</div>'
            ).join('') : '')
                + (list.length ? '' : emptyStateMarkup('Tick an intern to add them here.'));

            selectedCount.textContent = list.length;

            // Mirror the selection into hidden inputs so the POST carries it
            selectedIdInputs.innerHTML = list
                .map(stu => '<input type="hidden" name="student_ids[]" value="' + stu.id + '">')
                .join('');

            const submitBtn = document.getElementById('assignSubmitBtn');
            const unlinkBtn  = document.getElementById('unassignBtn');
            if (submitBtn) {
                submitBtn.disabled = list.length === 0;
                submitBtn.classList.toggle('opacity-50', list.length === 0);
                submitBtn.classList.toggle('cursor-not-allowed', list.length === 0);
            }
            if (unlinkBtn) {
                // Unlinking only makes sense for interns that already have a placement
                unlinkBtn.classList.toggle('hidden', !list.some(s => s.company_id || s.supervisor_id));
            }

            selectedList.querySelectorAll('.picker-remove').forEach(btn => {
                btn.addEventListener('click', function () {
                    selectedStudents.delete(parseInt(this.dataset.id));
                    renderAvailable();
                    renderSelected();
                });
            });
        }

        if (courseCodeSelect) {
            courseCodeSelect.addEventListener('change', renderAvailable);
        }

        if (placementStatusFilter) {
            placementStatusFilter.addEventListener('change', renderAvailable);
        }

        if (pickerSearchInput) {
            pickerSearchInput.addEventListener('input', renderAvailable);

            // Escape clears the term; Enter must not submit the placement form
            pickerSearchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    e.stopPropagation();
                    clearPickerSearch();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function resetPicker() {
            selectedStudents.clear();
            if (pickerSearchInput) pickerSearchInput.value = '';
            if (pickerSearchClear) pickerSearchClear.classList.add('hidden');
            // Always start on the interns that still need a placement
            if (placementStatusFilter) placementStatusFilter.value = 'unassigned';
            document.getElementById('officeSelect').value = '';
            document.getElementById('actionType').value = 'assign';
            document.getElementById('unassignBtn').classList.add('hidden');
            renderAvailable();
            renderSelected();
        }

        function openAssignModal() {
            resetPicker();
            openModal('assignModal');
        }

        function filterSupervisorsByCompany(preselectedSupId = null) {
            const companyId = parseInt(document.getElementById('companySelect').value);
            const supSelect = document.getElementById('supervisorSelect');

            supSelect.innerHTML = '<option value="" disabled selected>-- Select Company Supervisor --</option>';

            const filtered = allSupervisors.filter(s => parseInt(s.company_id) === companyId);

            if (filtered.length > 0) {
                filtered.forEach(sup => {
                    const opt = document.createElement('option');
                    opt.value = sup.id;
                    opt.textContent = sup.name;
                    if (preselectedSupId && parseInt(sup.id) === preselectedSupId) {
                        opt.selected = true;
                    }
                    supSelect.appendChild(opt);
                });
                supSelect.disabled = false;
            } else {
                const opt = document.createElement('option');
                opt.value = "";
                opt.textContent = "No supervisors registered for this company yet";
                supSelect.appendChild(opt);
                supSelect.disabled = true;
            }
        }

        function quickAssign(studentId, studentName, officeId) {
            const stu = roster.find(s => s.id === parseInt(studentId));

            resetPicker();

            // Jump the course dropdown to the row's own course code so the left
            // panel lists that block, then drop the intern straight into the
            // right panel as the starting selection.
            if (stu && courseCodeSelect) {
                const option = Array.from(courseCodeSelect.options).find(o => o.value === stu.section);
                if (option) courseCodeSelect.value = stu.section;
                selectedStudents.set(stu.id, stu);
            }

            renderAvailable();
            renderSelected();

            if (officeId > 0) {
                document.getElementById('officeSelect').value = officeId;
                document.getElementById('unassignBtn').classList.remove('hidden');
            }

            openModal('assignModal');
        }

        function submitUnassign() {
            const count = selectedStudents.size;
            if (count === 0) {
                alert('Select at least one student first.');
                return;
            }
            if (confirm("Unlink the placement of " + count + " selected student(s)? This clears their company, office and supervisor.")) {
                document.getElementById('actionType').value = 'unassign';
                document.getElementById('officeSelect').removeAttribute('required');
                document.getElementById('assignModal').querySelector('form').submit();
            }
        }


        function toggleSectionsMenu(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('sectionsMenu');
            const chevron = document.getElementById('sectionsMenuChevron');
            if (menu) menu.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('rotate-180');
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('sectionsMenu');
            const chevronBtn = document.querySelector('button[aria-label="Toggle sections"]');
            if (menu && !menu.contains(e.target) && (!chevronBtn || !chevronBtn.contains(e.target))) {
                menu.classList.add('hidden');
                document.getElementById('sectionsMenuChevron')?.classList.remove('rotate-180');
            }
        });

    </script>
</body>
</html>
