<?php

// ============================================================
// supervisor/review_reports.php
// ============================================================

session_start();

require_once __DIR__ . '/../config/db.php';


// ============================================================
// CONFIGURATION
// ============================================================

define(
    'PYTHON_EXEC',
    'python'
);

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

    header(
        "Location: ../auth/login.php"
    );

    exit();
}


$userId = (int)$_SESSION['user_id'];


// ============================================================
// MESSAGE
// ============================================================

$message =
    $_SESSION['review_message']
    ?? '';

unset(
    $_SESSION['review_message']
);


// ============================================================
// 2. GET SUPERVISOR
// ============================================================

$stmtSup = $pdo->prepare(
    "
    SELECT id
    FROM supervisors
    WHERE user_id = ?
    LIMIT 1
    "
);

$stmtSup->execute([
    $userId
]);

$supervisorRecord =
    $stmtSup->fetch(
        PDO::FETCH_ASSOC
    );


if (!$supervisorRecord) {

    die(
        "Supervisor profile not found. "
        . "Please contact administrator."
    );
}


$supervisorId =
    (int)$supervisorRecord['id'];


// Keep supervisor id available to session.
$_SESSION['supervisor_id'] =
    $supervisorId;


// ============================================================
// 3. RESOLVE PDF PATH
// ============================================================

