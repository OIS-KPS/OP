<?php
// src/cli/extract_report.php
/**
 * ============================================================
 * src/cli/extract_report.php
 * ============================================================
 *
 * Background entity extraction worker.
 *
 *     php src/cli/extract_report.php <report_id>
 *
 * Launched by entityExtractionSpawn() when a member of staff opens a report
 * whose entities have never been extracted, or were last extracted before the
 * dictionary changed. Runs the same extraction the page used to run inline and
 * stamps the result, so the next view reads it from report_entities instead of
 * paying for another model call.
 *
 * NOT WEB ACCESSIBLE
 * Lives under src/ precisely so it is unreachable over HTTP: the root .htaccess
 * blocks that directory, and this script has no session guard of its own and
 * takes a report id straight from argv. Do not move it to a reachable directory.
 *
 * RUNS OUTSIDE THE WEB CONTEXT
 * There is no session, no $_SERVER, no CSRF token and no logged-in user here. It
 * must not depend on any of them. resolveReportPdfPath() is shared with the
 * controllers precisely so this does not need its own copy of the path logic --
 * it computes the project root itself rather than reading DOCUMENT_ROOT.
 */

if (PHP_SAPI !== 'cli') {
    // Belt and braces. The .htaccess rule should make this unreachable, but the
    // cost of being wrong here is an unauthenticated way to run the model.
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__, 2);

require_once $projectRoot . '/config/db.php';
require_once $projectRoot . '/src/services/entity_extraction.php';
require_once $projectRoot . '/src/services/report_workspace.php';

$reportId = isset($argv[1]) ? (int) $argv[1] : 0;

if ($reportId <= 0) {
    fwrite(STDERR, "Usage: php src/cli/extract_report.php <report_id>\n");
    exit(1);
}

/**
 * The PDF for this report, or null.
 *
 * previous_file_path is checked as well: a re-upload archives the old file
 * there, and the shared resolver has no way to know which one is live.
 */
$stmt = $pdo->prepare(
    'SELECT file_path, previous_file_path
     FROM reports
     WHERE id = ?
     LIMIT 1'
);

$stmt->execute([$reportId]);

$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    fwrite(STDERR, "Report {$reportId} does not exist.\n");
    exit(1);
}

$pdfPath = resolveReportPdfPath((string) ($report['file_path'] ?? ''));

if ($pdfPath === null) {
    // The file has gone from disk. Not retryable -- it will not come back on its
    // own -- so it is recorded rather than silently retried on every view.
    entityExtractionRecordError(
        $pdo,
        $reportId,
        'The uploaded PDF could not be found on the server.'
    );

    fwrite(STDERR, "Report {$reportId}: PDF not found on disk.\n");
    exit(1);
}

/*
 * ensureReportEntitiesExtracted() is used rather than calling the extractor
 * directly, so the worker inherits the same behaviour as the page path: the
 * freshness check, the per-report MySQL lock, the re-check after taking the lock,
 * and the permanent/transient split.
 *
 * $force is false. The worker was spawned because the report genuinely needed
 * work; if by the time it starts the dictionary changed again, or another
 * process finished first, reportNeedsEntityExtraction() returns false and this
 * exits without a second model call. That is the whole point of routing both
 * paths through one function.
 *
 * $async is false -- there is nothing left to defer to. This process *is* the
 * background.
 */
$extraction = ensureReportEntitiesExtracted($pdfPath, $reportId, $pdo, false, false);

if (!empty($extraction['ran'])) {
    printf(
        "Report %d: extracted %d entit%s.\n",
        $reportId,
        (int) ($extraction['entity_count'] ?? 0),
        ((int) ($extraction['entity_count'] ?? 0)) === 1 ? 'y' : 'ies'
    );

    exit(0);
}

if (!empty($extraction['error'])) {
    fwrite(STDERR, 'Report ' . $reportId . ': ' . $extraction['error'] . "\n");
    exit(1);
}

// No error and nothing ran: the report was found fresh, or another worker beat
// this one to it. Either way that is a success, not a fault.
printf("Report %d: nothing to do.\n", $reportId);
exit(0);
