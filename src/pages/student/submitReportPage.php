<!-- src/pages/student/submitReportPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS OJT Portal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Global Custom Stylesheet -->
    <link rel="stylesheet" href="public/css/style.css">
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
            <main class="p-8 max-w-xl w-full mx-auto space-y-5 flex-1">

                <!-- Cancel Link -->
                <div>
                    <a href="reports.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#0F2854] transition-all">
                        ← Cancel
                    </a>
                </div>

                <!-- Page Title Area -->
                <div>
                    <h2 class="text-xl font-bold text-slate-900 leading-snug">
                        Upload Your Week <?= htmlspecialchars($weekNumber); ?> Weekly Accomplishment Report
                    </h2>
                    <p class="text-slate-500 text-xs mt-1">
                        Please upload your official signed WAR document in PDF format.
                    </p>
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
                <form action="submit_report.php?week=<?= htmlspecialchars($weekNumber); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">

                    <!-- Drag & Drop PDF Box -->
                    <div id="drop-zone" class="relative border border-dashed border-slate-300 hover:border-[#0F2854] rounded-2xl p-8 bg-white hover:bg-blue-50/20 transition-all text-center cursor-pointer flex flex-col items-center justify-center min-h-[180px]">
                        
                        <input type="file" id="report_file" name="report_file" accept=".pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="handleFileSelect(this)">

                        <!-- Upload Icon -->
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 mb-2.5" id="upload-icon">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>

                        <!-- Drop Text -->
                        <p class="text-xs font-bold text-slate-800 mb-0.5" id="file-label">Click to upload or drag and drop</p>
                        <p class="text-[11px] text-slate-400 font-medium" id="file-subtext">PDF files only (Max size 10MB)</p>
                    </div>

                    <!-- Clean Action Button -->
                    <button type="submit" class="w-full py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white font-semibold rounded-xl text-xs transition-all shadow-xs">
                        Submit Week <?= htmlspecialchars($weekNumber); ?> WAR
                    </button>

                </form>

            </main>
        </div>
    </div>

    <!-- Drag & Drop Visual Script -->
    <script>
        function handleFileSelect(input) {
            const label = document.getElementById('file-label');
            const subtext = document.getElementById('file-subtext');
            const icon = document.getElementById('upload-icon');
            const dropZone = document.getElementById('drop-zone');

            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                label.textContent = "Selected: " + fileName;
                label.classList.add("text-[#0F2854]");
                subtext.textContent = "Click or drag to replace PDF";
                dropZone.classList.add("border-[#0F2854]", "bg-blue-50/30");
                icon.innerHTML = `<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            }
        }
    </script>

<?php include __DIR__ . '/../../components/password_change_popup.php'; ?>
</body>
</html>