<!-- src/pages/coordinator/evaluationsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Student Evaluations - OJT Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-6 max-w-7xl w-full mx-auto space-y-6 flex-1">

                <!-- Header Title -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Final Student Evaluations</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Supervisor ratings and sign-off records.</p>
                    </div>
                </div>

                <!-- 1. Top Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Students</p>
                        <p class="text-2xl font-extrabold text-slate-900"><?= $totalCount; ?></p>
                        <p class="text-[11px] text-slate-400">Enrolled interns</p>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Completed</p>
                        <p class="text-2xl font-extrabold text-emerald-600"><?= $completedCount; ?></p>
                        <p class="text-[11px] text-slate-400">Signed evaluations</p>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending</p>
                        <p class="text-2xl font-extrabold text-amber-600"><?= $pendingCount; ?></p>
                        <p class="text-[11px] text-slate-400">Awaiting supervisor</p>
                    </div>

                </div>

                <!-- 2. Filters Bar -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4 text-xs">
                    
                    <form method="GET" action="evaluations.php" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        
                        <!-- Search Box -->
                        <div class="relative min-w-[220px]">
                            <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                            <input 
                                type="text" 
                                name="search" 
                                value="<?= htmlspecialchars($searchQuery); ?>" 
                                placeholder="Search student name or ID..." 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#0F2854]"
                            >
                        </div>

                        <!-- Company Filter -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700 text-[11px]">Company:</label>
                            <select name="company_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Companies</option>
                                <?php foreach ($companiesList as $comp): ?>
                                    <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($comp['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700 text-[11px]">Status:</label>
                            <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= ($selectedStatus ?? 'all') === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                <option value="Completed" <?= ($selectedStatus ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Pending" <?= ($selectedStatus ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            </select>
                        </div>

                        <button type="submit" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all">Filter</button>

                        <?php if (!empty($searchQuery) || ($selectedCompany ?? 'all') !== 'all' || ($selectedStatus ?? 'all') !== 'all'): ?>
                            <a href="evaluations.php" class="text-slate-400 hover:text-slate-600 font-semibold px-1 self-center">Reset</a>
                        <?php endif; ?>
                    </form>

                    <div class="text-slate-400 font-semibold text-[11px]">
                        Total: <span class="text-slate-800 font-bold"><?= count($filteredEvals); ?></span>
                    </div>
                </div>

                <!-- 3. Table Section -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                    <th class="py-3 px-5">Student Intern</th>
                                    <th class="py-3 px-5">Company / Supervisor</th>
                                    <th class="py-3 px-5">Rating</th>
                                    <th class="py-3 px-5">Status</th>
                                    <th class="py-3 px-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php if (empty($filteredEvals)): ?>
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-slate-400 italic">No evaluation records found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($filteredEvals as $eval): 
                                        $isCompleted = ($eval['status'] === 'Completed');
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <p class="font-bold text-slate-900"><?= htmlspecialchars($eval['student_name']); ?></p>
                                                <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($eval['student_number'] ?? 'N/A'); ?></p>
                                            </td>

                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($eval['company_name'] ?? 'Unassigned'); ?></p>
                                                <p class="text-[11px] text-slate-400"><?= htmlspecialchars($eval['supervisor_name'] ?? 'Pending Assignment'); ?></p>
                                            </td>

                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <?php if ($isCompleted && isset($eval['final_score'])): ?>
                                                    <div class="flex items-baseline gap-2">
                                                        <span class="text-sm font-extrabold text-[#0F2854]"><?= number_format($eval['final_score'], 1); ?>%</span>
                                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-[#0F2854] text-[10px] font-bold border border-blue-100">
                                                            <?= htmlspecialchars($eval['grade_equivalent'] ?? 'Passed'); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-slate-400 italic">Pending</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                                        ✓ Signed
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">
                                                        Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <a href="evaluations.php?view_id=<?= $eval['eval_id'] ?? $eval['student_id']; ?>" class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-semibold rounded-xl transition-all shadow-2xs">
                                                        View Details →
                                                    </a>
                                                <?php else: ?>
                                                    <button disabled class="px-3.5 py-1.5 bg-slate-100 text-slate-400 text-[11px] font-semibold rounded-xl cursor-not-allowed">
                                                        Pending
                                                    </button>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Evaluation Detail Modal -->
    <?php if ($activeEval): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full overflow-hidden flex flex-col max-h-[90vh] my-auto">
                
                <!-- Modal Top Header -->
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-900">
                        Evaluation: <span class="text-[#0F2854]"><?= htmlspecialchars($activeEval['student_name']); ?></span>
                    </h3>
                    <a href="evaluations.php" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs">✕</a>
                </div>

                <!-- Modal Content -->
                <div class="p-5 space-y-4 overflow-y-auto text-xs">
                    
                    <!-- Metadata -->
                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Student Intern</p>
                            <p class="font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($activeEval['student_name']); ?></p>
                            <p class="text-[11px] text-slate-500">ID: <?= htmlspecialchars($activeEval['student_number'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Company & Supervisor</p>
                            <p class="font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($activeEval['company_name'] ?? 'Host Agency'); ?></p>
                            <p class="text-[11px] text-slate-500"><?= htmlspecialchars($activeEval['supervisor_name'] ?? 'Supervisor'); ?></p>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="space-y-2">
                        <p class="text-[10px] font-bold uppercase text-slate-400">Score Breakdown</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Technical:</span>
                                <span class="font-bold text-[#0F2854]"><?= number_format($activeEval['technical_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Work Ethics:</span>
                                <span class="font-bold text-[#0F2854]"><?= number_format($activeEval['work_ethics_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Communication:</span>
                                <span class="font-bold text-[#0F2854]"><?= number_format($activeEval['communication_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Punctuality:</span>
                                <span class="font-bold text-[#0F2854]"><?= number_format($activeEval['punctuality_score'] ?? 0, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Final Score -->
                    <div class="p-3.5 bg-blue-50/50 rounded-xl border border-blue-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-[#0F2854]">Final Grade</p>
                            <p class="text-xl font-extrabold text-[#0F2854]"><?= number_format($activeEval['final_score'] ?? 0, 1); ?>%</p>
                        </div>
                        <div>
                            <span class="px-3 py-1 rounded-full bg-[#0F2854] text-white font-bold text-xs">
                                Grade: <?= htmlspecialchars($activeEval['grade_equivalent'] ?? '1.0'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Supervisor Remarks -->
                    <?php if (!empty($activeEval['feedback'])): ?>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold uppercase text-slate-400">Supervisor Remarks</p>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-700 italic">
                                "<?= htmlspecialchars($activeEval['feedback']); ?>"
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Footer Close -->
                <div class="p-3 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                    <a href="evaluations.php" class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs">
                        Close
                    </a>
                </div>

            </div>
        </div>
    <?php endif; ?>

</body>
</html>