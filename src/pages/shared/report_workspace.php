<?php
// src/pages/shared/report_workspace.php
/**
 * Shared markup for the per-student report workspace.
 *
 * Included by src/pages/{coordinator,student,supervisor}/viewReportPage.php,
 * which must define the following before requiring this file:
 *
 *   $viewContext        'coordinator' | 'student' | 'supervisor'
 *   $selfPath           controller filename used for form/GET targets
 *   $backUrl            where the "Back" button points
 *   $canManageEntities  show Add Entity / Archive / reclassify controls
 *   $canExportCsv       show the CSV summary button
 *   $canReview          show Approve / Request Changes controls
 *   $showItPercentages  show the overall + per-week IT/clerical ratio bars
 *   $showEntities       show the per-week extracted-entity panel at all
 *   $selectorInRightPanel  render the week dropdown / submitted stamp /
 *                          fullscreen link / late flag as a card at the top of
 *                          the right column instead of a bar above the PDF
 *   $pdfView            'width' (default) or 'fit' embedded PDF zoom
 *   $sidebarPath        sidebar component to include
 *   $pageTitle          <title> suffix
 *
 * The workspace data itself comes from reportWorkspaceBuild() in
 * src/services/report_workspace.php and is exposed here as the same variable
 * names the coordinator controller has always used.
 */

require_once __DIR__ . '/../../services/report_workspace.php';

// This view is shared across roles. Only the coordinator controller verifies
// the CSRF token today, but every POST form here carries one so that adding
// verification to the student/supervisor controllers later needs no markup
// change. The include keeps csrf_field() safe regardless of which role renders
// this file.
require_once __DIR__ . '/../../../config/csrf.php';

$viewContext = $viewContext ?? 'coordinator';
$selfPath = $selfPath ?? 'view_report.php';
$backUrl = $backUrl ?? 'reports.php';
$canManageEntities = $canManageEntities ?? false;
$canExportCsv = $canExportCsv ?? false;
$canReview = $canReview ?? false;
$showItPercentages = $showItPercentages ?? false;
$showEntities = $showEntities ?? true;
$selectorInRightPanel = $selectorInRightPanel ?? false;
$sidebarPath = $sidebarPath ?? ($viewContext . '_sidebar.php');
$pageTitle = $pageTitle ?? 'Report Workspace';

$student = $student ?? null;
$studentId = $studentId ?? 0;
$reportsList = $reportsList ?? [];
$activeReport = $activeReport ?? null;
$allEntities = $allEntities ?? [];
$weekEntities = $weekEntities ?? [];
$archivedEntities = $archivedEntities ?? [];
$itPct = $itPct ?? 0;
$clericalPct = $clericalPct ?? 0;
$weekItPct = $weekItPct ?? 0;
$weekClericalPct = $weekClericalPct ?? 0;
$weekTotalCount = $weekTotalCount ?? 0;
$pdfUrl = $pdfUrl ?? reportWorkspacePdfUrl($activeReport['file_path'] ?? '');

$activeWeekNumber = $activeReport ? (int) $activeReport['week_number'] : null;
$activeStatus = strtolower((string) ($activeReport['status'] ?? 'pending'));
$activeApprovedAt = !empty($activeReport['approved_at'])
    ? date("M d, Y g:i A", strtotime($activeReport['approved_at']))
    : (!empty($activeReport['updated_at']) && $activeStatus === 'approved'
        ? date("M d, Y g:i A", strtotime($activeReport['updated_at']))
        : 'Pending');
$activeSubmittedAt = !empty($activeReport['submitted_at'])
    ? date("M d, Y g:i A", strtotime($activeReport['submitted_at']))
    : 'N/A';
$activeRemarks = trim((string) ($activeReport['supervisor_remarks'] ?? ''));

$workspaceQuery = $viewContext === 'coordinator'
    ? $selfPath . '?student_id=' . $studentId
    : $selfPath;
$archiveFormAction = $selfPath . ($viewContext === 'coordinator' ? '?student_id=' . $studentId : '');

/**
 * Whether the right-hand column has anything to show.
 *
 * It normally holds the review verdict, the overall IT ratio card and the
 * per-week entity panel. A role that turns all three off would otherwise be
 * left with an empty 5-column gap, so the column is dropped entirely and the
 * PDF viewer takes the full width. Only the supervisor feedback banner can
 * still keep it alive for such a role.
 *
 * $selectorInRightPanel is a fourth reason to keep it: a role that has moved
 * the week controls over there needs the column to exist even when it has
 * nothing else to put in it.
 */
$hasRightColumn = $showEntities
    || $showItPercentages
    || ($canReview && $activeReport)
    || ($activeReport && $activeRemarks !== '')
    || $selectorInRightPanel;

/**
 * Zoom fragment for the embedded PDF viewer.
 *
 * 'width' is the historical behaviour: the page is scaled until it fills the
 * embed horizontally, so a short page leaves room to spare and a long one
 * runs off the bottom. 'fit' scales the whole page into the viewport instead.
 *
 * The two fragments are combined because they mean different things in
 * different viewers: view= is the Acrobat/Edge form, zoom= is the one Chrome
 * honours (it ignores view= entirely and falls back to fit-width). Keeping
 * both means each browser gets the behaviour it actually implements.
 *
 * A role whose column is widened should ask for 'fit' -- at twice the width, a
 * fit-width PDF scales up sharply and the text becomes larger than the
 * surrounding page.
 */
$pdfView = $pdfView ?? 'width';

$pdfViewerFragment = $pdfView === 'fit'
    ? '#navpanes=0&view=Fit&zoom=page-fit'
    : '#navpanes=0&view=FitH';

/**
 * Renders the week picker, the submitted stamp, the fullscreen link and the late
 * flag.
 *
 * These four are one unit: they all describe the single week the PDF viewer is
 * showing. $asCard picks the wrapper -- a card at the top of the right-hand
 * column, or the flat bar above the PDF -- and both wrappers render the same
 * controls from one closure, so a role that switches can never end up with a
 * dropdown listing different weeks than it did before, or an item that exists in
 * one layout and not the other.
 */
