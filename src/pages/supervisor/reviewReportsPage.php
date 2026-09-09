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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        #pdf-viewer { position: relative; width: 100%; min-width: 0; overflow-y: auto; overflow-x: hidden; background: #f1f5f9; }
        .pdf-page { position: relative; max-width: 100%; margin: 0 auto 14px; background: #fff; box-shadow: 0 1px 4px rgba(15, 23, 42, .12); border-radius: 6px; }
        .pdf-page canvas { display: block; max-width: 100%; height: auto; }
        .pdf-text-layer { position: absolute; inset: 0; overflow: hidden; line-height: 1; user-select: text; }
        .pdf-text-layer span { position: absolute; color: transparent; white-space: pre; transform-origin: 0 0; cursor: text; }
        .pdf-text-layer span.entity-highlight { color: #713f12; background: #fde68a; border-radius: 2px; box-shadow: 0 0 0 1px rgba(245, 158, 11, .35); }
        .entity-card { cursor: pointer; transition: all .15s ease; }
        .entity-card:hover, .entity-card.entity-selected { border-color: #f59e0b; background: #fffbeb; box-shadow: 0 0 0 2px rgba(245, 158, 11, .15); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex min-h-screen">
    
    <!-- Sidebar Component -->
    <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Header Component -->
        <?php include __DIR__ . '/../../components/header.php'; ?>

        <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

            <!-- Page Header Card -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/80 shadow-xs">
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-snug">Review Accomplishment Reports</h1>
                    <p class="text-xs font-medium text-slate-500 mt-1">Review student task logs, inspect extracted technical activities, and approve reports.</p>
                </div>

                <!-- Status Filter Tabs -->
                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-xl border border-slate-200/70 text-xs font-semibold">
                    <a href="review_reports.php?status=All" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'All' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'; ?>">All</a>
                    <a href="review_reports.php?status=Pending" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'Pending' ? 'bg-white text-amber-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'; ?>">Pending</a>
                    <a href="review_reports.php?status=Approved" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'Approved' ? 'bg-white text-emerald-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'; ?>">Approved</a>
                    <a href="review_reports.php?status=Needs+Revision" class="px-3.5 py-1.5 rounded-lg transition-all <?= $filter_status === 'Needs Revision' ? 'bg-white text-rose-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'; ?>">Needs Changes</a>
                </div>
            </div>

            <!-- Flash Alert Message -->
            <?php if (!empty($message)): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold shadow-2xs flex items-center gap-2">
                    <span class="font-bold text-sm">✓</span>
                    <span><?= e($message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Submissions Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/40">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Submissions Queue</h3>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5">Showing student accomplishment logs matching your filter</p>
                    </div>
                    <span class="text-xs font-bold text-slate-700 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-2xs">
                        <?= count($reports); ?> Total
                    </span>
                </div>

                <?php if (!empty($reports)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/70 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                    <th class="py-4 px-6">Student</th>
                                    <th class="py-4 px-6">Report #</th>
                                    <th class="py-4 px-6">Date Submitted</th>
                                    <th class="py-4 px-6">Status</th>
                                    <th class="py-4 px-6 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php foreach ($reports as $item): 
                                    $status = strtolower($item['status'] ?? 'pending');
                                    $isPending = ($status === 'pending');
                                    $isItemApproved = ($status === 'approved');
                                    $itemApprovedAt = $item['approved_at'] ?? null;
                                ?>
                                    <tr class="hover:bg-slate-50/70 transition-colors <?= $isPending ? 'bg-amber-50/15' : ''; ?>">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-200/80">
                                                    <?php if (!empty($item['student_avatar'])): ?>
                                                        <img src="<?= e($item['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                    <?php else: ?>
                                                        <?= e(strtoupper(substr($item['student_name'] ?? 'S', 0, 1))); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900 text-sm"><?= e($item['student_name']); ?></p>
                                                    <p class="text-[11px] text-slate-400 font-medium">ID: <?= e($item['student_number'] ?? 'N/A'); ?> &bull; <?= e($item['program'] ?? 'BSIT'); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                                                Week <?= e($item['week_number']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-slate-600 font-medium whitespace-nowrap">
                                            <?= !empty($item['submitted_at']) ? e(date("M d, Y \a\\t g:i A", strtotime($item['submitted_at']))) : '—'; ?>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <?php if ($isItemApproved): ?>
                                                <div class="flex flex-col items-start gap-1">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Approved
                                                    </span>
                                                    <?php if (!empty($itemApprovedAt)): ?>
                                                        <span class="text-[11px] font-medium text-slate-500 pl-0.5">
                                                            <?= e(date("M d, Y \a\\t g:i A", strtotime($itemApprovedAt))); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php elseif ($status === 'pending'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Waiting for Review
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Needs Changes
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <a href="review_reports.php?review_id=<?= (int)$item['id']; ?>&status=<?= e($filter_status); ?>" 
                                               class="px-4 py-2 <?= $isPending ? 'bg-[#0F2854] text-white hover:bg-blue-900 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200'; ?> text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1.5">
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
                        <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center mx-auto text-lg font-bold border border-slate-200">
                            📋
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">No reports found</h4>
                        <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto">There are no submissions matching your current filter.</p>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<!-- ============================================================
     REVIEW REPORT MODAL
     ============================================================ -->
<?php if ($hasActiveReport): ?>
<div id="reviewModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 sm:p-6 overflow-y-auto">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-6xl w-full overflow-hidden relative my-auto flex flex-col p-6 sm:p-7 space-y-5 max-h-[92vh]">
        
        <!-- Modal Header -->
        <div class="border-b border-slate-100 pb-4 flex justify-between items-center pr-8 shrink-0">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-base font-bold text-slate-900">
                        <?= e($activeReport['student_name'] ?? 'Student'); ?> &bull; Week <?= e($activeReport['week_number'] ?? 'N/A'); ?> Report
                    </h2>
                    <?php if ($isApproved): ?>
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold rounded-full">Approved</span>
                    <?php elseif ($isRevision): ?>
                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold rounded-full">Needs Changes</span>
                    <?php else: ?>
                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold rounded-full">Pending</span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Submitted on <?= !empty($activeSubmittedAt) ? e(date("F d, Y \a\\t g:i A", strtotime($activeSubmittedAt))) : '—'; ?>
                    <?php if ($isApproved && !empty($activeApprovedAt)): ?>
                        &bull; <span class="text-emerald-700 font-semibold">Approved on <?= e(date("F d, Y \a\\t g:i A", strtotime($activeApprovedAt))); ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <a href="review_reports.php?status=<?= e($filter_status); ?>" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm font-bold transition-all" aria-label="Close">
                ✕
            </a>
        </div>

        <!-- 2-Column Review Body -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 min-h-0 flex-1">
            
            <!-- Left: PDF Viewer -->
            <div class="lg:col-span-3 bg-slate-100/70 rounded-2xl border border-slate-200 p-3 flex flex-col h-[60vh] min-h-[420px]">
                <div class="flex items-center justify-between mb-2 px-1 shrink-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Report Document</span>
                    <?php if (!empty($pdfUrl)): ?>
                        <a href="<?= e($pdfUrl); ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-[#0F2854] hover:underline">
                            Open Fullscreen ↗
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($pdfUrl)): ?>
                    <div class="flex-1 min-h-0 bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                        <div id="pdf-viewer" class="w-full h-full" data-pdf-url="<?= e($pdfUrl); ?>">
                            <div class="h-full flex items-center justify-center text-xs text-slate-400 p-4">Loading Document…</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex-1 min-h-0 bg-white rounded-xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-center p-6">
                        <p class="text-xs font-bold text-slate-700">No PDF uploaded</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">There is no document attached to this submission.</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($activeReport['student_id'])): ?>
                    <div class="pt-2.5 mt-2 border-t border-slate-200/60 text-center shrink-0">
                        <a href="interns.php?id=<?= (int)$activeReport['student_id']; ?>" class="text-xs font-bold text-[#0F2854] hover:underline">
                            View All Weekly Reports by <?= e($activeReport['student_name'] ?? 'Student'); ?> &rarr;
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Detected Skills & Direct Actions -->
            <div class="lg:col-span-2 flex flex-col h-[60vh] min-h-[420px] space-y-4">
                
                <!-- Skills/Entities Box -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col min-h-0 flex-1 shadow-2xs">
                    <div class="flex items-center justify-between mb-1 shrink-0">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Skills & Keywords Found</span>
                        <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                            <?= count($extractedEntities); ?> Found
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-3 shrink-0">Click any keyword to find and highlight it in the PDF.</p>

                    <?php if (!empty($extractedEntities)): ?>
                        <div class="space-y-2 overflow-y-auto pr-1 min-h-0 flex-1">
                            <?php foreach ($extractedEntities as $entity): 
                                $entityName = $entity['entity_name'] ?? '';
                                $category = $entity['category'] ?? 'General';
                                $isTechnical = (($entity['classification'] ?? $entity['activity_type'] ?? 'Technical') === 'Technical' || ($entity['activity_type'] ?? '') === 'Software' || ($entity['activity_type'] ?? '') === 'Hardware');
                                if (trim((string)$entityName) === '') continue;
                            ?>
                                <div class="entity-card w-full text-left bg-slate-50 hover:bg-amber-50/50 border border-slate-200/80 rounded-xl p-2.5" role="button" tabindex="0" data-entity-term="<?= e($entityName); ?>">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-bold text-slate-900"><?= e($entityName); ?></span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $isTechnical ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-200/60 text-slate-600'; ?>">
                                            <?= $isTechnical ? 'Technical' : 'Clerical'; ?>
                                        </span>
                                    </div>
                                    <span class="block text-[10px] font-semibold text-slate-400 mt-1">Category: <strong class="text-slate-600"><?= e($category); ?></strong></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="my-auto text-center py-6 text-slate-400">
                            <p class="text-xs font-semibold text-slate-600">No keywords detected</p>
                            <p class="text-[11px] mt-0.5">Could not auto-extract technical tasks from this file.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Controls Form -->
                <?php if ($isApproved): ?>
                    <div class="flex items-center justify-end shrink-0 pt-2">
                        <a href="review_reports.php?status=<?= e($filter_status); ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-all">
                            Close Review
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3 pt-3 border-t border-slate-100 shrink-0">
                        
                        <!-- Revision Instructions Box -->
                        <div id="revisionBox" class="hidden space-y-1.5">
                            <label class="block text-[11px] font-bold text-slate-700">Revision Instructions for Student:</label>
                            <textarea id="supervisorRemarksInput" rows="2" placeholder="Explain the corrections or missing items the student needs to address..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#0F2854]"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <a href="review_reports.php?status=<?= e($filter_status); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-all">
                                Cancel
                            </a>

                            <!-- Unified Action Form -->
                            <form method="POST" action="review_reports.php?status=<?= e($filter_status); ?>" class="flex items-center gap-2">
                                <input type="hidden" name="action_report_id" value="<?= (int)($activeReport['id'] ?? 0); ?>">
                                <input type="hidden" name="student_name" value="<?= e($activeReport['student_name'] ?? 'Student'); ?>">
                                <input type="hidden" name="supervisor_remarks" id="formRemarksField" value="">
                                <input type="hidden" name="status" id="formStatusField" value="">

                                <!-- Request Changes Button -->
                                <button type="button" onclick="handleRevisionClick(this.form)" id="btnRequestChanges" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 transition-all cursor-pointer">
                                    Request Changes
                                </button>

                                <!-- Direct Approve Button -->
                                <button type="button" onclick="handleApproveClick(this.form)" id="btnApprove" class="px-5 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                                    Approve Report
                                </button>
                            </form>
                        </div>
                    </div>

                    <script>
                    function handleRevisionClick(form) {
                        const box = document.getElementById('revisionBox');
                        const btn = document.getElementById('btnRequestChanges');
                        const btnApprove = document.getElementById('btnApprove');

                        if (box.classList.contains('hidden')) {
                            box.classList.remove('hidden');
                            document.getElementById('supervisorRemarksInput').focus();
                            btn.innerText = 'Send Change Request';
                            btn.className = 'px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer';
                            btnApprove.classList.add('hidden');
                        } else {
                            const remarks = document.getElementById('supervisorRemarksInput').value.trim();
                            if (!remarks) {
                                alert('Please provide instructions for the student regarding what needs to be changed.');
                                return;
                            }
                            document.getElementById('formRemarksField').value = remarks;
                            document.getElementById('formStatusField').value = 'rejected';
                            form.submit();
                        }
                    }

                    function handleApproveClick(form) {
                        document.getElementById('formStatusField').value = 'approved';
                        form.submit();
                    }
                    </script>
                <?php endif; ?>

            </div>

        </div>

    </div>
</div>
<?php endif; ?>

<!-- PDF.js & Entity Highlighter Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    'use strict';

    const viewer = document.getElementById('pdf-viewer');
    if (!viewer) return;

    const pdfUrl = viewer.getAttribute('data-pdf-url');
    if (!pdfUrl) return;

    const loadingHTML =
        '<div class="h-full flex items-center justify-center text-xs text-slate-400 p-4">Loading Document…</div>';

    function showError(message) {
        viewer.innerHTML =
            '<div class="h-full flex flex-col items-center justify-center text-center p-6">' +
                '<div class="text-rose-500 text-sm font-bold mb-2">Unable to display PDF</div>' +
                '<div class="text-xs text-slate-500 max-w-md">' + escapeHtml(message) + '</div>' +
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

    function normalizeText(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/[\u2010-\u2015]/g, '-')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function clearHighlights() {
        viewer.querySelectorAll('.pdf-text-layer span.entity-highlight').forEach(function (span) {
            span.classList.remove('entity-highlight');
        });
    }

    function highlightEntity(term) {
        clearHighlights();

        const wanted = normalizeText(term);
        if (!wanted) return;

        let found = false;

        viewer.querySelectorAll('.pdf-text-layer span').forEach(function (span) {
            const text = normalizeText(span.textContent);
            if (!text) return;

            if (text.includes(wanted) || wanted.includes(text) && text.length > 1) {
                span.classList.add('entity-highlight');
                found = true;
            }
        });

        const selectedCards = document.querySelectorAll('.entity-card');
        selectedCards.forEach(function(card) {
            card.classList.toggle(
                'entity-selected',
                normalizeText(card.getAttribute('data-entity-term')) === wanted
            );
        });

        if (found) {
            const first = viewer.querySelector('.pdf-text-layer span.entity-highlight');
            if (first) {
                first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
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

            document.querySelectorAll('.entity-card').forEach(function (card) {
                const handler = function () {
                    highlightEntity(card.getAttribute('data-entity-term') || '');
                };

                card.addEventListener('click', handler);
                card.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        handler();
                    }
                });
            });

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