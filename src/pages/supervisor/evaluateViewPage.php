<!-- src/pages/supervisor/evaluateViewPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Summary - <?= htmlspecialchars($evaluation['student_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
            body { background: white !important; }
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
            <main class="p-6 max-w-4xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Navigation & Action Top Bar -->
                <div class="flex items-center justify-between no-print">
                    <a href="evaluate_interns.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0F2854] hover:underline bg-white px-3.5 py-2 rounded-xl border border-slate-200/80 shadow-2xs transition-all hover:bg-slate-50">
                        <span>← Back to Evaluation Roster</span>
                    </a>

                    <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 px-4 py-2 rounded-xl border border-slate-200 shadow-2xs transition-all cursor-pointer">
                        <span>🖨️ Print / Save as PDF</span>
                    </button>
                </div>

                <!-- Main Evaluation Document Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden p-6 md:p-8 space-y-6">
                    
                    <!-- Document Header -->
                    <div class="border-b border-slate-100 pb-5 flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold inline-flex items-center gap-1.5 mb-2">
                                <span>🔒</span>
                                <span>Verified & Signed via Email OTP</span>
                            </span>
                            <h1 class="text-xl font-extrabold text-slate-900">Final Intern Evaluation Report</h1>
                            <p class="text-xs text-slate-400 mt-0.5">Submitted on <?= date("F d, Y \a\\t g:i A", strtotime($evaluation['evaluated_at'])); ?></p>
                        </div>

                        <!-- Overall Grade Badge Callout -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-center min-w-[150px]">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Final Rating</p>
                            <p class="text-2xl font-black text-[#0F2854] mt-0.5"><?= $evaluation['final_score']; ?> / 100%</p>
                        </div>
                    </div>

                    <!-- Student & Supervisor Metadata Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/70 rounded-xl p-4 border border-slate-200/60 text-xs">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Student Intern Details</p>
                            <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($evaluation['student_name']); ?></p>
                            <p class="text-slate-500 mt-0.5">ID: <?= htmlspecialchars($evaluation['student_number']); ?> • Program: <?= htmlspecialchars($evaluation['program']); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['student_email']); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Evaluator Details</p>
                            <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($evaluation['supervisor_name']); ?></p>
                            <p class="text-slate-500 mt-0.5">Industry Supervisor</p>
                            <p class="text-slate-500"><?= htmlspecialchars($evaluation['company_name'] ?? 'OJT Host Company'); ?></p>
                        </div>
                    </div>

                    <!-- Performance Rating Breakdown -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Competency Ratings Breakdown</h3>

                        <div class="space-y-3.5 divide-y divide-slate-100 text-xs">
                            
                            <!-- Criterion 1 -->
                            <div class="pt-2 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-800">1. Technical Competence & IT Skills</p>
                                    <p class="text-[11px] text-slate-400">Application of IT concepts, problem-solving, and technical proficiency.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-900 text-sm"><?= $evaluation['scores']['tech_skills']; ?> / 5</span>
                                    <p class="text-[10px] text-slate-400">(<?= $evaluation['scores']['tech_skills'] * 4; ?>%)</p>
                                </div>
                            </div>

                            <!-- Criterion 2 -->
                            <div class="pt-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-800">2. Quality of Work & Accuracy</p>
                                    <p class="text-[11px] text-slate-400">Thoroughness, attention to detail, and reliability of outputs.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-900 text-sm"><?= $evaluation['scores']['quality_of_work']; ?> / 5</span>
                                    <p class="text-[10px] text-slate-400">(<?= $evaluation['scores']['quality_of_work'] * 4; ?>%)</p>
                                </div>
                            </div>

                            <!-- Criterion 3 -->
                            <div class="pt-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-800">3. Work Ethics & Punctuality</p>
                                    <p class="text-[11px] text-slate-400">Adherence to company policies, timekeeping, and professionalism.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-900 text-sm"><?= $evaluation['scores']['work_ethic']; ?> / 5</span>
                                    <p class="text-[10px] text-slate-400">(<?= $evaluation['scores']['work_ethic'] * 4; ?>%)</p>
                                </div>
                            </div>

                            <!-- Criterion 4 -->
                            <div class="pt-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-800">4. Communication & Teamwork</p>
                                    <p class="text-[11px] text-slate-400">Collaborative skills, clarity in communication, and adaptability.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-900 text-sm"><?= $evaluation['scores']['communication']; ?> / 5</span>
                                    <p class="text-[10px] text-slate-400">(<?= $evaluation['scores']['communication'] * 4; ?>%)</p>
                                </div>
                            </div>

                            <!-- Criterion 5 -->
                            <div class="pt-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-800">5. Initiative & Resourcefulness</p>
                                    <p class="text-[11px] text-slate-400">Self-motivation, willingness to learn, and proactive attitude.</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-900 text-sm"><?= $evaluation['scores']['initiative']; ?> / 5</span>
                                    <p class="text-[10px] text-slate-400">(<?= $evaluation['scores']['initiative'] * 4; ?>%)</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Supervisor Written Remarks -->
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Supervisor Final Remarks / Recommendation</p>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-800 font-medium italic leading-relaxed">
                            "<?= htmlspecialchars($evaluation['remarks']); ?>"
                        </div>
                    </div>

                    <!-- Lock Audit Notice -->
                    <div class="pt-4 border-t border-slate-100 text-center text-[11px] text-slate-400">
                        This evaluation record is permanently locked and digitally verified. Ref ID: <span class="font-mono text-slate-600 font-semibold">EVAL-2026-<?= str_pad($evaluation['evaluation_id'], 4, '0', STR_PAD_LEFT); ?></span>
                    </div>

                </div>

            </main>
        </div>
    </div>

<?php include __DIR__ . '/../../components/password_change_popup.php'; ?>
</body>
</html>