$renderWeekSelector = function (bool $asCard) use ($selfPath, $viewContext, $studentId, $reportsList, $activeReport, $activeSubmittedAt, $pdfUrl): void {
    /*
     * The week number and the approval state are already spelled out by the
     * select below ("Week 3 [Approved]"), so neither wrapper repeats them. A
     * dark bar used to sit above the select and carry "Week N Report Document"
     * plus Sub and Appr badges, which read as the same facts twice, one row
     * apart. What is left is what the select cannot express: the late flag and
     * the submitted stamp.
     */
    ?>
    <?php /* Card wrapper, styled like the other cards in the right-hand column
        (rounded, bordered, full width) rather than inset into the PDF panel,
        because here it is a column card and not part of the viewer. It keeps its
        own heading: a role using this layout has no entity panel or verdict card
        above it, so nothing else in the column says what is being selected.
        The card branch below is the student's layout and is left exactly as it
        is; the bar branch is the one the staff pages use. */
    if ($asCard): ?>
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs shrink-0">
            <div class="p-4 border-b border-slate-200/70 bg-slate-50/60">
                <h2 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">Report Document</h2>
                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Select a week to inspect its PDF</p>
            </div>
            <div class="p-4 space-y-3">
                <?php /* Card layout, two rows:

                    [ week dropdown ][ submitted stamp ]
                    [ open fullscreen ][ late warning   ]

                The stamp sits beside the dropdown because the two answer the same
                question -- which week, and when it landed -- and reading them
                together avoids making the eye jump down a row for the second half of
                one fact. The dropdown is flex-1 with a basis so it absorbs the
                leftover width; the stamp is shrink-0 so a long date never pushes it
                out of the row.

                The fullscreen link drops to the second row because it is an action
                rather than a fact about the submission, and it needs its own space to
                sit on. The late warning follows it on that row: the link opens the
                very document the warning is about, so the two belong side by side
                with the flag last, where it reads as an aside to what is above. */ ?>
            <?php else: ?>
                <div class="p-4 bg-slate-100/80 border-b border-slate-200/70 space-y-3 shrink-0">
                <?php endif; ?>

                <?php /* First row: the dropdown, plus the fullscreen link for the staff bar.
            Both wrappers keep the exact gap they used to render with, so the card
            layout is untouched and the bar only gains its second row. */ ?>
                <div class="flex flex-wrap items-center <?= $asCard ? 'gap-2' : 'gap-3'; ?>">

                    <?php /* The form and select below are shared by both wrappers, but the bar
        wrapper keeps the exact attributes the staff pages have always rendered
        with -- the same form class, the same wrapping div, no extra attributes on
        the select. Only the card branch asks for anything different, so switching
        $selectorInRightPanel on for a role cannot restyle the others. */ ?>
                    <form method="GET" action="<?= htmlspecialchars($selfPath); ?>"
                        class="<?= $asCard ? 'text-xs flex-1 basis-64' : 'flex items-center gap-3 flex-1 text-xs'; ?>">
                        <?php if ($viewContext === 'coordinator'): ?>
                            <input type="hidden" name="student_id" value="<?= (int) $studentId; ?>">
                        <?php endif; ?>
                        <div class="<?= $asCard ? '' : 'flex-1'; ?>">
                            <select name="report_id" <?= $asCard ? 'aria-label="Select submitted week report"' : '' ?>
                                onchange="showWeekLoading(this); this.form.submit()"
                                class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                <option value="" disabled <?= $activeReport ? '' : 'selected'; ?>>-- Select Submitted Week
                                    Report --</option>
                                <?php foreach ($reportsList as $rep):
                                    $isActive = $activeReport && (int) $rep['id'] === (int) $activeReport['id'];
                                    ?>
                                    <option value="<?= (int) $rep['id']; ?>" <?= $isActive ? 'selected' : ''; ?>>
                                        Week <?= (int) $rep['week_number']; ?>
                                        [<?= htmlspecialchars(ucfirst((string) $rep['status'])); ?>]<?= !empty($rep['is_late']) ? ' - Late' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>

                    <?php if ($asCard): ?>
                        <span
                            class="inline-flex shrink-0 items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-white text-slate-600 border border-slate-300 text-[11px] font-bold"
                            title="When this report was submitted.">
                            <span aria-hidden="true">&#128229;</span>
                            Submitted <?= htmlspecialchars($activeSubmittedAt); ?>
                        </span>
                    </div>

                    <?php /* The second row is skipped outright when it would hold nothing,
                        rather than left behind as an empty flex row carrying its gap. */ ?>
                    <?php if ($pdfUrl !== '' || !empty($activeReport['is_late'])): ?>
                        <div class="flex flex-wrap items-center gap-2">
                            <?php if ($pdfUrl !== ''): ?>
                                <a href="<?= htmlspecialchars($pdfUrl); ?>" target="_blank" rel="noopener"
                                    class="shrink-0 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-[11px] font-bold shadow-xs inline-flex items-center gap-1">
                                    <span>Open Fullscreen &nearr;</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($activeReport['is_late'])): ?>
                                <span
                                    class="inline-flex shrink-0 items-center justify-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold"
                                    title="This report was submitted after its deadline.">
                                    <span aria-hidden="true">&#9888;</span>
                                    Late submission
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php /* Bar layout, two rows:

                        [ week dropdown ][ open fullscreen ]
                        [ submitted date ][ late warning   ]

                    The fullscreen link moves up beside the dropdown because both name
                    and open the week report, so they belong on one line with the
                    dropdown taking the leftover width. The two facts about the
                    submission -- when it landed, and whether it was late -- drop to the
                    row below in the same left-to-right order, where the flag reads as a
                    qualifier on the date rather than competing with the picker for
                    attention. Both rows wrap, so nothing is squeezed out on a narrow
                    screen. Every element keeps the classes it has always had; only the
                    arrangement changed. */ ?>
                    <?php if ($activeReport && $pdfUrl !== ''): ?>
                        <a href="<?= htmlspecialchars($pdfUrl); ?>" target="_blank" rel="noopener"
                            class="shrink-0 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-[11px] font-bold shadow-xs inline-flex items-center gap-1">
                            <span>Open Fullscreen &nearr;</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <?php if ($activeReport): ?>
                        <span
                            class="inline-flex items-center gap-1.5 shrink-0 px-2.5 py-1.5 rounded-lg bg-white text-slate-600 border border-slate-300 text-[11px] font-bold"
                            title="When this report was submitted.">
                            <span aria-hidden="true">&#128229;</span>
                            Submitted <?= htmlspecialchars($activeSubmittedAt); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($activeReport && !empty($activeReport['is_late'])): ?>
                        <span
                            class="inline-flex items-center gap-1.5 shrink-0 px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold"
                            title="This report was submitted after its deadline.">
                            <span aria-hidden="true">&#9888;</span>
                            Late submission
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php /* The card opens two elements (outer card, padded body) and the bar
            opens one, so each needs its own closing tags. Emitting the card's
            pair unconditionally would leave a stray </div> in the staff pages. */ ?>
            <?php if ($asCard): ?>
            </div>
        </div>
    <?php else: ?>
        </div>
    <?php endif; ?>
