<!-- src/pages/coordinator/evaluationsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Evaluations - Coordinator Portal</title>
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

                <!-- 1. Top Stat Cards (Clean Vector SVGs) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Interns</p>
                            <p class="text-2xl font-extrabold text-slate-900 mt-1"><?= $totalCount; ?></p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Enrolled students</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#0F2854] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#0F2854]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Completed</p>
                            <p class="text-2xl font-extrabold text-emerald-600 mt-1"><?= $completedCount; ?></p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Signed evaluations</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pending</p>
                            <p class="text-2xl font-extrabold text-amber-600 mt-1"><?= $pendingCount; ?></p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Waiting for supervisor</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                </div>

                <!-- 2. Unified Evaluations Container -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    
                    <!-- Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug">Final Student Evaluations</h1>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">
                                Supervisor evaluation ratings, and OTP verification records.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                                <?= count($filteredEvals ?? []); ?> Total <?= count($filteredEvals ?? []) === 1 ? 'Record' : 'Records'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Integrated Filter & Live Search Toolbar -->
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
                                    placeholder="Search by student name or ID number..." 
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

                    <!-- 3. Submissions Table -->
                    <?php if (!empty($filteredEvals)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Host Company & Supervisor</th>
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
                        <div class="text-center py-16 px-4 space-y-3">
                            <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto border border-slate-200">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">No evaluation records found</h4>
                                <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto mt-0.5">There are no evaluation records matching your filter criteria.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- Evaluation Detail Scorecard Modal -->
    <?php if ($activeEval): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 animate-in fade-in duration-150">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full overflow-hidden flex flex-col max-h-[90vh]">
                
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
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Competency Breakdown</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Technical Competency:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['technical_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Work Ethics & Discipline:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['work_ethics_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Communication Skills:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['communication_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-600 font-medium">Punctuality & Attendance:</span>
                                <span class="font-bold text-[#0F2854] text-xs"><?= number_format($activeEval['punctuality_score'] ?? 0, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Score -->
                    <div class="p-4 bg-blue-50/60 rounded-xl border border-blue-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[#0F2854]">Overall Performance Grade</p>
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
                    <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold">
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.18-8.204a2.25 2.25 0 00-2.14 0L4.78 4.39A2.25 2.25 0 003.5 6.36v7.38c0 4.26 3.27 8.04 7.5 9.26 4.23-1.22 7.5-5 7.5-9.26V6.36a2.25 2.25 0 00-1.28-1.97l-4.15-2.04z" />
                            </svg>
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

    <!-- Instant Client-Side Search Script -->
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