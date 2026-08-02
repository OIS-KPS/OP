<!-- src/pages/coordinator/usersPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - OJT Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

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

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button onclick="toggleModal('addSingleUserModal')" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <span>+</span>
                            <span>Add Single User</span>
                        </button>

                        <button onclick="toggleModal('bulkImportModal')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <span>📊</span>
                            <span>Bulk Import (.xlsx)</span>
                        </button>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (!empty($success)): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs p-3.5 rounded-2xl font-medium">
                        ✓ <?= htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3.5 rounded-2xl font-medium">
                        ⚠️ <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Navigation Tabs -->
                <div class="flex items-center gap-2 border-b border-slate-200/80 pb-1">
                    <a href="users.php?tab=students" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= $tab === 'students' ? 'bg-[#0F2854] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                        🎓 Students (<?= count($students); ?>)
                    </a>
                    <a href="users.php?tab=supervisors" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= $tab === 'supervisors' ? 'bg-[#0F2854] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                        👔 Industry Supervisors (<?= count($supervisors); ?>)
                    </a>
                    <a href="users.php?tab=companies" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= $tab === 'companies' ? 'bg-[#0F2854] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                        🏢 Partner Companies (<?= count($companies); ?>)
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
                                    <?php foreach ($students as $s): ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3.5 px-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-extrabold text-xs shrink-0 border border-[#0F2854]/20">
                                                        <?= strtoupper(substr($s['name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900"><?= htmlspecialchars($s['name']); ?></p>
                                                        <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($s['student_number']); ?> • <?= htmlspecialchars($s['email']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-5 font-bold text-slate-800"><?= htmlspecialchars($s['program'] ?? 'BSIT'); ?></td>
                                            <td class="py-3.5 px-5 text-slate-700 font-medium"><?= htmlspecialchars($s['company_name'] ?? 'Unassigned'); ?></td>
                                            <td class="py-3.5 px-5 text-slate-700"><?= htmlspecialchars($s['supervisor_name'] ?? 'Unassigned'); ?></td>
                                            <td class="py-3.5 px-5 text-right">
                                                <?php if (($s['supervisor_name'] ?? 'Unassigned') !== 'Unassigned'): ?>
                                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">Active / Placed</span>
                                                <?php else: ?>
                                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">Pending Assignment</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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
                                    <?php foreach ($supervisors as $sup): ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3.5 px-5 font-bold text-slate-900"><?= htmlspecialchars($sup['name']); ?></td>
                                            <td class="py-3.5 px-5 text-slate-500"><?= htmlspecialchars($sup['email']); ?></td>
                                            <td class="py-3.5 px-5 font-medium text-slate-800"><?= htmlspecialchars($sup['company_name'] ?? 'N/A'); ?></td>
                                            <td class="py-3.5 px-5 text-right font-bold text-[#0F2854]"><?= $sup['assigned_interns'] ?? 0; ?> Intern(s)</td>
                                        </tr>
                                    <?php endforeach; ?>
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
                                    <?php foreach ($companies as $comp): ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3.5 px-5 font-bold text-slate-900"><?= htmlspecialchars($comp['name']); ?></td>
                                            <td class="py-3.5 px-5 text-slate-500"><?= htmlspecialchars($comp['department'] ?? 'Main Office'); ?></td>
                                            <td class="py-3.5 px-5 text-right font-bold text-[#0F2854]"><?= $comp['total_interns'] ?? 0; ?> Intern(s)</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <!-- 1. SINGLE USER CREATION MODAL -->
    <div id="addSingleUserModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
            
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Add New Account</h3>
                <button type="button" onclick="toggleModal('addSingleUserModal')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>

            <form method="POST" class="space-y-3.5 text-xs">
                <input type="hidden" name="action" value="create_single_user">

                <!-- Account Role Selector -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Account Role</label>
                    <select id="userRoleSelect" name="role" onchange="toggleRoleFields()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                        <option value="student">🎓 Student Intern</option>
                        <option value="supervisor">👔 Industry Supervisor</option>
                    </select>
                </div>

                <!-- Common Field: Full Name -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" required placeholder="e.g., Katelyn Coming" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                </div>

                <!-- Student Specific Fields -->
                <div id="studentFields" class="space-y-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Student ID Number</label>
                        <input type="text" id="studentNumberInput" name="student_number" placeholder="e.g., 20231053" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Institutional Email (@nbsc.edu.ph)</label>
                        <input type="email" id="studentEmailInput" name="email" placeholder="20231053@nbsc.edu.ph" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                    </div>
                </div>

                <!-- Supervisor Specific Fields -->
                <div id="supervisorFields" class="space-y-3 hidden">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Work Email Address</label>
                        <input type="email" id="supervisorEmailInput" name="supervisor_email" placeholder="e.g., supervisor@company.com" disabled class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:border-[#0F2854]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Host Company / Agency</label>
                        <select id="supervisorCompanySelect" name="company_id" disabled class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-[#0F2854]">
                            <option value="">-- Select Partner Company --</option>
                            <?php foreach ($companies as $comp): ?>
                                <option value="<?= $comp['id']; ?>"><?= htmlspecialchars($comp['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="toggleModal('addSingleUserModal')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-semibold hover:bg-slate-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2854] text-white rounded-xl font-semibold hover:bg-blue-900 transition-colors">Create Account</button>
                </div>
            </form>
        </div>
    </div>


    <!-- 2. BULK IMPORT (.XLSX) MODAL -->
    <div id="bulkImportModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden p-6 space-y-5">
            
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Bulk Import Students</h3>
                    <p class="text-slate-500 text-[11px]">Upload an Excel spreadsheet to batch enroll students.</p>
                </div>
                <button type="button" onclick="toggleModal('bulkImportModal')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>

            <form method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                <input type="hidden" name="action" value="bulk_import_students">

                <!-- Step 1 -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center text-[10px]">1</span>
                        <span>Download System Template</span>
                    </label>
                    <div class="pt-1">
                        <button type="button" onclick="alert('Downloading student_template.xlsx...')" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-colors inline-flex items-center gap-1.5">
                            <span>📥</span> Download .xlsx Template
                        </button>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center text-[10px]">2</span>
                        <span>Upload Completed File</span>
                    </label>
                    <input type="file" accept=".xlsx, .xls, .csv" required onchange="showMockPreview()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#0F2854] file:text-white hover:file:bg-blue-900 cursor-pointer">
                </div>

                <!-- Step 3: Mock Preview Area -->
                <div id="importPreviewArea" class="space-y-1.5 pt-1 hidden">
                    <label class="font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center text-[10px]">3</span>
                        <span>Preview Before Creating</span>
                    </label>
                    <p class="text-[11px] text-slate-500">3 students detected. Review before importing:</p>

                    <div class="border border-slate-200 rounded-xl overflow-hidden max-h-40 overflow-y-auto">
                        <table class="w-full text-left border-collapse text-[11px]">
                            <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600">
                                <tr>
                                    <th class="p-2">#</th>
                                    <th class="p-2">STUDENT EMAIL</th>
                                    <th class="p-2">STUDENT ID</th>
                                    <th class="p-2 text-right">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="p-2">1</td>
                                    <td class="p-2 font-medium">20231053@nbsc.edu.ph</td>
                                    <td class="p-2">20231053</td>
                                    <td class="p-2 text-right text-emerald-600 font-bold">✓ Valid</td>
                                </tr>
                                <tr>
                                    <td class="p-2">2</td>
                                    <td class="p-2 font-medium">20231052@nbsc.edu.ph</td>
                                    <td class="p-2">20231052</td>
                                    <td class="p-2 text-right text-emerald-600 font-bold">✓ Valid</td>
                                </tr>
                                <tr>
                                    <td class="p-2">3</td>
                                    <td class="p-2 font-medium">20231969@nbsc.edu.ph</td>
                                    <td class="p-2">20231969</td>
                                    <td class="p-2 text-right text-emerald-600 font-bold">✓ Valid</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                    <span class="text-[11px] text-slate-400 font-semibold">Step 4: Confirm Import</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="toggleModal('bulkImportModal')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-semibold hover:bg-slate-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition-colors">Confirm & Send Emails</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="/ICS-PORTAL/public/js/userModal.js"></script>

</body>
</html>