<!-- src/pages/supervisor/reviewReportsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');

$filter_status = $filter_status ?? 'All';
$message = $message ?? '';
$reports = $reports ?? [];
$activeReport = $activeReport ?? null;

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$hasActiveReport = !empty($activeReport) && is_array($activeReport);
$activeStatus = strtolower($activeReport['status'] ?? 'pending');
$isApproved = ($activeStatus === 'approved');
$isRevision = ($activeStatus === 'rejected');

$activeFilePath = $activeReport['file_path'] ?? '';
$activeSubmittedAt = $activeReport['submitted_at'] ?? null;
$activeApprovedAt = $activeReport['approved_at'] ?? null;
$extractedEntities = $activeReport['extracted_entities'] ?? [];

/*
 * Direct PDF URL Resolver (Matches Intern Portal)
 */
function buildSupervisorPdfUrl(string $filePath): string
{
    $path = trim(str_replace('\\', '/', $filePath));
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $fileName = basename($path);
    $projectRoot = realpath(__DIR__ . '/../../..');
    if ($projectRoot && file_exists($projectRoot . '/uploads/reports/' . $fileName)) {
        return '/ICS-PORTAL/uploads/reports/' . $fileName;
    }
    if (preg_match('#^/?ICS-PORTAL/#i', $path)) {
        return '/' . ltrim($path, '/');
    }
    if (str_starts_with($path, '/')) {
        return $path;
    }
    return '/ICS-PORTAL/' . ltrim($path, '/');
}