function resolveReportPdfPath(
    $filePath
) {

    if (
        empty($filePath)
    ) {

        return false;
    }


    /*
     * Normalize Windows / Linux separators.
     */

    $filePath = str_replace(
        ['/', '\\'],
        DIRECTORY_SEPARATOR,
        $filePath
    );


    /*
     * Case 1:
     * Already an absolute path.
     */

    if (
        is_file($filePath)
    ) {

        $realPath =
            realpath($filePath);

        if ($realPath !== false) {

            return $realPath;
        }
    }


    /*
     * Project root.
     *
     * review_reports.php is:
     *
     * /ICS-PORTAL/supervisor/
     *
     * therefore:
     *
     * __DIR__ . '/..'
     *
     * points to:
     *
     * /ICS-PORTAL/
     */

    $projectRoot =
        realpath(
            __DIR__ . '/..'
        );


    if (
        $projectRoot !== false
    ) {

        $relativePath =
            ltrim(
                $filePath,
                DIRECTORY_SEPARATOR
            );

        /*
         * If the database contains:
         *
         * uploads/reports/file.pdf
         *
         */

        $candidate =
            $projectRoot
            . DIRECTORY_SEPARATOR
            . $relativePath;


        if (
            is_file($candidate)
        ) {

            return realpath(
                $candidate
            );
        }


        /*
         * If database contains:
         *
         * /ICS-PORTAL/uploads/reports/file.pdf
         *
         * remove the project folder.
         */

        $normalized =
            str_replace(
                '\\',
                '/',
                $relativePath
            );

        $normalized =
            preg_replace(
                '#^ICS-PORTAL/#i',
                '',
                $normalized
            );


        $candidate =
            $projectRoot
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $normalized
            );


        if (
            is_file($candidate)
        ) {

            return realpath(
                $candidate
            );
        }
    }


    /*
     * Case 3:
     * Relative to supervisor directory.
     */

    $candidate =
        __DIR__
        . DIRECTORY_SEPARATOR
        . ltrim(
            $filePath,
            DIRECTORY_SEPARATOR
        );


    if (
        is_file($candidate)
    ) {

        return realpath(
            $candidate
        );
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

    if (
        !is_file($pdfPath)
    ) {

        return [

            'success' =>
                false,

            'error' =>
                'PDF file was not found.',

            'entities' =>
                [],

            'content' =>
                ''
        ];
    }


    /*
     * Resolve Python script.
     */

    $pythonScript =
        realpath(
            PYTHON_SCRIPT
        );


    if (
        $pythonScript === false
    ) {

        return [

            'success' =>
                false,

            'error' =>
                'Python extraction script was not found: '
                . PYTHON_SCRIPT,

            'entities' =>
                [],

            'content' =>
                ''
        ];
    }


    /*
     * Resolve PDF path again.
     */

    $realPdfPath =
        realpath($pdfPath);


    if (
        $realPdfPath === false
    ) {

        return [

            'success' =>
                false,

            'error' =>
                'Unable to resolve PDF path.',

            'entities' =>
                [],

            'content' =>
                ''
        ];
    }


    // ========================================================
    // EXECUTE PYTHON
    // ========================================================

    $command =
        escapeshellarg(
            PYTHON_EXEC
        )
        . ' '
        . escapeshellarg(
            $pythonScript
        )
        . ' '
        . escapeshellarg(
            $realPdfPath
        )
        . ' 2>&1';


    $output =
        shell_exec(
            $command
        );


    if (
        $output === null ||
        trim($output) === ''
    ) {

        return [

            'success' =>
                false,

            'error' =>
                'Python returned no output.',

            'entities' =>
                [],

            'content' =>
                ''
        ];
    }


    // ========================================================
    // DECODE JSON
    // ========================================================

    $data =
        json_decode(
            $output,
            true
        );


    if (
        json_last_error()
        !== JSON_ERROR_NONE
    ) {

        error_log(
            "spaCy JSON error: "
            . json_last_error_msg()
            . " | Output: "
            . $output
        );


        return [

            'success' =>
                false,

            'error' =>
                'Python returned invalid JSON.',

            'entities' =>
                [],

            'content' =>
                '',

            'raw_output' =>
                $output
        ];
    }


    if (
        !is_array($data)
    ) {

        return [

            'success' =>
                false,

            'error' =>
                'Invalid extraction response.',

            'entities' =>
                [],

            'content' =>
                ''
        ];
    }


    if (
        isset($data['error']) &&
        empty($data['success'])
    ) {

        return [

            'success' =>
                false,

            'error' =>
                $data['error'],

            'details' =>
                $data['details']
                ?? '',

            'entities' =>
                [],

            'content' =>
                ''
        ];
    }


    $entities =
        $data['entities']
        ?? [];


    if (
        !is_array($entities)
    ) {

        $entities = [];
    }


    // ========================================================
    // SAVE TO report_entities
    // ========================================================

    try {

        $pdo->beginTransaction();


        // ----------------------------------------------------
        // Remove old extraction for this report
        // ----------------------------------------------------

        $deleteStmt =
            $pdo->prepare(
                "
                DELETE FROM report_entities
                WHERE report_id = ?
                "
            );


        $deleteStmt->execute([
            $reportId
        ]);


        // ----------------------------------------------------
        // Insert new extraction
        //
        // Based on your schema:
        //
        // report_entities
        //   report_id
        //   entity_name
        //   canonical_name
        //   category
        //   activity_type
        //   it_related
        //   source
        //   confidence_score
        // ----------------------------------------------------

        $insertStmt =
            $pdo->prepare(
                "
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
                "
            );


        foreach (
            $entities as $entity
        ) {

            $entityName =
                trim(
                    $entity['entity_name']
                    ??
                    $entity['entity']
                    ??
                    ''
                );


            if (
                $entityName === ''
            ) {

                continue;
            }


            $canonicalName =
                trim(
                    $entity['canonical_name']
                    ??
                    $entityName
                );


            $category =
                trim(
                    $entity['category']
                    ??
                    'Other'
                );


            $activityType =
                trim(
                    $entity['activity_type']
                    ??
                    'Other'
                );


            /*
             * Ensure ENUM compatibility.
             */

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

                $activityType =
                    'Other';
            }


            $itRelated =
                strtolower(
                    trim(
                        $entity['it_related']
                        ??
                        'unknown'
                    )
                );


            /*
             * Ensure ENUM compatibility.
             */

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

                $itRelated =
                    'unknown';
            }


            $source =
                trim(
                    $entity['source']
                    ??
                    'spacy_predefined'
                );


            /*
             * Ensure ENUM compatibility.
             */

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

                $source =
                    'spacy_predefined';
            }


            $confidence =
                isset(
                    $entity['confidence_score']
                )
                    ? (float)$entity['confidence_score']
                    : 100.00;


            if (
                $confidence < 0
            ) {

                $confidence = 0;
            }


            if (
                $confidence > 100
            ) {

                $confidence = 100;
            }


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


    } catch (
        Throwable $e
    ) {

        if (
            $pdo->inTransaction()
        ) {

            $pdo->rollBack();
        }


        error_log(
            "Unable to save extracted entities: "
            . $e->getMessage()
        );


        return [

            'success' =>
                false,

            'error' =>
                'Unable to save extracted entities.',

            'details' =>
                $e->getMessage(),

            'entities' =>
                [],

            'content' =>
                ''
        ];
    }


    // ========================================================
    // RETURN RESULT TO CONTROLLER
    // ========================================================

    return [

        'success' =>
            true,

        'content' =>
            $data['content']
            ?? '',

        'entities' =>
            $entities,

        'summary' =>
            $data['summary']
            ?? [],

        'entity_count' =>
            count($entities)
    ];
}


