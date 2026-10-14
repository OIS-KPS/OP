<!-- src/pages/coordinator/dashboardPage.php -->
<?php
// View-model preparation. The coordinator controller supplies the live arrays;
// these fallbacks keep the view compatible with older controller versions.
$entityAnalysisRows = is_array($entityFrequencyAnalysis ?? null) ? $entityFrequencyAnalysis : [];
if (!$entityAnalysisRows && is_array($entitiesData ?? null)) {
    $frequencyRows = [];
    foreach ($entitiesData as $entity) {
        $entityName = trim((string) ($entity['entity'] ?? ''));
        if ($entityName === '') {
            continue;
        }
        $category = trim((string) ($entity['category'] ?? 'Other')) ?: 'Other';
        $classification = trim((string) ($entity['classification'] ?? 'Technical')) ?: 'Technical';
        $key = strtolower($entityName) . '|' . strtolower($category) . '|' . strtolower($classification);
        if (!isset($frequencyRows[$key])) {
            $frequencyRows[$key] = [
                'entity' => $entityName,
                'category' => $category,
                'classification' => $classification,
                'frequency' => 0,
                'company_count' => 0,
                '_companies' => []
            ];
        }
        $frequencyRows[$key]['frequency'] += max(0, (int) ($entity['frequency'] ?? 0));
        $frequencyRows[$key]['_companies'][(string) ($entity['company'] ?? 'Unassigned')] = true;
    }
    foreach ($frequencyRows as $row) {
        $row['company_count'] = count($row['_companies']);
        unset($row['_companies']);
        $entityAnalysisRows[] = $row;
    }
}
usort($entityAnalysisRows, static function (array $left, array $right): int {
    return ((int) ($right['frequency'] ?? 0)) <=> ((int) ($left['frequency'] ?? 0));
});

$totalEntityOccurrencesView = 0;
$technicalEntityOccurrencesView = 0;
$clericalEntityOccurrencesView = 0;
foreach ($entityAnalysisRows as $row) {
    $frequency = max(0, (int) ($row['frequency'] ?? 0));
    $totalEntityOccurrencesView += $frequency;
    if (strcasecmp((string) ($row['classification'] ?? ''), 'Clerical') === 0) {
        $clericalEntityOccurrencesView += $frequency;
    } else {
        $technicalEntityOccurrencesView += $frequency;
    }
}
$technicalActivityPct = $totalEntityOccurrencesView > 0
    ? round(($technicalEntityOccurrencesView / $totalEntityOccurrencesView) * 100, 1)
    : 0.0;
$clericalActivityPct = $totalEntityOccurrencesView > 0
    ? round(($clericalEntityOccurrencesView / $totalEntityOccurrencesView) * 100, 1)
    : 0.0;

$dashboardCompanies = is_array($companyPerformance ?? null) ? $companyPerformance : [];
$lowCompaniesView = array_values(array_filter($dashboardCompanies, static function (array $company): bool {
    return (float) ($company['percentage'] ?? 0) > 0 && (float) ($company['percentage'] ?? 0) < 60;
}));
$noEvaluationCompaniesView = array_values(array_filter($dashboardCompanies, static function (array $company): bool {
    return (int) ($company['verified_evaluations'] ?? 0) === 0;
}));
$evaluationCoverageView = (float) ($evaluationCoveragePct ?? 0);
if (!isset($evaluationCoveragePct)) {
    $evaluationCoverageView = (int) ($totalStudents ?? 0) > 0
        ? (((int) ($evaluatedStudents ?? 0) / (int) $totalStudents) * 100)
        : 0.0;
}
$evaluationCoverageView = round($evaluationCoverageView, 1);
$totalReportsView = (int) ($totalReports ?? 0);
$reportsWithEntitiesView = (int) ($totalReportsWithEntities ?? 0);
$confidenceView = (float) ($spacyConfidence ?? 0);
// The "top category" figures were assigned here but never rendered, and with
// category reduced to a mirror of classification they only restated the
// IT/Clerical split shown above them.
$highestItCompanyView = is_array($highestItCompany ?? null) ? $highestItCompany : null;
$lowestItCompanyView = is_array($lowestItCompany ?? null) ? $lowestItCompany : null;
$entityPriorityRowsView = is_array($entityPrioritySummary ?? null) ? $entityPrioritySummary : [];
$entityPriorityGroupsView = ['High' => [], 'Medium' => [], 'Low' => []];
foreach ($entityPriorityRowsView as $priorityRow) {
    $priorityName = (string) ($priorityRow['priority'] ?? 'Low');
    if (!isset($entityPriorityGroupsView[$priorityName])) {
        $priorityName = 'Low';
    }
    if (count($entityPriorityGroupsView[$priorityName]) < 3) {
        $entityPriorityGroupsView[$priorityName][] = $priorityRow;
    }
}

