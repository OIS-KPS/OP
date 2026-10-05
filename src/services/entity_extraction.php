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
    $activityType = trim(
        (string) (
            $entity['activity_type']
            ?? ''
        )
    );

    if ($activityType === '') {

        $categoryUpper = strtoupper($category);

        if (
            str_contains($categoryUpper, 'HARDWARE')
        ) {
            $activityType = 'Hardware';

        } elseif (
            str_contains($categoryUpper, 'CLERICAL')
        ) {
            $activityType = 'Clerical';

        } elseif (
            str_contains($categoryUpper, 'IT') ||
            str_contains($categoryUpper, 'SOFTWARE')
        ) {
            $activityType = 'Software';

        } else {
            $activityType = 'Other';
        }
    }

    if (
        !in_array(
            $activityType,
            [
                'Software',
                'Hardware',
                'Clerical',
                'Other'
            ],
            true
        )
    ) {
        $activityType = 'Other';
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

    if ($itRelated === '') {

        $categoryUpper = strtoupper($category);

        if (
            str_contains($categoryUpper, 'CLERICAL')
        ) {
            $itRelated = 'no';

        } elseif (
            str_contains($categoryUpper, 'IT') ||
            str_contains($categoryUpper, 'SOFTWARE') ||
            str_contains($categoryUpper, 'HARDWARE')
        ) {
            $itRelated = 'yes';

        } else {
            $itRelated = 'unknown';
        }
    }

    if (
        !in_array(
            $itRelated,
            [
                'yes',
                'no',
                'unknown'
            ],
            true
        )
    ) {
        $itRelated = 'unknown';
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

        /**
         * Remove old automatically extracted results.
         *
         * Current report flow treats the extraction result
         * as the current result for the report.
         */
        $deleteStmt = $pdo->prepare("
            DELETE FROM report_entities
            WHERE report_id = ?
        ");

        $deleteStmt->execute([
            $reportId
        ]);

        $insertStmt = $pdo->prepare("
            INSERT INTO report_entities
            (
                report_id,
                entity_name,
                canonical_name,
                category,
                activity_type,
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

            $insertStmt->execute([
                $reportId,
                $normalized['entity_name'],
                $normalized['canonical_name'],
                $normalized['category'],
                $normalized['activity_type'],
                $normalized['it_related'],
                $normalized['source'],
                $normalized['confidence_score']
            ]);
        }

        $pdo->commit();

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