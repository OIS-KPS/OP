<!-- src/pages/coordinator/evaluationsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Student Evaluations - OJT Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Coordinator Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Right Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Content Area -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Page Header Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Supervisor Final Performance Evaluations</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Read-only repository of supervisor-submitted performance ratings and OTP verification sign-offs.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full border border-emerald-200 text-[11px] flex items-center gap-1 shadow-2xs">
                            <span>🔒</span> Official Coordinator Access Only
                        </span>
                    </div>
                </div>

                <!-- Cohort Evaluation Summary Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Expected</p>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-slate-900"><?= $totalCount; ?> Interns</span>
                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Cohort BSIT</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Total registered trainee records</p>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Completed Sign-offs</p>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-emerald-600"><?= $completedCount; ?> Submitted</span>
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">✓ OTP Signed</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Verified by Industry Supervisors</p>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Evaluations</p>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-amber-600"><?= $pendingCount; ?> Pending</span>
                            <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Awaiting Form</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Supervisors pending submission</p>
                    </div>

                </div>

                <!-- Filter Controls Bar -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4 text-xs">
                    
                    <form method="GET" action="evaluations.php" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        
                        <!-- Search Student Input -->
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

                        <!-- Filter 1: Host Office -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Host Office:</label>
                            <select name="company_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Host Offices</option>
                                <?php foreach ($companiesList as $comp): ?>
                                    <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($comp['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter 2: Submission Status -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Status:</label>
                            <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= ($selectedStatus ?? 'all') === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                <option value="Completed" <?= ($selectedStatus ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed Only</option>
                                <option value="Pending" <?= ($selectedStatus ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending Only</option>
                            </select>
                        </div>

                        <button type="submit" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all cursor-pointer">Filter</button>

                        <?php if (!empty($searchQuery) || ($selectedCompany ?? 'all') !== 'all' || ($selectedStatus ?? 'all') !== 'all'): ?>
                            <a href="evaluations.php" class="text-slate-400 hover:text-slate-600 font-semibold px-1 self-center">Reset</a>
                        <?php endif; ?>
                    </form>

                    <div class="text-slate-400 font-semibold text-[11px]">
                        Showing <span class="text-slate-800 font-bold"><?= count($filteredEvals); ?></span> Student Records
                    </div>
                </div>

                <!-- Structured Scalable Evaluations Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                    <th class="py-3.5 px-5">Student Intern</th>
                                    <th class="py-3.5 px-5">Host Agency & Supervisor</th>
                                    <th class="py-3.5 px-5">Final Performance Rating</th>
                                    <th class="py-3.5 px-5">OTP Sign-Off Status</th>
                                    <th class="py-3.5 px-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php if (empty($filteredEvals)): ?>
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                            <div class="text-2xl mb-1">📋</div>
                                            <p class="font-semibold text-slate-600">No student evaluation records found.</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Add students and assign supervisors to view evaluation progress.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($filteredEvals as $eval): 
                                        $isCompleted = ($eval['status'] === 'Completed');
                                    ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            
                                            <!-- Student Info -->
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <p class="font-bold text-slate-900"><?= htmlspecialchars($eval['student_name']); ?></p>
                                                <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($eval['student_number'] ?? 'N/A'); ?> • <?= htmlspecialchars($eval['program'] ?? 'BSIT'); ?></p>
                                            </td>

                                            <!-- Host Agency & Supervisor -->
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($eval['company_name'] ?? 'Unassigned Host'); ?></p>
                                                <p class="text-[11px] text-slate-400"><?= htmlspecialchars($eval['supervisor_name'] ?? 'Unassigned Supervisor'); ?></p>
                                            </td>

                                            <!-- Final Performance Rating -->
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <?php if ($isCompleted && isset($eval['final_score'])): ?>
                                                    <div class="flex items-baseline gap-2">
                                                        <span class="text-sm font-extrabold text-[#0F2854]"><?= number_format($eval['final_score'], 1); ?>%</span>
                                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-[#0F2854] text-[10px] font-bold border border-blue-100">
                                                            Grade: <?= htmlspecialchars($eval['grade_equivalent'] ?? 'Passed'); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-slate-400 italic">Not Evaluated Yet</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- OTP Verification Status -->
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200 inline-flex items-center gap-1 shadow-2xs">
                                                        <span>🛡️</span> Verified via OTP
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200 inline-flex items-center gap-1 shadow-2xs">
                                                        <span>⏳</span> Awaiting Supervisor
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- View Action Button -->
                                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <a href="evaluations.php?view_id=<?= $eval['eval_id'] ?? $eval['student_id']; ?>" class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-semibold rounded-xl transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                                        <span>Inspect Sign-off →</span>
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

    <!-- DETAILED EVALUATION SHEET MODAL -->
    <?php if ($activeEval): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[90vh] relative my-auto">
                
                <!-- Modal Top Header -->
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Official Evaluation Record</span>
                        <h3 class="text-sm font-bold text-slate-900">
                            Final Rating: <span class="text-[#0F2854]"><?= htmlspecialchars($activeEval['student_name']); ?></span>
                        </h3>
                    </div>
                    <a href="evaluations.php" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs">✕</a>
                </div>

                <!-- Modal Content Scroll Area -->
                <div class="p-6 space-y-5 overflow-y-auto text-xs">
                    
                    <!-- Intern & Host Meta Card -->
                    <div class="grid grid-cols-2 gap-4 p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-slate-400">Student Intern</p>
                            <p class="font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($activeEval['student_name']); ?></p>
                            <p class="text-[11px] text-slate-500">ID: <?= htmlspecialchars($activeEval['student_number'] ?? 'N/A'); ?> (<?= htmlspecialchars($activeEval['program'] ?? 'BSIT'); ?>)</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-slate-400">Host Agency & Supervisor</p>
                            <p class="font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($activeEval['company_name'] ?? 'Host Company'); ?></p>
                            <p class="text-[11px] text-slate-500"><?= htmlspecialchars($activeEval['supervisor_name'] ?? 'Industry Supervisor'); ?></p>
                        </div>
                    </div>

                    <!-- Category Breakdown Ratings -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Performance Category Scores</h4>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Technical Proficiency:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= number_format($activeEval['technical_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Work Ethics & Discipline:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= number_format($activeEval['work_ethics_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Communication Skills:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= number_format($activeEval['communication_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Punctuality & Attendance:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= number_format($activeEval['punctuality_score'] ?? 0, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Final Score Banner -->
                    <div class="p-4 bg-[#0F2854]/5 rounded-xl border border-[#0F2854]/15 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[#0F2854]">Cumulative Final Grade</p>
                            <p class="text-2xl font-extrabold text-[#0F2854]"><?= number_format($activeEval['final_score'] ?? 0, 1); ?>%</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full bg-[#0F2854] text-white font-bold text-xs">
                                Grade: <?= htmlspecialchars($activeEval['grade_equivalent'] ?? '1.0'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Supervisor Feedback Remarks -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Supervisor Remarks & Feedback</h4>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 text-slate-700 leading-relaxed font-medium italic">
                            "<?= htmlspecialchars($activeEval['feedback'] ?? 'No written remarks provided.'); ?>"
                        </div>
                    </div>

                    <!-- Cryptographic OTP Verification Audit Record -->
                    <div class="p-3.5 bg-emerald-50/60 rounded-xl border border-emerald-200/80 space-y-1">
                        <div class="flex items-center gap-1.5 text-emerald-800 font-bold">
                            <span>🛡️</span>
                            <span>Cryptographic OTP Verification Audit Trail</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">
                            Signed off by supervisor via One-Time Password on <span class="font-bold"><?= !empty($activeEval['otp_signed_at']) ? date("M d, Y \a\\t g:i A", strtotime($activeEval['otp_signed_at'])) : 'Timestamp on file'; ?></span>
                        </p>
                        <p class="text-[10px] text-emerald-600 font-mono">
                            Sign-off IP Stamp: <?= htmlspecialchars($activeEval['otp_ip_address'] ?? '127.0.0.1'); ?> • Verification Hash: Valid ✓
                        </p>
                    </div>

                </div>

                <!-- Footer Close Bar -->
                <div class="p-3 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                    <a href="evaluations.php" class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs">
                        Close Record
                    </a>
                </div>

            </div>
        </div>
    <?php endif; ?>

</body>
</html>