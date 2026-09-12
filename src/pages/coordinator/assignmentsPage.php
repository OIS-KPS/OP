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

                <!-- Unified Assignments Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
                    <!-- 1. Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                        <div>
                            <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">Student Placements</h1>
                            <p class="text-xs font-semibold text-slate-600 mt-1">
                                Link enrolled interns to their host company and assigned industry supervisor.
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center gap-3 shrink-0">
                            <button type="button" onclick="openAssignModal()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer">
                                <span>+ Assign Student</span>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Integrated Filter Tabs & Live Search Toolbar -->
                    <div class="p-4 border-b border-slate-200/70 flex flex-col md:flex-row items-center justify-between gap-4 bg-white">
                        
                        <!-- Status Filter Tabs -->
                        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
                            <button type="button" onclick="filterByStatus('all', this)" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-[#0F2854] text-white border border-[#0F2854] cursor-pointer">
                                <span>All Interns</span>
                                <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white"><?= count($students ?? []); ?></span>
                            </button>
                            <button type="button" onclick="filterByStatus('assigned', this)" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 cursor-pointer">
                                <span>Assigned</span>
                            </button>
                            <button type="button" onclick="filterByStatus('unassigned', this)" class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 cursor-pointer">
                                <span>Unassigned</span>
                            </button>
                        </div>

                        <!-- Section Filter & Live Search Input -->
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <!-- Section Filter -->
                            <select onchange="location.href='assignments.php?section=' + this.value" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer">
                                <option value="all" <?= ($selectedSection ?? 'all') === 'all' ? 'selected' : ''; ?>>All Sections</option>
                                <?php foreach ($activeSections as $sec): ?>
                                    <option value="<?= htmlspecialchars($sec); ?>" <?= ($selectedSection ?? '') === $sec ? 'selected' : ''; ?>>
                                        Section <?= htmlspecialchars($sec); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Live Search Input -->
                            <div class="relative w-full md:w-64">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input 
                                    type="text" 
                                    id="assignmentSearchInput"
                                    placeholder="Search student or company..." 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- 3. Placement Table -->
                    <?php if (!empty($students)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Section</th>
                                        <th class="py-4 px-6">Company</th>
                                        <th class="py-4 px-6">Supervisor</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($students as $s): 
                                        $hasPlacement = !empty($s['supervisor_id']);
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors group assignment-row" data-status="<?= $hasPlacement ? 'assigned' : 'unassigned'; ?>">
                                            
                                            <!-- Student Info -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($s['avatar_url'])): ?>
                                                            <img src="<?= htmlspecialchars($s['avatar_url']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($s['name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-extrabold text-slate-950 text-sm intern-name"><?= htmlspecialchars($s['name']); ?></p>
                                                        <p class="text-[11px] text-slate-600 font-semibold mt-0.5 intern-id">ID: <?= htmlspecialchars($s['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($s['email']); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Section Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                    Section <?= htmlspecialchars($s['section'] ?? 'A'); ?>
                                                </span>
                                            </td>

                                            <!-- Host Company -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <?php if (!empty($s['company_name'])): ?>
                                                    <p class="font-bold text-slate-900 text-xs company-name"><?= htmlspecialchars($s['company_name']); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-medium mt-0.5"><?= htmlspecialchars($s['company_dept'] ?? 'Main Office'); ?></p>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Unassigned
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Assigned Supervisor -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <?php if (!empty($s['supervisor_name'])): ?>
                                                    <p class="font-bold text-slate-900 text-xs supervisor-name"><?= htmlspecialchars($s['supervisor_name']); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-medium mt-0.5">Company Supervisor</p>
                                                <?php else: ?>
                                                    <span class="text-slate-500 text-xs font-semibold italic">Pending Assignment</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap align-middle">
                                                <button 
                                                    type="button"
                                                    onclick="quickAssign(<?= $s['id']; ?>, '<?= addslashes($s['name']); ?>', <?= intval($s['company_id'] ?? 0); ?>, <?= intval($s['supervisor_id'] ?? 0); ?>)" 
                                                    class="px-3.5 py-1.5 text-xs font-bold <?= $hasPlacement ? 'text-slate-800 bg-white hover:bg-slate-100 border border-slate-300' : 'text-white bg-[#0F2854] hover:bg-blue-900 border border-[#0F2854]'; ?> rounded-xl shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                                                    <span><?= $hasPlacement ? 'Edit Link' : 'Assign Now →' ?></span>
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                👥
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No students found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">There are no registered students in this section.</p>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- MODAL: LINK STUDENT TO COMPANY & SUPERVISOR -->
    <div id="assignModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4 relative my-auto">
            
            <button type="button" onclick="closeModal('assignModal')" class="absolute top-5 right-5 w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>

            <div class="border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-950">Assign Student Placement</h3>
                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Select a company first to load its registered supervisors.</p>
            </div>

            <form method="POST" action="assignments.php" class="space-y-4 text-xs">
                <input type="hidden" name="assign_student" value="1">
                <input type="hidden" id="actionType" name="action_type" value="assign">

                <!-- 1. Select Student -->
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700 mb-1">Student Intern</label>
                    <select id="studentSelect" name="student_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                        <option value="" disabled selected>-- Choose Student --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['name']); ?> (Sec <?= htmlspecialchars($s['section'] ?? 'A'); ?> - ID: <?= htmlspecialchars($s['student_number'] ?? 'N/A'); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Select Host Company -->
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700 mb-1">Partner Company</label>
                    <select id="companySelect" name="company_id" required onchange="filterSupervisorsByCompany()" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                        <option value="" disabled selected>-- Choose Partner Company --</option>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?> - <?= htmlspecialchars($c['department'] ?? 'Main Office'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 3. Select Supervisor -->
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700 mb-1">Company Supervisor</label>
                    <select id="supervisorSelect" name="supervisor_id" required disabled class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] disabled:bg-slate-100 disabled:text-slate-400">
                        <option value="" disabled selected>-- Select Partner Company First --</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-200/70">
                    <button type="button" id="unassignBtn" onclick="submitUnassign()" class="hidden text-rose-700 hover:text-rose-950 font-bold text-xs cursor-pointer">
                        Unlink Placement
                    </button>
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" onclick="closeModal('assignModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold transition-colors shadow-xs cursor-pointer">Save Placement</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        const allSupervisors = <?= json_encode($supervisors ?? []); ?>;
        let currentStatusFilter = 'all';

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openAssignModal() {
            document.getElementById('studentSelect').value = '';
            document.getElementById('companySelect').value = '';
            document.getElementById('supervisorSelect').innerHTML = '<option value="" disabled selected>-- Select Partner Company First --</option>';
            document.getElementById('supervisorSelect').disabled = true;
            document.getElementById('actionType').value = 'assign';
            document.getElementById('unassignBtn').classList.add('hidden');
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

        function quickAssign(studentId, studentName, companyId, supervisorId) {
            document.getElementById('studentSelect').value = studentId;
            document.getElementById('actionType').value = 'assign';

            if (companyId > 0) {
                document.getElementById('companySelect').value = companyId;
                filterSupervisorsByCompany(supervisorId);
                document.getElementById('unassignBtn').classList.remove('hidden');
            } else {
                document.getElementById('companySelect').value = '';
                document.getElementById('supervisorSelect').innerHTML = '<option value="" disabled selected>-- Select Partner Company First --</option>';
                document.getElementById('supervisorSelect').disabled = true;
                document.getElementById('unassignBtn').classList.add('hidden');
            }

            openModal('assignModal');
        }

        function submitUnassign() {
            if (confirm("Are you sure you want to remove this student's placement link?")) {
                document.getElementById('actionType').value = 'unassign';
                document.getElementById('companySelect').removeAttribute('required');
                document.getElementById('supervisorSelect').removeAttribute('required');
                document.getElementById('assignModal').querySelector('form').submit();
            }
        }

        // Live Filtering by Status Tab
        function filterByStatus(status, btnElement) {
            currentStatusFilter = status;
            
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.className = 'filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 cursor-pointer';
            });
            btnElement.className = 'filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-[#0F2854] text-white border border-[#0F2854] cursor-pointer';

            applyCombinedFilter();
        }

        // Live Search Listener
        const searchInput = document.getElementById('assignmentSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', applyCombinedFilter);
        }

        function applyCombinedFilter() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const rows = document.querySelectorAll('.assignment-row');

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const name = row.querySelector('.intern-name')?.textContent.toLowerCase() || '';
                const id   = row.querySelector('.intern-id')?.textContent.toLowerCase() || '';
                const comp = row.querySelector('.company-name')?.textContent.toLowerCase() || '';
                const sup  = row.querySelector('.supervisor-name')?.textContent.toLowerCase() || '';

                const matchesSearch = name.includes(query) || id.includes(query) || comp.includes(query) || sup.includes(query);
                const matchesStatus = (currentStatusFilter === 'all') || (rowStatus === currentStatusFilter);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>