$cqiSummaryView = is_array($cqiSummary ?? null) ? $cqiSummary : [];
$cqiStrengthsView = is_array($cqiSummaryView['strengths'] ?? null) ? $cqiSummaryView['strengths'] : [];
$cqiGapsView = is_array($cqiSummaryView['gaps'] ?? null) ? $cqiSummaryView['gaps'] : [];
$cqiOverallRecommendationView = trim((string) ($cqiOverallRecommendation ?? ''));
// The most frequent recorded term. This replaces the old "entity priorities" bullet,
// which grouped terms by priority level and buried them in one run-on sentence.
// Naming a single term is the same finding the CQI export now leads with.
$cqiTopTermView = trim((string) ($cqiSummaryView['evidence']['top_term'] ?? ''));
$cqiTopTermOccurrencesView = max(0, (int) ($cqiSummaryView['evidence']['top_term_occurrences'] ?? 0));
$recommendationPriorityView = (string) ($recommendationPriority ?? 'Continuous');
$recommendationPriorityClassView = $recommendationPriorityView === 'High'
    ? 'bg-rose-50 text-rose-700 border-rose-200'
    : ($recommendationPriorityView === 'Medium' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200');

if (!$cqiStrengthsView) {
    if ($evaluationCoverageView >= 90 && (int) ($totalStudents ?? 0) > 0) {
        $cqiStrengthsView[] = sprintf('Evaluation coverage is %.1f%%, meeting the 90%% monitoring threshold.', $evaluationCoverageView);
    }
    if ($confidenceView >= 90) {
        $cqiStrengthsView[] = sprintf('Average verified extraction confidence is %.2f%%.', $confidenceView);
    }
    if ($reportsWithEntitiesView > 0) {
        $cqiStrengthsView[] = sprintf('%d report(s) contain persisted extracted-entity evidence.', $reportsWithEntitiesView);
    }
}

if (!$cqiGapsView) {
    if ($evaluationCoverageView < 100 && (int) ($totalStudents ?? 0) > 0) {
        $cqiGapsView[] = sprintf('Evaluation coverage is %.1f%%, below the 100%% completion target.', $evaluationCoverageView);
    }
    if ($totalReportsView > $reportsWithEntitiesView) {
        $cqiGapsView[] = sprintf('%d submitted report(s) do not yet have persisted entity records.', $totalReportsView - $reportsWithEntitiesView);
    }
    if ($totalEntityOccurrencesView > 0 && $confidenceView < 90) {
        $cqiGapsView[] = sprintf('Average extraction confidence is %.2f%%, below the 90%% review threshold.', $confidenceView);
    }
    foreach ($lowCompaniesView as $company) {
        $cqiGapsView[] = sprintf('%s has an average verified score of %.1f%%.', $company['name'], $company['percentage']);
    }
    if ($noEvaluationCompaniesView) {
        $names = array_map(static fn (array $company): string => (string) $company['name'], $noEvaluationCompaniesView);
        $cqiGapsView[] = 'No verified evaluation is recorded for: ' . implode(', ', $names) . '.';
    }
}

$cqiStatusView = (string) ($cqiSummaryView['status'] ?? 'Insufficient data');
$cqiStatusClassView = match ($cqiStatusView) {
    'On track' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    'Needs attention' => 'border-amber-200 bg-amber-50 text-amber-700',
    'Data error' => 'border-rose-200 bg-rose-50 text-rose-700',
    default => 'border-slate-200 bg-slate-100 text-slate-600'
};

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CQI Analytics Dashboard - OJT Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-6 max-w-7xl w-full mx-auto space-y-6 flex-1">

                <!-- Top Header Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">CQI Analytics Dashboard</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Task analysis and skill extraction from approved reports</p>
                    </div>

                    <div class="flex items-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50/80 text-[#0F2854] font-bold rounded-xl border border-blue-200/60 text-xs shadow-2xs">
                            <span class="text-blue-600">✦</span>
                            <span>spaCy Confidence: <?= $spacyConfidence; ?>%</span>
                        </span>
                    </div>
                </div>

                                <!-- The former top KPI card grid was removed. Its values remain available to the live charts and CQI calculations. -->


                <!-- 2. Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Bar Chart -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4 flex flex-col justify-between">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xs font-bold text-slate-900">Company IT Task Analysis</h2>
                                <p class="text-[11px] text-slate-400">Percentage of IT-related tasks per company • <span class="text-blue-600 font-semibold cursor-pointer">Click a bar to filter entities</span></p>
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">High ≥ 80%</span>
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Mod 60–79%</span>
                                <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 border border-rose-300">Low &lt; 60%</span>
                            </div>
                        </div>
                        <div class="h-60 relative">
                            <canvas id="companyTaskBarChart"></canvas>
                        </div>
                    </div>

                    <!-- Donut Chart -->
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4 flex flex-col justify-between">
                        <div>
                            <h2 class="text-xs font-bold text-slate-900">Activity Distribution</h2>
                            <p class="text-[11px] text-slate-400">Technical vs. clerical tasks</p>
                        </div>
                        <div class="relative flex items-center justify-center p-2 h-44">
                            <canvas id="activityDonutChart"></canvas>
                        </div>
                        <div class="space-y-1.5 text-xs border-t border-slate-100 pt-3">
                            <div class="flex items-center justify-between font-semibold text-slate-700 text-[11px]">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#0F2854]"></span> Technical / IT-Related</span>
                                <span class="font-bold text-slate-700"><?= number_format($technicalActivityPct, 1); ?>%</span>
                            </div>
                            <div class="flex items-center justify-between font-semibold text-slate-700 text-[11px]">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Clerical / Non-IT</span>
                                <span class="font-bold text-slate-700"><?= number_format($clericalActivityPct, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 3. Entity Frequency Horizontal Bar Chart Section -->
                <div class="w-full min-w-0 max-w-full min-h-[360px] max-h-[70vh] overflow-y-auto overflow-x-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="sticky top-0 z-20 px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs bg-white">
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 tracking-tight">Entity Frequency Analysis</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Frequency breakdown grouped by category</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Classification Color Legends -->
                            <div class="flex items-center gap-3 text-[11px] font-semibold mr-1">
                                <span class="flex items-center gap-1.5 text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#0F2854]"></span> Technical
                                </span>
                                <span class="flex items-center gap-1.5 text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Clerical
                                </span>
                            </div>

                            <!-- Clean Dropdown & Date Filters -->
                            <div class="flex flex-wrap items-center gap-2">
                                <select id="companyFilter" onchange="renderEntityChart()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium focus:outline-none focus:border-[#0F2854]">
                                    <option value="all">All Companies</option>
                                    <?php foreach ($companyPerformance as $cp): ?>
                                        <option value="<?= htmlspecialchars(strtolower($cp['name'])); ?>"><?= htmlspecialchars($cp['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select id="typeFilter" onchange="renderEntityChart()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium focus:outline-none focus:border-[#0F2854]">
                                    <option value="all">All Types</option>
                                    <option value="technical">Technical</option>
                                    <option value="clerical">Clerical</option>
                                </select>

                                <?php /* No category filter. category now mirrors it_related, so this
                                         dropdown offered the same two choices as the Type filter
                                         directly beside it, under extractor jargon (IT_TERM). */ ?>

                                <div class="flex items-center gap-1">
                                    <input type="date" id="dateFilter" onchange="renderEntityChart()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] text-slate-700 font-medium">
                                    <button type="button" onclick="clearDateFilter()" title="Clear date filter" class="text-slate-400 hover:text-slate-600 p-1.5 text-xs font-bold">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Unified Chart Container -->
                    <div id="entityChartWrapper" class="w-full min-w-0 overflow-hidden p-6">
                        <div id="chartContainer" class="relative w-full min-w-0 overflow-hidden" style="min-height: 280px;">
                            <canvas id="entityFrequencyChart"></canvas>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="entityEmptyState" class="hidden p-12 text-center">
                        <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center mx-auto mb-2 text-base font-bold">🔍</div>
                        <h3 class="text-xs font-bold text-slate-800">No matching entities found</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Try adjusting your filters or date range.</p>
                    </div>
                </div>

                    <!-- 4. Detailed Evidence-Based CQI Summary & Action Plan -->
                    <section class="cqi-summary-card w-full max-w-none min-w-0 overflow-hidden rounded-2xl border border-slate-200/80 border-l-4 border-l-[#0F2854] bg-white p-4 shadow-xs" aria-labelledby="cqi-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h2 id="cqi-heading" class="flex items-center gap-2 text-[15px] font-bold uppercase tracking-wide text-slate-900">
                                <span aria-hidden="true">&#9635;</span> CQI Summary &amp; Action Plan
                            </h2>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="w-fit rounded-full border px-3 py-1 text-[11px] font-bold <?= htmlspecialchars($cqiStatusClassView, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($cqiStatusView, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <a href="export_cqi.php" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0F2854] px-3.5 py-2 text-xs font-bold text-white transition-colors hover:bg-blue-900">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>
                                    Export CV
                                </a>
                            </div>
                        </div>
                        <div class="mt-2.5 flex flex-wrap items-center justify-between gap-2">
                            <p class="text-xs text-slate-500"><?= htmlspecialchars((string) ($cqiSummaryView['period'] ?? 'Current academic records'), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <?php /* The four headline figures.

                                 No section heading and no tile borders: each figure carries
                                 its own label, so a heading above them repeated what the
                                 labels already said. */ ?>
                        <div class="mt-4 grid min-w-0 grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-4">
                            <div class="min-w-0">
                                <p class="text-xs text-slate-500">Completed evaluations</p>
                                <p class="mt-0.5 text-[15px] font-bold leading-tight text-slate-900"><?= (int) ($evaluatedStudents ?? 0); ?> / <?= (int) ($totalStudents ?? 0); ?></p>
                                <p class="text-[11px] text-slate-400"><?= number_format($evaluationCoverageView, 1); ?>% covered</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-slate-500">Reports with keywords</p>
                                <p class="mt-0.5 text-[15px] font-bold leading-tight text-slate-900"><?= (int) $reportsWithEntitiesView; ?> / <?= (int) $totalReportsView; ?></p>
                                <p class="text-[11px] text-slate-400"><?= $totalReportsView > 0 ? number_format(($reportsWithEntitiesView / $totalReportsView) * 100, 1) : '0.0'; ?>% covered</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-slate-500">IT tasks vs clerical</p>
                                <p class="mt-0.5 text-[15px] font-bold leading-tight text-slate-900"><?= number_format($technicalActivityPct, 1); ?>% / <?= number_format($clericalActivityPct, 1); ?>%</p>
                                <p class="text-[11px] text-slate-400">of <?= (int) $totalEntityOccurrencesView; ?> keywords</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-slate-500">Keyword match rate</p>
                                <p class="mt-0.5 text-[15px] font-bold leading-tight text-slate-900"><?= number_format($confidenceView, 1); ?>%</p>
                                <p class="text-[11px] text-slate-400">how often a term matched</p>
                            </div>
                        </div>

                        <?php /* Company comparison. Two plain lines -- the labels describe
                                 themselves, so no heading and no accent rules. */ ?>
                        <div class="mt-5 grid min-w-0 grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2">
                            <?php if ($highestItCompanyView): ?>
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500">Most IT tasks</p>
                                    <p class="mt-0.5 text-[13px] font-medium leading-6 break-words text-slate-800"><?= htmlspecialchars((string) $highestItCompanyView['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-[11px] text-slate-400"><span class="font-semibold text-[#0F2854]"><?= number_format((float) $highestItCompanyView['it_task_ratio'], 1); ?>%</span> &middot; <?= (int) $highestItCompanyView['it_related_occurrences']; ?> of <?= (int) $highestItCompanyView['entity_occurrences']; ?> keyword matches</p>
                                </div>
                            <?php else: ?>
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500">Most IT tasks</p>
                                    <p class="mt-0.5 text-[13px] leading-6 text-slate-400">Not enough keyword evidence yet.</p>
                                </div>
                            <?php endif; ?>

                            <?php if ($lowestItCompanyView): ?>
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500">Fewest IT tasks</p>
                                    <p class="mt-0.5 text-[13px] font-medium leading-6 break-words text-slate-800"><?= htmlspecialchars((string) $lowestItCompanyView['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-[11px] text-slate-400"><span class="font-semibold text-slate-700"><?= number_format((float) $lowestItCompanyView['it_task_ratio'], 1); ?>%</span> &middot; <?= (int) $lowestItCompanyView['it_related_occurrences']; ?> of <?= (int) $lowestItCompanyView['entity_occurrences']; ?> keyword matches</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php /* Terms to review.

                                 This was three rows: a chip row, a row of filter buttons,
                                 and a panel underneath that the buttons rewrote. All three
                                 listed the same three priority groups.

                                 There was nothing to filter here -- the full list is thirteen
                                 terms and fits on screen -- so the filter was decoration
                                 with a click attached. The three groups are now three plain
                                 lines, listed in full. */ ?>
                        <?php
                        $priorityGroupNamesView = [];
                        foreach ($entityPriorityRowsView as $priorityRowView) {
                            $priorityNameView = (string) ($priorityRowView['priority'] ?? 'Low');
                            if (!isset($priorityGroupNamesView[$priorityNameView])) {
                                $priorityGroupNamesView[$priorityNameView] = [];
                            }
                            $priorityGroupNamesView[$priorityNameView][] = (string) ($priorityRowView['entity'] ?? '');
                        }
                        ?>
                        <div class="mt-5">
                            <p class="text-xs font-semibold text-slate-700">Terms to review</p>
                            <?php if ($cqiTopTermView !== '' && $cqiTopTermOccurrencesView > 0): ?>
                                <p class="mt-0.5 text-[11px] text-slate-400">Most frequent: <?= htmlspecialchars($cqiTopTermView, ENT_QUOTES, 'UTF-8'); ?> (<?= (int) $cqiTopTermOccurrencesView; ?> <?= $cqiTopTermOccurrencesView === 1 ? 'time' : 'times'; ?>)</p>
                            <?php endif; ?>
                            <div class="mt-2 space-y-1">
                                <?php foreach (['High', 'Medium', 'Low'] as $priorityLevelView): ?>
                                    <?php $priorityNamesView = $priorityGroupNamesView[$priorityLevelView] ?? []; ?>
                                    <?php if (!$priorityNamesView) { continue; } ?>
                                    <p class="flex gap-2 text-[13px] leading-6">
                                        <span class="w-16 shrink-0 text-slate-500"><?= $priorityLevelView; ?></span>
                                        <span class="min-w-0 text-slate-700"><?= htmlspecialchars(implode(', ', $priorityNamesView), ENT_QUOTES, 'UTF-8'); ?></span>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php /* Gaps and the action plan.

                                 "What this means" used to sit here and print $cqiSummaryView
                                 ['narrative'] -- roughly sixty words in a single grey
                                 paragraph. It is gone from the dashboard because every figure
                                 in it is already shown in the four metrics and the company
                                 rows directly above: the student count, the evaluation count,
                                 the report count, the technical/clerical split, and both
                                 company ratios.

                                 Nothing is lost. src/services/cqi_dashboard_data.php still
                                 builds the narrative, and coordinator/export_cqi.php still
                                 prints it under "Academic Interpretation", so the full
                                 wording remains available in the PDF for anyone who needs it. */ ?>
                        <div class="mt-5 space-y-4 border-t border-slate-200 pt-4">
                            <?php if ($cqiGapsView): ?>
                                <div>
                                    <p class="text-xs font-semibold text-slate-700">Needs attention</p>
                                    <ul class="mt-1 space-y-0.5">
                                        <?php foreach ($cqiGapsView as $gapView): ?>
                                            <li class="text-[13px] leading-6 text-slate-600"><?= htmlspecialchars((string) $gapView, ENT_QUOTES, 'UTF-8'); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div>
                                <p class="text-xs font-semibold text-slate-700">What to do</p>
                                <p class="mt-1 text-[13px] leading-6 text-slate-700"><?= htmlspecialchars($cqiOverallRecommendationView !== '' ? $cqiOverallRecommendationView : 'Use the next academic CQI cycle to verify evaluation completion, improve report extraction coverage, compare company IT-task exposure, and review entity classifications against the program learning outcomes.', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </section>

            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Bar Chart (IT Task Performance by Company)
        const companies = <?= json_encode($companyPerformance); ?>;
        const ctxBar = document.getElementById('companyTaskBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: companies.map(c => c.name),
                datasets: [{
                    label: 'IT Task %',
                    data: companies.map(c => c.it_task_ratio === null || c.it_task_ratio === undefined ? 0 : Number(c.it_task_ratio)),
                    backgroundColor: companies.map(c => c.it_task_ratio === null || c.it_task_ratio === undefined ? '#CBD5E1' : (c.it_task_ratio >= 80 ? '#059669' : (c.it_task_ratio >= 60 ? '#D97706' : '#991B1B'))),
                    hoverBackgroundColor: companies.map(c => c.it_task_ratio === null || c.it_task_ratio === undefined ? '#94A3B8' : (c.it_task_ratio >= 80 ? '#047857' : (c.it_task_ratio >= 60 ? '#B45309' : '#7F1D1D'))),
                    borderRadius: 6,
                    barThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onHover: (event, chartElement) => {
                    const target = event.native ? event.native.target : (event.chart ? event.chart.canvas : null);
                    if (target) {
                        target.style.cursor = chartElement && chartElement.length > 0 ? 'pointer' : 'default';
                    }
                },
                onClick: (event, elements) => {
                    if (elements && elements.length > 0) {
                        const clickedIndex = elements[0].index;
                        const clickedCompany = companies[clickedIndex];
                        const companySelect = document.getElementById('companyFilter');
                        if (!companySelect || !clickedCompany) return;

                        const targetValue = clickedCompany.name.toLowerCase();
                        // Toggle filter: if already selected, reset to 'all'; otherwise select clicked company
                        if (companySelect.value === targetValue) {
                            companySelect.value = 'all';
                        } else {
                            companySelect.value = targetValue;
                        }

                        // Re-render the Entity Frequency Chart with the new company filter
                        renderEntityChart();

                        // Smoothly scroll down to the Entity Frequency chart
                        const entityWrapper = document.getElementById('entityChartWrapper');
                        if (entityWrapper) {
                            entityWrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        callbacks: {
                            label: function(context) {
                                const company = companies[context.dataIndex];
                                if (!company || company.it_task_ratio === null || company.it_task_ratio === undefined) {
                                    return ' IT-related task ratio: No entity data';
                                }
                                return [
                                    ` IT-related task ratio: ${Number(company.it_task_ratio).toFixed(1)}%`,
                                    ` Level: ${company.it_task_level || 'Unclassified'}`,
                                    ` Evidence: ${company.it_related_occurrences || 0} of ${company.entity_occurrences || 0} occurrences`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#334155' }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: v => v + '%',
                            font: { family: 'Inter', size: 11 },
                            color: '#334155'
                        },
                        grid: { color: '#F1F5F9' }
                    }
                }
            }
        });

        // Donut Chart
        const ctxDonut = document.getElementById('activityDonutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Technical', 'Clerical'],
                datasets: [{
                    data: [
                        Number(<?= json_encode($technicalActivityPct); ?>),
                        Number(<?= json_encode($clericalActivityPct); ?>)
                    ],
                    backgroundColor: ['#0F2854', '#94A3B8'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });

        // 3. Entity Frequency Horizontal Bar Chart (Unified Single Chart with Category Background & Label Indicators)
        const rawEntitiesData = <?= json_encode($entitiesData); ?>;
        let entityChartInstance = null;

        function clearDateFilter() {
            document.getElementById('dateFilter').value = '';
            renderEntityChart();
        }

        // Custom Chart.js plugin to draw background bands and category indicator labels for each category group
        function computeEntityGroups(items) {
            const groups = [];
            let current = null;

            items.forEach((item, index) => {
                // Keyed on classification alone. category used to be part of the key,
                // which let a stale value put a clerical term inside a band
                // labelled IT_TERM. it_related is the source of truth, so the
                // chart reads from it and never from the mirrored column.
                const key = item.classification;

                if (!current || current.key !== key) {
                    if (current) groups.push(current);
                    current = {
                        key: key,
                        classification: item.classification,
                        category: item.category,
                        startIndex: index,
                        endIndex: index,
                        totalFreq: item.frequency
                    };
                } else {
                    current.endIndex = index;
                    current.totalFreq += item.frequency;
                }
            });
            if (current) groups.push(current);

            return groups;
        }

        const categoryGroupIndicatorPlugin = {
            id: 'categoryGroupIndicator',
            beforeDraw(chart) {
                const { ctx, chartArea, scales: { y } } = chart;
                const items = chart.config._entityItems;
                if (!items || items.length === 0 || !y || !chartArea) return;

                const { top, bottom } = chartArea;
                const groups = computeEntityGroups(items);

                ctx.save();
                const step = items.length > 1 ? (bottom - top) / items.length : bottom - top;
                const halfStep = step / 2;

                groups.forEach((group, gIdx) => {
                    const topY = y.getPixelForValue(group.startIndex) - halfStep + 2;
                    const bottomY = y.getPixelForValue(group.endIndex) + halfStep - 2;
                    const groupHeight = Math.max(bottomY - topY, step);

                    // 1. Draw subtle alternating category background strip BEFORE any text or axes are rendered
                    const isEven = gIdx % 2 === 0;
                    ctx.fillStyle = isEven ? '#F8FAFC' : '#F1F5F9';
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(4, topY, chartArea.right - 8, groupHeight, 8);
                    } else {
                        ctx.rect(4, topY, chartArea.right - 8, groupHeight);
                    }
                    ctx.fill();

                    // 2. Draw left vertical accent line for the category group
                    ctx.fillStyle = '#0F2854';
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(4, topY + 4, 3, groupHeight - 8, 2);
                    } else {
                        ctx.rect(4, topY + 4, 3, groupHeight - 8);
                    }
                    ctx.fill();

                    // 3. Full-width rule where the classification block changes.
                    //    Technical and Clerical terms are grouped separately, so
                    //    the boundary is marked rather than left to be inferred.
                    const next = groups[gIdx + 1];
                    if (next && next.classification !== group.classification) {
                        const ruleY = y.getPixelForValue(group.endIndex) + halfStep;
                        ctx.strokeStyle = '#CBD5E1';
                        ctx.lineWidth = 1;
                        ctx.setLineDash([4, 3]);
                        ctx.beginPath();
                        ctx.moveTo(4, ruleY);
                        ctx.lineTo(chartArea.right - 4, ruleY);
                        ctx.stroke();
                        ctx.setLineDash([]);
                    }
                });
                ctx.restore();
            },
            afterDraw(chart) {
                const { ctx, chartArea, scales: { y } } = chart;
                const items = chart.config._entityItems;
                if (!items || items.length === 0 || !y || !chartArea) return;

                const { top, bottom } = chartArea;
                const groups = computeEntityGroups(items);

                ctx.save();
                const step = items.length > 1 ? (bottom - top) / items.length : bottom - top;
                const halfStep = step / 2;

                groups.forEach((group) => {
                    const topY = y.getPixelForValue(group.startIndex) - halfStep + 2;

                    // Band badge, anchored to the plot area's right edge. It used
                    // to hang off chart.width, which drifts it over the bars.
                    const badgeText = `${group.classification} · ${group.totalFreq}x`;
                    ctx.font = '600 10px Inter, sans-serif';
                    const textMetrics = ctx.measureText(badgeText);
                    const badgeWidth = textMetrics.width + 16;
                    const badgeHeight = 20;
                    const badgeX = chartArea.right - badgeWidth - 8;
                    const badgeY = topY + 6;

                    // Pill background
                    ctx.fillStyle = '#FFFFFF';
                    ctx.strokeStyle = '#CBD5E1';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(badgeX, badgeY, badgeWidth, badgeHeight, 6);
                    } else {
                        ctx.rect(badgeX, badgeY, badgeWidth, badgeHeight);
                    }
                    ctx.fill();
                    ctx.stroke();

                    // Pill text
                    ctx.fillStyle = '#334155';
                    ctx.fillText(badgeText, badgeX + 8, badgeY + 14);
                });
                ctx.restore();
            }
        };

        function renderEntityChart() {
            if (entityChartInstance) {
                entityChartInstance.destroy();
                entityChartInstance = null;
            }

            const company = document.getElementById('companyFilter').value;
            const type = document.getElementById('typeFilter').value;
            const date = document.getElementById('dateFilter').value;

            // Filter raw data
            let filtered = rawEntitiesData.filter(item => {
                const itemComp = (item.company || '').toLowerCase();
                const matchComp = (company === 'all' || itemComp === company.toLowerCase());
                const matchType = (type === 'all' || item.classification.toLowerCase() === type.toLowerCase());
                const matchDate = (!date || item.date === date);
                return matchComp && matchType && matchDate;
            });

            // Collapse the same term seen across several reports into one bar, so the
            // length of the bar is the term's TOTAL occurrences rather than one
            // arbitrary row per report. Keyed on the term and its
            // classification only -- category is a mirror of classification now,
            // so including it would split a term into duplicate bars.
            const merged = new Map();
            filtered.forEach(item => {
                const key = (item.entity || '').toLowerCase()
                    + '|' + (item.classification || '').toLowerCase();

                if (!merged.has(key)) {
                    merged.set(key, {
                        entity: item.entity,
                        category: item.category,
                        classification: item.classification,
                        frequency: 0,
                        companies: new Set(),
                        dates: []
                    });
                }

                const row = merged.get(key);
                row.frequency += item.frequency;
                if (item.company) row.companies.add(item.company);
                if (item.date && !row.dates.includes(item.date)) {
                    row.dates.push(item.date);
                }
            });
            filtered = Array.from(merged.values());

            // Technical first, then Clerical. Plain alphabetical order would put
            // "Clerical" above "Technical", which buries the IT block that
            // carries the headline metric.
            const classificationRank = item =>
                String(item.classification || '').toLowerCase() === 'clerical' ? 1 : 0;

            // Classification first so every IT term sits in one block and every
            // clerical term in the other, then category inside each block, then
            // frequency descending. Grouping by category alone used to leave a
            // Clerical term stranded between Technical ones inside an "IT_TERM"
            // band.
            filtered.sort((a, b) =>
                (classificationRank(a) - classificationRank(b))
                || a.category.localeCompare(b.category)
                || b.frequency - a.frequency
            );

            const wrapper = document.getElementById('entityChartWrapper');
            const emptyState = document.getElementById('entityEmptyState');
            const container = document.getElementById('chartContainer');

            if (filtered.length === 0) {
                wrapper.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            wrapper.classList.remove('hidden');
            emptyState.classList.add('hidden');

            // Dynamically size container height based on total horizontal bars
            const calculatedHeight = Math.max(filtered.length * 56 + 40, 240);
            container.style.height = `${calculatedHeight}px`;

            const canvas = document.getElementById('entityFrequencyChart');
            const ctx = canvas.getContext('2d');

            const maxVal = Math.max(...filtered.map(i => i.frequency), 10);
            const suggestedMax = Math.ceil(maxVal * 1.15);

            entityChartInstance = new Chart(ctx, {
                type: 'bar',
                plugins: [categoryGroupIndicatorPlugin],
                data: {
                    labels: filtered.map(item => item.entity),
                    datasets: [{
                        label: 'Total occurrences',
                        data: filtered.map(item => item.frequency),
                        backgroundColor: filtered.map(item => item.classification.toLowerCase() === 'technical' ? '#0F2854' : '#94A3B8'),
                        hoverBackgroundColor: filtered.map(item => item.classification.toLowerCase() === 'technical' ? '#0F2854' : '#94A3B8'),
                        borderRadius: 6,
                        barThickness: 22
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { right: 20, left: 10 }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Inter', size: 11 },
                            callbacks: {
                                title: function(context) {
                                    const item = filtered[context[0].dataIndex];
                                    return item.entity;
                                },
                                label: function(context) {
                                    const item = filtered[context.dataIndex];
                                    const companies = item.companies.size
                                        ? Array.from(item.companies).join(', ')
                                        : 'N/A';
                                    return [
                                        ` Total occurrences: ${item.frequency}x`,
                                        ` Companies: ${item.companies.size} (${companies})`,
                                        ` Reports: ${item.dates.length}`,
                                        ` Type: ${item.classification}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            suggestedMax: suggestedMax,
                            grid: { color: '#F1F5F9' },
                            ticks: {
                                font: { family: 'Inter', size: 10 },
                                color: '#334155',
                                stepSize: 10,
                                callback: v => v + 'x'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Inter', size: 11, weight: '400' },
                                color: '#334155',
                                autoSkip: false
                            }
                        }
                    }
                }
            });

            // Store items on chart config for plugin access
            entityChartInstance.config._entityItems = filtered;
            entityChartInstance.update();
        }

        // The priority filter that used to live here was removed along with its
        // buttons. The card now prints every term, grouped by priority, in the
        // markup above -- there is nothing left for a click handler to reveal.

        // Initial render on page load
        renderEntityChart();
    </script>

</body>
</html>