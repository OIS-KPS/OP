<?php

/**
 * ============================================================
 * services/entity_extraction.php
 * ============================================================
 *
 * Shared entity extraction service.
 *
 * PHP pages should NOT directly call extract_entities.py.
 *
 * Flow:
 *
 * PDF
 *  ↓
 * FastAPI /extract-pdf
 *  ↓
 * trained spaCy model + terms.csv
 *  ↓
 * JSON entities
 *  ↓
 * report_entities
 *
 * ============================================================
 */

if (!defined('ENTITY_API_URL')) {
    define(
        'ENTITY_API_URL',
        'http://127.0.0.1:8000'
    );
}

/*
 * Background spawn settings (PHP CLI path, debounce window).
 *
 * config/db.php loads .env before this file is reached at every call site, so
 * $_ENV is already populated here. The require is placed inside a guard because
 * this file is included from both the web controllers and the CLI worker, and
 * only one of them may have pulled it in already.
 */
require_once __DIR__ . '/../../config/background_jobs.php';

/**
 * ------------------------------------------------------------
 * POST JSON to FastAPI
 * ------------------------------------------------------------
 */
function entityApiPostJson(string $url, array $payload): array
{
    $json = json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    if ($json === false) {
        return [
            'success' => false,
            'error' => 'Unable to encode extraction request as JSON.',
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 120
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'success' => false,
            'error' => 'Unable to connect to the entity extraction API.',
            'details' => $error,
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    $httpCode = (int) curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    $data = json_decode(
        $response,
        true
    );

    if (!is_array($data)) {
        return [
            'success' => false,
            'error' => 'FastAPI returned invalid JSON.',
            'details' => $response,
            'http_code' => $httpCode,
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        return [
            'success' => false,
            'error' => $data['error']
                ?? 'FastAPI returned an HTTP error.',
            'details' => $data['details']
                ?? '',
            'http_code' => $httpCode,
            'entities' => [],
            'content' => $data['content'] ?? '',
            'summary' => $data['summary'] ?? []
        ];
    }

    return $data;
}


/**
 * ------------------------------------------------------------
 * POST PDF to FastAPI /extract-pdf
 * ------------------------------------------------------------
 */
function entityApiExtractPdf(string $pdfPath): array
{
    if (!is_file($pdfPath)) {
        return [
            'success' => false,
            'error' => 'PDF file was not found.',
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    $realPdfPath = realpath($pdfPath);

    if ($realPdfPath === false) {
        return [
            'success' => false,
            'error' => 'Unable to resolve PDF path.',
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    /**
     * CURLFile sends the PDF as multipart/form-data.
     */
    $file = new CURLFile(
        $realPdfPath,
        'application/pdf',
        basename($realPdfPath)
    );

    $ch = curl_init(
        ENTITY_API_URL . '/extract-pdf'
    );

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'file' => $file
        ],
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 120
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'success' => false,
            'error' => 'Unable to connect to the FastAPI entity extraction service.',
            'details' => $error,
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    $httpCode = (int) curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    $data = json_decode(
        $response,
        true
    );

    if (!is_array($data)) {
        return [
            'success' => false,
            'error' => 'FastAPI returned invalid JSON.',
            'details' => $response,
            'http_code' => $httpCode,
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        return [
            'success' => false,
            'error' => $data['error']
                ?? 'FastAPI PDF extraction failed.',
            'details' => $data['details']
                ?? '',
            'http_code' => $httpCode,
            'entities' => [],
            'content' => $data['content'] ?? '',
            'summary' => $data['summary'] ?? []
        ];
    }

    return [
        'success' => !empty($data['success']),
        'content' => $data['content'] ?? '',
        'entities' => is_array($data['entities'] ?? null)
            ? $data['entities']
            : [],
        'summary' => is_array($data['summary'] ?? null)
            ? $data['summary']
            : [],
        'entity_count' => (int) (
            $data['entity_count']
            ?? count($data['entities'] ?? [])
        ),
        'details' => $data['details'] ?? ''
    ];
}


/**
 * ------------------------------------------------------------
 * Convert FastAPI entity to report_entities format
 * ------------------------------------------------------------
 *
 * FastAPI:
 *
 * term
 * category
 * confidence
 * source
 *
 * Database:
 *
 * entity_name
 * canonical_name
 * category
 * activity_type
 * it_related
 * source
 * confidence_score
 *
 * ------------------------------------------------------------
 */
function normalizeExtractedEntity(array $entity): ?array
{
    $entityName = trim(
        (string) (
            $entity['term']
            ?? $entity['entity_name']
            ?? $entity['entity']
            ?? $entity['text']
            ?? ''
        )
    );

    if ($entityName === '') {
        return null;
    }

    $canonicalName = trim(
        (string) (
            $entity['canonical_name']
            ?? $entityName
        )
    );

    if ($canonicalName === '') {
        $canonicalName = $entityName;
    }

    /**
     * Category returned by FastAPI.
     *
     * Expected examples:
     *
     * IT_TERM
     * CLERICAL_TERM
     * HARDWARE_TERM
     * SOFTWARE_TERM
     */
    $category = trim(
        (string) (
            $entity['category']
            ?? 'Other'
        )
    );

    if ($category === '') {
        $category = 'Other';
    }

    /**
     * --------------------------------------------------------
     * Activity type
     * --------------------------------------------------------
     *
     * If FastAPI already supplies activity_type,
     * preserve it.
     *
     * Otherwise derive it from the entity category.
     */
    $activityType = ucfirst(strtolower(trim((string) ($entity['activity_type'] ?? ''))));
    $categoryUpper = strtoupper($category);

    // FastAPI may return the generic defaults Other/unknown for model-only
    // entities. Derive the portal classification from the category in that
    // case; otherwise every model entity is stored as unclassified.
    if (
        $activityType === '' ||
        $activityType === 'Other' ||
        !in_array($activityType, ['Software', 'Hardware', 'Clerical', 'Other'], true)
    ) {
        if (str_contains($categoryUpper, 'HARDWARE')) {
            $activityType = 'Hardware';
        } elseif (str_contains($categoryUpper, 'CLERICAL')) {
            $activityType = 'Clerical';
        } elseif (
            str_contains($categoryUpper, 'IT') ||
            str_contains($categoryUpper, 'SOFTWARE') ||
            str_contains($categoryUpper, 'TECHNICAL')
        ) {
            $activityType = 'Software';
        } else {
            $activityType = 'Other';
        }
    }

    /**
     * --------------------------------------------------------
     * IT-related
     * --------------------------------------------------------
     */
    $itRelated = strtolower(
        trim(
            (string) (
                $entity['it_related']
                ?? ''
            )
        )
    );

    // Treat unknown as missing when activity_type/category gives a safe answer.
    if ($itRelated === '' || $itRelated === 'unknown' || !in_array($itRelated, ['yes', 'no'], true)) {
        if ($activityType === 'Clerical' || str_contains($categoryUpper, 'CLERICAL')) {
            $itRelated = 'no';
        } elseif (
            in_array($activityType, ['Software', 'Hardware'], true) ||
            str_contains($categoryUpper, 'IT') ||
            str_contains($categoryUpper, 'SOFTWARE') ||
            str_contains($categoryUpper, 'HARDWARE') ||
            str_contains($categoryUpper, 'TECHNICAL')
        ) {
            $itRelated = 'yes';
        } else {
            $itRelated = 'unknown';
        }
    }

    /**
     * --------------------------------------------------------
     * Source
     * --------------------------------------------------------
     */
    $source = strtolower(
        trim(
            (string) (
                $entity['source']
                ?? 'ml'
            )
        )
    );

    /**
     * Map FastAPI sources to the values accepted
     * by the existing report_entities table.
     */
    if (
        in_array(
            $source,
            [
                'dictionary',
                'predefined'
            ],
            true
        )
    ) {
        $source = 'predefined';

    } elseif (
        in_array(
            $source,
            [
                'ml',
                'spacy',
                'transformer',
                'model'
            ],
            true
        )
    ) {
        $source = 'spacy';

    } elseif (
        in_array(
            $source,
            [
                'spacy_predefined',
                'hybrid'
            ],
            true
        )
    ) {
        $source = 'spacy_predefined';

    } else {
        $source = 'spacy';
    }

    /**
     * --------------------------------------------------------
     * Confidence
     * --------------------------------------------------------
     *
     * FastAPI uses 0.0 - 1.0.
     * Database currently uses 0 - 100.
     */
    $confidence = $entity['confidence']
        ?? $entity['confidence_score']
        ?? null;

    if ($confidence === null || !is_numeric($confidence)) {
        $confidence = 0;
    } else {
        $confidence = (float) $confidence;

        if ($confidence <= 1) {
            $confidence *= 100;
        }
    }

    $confidence = max(
        0,
        min(100, $confidence)
    );

    return [
        'entity_name' => $entityName,
        'canonical_name' => $canonicalName,
        'category' => $category,
        'activity_type' => $activityType,
        'it_related' => $itRelated,
        'source' => $source,
        'confidence_score' => round($confidence, 2)
    ];
}


/**
 * ------------------------------------------------------------
 * Clear replaceable entities for a report
 * ------------------------------------------------------------
 *
 * The single definition of what a re-extraction is allowed to destroy.
 *
 * Only ACTIVE, AUTO-EXTRACTED rows are cleared. Two kinds of row are
 * coordinator decisions, not extraction output, and must survive:
 *
 *   source = 'manual_addition'  a term the coordinator typed in
 *   is_archived = 1             a term the coordinator hid/ignored
 *
 * Before this function existed the DELETE had no filter, so re-running
 * extraction (which happens whenever a report is viewed) wiped manual
 * additions and resurrect-then-delete archived ones, making both features
 * unusable.
 *
 * Narrowing the WHERE clause alone is not enough: the surviving manual rows
 * would then collide with freshly extracted terms of the same name and
 * produce duplicates. So this also returns the lowercased names already held
 * as manual additions, and the caller skips inserting those.
 *
 * MUST be called inside a transaction; the caller commits.
 *
 * Returns the list of lowercased manual entity names for this report.
 */
function clearReplaceableReportEntities(PDO $pdo, int $reportId): array
{
    $deleteStmt = $pdo->prepare("
        DELETE FROM report_entities
        WHERE report_id = ?
          AND (source IS NULL OR source <> 'manual_addition')
          AND is_archived = 0
    ");

    $deleteStmt->execute([$reportId]);

    $manualStmt = $pdo->prepare("
        SELECT LOWER(TRIM(entity_name)) AS n
        FROM report_entities
        WHERE report_id = ?
          AND source = 'manual_addition'
    ");
    $manualStmt->execute([$reportId]);

    return array_column(
        $manualStmt->fetchAll(PDO::FETCH_ASSOC),
        'n'
    );
}


/**
 * ------------------------------------------------------------
 * Save extracted entities
 * ------------------------------------------------------------
 */
function saveExtractedEntities(
    PDO $pdo,
    int $reportId,
    array $entities
): bool {

    try {

        $pdo->beginTransaction();

        $manualNames = clearReplaceableReportEntities($pdo, $reportId);

        $skipped = 0;

        $insertStmt = $pdo->prepare("
            INSERT INTO report_entities
            (
                report_id,
                entity_name,
                canonical_name,
                category,
                it_related,
                source,
                confidence_score
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ");

        foreach ($entities as $entity) {

            if (!is_array($entity)) {
                continue;
            }

            $normalized = normalizeExtractedEntity(
                $entity
            );

            if ($normalized === null) {
                continue;
            }

            // Skip anything the coordinator already added by hand for this
            // report, so the same term never appears twice.
            $candidate = strtolower(trim((string) $normalized['entity_name']));
            if ($candidate !== '' && in_array($candidate, $manualNames, true)) {
                $skipped++;
                continue;
            }

            $insertStmt->execute([
                $reportId,
                $normalized['entity_name'],
                $normalized['canonical_name'],
                $normalized['category'],
                $normalized['it_related'],
                $normalized['source'],
                $normalized['confidence_score']
            ]);
        }

        $pdo->commit();

        if ($skipped > 0) {
            error_log(
                "Entity extraction: skipped {$skipped} extracted term(s) for report " .
                "{$reportId} because the coordinator had already added them manually."
            );
        }

        return true;

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log(
            'Entity save error: ' .
            $e->getMessage()
        );

        return false;
    }
}


/**
 * ------------------------------------------------------------
 * Main extraction function
 * ------------------------------------------------------------
 *
 * This is the function PHP pages should call.
 *
 * ------------------------------------------------------------
 */
function extractEntitiesFromReport(
    string $pdfPath,
    int $reportId,
    PDO $pdo,
    bool $saveToDatabase = true
): array {

    if (!is_file($pdfPath)) {
        return [
            'success' => false,
            'error' => 'PDF file was not found.',
            'entities' => [],
            'content' => '',
            'summary' => [],
            'entity_count' => 0
        ];
    }

    /**
     * Call FastAPI.
     */
    $result = entityApiExtractPdf(
        $pdfPath
    );

    if (empty($result['success'])) {
        return [
            'success' => false,
            'error' => $result['error']
                ?? 'Entity extraction failed.',
            'details' => $result['details']
                ?? '',
            // Carried through deliberately. Without it the caller cannot tell a
            // 400 (this PDF will never be accepted, stop asking) from a 503 (the
            // API is briefly down, ask again later), so every failure would look
            // transient and a permanently unanalysable report would be retried on
            // every single view for ever.
            'http_code' => (int) ($result['http_code'] ?? 0),
            'entities' => [],
            'content' => $result['content'] ?? '',
            'summary' => $result['summary'] ?? [],
            'entity_count' => 0
        ];
    }

    $rawEntities = $result['entities'] ?? [];

    if (!is_array($rawEntities)) {
        $rawEntities = [];
    }

    /**
     * Normalize API entities for the existing database/UI.
     */
    $normalizedEntities = [];

    foreach ($rawEntities as $entity) {

        if (!is_array($entity)) {
            continue;
        }

        $normalized = normalizeExtractedEntity(
            $entity
        );

        if ($normalized !== null) {
            $normalizedEntities[] = $normalized;
        }
    }

    /**
     * Save to report_entities.
     */
    if ($saveToDatabase) {

        $saved = saveExtractedEntities(
            $pdo,
            $reportId,
            $normalizedEntities
        );

        if (!$saved) {
            return [
                'success' => false,
                'error' => 'Entities were extracted but could not be saved to the database.',
                'entities' => $normalizedEntities,
                'content' => $result['content'] ?? '',
                'summary' => $result['summary'] ?? [],
                'entity_count' => count($normalizedEntities)
            ];
        }
    }

    return [
        'success' => true,
        'content' => $result['content'] ?? '',
        'entities' => $normalizedEntities,
        'summary' => $result['summary'] ?? [],
        'entity_count' => count($normalizedEntities)
    ];
}


/**
 * ------------------------------------------------------------
 * Current Entity Dictionary version
 * ------------------------------------------------------------
 *
 * MAX(predefined_entities.updated_at) is the watermark stored alongside each
 * report's extraction timestamp. Any dictionary edit -- a new term, an alias,
 * a reclassification, an archive -- bumps updated_at, so a stored watermark
 * that is older than the current one means the extraction predates the edit.
 *
 * Dictionary edits do NOT by themselves invalidate. Deciding when to re-run
 * extraction is left to the caller; see reportNeedsEntityExtraction().
 */
function entityDictionaryVersion(PDO $pdo): ?string
{
    try {
        $stmt = $pdo->query(
            'SELECT MAX(updated_at) FROM predefined_entities'
        );

        $value = $stmt !== false ? $stmt->fetchColumn() : false;

        return ($value === false || $value === null)
            ? null
            : (string) $value;

    } catch (Throwable $e) {
        error_log(
            'Entity dictionary version lookup failed: ' . $e->getMessage()
        );

        // Unknown watermark. Treated as "changed" by the caller so the report
        // is never silently left stale because of a query error.
        return null;
    }
}


/**
 * ------------------------------------------------------------
 * Does this report need extracting again?
 * ------------------------------------------------------------
 *
 * Extraction is expensive (a blocking FastAPI call), so it must not run on
 * every page view. It runs only when:
 *
 *   - the report has never been extracted (entities_extracted_at IS NULL), or
 *   - the Entity Dictionary has changed since that extraction ran
 *
 * $force still bypasses both checks. There is no UI for it: the coordinator
 * "Re-extract" button was removed, so the only way to trigger it is calling
 * coordinator/view_report.php?extract=1 directly. That is left in place as a
 * deliberate escape hatch for the case where a single report must be re-run
 * without waiting for a dictionary edit. Pass false to drop it.
 *
 * The dictionary clause is what preserves the original intent of the old
 * "always extract on every view to pick up new predefined entities" comment,
 * without paying for it on every click.
 *
 * reports.updated_at is deliberately NOT consulted: it is
 * ON UPDATE current_timestamp() and is bumped by every approve / reject /
 * remark write, so it says nothing about extraction.
 */
function reportNeedsEntityExtraction(
    PDO $pdo,
    int $reportId,
    bool $force = false
): bool {

    if ($force) {
        return true;
    }

    try {
        $stmt = $pdo->prepare(
            'SELECT entities_extracted_at, entities_dictionary_version
             FROM reports
             WHERE id = ?
             LIMIT 1'
        );
        $stmt->execute([$reportId]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$report) {
            return false;
        }

        if (empty($report['entities_extracted_at'])) {
            return true;
        }

        $currentVersion = entityDictionaryVersion($pdo);

        // A NULL watermark means "never recorded". If it also cannot be read
        // now, there is nothing to compare, so extract rather than guess.
        if ($currentVersion === null || $report['entities_dictionary_version'] === null) {
            return true;
        }

        return (string) $report['entities_dictionary_version'] !== $currentVersion;

    } catch (Throwable $e) {
        error_log(
            'Entity extraction check failed for report ' . $reportId . ': ' .
            $e->getMessage()
        );

        // Fail open: extract rather than show a possibly stale list.
        return true;
    }
}


/**
 * ------------------------------------------------------------
 * Record that extraction completed for a report
 * ------------------------------------------------------------
 *
 * Only called after a successful extraction, so a failed run leaves the report
 * unstamped and is retried on the next view.
 *
 * entities_extraction_started_at is cleared here. It is the marker the page uses
 * to decide whether a job is in flight, and leaving it behind meant a successful
 * extraction kept reporting itself as still running -- the "Analyzing..." banner
 * survived a finished run for the whole debounce window. The window exists to
 * stop duplicate spawns of work that has not finished; once the work has
 * finished there is nothing left to debounce.
 */
function markReportEntitiesExtracted(PDO $pdo, int $reportId): void
{
    try {
        $stmt = $pdo->prepare(
            'UPDATE reports
             SET entities_extracted_at = NOW(),
                 entities_dictionary_version = ?,
                 entities_extraction_started_at = NULL
             WHERE id = ?'
        );

        $stmt->execute([
            entityDictionaryVersion($pdo),
            $reportId
        ]);

    } catch (Throwable $e) {
        // A missing stamp only costs one redundant extraction next time, so
        // this must never break the page that just finished extracting.
        error_log(
            'Could not stamp extraction for report ' . $reportId . ': ' .
            $e->getMessage()
        );
    }
}


/**
 * ------------------------------------------------------------
 * Decide whether a failure is worth retrying
 * ------------------------------------------------------------
 *
 * A failed extraction splits into two very different kinds, and treating them
 * the same is what produces a report that is stuck at 0% entities with nothing
 * on screen saying why.
 *
 * PERMANENT -- the extractor has already made up its mind about this PDF and
 * would refuse it again no matter how often we asked. HTTP 400 means the file is
 * too large or its text too long; HTTP 422 means the PDF has no readable text
 * (typically a scan rather than an export). Retrying burns a model call per view
 * for ever and never succeeds, so the reason is recorded on the report and shown
 * to the coordinator.
 *
 * TRANSIENT -- the request never got a verdict: the API is restarting, the
 * connection was refused, the read timed out, the model failed mid-inference.
 * HTTP 500 / 503 and a missing HTTP status all land here. These are logged and
 * nothing is recorded, so the next view simply tries again. One bad minute at the
 * API must not permanently mark a perfectly good report as unanalysable.
 */
function entityExtractionClassify(array $result): array
{
    $httpCode = isset($result['http_code']) ? (int) $result['http_code'] : 0;

    $message = trim((string) ($result['error'] ?? ''));

    // entityApiExtractPdf() hands back FastAPI's own wording, which is already
    // written for a human and names the actual limit. Prefer it over anything
    // invented here; fall back only when the API said nothing useful.
    if ($message === '') {
        $message = $httpCode > 0
            ? 'The extraction service returned an error (HTTP ' . $httpCode . ').'
            : 'The extraction service could not be reached.';
    }

    $permanent = in_array($httpCode, [400, 415, 422], true);

    return [
        'permanent' => $permanent,
        'message'   => $permanent
            ? mb_substr($message, 0, 255)
            : mb_substr($message, 0, 255),
    ];
}


/**
 * ------------------------------------------------------------
 * Record a permanent failure against the report
 * ------------------------------------------------------------
 *
 * The reason survives, so the coordinator sees why the entity list is empty
 * instead of assuming there were simply no skills in the report.
 */
function entityExtractionRecordError(PDO $pdo, int $reportId, string $message): void
{
    try {
        $stmt = $pdo->prepare(
            'UPDATE reports
             SET entities_extraction_error = ?
             WHERE id = ?'
        );

        $stmt->execute([mb_substr($message, 0, 255), $reportId]);

    } catch (Throwable $e) {
        error_log(
            'Could not record extraction error for report ' . $reportId . ': ' .
            $e->getMessage()
        );
    }
}


/**
 * ------------------------------------------------------------
 * Clear a previously recorded failure
 * ------------------------------------------------------------
 *
 * Run on success only. A transient blip must not clear a genuine permanent
 * failure, and a permanent failure must not linger once the report is re-uploaded
 * and successfully analysed.
 */
function entityExtractionClearError(PDO $pdo, int $reportId): void
{
    try {
        $stmt = $pdo->prepare(
            'UPDATE reports
             SET entities_extraction_error = NULL
             WHERE id = ? AND entities_extraction_error IS NOT NULL'
        );

        $stmt->execute([$reportId]);

    } catch (Throwable $e) {
        error_log(
            'Could not clear extraction error for report ' . $reportId . ': ' .
            $e->getMessage()
        );
    }
}


/**
 * ------------------------------------------------------------
 * Mark a background job as started
 * ------------------------------------------------------------
 *
 * Doubles as the debounce marker. A view that arrives while a job is in flight
 * sees this and spawns nothing, which is what stops a report that keeps failing
 * from being respawned on every single page view for ever.
 */
function entityExtractionMarkStarted(PDO $pdo, int $reportId): void
{
    try {
        $stmt = $pdo->prepare(
            'UPDATE reports
             SET entities_extraction_started_at = NOW()
             WHERE id = ?'
        );

        $stmt->execute([$reportId]);

    } catch (Throwable $e) {
        error_log(
            'Could not mark extraction start for report ' . $reportId . ': ' .
            $e->getMessage()
        );
    }
}


/**
 * ------------------------------------------------------------
 * Is a background job already in flight for this report?
 * ------------------------------------------------------------
 *
 * Deliberately only concerned with the spawn marker. It must NOT clear a
 * permanent failure, or the retry loop we are avoiding would come straight back.
 */
function entityExtractionInFlight(PDO $pdo, int $reportId): bool
{
    try {
        /*
         * The elapsed time is computed by MySQL, never in PHP.
         *
         * This used to be strtotime($startedAt) against time(), which silently
         * produced a NEGATIVE interval whenever the two disagreed about the
         * clock: MySQL's NOW() writes Asia/Manila while PHP's default timezone
         * here is Europe/Berlin, six hours apart. A negative interval passes a
         * naive "< 900 seconds" test, so the report read as permanently in flight
         * -- the page refused to ever spawn a job again and the "Analyzing"
         * banner never went away.
         *
         * TIMESTAMPDIFF keeps both ends inside MySQL, where the timezone is
         * unambiguous. The >= 0 guard then makes a stale or skewed row expire
         * instead of wedging the report, whatever the cause.
         */
        $stmt = $pdo->prepare(
            'SELECT TIMESTAMPDIFF(SECOND, entities_extraction_started_at, NOW()) AS elapsed
             FROM reports
             WHERE id = ?
             LIMIT 1'
        );

        $stmt->execute([$reportId]);

        $elapsed = $stmt->fetchColumn();

        if ($elapsed === false || $elapsed === null) {
            return false;
        }

        $elapsed = (int) $elapsed;

        return $elapsed >= 0 && $elapsed < EXTRACTION_DEBOUNCE_SECONDS;

    } catch (Throwable $e) {
        // Unknown. Assume nothing is running, so the work still gets attempted.
        error_log(
            'Could not read extraction start marker for report ' . $reportId . ': ' .
            $e->getMessage()
        );

        return false;
    }
}


/**
 * ------------------------------------------------------------
 * Hand a report to the background worker
 * ------------------------------------------------------------
 *
 * Returns true when the worker was launched. False means the caller must run the
 * extraction inline instead -- nothing is lost either way, the work still gets
 * done, it just happens on the request that noticed it.
 *
 * The command is detached on purpose. popen() keeps the child's pipes open until
 * it exits, so a caller that reads the handle or waits on it would sit there for
 * the length of a model run and undo the whole point. Closing the handle
 * immediately and redirecting the child's output to NUL is what makes this fire
 * and forget.
 */
function entityExtractionSpawn(int $reportId): bool
{
    if (!BACKGROUND_JOBS_ENABLED) {
        return false;
    }

    $binary = backgroundPhpBinary();

    if ($binary === null) {
        error_log(
            'Background extraction disabled: no PHP CLI binary found. ' .
            'Set BACKGROUND_PHP_BINARY in .env.'
        );

        return false;
    }

    // Confirm the function we are about to rely on is actually available; hosts
    // do disable it and the failure would otherwise look like a bad command.
    if (!function_exists('popen')) {
        error_log('Background extraction disabled: popen() is unavailable.');

        return false;
    }

    $worker = dirname(__DIR__, 2) . '/src/cli/extract_report.php';

    if (!is_file($worker)) {
        error_log('Background extraction disabled: worker missing at ' . $worker);

        return false;
    }

    /*
     * Windows needs start /B to fully detach, and the empty "" pair is the
     * documented workaround for start's habit of treating a quoted executable
     * path as a window title. The posix branch is the plain form.
     *
     * The worker writes to a log file rather than to NUL. Discarding its output
     * hid every failure: a launch that never started and an extraction that
     * threw both looked identical from the portal's side -- the report simply
     * sat there stamped as "started" and never completed, with nothing anywhere
     * to explain it. popen() returning a handle only means cmd.exe accepted the
     * command, not that the worker ran, so the worker's own output is the only
     * real evidence.
     */
    $logDir = dirname(__DIR__, 2) . '/logs';
    $logFile = $logDir . '/extraction.log';

    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }

    $logRedirect = is_dir($logDir)
        ? (DIRECTORY_SEPARATOR === '\\'
            ? '>> "' . $logFile . '" 2>&1'
            : '>> ' . escapeshellarg($logFile) . ' 2>&1')
        : '>NUL 2>&1';

    if (DIRECTORY_SEPARATOR === '\\') {
        $command = 'start /B "" ' . escapeshellarg($binary) . ' '
            . escapeshellarg($worker) . ' ' . $reportId . ' ' . $logRedirect;
    } else {
        $command = 'nohup ' . escapeshellarg($binary) . ' '
            . escapeshellarg($worker) . ' ' . $reportId
            . ' ' . $logRedirect . ' &';
    }

    $handle = @popen($command, 'r');

    if ($handle === false) {
        error_log('Background extraction: popen() failed for report ' . $reportId);

        return false;
    }

    // Never read the handle: waiting on it would block this request for the
    // length of the extraction.
    pclose($handle);

    return true;
}


/**
 * ------------------------------------------------------------
 * Extraction state for one report, for the polling endpoint
 * ------------------------------------------------------------
 */
function entityExtractionReportStatus(PDO $pdo, int $reportId): array
{
    $status = [
        'report_id' => $reportId,
        'extracted_at' => null,
        'started_at'  => null,
        'error'       => null,
        'pending'     => false,
    ];

    try {
        $stmt = $pdo->prepare(
            'SELECT entities_extracted_at, entities_extraction_started_at,
                    entities_extraction_error,
                    TIMESTAMPDIFF(SECOND, entities_extraction_started_at, NOW()) AS elapsed
             FROM reports
             WHERE id = ?
             LIMIT 1'
        );

        $stmt->execute([$reportId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return $status;
        }

        $status['extracted_at'] = $row['entities_extracted_at'] ?: null;
        $status['started_at']   = $row['entities_extraction_started_at'] ?: null;
        $status['error']        = $row['entities_extraction_error'] ?: null;

        /*
         * Same reasoning as entityExtractionInFlight(): the interval is computed
         * by MySQL so the two clocks cannot disagree. See the comment there for
         * what a negative interval did to the banner.
         */
        $status['pending'] = $row['elapsed'] !== null
            && (int) $row['elapsed'] >= 0
            && (int) $row['elapsed'] < EXTRACTION_DEBOUNCE_SECONDS;

        // A recorded permanent failure is not something a running job will fix,
        // so it wins over the pending flag.
        if ($status['error'] !== null) {
            $status['pending'] = false;
        }

    } catch (Throwable $e) {
        error_log(
            'Could not read extraction status for report ' . $reportId . ': ' .
            $e->getMessage()
        );
    }

    return $status;
}


/**
 * ------------------------------------------------------------
 * Main extraction entry point for a page request
 * ------------------------------------------------------------
 *
 * $async = true runs the work in the background and returns immediately, so the
 * caller renders whatever entities already exist. $async = false keeps the
 * original blocking behaviour.
 *
 * $force always runs inline. ?extract=1 is a deliberate "do it now" action from
 * a member of staff, and the caller reports a truthful success or failure
 * straight away; deferring it would mean that flash message was a lie.
 */
function ensureReportEntitiesExtracted(
    string $pdfPath,
    int $reportId,
    PDO $pdo,
    bool $force = false,
    bool $async = false
): array {

    $skipped = static function (array $extra = []): array {
        return array_merge([
            'success'   => true,
            'skipped'   => true,
            'ran'       => false,
            'deferred'  => false,
            'pending'   => false,
            'error'     => '',
            'entities'  => [],
            'content'   => '',
            'summary'   => [],
            'entity_count' => 0,
        ], $extra);
    };

    if (!reportNeedsEntityExtraction($pdo, $reportId, $force)) {
        return $skipped();
    }

    /*
     * Background path. Deliberately placed after the freshness check so a report
     * that is already done never spawns anything.
     */
    if ($async && !$force) {

        if (entityExtractionInFlight($pdo, $reportId)) {
            return $skipped(['pending' => true]);
        }

        if (entityExtractionSpawn($reportId)) {
            entityExtractionMarkStarted($pdo, $reportId);

            return $skipped(['deferred' => true, 'pending' => true]);
        }

        // Spawning was not possible. Fall through and do the work inline, so the
        // page still ends up with its entities.
    }

    /*
     * The cache above stops a second run on a LATER view, but it cannot stop
     * two views that are already in flight: both read "not extracted yet"
     * before either one stamps the row, so both would call FastAPI.
     *
     * That is not hypothetical. saveExtractedEntities() clears the previous
     * auto-extracted rows with a DELETE and then INSERTs the new ones inside
     * one transaction, so two overlapping runs can each insert a copy of the
     * same term and leave the report showing duplicates.
     *
     * A MySQL named lock serialises the runs per report, and only per report:
     * two students' reports still extract concurrently, which is what we want.
     * It also releases itself if the connection dies, so a crashed worker
     * cannot wedge a report forever.
     */
    $lockName = 'extract_report_' . $reportId;

    if (!entityExtractionLock($pdo, $lockName, 10)) {
        return $skipped([
            'error' => 'Another extraction for this report is already ' .
                       'running. Please try again in a moment.',
        ]);
    }

    try {
        /*
         * Re-check now that the lock is held. Whoever held it may have finished
         * between the first check and here, in which case this run is redundant.
         * $force is passed through so an explicit ?extract=1 is never skipped.
         */
        if (!reportNeedsEntityExtraction($pdo, $reportId, $force)) {
            return $skipped();
        }

        $result = extractEntitiesFromReport($pdfPath, $reportId, $pdo, true);

        $result['skipped']  = false;
        $result['ran']      = !empty($result['success']);
        $result['deferred'] = false;
        $result['pending']  = false;

        if (!empty($result['success'])) {
            markReportEntitiesExtracted($pdo, $reportId);

            // A success supersedes any failure recorded against an earlier
            // version of this report.
            entityExtractionClearError($pdo, $reportId);

        } else {
            $failure = entityExtractionClassify($result);

            error_log(
                'Entity extraction failed for report ' . $reportId .
                ' (' . ($failure['permanent'] ? 'permanent' : 'transient') . '): ' .
                $failure['message']
            );

            if ($failure['permanent']) {
                entityExtractionRecordError($pdo, $reportId, $failure['message']);
            }

            $result['permanent'] = $failure['permanent'];
        }

        return $result;

    } finally {
        entityExtractionUnlock($pdo, $lockName);
    }
}


/**
 * ------------------------------------------------------------
 * Per-report extraction lock
 * ------------------------------------------------------------
 *
 * MySQL named locks are scoped to a single connection and are dropped by the
 * server when that connection closes, which is exactly the lifetime wanted
 * here: the lock lives no longer than the request that took it.
 */
function entityExtractionLock(PDO $pdo, string $name, int $timeoutSeconds): bool
{
    try {
        $stmt = $pdo->prepare('SELECT GET_LOCK(?, ?)');

        if ($stmt->execute([$name, $timeoutSeconds]) === false) {
            return false;
        }

        return (int) $stmt->fetchColumn() === 1;

    } catch (Throwable $e) {
        // Without the lock the worst case is the duplicate-row race this
        // function exists to prevent, so carry on rather than fail the page.
        error_log(
            'Entity extraction lock could not be taken (' . $name . '): ' .
            $e->getMessage()
        );

        return true;
    }
}


function entityExtractionUnlock(PDO $pdo, string $name): void
{
    try {
        $stmt = $pdo->prepare('SELECT RELEASE_LOCK(?)');
        $stmt->execute([$name]);

    } catch (Throwable $e) {
        // Nothing to do. The lock dies with the connection at end of request,
        // so a failed release cannot outlive the page that took it.
        error_log(
            'Entity extraction lock could not be released (' . $name . '): ' .
            $e->getMessage()
        );
    }
}
