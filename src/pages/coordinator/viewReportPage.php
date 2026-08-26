<!-- src/pages/coordinator/viewReportPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Inspection - Week <?= htmlspecialchars($report['week_number']); ?> - OJT Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        #pdf-viewer {
            width: 100%;
            min-width: 0;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            background: #e2e8f0;
        }
        .pdf-page {
            position: relative;
            margin: 0 auto 14px;
            background: #fff;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .16);
        }
        .pdf-page canvas {
            display: block;
            max-width: 100%;
            height: auto;
        }
        .pdf-text-layer {
            position: absolute;
            inset: 0;
            overflow: hidden;
            user-select: text;
        }
        .pdf-text-layer span {
            position: absolute;
            color: transparent;
            white-space: pre;
            transform-origin: 0 0;
        }
        .pdf-text-layer span.entity-highlight {
            color: #713f12;
            background: #fde68a;
            border-radius: 2px;
            box-shadow: 0 0 0 1px rgba(245, 158, 11, .35);
        }
        .entity-card {
            cursor: pointer;
            transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
        }
        .entity-card:hover,
        .entity-card.entity-selected {
            border-color: #f59e0b;
            background: #fffbeb;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, .12);
        }
    </style>
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
                                <div
                                    id="pdf-viewer"
                                    class="w-full h-full"
                                    data-pdf-url="/ICS-PORTAL/<?= htmlspecialchars(ltrim($report['file_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="Report PDF"
                                >
                                    <div class="flex h-full items-center justify-center p-4 text-xs text-slate-400">Loading PDF…</div>
                                </div>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs p-6 text-center">
                                    <span class="text-3xl mb-2">📁</span>
                                    <p class="font-bold text-slate-600">No PDF Attached</p>
                                    <p class="text-[11px]">No document file path exists for this submission.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Extracted Entities & Task Percentages (5 of 12 columns) -->
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
                                <p class="text-xs text-slate-400 italic">No verified entities are available for this report.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Entities Panel Card -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
                            
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                                <div>
                                    <h2 class="font-bold text-xs text-slate-900">Extracted Entities</h2>
                                    <p class="text-[11px] text-slate-400">Verified entities from the predefined catalog</p>
                                </div>


                            </div>

                            <!-- Extracted Entity List -->
                            <div class="p-4 space-y-2.5 max-h-[380px] overflow-y-auto">
                                <?php if (empty($extractedEntities)): ?>
                                    <div class="py-10 text-center text-slate-400">
                                        <div class="text-2xl mb-1">🔍</div>
                                        <p class="font-semibold text-slate-600 text-xs">No extraction yet</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 max-w-xs mx-auto">
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
                                            class="entity-card p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-center justify-between gap-2"
                                            role="button"
                                            tabindex="0"
                                            aria-pressed="false"
                                            data-entity-term="<?= htmlspecialchars($entityName, ENT_QUOTES, 'UTF-8'); ?>"
                                            title="Click to highlight this entity in the PDF"
                                        >
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 text-xs truncate">
                                                    <?= htmlspecialchars($entityName, ENT_QUOTES, 'UTF-8'); ?>
                                                </p>

                                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                                    <span class="px-2 py-0.5 bg-slate-200/70 text-slate-700 rounded text-[10px] font-semibold">
                                                        <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>

                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $isClerical ? 'bg-violet-50 text-violet-700 border-violet-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'; ?>">
                                                        <?= htmlspecialchars($classification, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>

                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $isItRelated ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200'; ?>">
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

    <!-- Click an extracted entity card to toggle its PDF highlight. -->
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
                        const availableWidth = Math.max(viewer.clientWidth - 16, 240);
                        const scale = Math.min(1.25, availableWidth / baseViewport.width);
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
                    viewer.innerHTML = '<div class="flex h-full items-center justify-center p-4 text-center text-xs text-rose-500">Unable to render this PDF. Use “Open Full PDF” to view it.</div>';
                    console.error('PDF viewer error:', error);
                }
            }

            renderPdf();
        })();
    </script>



</body>
</html>