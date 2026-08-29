<!-- src/pages/coordinator/evaluationsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Student Evaluations - OJT Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100/70 text-slate-900 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Workspace -->
            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Header Actions Card -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-300 shadow-xs">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Final Student Evaluations</h1>
                        <p class="text-xs font-semibold text-slate-600 mt-1">
                            Review supervisor assessment ratings, final performance grades, and sign-off records.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <?= count($filteredEvals ?? []); ?> Total <?= count($filteredEvals ?? []) === 1 ? 'Record' : 'Records'; ?>
                        </span>
                    </div>
                </div>

                <!-- 1. Top Stat Cards (Student Dashboard Sizing & Hierarchy) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    
                    <div class="bg-white p-5 rounded-2xl border border-slate-300 shadow-xs flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">Total Enrolled</p>
                            <p class="text-2xl font-extrabold text-slate-900 mt-1"><?= $totalCount; ?></p>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Enrolled intern cohort</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-[#0F2854] font-extrabold flex items-center justify-center text-base border border-blue-200">
                            👥
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-300 shadow-xs flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">Completed</p>
                            <p class="text-2xl font-extrabold text-emerald-700 mt-1"><?= $completedCount; ?></p>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Signed evaluations</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-base border border-emerald-200">
                            ✓
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-300 shadow-xs flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">Pending</p>
                            <p class="text-2xl font-extrabold text-amber-700 mt-1"><?= $pendingCount; ?></p>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Awaiting supervisor</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-800 font-extrabold flex items-center justify-center text-base border border-amber-200">
                            ⏱
                        </div>
                    </div>

                </div>

                <!-- 2. Integrated Filter & Instant Search Toolbar -->
                <form id="filterForm" method="GET" action="evaluations.php" class="bg-white p-4 rounded-2xl border border-slate-300 shadow-xs flex flex-col md:flex-row items-center gap-3 text-xs">
                    
                    <!-- Search Box -->
                    <div class="relative flex-1 w-full">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input 
                            type="text" 
                            id="searchInput"
                            name="search" 
                            value="<?= htmlspecialchars($searchQuery ?? ''); ?>" 
                            placeholder="Type student name or ID to filter instantly..." 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-all"
                        >
                    </div>

                    <!-- Filter Company -->
                    <div class="w-full md:w-60">
                        <select name="company_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                            <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Companies</option>
                            <?php foreach ($companiesList as $comp): ?>
                                <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($comp['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="w-full md:w-44">
                        <select name="status" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                            <option value="all" <?= ($selectedStatus ?? 'all') === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="Completed" <?= ($selectedStatus ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Pending" <?= ($selectedStatus ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>

                    <?php if (!empty($searchQuery) || ($selectedCompany ?? 'all') !== 'all' || ($selectedStatus ?? 'all') !== 'all'): ?>
                        <div class="w-full md:w-auto">
                            <a href="evaluations.php" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl border border-slate-300 transition-all inline-block text-center whitespace-nowrap">
                                Reset Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- 3. Evaluations Table (Exact Matching Typography Scale) -->
                <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-200 bg-slate-50/60">
                        <h3 class="text-xs font-extrabold text-slate-900 tracking-wider uppercase">Evaluation Records</h3>
                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Summary of intern final evaluation ratings and status</p>
                    </div>

                    <?php if (!empty($filteredEvals)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs" id="evalTable">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-extrabold">
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Host Company & Supervisor</th>
                                        <th class="py-4 px-6">Performance Rating</th>
                                        <th class="py-4 px-6">Evaluation Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-slate-900 font-medium">
                                    <?php foreach ($filteredEvals as $eval): 
                                        $isCompleted = ($eval['status'] === 'Completed');
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors group eval-row">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-extrabold text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($eval['student_avatar'])): ?>
                                                            <img src="<?= htmlspecialchars($eval['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($eval['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800 text-sm intern-name"><?= htmlspecialchars($eval['student_name']); ?></p>
                                                        <p class="text-[11px] text-slate-500 font-medium intern-id">ID: <?= htmlspecialchars($eval['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($eval['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Company & Supervisor -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <p class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($eval['company_name'] ?? 'Unassigned Office'); ?></p>
                                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                                    Supervisor: <span class="font-semibold text-slate-700"><?= htmlspecialchars($eval['supervisor_name'] ?? 'Assigned Supervisor'); ?></span>
                                                </p>
                                            </td>

                                            <!-- Score & Grade -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isCompleted && isset($eval['final_score'])): ?>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold text-[#0F2854]"><?= number_format($eval['final_score'], 1); ?>%</span>
                                                        <span class="px-2 py-0.5 rounded-lg bg-blue-100 text-[#0F2854] text-[10px] font-bold border border-blue-200">
                                                            Grade: <?= htmlspecialchars($eval['grade_equivalent'] ?? '1.0'); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-slate-500 font-medium italic">Pending Grading</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        Completed &bull; Signed
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <a href="evaluations.php?view_id=<?= $eval['eval_id'] ?? $eval['student_id']; ?>" class="px-4 py-2 bg-slate-100 hover:bg-[#0F2854] hover:text-white text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-all inline-flex items-center gap-1.5 group/btn cursor-pointer">
                                                        <span>View Scorecard</span>
                                                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/btn:translate-x-0.5 text-slate-700 group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                    </a>
                                                <?php else: ?>
                                                    <button disabled class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-semibold rounded-xl border border-slate-200 cursor-not-allowed">
                                                        Not Available
                                                    </button>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-bold border border-slate-300">
                                📋
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">No evaluation records found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">There are no evaluation records matching your search or filter.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <!-- Evaluation Detail Scorecard Modal -->
    <?php if ($activeEval): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 animate-in fade-in duration-150">
            <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-xl w-full overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Top Header -->
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/60">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Final Performance Scorecard</h3>
                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Signed by <?= htmlspecialchars($activeEval['supervisor_name']); ?></p>
                    </div>
                    <a href="evaluations.php" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs transition-colors">✕</a>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-5 overflow-y-auto text-xs">
                    
                    <!-- Intern & Host Meta Card -->
                    <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-300">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">Student Intern</p>
                            <p class="font-bold text-slate-800 text-sm mt-0.5"><?= htmlspecialchars($activeEval['student_name']); ?></p>
                            <p class="text-[11px] text-slate-500 font-medium">ID: <?= htmlspecialchars($activeEval['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($activeEval['program'] ?? 'BSIT'); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">Host Office</p>
                            <p class="font-bold text-slate-800 text-sm mt-0.5"><?= htmlspecialchars($activeEval['company_name'] ?? 'Host Agency'); ?></p>
                            <p class="text-[11px] text-slate-500 font-medium">Evaluator: <?= htmlspecialchars($activeEval['supervisor_name'] ?? 'Supervisor'); ?></p>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="space-y-2.5">
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-600">Competency Breakdown</p>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="p-3 bg-white rounded-xl border border-slate-300 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Technical Competency:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['technical_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-300 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Work Ethics & Discipline:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['work_ethics_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-300 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Communication Skills:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['communication_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-300 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Punctuality & Attendance:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['punctuality_score'] ?? 0, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Final Score -->
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-200 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#0F2854]">Overall Performance Score</p>
                            <p class="text-2xl font-extrabold text-[#0F2854] mt-0.5"><?= number_format($activeEval['final_score'] ?? 0, 1); ?>%</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3.5 py-1.5 rounded-xl bg-[#0F2854] text-white font-bold text-xs shadow-2xs">
                                Grade: <?= htmlspecialchars($activeEval['grade_equivalent'] ?? '1.0'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <?php if (!empty($activeEval['feedback'])): ?>
                        <div class="space-y-1.5">
                            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-600">Supervisor Remarks</p>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-300 text-slate-700 italic leading-relaxed">
                                "<?= htmlspecialchars($activeEval['feedback']); ?>"
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Digital Verification Audit Trail -->
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between text-[11px]">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold">
                            <span>🛡️</span>
                            <span>OTP Verified by Supervisor</span>
                        </div>
                        <span class="text-slate-600 font-medium">
                            <?= !empty($activeEval['otp_signed_at']) ? date("M d, Y \a\\t g:i A", strtotime($activeEval['otp_signed_at'])) : 'Verified Record'; ?>
                        </span>
                    </div>

                </div>

                <!-- Footer Action -->
                <div class="p-4 border-t border-slate-200 bg-slate-50/60 flex justify-end">
                    <a href="evaluations.php" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl border border-slate-300 text-xs transition-colors cursor-pointer">
                        Close
                    </a>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <!-- Instant Search Script -->
    <script>
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.eval-row');

                rows.forEach(function(row) {
                    const name = row.querySelector('.intern-name')?.textContent.toLowerCase() || '';
                    const id   = row.querySelector('.intern-id')?.textContent.toLowerCase() || '';
                    
                    if (name.includes(query) || id.includes(query)) {
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