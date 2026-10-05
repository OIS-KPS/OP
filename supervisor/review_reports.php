<?php

// ============================================================
// supervisor/review_reports.php
// ============================================================

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/entity_extraction.php';


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
// 2. MESSAGE
// ============================================================

$message = $_SESSION['review_message'] ?? '';

unset($_SESSION['review_message']);


// ============================================================
// 3. GET SUPERVISOR
// ============================================================

$stmtSup = $pdo->prepare("
    SELECT id
    FROM supervisors
    WHERE user_id = ?
    LIMIT 1
");

$stmtSup->execute([
    $userId
]);

$supervisorRecord = $stmtSup->fetch(
    PDO::FETCH_ASSOC
);

if (!$supervisorRecord) {
    die(
        "Supervisor profile not found. " .
        "Please contact administrator."
    );
}

$supervisorId = (int) $supervisorRecord['id'];

$_SESSION['supervisor_id'] = $supervisorId;


// ============================================================
// 4. RESOLVE PDF PATH
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
    // CASE 1: Database already contains absolute path
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

        $fileName = basename(
            $filePath
        );

        // ----------------------------------------------------
        // Check inside uploads/reports/
        // ----------------------------------------------------

        $candidateReports =
            $projectRoot .
            DIRECTORY_SEPARATOR .
            'uploads' .
            DIRECTORY_SEPARATOR .
            'reports' .
            DIRECTORY_SEPARATOR .
            $fileName;

        if (is_file($candidateReports)) {

            return realpath(
                $candidateReports
            );
        }

        // ----------------------------------------------------
        // Example:
        // uploads/example.pdf
        // ----------------------------------------------------

        $candidate =
            $projectRoot .
            DIRECTORY_SEPARATOR .
            $relativePath;

        if (is_file($candidate)) {

            return realpath(
                $candidate
            );
        }

        // ----------------------------------------------------
        // Example:
        // ICS-PORTAL/uploads/reports/example.pdf
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

            return realpath(
                $candidate
            );
        }
    }

    // --------------------------------------------------------
    // CASE 3:
    // Relative to supervisor directory
    // --------------------------------------------------------

    $candidate =
        __DIR__ .
        DIRECTORY_SEPARATOR .
        ltrim(
            $filePath,
            DIRECTORY_SEPARATOR
        );

    if (is_file($candidate)) {

        return realpath(
            $candidate
        );
    }

    return false;
}


// ============================================================
// 5. FASTAPI ENTITY EXTRACTION
// ============================================================

function extractEntitiesWithFastAPI(
    string $pdfPath,
    int $reportId,
    PDO $pdo
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

    try {

        /*
         * entity_extraction.php handles:
         *
         * PDF
         * ↓
         * FastAPI /extract-pdf
         * ↓
         * trained spaCy model
         * ↓
         * normalized entities
         * ↓
         * report_entities
         */

        return extractEntitiesFromReport(
            $pdfPath,
            $reportId,
            $pdo,
            true
        );

    } catch (Throwable $e) {

        error_log(
            "FastAPI entity extraction error: " .
            $e->getMessage()
        );

        return [
            'success' => false,
            'error' => 'Entity extraction failed.',
            'details' => $e->getMessage(),
            'entities' => [],
            'content' => '',
            'summary' => [],
            'entity_count' => 0
        ];
    }
}


