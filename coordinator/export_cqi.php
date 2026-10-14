<?php
// coordinator/export_cqi.php
// Exports the CQI Summary & Action Plan as a PDF (mPDF).
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
$narrative     = trim((string) ($summary['narrative'] ?? ''));
$strengths     = is_array($summary['strengths'] ?? null) ? array_values($summary['strengths']) : [];
$gaps          = is_array($summary['gaps'] ?? null) ? array_values($summary['gaps']) : [];
$recommendation = trim((string) ($cqiOverallRecommendation ?? ''));

$totalStudents           = (int) ($totalStudents ?? 0);
$evaluatedStudents       = (int) ($evaluatedStudents ?? 0);
$totalReports            = (int) ($totalReports ?? 0);
$totalReportsWithEntities = (int) ($totalReportsWithEntities ?? 0);
$totalEntityOccurrences  = (int) ($totalEntityOccurrences ?? 0);
$technicalEntityOccurrences = (int) ($technicalEntityOccurrences ?? 0);
$clericalEntityOccurrences  = (int) ($clericalEntityOccurrences ?? 0);
$confidence = (float) ($spacyConfidence ?? 0);
$coverage   = (float) ($evaluationCoveragePct ?? 0);

$techPct = $totalEntityOccurrences > 0 ? round(($technicalEntityOccurrences / $totalEntityOccurrences) * 100, 1) : 0.0;
$clerPct = $totalEntityOccurrences > 0 ? round(($clericalEntityOccurrences / $totalEntityOccurrences) * 100, 1) : 0.0;
$reportsCoverage = $totalReports > 0 ? round(($totalReportsWithEntities / $totalReports) * 100, 1) : 0.0;

$highestIt = is_array($highestItCompany ?? null) ? $highestItCompany : null;
$lowestIt  = is_array($lowestItCompany ?? null) ? $lowestItCompany : null;
$priorities = is_array($entityPrioritySummary ?? null) ? array_values($entityPrioritySummary) : [];
$priorities = array_slice($priorities, 0, 10);

$generatedAt  = date('F j, Y g:i A');
$filenameBase = 'CQI-Summary-Action-Plan-' . date('Y-m-d');

$metrics = [
    ['label' => 'Verified evaluations', 'value' => $evaluatedStudents . ' / ' . $totalStudents],
    ['label' => 'Reports with entities', 'value' => $totalReportsWithEntities . ' / ' . $totalReports],
    ['label' => 'Technical / Clerical', 'value' => number_format($techPct, 1) . '% / ' . number_format($clerPct, 1) . '%'],
    ['label' => 'Extraction confidence', 'value' => number_format($confidence, 2) . '%'],
];

$highlights = [];
if ($highestIt) {
    $highlights[] = sprintf(
        'The company with the highest IT-related task ratio is %s at %.1f%%, based on %d of %d verified entity occurrences.',
        (string) $highestIt['name'],
        (float) $highestIt['it_task_ratio'],
        (int) $highestIt['it_related_occurrences'],
        (int) $highestIt['entity_occurrences']
    );
} else {
    $highlights[] = 'No company currently has enough extracted-entity evidence for an IT-task comparison.';
}
if ($lowestIt) {
    $highlights[] = sprintf(
        'The company with the lowest IT-related task ratio is %s at %.1f%%, based on %d of %d verified entity occurrences.',
        (string) $lowestIt['name'],
        (float) $lowestIt['it_task_ratio'],
        (int) $lowestIt['it_related_occurrences'],
        (int) $lowestIt['entity_occurrences']
    );
}
// Coverage and the technical/clerical split are intentionally NOT repeated as
// bullets -- both already appear in the metric tiles directly above. The figures
// are still printed once, in $metrics.
unset($coverage, $reportsCoverage);

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
$html .= '.metric { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px; }';
$html .= '.metric .label { color: #475569; font-size: 8px; font-weight: bold; text-transform: uppercase; }';
$html .= '.metric .value { color: #0F2854; font-size: 13px; font-weight: bold; }';
$html .= 'ul { margin: 4px 0 0 14px; padding: 0; }';
$html .= 'li { margin-bottom: 5px; line-height: 1.55; }';
$html .= 'p.body { font-size: 9px; line-height: 1.65; margin: 0 0 6px 0; }';
$html .= '.rule { border-bottom: 2px solid #0F2854; margin: 2px 0 8px 0; }';
// Priority pills reuse the dashboard's severity language: rose = act now,
// amber = scheduled, slate = continuous. Bold text alone was unreadable in a
// table of eleven near-identical rows.
$html .= '.pill { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; border: 1px solid; }';
$html .= '.pill-High { background:#fff1f2; color:#be123c; border-color:#fda4af; }';
$html .= '.pill-Medium { background:#fffbeb; color:#b45309; border-color:#fcd34d; }';
$html .= '.pill-Low, .pill-Continuous { background:#f1f5f9; color:#475569; border-color:#cbd5e1; }';
// Each grouped sub-finding keeps its own line inside the parent row so nothing
// is flattened into a single sentence.
$html .= '.sub { font-size: 7.5pt; color:#334155; padding: 2px 0 2px 8px; border-left: 2px solid #cbd5e1; margin: 2px 0; }';
$html .= '</style>';

