<!-- src/pages/coordinator/usersPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Coordinator Portal</title>
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

                <!-- Main Integrated Directory Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
                    <!-- 1. Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                        <div>
                            <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">User Directory</h1>
                            <p class="text-xs font-semibold text-slate-600 mt-1">
                                Manage student intern, industry supervisor, partner company, and coordinator records.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2.5 shrink-0">
                            <?php
                            $importModalId = 'bulkImportModal';
                            if (($tab ?? 'students') === 'coordinators') {
                                $importModalId = 'bulkImportCoordinatorsModal';
                            } elseif (($tab ?? 'students') === 'companies') {
                                $importModalId = 'bulkImportCompaniesSupervisorsModal';
                            }
                            ?>
                            <button type="button" onclick="toggleModal('<?= $importModalId; ?>')" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 border border-slate-300 shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                <span>Import CSV</span>
                            </button>

                            <!-- + Add New Dropdown -->
                            <div class="relative inline-block text-left">
                                <button type="button" onclick="toggleAddMenu()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <span>+ Add New</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>

                                <div id="addMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-300 z-50 text-xs overflow-hidden p-1.5">
                                    <button type="button" onclick="toggleAddMenu(); toggleModal('addStudentModal');" class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors cursor-pointer">
                                        <span>Student Intern</span>
                                    </button>
                                    <button type="button" onclick="toggleAddMenu(); openAddSupervisorModal();" class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors cursor-pointer">
                                        <span>Company &amp; Supervisor</span>
                                    </button>
                                    <button type="button" onclick="toggleAddMenu(); toggleModal('addCoordinatorModal');" class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors cursor-pointer">
                                        <span>Coordinator</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Integrated Tabs, Section Selector & Live Search -->
                    <div class="p-4 border-b border-slate-200/70 flex flex-col md:flex-row items-center justify-between gap-4 bg-white">
                        
                        <!-- Navigation Tabs -->
                        <div class="flex items-center gap-2 overflow-visible flex-wrap w-full md:w-auto">
                            <!-- Students Dropdown -->
                            <div class="relative inline-block text-left">
                                <div class="inline-flex items-center gap-0 <?= ($tab ?? 'students') === 'students' ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 border border-slate-300'; ?> rounded-lg">
                                    <a href="users.php?tab=students" class="px-3.5 py-1.5 text-xs font-bold inline-flex items-center gap-2">
                                        <span>Students<?= ($tab ?? '') === 'students' && ($selectedSection ?? 'all') !== 'all' ? ' · Sec ' . htmlspecialchars($selectedSection) : ''; ?></span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? 'students') === 'students' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($students ?? []); ?></span>
                                    </a>
                                    <button type="button" onclick="toggleStudentsMenu(event)" class="px-2 py-1.5 border-l border-current/20 cursor-pointer" aria-label="Toggle sections">
                                        <svg id="studentsMenuChevron" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                    </button>
                                </div>

                                <div id="studentsMenu" class="hidden absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-slate-300 z-50 text-xs overflow-hidden p-1.5">
                                    <a href="users.php?tab=students&section=all" class="block px-3 py-2 rounded-xl font-bold transition-colors <?= ($selectedSection ?? 'all') === 'all' ? 'bg-[#0F2854] text-white' : 'text-slate-800 hover:bg-slate-100'; ?>">
                                        All Students
                                    </a>
                                    <?php foreach ($activeSections as $sec): ?>
                                        <a href="users.php?tab=students&section=<?= urlencode($sec); ?>" class="block px-3 py-2 rounded-xl font-bold transition-colors <?= ($selectedSection ?? '') === $sec ? 'bg-[#0F2854] text-white' : 'text-slate-800 hover:bg-slate-100'; ?>">
                                            Section <?= htmlspecialchars($sec); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <a href="users.php?tab=companies" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= ($tab ?? '') === 'companies' ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                <span>Companies</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? '') === 'companies' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($companies ?? []); ?></span>
                            </a>

                            <a href="users.php?tab=coordinators" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= ($tab ?? '') === 'coordinators' ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                <span>Coordinators</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? '') === 'coordinators' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($coordinators ?? []); ?></span>
                            </a>

                            <a href="users.php?tab=archived" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= ($tab ?? '') === 'archived' ? 'bg-rose-700 text-white border border-rose-700' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                <span>Archived</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? '') === 'archived' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($archivedUsers ?? []); ?></span>
                            </a>
                        </div>

                    </div>

                    <!-- (Section filters now live inside the Students dropdown) -->

                    <!-- Full-Width Search -->
                    <div class="p-4 border-b border-slate-200/70 bg-white">
                        <div class="relative w-full">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <input 
                                type="text" 
                                id="userSearchInput" 
                                placeholder="Search..." 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                            >
                        </div>
                    </div>

                    <!-- 3. Dynamic Data Tables -->

                    <!-- TAB 1: STUDENTS TABLE -->
                    <?php if (($tab ?? 'students') === 'students'): ?>
                        <?php if (!empty($students)): ?>
                            <div class="overflow-hidden">
                                <table class="w-full table-fixed text-left border-collapse text-xs">
                                    <colgroup>
                                        <col style="width: 30%;">
                                        <col style="width: 12%;">
                                        <col style="width: 40%;">
                                        <col style="width: 18%;">
                                    </colgroup>
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                            <th class="py-4 px-4">Student Intern</th>
                                            <th class="py-4 px-4">Section</th>
                                            <th class="py-4 px-4">Company &amp; Supervisor</th>
                                            <th class="py-4 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                        <?php foreach ($students as $s): ?>
                                            <tr class="hover:bg-slate-50 transition-colors user-row">
                                                <td class="py-4 px-4 align-middle">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                            <?php if (!empty($s['avatar_url'])): ?>
                                                                <img src="<?= htmlspecialchars($s['avatar_url']); ?>" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <?= strtoupper(substr($s['name'] ?? 'S', 0, 1)); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="font-extrabold text-slate-950 text-sm row-name break-words"><?= htmlspecialchars($s['name']); ?></p>
                                                            <p class="text-[11px] text-slate-600 font-semibold mt-0.5 row-sub">ID: <?= htmlspecialchars($s['student_number'] ?? 'N/A'); ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap align-middle">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                        Section <?= htmlspecialchars($s['section'] ?? 'A'); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4 align-middle">
                                                    <p class="font-bold text-slate-900 text-xs break-words"><?= htmlspecialchars($s['company_name'] ?? 'Unassigned'); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-medium mt-0.5 break-words">Supervisor: <span class="font-bold text-slate-800"><?= htmlspecialchars($s['supervisor_name'] ?? 'Pending Assignment'); ?></span></p>
                                                </td>
                                                <td class="py-4 px-4 text-right whitespace-nowrap align-middle space-x-1.5">
                                                    <button onclick='openEditStudentModal(<?= json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG); ?>)' class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 rounded-xl border border-slate-300 shadow-2xs transition-colors cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Archive student <?= htmlspecialchars(addslashes($s['name'])); ?>?');">
                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="action" value="archive_user">
                                                        <input type="hidden" name="redirect_tab" value="students">
                                                        <input type="hidden" name="user_id" value="<?= $s['user_id']; ?>">
                                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-300 shadow-2xs transition-colors cursor-pointer">
                                                            Archive
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                                No students registered in this section yet. Click "+ Add New" or "Import CSV" above to begin.
                            </div>
                        <?php endif; ?>

                    <!-- TAB 2: COMPANIES & SUPERVISORS (collapsible cards) -->
                    <?php elseif ($tab === 'companies'): ?>
                        <?php
                        // Renders one supervisor row (shared by company cards and the Unassigned card)
                        $renderSupervisorRow = function (array $sup) {
                            $interns = $sup['interns'] ?? [];
                            ?>
                            <div class="sup-row px-6 py-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="min-w-0 flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-300 text-[#0F2854] text-xs font-black flex items-center justify-center overflow-hidden shrink-0">
                                        <?php if (!empty($sup['avatar_url'])): ?>
                                            <img src="<?= htmlspecialchars($sup['avatar_url']); ?>" alt="" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.onerror=null; this.parentElement.textContent='<?= htmlspecialchars(strtoupper(substr($sup['name'], 0, 1)), ENT_QUOTES); ?>';">
                                        <?php else: ?>
                                            <?= htmlspecialchars(strtoupper(substr($sup['name'], 0, 1))); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-slate-950 text-sm row-name"><?= htmlspecialchars($sup['name']); ?></p>
                                        <p class="text-[11px] text-slate-600 font-semibold mt-0.5 row-sub"><?= htmlspecialchars($sup['email']); ?></p>
                                        <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-[11px]">
                                            <p class="font-semibold text-slate-600">
                                                <span class="font-black text-slate-700 uppercase tracking-wider text-[10px]">Position:</span>
                                                <?= !empty($sup['job_title']) ? '<span class="font-bold text-slate-900">' . htmlspecialchars($sup['job_title']) . '</span>' : '<span class="italic text-slate-400">Not provided</span>'; ?>
                                            </p>
                                            <p class="font-semibold text-slate-600">
                                                <span class="font-black text-slate-700 uppercase tracking-wider text-[10px]">Contact:</span>
                                                <?= !empty($sup['contact_number']) ? '<span class="font-bold text-slate-900">' . htmlspecialchars($sup['contact_number']) . '</span>' : '<span class="italic text-slate-400">Not provided</span>'; ?>
                                            </p>
                                        </div>

                                        <div class="mt-3">
                                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                                Assigned Interns (<?= count($interns); ?>)
                                            </p>
                                            <?php if (!empty($interns)): ?>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <?php foreach ($interns as $intern): ?>
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-semibold text-[11px] border border-slate-300">
                                                            <?= htmlspecialchars($intern['name']); ?>
                                                            <?php if (!empty($intern['student_number'])): ?>
                                                                <span class="text-slate-500 font-medium">· <?= htmlspecialchars($intern['student_number']); ?></span>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-[11px] italic text-slate-400 font-semibold">No interns assigned.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" onclick='openEditSupervisorModal(<?= json_encode($sup, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG); ?>)' class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 rounded-xl border border-slate-300 shadow-2xs transition-colors cursor-pointer">
                                        Edit
                                    </button>
                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Archive supervisor <?= htmlspecialchars(addslashes($sup['name'])); ?>?');">
                        <?= csrf_field(); ?>
                                        <input type="hidden" name="action" value="archive_user">
                                        <input type="hidden" name="redirect_tab" value="companies">
                                        <input type="hidden" name="user_id" value="<?= (int)$sup['user_id']; ?>">
                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-300 shadow-2xs transition-colors cursor-pointer">
                                            Archive
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php
                        };
                        ?>

                        <?php if (!empty($companies) || !empty($unassignedSupervisors)): ?>
                            <div class="p-4 space-y-4 bg-slate-50/40">

                                <?php foreach ($companies as $comp):
                                    $compOffices    = $officesByCompany[(int)$comp['id']] ?? [];
                                    $compSupervisors = $supervisorsByCompany[(int)$comp['id']] ?? [];
                                    $detailLine = trim(($comp['address'] ?? ''), ' ·');
                                ?>
                                    <div class="company-card bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden" data-name="<?= htmlspecialchars(strtolower($comp['name']), ENT_QUOTES); ?>">
                                        <div class="flex items-stretch bg-slate-50/60">
                                            <button type="button" onclick="toggleCompanyCard(this.closest('.company-card'))" class="flex-1 min-w-0 flex items-center justify-between gap-4 pl-6 pr-4 py-4 hover:bg-slate-100/70 transition-colors text-left cursor-pointer" aria-expanded="true">
                                                <div class="min-w-0">
                                                    <p class="font-extrabold text-slate-950 text-sm"><?= htmlspecialchars($comp['name']); ?></p>
                                                    <?php if ($detailLine !== ''): ?>
                                                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5"><?= htmlspecialchars($detailLine); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex items-center gap-2.5 shrink-0">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-[#0F2854] font-bold text-[11px] border border-blue-200">
                                                        <?= intval($comp['total_interns']); ?> Intern(s)
                                                    </span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                        <?= count($compOffices); ?> Office(s)
                                                    </span>
                                                    <svg class="company-chevron w-4 h-4 text-slate-500 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                                </div>
                                            </button>
                                            <div class="flex items-center pr-6">
                                                <button type="button" onclick='openEditCompanyModal(<?= json_encode(['id' => (int)$comp['id'], 'name' => $comp['name'], 'address' => $comp['address'] ?? '', 'offices' => array_map(static function ($o) { return ['id' => (int)$o['id'], 'office_name' => $o['office_name'], 'intern_count' => (int)$o['intern_count']]; }, $compOffices)], JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG); ?>)' class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 rounded-xl border border-slate-300 shadow-2xs transition-colors cursor-pointer">
                                                    Manage
                                                </button>
                                                <form method="POST" action="users.php" class="inline ml-1.5" onsubmit="return confirm('Archive company <?= htmlspecialchars(addslashes($comp['name'])); ?>? It will be hidden from the Companies list and can be restored later.');">
                        <?= csrf_field(); ?>
                                                    <input type="hidden" name="action" value="archive_company">
                                                    <input type="hidden" name="company_id" value="<?= (int)$comp['id']; ?>">
                                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-300 shadow-2xs transition-colors cursor-pointer">
                                                        Archive
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="company-body divide-y divide-slate-200/80 border-t border-slate-200/70">
                                            <?php if (!empty($compOffices)): ?>
                                                <?php foreach ($compOffices as $off): ?>
                                                    <div class="office-block px-6 py-5">
                                                        <!-- Office Sub-Header -->
                                                        <div class="flex items-center justify-between gap-3 pb-3">
                                                            <div class="flex items-center gap-2 min-w-0">
                                                                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                                                </div>
                                                                <p class="text-xs font-black text-slate-900 uppercase tracking-wider truncate">
                                                                    <?= htmlspecialchars($off['office_name']); ?>
                                                                </p>
                                                            </div>
                                                            <div class="flex items-center gap-2 shrink-0">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-[#0F2854] font-bold text-[11px] border border-blue-200">
                                                                    <?= (int)$off['intern_count']; ?> Intern(s)
                                                                </span>
                                                                <?php if ((int)$off['linked_supervisors'] === 0 && (int)$off['linked_students'] === 0): ?>
                                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Delete office <?= htmlspecialchars(addslashes($off['office_name'])); ?>? This cannot be undone.');">
                        <?= csrf_field(); ?>
                                                                        <input type="hidden" name="action" value="delete_office">
                                                                        <input type="hidden" name="office_id" value="<?= (int)$off['id']; ?>">
                                                                        <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-300 transition-colors cursor-pointer">
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    <button type="button" disabled class="px-2.5 py-1 text-[11px] font-bold text-slate-400 bg-slate-100 rounded-lg border border-slate-200 cursor-not-allowed" title="Unlink the supervisor and interns before deleting this office">
                                                                        Delete
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Office Supervisor -->
                                                        <?php if (!empty($off['supervisor'])): ?>
                                                            <?php $renderSupervisorRow($off['supervisor']); ?>
                                                        <?php else: ?>
                                                            <div class="rounded-xl bg-amber-50/70 border border-amber-200 px-4 py-3 flex items-center justify-between gap-3">
                                                                <p class="text-[11px] font-bold text-amber-900">Supervisor required, add one.</p>
                                                                <button type="button" onclick="openAddSupervisorForOffice(<?= (int)$comp['id']; ?>, '<?= htmlspecialchars(addslashes($off['office_name'])); ?>', '<?= htmlspecialchars(addslashes($comp['name'])); ?>')" class="px-3.5 py-1.5 text-xs font-bold text-amber-950 bg-amber-100 hover:bg-amber-200 rounded-xl border border-amber-300 transition-colors cursor-pointer">
                                                                    + Add Supervisor
                                                                </button>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="px-6 py-5">
                                                    <p class="text-[11px] font-bold text-slate-500">No offices registered for this company yet.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if (!empty($unassignedSupervisors)): ?>
                                    <div class="company-card bg-white rounded-2xl border border-amber-300 shadow-xs overflow-hidden" data-name="unassigned supervisors">
                                        <button type="button" onclick="toggleCompanyCard(this.closest('.company-card'))" class="w-full flex items-center justify-between gap-4 px-6 py-4 bg-amber-50/70 hover:bg-amber-100/60 transition-colors text-left cursor-pointer" aria-expanded="true">
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-amber-950 text-sm">Unassigned Supervisors</p>
                                                <p class="text-[11px] font-semibold text-amber-800 mt-0.5">Supervisors not linked to a partner company yet.</p>
                                            </div>
                                            <div class="flex items-center gap-2.5 shrink-0">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-900 font-bold text-[11px] border border-amber-300">
                                                    <?= count($unassignedSupervisors); ?> Supervisor(s)
                                                </span>
                                                <svg class="company-chevron w-4 h-4 text-amber-700 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                            </div>
                                        </button>

                                        <div class="company-body divide-y divide-slate-200/80 border-t border-amber-200">
                                            <?php foreach ($unassignedSupervisors as $sup) { $renderSupervisorRow($sup); } ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php else: ?>
                            <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                                No partner companies registered yet.
                            </div>
                        <?php endif; ?>

                    <!-- TAB 3B: COORDINATORS TABLE -->
                    <?php elseif ($tab === 'coordinators'): ?>
                        <?php if (!empty($coordinators)): ?>
                            <div class="overflow-hidden">
                                <table class="w-full table-fixed text-left border-collapse text-xs">
                                    <colgroup>
                                        <col style="width: 30%;">
                                        <col style="width: 30%;">
                                        <col style="width: 20%;">
                                        <col style="width: 20%;">
                                    </colgroup>
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                            <th class="py-4 px-4">Coordinator</th>
                                            <th class="py-4 px-4">Email</th>
                                            <th class="py-4 px-4">Status</th>
                                            <th class="py-4 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                        <?php foreach ($coordinators as $coord): ?>
                                            <tr class="hover:bg-slate-50 transition-colors user-row">
                                                <td class="py-4 px-4 align-middle">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                            <?php if (!empty($coord['avatar_url'])): ?>
                                                                <img src="<?= htmlspecialchars($coord['avatar_url']); ?>" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <?= strtoupper(substr($coord['name'] ?? 'C', 0, 1)); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <p class="font-extrabold text-slate-950 text-sm row-name break-words"><?= htmlspecialchars($coord['name']); ?></p>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4 text-slate-600 text-xs font-semibold row-sub align-middle break-all">
                                                    <?= htmlspecialchars($coord['email']); ?>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap align-middle">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-bold text-[11px] border border-emerald-200">
                                                        <?= htmlspecialchars(ucfirst($coord['status'] ?? 'active')); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4 text-right whitespace-nowrap align-middle space-x-1.5">
                                                    <button onclick='openEditCoordinatorModal(<?= json_encode($coord, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG); ?>)' class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 rounded-xl border border-slate-300 shadow-2xs transition-colors cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Archive coordinator <?= htmlspecialchars(addslashes($coord['name'])); ?>?');">
                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="action" value="archive_user">
                                                        <input type="hidden" name="redirect_tab" value="coordinators">
                                                        <input type="hidden" name="user_id" value="<?= $coord['user_id']; ?>">
                                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-300 shadow-2xs transition-colors cursor-pointer">
                                                            Archive
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                                No active coordinators registered yet. Click "+ Add New" above to begin.
                            </div>
                        <?php endif; ?>

                    <!-- TAB 4: ARCHIVED TABLE -->
                    <?php elseif ($tab === 'archived'): ?>

                        <!-- Archived Companies -->
                        <?php if (!empty($archivedCompanies)): ?>
                            <div class="p-4 border-b border-slate-200/70 bg-slate-50/60">
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 mb-3">Archived Companies</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <?php foreach ($archivedCompanies as $ac): ?>
                                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs px-4 py-3 flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-xs font-extrabold text-slate-950 truncate"><?= htmlspecialchars($ac['name']); ?></p>
                                                <p class="text-[11px] text-slate-600 font-medium mt-0.5 truncate">
                                                    <?= htmlspecialchars($ac['department'] ?? ''); ?>
                                                    <?php if (!empty($ac['address'])): ?>
                                                        &bull; <?= htmlspecialchars($ac['address']); ?>
                                                    <?php endif; ?>
                                                </p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">
                                                    Archived: <?= date("M d, Y", strtotime($ac['archived_at'] ?? 'now')); ?>
                                                </p>
                                            </div>
                                            <form method="POST" action="users.php" class="shrink-0" onsubmit="return confirm('Restore company <?= htmlspecialchars(addslashes($ac['name'])); ?> back to active status?');">
                        <?= csrf_field(); ?>
                                                <input type="hidden" name="action" value="restore_company">
                                                <input type="hidden" name="company_id" value="<?= (int)$ac['id']; ?>">
                                                <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-emerald-900 bg-emerald-100/80 hover:bg-emerald-200 rounded-xl border border-emerald-300 shadow-2xs transition-colors cursor-pointer">
                                                    Restore
                                                </button>
                                            </form>
                                            <form method="POST" action="users.php" class="shrink-0 ml-1.5" onsubmit="return confirm('⚠️ WARNING: Permanently delete company <?= htmlspecialchars(addslashes($ac['name'])); ?>? This action CANNOT be undone.');">
                        <?= csrf_field(); ?>
                                                <input type="hidden" name="action" value="delete_company_permanently">
                                                <input type="hidden" name="company_id" value="<?= (int)$ac['id']; ?>">
                                                <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-rose-900 bg-rose-100/80 hover:bg-rose-200 rounded-xl border border-rose-300 shadow-2xs transition-colors cursor-pointer">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($archivedUsers)): ?>
                            <div class="overflow-hidden">
                                <table class="w-full table-fixed text-left border-collapse text-xs">
                                    <colgroup>
                                        <col style="width: 24%;">
                                        <col style="width: 28%;">
                                        <col style="width: 14%;">
                                        <col style="width: 16%;">
                                        <col style="width: 18%;">
                                    </colgroup>
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                            <th class="py-4 px-4">Account Name</th>
                                            <th class="py-4 px-4">Email</th>
                                            <th class="py-4 px-4">Role</th>
                                            <th class="py-4 px-4">Archived Date</th>
                                            <th class="py-4 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                        <?php foreach ($archivedUsers as $au): ?>
                                            <tr class="hover:bg-slate-50 transition-colors user-row">
                                                <td class="py-4 px-4 font-extrabold text-slate-950 text-sm row-name align-middle break-words">
                                                    <?= htmlspecialchars($au['name']); ?>
                                                </td>
                                                <td class="py-4 px-4 text-slate-600 text-xs font-semibold row-sub align-middle break-all">
                                                    <?= htmlspecialchars($au['email']); ?>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap align-middle">
                                                    <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[10px] font-bold uppercase border border-slate-300">
                                                        <?= htmlspecialchars($au['role']); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-slate-600 text-xs font-semibold align-middle">
                                                    <?= date("M d, Y \a\\t g:i A", strtotime($au['archived_at'] ?? 'now')); ?>
                                                </td>
                                                <td class="py-4 px-4 text-right whitespace-nowrap align-middle space-x-1.5">
                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Restore user <?= htmlspecialchars(addslashes($au['name'])); ?> back to active status?');">
                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="action" value="restore_user">
                                                        <input type="hidden" name="user_id" value="<?= $au['id']; ?>">
                                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-emerald-900 bg-emerald-100/80 hover:bg-emerald-200 rounded-xl border border-emerald-300 shadow-2xs transition-colors cursor-pointer">
                                                            Restore
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('⚠️ WARNING: Permanently delete <?= htmlspecialchars(addslashes($au['name'])); ?>? This action CANNOT be undone.');">
                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="action" value="delete_user_permanently">
                                                        <input type="hidden" name="user_id" value="<?= $au['id']; ?>">
                                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-rose-900 bg-rose-100/80 hover:bg-rose-200 rounded-xl border border-rose-300 shadow-2xs transition-colors cursor-pointer">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php elseif (empty($archivedCompanies)): ?>
                            <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                                No archived accounts or companies found.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- MODAL 1: ADD STUDENT -->
    <div id="addStudentModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Add Student Intern</h3>
                <button type="button" onclick="toggleModal('addStudentModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3.5 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="create_student">

                <div class="space-y-2.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="name" required placeholder="Enter full name" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Student ID Number <span class="text-rose-600">*</span></label>
                            <input type="text" name="student_number" required placeholder="Enter student ID" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Section <span class="text-rose-600">*</span></label>
                            <select name="section" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                                <option value="A">Section A</option>
                                <option value="B">Section B</option>
                                <option value="C">Section C</option>
                                <option value="D">Section D</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Student Email Address <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" required placeholder="Enter student email address" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Office Placement (Optional)</span>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Company Office</label>
                        <select name="office_id" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800 font-bold focus:outline-none focus:border-[#0F2854]">
                            <option value="">-- Unassigned --</option>
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
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('addStudentModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Student</button>
                </div>
            </form>
        </div>
    </div>

   <!-- MODAL 2: UNIFIED ADD COMPANY & SUPERVISOR -->
    <div id="addCompanySupervisorModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-xl w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-950">Add Partner Company &amp; Supervisor</h3>
                    <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Register a host agency and its industry supervisor.</p>
                </div>
                <button type="button" onclick="toggleModal('addCompanySupervisorModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>

            <form action="users.php" method="POST" class="space-y-4 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="create_company_supervisor">

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Company Information</span>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Company / Office Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="company_name" id="add_cs_company_name" list="existingCompaniesList" oninput="syncOfficeSuggestions()" required maxlength="255" placeholder="Enter or pick a company name" autocomplete="off" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        <datalist id="existingCompaniesList">
                            <?php foreach ($companies as $comp): ?>
                                <option value="<?= htmlspecialchars($comp['name'], ENT_QUOTES); ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                        <p class="text-[10px] text-slate-500 mt-1">Pick an existing company to add another office under it.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Office / Branch Name <span class="text-rose-600">*</span></label>
                            <input type="text" name="department" id="add_cs_office_name" list="existingOfficesList" oninput="syncOfficeSuggestions()" required maxlength="255" placeholder="Enter office or branch name" autocomplete="off" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                            <datalist id="existingOfficesList"></datalist>
                            <p class="text-[10px] text-slate-500 mt-1" id="add_cs_office_hint">Suggestions appear once a company is picked.</p>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Company Address <span class="text-rose-600">*</span></label>
                            <input type="text" name="address_display" id="add_cs_address" oninput="syncAddressCarrier()" required maxlength="255" placeholder="Enter company address" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                            <input type="hidden" name="address" id="add_cs_address_value">
                            <p class="text-[10px] text-slate-500 mt-1" id="add_cs_address_hint"></p>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Supervisor Information</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                            <input type="text" name="supervisor_name" required placeholder="Enter full name" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email Address <span class="text-rose-600">*</span></label>
                            <input type="email" name="supervisor_email" required placeholder="Enter email address" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Job Title / Position</label>
                            <input type="text" name="job_title" maxlength="150" placeholder="Enter job title or position" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Contact Number</label>
                            <input type="tel" name="contact_number" maxlength="20" pattern="[0-9+\-\s()]{7,20}" title="7-20 characters: digits, +, -, spaces or parentheses" placeholder="Enter contact number" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('addCompanySupervisorModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Company &amp; Supervisor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: BULK IMPORT -->
    <div id="bulkImportModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Bulk Import Students (.csv)</h3>
                <button type="button" onclick="toggleModal('bulkImportModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                Upload a CSV file formatted with these 4 columns: <br>
                <span class="font-extrabold text-slate-900">Full Name, Student ID, Email Address, Section</span>
            </p>
            
            <form action="users.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="bulk_import_students">
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-5 text-center bg-slate-50 hover:border-[#0F2854] transition-colors">
                    <input type="file" name="excel_file" accept=".csv, .txt" required class="block w-full text-xs text-slate-600 font-medium file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0F2854] file:text-white hover:file:bg-blue-900 cursor-pointer">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('bulkImportModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <span>Process Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3B: BULK IMPORT COORDINATORS -->
    <div id="bulkImportCoordinatorsModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Bulk Import Coordinators (.csv)</h3>
                <button type="button" onclick="toggleModal('bulkImportCoordinatorsModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                Upload a CSV file formatted with these 2 columns: <br>
                <span class="font-extrabold text-slate-900">Full Name, Email Address</span>
            </p>

            <form action="users.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="bulk_import_coordinators">
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-5 text-center bg-slate-50 hover:border-[#0F2854] transition-colors">
                    <input type="file" name="excel_file" accept=".csv, .txt" required class="block w-full text-xs text-slate-600 font-medium file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0F2854] file:text-white hover:file:bg-blue-900 cursor-pointer">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('bulkImportCoordinatorsModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <span>Process Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3C: BULK IMPORT COMPANIES & SUPERVISORS -->
    <div id="bulkImportCompaniesSupervisorsModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-2xl w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Bulk Import Companies &amp; Supervisors (.csv)</h3>
                <button type="button" onclick="toggleModal('bulkImportCompaniesSupervisorsModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                Upload a CSV file formatted with these 7 columns: <br>
                <span class="font-extrabold text-slate-900">Company Name, Office / Branch, Address, Supervisor Name, Supervisor Email, Job Title, Contact Number</span>
            </p>
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-[11px] font-medium leading-relaxed">
                Companies are matched by exact name (existing companies are reused). Each office can only have <strong>one supervisor</strong> — rows for an office that already has a supervisor are skipped.
            </div>

            <form action="users.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="bulk_import_companies_supervisors">
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-5 text-center bg-slate-50 hover:border-[#0F2854] transition-colors">
                    <input type="file" name="excel_file" accept=".csv, .txt" required class="block w-full text-xs text-slate-600 font-medium file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0F2854] file:text-white hover:file:bg-blue-900 cursor-pointer">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('bulkImportCompaniesSupervisorsModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <span>Process Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: EDIT STUDENT -->
    <div id="editStudentModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Edit Student Intern</h3>
                <button type="button" onclick="toggleModal('editStudentModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3.5 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="role" value="student">
                <input type="hidden" name="redirect_tab" value="students">
                <input type="hidden" name="user_id" id="edit_student_user_id">
                
                <div class="space-y-2.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="name" id="edit_student_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Student ID Number <span class="text-rose-600">*</span></label>
                            <input type="text" name="student_number" id="edit_student_number" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Section <span class="text-rose-600">*</span></label>
                            <select name="section" id="edit_student_section" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                                <option value="A">Section A</option>
                                <option value="B">Section B</option>
                                <option value="C">Section C</option>
                                <option value="D">Section D</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Student Email Address <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" id="edit_student_email" required placeholder="Enter student email address" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Office Placement</span>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Company Office</label>
                        <select name="office_id" id="edit_student_office" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800 font-bold focus:outline-none focus:border-[#0F2854]">
                            <option value="">-- Unassigned --</option>
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
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('editStudentModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: EDIT SUPERVISOR -->
    <div id="editSupervisorModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Edit Supervisor</h3>
                <button type="button" onclick="toggleModal('editSupervisorModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="role" value="supervisor">
                <input type="hidden" name="redirect_tab" value="companies">
                <input type="hidden" name="user_id" id="edit_supervisor_user_id">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="name" id="edit_supervisor_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" id="edit_supervisor_email" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Assigned Company</label>
                    <select name="company_id" id="edit_supervisor_company" onchange="filterSupervisorOffices()" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        <option value="">-- Select Partner Company --</option>
                        <?php foreach ($companies as $comp): ?>
                            <option value="<?= $comp['id']; ?>"><?= htmlspecialchars($comp['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Office / Branch</label>
                    <select name="office_id" id="edit_supervisor_office" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        <option value="">-- Select Office --</option>
                    </select>
                    <input type="hidden" name="office_id" id="edit_supervisor_office_locked" value="">
                    <p class="text-[10px] text-slate-500 mt-1" id="edit_supervisor_office_hint"></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Job Title / Position</label>
                        <input type="text" name="job_title" id="edit_supervisor_job_title" maxlength="150" placeholder="Enter job title or position" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Contact Number</label>
                        <input type="tel" name="contact_number" id="edit_supervisor_contact_number" maxlength="20" pattern="[0-9+\-\s()]{7,20}" title="7-20 characters: digits, +, -, spaces or parentheses" placeholder="Enter contact number" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('editSupervisorModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5B: EDIT COMPANY -->
    <div id="editCompanyModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-950">Manage Company</h3>
                    <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Edit the company details and its offices.</p>
                </div>
                <button type="button" onclick="toggleModal('editCompanyModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center shrink-0">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3.5 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="edit_company">
                <input type="hidden" name="company_id" id="edit_company_id">

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Company Name</label>
                    <input type="text" id="edit_company_name" disabled value="" class="w-full bg-slate-100 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-500 cursor-not-allowed">
                    <p class="text-[10px] text-slate-500 mt-1">Company details cannot be changed. Archive and re-create to rename.</p>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Company Address</label>
                    <input type="text" id="edit_company_address" disabled value="" class="w-full bg-slate-100 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-500 cursor-not-allowed">
                </div>

                <!-- Offices (read-only) -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Offices</span>
                        <span class="text-[10px] font-semibold text-slate-500"><?= count($offices) > 0 ? 'Existing offices cannot be renamed here' : ''; ?></span>
                    </div>

                    <div id="editCompanyOffices" class="space-y-2"></div>

                    <div id="editCompanyNoOffices" class="hidden text-[11px] italic text-slate-400 font-semibold">
                        No offices yet — add one below.
                    </div>
                </div>

                <!-- Add Office & Supervisor -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Add Office &amp; Supervisor</span>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Office / Branch Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="office_name" maxlength="255" placeholder="Enter office or branch name" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                    </div>

                    <div class="pt-1 border-t border-slate-200/80">
                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Supervisor Information</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                            <input type="text" name="supervisor_name" maxlength="255" placeholder="Enter full name" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email Address <span class="text-rose-600">*</span></label>
                            <input type="email" name="supervisor_email" maxlength="255" placeholder="Enter email address" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Job Title / Position</label>
                            <input type="text" name="job_title" maxlength="150" placeholder="Enter job title" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Contact Number</label>
                            <input type="tel" name="contact_number" maxlength="20" pattern="[0-9+\-\s()]{7,20}" title="7-20 characters: digits, +, -, spaces or parentheses" placeholder="Enter contact number" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-500">Each office can only have one supervisor. Office removal is available on the company card.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('editCompanyModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 6: ADD COORDINATOR -->
    <div id="addCoordinatorModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Add Coordinator</h3>
                <button type="button" onclick="toggleModal('addCoordinatorModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="create_coordinator">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                    <input type="text" name="name" required placeholder="Enter full name" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address <span class="text-rose-600">*</span></label>
                    <input type="email" name="email" required placeholder="Enter email address" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('addCoordinatorModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Coordinator</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 7: EDIT COORDINATOR -->
    <div id="editCoordinatorModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full m-auto p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Edit Coordinator</h3>
                <button type="button" onclick="toggleModal('editCoordinatorModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3 text-xs">
                        <?= csrf_field(); ?>
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="role" value="coordinator">
                <input type="hidden" name="redirect_tab" value="coordinators">
                <input type="hidden" name="user_id" id="edit_coordinator_user_id">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                    <input type="text" name="name" id="edit_coordinator_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address <span class="text-rose-600">*</span></label>
                    <input type="email" name="email" id="edit_coordinator_email" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role / Status <span class="text-rose-600">*</span></label>
                    <select name="new_role" id="edit_coordinator_new_role" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        <option value="coordinator">Coordinator</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('editCoordinatorModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const allSupervisors = <?= json_encode($supervisors ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const allOffices = <?= json_encode($offices ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        function toggleStudentsMenu(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('studentsMenu');
            const chevron = document.getElementById('studentsMenuChevron');
            if (menu) menu.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('rotate-180');
        }

        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.toggle('hidden');
        }

        function toggleAddMenu() {
            const menu = document.getElementById('addMenu');
            if (menu) menu.classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            const menu = document.getElementById('addMenu');
            const btn = e.target.closest('button');
            if (menu && !menu.contains(e.target) && (!btn || !btn.textContent.includes('+ Add New'))) {
                menu.classList.add('hidden');
            }

            const studentsMenu = document.getElementById('studentsMenu');
            if (studentsMenu && !studentsMenu.contains(e.target) && !e.target.closest('#studentsMenuChevron') && !e.target.closest('button[aria-label="Toggle sections"]')) {
                studentsMenu.classList.add('hidden');
                document.getElementById('studentsMenuChevron')?.classList.remove('rotate-180');
            }
        });

        // Suggest existing offices + address for the company name typed in the modal
        // JSON_HEX_TAG stops a company or office name from closing this script block.
        const companyOfficesMap = <?= json_encode(array_reduce($offices ?? [], function ($map, $office) {
            $key = strtolower((string)($office['company_name'] ?? ''));
            $map[$key][] = (string)($office['office_name'] ?? '');
            return $map;
        }, []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        const companyAddressesMap = <?= json_encode(array_reduce($companies ?? [], function ($map, $company) {
            $map[strtolower((string)($company['name'] ?? ''))] = (string)($company['address'] ?? '');
            return $map;
        }, []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        function setAddressLocked(locked, value) {
            const addressInput = document.getElementById('add_cs_address');
            const addressCarrier = document.getElementById('add_cs_address_value');
            const addressHint = document.getElementById('add_cs_address_hint');
            if (!addressInput) return;

            if (locked) {
                addressInput.value = value || '';
                // A disabled input is not submitted, so mirror the value into the hidden field
                if (addressCarrier) addressCarrier.value = value || '';
                addressInput.disabled = true;
                addressInput.classList.add('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');
                if (addressHint) {
                    addressHint.textContent = 'Address taken from the existing company record.';
                }
            } else {
                addressInput.disabled = false;
                addressInput.classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');
                if (addressCarrier) addressCarrier.value = addressInput.value || '';
                if (addressHint) {
                    addressHint.textContent = '';
                }
            }
        }

        // Keeps the hidden carrier in sync while the coordinator types a new address
        function syncAddressCarrier() {
            const addressInput = document.getElementById('add_cs_address');
            const addressCarrier = document.getElementById('add_cs_address_value');
            if (addressInput && addressCarrier && !addressInput.disabled) {
                addressCarrier.value = addressInput.value || '';
            }
        }

        function syncOfficeSuggestions() {
            const companyInput = document.getElementById('add_cs_company_name');
            const officeInput = document.getElementById('add_cs_office_name');
            const datalist = document.getElementById('existingOfficesList');
            const hint = document.getElementById('add_cs_office_hint');
            if (!companyInput || !officeInput || !datalist) return;

            const typedName = companyInput.value.trim();
            const key = typedName.toLowerCase();
            const matches = key && companyOfficesMap[key] ? companyOfficesMap[key] : [];
            const existingAddress = key && Object.prototype.hasOwnProperty.call(companyAddressesMap, key)
                ? companyAddressesMap[key]
                : null;
            const isExistingCompany = existingAddress !== null;

            datalist.innerHTML = '';
            matches.forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                datalist.appendChild(opt);
            });

            if (isExistingCompany) {
                setAddressLocked(true, existingAddress);
            } else {
                setAddressLocked(false);
            }

            if (matches.length > 0) {
                hint.textContent = 'Existing offices at this company: ' + matches.join(', ');
            } else if (key) {
                hint.textContent = 'No existing offices found for "' + typedName + '" — this will be a new company.';
            } else {
                hint.textContent = 'Suggestions appear once a company is picked.';
            }
        }

        // Opens the Add Company & Supervisor modal from the "+ Add New" menu.
        // Must re-enable the address field, otherwise it stays disabled from a
        // previous openAddSupervisorForOffice() call and new companies save blank.
        function openAddSupervisorModal() {
            const form = document.querySelector('#addCompanySupervisorModal form');
            if (form) form.reset();
            setAddressLocked(false);
            syncOfficeSuggestions();
            toggleModal('addCompanySupervisorModal');
        }

        // Opens the Add Company & Supervisor modal pre-filled for a specific office
        function openAddSupervisorForOffice(companyId, officeName, companyName) {
            const form = document.querySelector('#addCompanySupervisorModal form');
            if (form) {
                form.reset();
                form.querySelector('[name="company_name"]').value = companyName || '';
                form.querySelector('[name="department"]').value = officeName || '';
            }
            // This button always targets an existing company, so its address is read-only
            const key = (companyName || '').trim().toLowerCase();
            const knownAddress = Object.prototype.hasOwnProperty.call(companyAddressesMap, key)
                ? companyAddressesMap[key]
                : '';
            setAddressLocked(true, knownAddress);

            toggleModal('addCompanySupervisorModal');
            syncOfficeSuggestions();
        }

        // Dynamic Filtering: Select Company in Modal -> Loads Supervisors
        function filterModalSupervisors(prefix, preselectedSupId = null) {
            const compSelect = document.getElementById(prefix + '_company');
            const supSelect  = document.getElementById(prefix + '_supervisor');
            const companyId  = parseInt(compSelect.value);

            supSelect.innerHTML = '<option value="">-- Unassigned --</option>';

            if (!companyId) {
                supSelect.disabled = true;
                return;
            }

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
                opt.textContent = "No supervisors for this company";
                supSelect.appendChild(opt);
                supSelect.disabled = false;
            }
        }

        // Dynamic Filtering: Select Company in Edit Supervisor Modal -> Loads Offices
        function filterSupervisorOffices(preselectedOfficeId = null) {
            const companySelect = document.getElementById('edit_supervisor_company');
            const officeSelect  = document.getElementById('edit_supervisor_office');
            const companyId     = parseInt(companySelect.value);

            officeSelect.innerHTML = '<option value="">-- Select Office --</option>';

            if (!companyId) {
                return;
            }

            const filtered = allOffices.filter(o => parseInt(o.company_id) === companyId);

            if (filtered.length > 0) {
                filtered.forEach(off => {
                    const opt = document.createElement('option');
                    opt.value = off.id;
                    opt.textContent = off.office_name;
                    if (preselectedOfficeId && parseInt(off.id) === preselectedOfficeId) {
                        opt.selected = true;
                    }
                    officeSelect.appendChild(opt);
                });
            }
        }

        function openEditStudentModal(student) {
            document.getElementById('edit_student_user_id').value = student.user_id;
            document.getElementById('edit_student_name').value = student.name;
            document.getElementById('edit_student_number').value = student.student_number;
            document.getElementById('edit_student_email').value = student.email;
            document.getElementById('edit_student_section').value = student.section || 'A';
            document.getElementById('edit_student_office').value = student.office_id || '';
            
            toggleModal('editStudentModal');
        }

        function openEditSupervisorModal(supervisor) {
            document.getElementById('edit_supervisor_user_id').value = supervisor.user_id;
            document.getElementById('edit_supervisor_name').value = supervisor.name;
            document.getElementById('edit_supervisor_email').value = supervisor.email;
            document.getElementById('edit_supervisor_company').value = supervisor.company_id || '';
            document.getElementById('edit_supervisor_job_title').value = supervisor.job_title || '';
            document.getElementById('edit_supervisor_contact_number').value = supervisor.contact_number || '';
            filterSupervisorOffices(parseInt(supervisor.office_id || 0));

            // A supervisor is permanently attached to their office once assigned
            const currentOfficeId = parseInt(supervisor.office_id || 0);
            const officeSelect = document.getElementById('edit_supervisor_office');
            const officeHint = document.getElementById('edit_supervisor_office_hint');
            const companySelect = document.getElementById('edit_supervisor_company');

            if (officeSelect) {
                if (currentOfficeId > 0) {
                    officeSelect.disabled = true;
                    officeSelect.classList.add('bg-slate-100', 'text-slate-500');
                } else {
                    officeSelect.disabled = false;
                    officeSelect.classList.remove('bg-slate-100', 'text-slate-500');
                }
            }

            // A disabled select is not submitted, so carry the locked value in a hidden field
            const lockedOfficeField = document.getElementById('edit_supervisor_office_locked');
            if (lockedOfficeField) {
                lockedOfficeField.disabled = !(currentOfficeId > 0);
                lockedOfficeField.value = currentOfficeId > 0 ? currentOfficeId : '';
            }

            if (officeHint) {
                officeHint.textContent = currentOfficeId > 0
                    ? 'Locked to this office — a supervisor cannot be moved.'
                    : 'Pick the office this supervisor manages.';
            }

            if (companySelect) {
                companySelect.disabled = currentOfficeId > 0;
                if (currentOfficeId > 0) {
                    companySelect.classList.add('bg-slate-100', 'text-slate-500');
                } else {
                    companySelect.classList.remove('bg-slate-100', 'text-slate-500');
                }
            }

            toggleModal('editSupervisorModal');
        }

        // Renders one read-only office row inside the Manage Company modal
        function addEditCompanyOfficeRow(officeId, officeName, internCount) {
            const container = document.getElementById('editCompanyOffices');
            const emptyNote = document.getElementById('editCompanyNoOffices');
            if (!container) return;

            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2';

            row.innerHTML = '<span class="text-xs font-bold text-slate-800 truncate">' +
                String(officeName || '').replace(/[<>&]/g, '') + '</span>' +
                '<span class="text-[10px] font-bold text-slate-500 shrink-0">' +
                parseInt(internCount || 0, 10) + ' Intern(s)</span>';

            container.appendChild(row);
            if (emptyNote) emptyNote.classList.add('hidden');
        }

        function openEditCompanyModal(company) {
            document.getElementById('edit_company_id').value = company.id;
            document.getElementById('edit_company_name').value = company.name || '';
            document.getElementById('edit_company_address').value = company.address || '';

            const container = document.getElementById('editCompanyOffices');
            const emptyNote = document.getElementById('editCompanyNoOffices');
            if (container) container.innerHTML = '';

            const offices = company.offices || [];
            offices.forEach(function (off) {
                addEditCompanyOfficeRow(off.id, off.office_name, off.intern_count);
            });

            if (emptyNote) {
                emptyNote.classList.toggle('hidden', offices.length > 0);
            }

            toggleModal('editCompanyModal');
        }

        function openEditCoordinatorModal(coordinator) {
            document.getElementById('edit_coordinator_user_id').value = coordinator.user_id;
            document.getElementById('edit_coordinator_name').value = coordinator.name;
            document.getElementById('edit_coordinator_email').value = coordinator.email;
            document.getElementById('edit_coordinator_new_role').value = 'coordinator';
            toggleModal('editCoordinatorModal');
        }

        // Collapsible company cards
        function setCompanyCardOpen(card, open) {
            const body = card.querySelector('.company-body');
            const chevron = card.querySelector('.company-chevron');
            const toggle = card.querySelector('button[aria-expanded]');
            if (body) body.classList.toggle('hidden', !open);
            if (chevron) chevron.classList.toggle('rotate-180', open);
            if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        function toggleCompanyCard(card) {
            const body = card.querySelector('.company-body');
            setCompanyCardOpen(card, body && body.classList.contains('hidden'));
        }

        // Cards start expanded
        document.querySelectorAll('.company-card').forEach(function(card) {
            setCompanyCardOpen(card, true);
        });

        // Live Search Filter
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.user-row');

                // Company cards: match company name (show all its supervisors) or individual supervisors
                document.querySelectorAll('.company-card').forEach(function(card) {
                    const companyMatch = query === '' || (card.dataset.name || '').includes(query);
                    let anyMatch = false;

                    card.querySelectorAll('.sup-row').forEach(function(row) {
                        const name = row.querySelector('.row-name')?.textContent.toLowerCase() || '';
                        const sub  = row.querySelector('.row-sub')?.textContent.toLowerCase() || '';
                        const match = query === '' || name.includes(query) || sub.includes(query);
                        row.style.display = (match || companyMatch) ? '' : 'none';
                        if (match) anyMatch = true;
                    });

                    card.style.display = (companyMatch || anyMatch) ? '' : 'none';
                    if (query !== '' && (companyMatch || anyMatch)) {
                        setCompanyCardOpen(card, true);
                    }
                });

                rows.forEach(function(row) {
                    const name = row.querySelector('.row-name')?.textContent.toLowerCase() || '';
                    const sub  = row.querySelector('.row-sub')?.textContent.toLowerCase() || '';
                    
                    if (name.includes(query) || sub.includes(query)) {
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