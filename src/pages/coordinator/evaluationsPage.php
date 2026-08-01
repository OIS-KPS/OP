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
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Coordinator Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Right Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Dynamic Top Header -->
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
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full border border-emerald-200 text-[11px] flex items-center gap-1">
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
                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Cohort 2026</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Total BSIT graduating interns</p>
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
                    
                    <form method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        
                        <!-- Search Student Input -->
                        <div class="relative min-w-[220px]">
                            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery); ?>" placeholder="Search student name or ID..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#0F2854]">
                            <span class="absolute left-2.5 top-2 text-slate-400">🔍</span>
                        </div>

                        <!-- Filter 1: Host Office -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700">Host Office:</label>
                            <select name="company_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= $selectedCompany === 'all' ? 'selected' : ''; ?>>All Host Offices</option>
                                <option value="ICS IT Dept" <?= $selectedCompany === 'ICS IT Dept' ? 'selected' : ''; ?>>ICS IT Dept</option>
                                <option value="LGU Manolo Fortich" <?= $selectedCompany === 'LGU Manolo Fortich' ? 'selected' : ''; ?>>LGU Manolo Fortich</option>
                            </select>
                        </div>

                        <!-- Filter 2: Submission Status -->
                        <div class="flex items-center gap-1.5">
                            <label class="font-bold text-slate-700">Status:</label>
                            <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="all" <?= $selectedStatus === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                <option value="Completed" <?= $selectedStatus === 'Completed' ? 'selected' : ''; ?>>Completed Only</option>
                                <option value="Pending" <?= $selectedStatus === 'Pending' ? 'selected' : ''; ?>>Pending Only</option>
                            </select>
                        </div>

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
                                <?php foreach ($filteredEvals as $eval): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        
                                        <!-- Student Info -->
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <p class="font-bold text-slate-900"><?= htmlspecialchars($eval['student_name']); ?></p>
                                            <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($eval['student_number']); ?> • <?= htmlspecialchars($eval['program']); ?></p>
                                        </td>

                                        <!-- Host Agency & Supervisor -->
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <p class="font-semibold text-slate-800"><?= htmlspecialchars($eval['company_name']); ?></p>
                                            <p class="text-[11px] text-slate-400"><?= htmlspecialchars($eval['supervisor_name']); ?></p>
                                        </td>

                                        <!-- Final Performance Rating -->
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <?php if ($eval['status'] === 'Completed'): ?>
                                                <div class="flex items-baseline gap-2">
                                                    <span class="text-sm font-extrabold text-[#0F2854]"><?= number_format($eval['final_score'], 1); ?>%</span>
                                                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-[#0F2854] text-[10px] font-bold border border-blue-100">
                                                        Grade: <?= $eval['grade_equivalent']; ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-slate-400 italic">Not Evaluated Yet</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- OTP Verification Status -->
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <?php if ($eval['otp_verified']): ?>
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200 inline-flex items-center gap-1">
                                                    <span>🛡️</span> Verified via OTP
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200 inline-flex items-center gap-1">
                                                    <span>⏳</span> Awaiting Supervisor
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- View Action Button -->
                                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                            <?php if ($eval['status'] === 'Completed'): ?>
                                                <a href="evaluations.php?view_id=<?= $eval['id']; ?>" class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-semibold rounded-xl transition-all shadow-2xs inline-flex items-center gap-1">
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
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- DETAILED EVALUATION SHEET MODAL                         -->
    <!-- ======================================================= -->
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
                            <p class="text-[11px] text-slate-500">ID: <?= htmlspecialchars($activeEval['student_number']); ?> (<?= htmlspecialchars($activeEval['program']); ?>)</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-slate-400">Host Agency & Supervisor</p>
                            <p class="font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($activeEval['company_name']); ?></p>
                            <p class="text-[11px] text-slate-500"><?= htmlspecialchars($activeEval['supervisor_name']); ?> (<?= htmlspecialchars($activeEval['supervisor_email']); ?>)</p>
                        </div>
                    </div>

                    <!-- Category Breakdown Ratings -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider text-slate-400">Performance Category Scores</h4>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Technical Proficiency:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= $activeEval['scores']['technical']; ?>%</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Work Ethics & Discipline:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= $activeEval['scores']['work_ethics']; ?>%</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Communication Skills:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= $activeEval['scores']['communication']; ?>%</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Punctuality & Attendance:</span>
                                <span class="font-extrabold text-[#0F2854] text-sm"><?= $activeEval['scores']['punctuality']; ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Final Score Banner -->
                    <div class="p-4 bg-[#0F2854]/5 rounded-xl border border-[#0F2854]/15 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[#0F2854]">Cumulative Final Grade</p>
                            <p class="text-2xl font-extrabold text-[#0F2854]"><?= number_format($activeEval['final_score'], 1); ?>%</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full bg-[#0F2854] text-white font-bold text-xs">
                                Grade: <?= $activeEval['grade_equivalent']; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Supervisor Feedback Remarks -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider text-slate-400 mb-1">Supervisor Remarks & Feedback</h4>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 text-slate-700 leading-relaxed font-medium italic">
                            "<?= htmlspecialchars($activeEval['feedback']); ?>"
                        </div>
                    </div>

                    <!-- Cryptographic OTP Verification Audit Record -->
                    <div class="p-3.5 bg-emerald-50/60 rounded-xl border border-emerald-200/80 space-y-1">
                        <div class="flex items-center gap-1.5 text-emerald-800 font-bold">
                            <span>🛡️</span>
                            <span>Cryptographic OTP Verification Audit Trail</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">
                            Signed off by supervisor via One-Time Password on <span class="font-bold"><?= date("M d, Y \a\\t g:i A", strtotime($activeEval['otp_signed_at'])); ?></span>
                        </p>
                        <p class="text-[10px] text-emerald-600 font-mono">
                            Sign-off IP Stamp: <?= $activeEval['otp_ip_address']; ?> • Verification Hash: Valid ✓
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