// ============================================================
// 6. HANDLE SUPERVISOR APPROVAL / REVISION
// ============================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action_report_id'])
) {

    $reportId = (int) $_POST['action_report_id'];

    $newStatus = strtolower(
        trim(
            $_POST['status'] ?? 'pending'
        )
    );

    // --------------------------------------------------------
    // Needs Revision -> rejected
    // --------------------------------------------------------

    if ($newStatus === 'needs revision') {
        $newStatus = 'rejected';
    }

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
        $_POST['supervisor_remarks'] ?? ''
    );

    $studentName =
        $_POST['student_name']
        ?? 'Student';

    try {

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
                    END,

                r.approved_at =
                    CASE
                        WHEN ? = 'approved'
                        THEN NOW()
                        ELSE r.approved_at
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
            $newStatus,
            $reportId,
            $supervisorId
        ]);

        // ----------------------------------------------------
        // Verify report ownership if nothing changed
        // ----------------------------------------------------

        if ($updateStmt->rowCount() === 0) {

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
            "Report for {$studentName} updated successfully.";

        $redirect = "review_reports.php";

        if (
            isset($_GET['status']) &&
            $_GET['status'] !== ''
        ) {

            $redirect .=
                "?status=" .
                urlencode(
                    $_GET['status']
                );
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
// 7. STATUS FILTER
// ============================================================

$filter_status =
    $_GET['status']
    ?? 'All';

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
// 8. BUILD WHERE
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
// 9. GET REPORTS
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

        r.approved_at,

        u.name AS student_name,

        u.avatar_url AS student_avatar,

        s.student_number,

        s.program,

        COALESCE(
            s.section,
            'A'
        ) AS section

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
    'supervisor_id' => $supervisorId
];

if (
    $filter_status !== 'All' &&
    $filter_status !== 'Needs Revision'
) {

    $params['status'] =
        strtolower(
            $filter_status
        );
}

$stmtReports->execute(
    $params
);

$reports =
    $stmtReports->fetchAll(
        PDO::FETCH_ASSOC
    ) ?: [];


// ============================================================
// 10. ACTIVE REPORT
// ============================================================

$review_id =
    isset($_GET['review_id'])
        ? (int) $_GET['review_id']
        : null;

$activeReport = null;


if ($review_id) {

    // --------------------------------------------------------
    // Find report from already-loaded reports
    // --------------------------------------------------------

    foreach (
        $reports
        as $rep
    ) {

        if (
            (int) $rep['id']
            === $review_id
        ) {

            $activeReport = $rep;

            break;
        }
    }


    // --------------------------------------------------------
    // If not found because of filter,
    // load directly and verify ownership
    // --------------------------------------------------------

    if (!$activeReport) {

        $singleStmt = $pdo->prepare("

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

                r.approved_at,

                u.name AS student_name,

                u.avatar_url AS student_avatar,

                s.student_number,

                s.program,

                COALESCE(
                    s.section,
                    'A'
                ) AS section

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
    // PROCESS ACTIVE REPORT
    // ========================================================

    if ($activeReport) {

        // ----------------------------------------------------
        // Default extraction values
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


        // ----------------------------------------------------
        // Resolve PDF physical location
        // ----------------------------------------------------

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
            // CHECK SAVED ENTITIES
            // =================================================

            $checkSavedStmt =
                $pdo->prepare("

                    SELECT *

                    FROM report_entities

                    WHERE report_id = ?

                    ORDER BY entity_name ASC
                ");

            $checkSavedStmt->execute([
                $activeReport['id']
            ]);

            $existingSaved =
                $checkSavedStmt->fetchAll(
                    PDO::FETCH_ASSOC
                ) ?: [];


            // =================================================
            // USE SAVED ENTITIES
            // =================================================

            if (!empty($existingSaved)) {

                $activeReport[
                    'extracted_entities'
                ] = $existingSaved;

                $activeReport[
                    'extraction_result'
                ] = [

                    'success' => true,

                    'entities' => $existingSaved,

                    'content' => '',

                    'summary' => []

                ];

            } else {

                // =============================================
                // RUN FASTAPI EXTRACTION
                // =============================================

                $extractionResult =
                    extractEntitiesWithFastAPI(
                        $pdfPath,
                        (int) $activeReport['id'],
                        $pdo
                    );


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


                // ---------------------------------------------
                // Extraction error
                // ---------------------------------------------

                if (
                    empty(
                        $extractionResult[
                            'success'
                        ]
                    )
                ) {

                    $message =
                        "PDF Extraction Error: " .
                        (
                            $extractionResult[
                                'error'
                            ]
                            ??
                            'Unknown extraction error.'
                        );

                    if (
                        !empty(
                            $extractionResult[
                                'details'
                            ]
                        )
                    ) {

                        error_log(
                            "Extraction details: " .
                            $extractionResult[
                                'details'
                            ]
                        );
                    }
                }
            }
        }


        // ====================================================
        // LOAD SAVED ENTITIES
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

                ORDER BY entity_name ASC
            ");

            $entityStmt->execute([
                $activeReport['id']
            ]);

            $savedEntities =
                $entityStmt->fetchAll(
                    PDO::FETCH_ASSOC
                ) ?: [];


            if (!empty($savedEntities)) {

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
                        $entity[
                            'activity_type'
                        ] ?? 'other'
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
                        $entity[
                            'it_related'
                        ] ?? 'unknown'
                    )
                );


            if (
                $itRelated === 'yes'
            ) {

                $itRelatedCount++;
            }


            if (
                $itRelated === 'no'
            ) {

                $notItRelatedCount++;
            }
        }


        // ====================================================
        // CALCULATE PERCENTAGES
        // ====================================================

        $softwarePercentage = 0;
        $hardwarePercentage = 0;
        $clericalPercentage = 0;
        $otherPercentage = 0;

        $itRelatedPercentage = 0;
        $notItRelatedPercentage = 0;


        if ($entityCount > 0) {

            $softwarePercentage =
                round(
                    (
                        $softwareCount /
                        $entityCount
                    ) * 100,
                    2
                );


            $hardwarePercentage =
                round(
                    (
                        $hardwareCount /
                        $entityCount
                    ) * 100,
                    2
                );


            $clericalPercentage =
                round(
                    (
                        $clericalCount /
                        $entityCount
                    ) * 100,
                    2
                );


            $otherPercentage =
                round(
                    (
                        $otherCount /
                        $entityCount
                    ) * 100,
                    2
                );


            $itRelatedPercentage =
                round(
                    (
                        $itRelatedCount /
                        $entityCount
                    ) * 100,
                    2
                );


            $notItRelatedPercentage =
                round(
                    (
                        $notItRelatedCount /
                        $entityCount
                    ) * 100,
                    2
                );
        }


        // ====================================================
        // STORE ENTITY ANALYTICS
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
// 11. RENDER VIEW
// ============================================================

require_once
    __DIR__ .
    '/../src/pages/supervisor/reviewReportsPage.php';