<!-- src/pages/coordinator/evaluationsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
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

                <!-- 1. Top Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Total Interns</span>
                            <p class="text-3xl font-black text-slate-950"><?= (int)$totalCount; ?></p>
                            <p class="text-xs font-semibold text-slate-600 pt-0.5">Enrolled students</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-[#0F2854] flex items-center justify-center border border-slate-300 shrink-0">
                            <svg class="w-6 h-6 text-[#0F2854]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Completed</span>
                            <p class="text-3xl font-black text-emerald-600"><?= (int)$completedCount; ?></p>
                            <p class="text-xs font-semibold text-slate-600 pt-0.5">Signed evaluations</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-300 shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Pending</span>
                            <p class="text-3xl font-black text-amber-600"><?= (int)$pendingCount; ?></p>
                            <p class="text-xs font-semibold text-slate-600 pt-0.5">Waiting for supervisor</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-300 shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                </div>

                <!-- 2. Unified Evaluations Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
                    <!-- Header Banner -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                        <div>
                            <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">Final Evaluations</h1>
                            <p class="text-xs font-semibold text-slate-600 mt-1">
                                Supervisor ratings, performance grades, and verified sign-off records.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                                <?= count($filteredEvals ?? []); ?> Total <?= count($filteredEvals ?? []) === 1 ? 'Record' : 'Records'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Clean Integrated Filter Toolbar -->
                    <div class="p-4 border-b border-slate-200/70 bg-white">
                        <form id="filterForm" method="GET" action="evaluations.php" class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                            
                            <!-- Search Field -->
                            <div class="relative flex-1 w-full">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input 
                                    type="text" 
                                    id="searchInput"
                                    name="search" 
                                    value="<?= e($searchQuery ?? ''); ?>" 
                                    placeholder="Search by student name or ID..." 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                >
                            </div>

                            <!-- Filter Section -->
                            <div class="w-full md:w-40">
                                <select name="section" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer">
                                    <option value="all" <?= ($selectedSection ?? 'all') === 'all' ? 'selected' : ''; ?>>All Sections</option>
                                    <?php foreach ($activeSections as $sec): ?>
                                        <option value="<?= e($sec); ?>" <?= ($selectedSection ?? '') === $sec ? 'selected' : ''; ?>>
                                            Section <?= e($sec); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Company -->
                            <div class="w-full md:w-56">
                                <select name="company_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer">
                                    <option value="all" <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>>All Companies</option>
                                    <?php foreach ($companiesList as $comp): ?>
                                        <option value="<?= $comp['id']; ?>" <?= ($selectedCompany ?? '') == $comp['id'] ? 'selected' : ''; ?>>
                                            <?= e($comp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div class="w-full md:w-36">
                                <select name="status" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer">
                                    <option value="all" <?= ($selectedStatus ?? 'all') === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                    <option value="Completed" <?= ($selectedStatus ?? '') === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Pending" <?= ($selectedStatus ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                </select>
                            </div>

                            <?php if (!empty($searchQuery) || ($selectedSection ?? 'all') !== 'all' || ($selectedCompany ?? 'all') !== 'all' || ($selectedStatus ?? 'all') !== 'all'): ?>
                                <div class="w-full md:w-auto">
                                    <a href="evaluations.php" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl border border-slate-300 transition-colors inline-block text-center whitespace-nowrap">
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
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Student Intern</th>
                                        <th class="py-4 px-6">Section</th>
                                        <th class="py-4 px-6">Host Company &amp; Supervisor</th>
                                        <th class="py-4 px-6">Score &amp; Grade</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($filteredEvals as $eval): 
                                        $isCompleted = ($eval['status'] === 'Completed');
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors eval-row">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($eval['student_avatar'])): ?>
                                                            <img src="<?= e($eval['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($eval['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-extrabold text-slate-950 text-sm intern-name"><?= e($eval['student_name']); ?></p>
                                                        <p class="text-[11px] text-slate-600 font-semibold intern-id">ID: <?= e($eval['student_number'] ?? 'N/A'); ?> &bull; <?= e($eval['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Section Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                    Sec <?= e($eval['section'] ?? 'A'); ?>
                                                </span>
                                            </td>

                                            <!-- Company & Supervisor -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <p class="font-bold text-slate-900 text-xs"><?= e($eval['company_name'] ?? 'Unassigned'); ?></p>
                                                <p class="text-[11px] text-slate-600 font-medium mt-0.5">
                                                    Supervisor: <span class="font-bold text-slate-800"><?= e($eval['supervisor_name'] ?? 'Pending Assignment'); ?></span>
                                                </p>
                                            </td>

                                            <!-- Score & Grade -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <?php if ($isCompleted && isset($eval['final_score'])): ?>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-black text-[#0F2854]"><?= number_format($eval['final_score'], 1); ?>%</span>
                                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-[#0F2854] text-[10px] font-black border border-blue-200">
                                                            Grade: <?= e($eval['grade_equivalent'] ?? '1.0'); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-slate-500 text-xs font-semibold italic">Pending Appraisal</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <?php if ($isCompleted): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        Signed
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap align-middle">
                                                <?php if ($isCompleted): ?>
                                                    <a href="evaluations.php?view_id=<?= $eval['eval_id'] ?? $eval['student_id']; ?>" class="px-3.5 py-1.5 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                                        <span>View Scorecard</span>
                                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="px-3.5 py-1.5 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-200 cursor-not-allowed inline-block select-none">
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
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📋
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No evaluation records found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">There are no evaluation records matching your filter criteria.</p>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- Evaluation Detail Scorecard Modal -->
    <?php if ($activeEval): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-xl w-full overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Top Header -->
                <div class="p-6 border-b border-slate-200/70 flex items-center justify-between bg-slate-50/60">
                    <div>
                        <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Final Evaluation Scorecard</h3>
                        <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Signed by <?= e($activeEval['supervisor_name'] ?? 'Supervisor'); ?></p>
                    </div>
                    <a href="evaluations.php" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-black text-xs border border-slate-300 transition-colors">✕</a>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-5 overflow-y-auto text-xs">
                    
                    <!-- Meta Grid -->
                    <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Student Intern</span>
                            <p class="font-extrabold text-slate-950 text-xs mt-0.5"><?= e($activeEval['student_name']); ?></p>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">ID: <?= e($activeEval['student_number'] ?? 'N/A'); ?> &bull; Sec <?= e($activeEval['section'] ?? 'A'); ?></p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Company &amp; Supervisor</span>
                            <p class="font-extrabold text-slate-950 text-xs mt-0.5"><?= e($activeEval['company_name'] ?? 'Unassigned'); ?></p>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5"><?= e($activeEval['supervisor_name'] ?? 'Supervisor'); ?></p>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="space-y-2">
                        <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Competency Breakdown</span>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-700 font-bold">Technical Competency:</span>
                                <span class="font-black text-[#0F2854] text-xs"><?= number_format($activeEval['technical_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-700 font-bold">Work Ethics &amp; Discipline:</span>
                                <span class="font-black text-[#0F2854] text-xs"><?= number_format($activeEval['work_ethics_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-700 font-bold">Communication Skills:</span>
                                <span class="font-black text-[#0F2854] text-xs"><?= number_format($activeEval['communication_score'] ?? 0, 1); ?>%</span>
                            </div>
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex justify-between items-center shadow-2xs">
                                <span class="text-slate-700 font-bold">Punctuality &amp; Attendance:</span>
                                <span class="font-black text-[#0F2854] text-xs"><?= number_format($activeEval['punctuality_score'] ?? 0, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Score Banner -->
                    <div class="p-5 bg-blue-50/70 rounded-2xl border border-blue-200 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-wider text-[#0F2854]">Overall Performance Grade</span>
                            <p class="text-2xl font-black text-[#0F2854] mt-0.5"><?= number_format($activeEval['final_score'] ?? 0, 1); ?>%</p>
                        </div>
                        <div>
                            <span class="px-4 py-2 rounded-xl bg-[#0F2854] text-white font-black text-xs shadow-2xs">
                                Grade: <?= e($activeEval['grade_equivalent'] ?? '1.0'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Supervisor Remarks -->
                    <?php if (!empty($activeEval['feedback'])): ?>
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Supervisor Remarks</span>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 font-medium leading-relaxed italic">
                                "&rlm;<?= e($activeEval['feedback']); ?>&rlm;"
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- OTP Signed Badge -->
                    <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-300 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-emerald-900 font-black">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.18-8.204a2.25 2.25 0 00-2.14 0L4.78 4.39A2.25 2.25 0 003.5 6.36v7.38c0 4.26 3.27 8.04 7.5 9.26 4.23-1.22 7.5-5 7.5-9.26V6.36a2.25 2.25 0 00-1.28-1.97l-4.15-2.04z" />
                            </svg>
                            <span>OTP Signed &amp; Verified</span>
                        </div>
                        <span class="text-slate-600 font-bold text-[11px]">
                            <?= !empty($activeEval['otp_signed_at']) ? date("M d, Y \a\\t g:i A", strtotime($activeEval['otp_signed_at'])) : 'Verified Record'; ?>
                        </span>
                    </div>

                </div>

                <!-- Footer Action -->
                <div class="p-4 border-t border-slate-200/70 bg-slate-50/60 flex justify-end">
                    <a href="evaluations.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs border border-slate-300 transition-colors">
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