$pdfUrl = buildSupervisorPdfUrl($activeFilePath);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Reports - Supervisor Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <script src="/ICS-PORTAL/public/js/loadingOverlay.js"></script>
    <style>
        #pdf-viewer { position: relative; width: 100%; min-width: 0; overflow-y: auto; overflow-x: hidden; background: #f1f5f9; }
        .pdf-page { position: relative; max-width: 100%; margin: 0 auto 14px; background: #fff; box-shadow: 0 1px 4px rgba(15, 23, 42, .12); border-radius: 6px; }
        .pdf-page canvas { display: block; max-width: 100%; height: auto; }
        .pdf-text-layer { position: absolute; inset: 0; overflow: hidden; line-height: 1; user-select: text; }
        .pdf-text-layer span { position: absolute; color: transparent; white-space: pre; transform-origin: 0 0; cursor: text; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

<div class="flex min-h-screen">
    
    <!-- Sidebar Component -->
    <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Header Component -->
        <?php include __DIR__ . '/../../components/header.php'; ?>

        <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

            <!-- Page Header Card -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/90 shadow-xs">
                <div>
                    <h1 class="text-base font-extrabold text-slate-950 leading-snug tracking-tight">Review Accomplishment Reports</h1>
                    <p class="text-xs font-semibold text-slate-600 mt-1">Review student task logs, inspect extracted technical activities, and approve reports.</p>
                </div>

                <!-- Status Filter Tabs -->
                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-xl border border-slate-300 text-xs font-bold">
                    <a href="review_reports.php?status=All" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'All' ? 'bg-white text-slate-950 shadow-2xs font-black' : 'text-slate-700 hover:text-slate-950'; ?>">All</a>
                    <a href="review_reports.php?status=Pending" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'Pending' ? 'bg-white text-amber-700 shadow-2xs font-black' : 'text-slate-700 hover:text-slate-950'; ?>">Pending</a>
                    <a href="review_reports.php?status=Approved" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'Approved' ? 'bg-white text-emerald-700 shadow-2xs font-black' : 'text-slate-700 hover:text-slate-950'; ?>">Approved</a>
                    <a href="review_reports.php?status=Needs+Revision" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'Needs Revision' ? 'bg-white text-rose-700 shadow-2xs font-black' : 'text-slate-700 hover:text-slate-950'; ?>">Needs Changes</a>
                </div>
            </div>

            <!-- Flash Alert Message -->
            <?php if (!empty($message)): ?>
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl text-xs font-bold shadow-2xs flex items-center gap-2">
                    <span class="font-black text-sm">✓</span>
                    <span><?= e($message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Submissions Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-200/70 flex justify-between items-center bg-slate-50/60">
                    <div>
                        <h3 class="text-xs font-black text-slate-900 tracking-wider uppercase">Submissions Queue</h3>
                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Showing student accomplishment logs matching your filter</p>
                    </div>
                    <span class="text-xs font-black text-slate-900 bg-white px-3.5 py-1 rounded-lg border border-slate-300 shadow-2xs">
                        <?= count($reports); ?> Total
                    </span>
                </div>

                <?php if (!empty($reports)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                    <th class="py-4 px-6">Student</th>
                                    <th class="py-4 px-6">Report #</th>
                                    <th class="py-4 px-6">Date Submitted</th>
                                    <th class="py-4 px-6">Status</th>
                                    <th class="py-4 px-6 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                <?php foreach ($reports as $item): 
                                    $status = strtolower($item['status'] ?? 'pending');
                                    $isPending = ($status === 'pending');
                                    $isItemApproved = ($status === 'approved');
                                    $itemApprovedAt = $item['approved_at'] ?? null;
                                ?>
                                    <tr class="hover:bg-slate-50 transition-colors <?= $isPending ? 'bg-amber-50/20' : ''; ?>">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-300">
                                                    <?php if (!empty($item['student_avatar'])): ?>
                                                        <img src="<?= e($item['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar" onerror="this.onerror=null; this.parentElement.innerHTML='<?= e(strtoupper(substr($item['student_name'] ?? 'S', 0, 1))); ?>';">
                                                    <?php else: ?>
                                                        <?= e(strtoupper(substr($item['student_name'] ?? 'S', 0, 1))); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="font-extrabold text-slate-950 text-sm tracking-tight"><?= e($item['student_name']); ?></p>
                                                    <p class="text-[11px] text-slate-500 font-semibold">ID: <?= e($item['student_number'] ?? 'N/A'); ?> &bull; <?= e($item['program'] ?? 'BSIT'); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-100 text-slate-900 font-extrabold text-xs border border-slate-300">
                                                Week <?= e($item['week_number']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-slate-700 font-semibold whitespace-nowrap">
                                            <?= !empty($item['submitted_at']) ? e(date("M d, Y \a\\t g:i A", strtotime($item['submitted_at']))) : '—'; ?>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <?php if ($isItemApproved): ?>
                                                <div class="flex flex-col items-start gap-1">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        Approved
                                                    </span>
                                                    <?php if (!empty($itemApprovedAt)): ?>
                                                        <span class="text-[11px] font-semibold text-slate-500 pl-0.5">
                                                            <?= e(date("M d, Y \a\\t g:i A", strtotime($itemApprovedAt))); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php elseif ($status === 'pending'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                    Waiting for Review
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100/80 text-rose-900 border border-rose-300 text-xs font-bold shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                    Needs Changes
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <a href="view_report.php?student_id=<?= (int)($item['student_id'] ?? 0); ?>&report_id=<?= (int)$item['id']; ?>" 
                                               data-ics-loading="Extracting entities from this report&hellip; This may take a moment."
                                               class="px-4 py-2 <?= $isPending ? 'bg-[#0F2854] text-white hover:bg-blue-900 shadow-xs' : 'bg-slate-100 text-slate-800 hover:bg-slate-200 border border-slate-300'; ?> text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                <span><?= $isPending ? 'Review' : 'View Details'; ?></span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 px-4 space-y-2">
                        <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                            📋
                        </div>
                        <h4 class="text-sm font-black text-slate-900">No reports found</h4>
                        <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">There are no submissions matching your current filter.</p>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<!-- ============================================================
     <!-- PDF.js Viewer Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    'use strict';

    const viewer = document.getElementById('pdf-viewer');
    if (!viewer) return;

    const pdfUrl = viewer.getAttribute('data-pdf-url');
    if (!pdfUrl) return;

    const loadingHTML =
        '<div class="h-full flex items-center justify-center text-xs text-slate-500 font-semibold p-4">Loading Document…</div>';

    function showError(message) {
        viewer.innerHTML =
            '<div class="h-full flex flex-col items-center justify-center text-center p-6">' +
                '<div class="text-rose-600 text-sm font-black mb-2">Unable to display PDF</div>' +
                '<div class="text-xs text-slate-600 max-w-md font-medium">' + escapeHtml(message) + '</div>' +
                '<a href="' + escapeHtml(pdfUrl) + '" target="_blank" rel="noopener noreferrer" ' +
                   'class="mt-3 text-xs font-bold text-[#0F2854] hover:underline">Open PDF directly ↗</a>' +
            '</div>';
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[char];
        });
    }

    async function renderPDF() {
        viewer.innerHTML = loadingHTML;

        if (!window.pdfjsLib) {
            showError('PDF.js failed to load. Check your internet connection or the PDF.js library.');
            return;
        }

        window.pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        try {
            const safeUrl = encodeURI(pdfUrl);

            const loadingTask = window.pdfjsLib.getDocument({
                url: safeUrl,
                withCredentials: true,
                disableAutoFetch: false,
                disableStream: false
            });

            const pdf = await loadingTask.promise;

            if (!pdf || !pdf.numPages) {
                throw new Error('The PDF contains no pages.');
            }

            viewer.innerHTML = '';

            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                const page = await pdf.getPage(pageNumber);

                const baseViewport = page.getViewport({ scale: 1 });
                const availableWidth = Math.max(viewer.clientWidth - 16, 280);

                const scale = Math.max(
                    Math.min(availableWidth / baseViewport.width, 2),
                    0.75
                );

                const viewport = page.getViewport({ scale: scale });

                const pageContainer = document.createElement('div');
                pageContainer.className = 'pdf-page';
                pageContainer.style.width = viewport.width + 'px';
                pageContainer.style.height = viewport.height + 'px';

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d', { alpha: false });

                const outputScale = window.devicePixelRatio || 1;

                canvas.width = Math.ceil(viewport.width * outputScale);
                canvas.height = Math.ceil(viewport.height * outputScale);
                canvas.style.width = viewport.width + 'px';
                canvas.style.height = viewport.height + 'px';

                pageContainer.appendChild(canvas);

                const textLayer = document.createElement('div');
                textLayer.className = 'pdf-text-layer';
                textLayer.style.width = viewport.width + 'px';
                textLayer.style.height = viewport.height + 'px';
                pageContainer.appendChild(textLayer);

                viewer.appendChild(pageContainer);

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport,
                    transform: outputScale !== 1
                        ? [outputScale, 0, 0, outputScale, 0, 0]
                        : null
                };

                await page.render(renderContext).promise;

                const textContent = await page.getTextContent();

                textContent.items.forEach(function (item) {
                    if (!item.str) return;

                    const tx = window.pdfjsLib.Util.transform(
                        viewport.transform,
                        item.transform
                    );

                    const fontHeight = Math.max(
                        Math.sqrt((tx[2] * tx[2]) + (tx[3] * tx[3])),
                        6
                    );

                    const span = document.createElement('span');
                    span.textContent = item.str;
                    span.style.left = tx[4] + 'px';
                    span.style.top = (tx[5] - fontHeight) + 'px';
                    span.style.fontSize = fontHeight + 'px';
                    span.style.fontFamily = 'sans-serif';
                    span.style.lineHeight = '1';

                    const scaleX = Math.sqrt(
                        (tx[0] * tx[0]) + (tx[1] * tx[1])
                    ) || 1;

                    span.style.transform = 'scaleX(' + scaleX + ')';
                    textLayer.appendChild(span);
                });
            }

            window.__reviewPdfLoaded = true;

        } catch (error) {
            console.error('PDF viewer error:', error);
            showError(
                error && error.message
                    ? error.message
                    : 'The PDF could not be loaded.'
            );
        }
    }

    requestAnimationFrame(function () {
        renderPDF();
    });

    window.addEventListener('resize', function () {
        if (!window.__reviewPdfLoaded) return;

        const currentWidth = viewer.clientWidth;
        if (
            window.__reviewPdfLastWidth &&
            Math.abs(currentWidth - window.__reviewPdfLastWidth) < 40
        ) {
            return;
        }

        window.__reviewPdfLastWidth = currentWidth;
        renderPDF();
    });
})();
</script>
</body>
</html>