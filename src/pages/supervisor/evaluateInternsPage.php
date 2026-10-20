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
                <?php
                /*
                 * Flash-based rather than ?evaluated=success. The old query
                 * param re-armed the banner on every reload and on any
                 * bookmarked URL, and bypassed the auto-dismiss header.php
                 * applies to flash alerts. evaluateFormPage.php now sets
                 * $_SESSION['flash_success'] before redirecting here.
                 */
                if (!empty($_SESSION['flash_success'])): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black text-sm">✓</span>
                            <div>
                                <p class="font-extrabold text-xs">Evaluation Signed & Submitted</p>
                                <p class="text-[11px] text-emerald-800 font-medium"><?= e($_SESSION['flash_success']); ?></p>
                            </div>
                        </div>
                        <a href="evaluate_interns.php" class="text-xs font-bold text-emerald-800 hover:underline">Dismiss</a>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
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
                    </div>

                    <?php if (!empty($students) && count($students) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Student</th>
                                        <th class="py-4 px-6">Completed Reports</th>
                                        <th class="py-4 px-6">Evaluation Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($students as $student): 
                                        $submittedWars = intval($student['submitted_wars'] ?? 0);
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

</body>
</html>