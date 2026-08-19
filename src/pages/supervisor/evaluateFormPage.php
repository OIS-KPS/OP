<!-- src/pages/supervisor/evaluateFormPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate <?= htmlspecialchars($student['name']); ?> - Supervisor Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Side Main Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scroll Area -->
            <main class="p-6 max-w-4xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Navigation Back Button -->
                <div>
                    <a href="evaluate_interns.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0F2854] hover:underline bg-white px-3.5 py-2 rounded-xl border border-slate-200/80 shadow-2xs transition-all hover:bg-slate-50">
                        <span>← Back to Evaluation Roster</span>
                    </a>
                </div>

                <!-- Intern Summary Header Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-extrabold text-lg shrink-0 border border-[#0F2854]/20">
                            <?= strtoupper(substr($student['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug"><?= htmlspecialchars($student['name']); ?></h1>
                            <p class="text-slate-500 text-xs mt-0.5">
                                ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($student['student_number']); ?></span> • 
                                Program: <span class="font-semibold text-slate-700"><?= htmlspecialchars($student['program']); ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-bold">
                        ✓ 12/12 WARs Fulfilled (~486 Hours)
                    </div>
                </div>

                <!-- Evaluation Form Card -->
                <form method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden p-6 space-y-6">
                    
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900">Performance Rating Criteria</h2>
                        <p class="text-slate-400 text-xs mt-0.5">Rate the intern's overall performance across key competencies (1 = Poor, 5 = Excellent).</p>
                    </div>

                    <!-- Rating Grid Questions -->
                    <div class="space-y-5 divide-y divide-slate-100">
                        
                        <!-- Criterion 1 -->
                        <div class="pt-3 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">1. Technical Competence & IT Skills</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Application of IT concepts, problem-solving, and technical proficiency.</p>
                            </div>
                            <select name="tech_skills" required class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="5">5 - Excellent (20%)</option>
                                <option value="4" selected>4 - Very Good (16%)</option>
                                <option value="3">3 - Satisfactory (12%)</option>
                                <option value="2">2 - Fair (8%)</option>
                                <option value="1">1 - Poor (4%)</option>
                            </select>
                        </div>

                        <!-- Criterion 2 -->
                        <div class="pt-4 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">2. Quality of Work & Accuracy</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Thoroughness, attention to detail, and reliability of outputs.</p>
                            </div>
                            <select name="quality_of_work" required class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="5" selected>5 - Excellent (20%)</option>
                                <option value="4">4 - Very Good (16%)</option>
                                <option value="3">3 - Satisfactory (12%)</option>
                                <option value="2">2 - Fair (8%)</option>
                                <option value="1">1 - Poor (4%)</option>
                            </select>
                        </div>

                        <!-- Criterion 3 -->
                        <div class="pt-4 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">3. Work Ethics & Punctuality</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Adherence to company policies, timekeeping, and professionalism.</p>
                            </div>
                            <select name="work_ethic" required class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="5" selected>5 - Excellent (20%)</option>
                                <option value="4">4 - Very Good (16%)</option>
                                <option value="3">3 - Satisfactory (12%)</option>
                                <option value="2">2 - Fair (8%)</option>
                                <option value="1">1 - Poor (4%)</option>
                            </select>
                        </div>

                        <!-- Criterion 4 -->
                        <div class="pt-4 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">4. Communication & Teamwork</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Collaborative skills, clarity in communication, and adaptability.</p>
                            </div>
                            <select name="communication" required class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="5">5 - Excellent (20%)</option>
                                <option value="4" selected>4 - Very Good (16%)</option>
                                <option value="3">3 - Satisfactory (12%)</option>
                                <option value="2">2 - Fair (8%)</option>
                                <option value="1">1 - Poor (4%)</option>
                            </select>
                        </div>

                        <!-- Criterion 5 -->
                        <div class="pt-4 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">5. Initiative & Resourcefulness</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Self-motivation, willingness to learn, and proactive attitude.</p>
                            </div>
                            <select name="initiative" required class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#0F2854]">
                                <option value="5" selected>5 - Excellent (20%)</option>
                                <option value="4">4 - Very Good (16%)</option>
                                <option value="3">3 - Satisfactory (12%)</option>
                                <option value="2">2 - Fair (8%)</option>
                                <option value="1">1 - Poor (4%)</option>
                            </select>
                        </div>

                    </div>

                    <!-- Supervisor Remarks -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-800 mb-1">Supervisor Final Remarks / Recommendation</label>
                        <textarea name="remarks" rows="3" placeholder="Provide general feedback or comments regarding the intern's overall performance..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-800 focus:outline-none focus:border-[#0F2854] resize-none">Katelyn demonstrated exceptional technical growth throughout her 486 internship hours, consistently delivering quality results in full-stack web development tasks.</textarea>
                    </div>

                    <!-- Form Action Bar -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" name="submit_form" class="px-6 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer">
                            Submit →
                        </button>
                    </div>

                </form>

            </main>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- MODAL 1: EMAIL OTP SECURITY VERIFICATION -->
    <!-- ======================================================= -->
    <?php if ($currentStep === 'otp'): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-5 text-center relative my-auto">
                
                <!-- Close Button -->
                <a href="evaluate_form.php?student_id=<?= $student_id; ?>" class="absolute top-4 right-4 w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-xs font-bold transition-all">
                    ✕
                </a>

                <!-- Security Icon -->
                <div class="w-14 h-14 bg-blue-50 text-[#0F2854] rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto border border-blue-100 shadow-2xs">
                    🔒
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900">Email OTP Verification</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                        To sign and confirm this evaluation for <span class="font-bold text-slate-800"><?= htmlspecialchars($student['name']); ?></span>, please enter the 6-digit OTP code sent to your email.
                    </p>
                    <p class="text-[10px] font-semibold text-blue-600 mt-1.5 bg-blue-50/80 py-1 rounded-lg border border-blue-100">
                        [Dev Testing Code: <span class="font-bold">123456</span>]
                    </p>
                </div>

                <!-- Error Alert -->
                <?php if (!empty($error)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs p-2.5 rounded-xl font-medium">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- OTP Form -->
                <form method="POST" class="space-y-4">
                    <div>
                        <input type="text" name="otp_code" maxlength="6" required autofocus placeholder="123456" class="w-48 text-center text-xl font-mono tracking-widest bg-slate-50 border border-slate-300 rounded-xl py-2 text-slate-900 focus:outline-none focus:border-[#0F2854] focus:ring-2 focus:ring-[#0F2854]/20">
                    </div>

                    <div class="flex items-center justify-center gap-2 pt-2">
                        <a href="evaluate_form.php?student_id=<?= $student_id; ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" name="verify_otp" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs cursor-pointer">
                            Verify & Submit Evaluation
                        </button>
                    </div>
                </form>

            </div>
        </div>
    <?php endif; ?>

    <!-- ======================================================= -->
    <!-- MODAL 2: EVALUATION CONFIRMED SUCCESS MODAL -->
    <!-- ======================================================= -->
    <?php if ($currentStep === 'success'): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full overflow-hidden p-6 space-y-4 text-center relative my-auto">
                
                <!-- Success Green Checkmark Icon -->
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-3xl font-bold mx-auto border border-emerald-100 shadow-xs">
                    ✓
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900">Evaluation Confirmed!</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        The final evaluation for <span class="font-bold text-slate-800"><?= htmlspecialchars($student['name']); ?></span> has been verified and securely saved.
                    </p>
                </div>

                <!-- Calculated Final Rating Score Callout -->
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/80">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Final Performance Score</p>
                    <p class="text-xl font-extrabold text-[#0F2854] mt-0.5"><?= $calculatedScore; ?> / 100%</p>
                </div>

                <!-- Return Button -->
                <div class="pt-2">
                    <a href="evaluate_interns.php" class="w-full py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-all shadow-2xs inline-block">
                        Return to Roster →
                    </a>
                </div>

            </div>
        </div>
    <?php endif; ?>

</body>
</html>