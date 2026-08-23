<?php

/*
 * ============================================================
 * src/pages/supervisor/reviewReportsPage.php
 * ============================================================
 *
 * Supervisor Review Reports VIEW
 *
 * IMPORTANT:
 *
 * This file is loaded by:
 *
 * supervisor/review_reports.php
 *
 * Therefore:
 *
 * - DO NOT call session_start() here.
 * - DO NOT require db.php here.
 * - DO NOT execute database queries here.
 *
 * Controller provides:
 *
 * $filter_status
 * $message
 * $reports
 * $activeReport
 *
 * ============================================================
 */


/* ============================================================
   SAFE DEFAULT VALUES
   ============================================================ */

$filter_status =
    $filter_status
    ?? 'All';

$message =
    $message
    ?? '';

$reports =
    $reports
    ?? [];

$activeReport =
    $activeReport
    ?? null;


/* ============================================================
   HELPER FUNCTIONS
   ============================================================ */

if (!function_exists('e')) {

    function e($value)
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}


/* ============================================================
   STATUS URL
   ============================================================ */

if (!function_exists('statusUrl')) {

    function statusUrl(
        $status = null,
        $reviewId = null
    ) {

        $params = [];


        if (
            $reviewId !== null
        ) {

            $params['review_id'] =
                (int)$reviewId;
        }


        if (
            $status !== null
            &&
            $status !== 'All'
        ) {

            $params['status'] =
                $status;
        }


        if (
            empty($params)
        ) {

            return 'review_reports.php';
        }


        return
            'review_reports.php?'
            .
            http_build_query(
                $params
            );
    }
}


/* ============================================================
   NORMALIZE STATUS
   ============================================================ */

if (
    !function_exists(
        'normalizeReportStatus'
    )
) {

    function normalizeReportStatus(
        $status
    ) {

        $status =
            trim(
                (string)$status
            );


        if (
            $status === ''
        ) {

            return 'pending';
        }


        $lower =
            strtolower(
                $status
            );


        if (
            $lower === 'approved'
        ) {

            return 'approved';
        }


        if (
            $lower === 'pending'
        ) {

            return 'pending';
        }


        if (
            $lower === 'needs revision'
            ||
            $lower === 'needs_revision'
            ||
            $lower === 'revision'
            ||
            $lower === 'rejected'
        ) {

            return 'needs revision';
        }


        return $lower;
    }
}


/* ============================================================
   IT RELATED CHECK
   ============================================================ */

if (
    !function_exists(
        'isITRelated'
    )
) {

    function isITRelated(
        $value
    ) {

        $value =
            strtolower(
                trim(
                    (string)$value
                )
            );


        return in_array(

            $value,

            [
                'yes',
                'true',
                '1',
                'it',
                'it related',
                'it-related'
            ],

            true
        );
    }
}


/* ============================================================
   NORMALIZE FILTER
   ============================================================ */

$allowedFilters = [

    'All',

    'Pending',

    'Approved',

    'Needs Revision'
];


if (
    !in_array(
        $filter_status,
        $allowedFilters,
        true
    )
) {

    $filter_status =
        'All';
}


/* ============================================================
   ACTIVE REPORT DATA
   ============================================================ */

$hasActiveReport =
    !empty($activeReport)
    &&
    is_array($activeReport);


$activeStatus =
    'pending';


$isApproved =
    false;


$isRevision =
    false;


$activeFilePath =
    '';


$activeSubmittedAt =
    null;


$activeRemarks =
    '';


$extractedEntities =
    [];


$extractionSummary =
    [];


if (
    $hasActiveReport
) {

    $activeStatus =
        normalizeReportStatus(
            $activeReport[
                'status'
            ]
            ?? 'pending'
        );


    $isApproved =
        (
            $activeStatus
            ===
            'approved'
        );


    $isRevision =
        (
            $activeStatus
            ===
            'needs revision'
        );


    $activeFilePath =
        $activeReport[
            'file_path'
        ]
        ??
        $activeReport[
            'attachment_path'
        ]
        ??
        '';


    $activeSubmittedAt =
        $activeReport[
            'submitted_at'
        ]
        ??
        $activeReport[
            'created_at'
        ]
        ??
        null;


    $activeRemarks =
        $activeReport[
            'supervisor_remarks'
        ]
        ??
        $activeReport[
            'remarks'
        ]
        ??
        '';


    /*
     * Extracted entities are supplied by
     * review_reports.php.
     */

    $extractedEntities =
        $activeReport[
            'extracted_entities'
        ]
        ??
        [];


    if (
        !is_array(
            $extractedEntities
        )
    ) {

        $extractedEntities =
            [];
    }


    /*
     * Extraction summary from Python.
     */

    $extractionSummary =
        $activeReport[
            'extraction_summary'
        ]
        ??
        [];


    if (
        !is_array(
            $extractionSummary
        )
    ) {

        $extractionSummary =
            [];
    }
}