$html .= '<h1>CQI Summary &amp; Action Plan</h1>';
$html .= '<div class="rule"></div>';
$html .= '<div class="muted">OJT Portal - Coordinator CQI Analytics</div>';
$html .= '<p><span class="badge">' . cqiE($status) . '</span> &nbsp; <strong>Period:</strong> ' . cqiE($period) . '<br><span class="muted">Generated: ' . cqiE($generatedAt) . '</span></p>';

$html .= '<table><tr>';
foreach ($metrics as $metric) {
    $html .= '<td class="metric"><div class="label">' . cqiE($metric['label']) . '</div><div class="value">' . cqiE($metric['value']) . '</div></td>';
}
$html .= '</tr></table>';

// Only the two company comparisons survive here. The coverage and the
// technical/clerical split were also listed as bullets, but they already appear
// verbatim in the metric tiles directly above, so repeating them added length
// without adding information.
$html .= '<h2>Evidence Highlights</h2><ul>';
foreach ($highlights as $highlight) {
    $html .= '<li>' . cqiE($highlight) . '</li>';
}
$html .= '</ul>';

if ($strengths) {
    $html .= '<h2>Strengths</h2><ul>';
    foreach ($strengths as $strength) {
        $html .= '<li>' . cqiE($strength) . '</li>';
    }
    $html .= '</ul>';
}

if ($gaps) {
    $html .= '<h2>Priority Gaps</h2><ul>';
    foreach ($gaps as $gap) {
        $html .= '<li>' . cqiE($gap) . '</li>';
    }
    $html .= '</ul>';
}

