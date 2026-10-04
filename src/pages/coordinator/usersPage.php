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
                                Manage student intern, industry supervisor, and partner company records.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2.5 shrink-0">
                            <button type="button" onclick="toggleModal('bulkImportModal')" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 border border-slate-300 shadow-2xs cursor-pointer">
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
                                    <button type="button" onclick="toggleAddMenu(); toggleModal('addCompanySupervisorModal');" class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-800 hover:bg-slate-100 font-bold transition-colors cursor-pointer">
                                        <span>Company &amp; Supervisor</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Integrated Tabs, Section Selector & Live Search -->
                    <div class="p-4 border-b border-slate-200/70 flex flex-col md:flex-row items-center justify-between gap-4 bg-white">
                        
                        <!-- Navigation Tabs -->
                        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
                            <a href="users.php?tab=students" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= ($tab ?? 'students') === 'students' ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                <span>Students</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? 'students') === 'students' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($students ?? []); ?></span>
                            </a>

                            <a href="users.php?tab=supervisors" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= ($tab ?? '') === 'supervisors' ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                <span>Supervisors</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? '') === 'supervisors' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($supervisors ?? []); ?></span>
                            </a>

                            <a href="users.php?tab=companies" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= ($tab ?? '') === 'companies' ? 'bg-[#0F2854] text-white border border-[#0F2854]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                <span>Companies</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? '') === 'companies' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($companies ?? []); ?></span>
                            </a>

                            <a href="users.php?tab=archived" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 <?= ($tab ?? '') === 'archived' ? 'bg-rose-700 text-white border border-rose-700' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                <span>Archived</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($tab ?? '') === 'archived' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'; ?>"><?= count($archivedUsers ?? []); ?></span>
                            </a>
                        </div>

                        <!-- Search & Section Filter Bar -->
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <?php if (($tab ?? 'students') === 'students'): ?>
                                <select onchange="location.href='users.php?tab=students&section=' + this.value" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer">
                                    <option value="all" <?= ($selectedSection ?? 'all') === 'all' ? 'selected' : ''; ?>>All Sections</option>
                                    <?php foreach ($activeSections as $sec): ?>
                                        <option value="<?= htmlspecialchars($sec); ?>" <?= ($selectedSection ?? '') === $sec ? 'selected' : ''; ?>>
                                            Section <?= htmlspecialchars($sec); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>

                            <!-- Instant Live Search -->
                            <div class="relative w-full md:w-64">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input 
                                    type="text" 
                                    id="userSearchInput" 
                                    placeholder="Search <?= htmlspecialchars($tab ?? 'students'); ?>..." 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- 3. Dynamic Data Tables -->

                    <!-- TAB 1: STUDENTS TABLE -->
                    <?php if (($tab ?? 'students') === 'students'): ?>
                        <?php if (!empty($students)): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                            <th class="py-4 px-6">Student Intern</th>
                                            <th class="py-4 px-6">Section</th>
                                            <th class="py-4 px-6">Company &amp; Supervisor</th>
                                            <th class="py-4 px-6 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                        <?php foreach ($students as $s): ?>
                                            <tr class="hover:bg-slate-50 transition-colors user-row">
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                            <?php if (!empty($s['avatar_url'])): ?>
                                                                <img src="<?= htmlspecialchars($s['avatar_url']); ?>" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <?= strtoupper(substr($s['name'] ?? 'S', 0, 1)); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <p class="font-extrabold text-slate-950 text-sm row-name"><?= htmlspecialchars($s['name']); ?></p>
                                                            <p class="text-[11px] text-slate-600 font-semibold mt-0.5 row-sub">ID: <?= htmlspecialchars($s['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($s['email']); ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                        Section <?= htmlspecialchars($s['section'] ?? 'A'); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <p class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($s['company_name'] ?? 'Unassigned'); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-medium mt-0.5">Supervisor: <span class="font-bold text-slate-800"><?= htmlspecialchars($s['supervisor_name'] ?? 'Pending Assignment'); ?></span></p>
                                                </td>
                                                <td class="py-4 px-6 text-right whitespace-nowrap align-middle space-x-1.5">
                                                    <button onclick='openEditStudentModal(<?= json_encode($s); ?>)' class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 rounded-xl border border-slate-300 shadow-2xs transition-colors cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Archive student <?= htmlspecialchars(addslashes($s['name'])); ?>?');">
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

                    <!-- TAB 2: SUPERVISORS TABLE -->
                    <?php elseif ($tab === 'supervisors'): ?>
                        <?php if (!empty($supervisors)): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                            <th class="py-4 px-6">Supervisor</th>
                                            <th class="py-4 px-6">Company</th>
                                            <th class="py-4 px-6">Assigned Interns</th>
                                            <th class="py-4 px-6 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                        <?php foreach ($supervisors as $sup): ?>
                                            <tr class="hover:bg-slate-50 transition-colors user-row">
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <p class="font-extrabold text-slate-950 text-sm row-name"><?= htmlspecialchars($sup['name']); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-semibold mt-0.5 row-sub"><?= htmlspecialchars($sup['email']); ?></p>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <p class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($sup['company_name'] ?? 'Unassigned'); ?></p>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                        <?= intval($sup['assigned_interns']); ?> Intern(s)
                                                    </span>
                                                </td>
                                                <td class="py-4 px-6 text-right whitespace-nowrap align-middle space-x-1.5">
                                                    <button onclick='openEditSupervisorModal(<?= json_encode($sup); ?>)' class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 rounded-xl border border-slate-300 shadow-2xs transition-colors cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Archive supervisor <?= htmlspecialchars(addslashes($sup['name'])); ?>?');">
                                                        <input type="hidden" name="action" value="archive_user">
                                                        <input type="hidden" name="redirect_tab" value="supervisors">
                                                        <input type="hidden" name="user_id" value="<?= $sup['user_id']; ?>">
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
                                No active supervisors registered yet.
                            </div>
                        <?php endif; ?>

                    <!-- TAB 3: COMPANIES TABLE -->
                    <?php elseif ($tab === 'companies'): ?>
                        <?php if (!empty($companies)): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                            <th class="py-4 px-6">Company Name</th>
                                            <th class="py-4 px-6">Department / Branch</th>
                                            <th class="py-4 px-6">Total Interns</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                        <?php foreach ($companies as $comp): ?>
                                            <tr class="hover:bg-slate-50 transition-colors user-row">
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <p class="font-extrabold text-slate-950 text-sm row-name"><?= htmlspecialchars($comp['name']); ?></p>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <p class="text-xs font-semibold text-slate-600"><?= htmlspecialchars($comp['department'] ?? 'Main Office'); ?></p>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-[#0F2854] font-bold text-[11px] border border-blue-200">
                                                        <?= intval($comp['total_interns']); ?> Intern(s)
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                                No partner companies registered yet.
                            </div>
                        <?php endif; ?>

                    <!-- TAB 4: ARCHIVED TABLE -->
                    <?php elseif ($tab === 'archived'): ?>
                        <?php if (!empty($archivedUsers)): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                            <th class="py-4 px-6">Account Name</th>
                                            <th class="py-4 px-6">Email</th>
                                            <th class="py-4 px-6">Role</th>
                                            <th class="py-4 px-6">Archived Date</th>
                                            <th class="py-4 px-6 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                        <?php foreach ($archivedUsers as $au): ?>
                                            <tr class="hover:bg-slate-50 transition-colors user-row">
                                                <td class="py-4 px-6 whitespace-nowrap font-extrabold text-slate-950 text-sm row-name align-middle">
                                                    <?= htmlspecialchars($au['name']); ?>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap text-slate-600 text-xs font-semibold row-sub align-middle">
                                                    <?= htmlspecialchars($au['email']); ?>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                    <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[10px] font-bold uppercase border border-slate-300">
                                                        <?= htmlspecialchars($au['role']); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 px-6 whitespace-nowrap text-slate-600 text-xs font-semibold align-middle">
                                                    <?= date("M d, Y \a\\t g:i A", strtotime($au['archived_at'] ?? 'now')); ?>
                                                </td>
                                                <td class="py-4 px-6 text-right whitespace-nowrap align-middle space-x-1.5">
                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Restore user <?= htmlspecialchars(addslashes($au['name'])); ?> back to active status?');">
                                                        <input type="hidden" name="action" value="restore_user">
                                                        <input type="hidden" name="user_id" value="<?= $au['id']; ?>">
                                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-emerald-900 bg-emerald-100/80 hover:bg-emerald-200 rounded-xl border border-emerald-300 shadow-2xs transition-colors cursor-pointer">
                                                            Restore
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('⚠️ WARNING: Permanently delete <?= htmlspecialchars(addslashes($au['name'])); ?>? This action CANNOT be undone.');">
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
                        <?php else: ?>
                            <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                                No archived accounts found.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- MODAL 1: ADD STUDENT -->
    <div id="addStudentModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full overflow-hidden p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Add Student Intern</h3>
                <button type="button" onclick="toggleModal('addStudentModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3.5 text-xs">
                <input type="hidden" name="action" value="create_student">

                <div class="space-y-2.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g., Katelyn Coming" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Student ID Number</label>
                            <input type="text" name="student_number" required placeholder="e.g., 2023-IT01" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Section</label>
                            <select name="section" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                                <option value="A">Section A</option>
                                <option value="B">Section B</option>
                                <option value="C">Section C</option>
                                <option value="D">Section D</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" required placeholder="student@nbsc.edu.ph" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Company Placement (Optional)</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Host Company</label>
                            <select id="add_student_company" name="company_id" onchange="filterModalSupervisors('add_student')" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800 font-bold focus:outline-none focus:border-[#0F2854]">
                                <option value="">-- Unassigned --</option>
                                <?php foreach ($companies as $comp): ?>
                                    <option value="<?= $comp['id']; ?>"><?= htmlspecialchars($comp['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Supervisor</label>
                            <select id="add_student_supervisor" name="supervisor_id" disabled class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800 font-bold focus:outline-none focus:border-[#0F2854] disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">-- Select Company First --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('addStudentModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: UNIFIED ADD COMPANY & SUPERVISOR -->
    <div id="addCompanySupervisorModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full overflow-hidden p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-950">Add Partner Company &amp; Supervisor</h3>
                    <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Register a host agency and its industry supervisor.</p>
                </div>
                <button type="button" onclick="toggleModal('addCompanySupervisorModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>

            <form action="users.php" method="POST" class="space-y-4 text-xs">
                <input type="hidden" name="action" value="create_company_supervisor">

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Company Information</span>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Company / Office Name</label>
                        <input type="text" name="company_name" required placeholder="e.g., LGU Manolo Fortich" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department / Branch</label>
                        <input type="text" name="department" placeholder="e.g., IT Department / SASDD" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Supervisor Information</span>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Supervisor Full Name</label>
                        <input type="text" name="supervisor_name" required placeholder="e.g., Jane Doe" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="supervisor_email" required placeholder="supervisor@company.com" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('addCompanySupervisorModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Company &amp; Supervisor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: BULK IMPORT -->
    <div id="bulkImportModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Bulk Import Students (.csv)</h3>
                <button type="button" onclick="toggleModal('bulkImportModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                Upload a CSV file formatted with these 4 columns: <br>
                <span class="font-extrabold text-slate-900">Full Name, Student ID, Email Address, Section</span>
            </p>
            
            <form action="users.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                <input type="hidden" name="action" value="bulk_import_students">
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-5 text-center bg-slate-50 hover:border-[#0F2854] transition-colors">
                    <input type="file" name="excel_file" accept=".csv, .txt" required class="block w-full text-xs text-slate-600 font-medium file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0F2854] file:text-white hover:file:bg-blue-900 cursor-pointer">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('bulkImportModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <span>Process Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: EDIT STUDENT -->
    <div id="editStudentModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full overflow-hidden p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Edit Student Intern</h3>
                <button type="button" onclick="toggleModal('editStudentModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3.5 text-xs">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="role" value="student">
                <input type="hidden" name="redirect_tab" value="students">
                <input type="hidden" name="user_id" id="edit_student_user_id">
                
                <div class="space-y-2.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="edit_student_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Student ID Number</label>
                            <input type="text" name="student_number" id="edit_student_number" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Section</label>
                            <select name="section" id="edit_student_section" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                                <option value="A">Section A</option>
                                <option value="B">Section B</option>
                                <option value="C">Section C</option>
                                <option value="D">Section D</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="edit_student_email" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Company Placement</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Host Company</label>
                            <select id="edit_student_company" name="company_id" onchange="filterModalSupervisors('edit_student')" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800 font-bold focus:outline-none focus:border-[#0F2854]">
                                <option value="">-- Unassigned --</option>
                                <?php foreach ($companies as $comp): ?>
                                    <option value="<?= $comp['id']; ?>"><?= htmlspecialchars($comp['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Supervisor</label>
                            <select id="edit_student_supervisor" name="supervisor_id" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800 font-bold focus:outline-none focus:border-[#0F2854]">
                                <option value="">-- Unassigned --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('editStudentModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: EDIT SUPERVISOR -->
    <div id="editSupervisorModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Edit Supervisor</h3>
                <button type="button" onclick="toggleModal('editSupervisorModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">✕</button>
            </div>
            <form action="users.php" method="POST" class="space-y-3 text-xs">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="role" value="supervisor">
                <input type="hidden" name="redirect_tab" value="supervisors">
                <input type="hidden" name="user_id" id="edit_supervisor_user_id">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Supervisor Full Name</label>
                    <input type="text" name="name" id="edit_supervisor_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="edit_supervisor_email" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Assigned Company</label>
                    <select name="company_id" id="edit_supervisor_company" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        <option value="">-- Select Partner Company --</option>
                        <?php foreach ($companies as $comp): ?>
                            <option value="<?= $comp['id']; ?>"><?= htmlspecialchars($comp['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="toggleModal('editSupervisorModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const allSupervisors = <?= json_encode($supervisors ?? []); ?>;

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
        });

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

        function openEditStudentModal(student) {
            document.getElementById('edit_student_user_id').value = student.user_id;
            document.getElementById('edit_student_name').value = student.name;
            document.getElementById('edit_student_number').value = student.student_number;
            document.getElementById('edit_student_email').value = student.email;
            document.getElementById('edit_student_section').value = student.section || 'A';
            
            const compSelect = document.getElementById('edit_student_company');
            compSelect.value = student.company_id || '';
            filterModalSupervisors('edit_student', parseInt(student.supervisor_id || 0));

            toggleModal('editStudentModal');
        }

        function openEditSupervisorModal(supervisor) {
            document.getElementById('edit_supervisor_user_id').value = supervisor.user_id;
            document.getElementById('edit_supervisor_name').value = supervisor.name;
            document.getElementById('edit_supervisor_email').value = supervisor.email;
            document.getElementById('edit_supervisor_company').value = supervisor.company_id || '';
            toggleModal('editSupervisorModal');
        }

        // Live Search Filter
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.user-row');

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