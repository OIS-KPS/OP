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
 *   $sidebarPath        sidebar component to include
 *   $pageTitle          <title> suffix
 *
 * The workspace data itself comes from reportWorkspaceBuild() in
 * src/services/report_workspace.php and is exposed here as the same variable
 * names the coordinator controller has always used.
 */

require_once __DIR__ . '/../../services/report_workspace.php';

$viewContext = $viewContext ?? 'coordinator';
$selfPath = $selfPath ?? 'view_report.php';
$backUrl = $backUrl ?? 'reports.php';
$canManageEntities = $canManageEntities ?? false;
$canExportCsv = $canExportCsv ?? false;
$canReview = $canReview ?? false;
$showItPercentages = $showItPercentages ?? false;
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

$activeWeekNumber = $activeReport ? (int)$activeReport['week_number'] : null;
$activeStatus = strtolower((string)($activeReport['status'] ?? 'pending'));
$activeApprovedAt = !empty($activeReport['approved_at'])
    ? date("M d, Y g:i A", strtotime($activeReport['approved_at']))
    : (!empty($activeReport['updated_at']) && $activeStatus === 'approved'
        ? date("M d, Y g:i A", strtotime($activeReport['updated_at']))
        : 'Pending');
$activeSubmittedAt = !empty($activeReport['submitted_at'])
    ? date("M d, Y g:i A", strtotime($activeReport['submitted_at']))
    : 'N/A';
$activeRemarks = trim((string)($activeReport['supervisor_remarks'] ?? ''));

$workspaceQuery = $viewContext === 'coordinator'
    ? $selfPath . '?student_id=' . $studentId
    : $selfPath;