/* ============================================================
   PDF URL
   ============================================================ */

$pdfUrl =
    '';


if (
    !empty($activeFilePath)
) {

    $cleanPath =
        ltrim(

            str_replace(
                '\\',
                '/',
                $activeFilePath
            ),

            '/'
        );


    if (
        stripos(
            $cleanPath,
            'ICS-PORTAL/'
        )
        ===
        0
    ) {

        $pdfUrl =
            '/'
            .
            $cleanPath;

    } else {

        $pdfUrl =
            '/ICS-PORTAL/'
            .
            $cleanPath;
    }
}


/* ============================================================
   CLOSE URL
   ============================================================ */

$closeUrl =
    statusUrl(

        $filter_status !== 'All'
            ? $filter_status
            : null

    );

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Review Reports - Supervisor Portal
    </title>

    <!-- Tailwind CSS -->

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->

    <link
        rel="stylesheet"
        href="/ICS-PORTAL/public/css/style.css"
    >

</head>


<body
    class="bg-slate-50 text-slate-800 antialiased"
>

<div class="flex min-h-screen">


    <!-- =====================================================
         SIDEBAR
         ===================================================== -->

    <?php include
        __DIR__
        . '/../../components/supervisor_sidebar.php';
    ?>


    <!-- =====================================================
         RIGHT MAIN CONTENT
         ===================================================== -->

    <div
        class="flex-1 flex flex-col min-w-0"
    >


        <!-- Shared Header -->

        <?php include
            __DIR__
            . '/../../components/header.php';
        ?>


        <!-- =================================================
             MAIN CONTENT
             ================================================= -->

        <main
            class="
                p-6
                max-w-7xl
                w-full
                mx-auto
                space-y-5
                flex-1
                relative
            "
        >


            <!-- =================================================
                 HEADER BANNER
                 ================================================= -->

            <div
                class="
                    bg-white
                    rounded-2xl
                    p-5
                    shadow-xs
                    border
                    border-slate-200/80
                    flex
                    flex-wrap
                    justify-between
                    items-center
                    gap-3
                "
            >

                <div>

                    <h1
                        class="
                            text-base
                            font-bold
                            text-slate-900
                            leading-snug
                        "
                    >
                        Review Weekly Accomplishment Reports
                    </h1>

                    <p
                        class="
                            text-slate-500
                            text-xs
                            mt-0.5
                        "
                    >
                        Filter, evaluate, and approve submitted
                        accomplishment reports from your interns.
                    </p>

                </div>


                <!-- FILTER TABS -->

                <div
                    class="
                        flex
                        items-center
                        gap-1
                        bg-slate-100
                        p-1
                        rounded-xl
                        border
                        border-slate-200/60
                        text-xs
                    "
                >

                    <!-- ALL -->

                    <a
                        href="<?= e(statusUrl()); ?>"
                        class="
                            px-3.5
                            py-1.5
                            rounded-lg
                            font-semibold
                            transition-all
                            <?= $filter_status === 'All'
                                ? 'bg-white text-[#0F2854] shadow-2xs'
                                : 'text-slate-500 hover:text-slate-800'; ?>
                        "
                    >
                        All
                    </a>


                    <!-- PENDING -->

                    <a
                        href="<?= e(statusUrl('Pending')); ?>"
                        class="
                            px-3.5
                            py-1.5
                            rounded-lg
                            font-semibold
                            transition-all
                            <?= $filter_status === 'Pending'
                                ? 'bg-white text-amber-600 shadow-2xs'
                                : 'text-slate-500 hover:text-slate-800'; ?>
                        "
                    >
                        Pending
                    </a>


                    <!-- APPROVED -->

                    <a
                        href="<?= e(statusUrl('Approved')); ?>"
                        class="
                            px-3.5
                            py-1.5
                            rounded-lg
                            font-semibold
                            transition-all
                            <?= $filter_status === 'Approved'
                                ? 'bg-white text-emerald-600 shadow-2xs'
                                : 'text-slate-500 hover:text-slate-800'; ?>
                        "
                    >
                        Approved
                    </a>


                    <!-- NEEDS REVISION -->

                    <a
                        href="<?= e(statusUrl('Needs Revision')); ?>"
                        class="
                            px-3.5
                            py-1.5
                            rounded-lg
                            font-semibold
                            transition-all
                            <?= $filter_status === 'Needs Revision'
                                ? 'bg-white text-rose-600 shadow-2xs'
                                : 'text-slate-500 hover:text-slate-800'; ?>
                        "
                    >
                        Revisions
                    </a>

                </div>

            </div>


            <!-- =================================================
                 MESSAGE
                 ================================================= -->

            <?php if (!empty($message)): ?>

                <div
                    class="
                        bg-emerald-50
                        border
                        border-emerald-200
                        text-emerald-800
                        px-4
                        py-3
                        rounded-xl
                        text-xs
                        font-semibold
                        flex
                        items-center
                        justify-between
                        shadow-2xs
                    "
                >

                    <span>

                        ✓
                        <?= e($message); ?>

                    </span>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 SUBMISSIONS QUEUE
                 ================================================= -->

            <div
                class="
                    bg-white
                    rounded-2xl
                    border
                    border-slate-200/80
                    shadow-xs
                    overflow-hidden
                "
            >

                <div
                    class="
                        p-4
                        px-5
                        border-b
                        border-slate-100
                        flex
                        justify-between
                        items-center
                    "
                >

                    <div>

                        <h3
                            class="
                                text-sm
                                font-bold
                                text-slate-900
                            "
                        >
                            Submissions Queue
                            (<?= count($reports); ?>)
                        </h3>

                        <p
                            class="
                                text-[11px]
                                text-slate-400
                                mt-0.5
                            "
                        >
                            Prioritizing pending submissions
                            awaiting review
                        </p>

                    </div>

                </div>


                <?php if (!empty($reports)): ?>

                    <div class="overflow-x-auto">

                        <table
                            class="
                                w-full
                                text-left
                                border-collapse
                            "
                        >

                            <thead>

                                <tr
                                    class="
                                        bg-slate-50/60
                                        text-slate-400
                                        text-[11px]
                                        uppercase
                                        tracking-wider
                                        border-b
                                        border-slate-100
                                        font-semibold
                                    "
                                >

                                    <th
                                        class="py-3 px-5"
                                    >
                                        Student Name
                                    </th>

                                    <th
                                        class="py-3 px-5"
                                    >
                                        Week Number
                                    </th>

                                    <th
                                        class="py-3 px-5"
                                    >
                                        Date & Time Submitted
                                    </th>

                                    <th
                                        class="py-3 px-5"
                                    >
                                        Status
                                    </th>

                                    <th
                                        class="
                                            py-3
                                            px-5
                                            text-right
                                        "
                                    >
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody
                                class="
                                    divide-y
                                    divide-slate-100
                                    text-slate-700
                                    text-xs
                                "
                            >

                            <?php foreach (
                                $reports
                                as $item
                            ): ?>

                                <?php

                                $status =
                                    normalizeReportStatus(
                                        $item['status']
                                        ?? 'pending'
                                    );

                                $submittedAt =
                                    $item[
                                        'submitted_at'
                                    ]
                                    ??
                                    $item[
                                        'created_at'
                                    ]
                                    ??
                                    null;

                                $studentName =
                                    $item[
                                        'student_name'
                                    ]
                                    ??
                                    'Unknown Student';

                                $studentNumber =
                                    $item[
                                        'student_number'
                                    ]
                                    ??
                                    'N/A';

                                $program =
                                    $item[
                                        'program'
                                    ]
                                    ??
                                    'BSIT';

                                $avatar =
                                    $item[
                                        'student_avatar'
                                    ]
                                    ??
                                    '';

                                $weekNumber =
                                    $item[
                                        'week_number'
                                    ]
                                    ??
                                    'N/A';

                                $reportId =
                                    (int)(
                                        $item['id']
                                        ??
                                        0
                                    );

                                $reviewUrl =
                                    statusUrl(

                                        $filter_status
                                            !== 'All'
                                            ? $filter_status
                                            : null,

                                        $reportId
                                    );

                                ?>

                                <tr
                                    class="
                                        transition-colors
                                        <?= $status === 'pending'
                                            ? 'bg-amber-50/40 hover:bg-amber-100/50 border-l-4 border-l-amber-500'
                                            : 'hover:bg-slate-50/80'; ?>
                                    "
                                >


                                    <!-- STUDENT -->

                                    <td
                                        class="
                                            py-3.5
                                            px-5
                                        "
                                    >

                                        <div
                                            class="
                                                flex
                                                items-center
                                                gap-2.5
                                            "
                                        >

                                            <div
                                                class="
                                                    w-8
                                                    h-8
                                                    rounded-full
                                                    bg-blue-50
                                                    text-[#0F2854]
                                                    flex
                                                    items-center
                                                    justify-center
                                                    font-bold
                                                    text-xs
                                                    shrink-0
                                                    overflow-hidden
                                                    border
                                                    border-slate-200
                                                "
                                            >

                                                <?php if (
                                                    !empty($avatar)
                                                ): ?>

                                                    <img
                                                        src="<?= e($avatar); ?>"
                                                        class="
                                                            w-full
                                                            h-full
                                                            object-cover
                                                        "
                                                        alt="Student avatar"
                                                    >

                                                <?php else: ?>

                                                    <?= e(
                                                        strtoupper(
                                                            substr(
                                                                $studentName,
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    ); ?>

                                                <?php endif; ?>

                                            </div>


                                            <div>

                                                <p
                                                    class="
                                                        font-bold
                                                        text-slate-900
                                                    "
                                                >
                                                    <?= e($studentName); ?>
                                                </p>

                                                <p
                                                    class="
                                                        text-[11px]
                                                        text-slate-400
                                                    "
                                                >
                                                    ID:
                                                    <?= e($studentNumber); ?>

                                                    •

                                                    <?= e($program); ?>
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- WEEK -->

                                    <td
                                        class="
                                            py-3.5
                                            px-5
                                        "
                                    >

                                        <p
                                            class="
                                                font-bold
                                                text-slate-800
                                            "
                                        >
                                            Week
                                            <?= e($weekNumber); ?>
                                        </p>

                                    </td>


                                    <!-- DATE -->

                                    <td
                                        class="
                                            py-3.5
                                            px-5
                                            text-slate-600
                                            font-medium
                                        "
                                    >

                                        <?php if (
                                            !empty($submittedAt)
                                        ): ?>

                                            <?= e(
                                                date(
                                                    "M d, Y \a\\t g:i A",
                                                    strtotime(
                                                        $submittedAt
                                                    )
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            —

                                        <?php endif; ?>

                                    </td>


                                    <!-- STATUS -->

                                    <td
                                        class="
                                            py-3.5
                                            px-5
                                        "
                                    >

                                        <?php if (
                                            $status
                                            ===
                                            'approved'
                                        ): ?>

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1
                                                    px-2.5
                                                    py-0.5
                                                    rounded-full
                                                    bg-emerald-50
                                                    text-emerald-700
                                                    text-[11px]
                                                    font-medium
                                                    border
                                                    border-emerald-200/50
                                                "
                                            >
                                                ● Approved
                                            </span>

                                        <?php elseif (
                                            $status
                                            ===
                                            'pending'
                                        ): ?>

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1
                                                    px-2.5
                                                    py-0.5
                                                    rounded-full
                                                    bg-amber-50
                                                    text-amber-700
                                                    text-[11px]
                                                    font-medium
                                                    border
                                                    border-amber-200/50
                                                "
                                            >
                                                ● Pending
                                            </span>

                                        <?php else: ?>

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1
                                                    px-2.5
                                                    py-0.5
                                                    rounded-full
                                                    bg-rose-50
                                                    text-rose-700
                                                    text-[11px]
                                                    font-medium
                                                    border
                                                    border-rose-200/50
                                                "
                                            >
                                                ● Needs Revision
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- ACTION -->

                                    <td
                                        class="
                                            py-3.5
                                            px-5
                                            text-right
                                        "
                                    >

                                        <a
                                            href="<?= e($reviewUrl); ?>"
                                            class="
                                                px-4
                                                py-1.5
                                                <?= $status === 'pending'
                                                    ? 'bg-[#0F2854] text-white hover:bg-blue-900'
                                                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?>
                                                text-[11px]
                                                font-semibold
                                                rounded-full
                                                border
                                                border-slate-200
                                                transition-all
                                                shadow-2xs
                                                inline-block
                                            "
                                        >
                                            <?= $status === 'pending'
                                                ? 'Review'
                                                : 'View Details'; ?>
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div
                        class="
                            text-center
                            py-12
                            px-4
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                bg-blue-50
                                text-blue-600
                                rounded-xl
                                flex
                                items-center
                                justify-center
                                mx-auto
                                mb-2
                                text-base
                                font-bold
                            "
                        >
                            📋
                        </div>

                        <h4
                            class="
                                text-sm
                                font-semibold
                                text-slate-800
                            "
                        >
                            No reports found
                        </h4>

                        <p
                            class="
                                text-xs
                                text-slate-500
                                max-w-xs
                                mx-auto
                                mt-0.5
                            "
                        >
                            There are currently no reports
                            matching the selected filter criteria.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </main>

    </div>

</div>


<!-- ============================================================
     ACTIVE REPORT MODAL
     ============================================================ -->

<?php if ($hasActiveReport): ?>

<div
    class="
        fixed
        inset-0
        bg-slate-900/60
        backdrop-blur-xs
        flex
        items-center
        justify-center
        z-50
        p-4
        sm:p-6
        overflow-y-auto
    "
>

    <!-- MODAL -->

    <div
        class="
            bg-white
            rounded-2xl
            border
            border-slate-200
            shadow-2xl
            max-w-6xl
            w-full
            overflow-hidden
            space-y-4
            p-6
            relative
            my-auto
        "
    >


        <!-- CLOSE BUTTON -->

        <a
            href="<?= e($closeUrl); ?>"
            class="
                absolute
                top-4
                right-4
                w-8
                h-8
                rounded-full
                bg-slate-100
                hover:bg-slate-200
                text-slate-500
                hover:text-slate-800
                flex
                items-center
                justify-center
                text-sm
                font-bold
                transition-all
                z-10
            "
            aria-label="Close"
        >
            ✕
        </a>


        <!-- MODAL HEADER -->

        <div
            class="
                border-b
                border-slate-100
                pb-3
                pr-10
                flex
                flex-wrap
                justify-between
                items-start
                gap-2
            "
        >

            <div>

                <h2
                    class="
                        text-base
                        font-bold
                        text-slate-900
                    "
                >

                    <?= e(
                        $activeReport[
                            'student_name'
                        ]
                        ??
                        'Unknown Student'
                    ); ?>

                    -

                    Week

                    <?= e(
                        $activeReport[
                            'week_number'
                        ]
                        ??
                        'N/A'
                    ); ?>

                </h2>


                <p
                    class="
                        text-xs
                        text-slate-500
                        mt-0.5
                    "
                >

                    Submitted on

                    <?php if (
                        !empty(
                            $activeSubmittedAt
                        )
                    ): ?>

                        <?= e(
                            date(
                                "F d, Y \a\\t g:i A",
                                strtotime(
                                    $activeSubmittedAt
                                )
                            )
                        ); ?>

                    <?php else: ?>

                        —

                    <?php endif; ?>

                </p>

            </div>


            <!-- STATUS -->

            <div
                class="mr-6"
            >

                <?php if ($isApproved): ?>

                    <span
                        class="
                            px-3
                            py-1
                            bg-emerald-50
                            text-emerald-700
                            border
                            border-emerald-200
                            text-[11px]
                            font-bold
                            rounded-full
                        "
                    >
                        ● Approved
                    </span>

                <?php elseif ($isRevision): ?>

                    <span
                        class="
                            px-3
                            py-1
                            bg-rose-50
                            text-rose-700
                            border
                            border-rose-200
                            text-[11px]
                            font-bold
                            rounded-full
                        "
                    >
                        ● Revision Requested
                    </span>

                <?php else: ?>

                    <span
                        class="
                            px-3
                            py-1
                            bg-amber-50
                            text-amber-700
                            border
                            border-amber-200
                            text-[11px]
                            font-bold
                            rounded-full
                        "
                    >
                        ● Pending Review
                    </span>

                <?php endif; ?>

            </div>

        </div>


        <!-- ====================================================
             TWO COLUMN CONTENT
             ==================================================== -->

        <div
            class="
                grid
                grid-cols-1
                lg:grid-cols-5
                gap-5
            "
        >


            <!-- =================================================
                 LEFT COLUMN
                 PDF VIEWER
                 ================================================= -->

            <div
                class="
                    lg:col-span-3
                    bg-slate-50
                    rounded-xl
                    border
                    border-slate-200/80
                    p-4
                    flex
                    flex-col
                    min-h-[650px]
                "
            >


                <!-- PDF HEADER -->

                <div class="mb-3">

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[11px]
                                    font-bold
                                    uppercase
                                    tracking-wider
                                    text-slate-400
                                "
                            >
                                Accomplishment Report
                            </p>

                            <p
                                class="
                                    text-xs
                                    text-slate-500
                                    mt-0.5
                                "
                            >
                                Week
                                <?= e(
                                    $activeReport[
                                        'week_number'
                                    ]
                                    ??
                                    'N/A'
                                ); ?>

                                — PDF Document
                            </p>

                        </div>


                        <?php if (
                            !empty($pdfUrl)
                        ): ?>

                            <a
                                href="<?= e($pdfUrl); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="
                                    text-[10px]
                                    font-semibold
                                    text-[#0F2854]
                                    hover:text-blue-900
                                    hover:underline
                                "
                            >
                                Open Fullscreen ↗
                            </a>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- PDF VIEWER -->

                <?php if (
                    !empty($pdfUrl)
                ): ?>

                    <div
                        class="
                            flex-1
                            bg-white
                            rounded-xl
                            border
                            border-slate-200
                            overflow-hidden
                            shadow-2xs
                            min-h-[580px]
                        "
                    >

                        <iframe
                            src="<?= e($pdfUrl); ?>#toolbar=1&navpanes=0&scrollbar=1"
                            class="
                                w-full
                                h-full
                                min-h-[580px]
                                border-0
                            "
                            title="Accomplishment Report PDF"
                        >
                        </iframe>

                    </div>

                <?php else: ?>

                    <div
                        class="
                            flex-1
                            bg-white
                            rounded-xl
                            border
                            border-dashed
                            border-slate-200
                            flex
                            flex-col
                            items-center
                            justify-center
                            text-center
                            min-h-[580px]
                        "
                    >

                        <div
                            class="
                                w-12
                                h-12
                                bg-slate-100
                                text-slate-400
                                rounded-xl
                                flex
                                items-center
                                justify-center
                                text-2xl
                                mb-3
                            "
                        >
                            📄
                        </div>


                        <p
                            class="
                                text-xs
                                font-semibold
                                text-slate-600
                            "
                        >
                            No PDF Document
                        </p>


                        <p
                            class="
                                text-[10px]
                                text-slate-400
                                mt-1
                            "
                        >
                            No attachment was uploaded
                            for this report.
                        </p>

                    </div>

                <?php endif; ?>


                <!-- STUDENT HISTORY -->

                <?php if (
                    !empty(
                        $activeReport[
                            'student_id'
                        ]
                    )
                ): ?>

                    <div
                        class="
                            pt-3
                            mt-3
                            border-t
                            border-slate-200/60
                        "
                    >

                        <a
                            href="interns.php?id=<?= (int)$activeReport['student_id']; ?>"
                            class="
                                w-full
                                text-center
                                text-xs
                                font-semibold
                                text-[#0F2854]
                                hover:underline
                                flex
                                items-center
                                justify-center
                                gap-1.5
                                py-1
                            "
                        >

                            <span>

                                View

                                <?= e(
                                    $activeReport[
                                        'student_name'
                                    ]
                                    ??
                                    'Student'
                                ); ?>

                                's Full WAR History

                            </span>

                            <span>→</span>

                        </a>

                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 RIGHT COLUMN
                 ENTITIES + FEEDBACK
                 ================================================= -->

            <div
                class="
                    lg:col-span-2
                    space-y-4
                    flex
                    flex-col
                    justify-between
                "
            >


                <!-- =================================================
                     EXTRACTED ENTITIES
                     ================================================= -->

                <div
                    class="
                        bg-slate-50
                        rounded-xl
                        border
                        border-slate-200/80
                        p-3.5
                        space-y-2
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                        "
                    >

                        <p
                            class="
                                text-[11px]
                                font-bold
                                uppercase
                                tracking-wider
                                text-slate-400
                            "
                        >
                            Extracted Entities
                        </p>


                        <?php if (
                            !empty(
                                $extractedEntities
                            )
                        ): ?>

                            <span
                                class="
                                    text-[10px]
                                    text-slate-400
                                "
                            >

                                <?= count(
                                    $extractedEntities
                                ); ?>

                                detected

                            </span>

                        <?php endif; ?>

                    </div>


                    <?php if (
                        !empty(
                            $extractedEntities
                        )
                    ): ?>


                        <!-- ENTITY LIST -->

                        <div
                            class="
                                space-y-2
                                max-h-[420px]
                                overflow-y-auto
                                pr-1
                            "
                        >

                            <?php foreach (
                                $extractedEntities
                                as $entity
                            ): ?>

                                <?php

                                /*
                                 * Entity name can come from
                                 * Python or report_entities.
                                 */

                                $entityName =
                                    $entity[
                                        'entity_name'
                                    ]
                                    ??
                                    $entity[
                                        'entity'
                                    ]
                                    ??
                                    $entity[
                                        'canonical_name'
                                    ]
                                    ??
                                    $entity[
                                        'matched_term'
                                    ]
                                    ??
                                    '';


                                $category =
                                    $entity[
                                        'category'
                                    ]
                                    ??
                                    'Uncategorized';


                                $activityType =
                                    $entity[
                                        'activity_type'
                                    ]
                                    ??
                                    '';


                                $itRelated =
                                    $entity[
                                        'it_related'
                                    ]
                                    ??
                                    '';


                                $matchedTerm =
                                    $entity[
                                        'matched_term'
                                    ]
                                    ??
                                    $entityName;


                                $spacyLabel =
                                    $entity[
                                        'spacy_label'
                                    ]
                                    ??
                                    '';


                                $frequency =
                                    $entity[
                                        'frequency'
                                    ]
                                    ??
                                    '';


                                $confidence =
                                    $entity[
                                        'confidence_score'
                                    ]
                                    ??
                                    '';

                                ?>


                                <?php if (
                                    $entityName !== ''
                                ): ?>

                                    <div
                                        class="
                                            bg-white
                                            border
                                            border-slate-200
                                            rounded-lg
                                            p-2.5
                                        "
                                    >


                                        <!-- ENTITY NAME -->

                                        <div
                                            class="
                                                flex
                                                items-center
                                                justify-between
                                                gap-2
                                            "
                                        >

                                            <span
                                                class="
                                                    text-xs
                                                    font-bold
                                                    text-slate-800
                                                "
                                            >
                                                <?= e(
                                                    $entityName
                                                ); ?>
                                            </span>


                                            <!-- IT STATUS -->

                                            <?php if (
                                                isITRelated(
                                                    $itRelated
                                                )
                                            ): ?>

                                                <span
                                                    class="
                                                        px-2
                                                        py-0.5
                                                        rounded-full
                                                        bg-emerald-50
                                                        text-emerald-700
                                                        border
                                                        border-emerald-200
                                                        text-[10px]
                                                        font-semibold
                                                        whitespace-nowrap
                                                    "
                                                >
                                                    IT Related
                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="
                                                        px-2
                                                        py-0.5
                                                        rounded-full
                                                        bg-slate-100
                                                        text-slate-500
                                                        border
                                                        border-slate-200
                                                        text-[10px]
                                                        font-semibold
                                                        whitespace-nowrap
                                                    "
                                                >
                                                    Non-IT
                                                </span>

                                            <?php endif; ?>

                                        </div>


                                        <!-- ENTITY DETAILS -->

                                        <div
                                            class="
                                                flex
                                                flex-wrap
                                                gap-x-3
                                                gap-y-1
                                                mt-1.5
                                            "
                                        >


                                            <!-- CATEGORY -->

                                            <span
                                                class="
                                                    text-[10px]
                                                    text-slate-500
                                                "
                                            >

                                                Category:

                                                <strong
                                                    class="
                                                        text-slate-700
                                                    "
                                                >
                                                    <?= e(
                                                        $category
                                                    ); ?>
                                                </strong>

                                            </span>


                                            <!-- ACTIVITY TYPE -->

                                            <?php if (
                                                $activityType
                                                !==
                                                ''
                                            ): ?>

                                                <span
                                                    class="
                                                        text-[10px]
                                                        text-slate-500
                                                    "
                                                >

                                                    Type:

                                                    <strong
                                                        class="
                                                            text-slate-700
                                                        "
                                                    >
                                                        <?= e(
                                                            $activityType
                                                        ); ?>
                                                    </strong>

                                                </span>

                                            <?php endif; ?>


                                            <!-- MATCHED TERM -->

                                            <?php if (
                                                $matchedTerm
                                                !==
                                                ''
                                            ): ?>

                                                <span
                                                    class="
                                                        text-[10px]
                                                        text-slate-500
                                                    "
                                                >

                                                    Matched:

                                                    <strong
                                                        class="
                                                            text-slate-700
                                                        "
                                                    >
                                                        <?= e(
                                                            $matchedTerm
                                                        ); ?>
                                                    </strong>

                                                </span>

                                            <?php endif; ?>


                                            <!-- SPACY LABEL -->

                                            <?php if (
                                                $spacyLabel
                                                !==
                                                ''
                                            ): ?>

                                                <span
                                                    class="
                                                        text-[10px]
                                                        text-slate-500
                                                    "
                                                >

                                                    spaCy:

                                                    <strong
                                                        class="
                                                            text-slate-700
                                                        "
                                                    >
                                                        <?= e(
                                                            $spacyLabel
                                                        ); ?>
                                                    </strong>

                                                </span>

                                            <?php endif; ?>


                                            <!-- FREQUENCY -->

                                            <?php if (
                                                $frequency
                                                !==
                                                ''
                                            ): ?>

                                                <span
                                                    class="
                                                        text-[10px]
                                                        text-slate-500
                                                    "
                                                >

                                                    Frequency:

                                                    <strong
                                                        class="
                                                            text-slate-700
                                                        "
                                                    >
                                                        <?= e(
                                                            $frequency
                                                        ); ?>
                                                    </strong>

                                                </span>

                                            <?php endif; ?>


                                            <!-- CONFIDENCE -->

                                            <?php if (
                                                $confidence
                                                !==
                                                ''
                                            ): ?>

                                                <span
                                                    class="
                                                        text-[10px]
                                                        text-slate-500
                                                    "
                                                >

                                                    Confidence:

                                                    <strong
                                                        class="
                                                            text-slate-700
                                                        "
                                                    >
                                                        <?= e(
                                                            number_format(
                                                                (float)$confidence,
                                                                2
                                                            )
                                                        ); ?>%
                                                    </strong>

                                                </span>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </div>


                    <?php else: ?>


                        <!-- NO ENTITIES -->

                        <div
                            class="
                                bg-white
                                border
                                border-dashed
                                border-slate-200
                                rounded-lg
                                p-4
                                text-center
                            "
                        >

                            <div
                                class="
                                    text-xl
                                    mb-1
                                "
                            >
                                🔍
                            </div>

                            <p
                                class="
                                    text-xs
                                    text-slate-400
                                    italic
                                "
                            >
                                No predefined entities were
                                detected in this PDF.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- =================================================
                     APPROVED REPORT
                     ================================================= -->

                <?php if (
                    $isApproved
                ): ?>

                    <div
                        class="
                            space-y-3
                            pt-2
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[11px]
                                    font-semibold
                                    text-slate-400
                                    uppercase
                                    tracking-wider
                                    mb-1
                                "
                            >
                                Supervisor Feedback
                            </p>


                            <div
                                class="
                                    bg-slate-50
                                    border
                                    border-slate-200
                                    rounded-xl
                                    p-3
                                    text-xs
                                    text-slate-800
                                    font-medium
                                    italic
                                "
                            >

                                "

                                <?= e(
                                    $activeRemarks
                                    !== ''
                                        ? $activeRemarks
                                        : 'Report verified and approved.'
                                ); ?>

                                "

                            </div>

                        </div>


                        <div
                            class="
                                flex
                                items-center
                                justify-end
                                pt-2
                                border-t
                                border-slate-100
                            "
                        >

                            <a
                                href="<?= e($closeUrl); ?>"
                                class="
                                    px-5
                                    py-1.5
                                    bg-slate-100
                                    hover:bg-slate-200
                                    text-slate-700
                                    text-xs
                                    font-semibold
                                    rounded-full
                                    border
                                    border-slate-200
                                    transition-all
                                "
                            >
                                Close
                            </a>

                        </div>

                    </div>


                <?php else: ?>


                    <!-- =================================================
                         PENDING / REVISION REPORT
                         ================================================= -->

                    <form
                        method="POST"
                        action="review_reports.php"
                        class="
                            space-y-3
                            pt-2
                        "
                    >


                        <!-- REPORT ID -->

                        <input
                            type="hidden"
                            name="action_report_id"
                            value="<?= (int)(
                                $activeReport['id']
                                ??
                                0
                            ); ?>"
                        >


                        <!-- STUDENT NAME -->

                        <input
                            type="hidden"
                            name="student_name"
                            value="<?= e(
                                $activeReport[
                                    'student_name'
                                ]
                                ??
                                ''
                            ); ?>"
                        >


                        <!-- FEEDBACK -->

                        <div>

                            <label
                                class="
                                    block
                                    text-[11px]
                                    font-semibold
                                    text-slate-400
                                    uppercase
                                    tracking-wider
                                    mb-1
                                "
                            >
                                Supervisor Feedback
                            </label>


                            <textarea
                                name="supervisor_remarks"
                                rows="4"
                                placeholder="Enter comments or instructions for required revisions..."
                                class="
                                    w-full
                                    text-xs
                                    bg-slate-50
                                    border
                                    border-slate-200
                                    rounded-xl
                                    p-2.5
                                    text-slate-800
                                    focus:outline-none
                                    focus:border-[#0F2854]
                                    resize-none
                                "
                            ><?= e(
                                $activeRemarks
                            ); ?></textarea>

                        </div>


                        <!-- ACTION BUTTONS -->

                        <div
                            class="
                                flex
                                items-center
                                justify-end
                                gap-2
                                pt-2
                                border-t
                                border-slate-100
                            "
                        >


                            <!-- CANCEL -->

                            <a
                                href="<?= e($closeUrl); ?>"
                                class="
                                    px-4
                                    py-1.5
                                    bg-slate-100
                                    hover:bg-slate-200
                                    text-slate-600
                                    text-xs
                                    font-semibold
                                    rounded-full
                                    border
                                    border-slate-200
                                    transition-all
                                "
                            >
                                Cancel
                            </a>


                            <!-- REQUEST REVISION -->

                            <button
                                type="submit"
                                name="status"
                                value="Needs Revision"
                                class="
                                    px-4
                                    py-1.5
                                    bg-rose-50
                                    hover:bg-rose-100
                                    text-rose-700
                                    text-xs
                                    font-semibold
                                    rounded-full
                                    border
                                    border-rose-200
                                    transition-all
                                    cursor-pointer
                                "
                            >
                                Request Revision
                            </button>


                            <!-- APPROVE -->

                            <button type="submit" name="status" value="Approved"
                                class="
                                    px-4
                                    py-1.5
                                    bg-[#0F2854]
                                    hover:bg-blue-900
                                    text-white
                                    text-xs
                                    font-semibold
                                    rounded-full
                                    border
                                    border-[#0F2854]
                                    transition-all
                                    shadow-2xs
                                    cursor-pointer
                                "
                            >
                                Approve Report
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

</body>
</html>