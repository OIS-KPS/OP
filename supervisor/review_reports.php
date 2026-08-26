<?php
// ============================================================
// supervisor/review_reports.php
// ============================================================

session_start();

require_once __DIR__ . '/../config/db.php';

// ============================================================
// CONFIGURATION
// ============================================================

define('PYTHON_EXEC', 'python');

define(
    'PYTHON_SCRIPT',
    __DIR__ . '/../python/extract_entities.py'
);

// ============================================================
// 1. AUTHORIZATION
// ============================================================

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'supervisor'
) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];

// ============================================================
// MESSAGE
// ============================================================

$message = $_SESSION['review_message'] ?? '';

unset($_SESSION['review_message']);

// ============================================================
// 2. GET SUPERVISOR
// ============================================================

$stmtSup = $pdo->prepare("
    SELECT id
    FROM supervisors
    WHERE user_id = ?
    LIMIT 1
");

$stmtSup->execute([$userId]);

$supervisorRecord = $stmtSup->fetch(PDO::FETCH_ASSOC);

if (!$supervisorRecord) {
    die(
        "Supervisor profile not found. " .
        "Please contact administrator."
    );
}

$supervisorId = (int) $supervisorRecord['id'];

$_SESSION['supervisor_id'] = $supervisorId;

// ============================================================
// 3. RESOLVE PDF PATH
// ============================================================

function resolveReportPdfPath($filePath)
{
    if (empty($filePath)) {
        return false;
    }

    // Normalize slashes
    $filePath = str_replace(
        ['/', '\\'],
        DIRECTORY_SEPARATOR,
        $filePath
    );

    // --------------------------------------------------------
    // CASE 1:
    // Database already contains absolute path
    // --------------------------------------------------------

    if (is_file($filePath)) {

        $realPath = realpath($filePath);

        if ($realPath !== false) {
            return $realPath;
        }
    }

    // --------------------------------------------------------
    // PROJECT ROOT
    // --------------------------------------------------------

    $projectRoot = realpath(
        __DIR__ . '/..'
    );

    if ($projectRoot !== false) {

        $relativePath = ltrim(
            $filePath,
            DIRECTORY_SEPARATOR
        );

        // ----------------------------------------------------
        // Example: uploads/reports/example.pdf
        // ----------------------------------------------------

        $candidate =
            $projectRoot .
            DIRECTORY_SEPARATOR .
            $relativePath;

        if (is_file($candidate)) {

            return realpath($candidate);
        }

        // ----------------------------------------------------
        // Example: ICS-PORTAL/uploads/reports/example.pdf
        // ----------------------------------------------------

        $normalized = str_replace(
            '\\',
            '/',
            $relativePath
        );

        $normalized = preg_replace(
            '#^ICS-PORTAL/#i',
            '',
            $normalized
        );

        $candidate =
            $projectRoot .
            DIRECTORY_SEPARATOR .
            str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $normalized
            );

        if (is_file($candidate)) {

            return realpath($candidate);
        }
    }

    // --------------------------------------------------------
    // CASE 3: Relative to supervisor directory
    // --------------------------------------------------------

    $candidate =
        __DIR__ .
        DIRECTORY_SEPARATOR .
        ltrim(
            $filePath,
            DIRECTORY_SEPARATOR
        );

    if (is_file($candidate)) {

        return realpath($candidate);
    }

    return false;
}

// ============================================================
// 4. RUN PYTHON / SPACY
// ============================================================

