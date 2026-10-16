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
 *   - the caller explicitly forces it (the coordinator's Re-extract action), or
 *   - the Entity Dictionary has changed since that extraction ran
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
 */
function markReportEntitiesExtracted(PDO $pdo, int $reportId): void
{
    try {
        $stmt = $pdo->prepare(
            'UPDATE reports
             SET entities_extracted_at = NOW(),
                 entities_dictionary_version = ?
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
 * Extract for a report only if it is actually needed
 * ------------------------------------------------------------
 *
 * Drop-in replacement for calling extractEntitiesFromReport() directly from a
 * page. Returns the same shape as extractEntitiesFromReport(), plus:
 *
 *   'skipped' => true  when the stored result was reused unchanged
 *
 * The caller should rebuild its workspace whenever 'ran' is true, since only
 * then can the stored rows have changed.
 */
function ensureReportEntitiesExtracted(
    string $pdfPath,
    int $reportId,
    PDO $pdo,
    bool $force = false
): array {

    if (!reportNeedsEntityExtraction($pdo, $reportId, $force)) {
        return [
            'success'   => true,
            'skipped'   => true,
            'ran'       => false,
            'error'     => '',
            'entities'  => [],
            'content'   => '',
            'summary'   => [],
            'entity_count' => 0
        ];
    }

    $result = extractEntitiesFromReport($pdfPath, $reportId, $pdo, true);

    $result['skipped'] = false;
    $result['ran'] = !empty($result['success']);

    if (!empty($result['success'])) {
        markReportEntitiesExtracted($pdo, $reportId);
    }

    return $result;
}
