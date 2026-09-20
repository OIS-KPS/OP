<!-- src/pages/coordinator/evaluationsPage.php -->
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

                <!-- Flash Alert -->
                <?php if (!empty($flashSuccess)): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <p class="font-extrabold text-xs"><?= e($flashSuccess); ?></p>
                        <a href="evaluations.php" class="text-xs font-bold text-emerald-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>
                <?php if (!empty($flashError)): ?>
                    <div class="bg-rose-50 border border-rose-300 text-rose-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <p class="font-extrabold text-xs"><?= e($flashError); ?></p>
                        <a href="evaluations.php" class="text-xs font-bold text-rose-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

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
                            <!-- Bulk Trigger Button -->
                            <button type="button" onclick="openTriggerModal()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                <span>Bulk Trigger Evaluations</span>
                            </button>

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
                            <div class="w-full md:w-36">
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
                            <div class="w-full md:w-48">
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
                            <div class="w-full md:w-32">
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

                    <!-- 3. Submissions Table (Vertically Scrollable with Fixed Header) -->
                    <?php if (!empty($filteredEvals)): ?>
                        <div class="w-full">
                            <table class="w-full text-left border-collapse text-xs table-fixed">
                                <thead class="block w-full">
                                    <tr class="bg-slate-100/90 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black flex w-full">
                                        <th class="py-3 px-3 w-[25%]">Student Intern</th>
                                        <th class="py-3 px-2 w-[10%]">Section</th>
                                        <th class="py-3 px-3 w-[22%]">Host Company &amp; Supervisor</th>
                                        <th class="py-3 px-2 w-[13%] text-center">Approved Reports</th>
                                        <th class="py-3 px-2 w-[15%]">Score &amp; Grade</th>
                                        <th class="py-3 px-3 w-[15%] text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800 block w-full max-h-[480px] overflow-y-auto">
                                    <?php foreach ($filteredEvals as $eval): 
                                        $isCompleted = ($eval['status'] === 'Completed');
                                        $isTriggered = !empty($eval['evaluation_triggered']);
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors eval-row flex w-full items-center">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-3 px-3 w-[25%] truncate align-middle">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                        <?php if (!empty($eval['student_avatar'])): ?>
                                                            <img src="<?= e($eval['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($eval['student_name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="font-extrabold text-slate-950 text-xs truncate intern-name"><?= e($eval['student_name']); ?></p>
                                                        <p class="text-[10px] text-slate-600 font-semibold truncate intern-id">ID: <?= e($eval['student_number'] ?? 'N/A'); ?> &bull; <?= e($eval['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Section Badge -->
                                            <td class="py-3 px-2 w-[10%] truncate align-middle">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                    Sec <?= e($eval['section'] ?? 'A'); ?>
                                                </span>
                                            </td>

                                            <!-- Company & Supervisor -->
                                            <td class="py-3 px-3 w-[22%] truncate align-middle">
                                                <p class="font-bold text-slate-900 text-xs truncate"><?= e($eval['company_name'] ?? 'Unassigned'); ?></p>
                                                <p class="text-[11px] text-slate-600 font-medium truncate mt-0.5">
                                                    Sup: <?= e($eval['supervisor_name'] ?? 'Pending Assignment'); ?>
                                                </p>
                                            </td>

                                            <!-- Approved Reports Column -->
                                            <td class="py-3 px-2 w-[13%] truncate align-middle text-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-900 border border-indigo-200 font-black text-[11px]">
                                                    <?= (int)($eval['approved_reports_count'] ?? 0); ?> Approved
                                                </span>
                                            </td>

                                            <!-- Score & Grade -->
                                            <td class="py-3 px-2 w-[15%] truncate align-middle">
                                                <?php if ($isCompleted && isset($eval['final_score'])): ?>
                                                    <div class="flex items-center gap-1.5 truncate">
                                                        <span class="text-xs font-black text-[#0F2854]"><?= number_format($eval['final_score'], 1); ?>%</span>
                                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 text-[#0F2854] text-[10px] font-black border border-blue-200">
                                                            <?= e($eval['grade_equivalent'] ?? '1.0'); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-slate-500 text-xs font-semibold italic">Pending</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-3 px-3 w-[15%] text-right truncate align-middle">
                                                <?php if ($isCompleted): ?>
                                                    <a href="evaluations.php?view_id=<?= $eval['eval_id'] ?? $eval['student_id']; ?>" class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-colors inline-flex items-center gap-1">
                                                        <span>View</span>
                                                    </a>
                                                <?php elseif ($isTriggered): ?>
                                                    <span class="px-2.5 py-1 text-[11px] font-bold text-[#0F2854] bg-blue-50 rounded-xl border border-blue-300 inline-block">
                                                        Awaiting
                                                    </span>
                                                <?php elseif ($eval['supervisor_name'] === 'Pending Assignment'): ?>
                                                    <span class="px-2.5 py-1 text-[11px] font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-200 inline-block" title="Assign a supervisor first">
                                                        N/A
                                                    </span>
                                                <?php else: ?>
                                                    <button type="button" onclick="openSingleConfirmModal(<?= $eval['student_id']; ?>, '<?= e($eval['student_name']); ?>')" class="px-2.5 py-1 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-1 cursor-pointer">
                                                        <span>Evaluate</span>
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

    <!-- 1. Bulk Trigger Evaluation Modal Component -->
    <div id="triggerEvalModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0F2854] flex items-center justify-center font-bold">📋</div>
                    <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Bulk Trigger Evaluations</h3>
                </div>
                <button type="button" onclick="closeTriggerModal()" class="w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold flex items-center justify-center cursor-pointer">✕</button>
            </div>
            
            <form method="POST" action="evaluations.php" class="p-6 space-y-4 text-xs flex flex-col flex-1 overflow-hidden">
                <input type="hidden" name="action" value="request_evaluation">
                
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="font-bold text-slate-700">Select eligible interns:</span>
                    <button type="button" onclick="toggleSelectAll()" class="text-[11px] font-bold text-[#0F2854] hover:underline cursor-pointer" id="selectAllBtn">Select All</button>
                </div>

                <!-- Scrollable Checkbox List Container -->
                <div class="overflow-y-auto max-h-60 space-y-2 pr-1 border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                    <?php if (!empty($eligibleStudents)): ?>
                        <?php foreach ($eligibleStudents as $es): ?>
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 transition-all cursor-pointer">
                                <input type="checkbox" name="student_ids[]" value="<?= $es['id']; ?>" class="student-checkbox w-4 h-4 rounded text-[#0F2854] focus:ring-[#0F2854] border-slate-300">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 truncate"><?= e($es['name']); ?></p>
                                    <p class="text-[10px] text-slate-500">ID: <?= e($es['student_number']); ?> &bull; Sec <?= e($es['section']); ?></p>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-slate-400 py-6 italic">No eligible students available (Must have an assigned supervisor)</p>
                    <?php endif; ?>
                </div>

                <!-- Professional Warning Notice -->
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2 text-amber-900">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>Security Notice:</strong> Triggering evaluation will immediately lock out selected students from uploading further Weekly Accomplishment Reports.</span>
                </div>
                
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeTriggerModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl cursor-pointer shadow-xs">Confirm &amp; Trigger Selected</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Individual Single Request Confirmation Modal Component -->
    <div id="singleConfirmModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">🔒</div>
                    <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Confirm Final Evaluation</h3>
                </div>
                <button type="button" onclick="closeSingleConfirmModal()" class="w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold flex items-center justify-center cursor-pointer">✕</button>
            </div>

            <form method="POST" action="evaluations.php" class="p-6 space-y-4 text-xs">
                <input type="hidden" name="action" value="request_evaluation">
                <input type="hidden" name="student_ids[]" id="confirmStudentId">

                <p class="text-slate-700 leading-relaxed">
                    Are you sure you want to request a final evaluation for <strong id="confirmStudentName" class="text-slate-950"></strong>?
                </p>

                <!-- Professional Warning Notice -->
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2 text-amber-900">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>This action will lock the student's account from submitting further weekly reports.</span>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeSingleConfirmModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl cursor-pointer shadow-xs">Yes, Request Evaluation</button>
                </div>
            </form>
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
                            <p class="text-xl font-black text-[#0F2854] mt-0.5"><?= number_format($activeEval['final_score'] ?? 0, 1); ?>%</p>
                        </div>
                        <div>
                            <span class="px-4 py-2 rounded-xl bg-[#0F2854] text-white font-black text-[11px] shadow-2xs">
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

    <!-- Client-Side Search & Modal Scripts -->
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

        // Bulk Modal Controls
        function openTriggerModal() {
            document.getElementById('triggerEvalModal').classList.remove('hidden');
            document.getElementById('triggerEvalModal').classList.add('flex');
        }

        function closeTriggerModal() {
            document.getElementById('triggerEvalModal').classList.remove('flex');
            document.getElementById('triggerEvalModal').classList.add('hidden');
        }

        let selectAllState = false;
        function toggleSelectAll() {
            selectAllState = !selectAllState;
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAllState);
            document.getElementById('selectAllBtn').textContent = selectAllState ? 'Deselect All' : 'Select All';
        }

        // Single Confirm Modal Controls
        function openSingleConfirmModal(studentId, studentName) {
            document.getElementById('confirmStudentId').value = studentId;
            document.getElementById('confirmStudentName').textContent = studentName;
            document.getElementById('singleConfirmModal').classList.remove('hidden');
            document.getElementById('singleConfirmModal').classList.add('flex');
        }

        function closeSingleConfirmModal() {
            document.getElementById('singleConfirmModal').classList.remove('flex');
            document.getElementById('singleConfirmModal').classList.add('hidden');
        }
    </script>
</body>
</html>