function extractEntitiesWithSpaCy(
    $pdfPath,
    $reportId,
    PDO $pdo
) {

    // --------------------------------------------------------
    // Validate PDF
    // --------------------------------------------------------

    if (!is_file($pdfPath)) {

        return [
            'success' => false,
            'error' => 'PDF file was not found.',
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    // --------------------------------------------------------
    // Resolve Python script
    // --------------------------------------------------------

    $pythonScript = realpath(PYTHON_SCRIPT);

    if ($pythonScript === false) {

        return [
            'success' => false,
            'error' =>
                'Python extraction script was not found: ' .
                PYTHON_SCRIPT,
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    // --------------------------------------------------------
    // Resolve PDF
    // --------------------------------------------------------

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

    // ========================================================
    // EXECUTE PYTHON
    // ========================================================

    $command =
        escapeshellarg(PYTHON_EXEC) .
        ' ' .
        escapeshellarg($pythonScript) .
        ' ' .
        escapeshellarg($realPdfPath) .
        ' 2>&1';

    $output = shell_exec($command);

    if (
        $output === null ||
        trim($output) === ''
    ) {

        return [
            'success' => false,
            'error' => 'Python returned no output.',
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    // ========================================================
    // DECODE JSON
    // ========================================================

    $data = json_decode(
        $output,
        true
    );

    if (
        json_last_error() !== JSON_ERROR_NONE
    ) {

        error_log(
            "spaCy JSON error: " .
            json_last_error_msg() .
            " | Output: " .
            $output
        );

        return [
            'success' => false,
            'error' =>
                'Python returned invalid JSON.',
            'entities' => [],
            'content' => '',
            'summary' => [],
            'raw_output' => $output
        ];
    }

    if (!is_array($data)) {

        return [
            'success' => false,
            'error' =>
                'Invalid extraction response.',
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    // ========================================================
    // PYTHON ERROR
    // ========================================================

    if (
        isset($data['error']) &&
        empty($data['success'])
    ) {

        return [
            'success' => false,
            'error' => $data['error'],
            'details' => $data['details'] ?? '',
            'entities' => [],
            'content' => $data['content'] ?? '',
            'summary' => $data['summary'] ?? []
        ];
    }

    // ========================================================
    // GET ENTITIES
    // ========================================================

    $entities = $data['entities'] ?? [];

    if (!is_array($entities)) {
        $entities = [];
    }

    // ========================================================
    // SAVE EXTRACTED ENTITIES
    // ========================================================

    try {

        $pdo->beginTransaction();

        // ----------------------------------------------------
        // Remove previous extraction
        // ----------------------------------------------------

        $deleteStmt = $pdo->prepare("
            DELETE FROM report_entities
            WHERE report_id = ?
        ");

        $deleteStmt->execute([
            $reportId
        ]);

        // ----------------------------------------------------
        // Insert extracted entities
        // ----------------------------------------------------

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

            // ------------------------------------------------
            // Entity name
            // ------------------------------------------------

            $entityName = trim(
                $entity['entity_name']
                ??
                $entity['entity']
                ??
                $entity['text']
                ??
                ''
            );

            if ($entityName === '') {
                continue;
            }

            // ------------------------------------------------
            // Canonical name
            // ------------------------------------------------

            $canonicalName = trim(
                $entity['canonical_name']
                ??
                $entityName
            );

            if ($canonicalName === '') {
                $canonicalName = $entityName;
            }

            // ------------------------------------------------
            // Category
            // ------------------------------------------------

            $category = trim(
                $entity['category']
                ??
                'Other'
            );

            if ($category === '') {
                $category = 'Other';
            }

            // ------------------------------------------------
            // Activity type
            // ------------------------------------------------

            $activityType = trim(
                $entity['activity_type']
                ??
                'Other'
            );

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

            // ------------------------------------------------
            // IT related
            // ------------------------------------------------

            $itRelated = strtolower(
                trim(
                    $entity['it_related']
                    ??
                    'unknown'
                )
            );

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

            // ------------------------------------------------
            // Source
            // ------------------------------------------------

            $source = trim(
                $entity['source']
                ??
                'spacy_predefined'
            );

            if (
                !in_array(
                    $source,
                    [
                        'spacy',
                        'predefined',
                        'spacy_predefined'
                    ],
                    true
                )
            ) {
                $source = 'spacy_predefined';
            }

            // ------------------------------------------------
            // Confidence
            // ------------------------------------------------

            $confidence = isset(
                $entity['confidence_score']
            )
                ? (float) $entity['confidence_score']
                : 100.00;

            if ($confidence < 0) {
                $confidence = 0;
            }

            if ($confidence > 100) {
                $confidence = 100;
            }

            // ------------------------------------------------
            // Insert
            // ------------------------------------------------

            $insertStmt->execute([
                $reportId,
                $entityName,
                $canonicalName,
                $category,
                $activityType,
                $itRelated,
                $source,
                $confidence
            ]);
        }

        $pdo->commit();

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log(
            "Unable to save extracted entities: " .
            $e->getMessage()
        );

        return [
            'success' => false,
            'error' =>
                'Unable to save extracted entities.',
            'details' =>
                $e->getMessage(),
            'entities' => [],
            'content' => '',
            'summary' => []
        ];
    }

    // ========================================================
    // RETURN EXTRACTION RESULT
    // ========================================================

    return [
        'success' => true,
        'content' =>
            $data['content'] ?? '',
        'entities' =>
            $entities,
        'summary' =>
            $data['summary'] ?? [],
        'entity_count' =>
            count($entities)
    ];
}

// ============================================================
// 5. HANDLE SUPERVISOR APPROVAL / REVISION
// ============================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action_report_id'])
) {

    $reportId = (int) $_POST[
        'action_report_id'
    ];

    $newStatus = strtolower(
        trim(
            $_POST['status']
            ?? 'pending'
        )
    );

    // --------------------------------------------------------
    // Needs Revision -> rejected
    // --------------------------------------------------------

    if (
        $newStatus === 'needs revision'
    ) {

        $newStatus = 'rejected';
    }

    // --------------------------------------------------------
    // Validate status
    // --------------------------------------------------------

    if (
        !in_array(
            $newStatus,
            [
                'pending',
                'approved',
                'rejected'
            ],
            true
        )
    ) {

        $newStatus = 'pending';
    }

    $remarks = trim(
        $_POST['supervisor_remarks']
        ?? ''
    );

    $studentName =
        $_POST['student_name']
        ?? 'Student';

    try {

        // ----------------------------------------------------
        // Make sure report belongs to supervisor
        // ----------------------------------------------------

        $updateStmt = $pdo->prepare("
            UPDATE reports r

            JOIN students s
                ON r.student_id = s.id

            SET
                r.status = ?,

                r.ocr_activities =
                    CASE
                        WHEN ? <> ''
                        THEN ?
                        ELSE r.ocr_activities
                    END

            WHERE
                r.id = ?

                AND
                s.supervisor_id = ?
        ");

        $updateStmt->execute([
            $newStatus,
            $remarks,
            $remarks,
            $reportId,
            $supervisorId
        ]);

        // ----------------------------------------------------
        // Check whether report actually belongs to supervisor
        // ----------------------------------------------------

        if ($updateStmt->rowCount() === 0) {

            // The status may already have the same value,
            // so verify ownership separately.

            $verifyStmt = $pdo->prepare("
                SELECT r.id
                FROM reports r

                JOIN students s
                    ON r.student_id = s.id

                WHERE
                    r.id = ?
                    AND
                    s.supervisor_id = ?

                LIMIT 1
            ");

            $verifyStmt->execute([
                $reportId,
                $supervisorId
            ]);

            if (!$verifyStmt->fetch()) {

                throw new Exception(
                    'Report does not belong to this supervisor.'
                );
            }
        }

        $_SESSION['review_message'] =
            "Report for {$studentName} " .
            "updated successfully.";

        $redirect =
            "review_reports.php";

        if (
            isset($_GET['status']) &&
            $_GET['status'] !== ''
        ) {

            $redirect .=
                "?status=" .
                urlencode($_GET['status']);
        }

        header(
            "Location: " .
            $redirect
        );

        exit();

    } catch (Throwable $e) {

        error_log(
            "Error updating report: " .
            $e->getMessage()
        );

        $message =
            "Unable to update report.";
    }
}

// ============================================================
// 6. STATUS FILTER
// ============================================================

$filter_status =
    $_GET['status'] ?? 'All';

$validStatuses = [
    'All',
    'Pending',
    'Approved',
    'Needs Revision'
];

if (
    !in_array(
        $filter_status,
        $validStatuses,
        true
    )
) {

    $filter_status = 'All';
}

// ============================================================
// 7. BUILD WHERE
// ============================================================

$whereSQL = "
    WHERE
        s.supervisor_id = :supervisor_id
";

if ($filter_status !== 'All') {

    if (
        $filter_status === 'Needs Revision'
    ) {

        $whereSQL .= "
            AND LOWER(r.status) = 'rejected'
        ";

    } else {

        $whereSQL .= "
            AND LOWER(r.status) = :status
        ";
    }
}

// ============================================================
// 8. GET REPORTS
// ============================================================

$reportsSql = "
    SELECT
        r.id,
        r.student_id,
        r.week_number,
        r.file_path,

        r.file_path AS attachment_path,

        r.ocr_activities,

        r.ocr_activities AS remarks,

        r.status,

        r.submitted_at,

        r.submitted_at AS created_at,

        u.name AS student_name,

        u.avatar_url AS student_avatar,

        s.student_number,

        s.program

    FROM reports r

    JOIN students s
        ON r.student_id = s.id

    JOIN users u
        ON s.user_id = u.id

    {$whereSQL}

    ORDER BY

        CASE
            WHEN LOWER(r.status) = 'pending'
            THEN 0
            ELSE 1
        END,

        r.submitted_at DESC
";

$stmtReports = $pdo->prepare(
    $reportsSql
);

$params = [
    'supervisor_id' =>
        $supervisorId
];

if (
    $filter_status !== 'All' &&
    $filter_status !== 'Needs Revision'
) {

    $params['status'] =
        strtolower($filter_status);
}

$stmtReports->execute(
    $params
);

$reports =
    $stmtReports->fetchAll(
        PDO::FETCH_ASSOC
    ) ?: [];

// ============================================================
// 9. ACTIVE REPORT
// ============================================================

$review_id =
    isset($_GET['review_id'])
        ? (int) $_GET['review_id']
        : null;

$activeReport = null;

// ============================================================
// ONLY EXTRACT WHEN review_id EXISTS
// ============================================================

if ($review_id) {

    // ========================================================
    // FIND REPORT
    // ========================================================

    foreach ($reports as $rep) {

        if (
            (int) $rep['id'] ===
            $review_id
        ) {

            $activeReport = $rep;

            break;
        }
    }

    // ========================================================
    // IF FILTER HIDES REPORT
    // FETCH DIRECTLY
    // ========================================================

    if (!$activeReport) {

        $singleStmt = $pdo->prepare("
            SELECT
                r.id,
                r.student_id,
                r.week_number,
                r.file_path,

                r.file_path
                    AS attachment_path,

                r.ocr_activities,

                r.ocr_activities
                    AS remarks,

                r.status,

                r.submitted_at,

                r.submitted_at
                    AS created_at,

                u.name
                    AS student_name,

                u.avatar_url
                    AS student_avatar,

                s.student_number,

                s.program

            FROM reports r

            JOIN students s
                ON r.student_id = s.id

            JOIN users u
                ON s.user_id = u.id

            WHERE
                r.id = ?

                AND
                s.supervisor_id = ?

            LIMIT 1
        ");

        $singleStmt->execute([
            $review_id,
            $supervisorId
        ]);

        $activeReport =
            $singleStmt->fetch(
                PDO::FETCH_ASSOC
            );
    }

    // ========================================================
    // REPORT FOUND
    // ========================================================

    if ($activeReport) {

        // ----------------------------------------------------
        // Initialize extraction values
        // ----------------------------------------------------

        $activeReport[
            'extracted_entities'
        ] = [];

        $activeReport[
            'extracted_content'
        ] = '';

        $activeReport[
            'extraction_summary'
        ] = [];

        $activeReport[
            'extraction_result'
        ] = [
            'success' => false,
            'entities' => [],
            'content' => '',
            'summary' => []
        ];

        // ====================================================
        // RESOLVE PDF
        // ====================================================

        $pdfPath =
            resolveReportPdfPath(
                $activeReport['file_path']
            );

        if ($pdfPath === false) {

            $message =
                "PDF Extraction Error: " .
                "The PDF file could not be located.";

            $activeReport[
                'extraction_result'
            ] = [
                'success' => false,
                'error' =>
                    'The PDF file could not be located.',
                'entities' => [],
                'content' => '',
                'summary' => []
            ];

        } else {

            // =================================================
            // RUN PYTHON
            // =================================================

            $extractionResult =
                extractEntitiesWithSpaCy(
                    $pdfPath,
                    (int) $activeReport['id'],
                    $pdo
                );

            // -------------------------------------------------
            // Store result for page
            // -------------------------------------------------

            $activeReport[
                'extraction_result'
            ] = $extractionResult;

            $activeReport[
                'extracted_entities'
            ] =
                $extractionResult[
                    'entities'
                ] ?? [];

            $activeReport[
                'extracted_content'
            ] =
                $extractionResult[
                    'content'
                ] ?? '';

            $activeReport[
                'extraction_summary'
            ] =
                $extractionResult[
                    'summary'
                ] ?? [];

            // -------------------------------------------------
            // Extraction failed
            // -------------------------------------------------

            if (
                empty(
                    $extractionResult['success']
                )
            ) {

                $message =
                    "PDF Extraction Error: " .
                    (
                        $extractionResult['error']
                        ??
                        'Unknown extraction error.'
                    );

                // Include details in PHP log
                if (
                    !empty(
                        $extractionResult['details']
                    )
                ) {

                    error_log(
                        "Extraction details: " .
                        $extractionResult['details']
                    );
                }
            }
        }

        // ====================================================
        // LOAD SAVED ENTITIES
        //
        // This guarantees that the modal receives the
        // entities actually stored in report_entities.
        // ====================================================

        try {

            $entityStmt = $pdo->prepare("
                SELECT
                    id,
                    report_id,
                    entity_name,
                    canonical_name,
                    category,
                    activity_type,
                    it_related,
                    source,
                    confidence_score

                FROM report_entities

                WHERE report_id = ?

                ORDER BY
                    entity_name ASC
            ");

            $entityStmt->execute([
                $activeReport['id']
            ]);

            $savedEntities =
                $entityStmt->fetchAll(
                    PDO::FETCH_ASSOC
                ) ?: [];

            // ------------------------------------------------
            // Always use saved entities if available
            // ------------------------------------------------

            if (
                !empty($savedEntities)
            ) {

                $activeReport[
                    'extracted_entities'
                ] = $savedEntities;
            }

        } catch (Throwable $e) {

            error_log(
                "Unable to load report entities: " .
                $e->getMessage()
            );
        }

        // ====================================================
        // ENTITY ANALYTICS
        // ====================================================

        $entityCount =
            count(
                $activeReport[
                    'extracted_entities'
                ]
            );

        $softwareCount = 0;
        $hardwareCount = 0;
        $clericalCount = 0;
        $otherCount = 0;

        $itRelatedCount = 0;
        $notItRelatedCount = 0;

        foreach (
            $activeReport[
                'extracted_entities'
            ] as $entity
        ) {

            $activityType =
                strtolower(
                    trim(
                        $entity['activity_type']
                        ?? 'other'
                    )
                );

            switch ($activityType) {

                case 'software':
                    $softwareCount++;
                    break;

                case 'hardware':
                    $hardwareCount++;
                    break;

                case 'clerical':
                    $clericalCount++;
                    break;

                default:
                    $otherCount++;
                    break;
            }

            $itRelated =
                strtolower(
                    trim(
                        $entity['it_related']
                        ?? 'unknown'
                    )
                );

            if ($itRelated === 'yes') {
                $itRelatedCount++;
            }

            if ($itRelated === 'no') {
                $notItRelatedCount++;
            }
        }

        // ----------------------------------------------------
        // Calculate percentages
        // ----------------------------------------------------

        $softwarePercentage = 0;
        $hardwarePercentage = 0;
        $clericalPercentage = 0;
        $otherPercentage = 0;
        $itRelatedPercentage = 0;
        $notItRelatedPercentage = 0;

        if ($entityCount > 0) {

            $softwarePercentage =
                round(
                    ($softwareCount / $entityCount) * 100,
                    2
                );

            $hardwarePercentage =
                round(
                    ($hardwareCount / $entityCount) * 100,
                    2
                );

            $clericalPercentage =
                round(
                    ($clericalCount / $entityCount) * 100,
                    2
                );

            $otherPercentage =
                round(
                    ($otherCount / $entityCount) * 100,
                    2
                );

            $itRelatedPercentage =
                round(
                    ($itRelatedCount / $entityCount) * 100,
                    2
                );

            $notItRelatedPercentage =
                round(
                    ($notItRelatedCount / $entityCount) * 100,
                    2
                );
        }

        // ====================================================
        // MAKE ANALYTICS AVAILABLE TO VIEW
        // ====================================================

        $activeReport[
            'entity_analytics'
        ] = [

            'total' =>
                $entityCount,

            'software' => [
                'count' =>
                    $softwareCount,
                'percentage' =>
                    $softwarePercentage
            ],

            'hardware' => [
                'count' =>
                    $hardwareCount,
                'percentage' =>
                    $hardwarePercentage
            ],

            'clerical' => [
                'count' =>
                    $clericalCount,
                'percentage' =>
                    $clericalPercentage
            ],

            'other' => [
                'count' =>
                    $otherCount,
                'percentage' =>
                    $otherPercentage
            ],

            'it_related' => [
                'count' =>
                    $itRelatedCount,
                'percentage' =>
                    $itRelatedPercentage
            ],

            'not_it_related' => [
                'count' =>
                    $notItRelatedCount,
                'percentage' =>
                    $notItRelatedPercentage
            ]
        ];
    }
}

// ============================================================
// 10. RENDER VIEW
// ============================================================

require_once
    __DIR__ .
    '/../src/pages/supervisor/reviewReportsPage.php';