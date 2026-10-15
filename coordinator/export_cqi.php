<?php
// coordinator/export_cqi.php
// Exports the CQI Analysis Summary as a PDF (mPDF).
//
// The printed document mirrors the CQI Analysis Summary card on the
// coordinator dashboard section for section: the same three metrics,
// the same company comparison, the same gaps and the same recommended
// action. Figures come from the same src/services/cqi_dashboard_data.php,
// so the two can never disagree.
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header('Location: ../auth/login.php');
    exit();
}

// Keep notices/deprecations from corrupting the generated file.
@ini_set('display_errors', '0');

require_once __DIR__ . '/../src/services/cqi_dashboard_data.php';

if (!function_exists('cqiE')) {
    function cqiE($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// ---------------------------------------------------------------
// Normalize the shared dashboard data into one report model.
// ---------------------------------------------------------------
$summary       = is_array($cqiSummary ?? null) ? $cqiSummary : [];
$status        = (string) ($summary['status'] ?? 'Insufficient data');
$period        = (string) ($summary['period'] ?? 'Current academic records in the database');
// narrative and strengths are not printed: neither appears on the dashboard
// card this document mirrors, and the narrative only restated figures that
// are already in the metric tiles below.
$gaps          = is_array($summary['gaps'] ?? null) ? array_values($summary['gaps']) : [];
$recommendation = trim((string) ($cqiOverallRecommendation ?? ''));

$totalStudents           = (int) ($totalStudents ?? 0);
$evaluatedStudents       = (int) ($evaluatedStudents ?? 0);
// totalReports / totalReportsWithEntities are no longer read: the "reports with
// keywords" metric was dropped so this document matches the dashboard card.
$totalEntityOccurrences  = (int) ($totalEntityOccurrences ?? 0);
$technicalEntityOccurrences = (int) ($technicalEntityOccurrences ?? 0);
$clericalEntityOccurrences  = (int) ($clericalEntityOccurrences ?? 0);
$confidence = (float) ($spacyConfidence ?? 0);
$coverage   = (float) ($evaluationCoveragePct ?? 0);

$techPct = $totalEntityOccurrences > 0 ? round(($technicalEntityOccurrences / $totalEntityOccurrences) * 100, 1) : 0.0;
$clerPct = $totalEntityOccurrences > 0 ? round(($clericalEntityOccurrences / $totalEntityOccurrences) * 100, 1) : 0.0;

$highestIt = is_array($highestItCompany ?? null) ? $highestItCompany : null;
$lowestIt  = is_array($lowestItCompany ?? null) ? $lowestItCompany : null;

$generatedAt  = date('F j, Y g:i A');
$filenameBase = 'CQI-Analysis-Summary-' . date('Y-m-d');

// ---------------------------------------------------------------
// Thresholds.
//
// These are the same bands the on-screen Company IT Task Analysis chart
// uses (High >= 80%, Moderate 60-79%, Low < 60%) and the same 90%
// monitoring threshold cqi_dashboard_data.php applies to evaluation
// coverage and extraction confidence. Re-declaring them here keeps the
// printed verdict consistent with the screen it is exported from.
// ---------------------------------------------------------------
const CQI_IT_HIGH_BAND   = 80.0;
const CQI_IT_MODERATE_BAND = 60.0;
const CQI_MONITOR_THRESHOLD = 90.0;

if (!function_exists('cqiBandLabel')) {
    function cqiBandLabel(float $ratio): string
    {
        if ($ratio >= CQI_IT_HIGH_BAND) {
            return 'High';
        }
        if ($ratio >= CQI_IT_MODERATE_BAND) {
            return 'Moderate';
        }
        return 'Low';
    }
}

/*
 * Each metric is printed with a one-line reading rather than a bare number:
 * what the figure is, and whether it clears the threshold the rest of the
 * CQI cycle is judged against. That is the difference between a figure
 * dump and an analysis.
 */
$metrics = [
    [
        'label'   => 'Completed evaluations',
        'value'   => $evaluatedStudents . ' / ' . $totalStudents,
        'reading' => $totalStudents === 0
            ? 'No interns on record, so coverage cannot be assessed.'
            : ($coverage >= 100.0
                ? sprintf('Full coverage. Every intern has a completed evaluation, against a %.0f%% completion target.', 100.0)
                : ($coverage >= CQI_MONITOR_THRESHOLD
                    ? sprintf('%.1f%% coverage clears the %.0f%% monitoring threshold but %d intern(s) remain unevaluated against the 100%% completion target.', $coverage, CQI_MONITOR_THRESHOLD, max(0, $totalStudents - $evaluatedStudents))
                    : sprintf('%.1f%% coverage is below the %.0f%% monitoring threshold; %d intern(s) still need a completed evaluation.', $coverage, CQI_MONITOR_THRESHOLD, max(0, $totalStudents - $evaluatedStudents)))),
    ],
    [
        'label'   => 'IT tasks vs clerical',
        'value'   => number_format($techPct, 1) . '% / ' . number_format($clerPct, 1) . '%',
        'reading' => $totalEntityOccurrences === 0
            ? 'No keyword occurrences recorded, so the IT exposure split cannot be computed.'
            : sprintf(
                '%d of %d keyword occurrences are IT-related. The batch sits in the "%s" band against the %.0f%% IT exposure reference.',
                $technicalEntityOccurrences,
                $totalEntityOccurrences,
                cqiBandLabel($techPct),
                CQI_IT_MODERATE_BAND
            ),
    ],
    [
        'label'   => 'Keyword match rate',
        'value'   => number_format($confidence, 2) . '%',
        'reading' => $confidence >= CQI_MONITOR_THRESHOLD
            ? sprintf('Extraction confidence clears the %.0f%% review threshold, so the distribution below can be read without manual re-checking.', CQI_MONITOR_THRESHOLD)
            : sprintf('Extraction confidence is below the %.0f%% review threshold; matches should be verified before the distribution is used for academic decisions.', CQI_MONITOR_THRESHOLD),
    ],
];

/*
 * Company comparison. Each company is placed against the cohort average and
 * the spread between the two extremes, so the pair reads as a distribution
 * rather than two unrelated numbers.
 */
$companiesForPdf = [];
if ($highestIt) {
    $companiesForPdf[] = ['label' => 'Most IT tasks', 'company' => $highestIt];
}
if ($lowestIt && (!$highestIt || strcasecmp((string) $highestIt['name'], (string) $lowestIt['name']) !== 0)) {
    $companiesForPdf[] = ['label' => 'Fewest IT tasks', 'company' => $lowestIt];
}

$itSpread = null;
if ($highestIt && $lowestIt && count($companiesForPdf) === 2) {
    $itSpread = round(
        (float) $highestIt['it_task_ratio'] - (float) $lowestIt['it_task_ratio'],
        1
    );
}

// Recommendation, split back into the parts it was assembled from so each
// action stands on its own line instead of one run-on sentence.
$recommendationText = $recommendation !== '' ? $recommendation : 'Use the next academic CQI cycle to verify evaluation completion, improve report extraction coverage, compare company IT-task exposure, and review entity classifications against the program learning outcomes.';
$recommendationBody = preg_replace('/^For the next academic CQI cycle,\s*the program should\s*/i', '', $recommendationText) ?: $recommendationText;
$actionBullets = [];

foreach (preg_split('/;\s*/', $recommendationBody) ?: [] as $actionPart) {
    $actionPart = trim(rtrim(trim($actionPart), '.'));
    if ($actionPart === '') {
        continue;
    }
    $actionBullets[] = strtoupper($actionPart[0]) . substr($actionPart, 1);
}

$recommendationPriority = (string) ($recommendationPriority ?? 'Continuous');

// ---------------------------------------------------------------
// PDF export (mPDF).
// ---------------------------------------------------------------
$statusColor = $status === 'On track' ? '#047857' : ($status === 'Needs attention' ? '#B45309' : ($status === 'Data error' ? '#BE123C' : '#475569'));

$html  = '<style>';
$html .= 'body { font-family: dejavusans; color: #0f172a; font-size: 10px; }';
$html .= 'h1 { color: #0F2854; font-size: 18px; margin: 0 0 2px 0; }';
$html .= '.muted { color: #64748b; font-size: 9px; }';
$html .= 'h2 { color: #0F2854; font-size: 12px; border-bottom: 1px solid #0F2854; padding-bottom: 3px; margin: 18px 0 7px 0; }';
$html .= '.badge { display: inline-block; padding: 3px 8px; border-radius: 8px; font-size: 9px; font-weight: bold; border: 1px solid ' . $statusColor . '; color: ' . $statusColor . '; }';
$html .= 'table { width: 100%; border-collapse: collapse; margin-top: 4px; }';
$html .= 'th { background: #0F2854; color: #ffffff; font-size: 8px; text-align: left; padding: 5px; }';
$html .= 'td { border: 1px solid #cbd5e1; font-size: 8px; padding: 5px; vertical-align: top; }';
// Metric tiles carry a reading line so the figure is interpreted, not just printed.
$html .= '.metric { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px; }';
$html .= '.metric .label { color: #475569; font-size: 8px; font-weight: bold; text-transform: uppercase; }';
$html .= '.metric .value { color: #0F2854; font-size: 13px; font-weight: bold; }';
$html .= '.metric .reading { color: #475569; font-size: 7.5pt; line-height: 1.45; margin-top: 3px; }';
$html .= 'ul { margin: 4px 0 0 14px; padding: 0; }';
$html .= 'li { margin-bottom: 5px; line-height: 1.55; }';
$html .= 'p.body { font-size: 9px; line-height: 1.65; margin: 0 0 6px 0; }';
$html .= '.rule { border-bottom: 2px solid #0F2854; margin: 2px 0 8px 0; }';
$html .= '.pill { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; border: 1px solid; }';
$html .= '.pill-High { background:#fff1f2; color:#be123c; border-color:#fda4af; }';
$html .= '.pill-Medium { background:#fffbeb; color:#b45309; border-color:#fcd34d; }';
$html .= '.pill-Low, .pill-Continuous { background:#f1f5f9; color:#475569; border-color:#cbd5e1; }';
// The company ratio is drawn as a bar so the two companies can be compared by
// length rather than by reading two percentages side by side.
$html .= '.bar { height: 5px; background: #e2e8f0; border-radius: 3px; margin: 3px 0 4px 0; }';
$html .= '.bar > span { display: block; height: 5px; border-radius: 3px; }';
$html .= '.bar-high > span { background: #059669; }';
$html .= '.bar-moderate > span { background: #D97706; }';
$html .= '.bar-low > span { background: #991B1B; }';
$html .= '.bar-avg > span { background: #64748b; }';
$html .= '</style>';

$html .= '<h1>CQI Analysis Summary</h1>';
$html .= '<div class="rule"></div>';
$html .= '<div class="muted">OJT Portal - Coordinator CQI Analytics</div>';
$html .= '<p><span class="badge">' . cqiE($status) . '</span> &nbsp; <strong>Period:</strong> ' . cqiE($period) . '<br><span class="muted">Generated: ' . cqiE($generatedAt) . '</span></p>';

$html .= '<table><tr>';
foreach ($metrics as $metric) {
    $html .= '<td class="metric">'
        . '<div class="label">' . cqiE($metric['label']) . '</div>'
        . '<div class="value">' . cqiE($metric['value']) . '</div>'
        . '<div class="reading">' . cqiE($metric['reading']) . '</div>'
        . '</td>';
}
$html .= '</tr></table>';

// ---------------------------------------------------------
// Company comparison.
//
// Mirrors the two company rows on the dashboard card, with the ratio
// drawn as a bar and each company placed against the cohort average.
// A bar reads faster than two percentages, and the average line turns
// the pair into a comparison rather than two isolated numbers.
// ---------------------------------------------------------
$html .= '<h2>Company Comparison</h2>';

if (!$companiesForPdf) {
    $html .= '<p class="body">Not enough keyword evidence yet to compare company IT-task exposure.</p>';
} else {
    $html .= '<table><tr>';

    foreach ($companiesForPdf as $entry) {
        $company    = $entry['company'];
        $ratio      = (float) $company['it_task_ratio'];
        $band       = cqiBandLabel($ratio);
        $bandClass  = 'bar-' . strtolower($band);
        $deltaVsAvg = round($ratio - $techPct, 1);

        $reading = sprintf(
            '%s of %d keyword matches are IT-related, placing it in the %s band (%s).',
            (int) $company['it_related_occurrences'],
            (int) $company['entity_occurrences'],
            $band,
            $band === 'High' ? 'at or above ' . number_format(CQI_IT_HIGH_BAND, 0) . '%'
                : ($band === 'Moderate'
                    ? number_format(CQI_IT_MODERATE_BAND, 0) . '% to ' . number_format(CQI_IT_HIGH_BAND - 1, 0) . '%'
                    : 'below ' . number_format(CQI_IT_MODERATE_BAND, 0) . '%')
        );

        $reading .= sprintf(
            ' That is %s the batch IT share of %s%%.',
            $deltaVsAvg > 0 ? sprintf('%.1f points above', $deltaVsAvg) : ($deltaVsAvg < 0 ? sprintf('%.1f points below', abs($deltaVsAvg)) : 'level with'),
            number_format($techPct, 1)
        );

        $html .= '<td style="width:50%;">'
            . '<div class="label" style="color:#475569;font-size:8px;font-weight:bold;text-transform:uppercase;">' . cqiE($entry['label']) . '</div>'
            . '<div style="color:#0F2854;font-size:11pt;font-weight:bold;margin-top:2px;">' . cqiE((string) $company['name']) . '</div>'
            . '<div style="color:#0F2854;font-size:13pt;font-weight:bold;">' . cqiE(number_format($ratio, 1)) . '%</div>'
            . '<div class="bar ' . $bandClass . '"><span style="width:' . cqiE(number_format(max(0.0, min(100.0, $ratio)), 1)) . '%;"></span></div>'
            . '<div style="color:#475569;font-size:7.5pt;line-height:1.45;">' . cqiE($reading) . '</div>'
            . '</td>';
    }

    $html .= '</tr></table>';

    // The spread only means something when both ends of the comparison exist.
    if ($itSpread !== null) {
        $html .= '<p class="body" style="margin-top:5px;">Spread between the two companies is '
            . '<strong>' . cqiE(number_format($itSpread, 1)) . ' percentage points</strong>'
            . ($itSpread < CQI_IT_MODERATE_BAND
                ? ', which is narrower than the 60-point band width used for placement; company placement on this cycle is therefore not strongly differentiated.'
                : ', which is wider than the 60-point band width used for placement, so company placement on this cycle is strongly differentiated.')
            . '</p>';
    } elseif (count($companiesForPdf) === 1) {
        $html .= '<p class="body" style="margin-top:5px;">Only one company currently has keyword evidence, so no spread can be computed. A comparison needs matches from at least two host companies.</p>';
    }
}

if ($gaps) {
    $html .= '<h2>Needs Attention (' . count($gaps) . ')</h2><ul>';
    foreach ($gaps as $gap) {
        $html .= '<li>' . cqiE($gap) . '</li>';
    }
    $html .= '</ul>';
}

$html .= '<h2>What to Do</h2>';
if ($actionBullets) {
    $priorityPill = in_array($recommendationPriority, ['High', 'Medium', 'Low', 'Continuous'], true)
        ? $recommendationPriority
        : 'Continuous';

    $html .= '<p class="body">Priority: <span class="pill pill-' . cqiE($priorityPill) . '">'
        . cqiE($recommendationPriority !== '' ? $recommendationPriority : 'Continuous') . '</span></p>';

    $html .= '<ol>';
    foreach ($actionBullets as $actionBullet) {
        $html .= '<li>' . cqiE($actionBullet) . '</li>';
    }
    $html .= '</ol>';
} else {
    $html .= '<p class="body">No recommended actions for this cycle.</p>';
}

try {
    $mpdf = new \Mpdf\Mpdf([
        'tempDir'      => sys_get_temp_dir(),
        'default_font' => 'dejavusans',
        'margin_left'  => 14,
        'margin_right' => 14,
        'margin_top'   => 16,
        'margin_bottom' => 18,
    ]);
    $mpdf->SetTitle('CQI Analysis Summary');
    $mpdf->SetAuthor('OJT Portal');
    $mpdf->SetHTMLFooter('<div style="font-size:8px;color:#64748b;text-align:center;border-top:1px solid #e2e8f0;padding-top:4px;">CQI Analysis Summary &bull; Generated ' . cqiE($generatedAt) . ' &bull; Page {PAGENO} of {nbpg}</div>');
    $mpdf->WriteHTML($html);

    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    $mpdf->Output($filenameBase . '.pdf', 'D');
} catch (\Throwable $exception) {
    error_log('CQI PDF export error: ' . $exception->getMessage());
    http_response_code(500);
    echo 'Unable to generate the CQI PDF export.';
}
exit;
