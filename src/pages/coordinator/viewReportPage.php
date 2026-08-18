<!-- src/pages/coordinator/viewReportPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Inspection - Week <?= htmlspecialchars($report['week_number']); ?> - OJT Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Right Side Main View -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Content Area -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-6 flex-1 relative">

                <!-- Navigation & Action Header -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="approved_reports.php" class="p-2 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-slate-600 transition-all text-xs font-bold shadow-2xs">
                            ← Back to Approved WARs
                        </a>
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug">
                                Week <?= htmlspecialchars($report['week_number']); ?> Weekly Activity Report
                            </h1>
                            <p class="text-slate-500 text-xs">
                                Submitted by <span class="font-semibold text-slate-700"><?= htmlspecialchars($report['student_name']); ?></span> (ID: <?= htmlspecialchars($report['student_number']); ?>)
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold border shadow-2xs <?= strtolower($report['status']) === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'; ?>">
                            Status: <?= ucfirst(htmlspecialchars($report['status'])); ?>
                        </span>
                        
                        <?php if (!empty($report['file_path'])): ?>
                            <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/')); ?>" target="_blank" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs flex items-center gap-1.5">
                                <span>📥</span> Open Original PDF
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Profile & Placement Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    
                    <!-- Card 1: Student Details -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-2">
                        <div class="flex items-center gap-2 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <span>👤</span> Student Trainee
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($report['student_name']); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($report['student_email']); ?></p>
                            <p class="text-[11px] text-slate-400 mt-1">Course: <?= htmlspecialchars($report['program'] ?? 'BSIT'); ?></p>
                        </div>
                    </div>

                    <!-- Card 2: Company / Host Office -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-2">
                        <div class="flex items-center gap-2 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <span>🏢</span> Partner Agency
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($report['company_name'] ?? 'Unassigned Host'); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($report['company_dept'] ?? 'Main Office'); ?></p>
                            <p class="text-[11px] text-slate-400 mt-1">Host Training Establishment</p>
                        </div>
                    </div>

                    <!-- Card 3: Industry Supervisor -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-2">
                        <div class="flex items-center gap-2 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <span>👔</span> Supervisor Evaluator
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($report['supervisor_name'] ?? 'Unassigned Supervisor'); ?></p>
                            <p class="text-slate-500"><?= htmlspecialchars($report['supervisor_email'] ?? 'No email on record'); ?></p>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-1">● Verified Industry Supervisor</p>
                        </div>
                    </div>

                </div>

                <!-- Main Grid: Report Viewer & Accomplishment Details -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left: PDF Embedded Viewer (2 Columns) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col h-[700px]">
                        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <h2 class="font-bold text-xs text-slate-800 flex items-center gap-2">
                                <span>📄</span> Submitted WAR Document
                            </h2>
                            <span class="text-[11px] text-slate-400">
                                Submitted: <?= !empty($report['submitted_at']) ? date('M d, Y h:i A', strtotime($report['submitted_at'])) : 'N/A'; ?>
                            </span>
                        </div>

                        <div class="flex-1 bg-slate-100 relative">
                            <?php if (!empty($report['file_path'])): ?>
                                <iframe 
                                    src="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/')); ?>#toolbar=0" 
                                    class="w-full h-full border-none"
                                    title="Student Weekly Report PDF">
                                </iframe>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs p-6 text-center">
                                    <span class="text-3xl mb-2">📁</span>
                                    <p class="font-bold text-slate-600">No Document Attached</p>
                                    <p class="text-[11px]">No PDF file path is recorded for this submission entry.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Extracted Activities & Multi-Week History (1 Column) -->
                    <div class="space-y-5 flex flex-col">

                        <!-- Extracted Accomplishment Text Card -->
                        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <h2 class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    <span>📝</span> Logged Accomplishments
                                </h2>
                                <span class="text-[10px] font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                                    Week <?= htmlspecialchars($report['week_number']); ?>
                                </span>
                            </div>

                            <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-200/60 text-xs text-slate-700 leading-relaxed max-h-60 overflow-y-auto">
                                <?php if (!empty($report['ocr_activities'])): ?>
                                    <p class="whitespace-pre-line"><?= htmlspecialchars($report['ocr_activities']); ?></p>
                                <?php else: ?>
                                    <p class="text-slate-400 italic">No text excerpt extracted for this report.</p>
                                <?php endif; ?>
                            </div>

                            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-1">
                                <span>OCR Text Extraction</span>
                                <span>SpaCy AI: <em class="text-slate-500 font-semibold">Pending Integration</em></span>
                            </div>
                        </div>

                        <!-- Submission History Timeline for this Trainee -->
                        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3 flex-1">
                            <h2 class="font-bold text-xs text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-1.5">
                                <span>⏱️</span> Intern Submission Timeline
                            </h2>

                            <div class="space-y-2.5 overflow-y-auto max-h-[300px] pr-1 text-xs">
                                <?php if (empty($studentHistory)): ?>
                                    <p class="text-slate-400 italic text-center py-4">No other reports on file.</p>
                                <?php else: ?>
                                    <?php foreach ($studentHistory as $hist): 
                                        $isCurrent = intval($hist['id']) === intval($report['id']);
                                    ?>
                                        <a href="view_report.php?id=<?= $hist['id']; ?>" class="block p-3 rounded-xl border transition-all <?= $isCurrent ? 'bg-[#0F2854]/5 border-[#0F2854]/30 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200/70'; ?>">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-bold text-slate-900 <?= $isCurrent ? 'text-[#0F2854]' : ''; ?>">
                                                    Week <?= htmlspecialchars($hist['week_number']); ?>
                                                    <?= $isCurrent ? '<span class="text-[10px] bg-[#0F2854] text-white px-1.5 py-0.2 rounded-md ml-1 font-normal">Active</span>' : ''; ?>
                                                </span>
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full <?= strtolower($hist['status']) === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'; ?>">
                                                    <?= ucfirst(htmlspecialchars($hist['status'])); ?>
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-400 truncate">
                                                <?= !empty($hist['ocr_activities']) ? htmlspecialchars($hist['ocr_activities']) : 'Accomplishment report on file'; ?>
                                            </p>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                </div>

            </main>
        </div>
    </div>

</body>
</html>