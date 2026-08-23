<?php

/*
 * ============================================================
 * src/pages/supervisor/reviewReportsPage.php
 * ============================================================
 *
 * Supervisor Review Reports VIEW
 *
 * Loaded by:
 *
 * supervisor/review_reports.php
 *
 * This file DOES NOT:
 * - start a session
 * - require db.php
 * - execute database queries
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

$filter_status = $filter_status ?? 'All';

$message = $message ?? '';

$reports = $reports ?? [];

$activeReport = $activeReport ?? null;


/* ============================================================
   HELPER FUNCTION
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

    function statusUrl($status = null, $reviewId = null)
    {
        $params = [];

        if ($reviewId !== null) {

            $params['review_id'] = (int)$reviewId;
        }

        if (
            $status !== null &&
            $status !== 'All'
        ) {

            $params['status'] = $status;
        }

        if (empty($params)) {

            return 'review_reports.php';
        }

        return 'review_reports.php?' .
            http_build_query($params);
    }
}


/* ============================================================
   NORMALIZE STATUS
   ============================================================ */

if (!function_exists('normalizeReportStatus')) {

    function normalizeReportStatus($status)
    {
        $status = trim((string)$status);

        if ($status === '') {

            return 'pending';
        }

        $lower = strtolower($status);

        if ($lower === 'approved') {

            return 'approved';
        }

        if ($lower === 'pending') {

            return 'pending';
        }

        if (
            $lower === 'needs revision' ||
            $lower === 'needs_revision' ||
            $lower === 'revision' ||
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

if (!function_exists('isITRelated')) {

    function isITRelated($value)
    {
        $value = strtolower(trim((string)$value));

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

    $filter_status = 'All';
}


/* ============================================================
   ACTIVE REPORT DATA
   ============================================================ */

$hasActiveReport =
    !empty($activeReport) &&
    is_array($activeReport);

$activeStatus = 'pending';

$isApproved = false;

$isRevision = false;

$activeFilePath = '';

$activeSubmittedAt = null;

$extractedEntities = [];

$extractionSummary = [];

$pdfText = '';


if ($hasActiveReport) {

    $activeStatus =
        normalizeReportStatus(
            $activeReport['status'] ?? 'pending'
        );

    $isApproved =
        ($activeStatus === 'approved');

    $isRevision =
        ($activeStatus === 'needs revision');


    $activeFilePath =
        $activeReport['file_path']
        ??
        $activeReport['attachment_path']
        ??
        '';


    $activeSubmittedAt =
        $activeReport['submitted_at']
        ??
        $activeReport['created_at']
        ??
        null;


    /*
     * Extracted entities supplied by
     * review_reports.php.
     */

    $extractedEntities =
        $activeReport['extracted_entities']
        ?? [];


    if (
        !is_array($extractedEntities)
    ) {

        $extractedEntities = [];
    }


    /*
     * Extraction summary.
     */

    $extractionSummary =
        $activeReport['extraction_summary']
        ?? [];


    if (
        !is_array($extractionSummary)
    ) {

        $extractionSummary = [];
    }


    /*
     * PDF extracted text.
     *
     * This should preferably be supplied
     * by review_reports.php after Python
     * extraction.
     */

    $pdfText =
        $activeReport['pdf_text']
        ??
        $activeReport['extracted_text']
        ??
        $activeReport['report_text']
        ??
        $activeReport['text']
        ??
        '';
}


/* ============================================================
   PDF URL
   ============================================================ */

$pdfUrl = '';


if (!empty($activeFilePath)) {

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
        ) === 0
    ) {

        $pdfUrl =
            '/' .
            $cleanPath;

    } else {

        $pdfUrl =
            '/ICS-PORTAL/' .
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


/* ============================================================
   PREPARE ENTITY DATA FOR JAVASCRIPT
   ============================================================ */

$javascriptEntities = [];


if (!empty($extractedEntities)) {

    foreach (
        $extractedEntities
        as $entity
    ) {

        if (!is_array($entity)) {

            continue;
        }


        $entityName =
            $entity['entity_name']
            ??
            $entity['entity']
            ??
            $entity['canonical_name']
            ??
            $entity['matched_term']
            ??
            '';


        $matchedTerm =
            $entity['matched_term']
            ??
            $entityName;


        $entityName =
            trim((string)$entityName);

        $matchedTerm =
            trim((string)$matchedTerm);


        if (
            $entityName === '' &&
            $matchedTerm === ''
        ) {

            continue;
        }


        $javascriptEntities[] = [

            'name' =>
                $entityName,

            'matched' =>
                $matchedTerm
        ];
    }
}

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


    <!-- ======================================================
         TAILWIND
         ====================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- ======================================================
         CUSTOM CSS
         ====================================================== -->

    <link
        rel="stylesheet"
        href="/ICS-PORTAL/public/css/style.css"
    >


    <style>

        /*
         * =====================================================
         * PDF TEXT HIGHLIGHT
         * =====================================================
         */

        .pdf-text-container {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            font-size: 13px;

            line-height: 1.65;

            white-space: pre-wrap;

            word-break: normal;

            overflow-wrap: anywhere;

            color: #334155;
        }


        .pdf-entity-highlight {

            background-color: #fde68a;

            color: #78350f;

            border-radius: 4px;

            padding: 1px 3px;

            box-shadow:
                0 0 0 1px
                rgba(245, 158, 11, 0.25);

            transition:
                background-color 0.15s ease,
                box-shadow 0.15s ease;
        }


        .pdf-entity-highlight.active {

            background-color: #fbbf24;

            color: #451a03;

            box-shadow:
                0 0 0 2px
                rgba(245, 158, 11, 0.35);
        }


        /*
         * =====================================================
         * ENTITY BUTTON
         * =====================================================
         */

        .entity-clickable {

            cursor: pointer;

            user-select: none;

            transition:
                background-color 0.15s ease,
                border-color 0.15s ease,
                transform 0.1s ease;
        }


        .entity-clickable:hover {

            background-color: #fff7ed;

            border-color: #fdba74;
        }


        .entity-clickable:active {

            transform: scale(0.99);
        }


        .entity-clickable.selected {

            background-color: #fff7ed;

            border-color: #f59e0b;

            box-shadow:
                0 0 0 2px
                rgba(245, 158, 11, 0.12);
        }


        /*
         * =====================================================
         * SHORT MODAL
         * =====================================================
         */

        .review-modal {

            max-height: 90vh;

            height: auto;
        }


        /*
         * =====================================================
         * MOBILE
         * =====================================================
         */

        @media (max-width: 1023px) {

            .review-modal {

                max-height: 94vh;
            }

        }

    </style>

</head>


<body
    class="
        bg-slate-50
        text-slate-800
        antialiased
    "
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
         MAIN CONTENT
         ===================================================== -->

    <div
        class="
            flex-1
            flex
            flex-col
            min-w-0
        "
    >


        <!-- HEADER -->

        <?php include
            __DIR__
            . '/../../components/header.php';
        ?>


        <!-- =================================================
             MAIN
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


                <!-- FILTER -->

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
                    "
                >

                    ✓ <?= e($message); ?>

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
                    "
                >

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
                        Prioritizing pending submissions awaiting review
                    </p>

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

                                    <th class="py-3 px-5">
                                        Student Name
                                    </th>

                                    <th class="py-3 px-5">
                                        Week Number
                                    </th>

                                    <th class="py-3 px-5">
                                        Date & Time Submitted
                                    </th>

                                    <th class="py-3 px-5">
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
                                    $item['submitted_at']
                                    ??
                                    $item['created_at']
                                    ??
                                    null;

                                $studentName =
                                    $item['student_name']
                                    ??
                                    'Unknown Student';

                                $studentNumber =
                                    $item['student_number']
                                    ??
                                    'N/A';

                                $program =
                                    $item['program']
                                    ??
                                    'BSIT';

                                $avatar =
                                    $item['student_avatar']
                                    ??
                                    '';

                                $weekNumber =
                                    $item['week_number']
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
                                        $filter_status !== 'All'
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

                                    <td class="py-3.5 px-5">

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

                                    <td class="py-3.5 px-5">

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

                                    <td class="py-3.5 px-5">

                                        <?php if (
                                            $status === 'approved'
                                        ): ?>

                                            <span
                                                class="
                                                    inline-flex
                                                    px-2.5
                                                    py-0.5
                                                    rounded-full
                                                    bg-emerald-50
                                                    text-emerald-700
                                                    text-[11px]
                                                    font-medium
                                                    border
                                                    border-emerald-200
                                                "
                                            >
                                                ● Approved
                                            </span>

                                        <?php elseif (
                                            $status === 'pending'
                                        ): ?>

                                            <span
                                                class="
                                                    inline-flex
                                                    px-2.5
                                                    py-0.5
                                                    rounded-full
                                                    bg-amber-50
                                                    text-amber-700
                                                    text-[11px]
                                                    font-medium
                                                    border
                                                    border-amber-200
                                                "
                                            >
                                                ● Pending
                                            </span>

                                        <?php else: ?>

                                            <span
                                                class="
                                                    inline-flex
                                                    px-2.5
                                                    py-0.5
                                                    rounded-full
                                                    bg-rose-50
                                                    text-rose-700
                                                    text-[11px]
                                                    font-medium
                                                    border
                                                    border-rose-200
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
                                mt-0.5
                            "
                        >
                            There are currently no reports matching
                            the selected filter criteria.
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
    id="reviewModal"
    class="
        fixed
        inset-0
        bg-slate-900/60
        backdrop-blur-xs
        flex
        items-center
        justify-center
        z-50
        p-3
        sm:p-5
        overflow-y-auto
    "
>


    <!-- ========================================================
         MODAL
         SHORTER HEIGHT
         ======================================================== -->

    <div
        class="
            review-modal
            bg-white
            rounded-2xl
            border
            border-slate-200
            shadow-2xl
            max-w-6xl
            w-full
            overflow-hidden
            relative
            my-auto
            flex
            flex-col
            p-5
        "
    >


        <!-- CLOSE -->

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
                z-20
            "
            aria-label="Close"
        >
            ✕
        </a>


        <!-- ====================================================
             MODAL HEADER
             ==================================================== -->

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
                shrink-0
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
                        $activeReport['student_name']
                        ??
                        'Unknown Student'
                    ); ?>

                    -

                    Week

                    <?= e(
                        $activeReport['week_number']
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
                        !empty($activeSubmittedAt)
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

            <div class="mr-10">

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
             TWO COLUMNS
             ==================================================== -->

        <div
            class="
                grid
                grid-cols-1
                lg:grid-cols-5
                gap-4
                mt-4
                min-h-0
            "
        >


            <!-- =================================================
                 LEFT SIDE
                 ================================================= -->

            <div
                class="
                    lg:col-span-3
                    bg-slate-50
                    rounded-xl
                    border
                    border-slate-200/80
                    p-3
                    flex
                    flex-col
                    h-[62vh]
                    min-h-[450px]
                    max-h-[620px]
                "
            >


                <!-- PDF HEADER -->

                <div
                    class="
                        mb-2
                        shrink-0
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-2
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
                                    $activeReport['week_number']
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


                <!-- =================================================
                     PDF VIEWER
                     ================================================= -->

                <?php if (!empty($pdfUrl)): ?>

                    <div
                        class="
                            flex-1
                            min-h-0
                            bg-white
                            rounded-xl
                            border
                            border-slate-200
                            overflow-hidden
                            shadow-2xs
                        "
                    >

                        <iframe
                            src="<?= e($pdfUrl); ?>#toolbar=1&navpanes=0&scrollbar=1"
                            class="
                                w-full
                                h-full
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
                            min-h-0
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
                            No attachment was uploaded for this report.
                        </p>

                    </div>

                <?php endif; ?>


                <!-- STUDENT HISTORY -->

                <?php if (
                    !empty(
                        $activeReport['student_id']
                    )
                ): ?>

                    <div
                        class="
                            pt-2
                            mt-2
                            border-t
                            border-slate-200/60
                            shrink-0
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
                            "
                        >

                            View
                            <?= e(
                                $activeReport['student_name']
                                ??
                                'Student'
                            ); ?>

                            's Full WAR History →

                        </a>

                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 RIGHT SIDE
                 ================================================= -->

            <div
                class="
                    lg:col-span-2
                    flex
                    flex-col
                    min-h-0
                    h-[62vh]
                    min-h-[450px]
                    max-h-[620px]
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
                        p-3
                        flex
                        flex-col
                        min-h-0
                        flex-1
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            shrink-0
                            mb-2
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
                            !empty($extractedEntities)
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


                    <p
                        class="
                            text-[10px]
                            text-slate-400
                            mb-2
                            shrink-0
                        "
                    >
                        Click an entity to locate and highlight
                        it in the extracted report text.
                    </p>


                    <?php if (
                        !empty($extractedEntities)
                    ): ?>

                        <div
                            class="
                                space-y-2
                                overflow-y-auto
                                pr-1
                                min-h-0
                            "
                        >

                            <?php foreach (
                                $extractedEntities
                                as $entity
                            ): ?>

                                <?php

                                if (!is_array($entity)) {

                                    continue;
                                }


                                $entityName =
                                    $entity['entity_name']
                                    ??
                                    $entity['entity']
                                    ??
                                    $entity['canonical_name']
                                    ??
                                    $entity['matched_term']
                                    ??
                                    '';


                                $category =
                                    $entity['category']
                                    ??
                                    'Uncategorized';


                                $activityType =
                                    $entity['activity_type']
                                    ??
                                    '';


                                $itRelated =
                                    $entity['it_related']
                                    ??
                                    '';


                                $matchedTerm =
                                    $entity['matched_term']
                                    ??
                                    $entityName;


                                $spacyLabel =
                                    $entity['spacy_label']
                                    ??
                                    '';


                                $frequency =
                                    $entity['frequency']
                                    ??
                                    '';


                                $confidence =
                                    $entity['confidence_score']
                                    ??
                                    '';


                                $clickTerm =
                                    $matchedTerm !== ''
                                        ? $matchedTerm
                                        : $entityName;

                                ?>


                                <?php if (
                                    trim(
                                        (string)$entityName
                                    ) !== ''
                                ): ?>

                                    <!-- =================================================
                                         CLICKABLE ENTITY
                                         ================================================= -->

                                    <button
                                        type="button"
                                        class="
                                            entity-clickable
                                            w-full
                                            text-left
                                            bg-white
                                            border
                                            border-slate-200
                                            rounded-lg
                                            p-2.5
                                        "
                                        data-entity="<?= e($clickTerm); ?>"
                                        onclick="highlightEntity(this)"
                                    >

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
                                                    text-orange-800
                                                "
                                            >
                                                <?= e(
                                                    $entityName
                                                ); ?>
                                            </span>


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


                                        <!-- DETAILS -->

                                        <div
                                            class="
                                                flex
                                                flex-wrap
                                                gap-x-3
                                                gap-y-1
                                                mt-1.5
                                            "
                                        >

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


                                            <?php if (
                                                $activityType !== ''
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


                                            <?php if (
                                                $matchedTerm !== ''
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


                                            <?php if (
                                                $spacyLabel !== ''
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


                                            <?php if (
                                                $frequency !== ''
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


                                            <?php if (
                                                $confidence !== ''
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

                                    </button>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div
                            class="
                                bg-white
                                border
                                border-dashed
                                border-slate-200
                                rounded-lg
                                p-4
                                text-center
                                my-auto
                            "
                        >

                            <div class="text-xl mb-1">
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
                     PDF TEXT / HIGHLIGHT AREA
                     ================================================= -->

                <div
                    id="pdfTextPanel"
                    class="
                        hidden
                        mt-3
                        bg-white
                        border
                        border-slate-200
                        rounded-xl
                        p-3
                        max-h-[180px]
                        overflow-y-auto
                        shadow-2xs
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            mb-2
                        "
                    >

                        <p
                            class="
                                text-[10px]
                                font-bold
                                uppercase
                                tracking-wider
                                text-slate-400
                            "
                        >
                            Extracted Report Text
                        </p>

                        <button
                            type="button"
                            onclick="clearEntityHighlight()"
                            class="
                                text-[10px]
                                text-slate-400
                                hover:text-slate-700
                            "
                        >
                            Clear
                        </button>

                    </div>


                    <div
                        id="pdfTextContent"
                        class="pdf-text-container"
                    ></div>

                </div>


                <!-- =================================================
                     ACTIONS
                     ================================================= -->

                <?php if ($isApproved): ?>

                    <div
                        class="
                            flex
                            items-center
                            justify-end
                            pt-3
                            mt-3
                            border-t
                            border-slate-100
                            shrink-0
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
                            "
                        >
                            Close
                        </a>

                    </div>


                <?php else: ?>


                    <form
                        method="POST"
                        action="review_reports.php"
                        class="
                            space-y-3
                            pt-3
                            mt-3
                            border-t
                            border-slate-100
                            shrink-0
                        "
                    >

                        <input
                            type="hidden"
                            name="action_report_id"
                            value="<?= (int)(
                                $activeReport['id']
                                ??
                                0
                            ); ?>"
                        >


                        <input
                            type="hidden"
                            name="student_name"
                            value="<?= e(
                                $activeReport['student_name']
                                ??
                                ''
                            ); ?>"
                        >


                        <div
                            class="
                                flex
                                items-center
                                justify-end
                                gap-2
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
                                "
                            >
                                Cancel
                            </a>


                            <!-- REVISION -->

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
                                "
                            >
                                Request Revision
                            </button>


                            <!-- APPROVE -->

                            <button
                                type="submit"
                                name="status"
                                value="Approved"
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


<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->

<script>

    /*
     * ==========================================================
     * PDF TEXT FROM PHP
     * ==========================================================
     */

    const pdfReportText =
        <?= json_encode(
            (string)$pdfText,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ); ?>;


    /*
     * ==========================================================
     * CURRENT HIGHLIGHT
     * ==========================================================
     */

    let currentlyHighlightedEntity = null;


    /*
     * ==========================================================
     * ESCAPE HTML
     * ==========================================================
     */

    function escapeHtml(value) {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    /*
     * ==========================================================
     * ESCAPE REGEX
     * ==========================================================
     */

    function escapeRegex(value) {

        return String(value).replace(
            /[.*+?^${}()|[\]\\]/g,
            '\\$&'
        );
    }


    /*
     * ==========================================================
     * HIGHLIGHT ENTITY
     *
     * IMPORTANT:
     *
     * There is NO highlight until the supervisor
     * clicks an entity.
     * ==========================================================
     */

    function highlightEntity(button) {

        if (!button) {

            return;
        }


        const entity =
            button.getAttribute(
                'data-entity'
            );


        if (
            !entity ||
            entity.trim() === ''
        ) {

            return;
        }


        /*
         * Remove previous selected
         * entity button.
         */

        document
            .querySelectorAll(
                '.entity-clickable.selected'
            )
            .forEach(
                function(item) {

                    item.classList.remove(
                        'selected'
                    );

                }
            );


        /*
         * Select clicked entity.
         */

        button.classList.add(
            'selected'
        );


        /*
         * If there is no extracted
         * text, we cannot highlight
         * inside the PDF viewer.
         */

        if (
            !pdfReportText ||
            pdfReportText.trim() === ''
        ) {

            return;
        }


        const panel =
            document.getElementById(
                'pdfTextPanel'
            );


        const content =
            document.getElementById(
                'pdfTextContent'
            );


        if (
            !panel ||
            !content
        ) {

            return;
        }


        /*
         * Escape original PDF text.
         */

        let safeText =
            escapeHtml(
                pdfReportText
            );


        /*
         * Highlight only the clicked
         * entity.
         */

        const regex =
            new RegExp(
                escapeRegex(entity),
                'gi'
            );


        let matchFound = false;


        safeText =
            safeText.replace(
                regex,
                function(match) {

                    matchFound = true;

                    return (
                        '<mark class="pdf-entity-highlight active">' +
                        escapeHtml(match) +
                        '</mark>'
                    );

                }
            );


        /*
         * Display the text panel only
         * after an entity is clicked.
         */

        panel.classList.remove(
            'hidden'
        );


        content.innerHTML =
            safeText;


        /*
         * Scroll to the first
         * highlighted entity.
         */

        const highlighted =
            content.querySelector(
                '.pdf-entity-highlight'
            );


        if (highlighted) {

            highlighted.scrollIntoView({

                behavior: 'smooth',

                block: 'center'

            });

        }


        currentlyHighlightedEntity =
            entity;


        /*
         * If entity was not found,
         * show a small message.
         */

        if (!matchFound) {

            content.innerHTML =
                '<div class="text-xs text-slate-400 italic">' +
                'The entity "' +
                escapeHtml(entity) +
                '" was not found in the extracted report text.' +
                '</div>';

        }

    }


    /*
     * ==========================================================
     * CLEAR HIGHLIGHT
     * ==========================================================
     */

    function clearEntityHighlight() {

        document
            .querySelectorAll(
                '.entity-clickable.selected'
            )
            .forEach(
                function(item) {

                    item.classList.remove(
                        'selected'
                    );

                }
            );


        const panel =
            document.getElementById(
                'pdfTextPanel'
            );


        const content =
            document.getElementById(
                'pdfTextContent'
            );


        if (panel) {

            panel.classList.add(
                'hidden'
            );

        }


        if (content) {

            content.innerHTML = '';

        }


        currentlyHighlightedEntity =
            null;
    }


    /*
     * ==========================================================
     * ESCAPE KEY
     * ==========================================================
     */

    document.addEventListener(
        'keydown',
        function(event) {

            if (
                event.key === 'Escape'
            ) {

                clearEntityHighlight();

            }

        }
    );

</script>


</body>

</html>