if ($priorities) {
    $html .= '<h2>Entity Priority Analysis</h2><table><tr>';
    foreach (['Entity', 'Category', 'Class', 'Freq.', 'Share', 'Priority'] as $heading) {
        $html .= '<th>' . cqiE($heading) . '</th>';
    }
    $html .= '</tr>';
    foreach ($priorities as $row) {
        $html .= '<tr>';
        $html .= '<td>' . cqiE($row['entity'] ?? '') . '</td>';
        $html .= '<td>' . cqiE($row['category'] ?? '') . '</td>';
        $html .= '<td>' . cqiE($row['classification'] ?? '') . '</td>';
        $html .= '<td>' . cqiE((int) ($row['frequency'] ?? 0)) . '</td>';
        $html .= '<td>' . cqiE(number_format((float) ($row['share_pct'] ?? 0), 1)) . '%</td>';
        $html .= '<td><span class="pill pill-' . cqiE(in_array($row['priority'] ?? '', ['High','Medium','Low'], true) ? (string) $row['priority'] : 'Low') . '">' . cqiE($row['priority'] ?? '') . '</span></td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
}

$html .= '<h2>Academic Interpretation</h2>';
// The narrative is one dense generated paragraph. It is split here for
// legibility only -- the wording and every figure are the shared string from
// src/services/cqi_dashboard_data.php, left untouched so the on-screen
// dashboard wording does not change.
$narrativeText = $narrative !== ''
    ? $narrative
    : 'The current CQI evidence should be reviewed before academic decisions are made.';
$narrativeSentences = preg_split('/(?<=\.)\s+/', trim($narrativeText)) ?: [];
foreach ($narrativeSentences as $sentence) {
    $sentence = trim($sentence);
    if ($sentence === '') {
        continue;
    }
    $html .= '<p class="body">' . cqiE($sentence) . '</p>';
}

$html .= '<h2>Action Plan</h2>';
$html .= '<p class="body">' . cqiE($recommendation !== '' ? $recommendation : 'Use the next academic CQI cycle to verify evaluation completion, improve report extraction coverage, compare company IT-task exposure, and review entity classifications against the program learning outcomes.') . '</p>';

// ---------------------------------------------------------------
// Action plan table.
//
// cqi_dashboard_data.php already builds one entry per finding with a priority,
// area, finding, action, owner, timeframe, measure and status. The export used
// to print a single prose paragraph and discard all of it. Every field is
// rendered here.
//
// The entity-priority entries repeat the same boilerplate action for each term,
// so eleven near-identical rows are collapsed into one grouped row. Each term
// keeps its own finding and priority line inside that cell -- nothing is merged
// away or summarised away.
// ---------------------------------------------------------------
$actionPlan = is_array($cqiActionPlan ?? null) ? array_values($cqiActionPlan) : [];
$priorityRank = ['High' => 0, 'Medium' => 1, 'Low' => 2, 'Continuous' => 3];
usort($actionPlan, static function (array $a, array $b) use ($priorityRank): int {
    $left  = $priorityRank[(string) ($a['priority'] ?? '')] ?? 9;
    $right = $priorityRank[(string) ($b['priority'] ?? '')] ?? 9;
    return $left <=> $right;
});

$entityActions = [];
$standaloneActions = [];
foreach ($actionPlan as $entry) {
    $area = (string) ($entry['area'] ?? '');
    if (strpos($area, 'Entity priority: ') === 0) {
        $entityActions[] = $entry;
        continue;
    }
    $standaloneActions[] = $entry;
}

$html .= '<table>';
$html .= '<thead><tr>'
    . '<th style="width:7%;">Priority</th>'
    . '<th style="width:15%;">Area</th>'
    . '<th style="width:38%;">Finding / Action</th>'
    . '<th style="width:14%;">Owner</th>'
    . '<th style="width:14%;">Timeframe</th>'
    . '<th style="width:6%;">Status</th>'
    . '<th style="width:6%;">Measure</th>'
    . '</tr></thead><tbody>';

foreach ($standaloneActions as $entry) {
    $priority = (string) ($entry['priority'] ?? '');
    $pill = isset($priorityRank[$priority]) ? $priority : 'Low';

    $html .= '<tr>'
        . '<td><span class="pill pill-' . cqiE($pill) . '">' . cqiE($priority !== '' ? $priority : 'Low') . '</span></td>'
        . '<td>' . cqiE($entry['area'] ?? '') . '</td>'
        . '<td>'
        .   '<div style="font-weight:bold;margin-bottom:2px;">' . cqiE($entry['finding'] ?? '') . '</div>'
        .   '<div style="color:#334155;">' . cqiE($entry['action'] ?? '') . '</div>'
        . '</td>'
        . '<td>' . cqiE($entry['owner'] ?? '') . '</td>'
        . '<td>' . cqiE($entry['timeframe'] ?? '') . '</td>'
        . '<td>' . cqiE($entry['status'] ?? '') . '</td>'
        . '<td>' . cqiE($entry['measure'] ?? '') . '</td>'
        . '</tr>';
}

if ($entityActions) {
    $termsList = [];
    $owners    = [];
    $timeframes = [];
    $measures  = [];
    $highest   = 'Low';
    foreach ($entityActions as $entry) {
        $term = trim(str_replace('Entity priority: ', '', (string) ($entry['area'] ?? '')));
        $termsList[] = '<div class="sub">'
            . '<span class="pill pill-' . cqiE($priorityRank[(string) $entry['priority']] ?? 2) . '">' . cqiE($entry['priority'] ?? '') . '</span> '
            . '<strong>' . cqiE($term) . '</strong> &mdash; ' . cqiE($entry['finding'] ?? '')
            . '</div>';

        if (isset($priorityRank[(string) $entry['priority']]) && $priorityRank[(string) $entry['priority']] < $priorityRank[$highest]) {
            $highest = (string) $entry['priority'];
        }
        $owners[]    = (string) ($entry['owner'] ?? '');
        $timeframes[] = (string) ($entry['timeframe'] ?? '');
        $measures[]  = (string) ($entry['measure'] ?? '');
    }

    $grouped = static function (array $values): string {
        return implode('; ', array_values(array_unique(array_filter($values))));
    };

    $html .= '<tr>'
        . '<td><span class="pill pill-' . cqiE($highest) . '">' . cqiE($highest) . '</span></td>'
        . '<td>Entity taxonomy review (' . count($entityActions) . ' recurring terms)</td>'
        . '<td>'
        .   '<div style="font-weight:bold;margin-bottom:3px;">'
        .     count($entityActions) . ' terms account for a high share of extracted occurrences and are queued for term review.'
        .   '</div>'
        .   '<div style="color:#334155;margin-bottom:4px;">Monitor each activity and verify that its classification and aliases remain accurate before the next monthly review.</div>'
        .   implode('', $termsList)
        . '</td>'
        . '<td>' . cqiE($grouped($owners)) . '</td>'
        . '<td>' . cqiE($grouped($timeframes)) . '</td>'
        . '<td>Planned</td>'
        . '<td>' . cqiE($grouped($measures)) . '</td>'
        . '</tr>';
}

$html .= '</tbody></table>';

try {
    $mpdf = new \Mpdf\Mpdf([
        'tempDir'      => sys_get_temp_dir(),
        'default_font' => 'dejavusans',
        'margin_left'  => 14,
        'margin_right' => 14,
        'margin_top'   => 16,
        'margin_bottom' => 18,
    ]);
    $mpdf->SetTitle('CQI Summary & Action Plan');
    $mpdf->SetAuthor('OJT Portal');
    $mpdf->SetHTMLFooter('<div style="font-size:8px;color:#64748b;text-align:center;border-top:1px solid #e2e8f0;padding-top:4px;">CQI Summary &amp; Action Plan &bull; Generated ' . cqiE($generatedAt) . ' &bull; Page {PAGENO} of {nbpg}</div>');
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
