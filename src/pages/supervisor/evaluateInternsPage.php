<!-- src/pages/supervisor/evaluateInternsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate Interns - Supervisor Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Shared Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Side Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scrollable Body -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Header Banner Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80">
                    <h1 class="text-base font-bold text-slate-900 leading-snug">Final Intern Evaluation</h1>
                    <p class="text-slate-500 text-xs mt-0.5">Evaluate interns who have completed all required Weekly Accomplishment Reports (486 Hours Target).</p>
                </div>

                <!-- Roster Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 px-5 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Assigned Interns Evaluation Status</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Requires completion of 12 weekly accomplishment reports (~486 hours compliance)</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                    <th class="py-3 px-5">Student Name</th>
                                    <th class="py-3 px-5">WAR Progress (486 Hours)</th>
                                    <th class="py-3 px-5">Evaluation Status</th>
                                    <th class="py-3 px-5">Final Rating</th>
                                    <th class="py-3 px-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                                <?php foreach ($students as $student): ?>
                                    <?php 
                                        $requiredWeeks = 12; // 12 Weeks = ~486 Hours
                                        $isWARComplete = ($student['submitted_wars'] >= $requiredWeeks);
                                        $isEvaluated   = ($student['evaluation_status'] === 'Verified');
                                    ?>
                                    <tr class="transition-colors <?= ($isWARComplete && !$isEvaluated) ? 'bg-amber-50/30 hover:bg-amber-100/40 border-l-4 border-l-amber-500' : 'hover:bg-slate-50/80'; ?>">
                                        
                                        <!-- Student Info -->
                                        <td class="py-3.5 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-extrabold text-xs shrink-0 border border-[#0F2854]/20">
                                                    <?= strtoupper(substr($student['name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($student['name']); ?></p>
                                                    <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($student['student_number']); ?> • <?= htmlspecialchars($student['program']); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- WAR Progress Count -->
                                        <td class="py-3.5 px-5 font-medium">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-slate-800"><?= $student['submitted_wars']; ?> / <?= $requiredWeeks; ?> WARs</span>
                                                <?php if ($isWARComplete): ?>
                                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md border border-emerald-200/60">
                                                        ✓ 486 Hrs Fulfilled
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-medium rounded-md">
                                                        In Progress
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Evaluation Status Badge -->
                                        <td class="py-3.5 px-5">
                                            <?php if ($isEvaluated): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200/50">
                                                    ● Verified & Submitted
                                                </span>
                                            <?php elseif ($isWARComplete): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200/50">
                                                    ● Ready for Evaluation
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[11px] font-medium border border-slate-200">
                                                    ● Awaiting Final WAR
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Final Score -->
                                        <td class="py-3.5 px-5 font-bold text-slate-900">
                                            <?= $student['final_score'] ? $student['final_score'] . ' / 100%' : '—'; ?>
                                        </td>

                                        <!-- Action Button -->
                                        <td class="py-3.5 px-5 text-right">
                                            <?php if ($isEvaluated): ?>
                                                <a href="evaluate_view.php?id=<?= $student['evaluation_id']; ?>" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold rounded-full border border-slate-200 transition-all shadow-2xs inline-block">
                                                    View Evaluation
                                                </a>
                                            <?php elseif ($isWARComplete): ?>
                                                <a href="evaluate_form.php?student_id=<?= $student['id']; ?>" class="px-4 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-semibold rounded-full transition-all shadow-2xs inline-block">
                                                    Evaluate Intern
                                                </a>
                                            <?php else: ?>
                                                <button disabled class="px-4 py-1.5 bg-slate-100/70 text-slate-400 text-[11px] font-semibold rounded-full border border-slate-200/80 cursor-not-allowed inline-block">
                                                    Incomplete WARs
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

</body>
</html>