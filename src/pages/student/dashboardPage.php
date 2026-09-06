<!-- src/pages/student/dashboardPage.php -->
<?php
$isEvaluated = !empty($student['evaluation_id']);
$isRequested = !empty($student['completion_requested']) && !$isEvaluated;

/*
 * The controller may provide $recentReports with one of these entity keys:
 *   entities, extracted_entities, or report_entities.
 * Each entity may contain entity_name/name, activity_type, it_related,
 * confidence_score/confidence, and priority.
 */
$reviewReports = array_values(array_filter($recentReports ?? [], static function ($report) {
    return !empty($report['file_path']);
}));

$selectedReviewReport = $reviewReports[0] ?? null;
$selectedReportEntities = [];

if ($selectedReviewReport) {
    $selectedReportEntities = $selectedReviewReport['entities']
        ?? $selectedReviewReport['extracted_entities']
        ?? $selectedReviewReport['report_entities']
        ?? [];

    if (is_string($selectedReportEntities)) {
        $decodedEntities = json_decode($selectedReportEntities, true);
        $selectedReportEntities = is_array($decodedEntities) ? $decodedEntities : [];
    }
}

$studentDashboardJson = static function ($value): string {
    return htmlspecialchars(
        json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ENT_QUOTES,
        'UTF-8'
    );
};

$reportViewerData = [];
foreach ($reviewReports as $index => $report) {
    $entities = $report['entities']
        ?? $report['extracted_entities']
        ?? $report['report_entities']
        ?? [];

    if (is_string($entities)) {
        $decodedEntities = json_decode($entities, true);
        $entities = is_array($decodedEntities) ? $decodedEntities : [];
    }

    $reportViewerData[] = [
        'id' => (string)($report['id'] ?? $report['report_id'] ?? $index),
        'week' => (string)($report['week_number'] ?? ($index + 1)),
        'title' => 'Week ' . (string)($report['week_number'] ?? ($index + 1)) . ' Accomplishment Report',
        'fileUrl' => '/ICS-PORTAL/' . ltrim((string)$report['file_path'], '/'),
        'entities' => is_array($entities) ? array_values($entities) : [],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - OJT Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .pdf-stage { background: #e8edf4; }
        .pdf-page { position: relative; margin: 0 auto 1rem; width: fit-content; max-width: 100%; box-shadow: 0 8px 24px rgba(15, 40, 84, .12); }
        .pdf-page canvas { display: block; max-width: 100%; height: auto; }
        .pdf-text-layer { position: absolute; inset: 0; overflow: hidden; line-height: 1; opacity: .95; }
        .pdf-text-layer span { position: absolute; color: transparent; white-space: pre; cursor: text; transform-origin: 0 0; }
        .pdf-text-layer span.entity-highlight { color: transparent; background: rgba(250, 204, 21, .68); border-radius: 3px; box-shadow: 0 0 0 1px rgba(180, 83, 9, .28); }
        .entity-card { transition: border-color .15s ease, background-color .15s ease, transform .15s ease; }
        .entity-card.active { border-color: #f59e0b; background: #fffbeb; }
        .entity-card:hover { transform: translateY(-1px); }
        .thin-scrollbar::-webkit-scrollbar { width: 7px; height: 7px; }
        .thin-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">
<div class="flex min-h-screen">
    <?php include __DIR__ . '/../../components/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">
        <?php include __DIR__ . '/../../components/header.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1">
            <div class="bg-[#0F2854] rounded-2xl p-6 sm:p-7 text-white shadow-xs space-y-5">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="space-y-1">
                        <?php if ($isEvaluated): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">OJT Completed</span>
                            <h1 class="text-base md:text-lg font-bold leading-tight">Internship Finished &amp; Submitted</h1>
                            <p class="text-xs text-blue-100/80 mt-1">Your supervisor has signed and forwarded your final evaluation to the OJT Coordinator.</p>
                        <?php elseif ($isRequested): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-200 border border-amber-400/30">Awaiting Evaluation</span>
                            <h1 class="text-base md:text-lg font-bold leading-tight">Completion Request Submitted</h1>
                            <p class="text-xs text-blue-100/80 mt-1">Waiting for your supervisor to evaluate and submit your performance report to the coordinator.</p>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-white/10 text-blue-100 border border-white/15">Weekly Task</span>
                            <h1 class="text-base md:text-lg font-bold leading-tight">Week <?= htmlspecialchars($nextWeek ?? '1'); ?> Accomplishment Report Due</h1>
                            <p class="text-xs text-blue-100/80 mt-1">Submit your weekly tasks and activities for supervisor review and verification.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (!$isEvaluated && !$isRequested): ?>
                        <a href="submit_report.php?week=<?= htmlspecialchars($nextWeek ?? '1'); ?>" class="shrink-0 px-5 py-3 bg-white hover:bg-slate-100 text-[#0F2854] font-bold rounded-xl text-xs transition-all shadow-xs inline-flex items-center gap-2">
                            Submit Week <?= htmlspecialchars($nextWeek ?? '1'); ?> Report
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7-7 7m0 0H3"/></svg>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="pt-5 border-t border-white/10 space-y-2">
                    <div class="flex justify-between items-center text-xs font-semibold text-slate-200">
                        <span>Overall Progress</span>
                        <span class="font-bold text-white"><?= intval($totalApproved ?? 0); ?> of <?= htmlspecialchars($targetWeeks ?? '12'); ?> Weeks Approved (<?= htmlspecialchars($progressPercentage ?? '0'); ?>%)</span>
                    </div>
                    <div class="w-full h-2.5 bg-black/25 rounded-full overflow-hidden border border-white/10">
                        <div class="bg-emerald-400 h-full rounded-full transition-all duration-500" style="width: <?= min(100, intval($progressPercentage ?? 0)); ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Recent report activity -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center gap-4">
                    <div>
                        <h2 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Recent Report Activity</h2>
                        <p class="text-xs font-medium text-slate-500 mt-1">Latest recorded weekly submissions and review updates.</p>
                    </div>
                    <a href="reports.php" class="text-xs font-bold text-[#0F2854] hover:underline inline-flex items-center gap-1">View All Reports <span aria-hidden="true">›</span></a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php if (empty($recentReports)): ?>
                        <div class="py-12 text-center text-slate-500 text-xs italic">No reports submitted yet. Click the submission button above to begin.</div>
                    <?php else: ?>
                        <?php foreach ($recentReports as $r):
                            $status = strtolower($r['status'] ?? 'pending');
                            $filePath = $r['file_path'] ?? '';
                        ?>
                            <div class="p-5 px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#0F2854] font-bold text-xs flex items-center justify-center shrink-0">W<?= htmlspecialchars($r['week_number'] ?? '—'); ?></div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900">Week <?= htmlspecialchars($r['week_number'] ?? '—'); ?> Accomplishment Report</h4>
                                        <p class="text-xs font-medium text-slate-500 mt-0.5">Submitted: <?= !empty($r['submitted_at']) ? date("M d, Y \\a\\t g:i A", strtotime($r['submitted_at'])) : '—'; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 self-end sm:self-center">
                                    <?php if ($status === 'approved'): ?>
                                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">Approved</span>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold">Needs Changes</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">Waiting for Review</span>
                                    <?php endif; ?>
                                    <?php if (!empty($filePath)): ?>
                                        <a href="review_report.php?report_id=<?= urlencode((string)($r['id'] ?? $r['report_id'] ?? '')); ?>"
   class="px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-[#0F2854] bg-slate-100 hover:bg-slate-200/80 rounded-xl border border-slate-200/70 transition-all">
    Review PDF
</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>


</body>
</html> 