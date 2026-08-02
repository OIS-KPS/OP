<!-- src/pages/coordinator/viewReportPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAR Inspection - OJT Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Coordinator Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Right Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Content Area -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Navigation Header Bar -->
                <div class="bg-white rounded-2xl p-4 px-5 shadow-xs border border-slate-200/80 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="approved_reports.php" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs transition-all">
                            ←
                        </a>
                        <div>
                            <h1 class="text-sm font-bold text-slate-900 leading-snug">
                                Week <?= $report['week_number']; ?> Report — <span class="text-[#0F2854]"><?= htmlspecialchars($report['student_name']); ?></span>
                            </h1>
                            <p class="text-[11px] text-slate-400">Supervisor-verified submission and extracted activity tasks.</p>
                        </div>
                    </div>

                    <!-- Action Buttons: Status Badge + Download Summary Button -->
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 font-bold rounded-full border border-emerald-200 text-[11px] flex items-center gap-1">
                            <span>✓</span> Supervisor Approved
                        </span>

                        <!-- 📥 DOWNLOAD STUDENT SUMMARY BUTTON -->
                        <button onclick="alert('Downloading full WAR summary report for <?= htmlspecialchars($report['student_name']); ?>...')" class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-[11px] transition-all flex items-center gap-1.5 shadow-2xs cursor-pointer">
                            <span>📥</span> Download Student Summary
                        </button>
                    </div>
                </div>

                <!-- 2-Column Clean Workspace -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    <!-- LEFT COLUMN (6 Cols): Embedded PDF Viewer & WAR History -->
                    <div class="lg:col-span-6 space-y-5">
                        
                        <!-- DIRECT EMBEDDED PDF VIEWER (No Open Button!) -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                <h3 class="text-xs font-bold text-slate-900">Submitted Report (PDF Document)</h3>
                                <span class="text-[10px] font-bold text-slate-400"><?= htmlspecialchars($report['pdf_file']); ?></span>
                            </div>

                            <!-- Embedded PDF Frame -->
                            <div class="w-full h-[520px] bg-slate-100 rounded-xl overflow-hidden border border-slate-200">
                                <iframe src="/ICS-PORTAL/public/uploads/reports/<?= htmlspecialchars($report['pdf_file']); ?>#toolbar=0" class="w-full h-full border-0">
                                    <div class="p-6 text-center text-xs text-slate-500">
                                        Your browser does not support embedded PDFs. 
                                        <a href="/ICS-PORTAL/public/uploads/reports/<?= htmlspecialchars($report['pdf_file']); ?>" target="_blank" class="text-blue-600 underline font-bold">Click here to view file</a>
                                    </div>
                                </iframe>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (6 Cols): Info, Task Ratio, Extracted Tags & WAR History -->
                    <div class="lg:col-span-6 space-y-5">
                        
                        <!-- 1. Student & Placement Info -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-3">
                            <h3 class="text-xs font-bold text-slate-900 border-b border-slate-100 pb-2">Student & Placement Info</h3>
                            
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Student Intern</p>
                                    <p class="font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($report['student_name']); ?></p>
                                    <p class="text-[11px] text-slate-500">ID: <?= htmlspecialchars($report['student_number']); ?> (<?= htmlspecialchars($report['program']); ?>)</p>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Host Agency</p>
                                    <p class="font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($report['company_name']); ?></p>
                                    <p class="text-[11px] text-slate-500"><?= htmlspecialchars($report['department']); ?></p>
                                </div>
                            </div>

                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-xs flex justify-between items-center text-slate-600 font-medium">
                                <span>Verified Supervisor: <strong class="text-slate-900"><?= htmlspecialchars($report['supervisor_name']); ?></strong></span>
                                <span>Date: <strong class="text-slate-900"><?= date("M d, Y", strtotime($report['approved_at'])); ?></strong></span>
                            </div>
                        </div>

                        <!-- 2. Task Ratio (IT vs Clerical) -->
                        <?php 
                            $clericalPct = $report['clerical_ratio'];
                            $itPct = 100 - $clericalPct;
                        ?>
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-2.5">
                            <h3 class="text-xs font-bold text-slate-900 border-b border-slate-100 pb-2">Task Ratio (IT vs Clerical)</h3>
                            
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-xs font-bold">
                                    <span class="text-[#0F2854]">💻 IT Work: <?= $itPct; ?>%</span>
                                    <span class="<?= $clericalPct >= 50 ? 'text-rose-600 font-extrabold' : 'text-slate-500'; ?>">
                                        📁 Clerical Work: <?= $clericalPct; ?>%
                                    </span>
                                </div>
                                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex border border-slate-200">
                                    <div style="width: <?= $itPct; ?>%" class="bg-[#0F2854] h-full"></div>
                                    <div style="width: <?= $clericalPct; ?>%" class="bg-rose-500 h-full"></div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Extracted Activity Tags -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-3">
                            <h3 class="text-xs font-bold text-slate-900 border-b border-slate-100 pb-2">Extracted Activity Tags</h3>
                            
                            <div class="space-y-3 text-xs">
                                <div>
                                    <p class="font-bold text-blue-700 text-[11px] mb-1.5">Core IT Competencies:</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <?php if (empty($report['entities']['technical'])): ?>
                                            <span class="text-slate-400 italic">None detected</span>
                                        <?php else: ?>
                                            <?php foreach ($report['entities']['technical'] as $t): ?>
                                                <span class="px-2.5 py-1 bg-blue-50 text-[#0F2854] border border-blue-100 rounded-lg font-semibold text-[11px]">
                                                    💻 <?= htmlspecialchars($t); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <p class="font-bold text-slate-500 text-[11px] mb-1.5">Clerical Activities:</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <?php if (empty($report['entities']['clerical'])): ?>
                                            <span class="text-slate-400 italic">None detected</span>
                                        <?php else: ?>
                                            <?php foreach ($report['entities']['clerical'] as $c): ?>
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-lg font-semibold text-[11px]">
                                                    📁 <?= htmlspecialchars($c); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Clean WAR Submission History (No 40hrs badges!) -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-3">
                            <div class="border-b border-slate-100 pb-2 flex items-center justify-between">
                                <h3 class="text-xs font-bold text-slate-900">Intern WAR Submission History</h3>
                                <span class="text-[10px] text-slate-400 font-medium">Click week to switch view</span>
                            </div>

                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <?php foreach ($report['history'] as $hist): ?>
                                    <a href="view_report.php?id=<?= $report['id']; ?>&week=<?= $hist['week']; ?>" class="p-2.5 rounded-xl border <?= $hist['week'] == $report['week_number'] ? 'bg-blue-50/80 border-blue-200 font-bold' : 'bg-slate-50/60 border-slate-200/60 hover:bg-slate-100'; ?> transition-all flex items-center justify-between text-xs block">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full <?= $hist['week'] == $report['week_number'] ? 'bg-[#0F2854]' : 'bg-slate-300'; ?>"></span>
                                            <p class="text-slate-900">Week <?= $hist['week']; ?> Report</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-600">✓ Approved</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>

                </div>

            </main>
        </div>
    </div>

</body>
</html>