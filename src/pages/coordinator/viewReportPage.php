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
        
        <!-- Sidebar -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Top Header Bar -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="approved_reports.php" class="p-2 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-slate-600 transition-all text-xs font-bold shadow-2xs">
                            ← Back
                        </a>
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug">
                                Week <?= htmlspecialchars($report['week_number']); ?> Accomplishment Report
                            </h1>
                            <p class="text-slate-500 text-xs">
                                Student: <strong class="text-slate-800"><?= htmlspecialchars($report['student_name']); ?></strong> (<?= htmlspecialchars($report['student_number']); ?>) &bull; <?= htmlspecialchars($report['company_name'] ?? 'Host Agency'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200/80">
                            ✓ Status: <?= ucfirst(htmlspecialchars($report['status'])); ?>
                        </span>

                        <?php if (!empty($report['file_path'])): ?>
                            <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/')); ?>" target="_blank" class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-all shadow-2xs flex items-center gap-1.5">
                                <span>📥</span> Open Full PDF
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2-Column Split: PDF on Left (60%), Extracted Entities on Right (40%) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    <!-- Left: PDF Viewer (7 of 12 columns) -->
                    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col h-[750px]">
                        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                            <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                <span>📄</span> Submitted WAR Document
                            </span>
                            <span class="text-[11px] text-slate-400">
                                Logged: <?= !empty($report['submitted_at']) ? date('M d, Y', strtotime($report['submitted_at'])) : 'N/A'; ?>
                            </span>
                        </div>

                        <div class="flex-1 bg-slate-100 relative">
                            <?php if (!empty($report['file_path'])): ?>
                                <iframe 
                                    src="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/')); ?>#toolbar=0" 
                                    class="w-full h-full border-none"
                                    title="Report PDF">
                                </iframe>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs p-6 text-center">
                                    <span class="text-3xl mb-2">📁</span>
                                    <p class="font-bold text-slate-600">No PDF Attached</p>
                                    <p class="text-[11px]">No document file path exists for this submission.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Extracted Entities, Task Percentages & Excerpt (5 of 12 columns) -->
                    <div class="lg:col-span-5 space-y-5">

                        <!-- Report Task Ratio Card -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-xs text-slate-900">Task Ratio Breakdown</h3>
                                <span class="text-[11px] font-semibold text-slate-400">
                                    <?= count($extractedEntities); ?> Total <?= count($extractedEntities) === 1 ? 'Entity' : 'Entities'; ?>
                                </span>
                            </div>

                            <!-- Ratio Badges & Progress Bar -->
                            <?php if (count($extractedEntities) > 0): ?>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-[#0F2854] flex items-center gap-1.5">
                                            <span>💻</span> IT Percentage: <?= $itPct; ?>%
                                        </span>
                                        <span class="text-rose-600 flex items-center gap-1.5">
                                            <span>📁</span> Clerical: <?= $clericalPct; ?>%
                                        </span>
                                    </div>
                                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden flex border border-slate-200/70 shadow-inner">
                                        <div class="bg-[#0F2854] h-full transition-all duration-300" style="width: <?= $itPct; ?>%"></div>
                                        <div class="bg-rose-500 h-full transition-all duration-300" style="width: <?= $clericalPct; ?>%"></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-xs text-slate-400 italic">Add or extract entities to compute task ratios.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Entities Panel Card -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
                            
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                                <div>
                                    <h2 class="font-bold text-xs text-slate-900">Extracted Entities</h2>
                                    <p class="text-[11px] text-slate-400">Named entities & confidence levels</p>
                                </div>

                                <button type="button" onclick="openAddEntityModal()" class="px-3 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold text-xs rounded-xl transition-all shadow-2xs flex items-center gap-1 cursor-pointer">
                                    <span>+</span> Add Entity
                                </button>
                            </div>

                            <!-- Extracted Entity List -->
                            <div class="p-4 space-y-2.5 max-h-[380px] overflow-y-auto">
                                <?php if (empty($extractedEntities)): ?>
                                    <div class="py-10 text-center text-slate-400">
                                        <div class="text-2xl mb-1">🔍</div>
                                        <p class="font-semibold text-slate-600 text-xs">No extraction yet</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 max-w-xs mx-auto">
                                            spaCy NLP pipeline is pending. You can click <strong>+ Add Entity</strong> above to manually tag activities.
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($extractedEntities as $entity): 
                                        $conf = floatval($entity['confidence_score'] ?? 100);
                                    ?>
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 text-xs truncate">
                                                    <?= htmlspecialchars($entity['entity_name']); ?>
                                                </p>
                                                
                                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                                    <span class="px-2 py-0.5 bg-slate-200/70 text-slate-700 rounded text-[10px] font-semibold">
                                                        <?= htmlspecialchars($entity['category']); ?>
                                                    </span>

                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $entity['classification'] === 'Technical' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'; ?>">
                                                        <?= htmlspecialchars($entity['classification']); ?>
                                                    </span>

                                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-md text-[10px] font-mono font-bold" title="spaCy NER Confidence Level">
                                                        <?= number_format($conf, 0); ?>% confidence
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Delete Button -->
                                            <form method="POST" action="view_report.php?id=<?= $report['id']; ?>" onsubmit="return confirm('Remove this entity?');">
                                                <input type="hidden" name="action" value="delete_entity">
                                                <input type="hidden" name="entity_id" value="<?= $entity['id']; ?>">
                                                <button type="submit" class="text-slate-400 hover:text-rose-600 p-1 text-xs font-bold" title="Delete">✕</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                        </div>

                        <!-- Activity Excerpt (OCR / Log) -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-2">
                            <h3 class="font-bold text-xs text-slate-900">Activity Excerpt (OCR / Log)</h3>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 text-xs text-slate-700 max-h-40 overflow-y-auto leading-relaxed">
                                <?= !empty($report['ocr_activities']) ? nl2br(htmlspecialchars($report['ocr_activities'])) : '<span class="text-slate-400 italic">No text excerpt provided.</span>'; ?>
                            </div>
                        </div>

                    </div>

                </div>

            </main>
        </div>
    </div>

    <!-- Add Entity Modal -->
    <div id="addEntityModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden">
            
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-900">Add Extracted Entity / Task</h3>
                <button type="button" onclick="closeAddEntityModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="view_report.php?id=<?= $report['id']; ?>" class="p-5 space-y-4 text-xs">
                <input type="hidden" name="action" value="add_entity">

                <!-- Entity Name -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Entity / Skill Name:</label>
                    <input type="text" name="entity_name" required placeholder="e.g. PHP REST API, Database Query, Network Setup" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#0F2854]">
                </div>

                <!-- Curriculum Category -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Category:</label>
                    <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#0F2854]">
                        <option value="Software Dev">Software Dev</option>
                        <option value="Database">Database</option>
                        <option value="Networking">Networking</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Administrative">Administrative</option>
                    </select>
                </div>

                <!-- Classification -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Classification:</label>
                    <div class="grid grid-cols-2 gap-3 pt-0.5">
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 font-medium">
                            <input type="radio" name="classification" value="Technical" checked class="text-[#0F2854]">
                            <span>Technical (IT)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 font-medium">
                            <input type="radio" name="classification" value="Clerical" class="text-[#0F2854]">
                            <span>Clerical (Non-IT)</span>
                        </label>
                    </div>
                </div>

                <!-- Confidence Level Input -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Confidence Level (%):</label>
                    <input type="number" name="confidence_score" min="1" max="100" value="100" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#0F2854]">
                    <p class="text-[10px] text-slate-400 mt-1">Defaults to 100% for manual coordinator entries.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddEntityModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs shadow-2xs">
                        Save Entity
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Modal Toggle Scripts -->
    <script>
        function openAddEntityModal() {
            document.getElementById('addEntityModal').classList.remove('hidden');
        }
        function closeAddEntityModal() {
            document.getElementById('addEntityModal').classList.add('hidden');
        }
    </script>

</body>
</html>