// ============================================================
// 5. HANDLE SUPERVISOR APPROVAL / REVISION
// ============================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    isset(
        $_POST['action_report_id']
    )
) {

    $reportId =
        (int)$_POST[
            'action_report_id'
        ];


    $newStatus =
        strtolower(
            trim(
                $_POST['status']
                ?? 'pending'
            )
        );


    /*
     * Your reports table uses:
     *
     * pending
     * approved
     * rejected
     *
     * Therefore "Needs Revision" is saved as rejected.
     */

    if (
        $newStatus === 'needs revision'
    ) {

        $newStatus =
            'rejected';
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

        $newStatus =
            'pending';
    }


    $remarks =
        trim(
            $_POST[
                'supervisor_remarks'
            ]
            ?? ''
        );


    $studentName =
        $_POST[
            'student_name'
        ]
        ?? 'Student';


    try {

        /*
         * The existing reports table has
         * ocr_activities, so we keep it.
         *
         * The extracted entities themselves are stored
         * in report_entities.
         */

        $updateStmt =
            $pdo->prepare(
                "
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
                "
            );


        $updateStmt->execute([

            $newStatus,

            $remarks,

            $remarks,

            $reportId,

            $supervisorId
        ]);


        $_SESSION[
            'review_message'
        ] =
            "Report for {$studentName} "
            . "updated successfully.";


        $redirect =
            "review_reports.php";


        if (
            isset(
                $_GET['status']
            )
            &&
            $_GET['status'] !== ''
        ) {

            $redirect .=
                "?status="
                . urlencode(
                    $_GET['status']
                );
        }


        header(
            "Location: "
            . $redirect
        );

        exit();


    } catch (
        Throwable $e
    ) {

        error_log(
            "Error updating report: "
            . $e->getMessage()
        );


        $message =
            "Unable to update report.";
    }
}


// ============================================================
// 6. STATUS FILTER
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

    $filter_status =
        'All';
}


// ============================================================
// 7. BUILD WHERE
// ============================================================

$whereSQL =
    "
    WHERE
        s.supervisor_id = :supervisor_id
    ";


if (
    $filter_status !== 'All'
) {

    if (
        $filter_status === 'Needs Revision'
    ) {

        /*
         * Database ENUM uses rejected.
         */

        $whereSQL .=
            "
            AND LOWER(r.status)
            = 'rejected'
            ";

    } else {

        $whereSQL .=
            "
            AND LOWER(r.status)
            = :status
            ";
    }
}


// ============================================================
// 8. GET REPORTS
// ============================================================

$reportsSql =
    "
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

    {$whereSQL}

    ORDER BY

        CASE
            WHEN LOWER(r.status) = 'pending'
            THEN 0
            ELSE 1
        END,

        r.submitted_at DESC
    ";


$stmtReports =
    $pdo->prepare(
        $reportsSql
    );


