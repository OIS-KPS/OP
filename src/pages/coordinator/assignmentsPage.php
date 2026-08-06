<!-- src/pages/coordinator/assignmentsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Assignments - OJT Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Coordinator Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Right Side Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Scroll Area -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Page Header Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Student Placements & Assignments</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Link BSIT students to their assigned Host Company/Office and Industry Supervisor.</p>
                    </div>

                    <button onclick="openModal('assignModal')" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                        <span>+ Assign Student Placement</span>
                    </button>
                </div>

                <!-- Alert Messages -->
                <?php if (!empty($success)): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs p-3.5 rounded-2xl font-medium flex items-center justify-between">
                        <span>✓ <?= htmlspecialchars($success); ?></span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 font-bold">✕</button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3.5 rounded-2xl font-medium flex items-center justify-between">
                        <span>⚠️ <?= htmlspecialchars($error); ?></span>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 font-bold">✕</button>
                    </div>
                <?php endif; ?>

                <!-- Active Placements Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-800">Student Placement Roster</h3>
                        <span class="text-[11px] font-semibold text-slate-400">Total Interns: <?= count($students); ?></span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                    <th class="py-3 px-5">Student Intern</th>
                                    <th class="py-3 px-5">1. Host Company / Office</th>
                                    <th class="py-3 px-5">2. Assigned Industry Supervisor</th>
                                    <th class="py-3 px-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php foreach ($students as $s): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        
                                        <!-- Student Info -->
                                        <td class="py-3.5 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-extrabold text-xs shrink-0 border border-[#0F2854]/20">
                                                    <?= strtoupper(substr($s['name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($s['name']); ?></p>
                                                    <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($s['student_number']); ?> • <?= htmlspecialchars($s['program']); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Host Company -->
                                        <td class="py-3.5 px-5">
                                            <?php if ($s['company_name']): ?>
                                                <p class="font-bold text-slate-800"><?= htmlspecialchars($s['company_name']); ?></p>
                                                <p class="text-[11px] text-slate-400"><?= htmlspecialchars($s['company_dept'] ?? 'Main Office'); ?></p>
                                            <?php else: ?>
                                                <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">No Company Linked</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Assigned Supervisor -->
                                        <td class="py-3.5 px-5">
                                            <?php if ($s['supervisor_name']): ?>
                                                <p class="font-bold text-[#0F2854]"><?= htmlspecialchars($s['supervisor_name']); ?></p>
                                                <p class="text-[11px] text-slate-400">Industry Supervisor</p>
                                            <?php else: ?>
                                                <span class="text-slate-400 italic">Unassigned</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Quick Edit/Assign Action -->
                                        <td class="py-3.5 px-5 text-right">
                                            <button onclick="quickAssign(<?= $s['id']; ?>, '<?= addslashes($s['name']); ?>')" class="px-3 py-1 bg-slate-100 hover:bg-[#0F2854] hover:text-white text-slate-700 text-[11px] font-semibold rounded-lg transition-all cursor-pointer">
                                                <?= $s['supervisor_id'] ? 'Edit Link' : 'Assign Now →' ?>
                                            </button>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- MODAL: LINK STUDENT TO COMPANY & SUPERVISOR -->
    <!-- ======================================================= -->
    <div id="assignModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4 relative my-auto">
            
            <button onclick="closeModal('assignModal')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold">✕</button>

            <div>
                <h3 class="text-base font-bold text-slate-900">Assign Student Placement</h3>
                <p class="text-slate-400 text-xs mt-0.5">Select a company first to load its registered supervisors.</p>
            </div>

            <form method="POST" class="space-y-4 text-xs">
                <input type="hidden" name="assign_student" value="1">

                <!-- 1. Select Student -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">1. Select Student Intern</label>
                    <select id="studentSelect" name="student_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-[#0F2854]">
                        <option value="" disabled selected>-- Choose Student --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['name']); ?> (ID: <?= htmlspecialchars($s['student_number']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Select Host Company First -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">2. Select Partner Company / Office</label>
                    <select id="companySelect" name="company_id" required onchange="filterSupervisorsByCompany()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-[#0F2854]">
                        <option value="" disabled selected>-- Choose Host Company --</option>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?> - <?= htmlspecialchars($c['department'] ?? 'Main Office'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 3. Select Supervisor (Populated Dynamically) -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">3. Select Industry Supervisor</label>
                    <select id="supervisorSelect" name="supervisor_id" required disabled class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-[#0F2854] disabled:bg-slate-100 disabled:text-slate-400">
                        <option value="" disabled selected>-- Select Host Company First --</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('assignModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition-all">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-semibold transition-all shadow-2xs">Confirm Placement Link</button>
                </div>
            </form>

        </div>
    </div>

    <!-- JavaScript Dependent Dropdown Logic -->
    <script>
        // Pass PHP Supervisor list into JavaScript array
        const allSupervisors = <?= json_encode($supervisors); ?>;

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Dynamic Filtering: Select Company -> Populates Supervisors
        function filterSupervisorsByCompany() {
            const companyId = parseInt(document.getElementById('companySelect').value);
            const supSelect = document.getElementById('supervisorSelect');

            supSelect.innerHTML = '<option value="" disabled selected>-- Select Supervisor --</option>';

            // Filter supervisors belonging to the selected company
            const filtered = allSupervisors.filter(s => parseInt(s.company_id) === companyId);

            if (filtered.length > 0) {
                filtered.forEach(sup => {
                    const opt = document.createElement('option');
                    opt.value = sup.id;
                    opt.textContent = sup.name;
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

        // Quick button trigger for specific student
        function quickAssign(studentId, studentName) {
            document.getElementById('studentSelect').value = studentId;
            openModal('assignModal');
        }
    </script>

</body>
</html>