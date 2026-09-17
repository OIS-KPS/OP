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
$highlights[] = sprintf(
    'Academic evidence coverage is %.1f%% for verified evaluations and %.1f%% for reports with persisted extracted entities.',
    $coverage,
    $reportsCoverage
);
$highlights[] = sprintf(
    'Recorded activity is %.1f%% technical and %.1f%% clerical, which should be reviewed against the academic program\'s expected learning outcomes.',
    $techPct,
    $clerPct
);

// ---------------------------------------------------------------
// PDF export (mPDF).
// ---------------------------------------------------------------
$statusColor = $status === 'On track' ? '#047857' : ($status === 'Needs attention' ? '#B45309' : ($status === 'Data error' ? '#BE123C' : '#475569'));

$html  = '<style>';
$html .= 'body { font-family: dejavusans; color: #0f172a; font-size: 10px; }';
$html .= 'h1 { color: #0F2854; font-size: 18px; margin: 0 0 2px 0; }';
$html .= '.muted { color: #64748b; font-size: 9px; }';
$html .= 'h2 { color: #0F2854; font-size: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; margin: 14px 0 6px 0; }';
$html .= '.badge { display: inline-block; padding: 3px 8px; border-radius: 8px; font-size: 9px; font-weight: bold; border: 1px solid ' . $statusColor . '; color: ' . $statusColor . '; }';
$html .= 'table { width: 100%; border-collapse: collapse; margin-top: 4px; }';
$html .= 'th { background: #0F2854; color: #ffffff; font-size: 8px; text-align: left; padding: 4px; }';
$html .= 'td { border: 1px solid #cbd5e1; font-size: 8px; padding: 4px; vertical-align: top; }';
$html .= '.metric { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px; }';
$html .= '.metric .label { color: #475569; font-size: 8px; font-weight: bold; text-transform: uppercase; }';
$html .= '.metric .value { color: #0F2854; font-size: 13px; font-weight: bold; }';
$html .= 'ul { margin: 4px 0 0 14px; padding: 0; }';
$html .= 'li { margin-bottom: 3px; line-height: 1.5; }';
$html .= '</style>';

$html .= '<h1>CQI Summary &amp; Action Plan</h1>';
$html .= '<div class="muted">OJT Portal - Coordinator CQI Analytics</div>';
$html .= '<p><span class="badge">' . cqiE($status) . '</span> &nbsp; <strong>Period:</strong> ' . cqiE($period) . '<br><span class="muted">Generated: ' . cqiE($generatedAt) . '</span></p>';

$html .= '<table><tr>';
foreach ($metrics as $metric) {
    $html .= '<td class="metric"><div class="label">' . cqiE($metric['label']) . '</div><div class="value">' . cqiE($metric['value']) . '</div></td>';
}
$html .= '</tr></table>';

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
        $html .= '<td><strong>' . cqiE($row['priority'] ?? '') . '</strong></td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
}

$html .= '<h2>Academic Interpretation</h2><p>' . cqiE($narrative !== '' ? $narrative : 'The current CQI evidence should be reviewed before academic decisions are made.') . '</p>';

$html .= '<h2>Action Plan</h2><p>' . cqiE($recommendation !== '' ? $recommendation : 'Use the next academic CQI cycle to verify evaluation completion, improve report extraction coverage, compare company IT-task exposure, and review entity classifications against the program learning outcomes.') . '</p>';

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