$params = [

    'supervisor_id' =>
        $supervisorId
];


if (
    $filter_status !== 'All'
    &&
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
    )
    ?: [];


// ============================================================
// 9. ACTIVE REPORT
// ============================================================

$review_id =
    isset(
        $_GET['review_id']
    )
        ? (int)$_GET['review_id']
        : null;


$activeReport =
    null;


if (
    $review_id
) {

    // ========================================================
    // FIRST SEARCH CURRENT REPORT LIST
    // ========================================================

    foreach (
        $reports as $rep
    ) {

        if (
            (int)$rep['id']
            === $review_id
        ) {

            $activeReport =
                $rep;

            break;
        }
    }


    // ========================================================
    // FETCH DIRECTLY IF FILTER HIDES REPORT
    // ========================================================

    if (
        !$activeReport
    ) {

        $singleStmt =
            $pdo->prepare(
                "
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
                "
            );


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
    // RUN SPACY WHEN REVIEW BUTTON IS CLICKED
    // ========================================================

    if (
        $activeReport
    ) {

        $pdfPath =
            resolveReportPdfPath(
                $activeReport[
                    'file_path'
                ]
            );


        if (
            $pdfPath !== false
        ) {

            /*
             * This is where the complete
             * extraction process starts.
             *
             * Review button:
             *
             * review_id
             *
             * ->
             *
             * resolve PDF
             *
             * ->
             *
             * Python
             *
             * ->
             *
             * spaCy
             *
             * ->
             *
             * predefined_entities
             *
             * ->
             *
             * report_entities
             *
             * ->
             *
             * modal
             */

            $extractionResult =
                extractEntitiesWithSpaCy(
                    $pdfPath,
                    (int)$activeReport['id'],
                    $pdo
                );


            $activeReport[
                'extraction_result'
            ] =
                $extractionResult;


            $activeReport[
                'extracted_entities'
            ] =
                $extractionResult[
                    'entities'
                ]
                ?? [];


            $activeReport[
                'extracted_content'
            ] =
                $extractionResult[
                    'content'
                ]
                ?? '';


            $activeReport[
                'extraction_summary'
            ] =
                $extractionResult[
                    'summary'
                ]
                ?? [];


            /*
             * If extraction failed, expose
             * the error to the view.
             */

            if (
                empty(
                    $extractionResult[
                        'success'
                    ]
                )
            ) {

                $message =
                    "PDF Extraction Error: "
                    .
                    (
                        $extractionResult[
                            'error'
                        ]
                        ??
                        'Unknown extraction error.'
                    );
            }

        } else {

            $activeReport[
                'extraction_result'
            ] = [

                'success' =>
                    false,

                'error' =>
                    'The PDF file could not be located.'
            ];


            $activeReport[
                'extracted_entities'
            ] = [];


            $activeReport[
                'extracted_content'
            ] = '';


            $activeReport[
                'extraction_summary'
            ] = [];


            $message =
                "PDF Extraction Error: "
                . "The PDF file could not be located.";
        }


        // ====================================================
        // LOAD SAVED ENTITIES
        //
        // This is a fallback / verification step.
        //
        // The Python extraction has already inserted the
        // entities into report_entities.
        // ====================================================

        if (
            empty(
                $activeReport[
                    'extracted_entities'
                ]
            )
        ) {

            try {

                $entityStmt =
                    $pdo->prepare(
                        "
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
                        "
                    );


                $entityStmt->execute([
                    $activeReport['id']
                ]);


                $savedEntities =
                    $entityStmt->fetchAll(
                        PDO::FETCH_ASSOC
                    )
                    ?: [];


                $activeReport[
                    'extracted_entities'
                ] =
                    $savedEntities;


            } catch (
                Throwable $e
            ) {

                error_log(
                    "Unable to load report entities: "
                    . $e->getMessage()
                );
            }
        }
    }
}


// ============================================================
// 10. RENDER VIEW
// ============================================================

require_once
    __DIR__
    . '/../src/pages/supervisor/reviewReportsPage.php';