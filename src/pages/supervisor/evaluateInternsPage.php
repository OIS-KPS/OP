<!-- src/pages/supervisor/evaluateInternsPage.php -->
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
    <title>Final Evaluation - Supervisor Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Supervisor Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Side Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scrollable Body -->
            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Alert Message -->
                <?php if (isset($_GET['evaluated']) && $_GET['evaluated'] === 'success'): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black text-sm">✓</span>
                            <div>
                                <p class="font-extrabold text-xs">Evaluation Signed & Submitted</p>
                                <p class="text-[11px] text-emerald-800 font-medium">The final performance appraisal has been verified and recorded.</p>
                            </div>
                        </div>
                        <a href="evaluate_interns.php" class="text-xs font-bold text-emerald-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <!-- Header Banner Card -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/90 shadow-xs">
                    <div>
                        <h1 class="text-base font-extrabold text-slate-950 leading-snug tracking-tight">Final Student Evaluation</h1>
                        <p class="text-xs font-semibold text-slate-600 mt-1">
                            <?= e($supervisor['company_name'] ?? 'Host Company'); ?> &bull; Submit final performance evaluations for students completing their internship.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                        <?= count($students ?? []); ?> Total <?= count($students ?? []) === 1 ? 'Student' : 'Students'; ?>
                    </span>
                </div>

                <!-- Roster Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-200/70 flex justify-between items-center bg-slate-50/60">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 tracking-wider uppercase">Student Evaluation List</h3>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Students submit weekly accomplishment reports continuously until completion</p>
                        </div>
                        <span class="text-xs font-bold text-slate-700 bg-white px-3 py-1 rounded-lg border border-slate-300 shadow-2xs">
                            Flexible Duration
                        </span>
                    </div>

                    <?php if (!empty($students) && count($students) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Student</th>
                                        <th class="py-4 px-6">Completed Reports</th>
                                        <th class="py-4 px-6">Evaluation Status</th>
                                        <th class="py-4 px-6">Evaluation</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($students as $student): 
                                        $submittedWars = intval($student['submitted_wars'] ?? 0);
                                        $approvedWars  = intval($student['approved_wars'] ?? 0);
                                        $isWARComplete = ($submittedWars > 0);
                                        $evalStatus    = strtolower($student['evaluation_status'] ?? '');
                                        $isEvaluated   = !empty($student['evaluation_id']) || in_array($evalStatus, ['verified', 'completed', 'approved']);
                                        $isTriggered   = !empty($student['evaluation_triggered']);
                                        $needsAction   = (!$isEvaluated && $isTriggered);
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors group <?= $needsAction ? 'bg-amber-50/20' : ''; ?>">
                                            
                                            <!-- Student Info -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-300 group-hover:border-[#0F2854] transition-colors">
                                                        <?php if (!empty($student['avatar_url'])): ?>
                                                            <img src="<?= e($student['avatar_url']); ?>" class="w-full h-full object-cover" alt="Avatar" onerror="this.onerror=null; this.parentElement.innerHTML='<?= e(strtoupper(substr($student['name'] ?? 'S', 0, 1))); ?>';">
                                                        <?php else: ?>
                                                            <?= e(strtoupper(substr($student['name'] ?? 'S', 0, 1))); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-extrabold text-slate-950 text-sm tracking-tight"><?= e($student['name'] ?? 'Student'); ?></p>
                                                        <p class="text-[11px] text-slate-500 font-semibold">ID: <?= e($student['student_number'] ?? 'N/A'); ?> &bull; <?= e($student['program'] ?? 'BSIT'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Progress Count -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-extrabold text-slate-950 text-xs"><?= $submittedWars; ?> <?= ($submittedWars === 1) ? 'Report' : 'Reports'; ?> Submitted</span>
                                                    <?php if ($submittedWars > 0): ?>
                                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 text-[10px] font-bold rounded-md border border-blue-200">
                                                            Active Logs
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-md border border-slate-300">
                                                            None Yet
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- Evaluation Status -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isEvaluated): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        Completed
                                                    </span>
                                                <?php elseif ($isTriggered): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        Ready for Evaluation
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100/80 text-blue-900 border border-blue-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                        Awaiting Coordinator
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Evaluation status. -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isEvaluated): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 text-[10px] font-black">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Submitted
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-xs font-semibold">&mdash;</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <?php if ($isEvaluated): ?>
                                                    <a href="evaluate_view.php?id=<?= (int)$student['evaluation_id']; ?>" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-all inline-block cursor-pointer">
                                                        View Result
                                                    </a>
                                                <?php elseif ($isTriggered): ?>
                                                    <a href="evaluate_form.php?student_id=<?= $student['id']; ?>" class="px-4 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-all shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                                                        <span>Evaluate</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                                    </a>
                                                <?php else: ?>
                                                    <button disabled class="px-3.5 py-1.5 bg-slate-100 text-slate-400 text-xs font-semibold rounded-xl border border-slate-200 cursor-not-allowed inline-block" title="Coordinator must trigger the evaluation request first">
                                                        Awaiting Coordinator
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
                            <h4 class="text-sm font-black text-slate-900">No students found</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">There are currently no students assigned to your account for evaluation.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <!-- ============================================================
         EVALUATION SCORECARD PREVIEW MODAL
         ============================================================ -->
    <?php if ($activeEval):
        $scorecardVariant = 'compact';
    ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[90vh]">

                <!-- Modal Header -->
                <div class="p-6 border-b border-slate-200/70 flex items-center justify-between bg-slate-50/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0F2854] flex items-center justify-center font-black text-sm border border-blue-200 shrink-0 overflow-hidden">
                            <?php if (!empty($activeEval['student_avatar'])): ?>
                                <img src="<?= e($activeEval['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                            <?php else: ?>
                                <?= e(strtoupper(substr($activeEval['student_name'] ?? 'S', 0, 1))); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Evaluation Scorecard</h3>
                            <p class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                <?= e($activeEval['student_name']); ?> &bull; ID <?= e($activeEval['student_number'] ?? 'N/A'); ?>
                            </p>
                        </div>
                    </div>
                    <a href="evaluate_interns.php" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-black text-xs border border-slate-300 transition-colors">✕</a>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 overflow-y-auto text-xs">
                    <?php require __DIR__ . '/../../components/evaluation_scorecard.php'; ?>

                    <!-- Supervisor Remarks -->
                    <?php if (!empty($activeEval['feedback'])): ?>
                        <div class="space-y-1.5">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Comments / Remarks</span>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 font-medium leading-relaxed italic">
                                "&ldquo;<?= e($activeEval['feedback']); ?>&rdquo;"
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

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200/70 bg-slate-50/60 flex justify-between gap-2 shrink-0">
                    <a href="evaluate_interns.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs border border-slate-300 transition-colors">
                        Close
                    </a>
                    <a href="evaluate_view.php?id=<?= (int)$activeEval['evaluation_id']; ?>" class="px-5 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                        Open Full Report
                    </a>
                </div>

            </div>
        </div>
    <?php endif; ?>

</body>
</html>