<!-- src/pages/coordinator/viewReportPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Inspection - Week <?= htmlspecialchars($report['week_number']); ?> - OJT Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-6 sm:p-8 max-w-[1600px] w-full mx-auto space-y-5 flex-1 relative">

                <!-- Alert Messages -->
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black">✓</span>
                            <span><?= htmlspecialchars($_SESSION['flash_success']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 font-black hover:text-emerald-950 cursor-pointer">✕</button>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="bg-rose-50 border border-rose-300 text-rose-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-700 font-black">✕</span>
                            <span><?= htmlspecialchars($_SESSION['flash_error']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 font-black hover:text-rose-950 cursor-pointer">✕</button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <!-- Top Header Bar -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <a href="approved_reports.php" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl text-slate-800 transition-colors text-xs font-bold shadow-2xs flex items-center gap-1.5 cursor-pointer">
                            <span>←</span>
                            <span>Back</span>
                        </a>
                        <div class="space-y-0.5">
                            <h1 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight leading-snug">
                                Week <?= htmlspecialchars($report['week_number']); ?> Accomplishment Report
                            </h1>
                            <p class="text-xs font-semibold text-slate-600">
                                Student: <strong class="text-slate-900"><?= htmlspecialchars($report['student_name']); ?></strong> (<?= htmlspecialchars($report['student_number']); ?>) &bull; <?= htmlspecialchars($report['company_name'] ?? 'Host Agency'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 font-bold text-xs shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Status: <?= ucfirst(htmlspecialchars($report['status'])); ?>
                        </span>

                        <?php if (!empty($report['file_path'])): ?>
                            <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/')); ?>" target="_blank" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-colors shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <span>Open Full PDF</span>
                                <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2-Column Split: High-Height PDF (65%) & Extracted Entities (35%) -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                    <!-- Left: Extended Height PDF Stage (8 of 12 columns) -->
                    <div class="xl:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col h-[calc(100vh-210px)] min-h-[780px] max-h-[calc(100vh-210px)]">
                        <div class="p-4 sm:p-5 border-b border-slate-200/70 flex items-center justify-between bg-slate-50/60 shrink-0">
                            <div class="min-w-0">
                                <h2 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>📄</span> Submitted WAR Document
                                </h2>
                                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                    Submitted: <?= !empty($report['submitted_at']) ? date('M d, Y \a\t g:i A', strtotime($report['submitted_at'])) : 'N/A'; ?>
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[10px] font-bold text-slate-700 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> High Visibility Mode
                            </span>
                        </div>

                        <div class="flex-1 min-h-0 bg-[#e8edf4] relative">
                            <?php if (!empty($report['file_path'])): ?>
                                <div
                                    id="pdf-viewer"
                                    data-pdf-url="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="Report PDF"
                                >
                                    <div class="flex h-full items-center justify-center p-4 text-xs font-semibold text-slate-500">Loading PDF document…</div>
                                </div>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs p-6 text-center space-y-2">
                                    <div class="w-12 h-12 bg-slate-200 rounded-2xl flex items-center justify-center mx-auto text-xl">📁</div>
                                    <p class="font-bold text-slate-700">No PDF Attached</p>
                                    <p class="text-[11px] text-slate-500">No document file path exists for this submission.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Extracted Entities & Task Breakdown (4 of 12 columns) -->
                    <div class="xl:col-span-4 flex flex-col h-[calc(100vh-210px)] min-h-[780px] max-h-[calc(100vh-210px)] space-y-5">

                        <!-- Report Task Ratio Card -->
                        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs space-y-3 shrink-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">Task Ratio Breakdown</h3>
                                <span class="text-[11px] font-bold text-slate-500">
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
                                        <span class="text-rose-700 flex items-center gap-1.5">
                                            <span>📁</span> Clerical: <?= $clericalPct; ?>%
                                        </span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden flex border border-slate-200 shadow-inner">
                                        <div class="bg-[#0F2854] h-full transition-all duration-300" style="width: <?= $itPct; ?>%"></div>
                                        <div class="bg-rose-500 h-full transition-all duration-300" style="width: <?= $clericalPct; ?>%"></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-xs text-slate-500 font-semibold italic">No verified entities are available for this report.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Entities Panel Card (Takes remaining vertical height) -->
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex-1 flex flex-col min-h-0">
                            
                            <div class="p-4 border-b border-slate-200/70 flex items-center justify-between bg-slate-50/60 shrink-0">
                                <div>
                                    <h2 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">Extracted Entities</h2>
                                    <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Click card to highlight term in document</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-300 font-black text-[10px]">
                                    <?= count($extractedEntities); ?>
                                </span>
                            </div>

                            <!-- Extracted Entity List Scroll Area -->
                            <div class="p-4 space-y-2.5 overflow-y-auto flex-1 thin-scrollbar">
                                <?php if (empty($extractedEntities)): ?>
                                    <div class="py-12 text-center text-slate-400 space-y-1">
                                        <div class="text-2xl">🔍</div>
                                        <p class="font-bold text-slate-700 text-xs">No extraction yet</p>
                                        <p class="text-[11px] text-slate-500 max-w-xs mx-auto">
                                            The spaCy pipeline did not return any verified predefined entities for this report.
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($extractedEntities as $entity):
                                        $entityName = trim((string) ($entity['entity_name'] ?? ''));
                                        $category = trim((string) ($entity['category'] ?? 'Other')) ?: 'Other';
                                        $activityType = trim((string) ($entity['activity_type'] ?? ''));
                                        $classification = trim((string) ($entity['classification_label'] ?? ''));
                                        if ($classification === '') {
                                            $classification = strtolower($activityType) === 'clerical' ? 'Clerical' : 'Technical';
                                        }
                                        $itRelated = strtolower(trim((string) ($entity['it_related'] ?? 'unknown')));
                                        $itLabel = trim((string) ($entity['it_related_label'] ?? ''));
                                        if ($itLabel === '') {
                                            $itLabel = $itRelated === 'yes' ? 'IT Related' : ($itRelated === 'no' ? 'Non-IT' : 'Unknown');
                                        }
                                        $isClerical = strcasecmp($classification, 'Clerical') === 0;
                                        $isItRelated = strcasecmp($itLabel, 'IT Related') === 0;
                                    ?>
                                        <div
                                            class="entity-card p-3.5 bg-slate-50 hover:bg-amber-50/50 rounded-xl border border-slate-300 flex items-center justify-between gap-2"
                                            role="button"
                                            tabindex="0"
                                            aria-pressed="false"
                                            data-entity-term="<?= htmlspecialchars($entityName, ENT_QUOTES, 'UTF-8'); ?>"
                                            title="Click to highlight this entity in the PDF"
                                        >
                                            <div class="min-w-0 space-y-1.5">
                                                <p class="font-extrabold text-slate-950 text-xs truncate">
                                                    <?= htmlspecialchars($entityName, ENT_QUOTES, 'UTF-8'); ?>
                                                </p>

                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <span class="px-2 py-0.5 bg-white text-slate-800 rounded-md text-[10px] font-bold border border-slate-200 shadow-2xs">
                                                        <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>

                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border shadow-2xs <?= $isClerical ? 'bg-violet-50 text-violet-800 border-violet-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200'; ?>">
                                                        <?= htmlspecialchars($classification, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>

                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border shadow-2xs <?= $isItRelated ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-slate-100 text-slate-700 border-slate-300'; ?>">
                                                        <?= htmlspecialchars($itLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>

                </div>

            </main>
        </div>
    </div>

    <!-- PDF.js Interactive Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        (() => {
            const viewer = document.getElementById('pdf-viewer');
            if (!viewer || !window.pdfjsLib) return;

            const cards = [...document.querySelectorAll('[data-entity-term]')];
            const activeTerms = new Set();
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const normalize = value => String(value || '')
                .toLowerCase()
                .replace(/[\u2010-\u2015]/g, '-')
                .replace(/\s+/g, ' ')
                .trim();

            const escapeRegExp = value => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

            const matchesEntityText = (sourceText, term) => {
                const source = normalize(sourceText);
                const target = normalize(term);
                if (!source || !target) return false;
                return new RegExp(`(^|\\s)${escapeRegExp(target)}(?=\\s|$)`, 'iu').test(source);
            };

            const setCardState = (term, active) => {
                cards.filter(card => normalize(card.dataset.entityTerm) === term)
                    .forEach(card => {
                        card.classList.toggle('entity-selected', active);
                        card.setAttribute('aria-pressed', active ? 'true' : 'false');
                    });
            };

            const applyHighlights = () => {
                document.querySelectorAll('.pdf-text-layer span').forEach(span => {
                    const active = [...activeTerms].some(term =>
                        matchesEntityText(span.textContent, term)
                    );
                    span.classList.toggle('entity-highlight', active);
                });
            };

            const toggleEntity = card => {
                const term = normalize(card.dataset.entityTerm);
                if (!term) return;
                const active = !activeTerms.has(term);
                active ? activeTerms.add(term) : activeTerms.delete(term);
                setCardState(term, active);
                applyHighlights();
            };

            cards.forEach(card => {
                card.addEventListener('click', () => toggleEntity(card));
                card.addEventListener('keydown', event => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        toggleEntity(card);
                    }
                });
            });

            async function renderPdf() {
                try {
                    const pdf = await pdfjsLib.getDocument(viewer.dataset.pdfUrl).promise;
                    viewer.replaceChildren();

                    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                        const page = await pdf.getPage(pageNumber);
                        const baseViewport = page.getViewport({ scale: 1 });
                        const availableWidth = Math.max(viewer.clientWidth - 40, 320);
                        const scale = Math.min(1.85, availableWidth / baseViewport.width);
                        const viewport = page.getViewport({ scale });

                        const pageContainer = document.createElement('div');
                        pageContainer.className = 'pdf-page';
                        pageContainer.style.width = `${viewport.width}px`;
                        pageContainer.style.height = `${viewport.height}px`;

                        const canvas = document.createElement('canvas');
                        canvas.width = Math.ceil(viewport.width);
                        canvas.height = Math.ceil(viewport.height);
                        pageContainer.appendChild(canvas);
                        viewer.appendChild(pageContainer);

                        await page.render({
                            canvasContext: canvas.getContext('2d'),
                            viewport
                        }).promise;

                        const textLayer = document.createElement('div');
                        textLayer.className = 'pdf-text-layer';
                        pageContainer.appendChild(textLayer);
                        const textContent = await page.getTextContent();

                        textContent.items.forEach(item => {
                            if (!item.str) return;
                            const span = document.createElement('span');
                            span.textContent = item.str;
                            const tx = pdfjsLib.Util.transform(viewport.transform, item.transform);
                            const fontHeight = Math.max(Math.hypot(tx[2], tx[3]), 1);
                            span.style.left = `${tx[4]}px`;
                            span.style.top = `${tx[5] - fontHeight}px`;
                            span.style.fontSize = `${fontHeight}px`;
                            textLayer.appendChild(span);
                        });
                    }

                    applyHighlights();
                } catch (error) {
                    viewer.innerHTML = '<div class="flex h-full items-center justify-center p-4 text-center text-xs font-bold text-rose-600">Unable to render this PDF. Use “Open Full PDF” to view it.</div>';
                    console.error('PDF viewer error:', error);
                }
            }

            renderPdf();
        })();
    </script>
</body>
</html>