<!-- src/pages/supervisor/evaluateViewPage.php -->
<?php
date_default_timezone_set('Asia/Manila');
$signedTime = !empty($evaluation['otp_signed_at']) ? strtotime($evaluation['otp_signed_at']) : (!empty($evaluation['evaluated_at']) ? strtotime($evaluation['evaluated_at']) : time());
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            aside, header, .no-print { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
            .print-clean { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Side Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scroll Area -->
            <main class="p-8 max-w-4xl w-full mx-auto space-y-6 flex-1 relative">

                <!-- Navigation & Action Top Bar -->
                <div class="flex items-center justify-between no-print">
                    <a href="evaluate_interns.php" class="inline-flex items-center gap-2 text-xs font-bold text-[#0F2854] hover:text-blue-900 bg-white px-4 py-2 rounded-xl border border-slate-200/80 shadow-2xs transition-all hover:bg-slate-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Back to Evaluation List</span>
                    </a>

                    <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 px-4 py-2 rounded-xl border border-slate-200 shadow-2xs transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                        <span>Print / Save as PDF</span>
                    </button>
                </div>

                <!-- Main Evaluation Document Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden p-8 space-y-7 print-clean">
                    
                    <!-- Document Header -->
                    <div class="border-b border-slate-100 pb-6 flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <?php if (!empty($evaluation['otp_verified'])): ?>
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold inline-flex items-center gap-1.5 mb-2 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Verified & Signed via Email OTP</span>
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold inline-flex items-center gap-1.5 mb-2">
                                    <span>Pending Signature</span>
                                </span>
                            <?php endif; ?>

                            <h1 class="text-xl font-extrabold text-slate-900">Final Intern Performance Appraisal</h1>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Submitted on <?= date("F d, Y \a\\t g:i A", $signedTime); ?>
                            </p>
                        </div>

                        <!-- Overall Final Rating Callout -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-center min-w-[150px] shadow-2xs">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Final Rating</span>
                            <p class="text-3xl font-black text-[#0F2854] mt-0.5"><?= number_format($evaluation['final_score'], 1); ?>%</p>
                        </div>
                    </div>

                    <!-- Student & Supervisor Metadata Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/70 rounded-2xl p-5 border border-slate-200/70 text-xs">
                        <div class="space-y-0.5">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Student Intern</span>
                            <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($evaluation['student_name']); ?></p>
                            <p class="text-slate-500">ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($evaluation['student_number']); ?></span> &bull; <?= htmlspecialchars($evaluation['program']); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['student_email']); ?></p>
                        </div>
                        <div class="space-y-0.5">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Industry Supervisor</span>
                            <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($evaluation['supervisor_name']); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['company_name'] ?? 'Host Company'); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['supervisor_email']); ?></p>
                        </div>
                    </div>

                    <!-- Performance Rating Breakdown (4 Criteria) -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Performance Criteria Breakdown</h3>

                        <div class="space-y-3 divide-y divide-slate-100 text-xs">
                            
                            <!-- Criterion 1: Technical Competence (40%) -->
                            <div class="pt-2 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">1. Technical Competence & IT Skills (40%)</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Application of technical concepts, problem-solving, and output accuracy.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-extrabold text-slate-900 text-sm"><?= number_format($evaluation['technical_score'], 1); ?>%</span>
                                    <p class="text-[10px] font-medium text-slate-400">Weighted: <?= number_format($evaluation['technical_score'] * 0.40, 2); ?>%</p>
                                </div>
                            </div>

                            <!-- Criterion 2: Work Ethics (25%) -->
                            <div class="pt-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">2. Work Ethics & Professionalism (25%)</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Reliability, professional integrity, and adherence to company policies.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-extrabold text-slate-900 text-sm"><?= number_format($evaluation['work_ethics_score'], 1); ?>%</span>
                                    <p class="text-[10px] font-medium text-slate-400">Weighted: <?= number_format($evaluation['work_ethics_score'] * 0.25, 2); ?>%</p>
                                </div>
                            </div>

                            <!-- Criterion 3: Communication (20%) -->
                            <div class="pt-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">3. Communication & Teamwork (20%)</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Clarity in verbal and written reports, collaboration with peers.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-extrabold text-slate-900 text-sm"><?= number_format($evaluation['communication_score'], 1); ?>%</span>
                                    <p class="text-[10px] font-medium text-slate-400">Weighted: <?= number_format($evaluation['communication_score'] * 0.20, 2); ?>%</p>
                                </div>
                            </div>

                            <!-- Criterion 4: Punctuality (15%) -->
                            <div class="pt-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">4. Punctuality & Attendance (15%)</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Prompt arrival, meeting project milestones, and regular schedule compliance.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-extrabold text-slate-900 text-sm"><?= number_format($evaluation['punctuality_score'], 1); ?>%</span>
                                    <p class="text-[10px] font-medium text-slate-400">Weighted: <?= number_format($evaluation['punctuality_score'] * 0.15, 2); ?>%</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Supervisor Written Remarks -->
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Supervisor Remarks & Qualitative Feedback</p>
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 text-xs text-slate-800 font-medium italic leading-relaxed">
                            <?= !empty($evaluation['remarks']) ? '"' . htmlspecialchars($evaluation['remarks']) . '"' : '<span class="text-slate-400 not-italic">No specific remarks provided.</span>'; ?>
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