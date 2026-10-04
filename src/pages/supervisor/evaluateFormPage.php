<!-- src/pages/supervisor/evaluateFormPage.php -->
<?php
$ojtCriteria  = ojtEvaluationCriteria();
$ojtScale     = ojtRatingScale();
$ojtScaleKeys = array_keys($ojtScale);
$ojtTotal     = count($ojtCriteria);
// HEX_TAG/HEX_AMP keep the JSON safe to inline inside a <script> block.
$ojtJsonFlags      = JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$ojtClientCriteria = json_encode(ojtCriteriaForClient(), $ojtJsonFlags);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate <?= htmlspecialchars($student['name'] ?? 'Student'); ?> - Supervisor Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        /* Whole-cell hover/selected affordance for the 1-5 radio columns. */
        .rating-option:hover { background-color: rgba(15, 40, 84, 0.05); }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">

        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-5xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Navigation -->
                <div>
                    <a href="evaluate_interns.php" class="inline-flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 px-4 py-2 rounded-xl border border-slate-300 shadow-2xs transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Back to Evaluations</span>
                    </a>
                </div>

                <!-- Evaluator Instruction Banner (docx wording) -->
                <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-5 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <p class="text-xs text-blue-900 font-medium leading-relaxed">
                        <strong class="font-extrabold">To the Evaluator:</strong> Thank you for taking time out of your hectic schedule. Your honest opinion of our
                        student's training performance will greatly aid us in our evaluation. Please check the box that corresponds to the answer that
                        best describes the performance of the trainee.
                    </p>
                </div>

                <!-- Evaluation Form -->
                <form id="evaluationForm" onsubmit="handleEvaluationSubmit(event)" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <input type="hidden" id="eval_student_id" value="<?= (int)($student['id'] ?? 0); ?>">

                    <!-- ============================================================
                         1. TRAINEE & HOST DETAILS (docx header block)
                         ============================================================ -->
                    <section class="p-7 space-y-3 border-b border-slate-200/70">
                        <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">Trainee &amp; Host Details</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Name of Student-Trainee</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1"><?= htmlspecialchars($student['name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Course</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1"><?= htmlspecialchars($student['program'] ?? 'N/A'); ?><?= !empty($student['section']) ? ' &bull; Sec ' . htmlspecialchars($student['section']) : ''; ?></p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Inclusive Dates of Training</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1"><?= htmlspecialchars($trainingPeriod ?? 'N/A'); ?></p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Name of HTE</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1"><?= htmlspecialchars($student['company_name'] ?? 'Unassigned'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- ============================================================
                         2. COMPETENCY TABLE (all 12 questions, 1-5 scale beside)
                         ============================================================ -->
                    <section class="p-7 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">Competency Rating</h2>
                                <p class="text-[11px] font-semibold text-slate-600 mt-0.5">All <?= $ojtTotal; ?> competencies must be rated before this form can be signed.</p>
                            </div>
                            <span id="ratedPill" class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-black border border-slate-300 transition-colors">
                                <span id="ratedCount">0</span>/<?= $ojtTotal; ?> answered
                            </span>
                        </div>

                        <div id="ojtTableTop" class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full border-collapse min-w-[680px]">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th class="text-left text-[10px] font-black uppercase tracking-wider text-slate-500 px-4 py-3 w-[60%]">Competency</th>
                                        <?php foreach ($ojtScaleKeys as $value): ?>
                                            <th class="px-2 py-3 text-center w-[8%]">
                                                <span class="block text-sm font-black text-slate-900"><?= $value; ?></span>
                                                <span class="block text-[9px] font-bold text-slate-400 mt-0.5"><?= htmlspecialchars($ojtScale[$value]); ?></span>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($ojtCriteria as $index => $criterion): ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="px-4 py-3 align-top">
                                                <div class="flex items-start gap-2.5">
                                                    <span class="w-5 h-5 rounded-md bg-[#0F2854] text-white flex items-center justify-center font-black text-[10px] shrink-0 mt-0.5"><?= $index + 1; ?></span>
                                                    <span class="text-xs font-semibold text-slate-800 leading-relaxed"><?= htmlspecialchars($criterion['text']); ?></span>
                                                </div>
                                            </td>
                                            <?php foreach ($ojtScaleKeys as $value): ?>
                                                <td class="p-0 align-middle">
                                                    <label class="rating-option flex items-center justify-center h-full w-full py-3 cursor-pointer transition-colors">
                                                        <input
                                                            type="radio"
                                                            name="criteria[<?= htmlspecialchars($criterion['key']); ?>]"
                                                            value="<?= $value; ?>"
                                                            data-criterion="<?= htmlspecialchars($criterion['key']); ?>"
                                                            data-group="<?= htmlspecialchars($criterion['group']); ?>"
                                                            required
                                                            class="rating-input w-4 h-4 rounded border-slate-300 text-[#0F2854] focus:ring-[#0F2854] cursor-pointer"
                                                        >
                                                    </label>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Legend (docx places it directly under the competency table) -->
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Legend</span>
                            <?php foreach ($ojtScaleKeys as $value): ?>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-700">
                                    <span class="w-5 h-5 rounded-md bg-slate-100 border border-slate-300 flex items-center justify-center font-black text-slate-600 text-[10px]"><?= $value; ?></span>
                                    <?= htmlspecialchars($ojtScale[$value]); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <p id="ratingError" class="text-rose-600 text-xs font-bold hidden"></p>
                    </section>

                    <div class="border-t border-slate-200/70"></div>

                    <!-- ============================================================
                         3. COMMENTS / REMARKS
                         ============================================================ -->
                    <section class="p-7 space-y-2">
                        <label class="block font-bold text-slate-800 text-xs" for="feedback">Comments / Remarks</label>
                        <textarea id="feedback" rows="4" placeholder="Write qualitative remarks regarding the student's performance and career readiness..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:outline-none focus:border-[#0F2854] font-medium text-slate-900"></textarea>
                    </section>

                    <!-- ============================================================
                         4. EVALUATOR SIGNATURE BLOCK
                         ============================================================ -->
                    <section class="p-7 space-y-3 border-t border-slate-200/70">
                        <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">Evaluator Details</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Evaluator's Signature over Printed Name</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1"><?= htmlspecialchars($supervisor['name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Position / Designation</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1"><?= !empty($supervisor['company_department']) ? htmlspecialchars($supervisor['company_department']) : 'OJT Supervisor'; ?></p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Office / Division Name</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1"><?= htmlspecialchars($supervisor['company_name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Date Signed</span>
                                <p class="text-xs font-extrabold text-slate-950 mt-1">Recorded on OTP verification</p>
                            </div>
                        </div>
                    </section>

                    <!-- ============================================================
                         5. SIGN-OFF NOTE
                         The overall rating and the per-group breakdown are deliberately
                         NOT rendered here: they are coordinator-only.
                         ============================================================ -->
                   

                    <!-- Form Action Button -->
                    <div class="px-7 py-5 border-t border-slate-200/70 bg-white flex items-center justify-end gap-3">
                        <a href="evaluate_interns.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-all">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                            <span>Sign &amp; Submit Evaluation</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </form>


            </main>
        </div>
    </div>

    <!-- ============================================================
         OTP VERIFICATION MODAL
         ============================================================ -->
    <div id="otpModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-300 shadow-2xl max-w-md w-full p-7 relative space-y-5 animate-in fade-in zoom-in duration-200">

            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <h3 class="text-sm font-black text-slate-900 tracking-tight">OTP Verification</h3>
                <button onclick="closeOtpModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-xs font-bold flex items-center justify-center border border-slate-300 transition-all cursor-pointer">✕</button>
            </div>

            <!-- Mail Graphic -->
            <div class="text-center space-y-1">
                <div class="w-14 h-14 bg-slate-100 border border-slate-300 rounded-full flex items-center justify-center mx-auto text-slate-700 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <h4 class="text-sm font-black text-slate-950 pt-2">Check your Gmail</h4>
                <p class="text-xs text-slate-600 font-medium">
                    Enter the 6-digit OTP sent to <strong id="otpEmailTarget" class="text-slate-950 font-extrabold">your email</strong>
                </p>
            </div>

            <!-- Ratings-to-sign recap: counts only, never the overall score -->
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Competencies Rated to Sign</span>
                <span class="text-sm font-black text-[#0F2854]"><span id="otpRatedRecap">0</span><span class="text-[10px] text-slate-400 font-bold">/12</span></span>
            </div>

            <!-- 6-Box Form -->
            <form onsubmit="handleOtpVerify(event)" class="space-y-4">
                <div class="flex justify-center gap-2" id="otpBoxContainer">
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-black text-slate-950 bg-slate-100 focus:bg-white border border-slate-300 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all shadow-2xs" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-black text-slate-950 bg-slate-100 focus:bg-white border border-slate-300 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all shadow-2xs" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-black text-slate-950 bg-slate-100 focus:bg-white border border-slate-300 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all shadow-2xs" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-black text-slate-950 bg-slate-100 focus:bg-white border border-slate-300 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all shadow-2xs" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-black text-slate-950 bg-slate-100 focus:bg-white border border-slate-300 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all shadow-2xs" />
                    <input type="text" maxlength="1" class="otp-box w-11 h-12 text-center text-lg font-black text-slate-950 bg-slate-100 focus:bg-white border border-slate-300 focus:border-[#0F2854] rounded-xl focus:outline-none transition-all shadow-2xs" />
                </div>

                <p id="otpErrorMsg" class="text-rose-600 text-xs font-bold text-center hidden"></p>

                <div class="text-center text-xs">
                    <span id="resendTimerText" class="text-slate-500 font-medium">Resend OTP in <strong id="timerCountdown" class="text-slate-800 font-bold">0:45</strong></span>
                    <button type="button" id="resendOtpBtn" onclick="requestOtpCode()" class="text-[#0F2854] font-extrabold hover:underline hidden">
                        Resend Code
                    </button>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="closeOtpModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="verifyBtn" class="px-6 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                        Verify &amp; Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Descriptor metadata only (no competency wording) so the browser can
        // recompute averages without referencing the web-blocked config/ path.
        const OJT_CRITERIA = <?= $ojtClientCriteria; ?>;
        const OJT_TOTAL = <?= $ojtTotal; ?>;

        let countdownTimer = null;
        let pendingRatings = null;

        function collectRatings() {
            const ratings = {};
            document.querySelectorAll('.rating-input:checked').forEach(input => {
                ratings[input.dataset.criterion] = parseInt(input.value, 10);
            });
            return ratings;
        }

        // Counts answered competencies only. The overall rating and the group
        // averages are deliberately NOT computed on the supervisor side; the
        // server derives them on submit and only the coordinator sees them.
        function computeSummary(ratings) {
            return {
                ratedCount: OJT_CRITERIA.filter(c => ratings[c.key] > 0).length
            };
        }

        function refreshSummary() {
            const ratings = collectRatings();
            const summary = computeSummary(ratings);

            document.getElementById('ratedCount').innerText = summary.ratedCount;
            document.getElementById('otpRatedRecap').innerText = summary.ratedCount;

            const pill = document.getElementById('ratedPill');
            const complete = summary.ratedCount === OJT_TOTAL;
            pill.classList.toggle('bg-emerald-50', complete);
            pill.classList.toggle('text-emerald-800', complete);
            pill.classList.toggle('border-emerald-300', complete);
            pill.classList.toggle('bg-slate-100', !complete);
            pill.classList.toggle('text-slate-700', !complete);
            pill.classList.toggle('border-slate-300', !complete);

            // NOTE: the overall rating and the per-group averages are intentionally
            // not displayed here. They are coordinator-only.

            return summary;
        }

        function handleEvaluationSubmit(e) {
            e.preventDefault();

            const summary = refreshSummary();
            if (summary.ratedCount < OJT_TOTAL) {
                const err = document.getElementById('ratingError');
                err.innerText = 'Please rate all ' + OJT_TOTAL + ' competencies before signing. ' +
                    (OJT_TOTAL - summary.ratedCount) + ' remaining.';
                err.classList.remove('hidden');
                document.getElementById('ojtTableTop').scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }

            document.getElementById('ratingError').classList.add('hidden');
            pendingRatings = collectRatings();
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
            document.getElementById('otpErrorMsg').classList.add('hidden');
            document.getElementById('resendOtpBtn').classList.add('hidden');
            document.getElementById('resendTimerText').classList.remove('hidden');

            try {
                const res = await fetch('/ICS-PORTAL/supervisor/api/evaluation_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'request_otp', student_id: document.getElementById('eval_student_id').value })
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
                criteria_ratings: pendingRatings || collectRatings(),
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

        // Tint the whole cell of the chosen option, and keep the counter in sync.
        document.querySelectorAll('.rating-input').forEach(input => {
            input.addEventListener('change', () => {
                document.querySelectorAll('.rating-option').forEach(opt => {
                    const radio = opt.querySelector('.rating-input');
                    opt.style.backgroundColor = (radio && radio.checked) ? 'rgba(15, 40, 84, 0.10)' : '';
                });
                refreshSummary();
                document.getElementById('ratingError').classList.add('hidden');
            });
        });

        refreshSummary();
    </script>
</body>
</html>