$archiveFormAction = $selfPath . ($viewContext === 'coordinator' ? '?student_id=' . $studentId : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle); ?> &ndash; <?= htmlspecialchars($student['student_name'] ?? 'Student'); ?> - OJT Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
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
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black">&check;</span>
                            <span><?= htmlspecialchars($_SESSION['flash_success']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 font-black hover:text-emerald-950 cursor-pointer">&times;</button>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="bg-rose-50 border border-rose-300 text-rose-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-700 font-black">&times;</span>
                            <span><?= htmlspecialchars($_SESSION['flash_error']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 font-black hover:text-rose-950 cursor-pointer">&times;</button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['review_message'])): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs p-4 rounded-2xl font-bold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black">&check;</span>
                            <span><?= htmlspecialchars($_SESSION['review_message']); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 font-black hover:text-emerald-950 cursor-pointer">&times;</button>
                    </div>
                    <?php unset($_SESSION['review_message']); ?>
                <?php endif; ?>

                <!-- Student Profile Banner Header -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <a href="<?= htmlspecialchars($backUrl); ?>" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl text-slate-800 transition-colors text-xs font-bold shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                <span>&larr;</span>
                                <span>Back</span>
                            </a>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-sm shrink-0 overflow-hidden border border-slate-300">
                            <?php if (!empty($student['student_avatar'])): ?>
                                <img src="<?= htmlspecialchars($student['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar">
                            <?php else: ?>
                                <?= strtoupper(substr($student['student_name'] ?? 'S', 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-0.5">
                            <h1 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight leading-snug">
                                <?= htmlspecialchars($student['student_name'] ?? 'Student'); ?>
                            </h1>
                            <p class="text-xs font-semibold text-slate-600">
                                ID: <?= htmlspecialchars($student['student_number'] ?? '—'); ?> &bull; Section <?= htmlspecialchars($student['section'] ?? '—'); ?> &bull; <strong class="text-slate-900"><?= htmlspecialchars($student['company_name'] ?? 'Host Company'); ?></strong>
                            </p>
                        </div>
                    </div>

                    <!-- Right Side: Active Inspection Badge & Export Button Below It -->
                    <div class="flex flex-col items-end gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-50 text-[#0F2854] border border-blue-200 font-bold text-xs shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Active Inspection: Week <?= $activeWeekNumber ?? '—'; ?>
                        </span>
                        <?php if ($canExportCsv): ?>
                            <a href="<?= htmlspecialchars($workspaceQuery . '&export=csv'); ?>" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl transition-colors text-xs font-bold shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                <span>Export CSV Summary</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 7/5 Balanced Grid Split Layout -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                    <!-- Left Column: Chronological WAR Timeline & Full-Size PDF Preview (7 of 12 columns) -->
                    <div class="xl:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col h-[calc(100vh-210px)] min-h-[780px]">
                        <div class="p-4 sm:p-5 border-b border-slate-200/70 flex items-center justify-between bg-slate-50/60 shrink-0">
                            <div>
                                <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">WAR Timeline &amp; Document Viewer</h2>
                                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Select a week category below to inspect its PDF</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[10px] font-bold text-slate-700 shadow-2xs">
                                <?= count($reportsList); ?> Weeks Total
                            </span>
                        </div>

                        <!-- Week Selector Category Dropdown Bar (Clean & Separated) -->
                        <div class="p-4 bg-slate-100/80 border-b border-slate-200/70 flex items-center gap-3 shrink-0">
                            <form method="GET" action="<?= htmlspecialchars($selfPath); ?>" class="flex items-center gap-3 w-full text-xs">
                                <?php if ($viewContext === 'coordinator'): ?>
                                    <input type="hidden" name="student_id" value="<?= (int)$studentId; ?>">
                                <?php endif; ?>
                                <div class="flex-1">
                                    <select name="report_id" onchange="this.form.submit()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                        <option value="" disabled <?= $activeReport ? '' : 'selected'; ?>>-- Select Submitted Week Report --</option>
                                        <?php foreach ($reportsList as $rep):
                                            $isActive = $activeReport && (int)$rep['id'] === (int)$rep['id'];
                                        ?>
                                            <option value="<?= (int)$rep['id']; ?>" <?= $isActive ? 'selected' : ''; ?>>
                                                Week <?= (int)$rep['week_number']; ?> [<?= htmlspecialchars(ucfirst((string)$rep['status'])); ?>]
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>

                        <!-- Full-Size Embedded PDF Viewer Stage for Active Report -->
                        <div class="flex-1 min-h-0 bg-[#0F172A] relative flex flex-col">
                            <?php if ($activeReport && $pdfUrl !== ''): ?>
                                <div class="px-4 py-2.5 bg-slate-900 border-b border-slate-800 flex flex-wrap items-center justify-between text-xs font-bold text-slate-200 shrink-0 gap-2">
                                    <span class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        Week <?= (int)$activeReport['week_number']; ?> Report Document
                                    </span>

                                    <!-- Separated Submission & Approval Timestamp Badges -->
                                    <div class="flex items-center gap-2 text-[11px] font-medium">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700">
                                            &#128229; Sub: <strong class="text-white"><?= htmlspecialchars($activeSubmittedAt); ?></strong>
                                        </span>
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700">
                                            &#9989; Appr: <strong class="text-emerald-400"><?= htmlspecialchars($activeApprovedAt); ?></strong>
                                        </span>
                                    </div>

                                    <a href="<?= htmlspecialchars($pdfUrl); ?>" target="_blank" rel="noopener" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-[11px] shadow-xs flex items-center gap-1 shrink-0">
                                        <span>Open Fullscreen &nearr;</span>
                                    </a>
                                </div>
                                <div class="flex-1 min-h-0 relative w-full h-full bg-slate-950">
                                    <embed src="<?= htmlspecialchars($pdfUrl); ?>#navpanes=0&view=FitH" type="application/pdf" class="w-full h-full border-0">
                                </div>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs p-6 text-center space-y-2">
                                    <div class="w-12 h-12 bg-slate-800 rounded-2xl flex items-center justify-center mx-auto text-xl">&#128193;</div>
                                    <p class="font-bold text-slate-200">No Report Selected</p>
                                    <p class="text-[11px] text-slate-400">Choose a week above to preview its submitted document.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Column: Aggregated Analytics & Active Week Entities (5 of 12 columns) -->
                    <div class="xl:col-span-5 flex flex-col h-[calc(100vh-210px)] min-h-[780px] space-y-5">

                        <!-- Active Week Review Verdict (supervisor only) -->
                        <?php if ($canReview && $activeReport): ?>
                            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 space-y-3 shrink-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">Review Verdict</h3>
                                    <?php if ($activeStatus === 'approved'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Approved
                                        </span>
                                    <?php elseif ($activeStatus === 'rejected'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Needs Changes
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Waiting for Review
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($activeStatus !== 'approved'): ?>
                                    <form method="POST" action="<?= htmlspecialchars($selfPath . '?student_id=' . $studentId . '&report_id=' . (int)$activeReport['id']); ?>" class="space-y-3" id="verdictForm">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="report_id" value="<?= (int)$activeReport['id']; ?>">
                                        <div class="space-y-1.5">
                                            <label for="verdictRemarks" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">Revision Instructions <span class="normal-case font-semibold text-slate-400">(required to request changes)</span></label>
                                            <textarea id="verdictRemarks" name="supervisor_remarks" rows="2" placeholder="Explain the corrections or missing items the intern needs to address..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-[#0F2854]"></textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" id="btnRequestChanges" class="px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-900 text-xs font-bold rounded-xl border border-rose-300 transition-all cursor-pointer">Request Changes</button>
                                            <button type="submit" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-colors shadow-xs cursor-pointer">Approve Report</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <p class="text-[11px] font-semibold text-slate-500">Approved <?= htmlspecialchars($activeApprovedAt); ?>. This submission is locked for editing.</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Supervisor Feedback Banner -->
                        <?php if ($activeReport && $activeRemarks !== ''): ?>
                            <div class="bg-rose-50 border border-rose-200 text-rose-950 rounded-2xl p-4 shadow-2xs shrink-0">
                                <p class="text-[10px] font-black uppercase tracking-wider text-rose-700">Supervisor Feedback</p>
                                <p class="text-xs font-medium leading-relaxed mt-1"><?= htmlspecialchars($activeRemarks); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Overall IT Percentage Ratio Card -->
                        <?php if ($showItPercentages): ?>
                        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs space-y-3 shrink-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">Overall IT Percentage</h3>
                                <span class="text-[11px] font-bold text-slate-500">
                                    <?= count($allEntities); ?> Total Entities (All Weeks)
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-[#0F2854] flex items-center gap-1.5">
                                        <span>&#128187;</span> IT Related: <?= $itPct; ?>%
                                    </span>
                                    <span class="text-rose-700 flex items-center gap-1.5">
                                        <span>&#128193;</span> Clerical: <?= $clericalPct; ?>%
                                    </span>
                                </div>
                                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex border border-slate-200 shadow-inner">
                                    <div class="bg-[#0F2854] h-full transition-all duration-300" style="width: <?= $itPct; ?>%"></div>
                                    <div class="bg-rose-500 h-full transition-all duration-300" style="width: <?= $clericalPct; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Active Week Entities & Manual Add Form -->
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex-1 flex flex-col min-h-0">
                            <div class="p-4 border-b border-slate-200/70 bg-slate-50/60 shrink-0 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="font-extrabold text-xs text-slate-950 uppercase tracking-wider">
                                            Week <?= $activeWeekNumber ?? '—'; ?> Entities
                                        </h2>
                                        <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Extracted Entities<?= $canManageEntities ? ' &amp; manual additions' : ''; ?></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <?php if ($canManageEntities): ?>
                                            <button type="button" onclick="document.getElementById('archiveModal').classList.remove('hidden')" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-xl text-xs transition-colors cursor-pointer">
                                                Archive (<?= count($archivedEntities); ?>)
                                            </button>
                                            <button type="button" onclick="document.getElementById('addEntityModal').classList.remove('hidden')" class="px-3 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors shadow-2xs cursor-pointer">
                                                + Add Entity
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- INDIVIDUAL ACTIVE WEEK PERCENTAGE BAR -->
                                <?php if ($showItPercentages && $weekTotalCount > 0): ?>
                                    <div class="pt-3 border-t border-slate-200 space-y-1.5">
                                        <div class="flex items-center justify-between text-[11px] font-bold">
                                            <span class="text-[#0F2854]">Week <?= (int)$activeReport['week_number']; ?> IT: <?= $weekItPct; ?>%</span>
                                            <span class="text-rose-700">Clerical: <?= $weekClericalPct; ?>%</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden flex shadow-inner">
                                            <div class="bg-[#0F2854] h-full transition-all duration-300" style="width: <?= $weekItPct; ?>%"></div>
                                            <div class="bg-rose-500 h-full transition-all duration-300" style="width: <?= $weekClericalPct; ?>%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Entities List for Selected Week -->
                            <div class="p-4 space-y-3 overflow-y-auto flex-1">
                                <?php if (empty($weekEntities)): ?>
                                    <div class="py-12 text-center text-slate-400 space-y-1">
                                        <div class="text-2xl">&#128269;</div>
                                        <p class="font-bold text-slate-700 text-xs">No entities for this week</p>
                                        <p class="text-[11px] text-slate-500 max-w-xs mx-auto"><?= $canManageEntities ? 'Click "+ Add Entity" above to manually record any missed keywords.' : 'No technical keywords were extracted from this document.'; ?></p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($weekEntities as $ent):
                                        $currentType = $ent['activity_type'] ?? 'Software';
                                        $itFlag = strtolower(trim((string)($ent['it_related'] ?? 'unknown')));
                                        [$itBadgeLabel, $itBadgeClass] = match ($itFlag) {
                                            'yes' => ['IT Related', 'text-emerald-800 bg-emerald-50 border-emerald-200'],
                                            'no' => ['Non-IT', 'text-slate-700 bg-slate-100 border-slate-300'],
                                            default => ['Unclassified', 'text-amber-800 bg-amber-50 border-amber-200'],
                                        };
                                    ?>
                                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-300 space-y-2 shadow-2xs hover:border-slate-400 transition-colors">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="font-extrabold text-slate-950 text-xs"><?= htmlspecialchars((string)$ent['entity_name']); ?></p>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <?php if ($canManageEntities): ?>
                                                        <?php if (isset($ent['confidence_score']) && $ent['confidence_score'] !== null): ?>
                                                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                                <?= number_format((float)$ent['confidence_score'], 1); ?>% Conf.
                                                            </span>
                                                        <?php endif; ?>
                                                        <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>" onsubmit="return confirm('Archive this entity?');">
                                                            <input type="hidden" name="action" value="delete_entity">
                                                            <input type="hidden" name="report_id" value="<?= $activeReport ? (int)$activeReport['id'] : 0; ?>">
                                                            <input type="hidden" name="entity_id" value="<?= (int)$ent['id']; ?>">
                                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold p-1 cursor-pointer" title="Archive">&times;</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded border <?= $itBadgeClass; ?>">
                                                            <?= $itBadgeLabel; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Reclassification Form (coordinator only) -->
                                            <?php if ($canManageEntities): ?>
                                                <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>" class="flex items-center gap-2 pt-1 border-t border-slate-200/80">
                                                    <input type="hidden" name="action" value="update_entity_type">
                                                    <input type="hidden" name="report_id" value="<?= $activeReport ? (int)$activeReport['id'] : 0; ?>">
                                                    <input type="hidden" name="entity_id" value="<?= (int)$ent['id']; ?>">
                                                    <select name="activity_type" onchange="this.form.submit()" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1 text-[11px] font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                                                        <option value="Software" <?= $currentType === 'Software' ? 'selected' : ''; ?>>Software (Technical)</option>
                                                        <option value="Hardware" <?= $currentType === 'Hardware' ? 'selected' : ''; ?>>Hardware (Technical)</option>
                                                        <option value="Clerical" <?= $currentType === 'Clerical' ? 'selected' : ''; ?>>Clerical (Non-IT)</option>
                                                        <option value="Other" <?= $currentType === 'Other' ? 'selected' : ''; ?>>Other</option>
                                                    </select>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                </div>

            </main>
        </div>
    </div>

    <!-- Add Missing Entity Modal (coordinator only) -->
    <?php if ($canManageEntities): ?>
    <div id="addEntityModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-slate-950">Add Missing Entity / Keyword</h3>
                <button type="button" onclick="document.getElementById('addEntityModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-sm cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>" class="space-y-4 text-xs">
                <input type="hidden" name="action" value="add_entity">
                <input type="hidden" name="report_id" value="<?= $activeReport ? (int)$activeReport['id'] : 0; ?>">

                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Entity / Tool Name</label>
                    <input type="text" name="entity_name" required placeholder="e.g. Python, Docker, Excel" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854]">
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Category</label>
                    <select name="category" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                        <option value="Programming" selected>Programming</option>
                        <option value="Database">Database</option>
                        <option value="Development Tool">Development Tool</option>
                        <option value="Office Software">Office Software</option>
                        <option value="Design Software">Design Software</option>
                        <option value="Operating System">Operating System</option>
                        <option value="Hardware Task">Hardware Task</option>
                        <option value="Networking">Networking</option>
                        <option value="Administrative Task">Administrative Task</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Classification / Activity Type</label>
                    <select name="classification" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] cursor-pointer">
                        <option value="Software">Software (Technical - IT Related)</option>
                        <option value="Hardware">Hardware (Technical - IT Related)</option>
                        <option value="Clerical">Clerical (Non-IT)</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2.5 pt-2">
                    <button type="button" onclick="document.getElementById('addEntityModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl transition-colors cursor-pointer shadow-xs">Save Entity</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Archive / Ignored Keywords Modal (coordinator only) -->
    <div id="archiveModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4 max-h-[80vh] flex flex-col">
            <div class="flex items-center justify-between shrink-0">
                <h3 class="font-extrabold text-sm text-slate-950">Archived / Ignored Entities</h3>
                <button type="button" onclick="document.getElementById('archiveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-sm cursor-pointer">&times;</button>
            </div>

            <div class="space-y-2.5 overflow-y-auto flex-1 pr-1">
                <?php if (empty($archivedEntities)): ?>
                    <div class="py-10 text-center text-slate-400 text-xs">No archived entities found.</div>
                <?php else: ?>
                    <?php foreach ($archivedEntities as $arch): ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-300 flex items-center justify-between gap-2 text-xs">
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Week <?= (int)$arch['week_number']; ?></span>
                                <p class="font-extrabold text-slate-950"><?= htmlspecialchars((string)$arch['entity_name']); ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <!-- Restore Action -->
                                <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>">
                                    <input type="hidden" name="action" value="restore_entity">
                                    <input type="hidden" name="entity_id" value="<?= (int)$arch['id']; ?>">
                                    <button type="submit" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-bold rounded-lg cursor-pointer transition-colors">Restore</button>
                                </form>
                                <!-- Permanent Delete Action -->
                                <form method="POST" action="<?= htmlspecialchars($archiveFormAction); ?>" onsubmit="return confirm('Permanently delete this entity? This cannot be undone.');">
                                    <input type="hidden" name="action" value="permanent_delete_entity">
                                    <input type="hidden" name="entity_id" value="<?= (int)$arch['id']; ?>">
                                    <button type="submit" class="px-3 py-1 bg-rose-100 hover:bg-rose-200 text-rose-900 font-bold rounded-lg cursor-pointer transition-colors">Delete</button>
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
            var remarks = document.getElementById('verdictRemarks').value.trim();
            if (!remarks) {
                document.getElementById('verdictRemarks').focus();
                return;
            }
            if (confirm('Send this change request to the intern?')) {
                this.form.querySelector('input[name="action"]').value = 'reject';
                this.form.submit();
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
