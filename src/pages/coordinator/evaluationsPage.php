<!-- src/pages/coordinator/evaluationsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Evaluations - OJT Portal</title>
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
<body class="bg-[#F8FAFC] text-slate-800 antialiased font-sans">

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
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Final Student Evaluations</h1>
                        <p class="text-xs font-medium text-slate-500 mt-1">
                            Supervisor evaluation scores and sign-off records.
                        </p>
                    </div>

                    <!-- Counter Pill -->
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <?= count($filteredEvals ?? []); ?> Total <?= count($filteredEvals ?? []) === 1 ? 'Record' : 'Records'; ?>
                        </span>
                    </div>
                </div>

                <!-- 1. Top Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    
                    <div class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-[0.14em]">Total Interns</p>
                            <p class="text-[2rem] leading-none font-extrabold text-slate-900 mt-2"><?= $totalCount; ?></p>
                            <p class="text-xs font-semibold text-slate-500 mt-1.5">Enrolled students</p>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 border border-blue-200 text-[#0F2854] font-bold text-lg flex items-center justify-center shrink-0 shadow-inner shadow-blue-100/60">
                            👥
                        </div>
                    </div>

                    <div class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-[0.14em]">Completed</p>
                            <p class="text-[2rem] leading-none font-extrabold text-emerald-600 mt-2"><?= $completedCount; ?></p>
                            <p class="text-xs font-semibold text-slate-500 mt-1.5">Signed evaluations</p>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-lg flex items-center justify-center shrink-0 shadow-inner shadow-emerald-100/60">
                            ✓
                        </div>
                    </div>

                    <div class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-600 uppercase tracking-[0.14em]">Pending</p>
                            <p class="text-[2rem] leading-none font-extrabold text-amber-600 mt-2"><?= $pendingCount; ?></p>
                            <p class="text-xs font-semibold text-slate-500 mt-1.5">Waiting for supervisor</p>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-100 to-amber-50 border border-amber-200 text-amber-700 font-bold text-lg flex items-center justify-center shrink-0 shadow-inner shadow-amber-100/60">
                            ⏱
                        </div>
                    </div>

                </div>

                <!-- 2. Integrated Filter & Live Search Toolbar -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    
                    <div class="p-4 border-b border-slate-100 bg-slate-50/40">
                        <form id="filterForm" method="GET" action="evaluations.php" class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                            
                            <!-- Search Field -->
                            <div class="relative flex-1 w-full">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input 
                                    type="text" 
                                    id="searchInput"
                                    name="search" 
                                    value="<?= htmlspecialchars($searchQuery ?? ''); ?>" 
                                    placeholder="Search student name or ID..." 
                                    class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0F2854]"
                                >
                            </div>

                            <!-- Filter Company -->
                            <div class="w-full md:w-56">
                                <select name="company_id" onchange="this.form.submit()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                    <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Companies</option>
                                    <?php foreach ($companiesList as $comp): ?>
                                        <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($comp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div class="w-full md:w-40">
                                <select name="status" onchange="this.form.submit()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                    <option value="all" <?= ($selectedStatus ?? 'all') === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                    <option value="Completed" <?= ($selectedStatus ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Pending" <?= ($selectedStatus ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                </select>
                            </div>

                            <?php if (!empty($searchQuery) || ($selectedCompany ?? 'all') !== 'all' || ($selectedStatus ?? 'all') !== 'all'): ?>
                                <div class="w-full md:w-auto">
                                    <a href="evaluations.php" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl border border-slate-200 transition-all inline-block text-center whitespace-nowrap">
                                        Reset
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- 3. Table Section -->
                    <?php if (!empty($filteredEvals)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs" id="evalTable">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Company & Supervisor</th>
                                        <th class="py-4 px-6">Rating</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php foreach ($filteredEvals as $eval): 
                                        $isCompleted = ($eval['status'] === 'Completed');
                                    ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors group eval-row">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-200/80 group-hover:border-[#0F2854] transition-colors">
                                                        <?php if (!empty($eval['student_avatar'])): ?>
                                                            <img src="<?= htmlspecialchars($eval['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($eval['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900 text-xs intern-name"><?= htmlspecialchars($eval['student_name']); ?></p>
                                                        <p class="text-xs font-medium text-slate-500 mt-0.5 intern-id">ID: <?= htmlspecialchars($eval['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($eval['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Company & Supervisor -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <p class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($eval['company_name'] ?? 'Unassigned'); ?></p>
                                                <p class="text-xs font-medium text-slate-500 mt-0.5">
                                                    Supervisor: <?= htmlspecialchars($eval['supervisor_name'] ?? 'Pending Assignment'); ?>
                                                </p>
                                            </td>

                                            <!-- Score & Grade -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isCompleted && isset($eval['final_score'])): ?>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-bold text-[#0F2854]"><?= number_format($eval['final_score'], 1); ?>%</span>
                                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-[#0F2854] text-[10px] font-bold border border-blue-100">
                                                            Grade: <?= htmlspecialchars($eval['grade_equivalent'] ?? '1.0'); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-xs font-medium italic">Pending</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Signed
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <?php if ($isCompleted): ?>
                                                    <a href="evaluations.php?view_id=<?= $eval['eval_id'] ?? $eval['student_id']; ?>" class="px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-[#0F2854] bg-slate-100 hover:bg-slate-200/80 rounded-xl border border-slate-200/70 transition-all inline-flex items-center gap-1.5 group/btn cursor-pointer">
                                                        <span>View Scorecard</span>
                                                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/btn:translate-x-0.5 text-slate-400 group-hover:text-[#0F2854]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="px-3.5 py-1.5 text-xs font-semibold text-slate-400 bg-slate-50 rounded-xl border border-slate-200/50 cursor-not-allowed inline-block">
                                                        Not Available
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="py-12 text-center text-slate-500 text-xs italic">
                            No evaluation records found matching your filter.
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <!-- Evaluation Detail Scorecard Modal -->
    <?php if ($activeEval): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Top Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Final Evaluation Scorecard</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Signed by <?= htmlspecialchars($activeEval['supervisor_name'] ?? 'Supervisor'); ?></p>
                    </div>
                    <a href="evaluations.php" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs">✕</a>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-4 overflow-y-auto text-xs">
                    
                    <!-- Meta Grid -->
                    <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Student Intern</p>
                            <p class="font-bold text-slate-900 text-xs mt-0.5"><?= htmlspecialchars($activeEval['student_name']); ?></p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">ID: <?= htmlspecialchars($activeEval['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($activeEval['program'] ?? 'BSIT'); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Company & Supervisor</p>
                            <p class="font-bold text-slate-900 text-xs mt-0.5"><?= htmlspecialchars($activeEval['company_name'] ?? 'Unassigned'); ?></p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5"><?= htmlspecialchars($activeEval['supervisor_name'] ?? 'Supervisor'); ?></p>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="space-y-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Score Breakdown</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Technical:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['technical_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Work Ethics:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['work_ethics_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Communication:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['communication_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Punctuality:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['punctuality_score'] ?? 0, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Score -->
                    <div class="p-4 bg-blue-50/60 rounded-xl border border-blue-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[#0F2854]">Final Grade</p>
                            <p class="text-xl font-bold text-[#0F2854] mt-0.5"><?= number_format($activeEval['final_score'] ?? 0, 1); ?>%</p>
                        </div>
                        <div>
                            <span class="px-3.5 py-1.5 rounded-xl bg-[#0F2854] text-white font-bold text-xs">
                                Grade: <?= htmlspecialchars($activeEval['grade_equivalent'] ?? '1.0'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Supervisor Remarks -->
                    <?php if (!empty($activeEval['feedback'])): ?>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Supervisor Remarks</p>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-700 italic">
                                "<?= htmlspecialchars($activeEval['feedback']); ?>"
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- OTP Signed Badge -->
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5 text-emerald-800 font-bold">
                            <span>🛡️</span>
                            <span>OTP Signed & Verified</span>
                        </div>
                        <span class="text-slate-500 font-medium text-[11px]">
                            <?= !empty($activeEval['otp_signed_at']) ? date("M d, Y \a\\t g:i A", strtotime($activeEval['otp_signed_at'])) : 'Verified Record'; ?>
                        </span>
                    </div>

                </div>

                <!-- Footer Action -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                    <a href="evaluations.php" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs transition-colors">
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