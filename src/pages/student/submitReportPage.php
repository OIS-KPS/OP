<!-- src/pages/student/submitReportPage.php -->
<?php require_once __DIR__ . '/../../../config/csrf.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Week <?= htmlspecialchars($weekNumber); ?> Report - Student Portal</title>
    <!-- Inter Font.
         This was the only student page that did not load it, so it rendered in
         the system sans-serif while every other screen used Inter. That mismatch
         is most of why the page looked like it belonged to a different app. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Global Custom Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_PATH; ?>/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Reusable Sidebar Component -->
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <!-- Right Side: Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Reusable Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Scrollable Body -->
            <main class="p-4 sm:p-8 max-w-xl w-full mx-auto space-y-5 flex-1">

                <!-- Cancel Link -->
                <div>
                    <a href="reports.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#0F2854] transition-colors">
                        ← Cancel
                    </a>
                </div>

                <!-- Page Title Area -->
                <div>
                    <h1 class="text-xl font-bold text-slate-900 leading-snug">
                        Submit Week <?= htmlspecialchars($weekNumber); ?> Report
                    </h1>
                    <p class="text-slate-500 text-xs mt-1">
                        <?php /* No "signed" and no "official" here. Nothing in the
                                 weekly flow involves a signature -- the WAR template the
                                 intern downloads has no signature field -- and every
                                 signature in this app is a digital OTP on the *final
                                 evaluation*, where export_evaluation.php states plainly
                                 that no physical signature is required. "Completed"
                                 states what the intern actually did, and the second
                                 sentence reuses the dashboard's own wording at
                                 dashboardPage.php:190. */ ?>
                        Upload your completed WAR (Weekly Accomplishment Report) as a PDF. Your supervisor will review and verify it.
                    </p>
                    <?php if (!empty($weekDue)): ?>
                        <p class="text-xs font-semibold mt-2 <?= !empty($isLatePreview) ? 'text-orange-700' : 'text-slate-600'; ?>">
                            Deadline: <?= htmlspecialchars($weekDue->format('M d, Y')); ?>
                            <?php if (!empty($isLatePreview)): ?>
                                — this submission will be marked as <span class="font-bold">Late Submission</span>.
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Validation Error Area -->
                <?php if (!empty($errors)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs space-y-1">
                        <p class="font-bold">Submission error:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Submission Form -->
                <?php if (!empty($isAssigned)): ?>
                <form action="submit_report.php?week=<?= htmlspecialchars($weekNumber); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <?= csrf_field(); ?>

                    <!-- Drag & Drop PDF Box -->
                    <div id="drop-zone" class="relative border border-dashed border-slate-300 hover:border-[#0F2854] rounded-2xl p-6 sm:p-8 bg-white hover:bg-blue-50/20 transition-colors text-center cursor-pointer flex flex-col items-center justify-center min-h-[180px]">
                        
                        <input type="file" id="report_file" name="report_file" accept=".pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="handleFileSelect(this)">

                        <!-- Upload Icon -->
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 mb-2.5" id="upload-icon">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>

                        <!-- Drop Text -->
                        <p class="text-xs font-bold text-slate-800 mb-0.5" id="file-label">Click to upload or drag and drop</p>
                        <p class="text-[11px] text-slate-400 font-medium" id="file-subtext">PDF files only &middot; Max <?= uploadLimitLabel() ?></p>
                    </div>

                    <!-- Clean Action Button -->
                    <button type="submit" id="submit-war-btn" class="w-full py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                        Submit Week <?= htmlspecialchars($weekNumber); ?> Report
                    </button>

                </form>
                <?php else: ?>
                    <!-- Not Assigned Blocking Notice -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-8 text-center space-y-4">
                        <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"></path>
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-extrabold text-sm text-slate-950">No Placement Assigned Yet</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Your OJT Coordinator has not assigned your internship placement yet. You can submit your weekly accomplishment reports once your company and supervisor have been assigned.
                            </p>
                        </div>
                        <div class="pt-2">
                            <a href="reports.php" class="w-full py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors inline-block shadow-xs">
                                Return to Reports Dashboard
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <!-- Coordinator Evaluation Notice Modal (ONLY shows when explicitly triggered by Coordinator) -->
    <?php if (!empty($isLocked)): ?>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 shadow-2xl space-y-4 text-center">
                <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"></path>
                            </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-sm text-slate-950">Final Evaluation Requested</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        The OJT Coordinator has requested your final evaluation from your industry supervisor. Weekly report submissions are now locked and closed.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="reports.php" class="w-full py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors inline-block shadow-xs">
                        Return to Reports Dashboard
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Drag & Drop Visual Script -->
    <script>
        // Injected from config/upload_limits.php so the browser enforces the same
        // number the server does. This used to be copy only -- handleFileSelect
        // checked nothing at all, so an oversized or non-PDF file was accepted by
        // the page and only rejected after the whole body had been uploaded.
        const MAX_UPLOAD_BYTES = <?= (int) MAX_UPLOAD_PDF_BYTES ?>;
        const MAX_UPLOAD_LABEL = '<?= uploadLimitLabel() ?>';

        // Set when the picked file is rejected. The submit handler reads it, because
        // clearing input.files does not stop the form being submitted on its own.
        let uploadRejected = false;

        function rejectSelection(message) {
            uploadRejected = true;

            const input = document.getElementById('report_file');
            if (input) {
                input.value = '';
            }

            const label = document.getElementById('file-label');
            const subtext = document.getElementById('file-subtext');
            const icon = document.getElementById('upload-icon');
            const dropZone = document.getElementById('drop-zone');

            if (label) {
                label.textContent = 'No file selected';
                label.classList.remove('text-[#0F2854]');
                label.classList.add('text-rose-600');
            }

            if (subtext) {
                subtext.textContent = message;
                subtext.classList.add('text-rose-600');
            }

            if (dropZone) {
                dropZone.classList.add('border-rose-300', 'bg-rose-50/30');
            }

            if (icon) {
                icon.innerHTML = `<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
            }
        }

        function handleFileSelect(input) {
            const label = document.getElementById('file-label');
            const subtext = document.getElementById('file-subtext');
            const icon = document.getElementById('upload-icon');
            const dropZone = document.getElementById('drop-zone');

            // Any new pick clears the previous rejection and the error styling.
            uploadRejected = false;

            if (subtext) {
                subtext.classList.remove('text-rose-600');
            }

            if (label) {
                label.classList.remove('text-rose-600');
            }

            if (dropZone) {
                dropZone.classList.remove('border-rose-300', 'bg-rose-50/30');
            }

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const isPdf = file.type === 'application/pdf'
                    || /\.pdf$/i.test(fileName);

                // Size first. It is the limit the extractor actually enforces, so
                // a file over it can never be analysed however it is named.
                if (file.size > MAX_UPLOAD_BYTES) {
                    rejectSelection(
                        'That file is too large. The maximum allowed size is '
                        + MAX_UPLOAD_LABEL + '.'
                    );
                    return;
                }

                if (file.size === 0) {
                    rejectSelection('That file appears to be empty.');
                    return;
                }

                if (!isPdf) {
                    rejectSelection('Please choose a PDF file.');
                    return;
                }

                label.textContent = "Selected: " + fileName;
                label.classList.add("text-[#0F2854]");
                subtext.textContent = "Click or drag to replace PDF";
                dropZone.classList.add("border-[#0F2854]", "bg-blue-50/30");
                icon.innerHTML = `<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            }
        }

        // Drag and drop. The zone was always called a drop-zone and the copy always
        // promised "drag and drop", but only a change event was ever wired up, so
        // in practice only clicking worked. handleFileSelect is reused for the
        // selected state, so a dropped file looks identical to a picked one.
        (function () {
            const zone = document.getElementById('drop-zone');
            const input = document.getElementById('report_file');
            if (!zone || !input) {
                return;
            }

            const ACTIVE = ['border-[#0F2854]', 'bg-blue-50/30'];

            ['dragenter', 'dragover'].forEach(function (name) {
                zone.addEventListener(name, function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    zone.classList.add.apply(zone.classList, ACTIVE);
                });
            });

            ['dragleave', 'dragend'].forEach(function (name) {
                zone.addEventListener(name, function (event) {
                    event.preventDefault();
                    zone.classList.remove.apply(zone.classList, ACTIVE);
                });
            });

            zone.addEventListener('drop', function (event) {
                event.preventDefault();
                event.stopPropagation();
                zone.classList.remove.apply(zone.classList, ACTIVE);

                if (!event.dataTransfer || !event.dataTransfer.files.length) {
                    return;
                }

                input.files = event.dataTransfer.files;
                handleFileSelect(input);
            });
        })();

        // A large upload otherwise gives no feedback at all -- the page just sits
        // there. loadingOverlay.js is deliberately not loaded here: it only fires
        // for a[data-ics-loading] links, and its card hardcodes the title
        // "Loading report", which would be wrong on an upload.
        (function () {
            const form = document.querySelector('form[enctype="multipart/form-data"]');
            if (!form) {
                return;
            }

            form.addEventListener('submit', function (event) {
                /*
                 * Rejected selections must never reach the network. Clearing
                 * input.files detaches the picked file but does not stop the form
                 * being submitted, so the check has to live here rather than in a
                 * listener of its own: the form is the event target, so both
                 * listeners would run in registration order, and a separate one
                 * would only get to act after this one had already disabled the
                 * button and rewritten its label.
                 *
                 * The server-side check in submit_report.php is the real
                 * guarantee. This only avoids wasting the upload.
                 */
                if (uploadRejected) {
                    event.preventDefault();
                    alert('Please choose a PDF file no larger than ' + MAX_UPLOAD_LABEL + '.');
                    return;
                }

                const button = document.getElementById('submit-war-btn');
                if (!button || button.disabled) {
                    return;
                }
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-not-allowed');
                button.textContent = 'Uploading…';
            });
        })();
    </script>

</body>
</html>