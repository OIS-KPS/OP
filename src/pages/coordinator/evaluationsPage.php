<!-- src/pages/coordinator/evaluationsPage.php -->
<?php

date_default_timezone_set('Asia/Manila');

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Final Evaluations - Coordinator Portal</title>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="<?= BASE_PATH; ?>/public/css/style.css"
    >

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">

        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Workspace -->
            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Flash Alert -->
                <?php if (!empty($flashSuccess)): ?>

                    <div class="bg-blue-50 border border-[#0F2854]/30 text-[#0F2854] px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">

                        <p class="font-extrabold text-xs">
                            <?= e($flashSuccess); ?>
                        </p>

                        <a
                            href="evaluations.php"
                            class="text-xs font-bold text-[#0F2854] hover:underline"
                        >
                            Dismiss
                        </a>

                    </div>

                <?php endif; ?>


                <?php if (!empty($flashError)): ?>

                    <div class="bg-rose-50 border border-rose-300 text-rose-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">

                        <p class="font-extrabold text-xs">
                            <?= e($flashError); ?>
                        </p>

                        <a
                            href="evaluations.php"
                            class="text-xs font-bold text-rose-800 hover:underline"
                        >
                            Dismiss
                        </a>

                    </div>

                <?php endif; ?>


                <!-- Page Header with bulk export -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">Performance Evaluations</h1>
                        <p class="text-xs font-semibold text-slate-600 mt-1">
                            Supervisor-signed OJT evaluation forms.
                        </p>
                    </div>

                    <?php if ((int)($completedCount ?? 0) > 0): ?>
                        <a
                            href="export_evaluation.php?bulk=1&amp;download=1"
                            class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer shrink-0"
                            title="Download every signed evaluation as one PDF"
                        >
                            <svg class="w-3.5 h-3.5 text-blue-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            <span>Export All Signed (<?= (int)$completedCount; ?>)</span>
                        </a>
                    <?php endif; ?>
                </div>


                <!-- 1. Top Stat Cards

                     Programme-wide totals: these ignore the filters below, and
                     $completedCount also drives the "Export All Signed" button
                     above. Labelled so they are not misread as filter-aware. -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                    <!-- Total -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">

                        <div class="space-y-1">

                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                Total Interns
                            </span>

                            <p class="text-3xl font-black text-slate-950">
                                <?= (int)$totalCount; ?>
                            </p>

                            <p class="text-xs font-semibold text-slate-600 pt-0.5">
                                All enrolled students
                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-[#0F2854] flex items-center justify-center border border-slate-300 shrink-0">

                            <svg
                                class="w-6 h-6 text-[#0F2854]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                                />
                            </svg>

                        </div>

                    </div>


                    <!-- Completed -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">

                        <div class="space-y-1">

                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                Completed
                            </span>

                            <p class="text-3xl font-black text-[#0F2854]">
                                <?= (int)$completedCount; ?>
                            </p>

                            <p class="text-xs font-semibold text-slate-600 pt-0.5">
                                Signed across all course codes
                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#0F2854] flex items-center justify-center border border-[#0F2854]/30 shrink-0">

                            <svg
                                class="w-6 h-6 text-[#0F2854]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>

                        </div>

                    </div>


                    <!-- Pending -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">

                        <div class="space-y-1">

                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                Pending
                            </span>

                            <p class="text-3xl font-black text-slate-700">
                                <?= (int)$pendingCount; ?>
                            </p>

                            <p class="text-xs font-semibold text-slate-600 pt-0.5">
                                Awaiting supervisor across all course codes
                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-300 shrink-0">

                            <svg
                                class="w-6 h-6 text-slate-500"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>

                        </div>

                    </div>

                </div>


                <!-- 2. Unified Evaluations Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">

                    <!-- Header -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">

                        <div>

                            <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">
                                Final Evaluations
                            </h1>

                            <p class="text-xs font-semibold text-slate-600 mt-1">
                                Supervisor ratings, performance grades, and verified sign-off records.
                            </p>

                        </div>


                        <div class="flex items-center gap-3 shrink-0">

                            <!-- Bulk Trigger -->
                            <button
                                type="button"
                                onclick="openTriggerModal()"
                                class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 cursor-pointer"
                            >

                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 4.5v15m7.5-7.5h-15"
                                    />
                                </svg>

                                <span>
                                    Bulk Trigger Evaluations
                                </span>

                            </button>


                            <!-- Editable evaluation form: the 12 competency questions -->
                            <button
                                type="button"
                                onclick="openEvaluationFormEditor()"
                                class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-xs transition-all inline-flex items-center gap-2 cursor-pointer"
                            >

                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"
                                    />
                                </svg>

                                <span>
                                    Update Evaluation Form
                                </span>

                            </button>


                            <span
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs"
                                title="Interns matching the filters below, across every page"
                            >

                                <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>

                                <?= (int)$totalFiltered; ?>

                                <?= (int)$totalFiltered === 1 ? 'Record' : 'Records'; ?>

                            </span>

                        </div>

                    </div>


                    <!-- Filter Toolbar -->
                    <div class="p-4 border-b border-slate-200/70 bg-white">

                        <form
                            id="filterForm"
                            method="GET"
                            action="evaluations.php"
                            class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs"
                        >

                            <!-- Search -->
                            <div class="relative flex-1 w-full">

                                <svg
                                    class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                                    />
                                </svg>

                                <input
                                    type="text"
                                    id="searchInput"
                                    name="search"
                                    value="<?= e($searchQuery ?? ''); ?>"
                                    placeholder="Search all interns by name or ID..."
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                                >

                            </div>


                            <!-- Course Code -->
                            <div class="w-full md:w-36">

                                <select
                                    name="section"
                                    onchange="this.form.submit()"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer"
                                >

                                    <option
                                        value="all"
                                        <?= ($selectedSection ?? 'all') === 'all' ? 'selected' : ''; ?>
                                    >
                                        All Course Codes
                                    </option>

                                    <?php foreach ($activeSections as $sec): ?>

                                        <option
                                            value="<?= e($sec); ?>"
                                            <?= ($selectedSection ?? '') === $sec ? 'selected' : ''; ?>
                                        >
                                            <?= e($sec); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- Company -->
                            <div class="w-full md:w-48">

                                <select
                                    name="company_id"
                                    onchange="this.form.submit()"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer"
                                >

                                    <option
                                        value="all"
                                        <?= ($selectedCompany ?? 'all') === 'all' ? 'selected' : ''; ?>
                                    >
                                        All Companies
                                    </option>

                                    <?php foreach ($companiesList as $comp): ?>

                                        <option
                                            value="<?= $comp['id']; ?>"
                                            <?= ($selectedCompany ?? 'all') == $comp['id'] ? 'selected' : ''; ?>
                                        >
                                            <?= e($comp['name']); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- Status -->
                            <div class="w-full md:w-32">

                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors cursor-pointer"
                                >

                                    <option
                                        value="all"
                                        <?= ($selectedStatus ?? 'all') === 'all' ? 'selected' : ''; ?>
                                    >
                                        All Statuses
                                    </option>

                                    <option
                                        value="Completed"
                                        <?= ($selectedStatus ?? '') === 'Completed' ? 'selected' : ''; ?>
                                    >
                                        Completed
                                    </option>

                                    <option
                                        value="Pending"
                                        <?= ($selectedStatus ?? '') === 'Pending' ? 'selected' : ''; ?>
                                    >
                                        Pending
                                    </option>

                                </select>

                            </div>


                            <!-- Reset -->
                            <?php if (
                                !empty($searchQuery) ||
                                ($selectedSection ?? 'all') !== 'all' ||
                                ($selectedCompany ?? 'all') !== 'all' ||
                                ($selectedStatus ?? 'all') !== 'all'
                            ): ?>

                                <div class="w-full md:w-auto">

                                    <a
                                        href="evaluations.php"
                                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl border border-slate-300 transition-colors inline-block text-center whitespace-nowrap"
                                    >
                                        Reset
                                    </a>

                                </div>

                            <?php endif; ?>

                        </form>

                    </div>


                    <!-- Evaluation Table -->
                    <?php if (!empty($filteredEvals)): ?>

                        <!-- Real table markup inside a horizontal scroller. The previous version
                             used display:block/flex on thead/tbody/tr, which takes
                             the rows out of table layout and loses column
                             alignment. The min-width keeps columns readable on
                             narrow screens by scrolling rather than squashing. -->
                        <div class="w-full overflow-x-auto">

                            <table class="w-full min-w-[900px] text-left border-collapse text-xs">

                                <!-- Widths live here, once, instead of being repeated on
                                     every th and every td. -->
                                <colgroup>
                                    <col class="w-[22%]">
                                    <col class="w-[10%]">
                                    <col class="w-[20%]">
                                    <col class="w-[12%]">
                                    <col class="w-[14%]">
                                    <col class="w-[22%]">
                                </colgroup>

                                <!-- No sticky header here on purpose. The card clips with overflow-hidden and
                                     the wrapper above is only a horizontal scroller, so neither
                                     gives the thead a vertical scroll context to pin to -- the
                                     page scrolls instead, and a sticky row would scroll away
                                     with it. Pagination bounds the table to $perPage rows,
                                     which is what keeps the header in view. -->
                                <thead>

                                    <tr class="bg-slate-100/90 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">

                                        <th class="py-3 px-3">
                                            Student Intern
                                        </th>

                                        <th class="py-3 px-2">
                                            Course Code
                                        </th>

                                        <th class="py-3 px-3">
                                            Host Company &amp; Supervisor
                                        </th>

                                        <th class="py-3 px-2 text-center">
                                            Approved Reports
                                        </th>

                                        <th class="py-3 px-2">
                                            Score &amp; Grade
                                        </th>

                                        <th class="py-3 px-3 text-right">
                                            Action
                                        </th>

                                    </tr>

                                </thead>


                                <tbody class="divide-y divide-slate-200/80 text-slate-800">

                                    <?php foreach ($filteredEvals as $eval):

                                        $isCompleted = ($eval['status'] === 'Completed');

                                        $isTriggered = !empty($eval['evaluation_triggered']);

                                    ?>

                                        <tr class="hover:bg-slate-50 transition-colors">


                                            <!-- Student -->
                                            <td class="py-3 px-3 align-middle">

                                                <div class="flex items-center gap-2.5">

                                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">

                                                        <?php if (!empty($eval['student_avatar'])): ?>

                                                            <img
                                                                src="<?= e($eval['student_avatar']); ?>"
                                                                class="w-full h-full object-cover"
                                                                alt="Avatar"
                                                            >

                                                        <?php else: ?>

                                                            <?= strtoupper(substr($eval['student_name'] ?? 'S', 0, 1)); ?>

                                                        <?php endif; ?>

                                                    </div>


                                                    <div class="min-w-0">

                                                        <p class="font-extrabold text-slate-950 text-xs truncate">
                                                            <?= e($eval['student_name']); ?>
                                                        </p>

                                                        <p class="text-[10px] text-slate-600 font-semibold truncate">
                                                            ID:
                                                            <?= e($eval['student_number'] ?? 'N/A'); ?>
                                                            &bull;
                                                            <?= e($eval['program'] ?? 'BSIT'); ?>
                                                        </p>

                                                    </div>

                                                </div>

                                            </td>


                                            <!-- Course Code -->
                                            <td class="py-3 px-2 align-middle">

                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">

                                                    <?= e($eval['section'] ?? 'IT44A'); ?>

                                                </span>

                                            </td>


                                            <!-- Company -->
                                            <td class="py-3 px-3 align-middle">

                                                <div class="min-w-0">

                                                    <p class="font-bold text-slate-900 text-xs truncate">
                                                        <?= e($eval['company_name'] ?? 'Unassigned'); ?>
                                                    </p>

                                                    <p class="text-[11px] text-slate-600 font-medium truncate mt-0.5">
                                                        Office:
                                                        <?= e($eval['office_name'] ?? 'Unassigned'); ?>
                                                    </p>

                                                    <p class="text-[11px] text-slate-600 font-medium truncate mt-0.5">

                                                        Sup:
                                                        <?= e($eval['supervisor_name'] ?? 'Pending Assignment'); ?>

                                                    </p>

                                                </div>

                                            </td>


                                            <!-- Approved Reports -->
                                            <td class="py-3 px-2 align-middle text-center">

                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-blue-50 text-[#0F2854] border border-[#0F2854]/30 font-black text-[11px]">

                                                    <?= (int)($eval['approved_reports_count'] ?? 0); ?>

                                                    Approved

                                                </span>

                                            </td>


                                            <!-- Score -->
                                            <td class="py-3 px-2 align-middle">

                                                <?php if ($isCompleted && isset($eval['final_score'])): ?>

                                                    <div class="flex items-center gap-1.5">

                                                        <span class="text-xs font-black text-[#0F2854]">

                                                            <?= number_format($eval['final_score'], 2); ?>

                                                            <span class="text-[9px] text-slate-400 font-bold">
                                                                /5
                                                            </span>

                                                        </span>


                                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 text-[#0F2854] text-[10px] font-black border border-[#0F2854]/30">

                                                            <?= e(
                                                                $eval['grade_equivalent']
                                                                ?? ojtRatingLabel($eval['final_score'])
                                                            ); ?>

                                                        </span>

                                                    </div>

                                                <?php else: ?>

                                                    <span class="text-slate-500 text-xs font-semibold italic">
                                                        Pending
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <!-- Action -->
                                            <?php /* The cell is deliberately not truncated: these are
                                                     controls, not text, and clipping them silently swallowed
                                                     the last button once a second one was added. The
                                                     inner div is nowrap so the pair always stays on one line. */ ?>
                                            <td class="py-3 px-3 text-right align-middle">

                                                <?php if ($isCompleted): ?>

                                                    <div class="flex items-center justify-end gap-2 whitespace-nowrap">

                                                        <a
                                                            href="export_evaluation.php?student_id=<?= (int)$eval['student_id']; ?>"
                                                            target="_blank"
                                                            rel="noopener"
                                                            class="px-2.5 py-1 bg-white hover:bg-blue-50 text-[#0F2854] text-[11px] font-bold rounded-xl border border-[#0F2854]/30 shadow-2xs transition-colors inline-flex items-center gap-1"
                                                            title="Open the archived evaluation PDF"
                                                        >
                                                            <svg class="w-3.5 h-3.5 text-[#0F2854] shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                            <span>PDF</span>
                                                        </a>


                                                        <a
                                                            href="evaluation_view.php?id=<?= (int)($eval['eval_id'] ?? 0); ?>"
                                                            class="px-2.5 py-1 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-bold rounded-xl shadow-2xs transition-colors inline-flex items-center gap-1"
                                                            title="Open the evaluation on screen"
                                                        >
                                                            <span>View</span>
                                                        </a>

                                                    </div>


                                                <?php elseif ($isTriggered): ?>

                                                    <!-- Awaiting + Cancel -->
                                                    <div class="flex items-center justify-end gap-2 whitespace-nowrap">

                                                        <span class="px-2.5 py-1 text-[11px] font-bold text-[#0F2854] bg-blue-50 rounded-xl border border-[#0F2854]/30 inline-block">
                                                            Awaiting
                                                        </span>


                                                        <button
                                                            type="button"
                                                            onclick="openCancelEvaluationModal(
                                                                <?= (int)$eval['student_id']; ?>,
                                                                '<?= htmlspecialchars(addslashes($eval['student_name'])); ?>'
                                                            )"
                                                            class="px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-300 transition-colors cursor-pointer"
                                                        >
                                                            Cancel
                                                        </button>

                                                    </div>


                                                <?php elseif (!empty($eval['completion_requested'])): ?>

                                                    <!-- The intern asked for this, so the coordinator is
                                                         answering a request rather than starting an
                                                         evaluation. The modal and the audit entry both
                                                         follow that distinction. -->
                                                    <?php if ($eval['supervisor_name'] === 'Pending Assignment'): ?>

                                                        <span
                                                            class="px-2.5 py-1 text-[11px] font-bold text-amber-700 bg-amber-50 rounded-xl border border-amber-300 inline-block"
                                                            title="Assign a supervisor first"
                                                        >
                                                            Needs Supervisor
                                                        </span>

                                                    <?php else: ?>

                                                        <button
                                                            type="button"
                                                            onclick="openSingleConfirmModal(
                                                                <?= (int)$eval['student_id']; ?>,
                                                                '<?= htmlspecialchars(addslashes($eval['student_name'])); ?>',
                                                                true
                                                            )"
                                                            class="px-2.5 py-1 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-1 cursor-pointer whitespace-nowrap"
                                                        >

                                                            <span>
                                                                Approve Request
                                                            </span>

                                                        </button>

                                                    <?php endif; ?>

                                                <?php elseif ($eval['supervisor_name'] === 'Pending Assignment'): ?>

                                                    <span
                                                        class="px-2.5 py-1 text-[11px] font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-200 inline-block"
                                                        title="Assign a supervisor first"
                                                    >
                                                        N/A
                                                    </span>


                                                <?php else: ?>

                                                    <button
                                                        type="button"
                                                        onclick="openSingleConfirmModal(
                                                            <?= (int)$eval['student_id']; ?>,
                                                            '<?= htmlspecialchars(addslashes($eval['student_name'])); ?>'
                                                        )"
                                                        class="px-2.5 py-1 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-1 cursor-pointer"
                                                    >

                                                        <span>
                                                            Evaluate
                                                        </span>

                                                    </button>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>


                        <!-- Pagination Footer -->
                        <?php if ($totalPages > 1):
                            // Every filter has to ride along, or the pager would drop the
                            // coordinator back onto an unfiltered list mid-search.
                            $baseQuery = 'search=' . urlencode($searchQuery)
                                . '&section=' . urlencode($selectedSection)
                                . '&company_id=' . urlencode($selectedCompany)
                                . '&status=' . urlencode($selectedStatus);

                            $firstShown = $totalFiltered > 0 ? (($page - 1) * $perPage) + 1 : 0;
                            $lastShown  = $totalFiltered > 0 ? min($page * $perPage, $totalFiltered) : 0;
                        ?>
                            <div class="px-4 py-3 border-t border-slate-200/70 bg-slate-50/60 flex flex-col sm:flex-row items-center justify-between gap-3">

                                <p class="text-[11px] font-semibold text-slate-600">
                                    Showing <span class="font-black text-slate-900"><?= $firstShown; ?></span>&ndash;<span class="font-black text-slate-900"><?= $lastShown; ?></span>
                                    of <span class="font-black text-slate-900"><?= (int)$totalFiltered; ?></span>
                                    &middot; Page <?= (int)$page; ?> of <?= (int)$totalPages; ?>
                                </p>

                                <div class="flex items-center gap-2">
                                    <?php if ($page > 1): ?>
                                        <a href="evaluations.php?<?= $baseQuery; ?>&page=<?= $page - 1; ?>"
                                           class="px-3 py-1.5 text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl transition-colors inline-flex items-center gap-1 cursor-pointer">
                                            <span>&lsaquo;</span> Prev
                                        </a>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 text-xs font-bold text-slate-400 bg-white border border-slate-200 rounded-xl inline-flex items-center gap-1 opacity-60">
                                            <span>&lsaquo;</span> Prev
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <a href="evaluations.php?<?= $baseQuery; ?>&page=<?= $page + 1; ?>"
                                           class="px-3 py-1.5 text-xs font-bold text-white bg-[#0F2854] hover:bg-blue-900 border border-[#0F2854] rounded-xl transition-colors inline-flex items-center gap-1 cursor-pointer">
                                            Next <span>&rsaquo;</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 text-xs font-bold text-slate-400 bg-white border border-slate-200 rounded-xl inline-flex items-center gap-1 opacity-60">
                                            Next <span>&rsaquo;</span>
                                        </span>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endif; ?>


                    <?php else: ?>

                        <?php
                        $isFiltered = ($searchQuery !== '')
                            || ($selectedSection !== 'all')
                            || ($selectedCompany !== 'all')
                            || ($selectedStatus !== 'all');
                        ?>

                        <div class="text-center py-16 px-4 space-y-2">

                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📋
                            </div>

                            <?php if ($isFiltered): ?>

                                <h4 class="text-sm font-black text-slate-900">
                                    No matching interns
                                </h4>

                                <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">
                                    No intern on record matches these filters. Try a different search term or reset the list.
                                </p>

                                <a href="evaluations.php" class="inline-block mt-3 px-3.5 py-1.5 text-xs font-bold text-white bg-[#0F2854] hover:bg-blue-900 rounded-xl transition-colors cursor-pointer">
                                    Clear filters
                                </a>

                            <?php else: ?>

                                <h4 class="text-sm font-black text-slate-900">
                                    No interns yet
                                </h4>

                                <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">
                                    Interns appear here once they are enrolled and assigned to a host company.
                                </p>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </main>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- 0. UPDATE EVALUATION FORM (12 competency questions)  -->
    <!-- ========================================================= -->

    <div
        id="evaluationFormModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden overflow-y-auto items-center justify-center z-50 p-4"
    >

        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-3xl w-full overflow-hidden flex flex-col max-h-[90vh]">

            <div class="flex items-start justify-between gap-4 p-5 border-b border-slate-200/70 shrink-0">

                <div>

                    <h3 class="text-sm font-black text-slate-950">
                        Update Evaluation Form
                    </h3>

                    <p class="text-[11px] text-slate-600 font-semibold mt-1">
                        Edit the wording of the 12 competencies on the OJT Performance Evaluation Form.
                        Changes apply to evaluations submitted from now on.
                    </p>

                </div>

                <button
                    type="button"
                    onclick="closeEvaluationFormEditor()"
                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center shrink-0 cursor-pointer"
                    aria-label="Close"
                >
                    &times;
                </button>

            </div>

            <div class="p-3 bg-amber-50 border-b border-amber-200">
                <p class="text-[11px] text-amber-900 font-medium leading-relaxed">
                    <strong>Completed evaluations are not affected.</strong>
                    Each submitted evaluation stores the questions it was answered against, so a signed record keeps its original wording even after you save changes here.
                </p>
            </div>

            <form method="POST" action="evaluations.php" class="flex flex-col min-h-0 flex-1">

                <?= csrf_field() ?>

                <input type="hidden" name="action" value="save_evaluation_questions">

                <div class="overflow-y-auto p-5 space-y-3 flex-1 min-h-0">

                    <?php
                    $evaluationFormDraft  = is_array($evaluationFormDraft ?? null) ? $evaluationFormDraft : [];
                    $evaluationFormErrors = is_array($evaluationFormErrors ?? null) ? $evaluationFormErrors : [];
                    $evaluationGroupLabels = ojtEvaluationGroups();
                    ?>

                    <?php foreach (($evaluationFormCriteria ?? []) as $formIndex => $formCriterion):
                        $formKey      = (string) $formCriterion['key'];
                        $formFieldErr = (string) ($evaluationFormErrors[$formKey] ?? '');
                        $formValue    = (string) ($evaluationFormDraft[$formKey] ?? $formCriterion['text']);
                        ?>
                        <div class="border <?= $formFieldErr !== '' ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200 bg-slate-50/50'; ?> rounded-xl p-3.5">

                            <div class="flex items-start gap-3">

                                <span class="w-6 h-6 rounded-lg bg-[#0F2854] text-white flex items-center justify-center font-black text-[10px] shrink-0 mt-0.5">
                                    <?= $formIndex + 1; ?>
                                </span>

                                <div class="min-w-0 flex-1">

                                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1.5">
                                        <?= htmlspecialchars($evaluationGroupLabels[$formCriterion['group']] ?? $formCriterion['group']); ?>
                                    </p>

                                    <label for="question_<?= htmlspecialchars($formKey); ?>" class="sr-only">
                                        Competency <?= $formIndex + 1; ?> wording
                                    </label>

                                    <textarea
                                        id="question_<?= htmlspecialchars($formKey); ?>"
                                        name="question[<?= htmlspecialchars($formKey); ?>]"
                                        rows="2"
                                        maxlength="1000"
                                        class="w-full bg-white border <?= $formFieldErr !== '' ? 'border-rose-400' : 'border-slate-300'; ?> rounded-lg px-3 py-2 text-[13px] font-medium leading-6 text-slate-800 focus:outline-none focus:border-[#0F2854] transition-colors"
                                    ><?= htmlspecialchars($formValue); ?></textarea>

                                    <?php if ($formFieldErr !== ''): ?>
                                        <p class="mt-1 text-[11px] font-semibold text-rose-700">
                                            <?= htmlspecialchars($formFieldErr); ?>
                                        </p>
                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>

                <div class="flex items-center justify-between gap-3 p-4 border-t border-slate-200/70 bg-slate-50 shrink-0">

                    <p class="text-[11px] text-slate-500 font-semibold">
                        <?= count($evaluationFormCriteria ?? []); ?> competencies &bull; rated 1&ndash;5 on each
                    </p>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="closeEvaluationFormEditor()"
                            class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-300 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-colors cursor-pointer"
                        >
                            Save Evaluation Form
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- 1. BULK TRIGGER EVALUATION MODAL -->
    <!-- ========================================================= -->

    <div
        id="triggerEvalModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden overflow-y-auto items-center justify-center z-50 p-4"
    >

        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full overflow-hidden flex flex-col max-h-[90vh]">


            <!-- Header -->
            <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">

                <div class="flex items-center gap-2">

                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0F2854] flex items-center justify-center font-bold">
                        📋
                    </div>

                    <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">
                        Bulk Trigger Evaluations
                    </h3>

                </div>


                <button
                    type="button"
                    onclick="closeTriggerModal()"
                    class="w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold flex items-center justify-center cursor-pointer"
                >
                    ✕
                </button>

            </div>


            <!-- Form -->
            <form
                method="POST"
                action="evaluations.php"
                class="p-6 space-y-4 text-xs flex flex-col flex-1 overflow-hidden"
            >
                        <?= csrf_field(); ?>

                <input
                    type="hidden"
                    name="action"
                    value="request_evaluation"
                >


                <div class="flex items-center justify-between pb-2 border-b border-slate-100">

                    <span class="font-bold text-slate-700">
                        Select eligible interns:
                    </span>

                    <button
                        type="button"
                        onclick="toggleSelectAll()"
                        class="text-[11px] font-bold text-[#0F2854] hover:underline cursor-pointer"
                        id="selectAllBtn"
                    >
                        Select All
                    </button>

                </div>


                <!-- Students -->
                <div class="overflow-y-auto max-h-60 space-y-2 pr-1 border border-slate-200 rounded-xl p-3 bg-slate-50/50">

                    <?php if (!empty($eligibleStudents)): ?>

                        <?php foreach ($eligibleStudents as $es): ?>

                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 transition-all cursor-pointer">

                                <input
                                    type="checkbox"
                                    name="student_ids[]"
                                    value="<?= $es['id']; ?>"
                                    class="student-checkbox w-4 h-4 rounded text-[#0F2854] focus:ring-[#0F2854] border-slate-300"
                                >

                                <div class="min-w-0">

                                    <p class="font-bold text-slate-900 truncate">
                                        <?= e($es['name']); ?>
                                    </p>

                                    <p class="text-[10px] text-slate-500">
                                        ID:
                                        <?= e($es['student_number']); ?>
                                        &bull;
                                        <?= e($es['section'] ?? 'IT44A'); ?>
                                    </p>

                                </div>

                            </label>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="text-center text-slate-400 py-6 italic">
                            No eligible students available
                            (Must have an assigned supervisor)
                        </p>

                    <?php endif; ?>

                </div>


                <!-- Warning -->
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2 text-amber-900">

                    <svg
                        class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 1.732 1.732 1.732z"
                        />
                    </svg>

                    <span>

                        <strong>
                            Security Notice:
                        </strong>

                        Triggering evaluation will immediately lock out selected students from uploading further Weekly Accomplishment Reports.

                    </span>

                </div>


                <!-- Buttons -->
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">

                    <button
                        type="button"
                        onclick="closeTriggerModal()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl cursor-pointer shadow-xs"
                    >
                        Confirm &amp; Trigger Selected
                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- 2. SINGLE EVALUATION CONFIRMATION MODAL -->
    <!-- ========================================================= -->

    <div
        id="singleConfirmModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden overflow-y-auto items-center justify-center z-50 p-4"
    >

        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full overflow-hidden flex flex-col">


            <!-- Header -->
            <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">

                <div class="flex items-center gap-2">

                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                        🔒
                    </div>

                    <!-- Title, body lead and CTA are rewritten by openSingleConfirmModal(),
                         because this one modal serves both "approve a request"
                         and "start an evaluation" and the two read very
                         differently to the coordinator. -->
                    <h3 id="confirmModalTitle" class="text-xs font-black text-slate-950 uppercase tracking-wider">
                        Confirm Final Evaluation
                    </h3>

                </div>


                <button
                    type="button"
                    onclick="closeSingleConfirmModal()"
                    class="w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold flex items-center justify-center cursor-pointer"
                >
                    ✕
                </button>

            </div>


            <!-- Form -->
            <form
                method="POST"
                action="evaluations.php"
                class="p-6 space-y-4 text-xs"
            >
                        <?= csrf_field(); ?>

                <input
                    type="hidden"
                    name="action"
                    value="request_evaluation"
                >

                <input
                    type="hidden"
                    name="student_ids[]"
                    id="confirmStudentId"
                >


                <p class="text-slate-700 leading-relaxed">

                    <span id="confirmModalLead">
                        Are you sure you want to request a final evaluation for
                    </span>

                    <strong
                        id="confirmStudentName"
                        class="text-slate-950"
                    ></strong>?

                </p>


                <!-- Shown only when approving an intern's own request, because the
                     next step differs: here the supervisor still owes an evaluation. -->
                <p
                    id="confirmModalNote"
                    class="hidden text-[11px] text-[#0F2854] bg-blue-50 border border-[#0F2854]/30 rounded-xl p-3 leading-relaxed"
                >
                    Their supervisor will be asked to complete and sign the evaluation. The intern is not being graded by this step.
                </p>


                <!-- Warning -->
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2 text-amber-900">

                    <svg
                        class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 1.732 1.732 1.732z"
                        />
                    </svg>

                    <span>
                        This action will lock the student's account from submitting further weekly reports.
                    </span>

                </div>


                <!-- Buttons -->
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">

                    <button
                        type="button"
                        onclick="closeSingleConfirmModal()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white font-bold rounded-xl cursor-pointer"
                    >
                        <span id="confirmModalSubmit">
                            Yes, Request Evaluation
                        </span>
                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- 3. CANCEL EVALUATION REQUEST MODAL -->
    <!-- ========================================================= -->

    <div
        id="cancelEvalModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden overflow-y-auto items-center justify-center z-50 p-4"
    >

        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-md w-full overflow-hidden flex flex-col">


            <!-- Header -->
            <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">

                <div class="flex items-center gap-2">

                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center font-bold">
                        !
                    </div>

                    <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">
                        Cancel Evaluation Request
                    </h3>

                </div>


                <button
                    type="button"
                    onclick="closeCancelEvaluationModal()"
                    class="w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold flex items-center justify-center cursor-pointer"
                >
                    ✕
                </button>

            </div>


            <!-- Cancel Form -->
            <form
                method="POST"
                action="evaluations.php"
                class="p-6 space-y-4 text-xs"
            >
                        <?= csrf_field(); ?>

                <input
                    type="hidden"
                    name="action"
                    value="cancel_evaluation"
                >

                <input
                    type="hidden"
                    name="student_id"
                    id="cancelStudentId"
                >


                <p class="text-slate-700 leading-relaxed">

                    Are you sure you want to cancel the pending evaluation request for

                    <strong
                        id="cancelStudentName"
                        class="text-slate-950"
                    ></strong>?

                </p>


                <!-- Warning -->
                <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 flex items-start gap-2 text-rose-900">

                    <svg
                        class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77-1.333-1.732-1.732 1.732-1.732z"
                        />
                    </svg>

                    <span>
                        The pending evaluation request will be cancelled. The student will become eligible for another evaluation request.
                    </span>

                </div>


                <!-- Buttons -->
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">

                    <button
                        type="button"
                        onclick="closeCancelEvaluationModal()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer"
                    >
                        Keep Request
                    </button>


                    <button
                        type="submit"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl cursor-pointer shadow-xs"
                    >
                        Yes, Cancel Request
                    </button>

                </div>

            </form>

        </div>

    </div>


    <script>
        /* =====================================================
           SEARCH

           Server-side, because the list is now paginated: a JS filter would
           only ever see the 25 rows on the current page and would silently
           hide matches sitting on page 2. The input debounces a real GET
           submit instead, matching coordinator/audit_logs.php.
        ===================================================== */

        const evalFilterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');

        if (evalFilterForm && searchInput) {
            let evalSearchTimer = null;

            // The form carries no hidden "page" input, so a submit always lands
            // on page 1 -- a filter change correctly restarts at the top.
            searchInput.addEventListener('input', function () {
                clearTimeout(evalSearchTimer);
                evalSearchTimer = setTimeout(function () {
                    evalFilterForm.submit();
                }, 350);
            });

            // Enter submits immediately rather than waiting out the debounce.
            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    clearTimeout(evalSearchTimer);
                    evalFilterForm.submit();
                }
            });
        }

        /* =====================================================
           BULK MODAL
        ===================================================== */

        function openTriggerModal() {
            const modal = document.getElementById('triggerEvalModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }


        function closeTriggerModal() {
            const modal = document.getElementById('triggerEvalModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        /* =====================================================
           SELECT ALL
        ===================================================== */
        let selectAllState = false;

        function toggleSelectAll() {
            selectAllState = !selectAllState;
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(function(cb) {
                cb.checked = selectAllState;
            });


            const button = document.getElementById('selectAllBtn');
            if (button) {button.textContent = selectAllState ? 'Deselect All' : 'Select All'; }
        }

        /* =====================================================
           SINGLE EVALUATION MODAL
        ===================================================== */

        /*
         * One modal serves two very different actions, so the wording is
         * swapped here rather than duplicated into a second modal.
         *
         * isApproval is true only from the "Approve Request" button, i.e. the
         * intern asked for this themselves. Everything is reset on every open,
         * so closing an approval and reopening a plain trigger cannot leave the
         * previous wording behind.
         */
        function openSingleConfirmModal(studentId, studentName, isApproval) {
            document.getElementById('confirmStudentId').value = studentId;
            document.getElementById('confirmStudentName').textContent = studentName;

            const title = document.getElementById('confirmModalTitle');
            const lead  = document.getElementById('confirmModalLead');
            const note  = document.getElementById('confirmModalNote');
            const cta   = document.getElementById('confirmModalSubmit');

            if (isApproval) {
                title.textContent = 'Approve Evaluation Request';
                lead.textContent  = 'Approve the final evaluation request from';
                cta.textContent   = 'Yes, Approve Request';
                note.classList.remove('hidden');
            } else {
                title.textContent = 'Confirm Final Evaluation';
                lead.textContent  = 'Are you sure you want to request a final evaluation for';
                cta.textContent   = 'Yes, Request Evaluation';
                note.classList.add('hidden');
            }

            const modal = document.getElementById('singleConfirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeSingleConfirmModal() {
            const modal =  document.getElementById('singleConfirmModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        /* =====================================================
           CANCEL EVALUATION MODAL
        ===================================================== */
        function openCancelEvaluationModal(studentId, studentName) {
            document.getElementById('cancelStudentId').value = studentId;
            document.getElementById('cancelStudentName').textContent = studentName;

            const modal = document.getElementById('cancelEvalModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeCancelEvaluationModal() {
            const modal = document.getElementById('cancelEvalModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
        /* =====================================================
           UPDATE EVALUATION FORM MODAL
        ===================================================== */
        function openEvaluationFormEditor() {
            const modal = document.getElementById('evaluationFormModal');
            if (!modal) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const firstField = modal.querySelector('textarea');
            if (firstField) {
                firstField.focus();
            }
        }

        function closeEvaluationFormEditor() {
            const modal = document.getElementById('evaluationFormModal');
            if (!modal) return;

            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
        /* =====================================================
           ESC KEY CLOSES MODALS
        ===================================================== */
        document.addEventListener('keydown', function(event) {
            if (event.key !== 'Escape') {
                return;
            }
            closeTriggerModal();
            closeSingleConfirmModal();
            closeCancelEvaluationModal();
            closeEvaluationFormEditor();
        });

        // Clicking the backdrop closes the evaluation form editor.
        document.addEventListener('click', function (event) {
            const modal = document.getElementById('evaluationFormModal');
            if (modal && event.target === modal) {
                closeEvaluationFormEditor();
            }
        });
    </script>
</body>
</html>