<?php
};

/*
 * workspaceActivityType() and workspaceItFlag() now live in
 * src/services/report_workspace.php, guarded by function_exists(), so this
 * view and reportWorkspaceGroupEntities() agree on one classification. The
 * require at the top of this file is what loads them.
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle); ?> &ndash; <?= htmlspecialchars($student['student_name'] ?? 'Student'); ?>
        - OJT Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= BASE_PATH; ?>/public/css/style.css">
    <script src="<?= BASE_PATH; ?>/public/js/loadingOverlay.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">

        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/' . $sidebarPath; ?>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-6 sm:p-8 max-w-[1700px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Alert Messages -->
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div
                        class="bg-blue-50 border border-[#0F2854]/30 text-[#0F2854] text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="font-black">&check;</span>
                            <span><?= htmlspecialchars($_SESSION['flash_success']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="font-black hover:text-slate-700 cursor-pointer">&times;</button>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div
                        class="bg-rose-50 border border-rose-300 text-rose-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-700 font-black">&times;</span>
                            <span><?= htmlspecialchars($_SESSION['flash_error']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-rose-700 font-black hover:text-rose-950 cursor-pointer">&times;</button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['review_message'])): ?>
                    <div
                        class="bg-blue-50 border border-[#0F2854]/30 text-[#0F2854] text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="font-black">&check;</span>
                            <span><?= htmlspecialchars($_SESSION['review_message']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="font-black hover:text-slate-700 cursor-pointer">&times;</button>
                    </div>
                    <?php unset($_SESSION['review_message']); ?>
                <?php endif; ?>

                <?php
                /*
                 * Background extraction status.
                 *
                 * Extraction is no longer part of the page load, so the entity
                 * percentages below can briefly describe an earlier state (or no
                 * state at all) while a worker is running. These two notices say
                 * which of those is happening instead of leaving a 0% that looks
                 * like a finding.
                 *
                 * A permanent failure is a stored column, not a flash, so it
                 * survives reloads and keeps explaining the empty list. The
                 * extractor's own wording is shown, which names the actual limit
                 * or problem rather than a generic error.
                 */
                $icsExtractionStatus = $extractionStatus ?? null;
                $icsExtractionPending = !empty($extractionPending) || !empty($icsExtractionStatus['pending']);
                $icsExtractionError = $icsExtractionStatus['error'] ?? null;

                /*
                 * Whether anything is already on screen decides what the banner is
                 * allowed to promise.
                 *
                 * With no stored entities the percentages really are about to go
                 * from 0% to real, so saying they will update is accurate. With
                 * entities already saved -- a first refresh after the migration
                 * added the tracking columns, or a dictionary change re-running
                 * extraction -- the figures below are valid right now and only
                 * risk being revised. Telling that user "the percentages below
                 * will update on their own" describes a change that may never
                 * happen and implies the current numbers are not to be trusted.
                 */
                $icsHasStoredEntities = count($weekEntities ?? []) > 0
                    || count($allEntities ?? []) > 0;
                ?>
                <?php if ($icsExtractionPending): ?>
                    <div id="ics-extracting-banner"
                        class="bg-amber-50 border border-amber-300 text-amber-900 text-xs p-4 rounded-2xl font-bold flex items-center gap-2.5 shadow-2xs">
                        <span class="inline-block w-3 h-3 rounded-full bg-amber-500 animate-pulse"
                            aria-hidden="true"></span>
                        <span>
                            <?php if ($icsHasStoredEntities): ?>
                                Refreshing report entities&hellip; the figures below are from the last analysis and may be
                                revised when it finishes.
                            <?php else: ?>
                                Analyzing report entities&hellip; this takes a few seconds. The percentages below will update on
                                their own.
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($icsExtractionError)): ?>
                    <div id="ics-extraction-error-banner"
                        class="bg-rose-50 border border-rose-300 text-rose-900 text-xs p-4 rounded-2xl font-bold flex items-start gap-2.5 shadow-2xs">
                        <span class="text-rose-700 font-black">&times;</span>
                        <span>Could not analyze this PDF: <?= htmlspecialchars($icsExtractionError); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Student Profile Banner Header -->
                <div
                    class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <a href="<?= htmlspecialchars($backUrl); ?>"
                                class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl text-slate-800 transition-colors text-xs font-bold shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                <span>&larr;</span>
                                <span>Back</span>
                            </a>
                        </div>
                        <div
                            class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-sm shrink-0 overflow-hidden border border-slate-300">
                            <?php if (!empty($student['student_avatar'])): ?>
                                <img src="<?= htmlspecialchars($student['student_avatar']); ?>"
                                    class="w-full h-full object-cover" alt="Avatar">
                            <?php else: ?>
                                <?= strtoupper(substr($student['student_name'] ?? 'S', 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-0.5">
                            <h1 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight leading-snug">
                                <?= htmlspecialchars($student['student_name'] ?? 'Student'); ?>
                            </h1>
                            <?php /* Student number, course code and host company — coordinator only.

                                An intern already sees all of this on their
                                profile page, so showing it here repeated it twice.
                                A supervisor does not need it either: the course code
                                is already on My Interns and Review Reports, and the
                                host company is their own workplace, identical on
                                every intern they open. The coordinator keeps the
                                line because their copy is the one that becomes
                                paperwork. */ ?>
                            <?php if ($viewContext === 'coordinator'): ?>
                                <p class="text-xs font-semibold text-slate-600">
                                    ID: <?= htmlspecialchars($student['student_number'] ?? '—'); ?> &bull;
                                    <?= htmlspecialchars($student['section'] ?? '—'); ?> &bull; <strong
                                        class="text-slate-900"><?= htmlspecialchars($student['company_name'] ?? 'Host Company'); ?></strong>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Side: Active Inspection Badge & Export Button Below It -->
                    <div class="flex flex-col items-end gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-50 text-[#0F2854] border border-blue-200 font-bold text-xs shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Active Inspection: Week <?= $activeWeekNumber ?? '—'; ?>
                        </span>
                        <?php if ($canExportCsv): ?>
                            <a href="<?= htmlspecialchars($workspaceQuery . '&export=csv'); ?>"
                                class="px-3.5 py-1.5 bg-white hover:bg-blue-50 border border-[#0F2854]/40 text-[#0F2854] rounded-xl transition-colors text-xs font-bold shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                <span>Export CSV Summary</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 7/5 Balanced Grid Split Layout -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                    <!-- Left Column: Chronological WAR Timeline & Full-Size PDF Preview (7 of 12, or 12 when there is no right column) -->
                    <div
                        class="xl:col-span-<?= $hasRightColumn ? '7' : '12' ?> bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col h-[60vh] min-h-[420px] xl:h-[calc(100vh-210px)] xl:min-h-[780px]">
                        <div
                            class="p-4 sm:p-5 border-b border-slate-200/70 flex items-center justify-between bg-slate-50/60 shrink-0">
                            <div>
                                <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">WAR Timeline
                                    &amp; Document Viewer</h2>
                                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Select a week category below
                                    to inspect its PDF</p>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[10px] font-bold text-slate-700 shadow-2xs">
                                <?= count($reportsList); ?> Weeks Total
                            </span>
                        </div>

                        <!-- Week Selector Category Dropdown Bar (Clean & Separated) -->
                        <?php if (!$selectorInRightPanel): ?>
                            <?php $renderWeekSelector(false); ?>
                        <?php endif; ?>

                        <!-- Full-Size Embedded PDF Viewer Stage for Active Report -->
                        <div class="flex-1 min-h-0 bg-[#0F172A] relative flex flex-col">
                            <?php if ($activeReport && $pdfUrl !== ''): ?>
                                <div class="flex-1 min-h-0 relative w-full h-full bg-slate-950">
                                    <embed src="<?= htmlspecialchars($pdfUrl . $pdfViewerFragment); ?>"
                                        type="application/pdf" class="w-full h-full border-0">
                                </div>
                            <?php else: ?>
                                <div
                                    class="flex flex-col items-center justify-center h-full text-slate-400 text-xs p-6 text-center space-y-2">
                                    <div
                                        class="w-12 h-12 bg-slate-800 rounded-2xl flex items-center justify-center mx-auto text-xl">
                                        &#128193;</div>
                                    <p class="font-bold text-slate-200">No Report Selected</p>
                                    <p class="text-[11px] text-slate-400">Choose a week above to preview its submitted
                                        document.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Column: Aggregated Analytics & Active Week Entities (5 of 12 columns; omitted entirely when it would be empty) -->
                    <?php if ($hasRightColumn): ?>
                        <div
                            class="xl:col-span-5 flex flex-col h-[60vh] min-h-[420px] xl:h-[calc(100vh-250px)] xl:min-h-[740px] space-y-5">

                            <!-- Week Dropdown / Submitted Stamp / Fullscreen / Late (roles that set $selectorInRightPanel) -->
                            <?php if ($selectorInRightPanel): ?>
                                <?php $renderWeekSelector(true); ?>
                            <?php endif; ?>

                            <!-- Review verdict controls moved into the Extracted Entities panel below. -->

                            <!-- Supervisor Feedback Banner -->
                            <?php if ($activeReport && $activeRemarks !== ''): ?>
                                <div
                                    class="bg-rose-50 border border-rose-200 text-rose-950 rounded-2xl p-4 shadow-2xs shrink-0">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-rose-700">Supervisor Feedback
                                    </p>
                                    <p class="text-xs font-medium leading-relaxed mt-1"><?= htmlspecialchars($activeRemarks); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Overall IT Percentage Ratio Card -->
                            <?php if ($showItPercentages): ?>
                                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs space-y-3 shrink-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">Overall IT
                                            Percentage</h3>
                                        <span class="text-[11px] font-bold text-slate-500">
                                            <?= count($allEntities); ?> Total Entities (All Weeks)
                                        </span>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs font-bold">
                                            <span class="text-[#0F2854] flex items-center gap-1.5">
                                                <span>&#128187;</span> IT Related: <?= $itPct; ?>%
                                            </span>
                                            <span class="text-slate-600 flex items-center gap-1.5">
                                                <span>&#128193;</span> Clerical: <?= $clericalPct; ?>%
                                            </span>
                                        </div>
                                        <div
                                            class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex border border-slate-200 shadow-inner">
                                            <div class="bg-[#0F2854] h-full transition-all duration-300"
                                                style="width: <?= $itPct; ?>%"></div>
                                            <div class="bg-slate-400 h-full transition-all duration-300"
                                                style="width: <?= $clericalPct; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Active Week Entities & Manual Add Form -->
                            <?php if ($showEntities): ?>
                                <div
                                    class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex-1 flex flex-col min-h-0">
                                    <div class="p-4 border-b border-slate-200/70 bg-slate-50/60 shrink-0 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h2 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">
                                                    Week <?= $activeWeekNumber ?? '—'; ?> Entities
                                                </h2>
                                                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                                    <?= $canManageEntities ? 'Extracted entities &amp; manual additions' : 'Extracted from this submission'; ?>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <?php /* Entity count, for every role. This panel is the
                                                only place a supervisor is told how much was
                                                pulled out of the document. */ ?>
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-300 text-xs font-black text-slate-900 shadow-2xs">
                                                    <?= count($weekEntities); ?>
                                                    <?= count($weekEntities) === 1 ? 'entity' : 'entities'; ?>
                                                </span>
                                                <?php if ($canManageEntities): ?>
                                                    <button type="button"
                                                        onclick="document.getElementById('archiveModal').classList.remove('hidden')"
                                                        class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-xl text-xs transition-colors cursor-pointer">
                                                        Archive (<?= count($archivedEntities); ?>)
                                                    </button>
                                                    <button type="button"
                                                        onclick="document.getElementById('addEntityModal').classList.remove('hidden')"
                                                        class="px-3 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors shadow-2xs cursor-pointer">
                                                        + Add Entity
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- INDIVIDUAL ACTIVE WEEK PERCENTAGE BAR -->
                                        <?php if ($showItPercentages && $weekTotalCount > 0): ?>
                                            <div class="pt-3 border-t border-slate-200 space-y-1.5">
                                                <div class="flex items-center justify-between text-[11px] font-bold">
                                                    <span class="text-[#0F2854]">Week <?= (int) $activeReport['week_number']; ?> IT:
                                                        <?= $weekItPct; ?>%</span>
                                                    <span class="text-slate-600">Clerical: <?= $weekClericalPct; ?>%</span>
                                                </div>
                                                <div
                                                    class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden flex shadow-inner">
                                                    <div class="bg-[#0F2854] h-full transition-all duration-300"
                                                        style="width: <?= $weekItPct; ?>%"></div>
                                                    <div class="bg-slate-400 h-full transition-all duration-300"
                                                        style="width: <?= $weekClericalPct; ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Entities List for Selected Week -->
                                    <div class="p-4 overflow-y-auto flex-1">
                                        <?php if (empty($weekEntities)): ?>
                                            <div class="py-12 text-center text-slate-400 space-y-1">
                                                <div class="text-2xl">&#128269;</div>
                                                <p class="font-bold text-slate-700 text-xs">No entities for this week</p>
                                                <p class="text-[11px] text-slate-500 max-w-xs mx-auto">
                                                    <?= $canManageEntities ? 'Click "+ Add Entity" above to manually record any missed keywords.' : 'No technical keywords were extracted from this document.'; ?>
                                                </p>
                                            </div>
                                        <?php elseif (!$canManageEntities): ?>
                                            <?php /* Supervisor: compact grouped chips.
                                                    The card list below is sized for editing — a card per
                                                    entity is roughly 62px to show a name and one chip,
                                                    which is four screens of scrolling at 40 entities.
                                                    The supervisor only reads this list, so the same
                                                    facts render as wrapped chips that fit on one
                                                    screen. Grouping carries the IT/clerical split
                                                    the ratio bar gives the coordinator, since
                                                    $showItPercentages is off for this role. */ ?>
                                            <?php
                                            $entityGroups = reportWorkspaceGroupEntities($weekEntities);
                                            $entityGroupMeta = [
                                                'it' => ['label' => 'IT Related', 'chip' => 'text-[#0F2854] bg-blue-50 border-[#0F2854]/30'],
                                                'clerical' => ['label' => 'Clerical', 'chip' => 'text-slate-700 bg-slate-100 border-slate-300'],
                                                'unknown' => ['label' => 'Unclassified', 'chip' => 'text-slate-600 bg-slate-100 border-slate-300'],
                                            ];
                                            ?>
                                            <div class="space-y-5">
                                                <?php foreach ($entityGroupMeta as $groupKey => $groupMeta):
                                                    $groupNames = $entityGroups[$groupKey] ?? [];
                                                    if (empty($groupNames)) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <div>
                                                        <div class="flex items-center justify-between gap-2 mb-2">
                                                            <span
                                                                class="text-[10px] font-black uppercase tracking-wider text-slate-400"><?= htmlspecialchars($groupMeta['label']); ?></span>
                                                            <span
                                                                class="text-[11px] font-bold text-slate-500"><?= count($groupNames); ?></span>
                                                        </div>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            <?php foreach ($groupNames as $entityName): ?>
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border <?= $groupMeta['chip']; ?>"><?= htmlspecialchars($entityName); ?></span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($weekEntities as $ent):
                                                $currentType = workspaceActivityType($ent);
                                                $itFlag = workspaceItFlag($ent, $currentType);
                                                [$itBadgeLabel, $itBadgeClass] = match ($itFlag) {
                                                    'yes' => ['IT Related', 'text-[#0F2854] bg-blue-50 border-[#0F2854]/30'],
                                                    'no' => ['Clerical', 'text-slate-700 bg-slate-100 border-slate-300'],
                                                    default => ['Unclassified', 'text-slate-600 bg-slate-100 border-slate-300'],
                                                };
                                                ?>
                                                <div
                                                    class="p-3 bg-white rounded-xl border border-slate-200 space-y-2 shadow-2xs hover:border-slate-300 transition-colors">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <p class="font-extrabold text-slate-950 text-xs leading-snug">
                                                            <?= htmlspecialchars((string) $ent['entity_name']); ?></p>
                                                        <div class="flex items-center gap-1.5 shrink-0">
                                                            <?php /* Entity provenance, and the management controls beside it: coordinator
                                                                        only.

                                                                        These badges exist so a coordinator can tell which
                                                                        terms the extractor produced and which they typed in
                                                                        or corrected by hand. That is internal workflow — a
                                                                        supervisor reading somebody else's report has no use
                                                                        for it, and the intern panel is hidden outright.
                                                                        Everyone else sees just the classification chip below.

                                                                        Three coordinator states, decided by reclassified_at
                                                                        and original_it_related:

                                                                          no stamp, no original  typed into Add Entity from nothing
                                                                          stamp + value differs  extracted, then corrected
                                                                          stamp + value matches the coordinator put it back, so
                                                                                                 there is no override left to report

                                                                        confidence_score is deliberately not used for this:
                                                                        every 'predefined' row sits at exactly 100.00, so a
                                                                        reclassified predefined term would be
                                                                        indistinguishable from a typed one. */ ?>
                                                            <?php if ($canManageEntities): ?>
                                                                <?php if (($ent['source'] ?? '') === 'manual_addition'):
                                                                    $stamped = !empty($ent['reclassified_at']);
                                                                    $original = $ent['original_it_related'] ?? null;
                                                                    $isReverted = ($original !== null && (string) $original === (string) ($ent['it_related'] ?? ''));
                                                                    $isReclassified = $stamped && !$isReverted;
                                                                    ?>
                                                                    <?php if ($isReclassified): ?>
                                                                        <span
                                                                            class="text-[10px] font-bold text-[#0F2854] bg-blue-50 border-[#0F2854]/40 border-dashed px-2 py-0.5 rounded border"
                                                                            title="Extracted from the document, then reclassified by a coordinator"><span
                                                                                aria-hidden="true">&#9998;</span> Reclassified</span>
                                                                    <?php elseif (!$isReverted): ?>
                                                                        <span
                                                                            class="text-[10px] font-bold text-[#0F2854] bg-blue-50 border-[#0F2854]/30 px-2 py-0.5 rounded border"
                                                                            title="Typed in by a coordinator, not extracted from your document">Added</span>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                                <?php /* Dictionary matches. Slate rather than amber: this is
                                                                                provenance, not a warning. A term found in
                                                                                predefined_entities is a certain match, so it is
                                                                                never treated as a low-confidence prediction. */ ?>
                                                                <?php if (reportWorkspaceIsPredefined($ent)): ?>
                                                                    <span
                                                                        class="text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded"
                                                                        title="Matched a term in the predefined entity list, not predicted by the model">Predefined</span>
                                                                <?php endif; ?>
                                                                <?php /* ML predictions below the extractor threshold. Amber, because
                                                                                amber is already this system's "pending" colour and a weak
                                                                                prediction is pending verification rather than wrong.

                                                                                Only ever true for source = 'spacy'; dictionary rows carry
                                                                                100.00 and are labelled above instead. */ ?>
                                                                <?php if (reportWorkspaceIsLowConfidence($ent)): ?>
                                                                    <span
                                                                        class="text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded"
                                                                        title="The model was less than <?= htmlspecialchars((string) REPORT_WORKSPACE_LOW_CONFIDENCE, ENT_QUOTES, 'UTF-8'); ?>% sure about this term. Check it against the report.">
                                                                        <span aria-hidden="true">&#9888;</span> Low confidence
                                                                    </span>
                                                                <?php endif; ?>
                                                                <?php if (isset($ent['confidence_score']) && $ent['confidence_score'] !== null): ?>
                                                                    <span
                                                                        class="text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">
                                                                        <?= number_format((float) $ent['confidence_score'], 1); ?>% Conf.
                                                                    </span>
                                                                <?php endif; ?>
                                                                <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>"
                                                                    onsubmit="return confirm('Archive this entity?');">
                                                                    <?= csrf_field(); ?>
                                                                    <input type="hidden" name="action" value="delete_entity">
                                                                    <input type="hidden" name="report_id"
                                                                        value="<?= $activeReport ? (int) $activeReport['id'] : 0; ?>">
                                                                    <input type="hidden" name="entity_id" value="<?= (int) $ent['id']; ?>">
                                                                    <button type="submit"
                                                                        class="text-rose-600 hover:text-rose-800 text-xs font-bold p-1 cursor-pointer"
                                                                        title="Archive">&times;</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- Classification dropdown (coordinator only) -->
                                                    <?php if ($canManageEntities): ?>
                                                        <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>"
                                                            class="flex items-center gap-2 pt-1 border-t border-slate-200/80">
                                                            <?= csrf_field(); ?>
                                                            <input type="hidden" name="action" value="update_entity_type">
                                                            <input type="hidden" name="report_id"
                                                                value="<?= $activeReport ? (int) $activeReport['id'] : 0; ?>">
                                                            <input type="hidden" name="entity_id" value="<?= (int) $ent['id']; ?>">
                                                            <select name="it_related" onchange="this.form.submit()"
                                                                class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1 text-[11px] font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                                                <?php /* Preselect from $itFlag, the effective flag after normalisation
                                                                              to it_related, so the control always shows the value that is
                                                                              actually stored and used by the reporting queries. */ ?>
                                                                <option value="yes" <?= $itFlag !== 'no' ? 'selected' : ''; ?>>IT Related
                                                                </option>
                                                                <option value="no" <?= $itFlag === 'no' ? 'selected' : ''; ?>>Clerical</option>
                                                            </select>
                                                        </form>
                                                    <?php else: ?>
                                                        <span
                                                            class="text-[10px] font-bold px-2 py-0.5 rounded border <?= $itBadgeClass; ?>">
                                                            <?= $itBadgeLabel; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($canReview && $activeReport): ?>
                                        <div class="p-4 border-t border-slate-200/70 bg-slate-50/60 shrink-0">
                                            <?php if ($activeStatus === 'approved'): ?>
                                                <p class="text-[11px] font-semibold text-slate-500 text-right">Approved
                                                    <?= htmlspecialchars($activeApprovedAt); ?>. This submission is locked for editing.
                                                </p>
                                            <?php else: ?>
                                                <form method="POST"
                                                    action="<?= htmlspecialchars($selfPath . '?student_id=' . $studentId . '&report_id=' . (int) $activeReport['id']); ?>"
                                                    class="space-y-3" id="verdictForm">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="report_id" value="<?= (int) $activeReport['id']; ?>">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button type="button" id="btnRequestChanges"
                                                            class="px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-900 text-xs font-bold rounded-xl border border-rose-300 transition-all cursor-pointer">Request
                                                            Changes</button>
                                                        <button type="submit"
                                                            class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-colors shadow-xs cursor-pointer">Approve
                                                            Report</button>
                                                    </div>
                                                    <div id="requestChangesBox" class="hidden space-y-1.5">
                                                        <label for="verdictRemarks"
                                                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">Revision
                                                            Instructions <span
                                                                class="normal-case font-semibold text-slate-400">(required to request
                                                                changes)</span></label>
                                                        <textarea id="verdictRemarks" name="supervisor_remarks" rows="2"
                                                            placeholder="Explain the corrections or missing items the intern needs to address..."
                                                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-[#0F2854]"></textarea>
                                                        <div class="flex items-center justify-end">
                                                            <button type="button" id="btnConfirmRequestChanges"
                                                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs cursor-pointer">Confirm
                                                                Request Changes</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- Add Missing Entity Modal (coordinator only) -->
    <?php if ($canManageEntities): ?>
        <div id="addEntityModal"
            class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex overflow-y-auto items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 shadow-xl space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h3 class="font-extrabold text-sm text-slate-950">Add Entity</h3>
                        <p class="text-[11px] font-medium text-slate-500 leading-snug">Record a keyword the extractor missed
                            on this report.</p>
                    </div>
                    <button type="button" onclick="document.getElementById('addEntityModal').classList.add('hidden')"
                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer shrink-0">&#10005;</button>
                </div>

                <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>" class="space-y-3 text-xs">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="action" value="add_entity">
                    <input type="hidden" name="report_id" value="<?= $activeReport ? (int) $activeReport['id'] : 0; ?>">

                    <div class="space-y-1">
                        <label for="addEntityName" class="font-bold text-slate-700">Entity name <span class="text-[#0F2854]"
                                title="Required">*</span></label>
                        <input type="text" id="addEntityName" name="entity_name" required
                            placeholder="e.g. Figma, Photoshop, Excel, Filing"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:outline-none focus:border-[#0F2854]">
                        <p class="text-[11px] font-medium text-slate-500">Required.</p>
                    </div>

                    <div class="space-y-1">
                        <label for="addEntityType" class="font-bold text-slate-700">Type <span class="text-[#0F2854]"
                                title="Required">*</span></label>
                        <select id="addEntityType" name="it_related"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                            <option value="yes">IT Related</option>
                            <option value="no">Clerical</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="addEntityAliases" class="font-bold text-slate-700">Aliases <span
                                class="font-semibold text-slate-400">(optional)</span></label>
                        <input type="text" id="addEntityAliases" name="aliases" placeholder="Separate with commas"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:outline-none focus:border-[#0F2854]">
                        <p class="text-[11px] font-medium text-slate-500 leading-relaxed">Other names for the same thing.
                            Example: Figma design, Figma prototype.</p>
                    </div>

                    <div class="space-y-1 rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="add_to_dictionary" value="1"
                                class="mt-0.5 w-4 h-4 accent-[#0F2854] shrink-0 cursor-pointer">
                            <span class="text-sm font-bold text-slate-800 leading-snug">
                                Also add to the Entity Dictionary
                            </span>
                        </label>
                        <p class="text-[11px] font-medium text-slate-500 leading-relaxed mt-2 pl-6.5">
                            Saves it for every future report. Tick this for a real tool or
                            system, like &ldquo;Troubleshooting&rdquo;. Skip it for common words
                            like &ldquo;data&rdquo;.
                        </p>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-1">
                        <button type="button" onclick="document.getElementById('addEntityModal').classList.add('hidden')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl transition-colors cursor-pointer shadow-xs">Add
                            Entity</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Archive / Ignored Keywords Modal (coordinator only) -->
        <div id="archiveModal"
            class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4 max-h-[80vh] flex flex-col">
                <div class="flex items-center justify-between shrink-0">
                    <h3 class="font-extrabold text-sm text-slate-950">Archived / Ignored Entities</h3>
                    <button type="button" onclick="document.getElementById('archiveModal').classList.add('hidden')"
                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer shrink-0">&#10005;</button>
                </div>

                <div class="space-y-2.5 overflow-y-auto flex-1 pr-1">
                    <?php if (empty($archivedEntities)): ?>
                        <div class="py-10 text-center text-slate-400 text-xs">No archived entities found.</div>
                    <?php else: ?>
                        <?php foreach ($archivedEntities as $arch): ?>
                            <div
                                class="p-3 bg-slate-50 rounded-xl border border-slate-300 flex items-center justify-between gap-2 text-xs">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Week
                                        <?= (int) $arch['week_number']; ?></span>
                                    <p class="font-extrabold text-slate-950"><?= htmlspecialchars((string) $arch['entity_name']); ?>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <!-- Restore Action -->
                                    <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="action" value="restore_entity">
                                        <input type="hidden" name="entity_id" value="<?= (int) $arch['id']; ?>">
                                        <button type="submit"
                                            class="px-3 py-1 bg-white hover:bg-blue-50 border border-[#0F2854]/40 text-[#0F2854] font-bold rounded-lg cursor-pointer transition-colors">Restore</button>
                                    </form>
                                    <!-- Permanent Delete Action -->
                                    <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>"
                                        onsubmit="return confirm('Permanently delete this entity? This cannot be undone.');">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="action" value="permanent_delete_entity">
                                        <input type="hidden" name="entity_id" value="<?= (int) $arch['id']; ?>">
                                        <button type="submit"
                                            class="px-3 py-1 bg-rose-100 hover:bg-rose-200 text-rose-900 font-bold rounded-lg cursor-pointer transition-colors">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($canReview && $activeReport && $activeStatus !== 'approved'): ?>
        <script>
            document.getElementById('btnRequestChanges').addEventListener('click', function () {
                var box = document.getElementById('requestChangesBox');
                if (box) {
                    if (box.classList.contains('hidden')) {
                        box.classList.remove('hidden');
                        var ta = document.getElementById('verdictRemarks');
                        if (ta) {
                            ta.focus();
                        }
                    } else {
                        box.classList.add('hidden');
                    }
                }
            });

            var confirmBtn = document.getElementById('btnConfirmRequestChanges');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function () {
                    var remarks = document.getElementById('verdictRemarks').value.trim();
                    if (!remarks) {
                        document.getElementById('verdictRemarks').focus();
                        return;
                    }
                    if (confirm('Send this change request to the intern?')) {
                        var form = document.getElementById('verdictForm');
                        form.querySelector('input[name="action"]').value = 'reject';
                        form.submit();
                    }
                });
            }
        </script>
    <?php endif; ?>

    <script>
        // Show loading overlay when week selector changes
        function showWeekLoading(select) {
            var selectedOption = select.options[select.selectedIndex];
            if (!selectedOption || !selectedOption.value) return;
            var weekText = selectedOption.textContent || 'report';
            <?php /* Only roles that actually run the extractor on view should be told
                about it. $showEntities is false exactly where the auto-extract
                call was removed from the controller, so promising "extracting"
                there would be a lie. */ ?>
            <?php if ($showEntities): ?>
                var message = 'Extracting entities from ' + weekText + '. This may take a moment\u2026';
            <?php else: ?>
                var message = 'Opening ' + weekText + '. This may take a moment\u2026';
            <?php endif; ?>

            // Use the existing ICSLoadingOverlay if available
            if (window.ICSLoadingOverlay && window.ICSLoadingOverlay.show) {
                window.ICSLoadingOverlay.show(message);
            } else {
                // Fallback: create simple loading indicator
                var overlay = document.createElement('div');
                overlay.id = 'ics-week-loading-overlay';
                overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;background:rgba(15,23,42,.65);backdrop-filter:blur(4px);font-family:Inter,system-ui,-apple-system,sans-serif;';
                overlay.innerHTML = '<div style="background:#fff;border-radius:1rem;padding:28px 36px;box-shadow:0 20px 45px rgba(2,6,23,.35);display:flex;flex-direction:column;align-items:center;gap:14px;max-width:340px;text-align:center;">' +
                    '<div style="width:44px;height:44px;border-radius:50%;border:4px solid #e2e8f0;border-top-color:#0F2854;animation:spin .8s linear infinite;"></div>' +
                    '<p style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Loading report</p>' +
                    '<p style="font-size:12px;font-weight:600;color:#64748b;margin:0;line-height:1.5;">' + message + '</p>' +
                    '<div style="width:100%;height:4px;border-radius:999px;overflow:hidden;background:#e2e8f0;"><span style="display:block;height:100%;width:40%;border-radius:999px;background:#0F2854;animation:slide 1.1s ease-in-out infinite;"></span></div>' +
                    '</div>';
                document.body.appendChild(overlay);

                // Add keyframes if not present
                if (!document.getElementById('ics-week-loading-style')) {
                    var style = document.createElement('style');
                    style.id = 'ics-week-loading-style';
                    style.textContent = '@keyframes spin{to{transform:rotate(360deg);}}@keyframes slide{0%{margin-left:-40%;}50%{margin-left:60%;}100%{margin-left:100%;}}';
                    document.head.appendChild(style);
                }
            }
        }
    </script>

    <script>
        /*
         * Pick up a finished background extraction.
         *
         * Extraction no longer happens during the page load, so a report that has
         * just been opened renders with whatever entities were already stored and the
         * percentages below can read 0% for a few seconds. Rather than make staff
         * reload by hand, watch api/report_entities_status.php and refresh once the
         * extraction timestamp actually moves.
         *
         * The status endpoint is only consulted when there is something to wait for,
         * so a page for an already-extracted report makes no extra requests at all.
         */
        (function () {
            var REPORT_ID = <?= (int) ($activeReport['id'] ?? 0); ?>;
            var BASELINE = <?= json_encode($icsExtractionStatus['extracted_at'] ?? null); ?>;
            var PENDING = <?= $icsExtractionPending ? 'true' : 'false'; ?>;
            var ERROR = <?= json_encode($icsExtractionError); ?>;

            if (!REPORT_ID || (!PENDING && !ERROR)) {
                return;
            }

            var POLL_INTERVAL_MS = 4000;
            // Give up eventually rather than poll forever if the worker never lands.
            // The page stays usable either way; the banner just stops claiming work
            // is in progress once it reloads.
            var MAX_WAIT_MS = 5 * 60 * 1000;
            var startedAt = Date.now();

            /*
             * Never reload out from under someone who is mid-edit. A refresh here
             * would discard a half-typed entity name in the Add Missing Entity modal
             * or a half-written supervisor remark, and those are worse to lose than a
             * few seconds of stale percentages.
             */
            function userIsEditing() {
                var modalIds = ['addEntityModal', 'archiveModal'];

                for (var i = 0; i < modalIds.length; i++) {
                    var modal = document.getElementById(modalIds[i]);
                    if (modal && !modal.classList.contains('hidden')) {
                        return true;
                    }
                }

                var active = document.activeElement;

                if (active && /^(INPUT|TEXTAREA|SELECT)$/.test(active.tagName)) {
                    // Hidden inputs never take focus, but be explicit anyway so a
                    // future hidden field cannot wedge the poller off permanently.
                    return active.type !== 'hidden';
                }

                return false;
            }

            function poll() {
                if (Date.now() - startedAt > MAX_WAIT_MS) {
                    return;
                }

                if (!userIsEditing()) {
                    fetch('<?= BASE_PATH; ?>/api/report_entities_status.php?report_id=' + REPORT_ID, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    })
                        .then(function (response) {
                            return response.ok ? response.json() : null;
                        })
                        .then(function (data) {
                            if (!data) {
                                return;
                            }

                            // A failure that appeared while the page was open.
                            if (data.error && !ERROR) {
                                window.location.reload();
                                return;
                            }

                            // The timestamp moved, so the entities on screen are stale.
                            if (data.extracted_at !== BASELINE) {
                                window.location.reload();
                            }
                        })
                        .catch(function () {
                            // A dropped request is not worth reporting. The next tick
                            // tries again and the worker is unaffected.
                        });
                }

                setTimeout(poll, POLL_INTERVAL_MS);
            }

            setTimeout(poll, POLL_INTERVAL_MS);
        })();
    </script>
</body>

</html>