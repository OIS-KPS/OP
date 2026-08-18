<!-- src/pages/coordinator/usersPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - OJT Coordinator Portal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component (Coordinator Sidebar) -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Right Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Body -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Page Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">User Accounts & Directory</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Manage accounts for BSIT Interns, Industry Supervisors, and Partner Host Offices.</p>
                    </div>

                    <!-- Action Modal Triggers -->
                    <div class="flex items-center gap-2">
                        <button onclick="toggleModal('bulkImportModal')" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <span>📊</span>
                            <span>Import CSV</span>
                        </button>
                        <button onclick="toggleModal('addStudentModal')" class="px-3.5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <span>+</span>
                            <span>Add Student</span>
                        </button>
                        <button onclick="toggleModal('addSupervisorModal')" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <span>+</span>
                            <span>Add Supervisor</span>
                        </button>
                        <button onclick="toggleModal('addCompanyModal')" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <span>+</span>
                            <span>Add Company</span>
                        </button>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (!empty($success)): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs p-3.5 rounded-2xl font-medium flex items-center gap-2 shadow-2xs">
                        <span class="font-bold">✓</span>
                        <span><?= htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3.5 rounded-2xl font-medium flex items-center gap-2 shadow-2xs">
                        <span class="font-bold">✕</span>
                        <span><?= htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Navigation Tabs -->
                <div class="flex items-center gap-2 border-b border-slate-200/80 pb-1">
                    <a href="users.php?tab=students" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= ($tab ?? 'students') === 'students' ? 'bg-[#0F2854] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                        🎓 Students (<?= count($students ?? []); ?>)
                    </a>
                    <a href="users.php?tab=supervisors" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= ($tab ?? '') === 'supervisors' ? 'bg-[#0F2854] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                        👔 Industry Supervisors (<?= count($supervisors ?? []); ?>)
                    </a>
                    <a href="users.php?tab=companies" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= ($tab ?? '') === 'companies' ? 'bg-[#0F2854] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                        🏢 Partner Companies (<?= count($companies ?? []); ?>)
                    </a>
                </div>

                <!-- Tab 1: Students Table -->
                <?php if ($tab === 'students'): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Student Info</th>
                                        <th class="py-3 px-5">Program</th>
                                        <th class="py-3 px-5">Assigned Company</th>
                                        <th class="py-3 px-5">Assigned Supervisor</th>
                                        <th class="py-3 px-5 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php if (empty($students)): ?>
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-slate-400 italic">No students registered yet. Click "+ Add Student" or "Import CSV" above to get started.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($students as $s): ?>
                                            <tr class="hover:bg-slate-50/80 transition-colors">
                                                <td class="py-3.5 px-5">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-extrabold text-xs shrink-0 border border-[#0F2854]/20 overflow-hidden">
                                                            <?php if (!empty($s['avatar_url'])): ?>
                                                                <img src="<?= htmlspecialchars($s['avatar_url']); ?>" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <?= strtoupper(substr($s['name'], 0, 1)); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <p class="font-bold text-slate-900"><?= htmlspecialchars($s['name']); ?></p>
                                                            <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($s['student_number']); ?> • <?= htmlspecialchars($s['email']); ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-5 font-bold text-slate-800"><?= htmlspecialchars($s['program']); ?></td>
                                                <td class="py-3.5 px-5 text-slate-700 font-medium"><?= htmlspecialchars($s['company_name'] ?? 'Unassigned'); ?></td>
                                                <td class="py-3.5 px-5 text-slate-700"><?= htmlspecialchars($s['supervisor_name'] ?? 'Unassigned'); ?></td>
                                                <td class="py-3.5 px-5 text-right">
                                                    <?php if (($s['supervisor_name'] ?? 'Unassigned') !== 'Unassigned'): ?>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">● Placed</span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">● Pending Assignment</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tab 2: Supervisors Table -->
                <?php if ($tab === 'supervisors'): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Supervisor Name</th>
                                        <th class="py-3 px-5">Email Address</th>
                                        <th class="py-3 px-5">Host Agency / Company</th>
                                        <th class="py-3 px-5 text-right">Assigned Interns</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php if (empty($supervisors)): ?>
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-slate-400 italic">No supervisors registered yet. Click "+ Add Supervisor" to create one.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($supervisors as $sup): ?>
                                            <tr class="hover:bg-slate-50/80 transition-colors">
                                                <td class="py-3.5 px-5">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-extrabold text-xs shrink-0 border border-[#0F2854]/20 overflow-hidden">
                                                            <?php if (!empty($sup['avatar_url'])): ?>
                                                                <img src="<?= htmlspecialchars($sup['avatar_url']); ?>" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <?= strtoupper(substr($sup['name'], 0, 1)); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <span class="font-bold text-slate-900"><?= htmlspecialchars($sup['name']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-5 text-slate-500"><?= htmlspecialchars($sup['email']); ?></td>
                                                <td class="py-3.5 px-5 font-medium text-slate-800"><?= htmlspecialchars($sup['company_name'] ?? 'N/A'); ?></td>
                                                <td class="py-3.5 px-5 text-right font-bold text-[#0F2854]"><?= intval($sup['assigned_interns']); ?> Intern(s)</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tab 3: Companies Table -->
                <?php if ($tab === 'companies'): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Company / Host Agency</th>
                                        <th class="py-3 px-5">Department / Office</th>
                                        <th class="py-3 px-5 text-right">Active Interns</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php if (empty($companies)): ?>
                                        <tr>
                                            <td colspan="3" class="py-8 text-center text-slate-400 italic">No partner companies registered yet. Click "+ Add Company" to add a host agency.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($companies as $comp): ?>
                                            <tr class="hover:bg-slate-50/80 transition-colors">
                                                <td class="py-3.5 px-5 font-bold text-slate-900"><?= htmlspecialchars($comp['name']); ?></td>
                                                <td class="py-3.5 px-5 text-slate-500"><?= htmlspecialchars($comp['department'] ?? 'Main Office'); ?></td>
                                                <td class="py-3.5 px-5 text-right font-bold text-[#0F2854]"><?= intval($comp['total_interns']); ?> Intern(s)</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <!-- MODAL 1: ADD STUDENT -->
    <div id="addStudentModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Add New Student Account</h3>
            <form action="users.php" method="POST" class="space-y-3 text-xs">
                <input type="hidden" name="action" value="create_student">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" required placeholder="e.g., Katelyn Coming" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Student ID Number</label>
                    <input type="text" name="student_number" required placeholder="e.g., 20231053" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Institutional Email</label>
                    <input type="email" name="email" required placeholder="20231053@nbsc.edu.ph" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Academic Program</label>
                    <input type="text" name="program" value="BSIT" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="toggleModal('addStudentModal')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-semibold cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] text-white rounded-xl font-semibold cursor-pointer">Save Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: ADD SUPERVISOR -->
    <div id="addSupervisorModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Add Industry Supervisor</h3>
            <form action="users.php" method="POST" class="space-y-3 text-xs">
                <input type="hidden" name="action" value="create_supervisor">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Supervisor Full Name</label>
                    <input type="text" name="name" required placeholder="e.g., Jane Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" required placeholder="supervisor@company.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Host Agency / Company</label>
                    <select name="company_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                        <option value="">-- Select Partner Company --</option>
                        <?php foreach ($companies as $comp): ?>
                            <option value="<?= $comp['id']; ?>"><?= htmlspecialchars($comp['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="toggleModal('addSupervisorModal')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-semibold cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] text-white rounded-xl font-semibold cursor-pointer">Save Supervisor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: ADD COMPANY -->
    <div id="addCompanyModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Add Partner Company / Office</h3>
            <form action="users.php" method="POST" class="space-y-3 text-xs">
                <input type="hidden" name="action" value="create_company">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Company / Office Name</label>
                    <input type="text" name="name" required placeholder="e.g., LGU Manolo Fortich" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Department / Branch</label>
                    <input type="text" name="department" placeholder="e.g., IT Department / SASDD" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="toggleModal('addCompanyModal')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-semibold cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold cursor-pointer">Save Company</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: BULK IMPORT -->
    <div id="bulkImportModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Bulk Import Students (.csv)</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Upload a standard CSV file with columns formatted in this order: <br>
                <span class="font-bold text-slate-700">Col A: Full Name | Col B: Student ID | Col C: Institutional Email | Col D: Program</span>
            </p>
            
            <form action="users.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                <input type="hidden" name="action" value="bulk_import_students">
                
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-5 text-center bg-slate-50 hover:border-[#0F2854] transition-colors">
                    <input type="file" name="excel_file" accept=".csv, .txt" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0F2854] file:text-white hover:file:bg-blue-900 cursor-pointer">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="toggleModal('bulkImportModal')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-semibold cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-semibold cursor-pointer flex items-center gap-1.5">
                        <span>🚀 Process Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>

<?php include __DIR__ . '/../../components/password_change_popup.php'; ?>
</body>
</html>