<!-- src/pages/supervisor/evaluateViewPage.php -->
<?php
date_default_timezone_set('Asia/Manila');
$signedTime = !empty($evaluation['otp_signed_at']) ? strtotime($evaluation['otp_signed_at']) : (!empty($evaluation['evaluated_at']) ? strtotime($evaluation['evaluated_at']) : time());
$scorecardVariant = 'compact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Summary - <?= htmlspecialchars($evaluation['student_name'] ?? 'Student'); ?></title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">

</head>
<body class="bg-slate-50 text-slate-800 subpixel-antialiased">

    <div class="flex min-h-screen">

        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Side Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scroll Area -->
            <main class="p-8 max-w-4xl w-full mx-auto space-y-6 flex-1 relative">

                <!-- Navigation Top Bar -->
                <div class="flex items-center justify-between no-print">
                    <a href="evaluate_interns.php" class="inline-flex items-center gap-2 text-xs font-bold text-[#0F2854] hover:text-blue-900 bg-white px-4 py-2 rounded-xl border border-slate-200/80 shadow-2xs transition-all hover:bg-slate-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Back to Evaluation List</span>
                    </a>
                </div>

                <!-- Main Evaluation Document Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden p-8 space-y-7 print-clean">

                    <!-- Document Header -->
                    <div class="border-b border-slate-100 pb-6 flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <?php if (!empty($evaluation['otp_verified'])): ?>
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold inline-flex items-center gap-1.5 mb-2 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Verified &amp; Signed via Email OTP</span>
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold inline-flex items-center gap-1.5 mb-2">
                                    <span>Pending Signature</span>
                                </span>
                            <?php endif; ?>

                            <h1 class="text-xl font-extrabold text-slate-900">On-the-Job Training Performance Evaluation</h1>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Submitted on <?= date("F d, Y \a\\t g:i A", $signedTime); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Trainee & Host Details (docx header block) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/70 rounded-2xl p-5 border border-slate-200/70 text-xs">
                        <div class="space-y-0.5">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Student Intern</span>
                            <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($evaluation['student_name']); ?></p>
                            <p class="text-slate-500">ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($evaluation['student_number']); ?></span> &bull; <?= htmlspecialchars($evaluation['program']); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['student_email']); ?></p>
                        </div>
                        <div class="space-y-0.5">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Industry Supervisor / HTE</span>
                            <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($evaluation['supervisor_name']); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['company_name'] ?? 'Host Company'); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['supervisor_email']); ?></p>
                        </div>
                    </div>

                    <!-- Competency Scorecard (12 competencies, 1-5 scale) -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Competency Rating</h3>
                        <?php
                        // Plain require (not require_once) so the markup always renders;
                        // the component guards its own helper with function_exists().
                        require __DIR__ . '/../../components/evaluation_scorecard.php';
                        ?>
                    </div>

                    <!-- Comments / Remarks -->
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Comments / Remarks</p>
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 text-xs text-slate-800 font-medium italic leading-relaxed">
                            <?= !empty($evaluation['remarks']) ? '"' . htmlspecialchars($evaluation['remarks']) . '"' : '<span class="text-slate-400 not-italic">No specific remarks provided.</span>'; ?>
                        </div>
                    </div>

                    <!-- Evaluator Signature Block -->
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Evaluator's  Name</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-xs">
                            <div>
                                <p class="font-extrabold text-slate-900 italic underline decoration-slate-300 underline-offset-4"><?= htmlspecialchars($evaluation['supervisor_name']); ?></p>
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5"> Name</p>
                            </div>
                            <div>
                                <p class="font-extrabold text-slate-900"><?= htmlspecialchars($evaluation['company_department'] ?? 'OJT Supervisor'); ?></p>
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">Position / Designation</p>
                            </div>
                            <div>
                                <p class="font-extrabold text-slate-900"><?= htmlspecialchars($evaluation['company_name'] ?? 'N/A'); ?></p>
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">Office / Division Name</p>
                            </div>
                            <div>
                                <p class="font-extrabold text-slate-900"><?= !empty($evaluation['otp_signed_at']) ? date("F d, Y", strtotime($evaluation['otp_signed_at'])) : 'Not yet signed'; ?></p>
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">Date Signed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lock & Digital Signature Audit Notice -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-2 text-[11px] text-slate-400">
                        <div>
                            Ref ID: <span class="font-mono text-slate-600 font-semibold">EVAL-2026-<?= str_pad($evaluation['evaluation_id'], 4, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <?php if (!empty($evaluation['otp_signed_at'])): ?>
                            <div class="text-slate-500">
                                Signed on: <strong class="text-slate-700"><?= date("M d, Y \a\\t g:i A", strtotime($evaluation['otp_signed_at'])); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </main>
        </div>
    </div>

</body>
</html>
