<!-- src/pages/supervisor/evaluateFormPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate <?= htmlspecialchars($student['name'] ?? 'Student'); ?> - Supervisor Portal</title>
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
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-4xl w-full mx-auto space-y-6 flex-1 relative">

                <!-- Navigation -->
                <div>
                    <a href="evaluate_interns.php" class="inline-flex items-center gap-2 text-xs font-bold text-[#0F2854] hover:text-blue-900 bg-white px-4 py-2 rounded-xl border border-slate-200/80 shadow-2xs transition-all hover:bg-slate-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Back to Evaluations</span>
                    </a>
                </div>

                <!-- Student Info Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0F2854] flex items-center justify-center font-bold text-base border border-blue-100 shrink-0">
                            <?= strtoupper(substr($student['name'] ?? 'S', 0, 1)); ?>
                        </div>
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug"><?= htmlspecialchars($student['name'] ?? 'Student'); ?></h1>
                            <p class="text-xs text-slate-500 font-medium">ID: <?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?> &bull; <?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                        12 Weeks Verified
                    </span>
                </div>

                <!-- Evaluation Form -->
                <form id="evaluationForm" onsubmit="handleEvaluationSubmit(event)" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-7 space-y-6">
                    <input type="hidden" id="eval_student_id" value="<?= (int)($student['id'] ?? 0); ?>">

                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Performance Criteria Rating (1 - 100)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Rate each competency category based on the intern's actual work output.</p>
                    </div>

                    <!-- Rating Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Technical Competence (40%)</label>
                            <input type="number" step="0.1" min="50" max="100" id="tech_score" required placeholder="e.g., 90.0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:outline-none focus:border-[#0F2854] font-medium">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Work Ethics & Professionalism (25%)</label>
                            <input type="number" step="0.1" min="50" max="100" id="ethics_score" required placeholder="e.g., 92.5" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:outline-none focus:border-[#0F2854] font-medium">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Communication Skills (20%)</label>
                            <input type="number" step="0.1" min="50" max="100" id="comm_score" required placeholder="e.g., 88.0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:outline-none focus:border-[#0F2854] font-medium">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Punctuality & Attendance (15%)</label>
                            <input type="number" step="0.1" min="50" max="100" id="punct_score" required placeholder="e.g., 95.0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:outline-none focus:border-[#0F2854] font-medium">
                        </div>
                    </div>

                    <!-- Feedback Field -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 text-xs">Supervisor Comments & Recommendation</label>
                        <textarea id="feedback" rows="4" placeholder="Write qualitative remarks regarding the student's performance and career readiness..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:outline-none focus:border-[#0F2854] font-medium"></textarea>
                    </div>

                    <!-- Form Action Button -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="evaluate.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                            <span>Sign & Submit Evaluation</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </form>

            </main>
        </div>
    </div>

    <!-- ============================================================
         OTP VERIFICATION MODAL (Exact match to Wireframe)
         ============================================================ -->
    <div id="otpModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-7 relative space-y-5 animate-in fade-in zoom-in duration-200">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">OTP verification</h3>
                <button onclick="closeOtpModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold p-1">✕</button>
            </div>

            <!-- Mail Graphic -->
            <div class="text-center space-y-1">
                <div class="w-14 h-14 bg-slate-100 border border-slate-200 rounded-full flex items-center justify-center mx-auto text-slate-600 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 pt-2">Check your Gmail</h4>
                <p class="text-xs text-slate-500">
                    Enter the 6-digit OTP sent to <strong id="otpEmailTarget" class="text-slate-800 font-semibold">your email</strong>
                </p>
            </div>

            <!-- 6-Box Form -->
            <form onsubmit="handleOtpVerify(event)" class="space-y-4">
                <div class="flex justify-center gap-2" id="otpBoxContainer">
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-bold text-slate-900 bg-slate-100 focus:bg-white border border-slate-200 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-bold text-slate-900 bg-slate-100 focus:bg-white border border-slate-200 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-bold text-slate-900 bg-slate-100 focus:bg-white border border-slate-200 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-bold text-slate-900 bg-slate-100 focus:bg-white border border-slate-200 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-bold text-slate-900 bg-slate-100 focus:bg-white border border-slate-200 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-bold text-slate-900 bg-slate-100 focus:bg-white border border-slate-200 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all" />
                </div>

                <p id="otpErrorMsg" class="text-rose-600 text-xs font-semibold text-center hidden"></p>

                <div class="text-center text-xs">
                    <span id="resendTimerText" class="text-slate-400">Resend OTP in <strong id="timerCountdown" class="text-slate-600">0:45</strong></span>
                    <button type="button" id="resendOtpBtn" onclick="requestOtpCode()" class="text-[#0F2854] font-bold hover:underline hidden">
                        Resend Code
                    </button>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeOtpModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="verifyBtn" class="px-6 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                        Verify & Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let countdownTimer = null;

        function handleEvaluationSubmit(e) {
            e.preventDefault();
            document.getElementById('otpModal').classList.remove('hidden');
            clearOtpInputs();
            requestOtpCode();
        }

        function closeOtpModal() {
            document.getElementById('otpModal').classList.add('hidden');
            if (countdownTimer) clearInterval(countdownTimer);
        }

        function clearOtpInputs() {
            document.querySelectorAll('.otp-box').forEach(input => input.value = '');
            document.getElementById('otpErrorMsg').classList.add('hidden');
        }

        async function requestOtpCode() {
            const studentId = document.getElementById('eval_student_id').value;
            document.getElementById('otpErrorMsg').classList.add('hidden');
            document.getElementById('resendOtpBtn').classList.add('hidden');
            document.getElementById('resendTimerText').classList.remove('hidden');

            try {
                const res = await fetch('/ICS-PORTAL/supervisor/api/evaluation_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'request_otp', student_id: studentId })
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('otpEmailTarget').innerText = data.email;
                    startCountdown(45);
                    document.querySelector('.otp-box').focus();
                } else {
                    showOtpError(data.error || 'Failed to dispatch verification code.');
                }
            } catch (err) {
                showOtpError('Connection error. Please try again.');
            }
        }

        function startCountdown(seconds) {
            if (countdownTimer) clearInterval(countdownTimer);
            let remaining = seconds;
            const timerEl = document.getElementById('timerCountdown');
            const textEl = document.getElementById('resendTimerText');
            const resendBtn = document.getElementById('resendOtpBtn');

            countdownTimer = setInterval(() => {
                remaining--;
                const mins = Math.floor(remaining / 60);
                const secs = remaining % 60;
                timerEl.innerText = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
                if (remaining <= 0) {
                    clearInterval(countdownTimer);
                    textEl.classList.add('hidden');
                    resendBtn.classList.remove('hidden');
                }
            }, 1000);
        }

        function showOtpError(msg) {
            const err = document.getElementById('otpErrorMsg');
            err.innerText = msg;
            err.classList.remove('hidden');
        }

        async function handleOtpVerify(e) {
            e.preventDefault();
            const boxes = document.querySelectorAll('.otp-box');
            let code = '';
            boxes.forEach(b => code += b.value.trim());

            if (code.length !== 6) {
                showOtpError('Please enter all 6 digits.');
                return;
            }

            const verifyBtn = document.getElementById('verifyBtn');
            verifyBtn.innerText = 'Verifying...';
            verifyBtn.disabled = true;

            const payload = {
                action: 'verify_and_submit_evaluation',
                student_id: document.getElementById('eval_student_id').value,
                technical_score: document.getElementById('tech_score').value,
                work_ethics_score: document.getElementById('ethics_score').value,
                communication_score: document.getElementById('comm_score').value,
                punctuality_score: document.getElementById('punct_score').value,
                feedback: document.getElementById('feedback').value,
                otp: code
            };

            try {
                const res = await fetch('/ICS-PORTAL/supervisor/api/evaluation_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    window.location.href = 'evaluate_interns.php?evaluated=success';
                } else {
                    showOtpError(data.error || 'Verification failed.');
                    verifyBtn.innerText = 'Verify & Submit';
                    verifyBtn.disabled = false;
                }
            } catch (err) {
                showOtpError('Connection error during verification.');
                verifyBtn.innerText = 'Verify & Submit';
                verifyBtn.disabled = false;
            }
        }

        document.querySelectorAll('.otp-box').forEach((box, idx, arr) => {
            box.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && idx < arr.length - 1) {
                    arr[idx + 1].focus();
                }
            });
            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                    arr[idx - 1].focus();
                }
            });
        });
    </script>
</body>
</html>