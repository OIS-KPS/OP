<!-- src/pages/supervisor/reviewReportsPage.php -->
<?php
date_default_timezone_set('Asia/Manila');

$message = $message ?? '';
$interns = $interns ?? [];
$pendingInterns = $pendingInterns ?? 0;
$pageError = $pageError ?? '';
$internsLoadFailed = $internsLoadFailed ?? false;

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
    <title>Review Reports - Supervisor Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <script src="/ICS-PORTAL/public/js/loadingOverlay.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

<div class="flex min-h-screen">

    <!-- Sidebar Component -->
    <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Header Component -->
        <?php include __DIR__ . '/../../components/header.php'; ?>

        <!-- Main Workspace -->
        <main class="p-8 max-w-[1150px] w-full mx-auto space-y-6 flex-1 relative">

            <!-- Unified Reports Container -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">

                <!-- 1. Header Toolbar Banner -->
                <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60">
                    <div>
                        <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">Review Accomplishment Reports</h1>
                        <p class="text-xs font-semibold text-slate-600 mt-1">
                            Submitted weekly accomplishment reports from your assigned interns.
                        </p>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center gap-3 shrink-0">
                        <?php if (!$internsLoadFailed): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                                <?= count($interns); ?> Total <?= count($interns) === 1 ? 'Intern' : 'Interns'; ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($pendingInterns > 0): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                <?= $pendingInterns; ?> Awaiting Review
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. Flash Alert Message -->
                <?php if (!empty($message)): ?>
                    <div class="mx-6 mt-4 bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl text-xs font-bold shadow-2xs flex items-center gap-2">
                        <span class="font-black text-sm">✓</span>
                        <span><?= e($message); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pageError)): ?>
                    <div class="mx-6 mt-4 bg-rose-50 border border-rose-300 text-rose-900 px-4 py-3 rounded-2xl text-xs font-bold shadow-2xs flex items-center gap-2">
                        <span class="text-rose-700 font-black">✕</span>
                        <span><?= e($pageError); ?></span>
                    </div>
                <?php endif; ?>

                <!-- 3. Live Search Toolbar -->
                <div class="p-4 border-b border-slate-200/70 bg-white">
                    <!-- Live Search Input -->
                    <div class="relative w-full">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search by intern name or ID number..."
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                        >
                    </div>
                </div>

                <!-- 4. Interns Summary Table -->
                <?php if (!empty($interns)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[880px] table-fixed text-left border-collapse text-xs">
                            <colgroup>
                                <col style="width: 30%;">
                                <col style="width: 9%;">
                                <col style="width: 38%;">
                                <col style="width: 23%;">
                            </colgroup>
                            <thead>
                                <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                    <th class="py-4 px-4">Student Intern</th>
                                    <th class="py-4 px-4">Section</th>
                                    <th class="py-4 px-4">Pending for Review</th>
                                    <th class="py-4 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                <?php foreach ($interns as $intern):
                                    $pendingCount = (int) $intern['pending_count'];
                                    $totalReports = (int) $intern['total_reports_count'];
                                    $focusReportId = (int) $intern['focus_report_id'];
                                    $latestPendingAt = $intern['latest_pending_at'] ?? null;
                                    $hasPending = ($pendingCount > 0);
                                ?>
                                    <tr class="hover:bg-slate-50 transition-colors intern-row <?= $hasPending ? 'bg-amber-50/20' : ''; ?>">

                                        <!-- Student Name, Avatar & ID -->
                                        <td class="py-4 px-4 align-middle">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-xs shrink-0 overflow-hidden border border-slate-300">
                                                    <?php if (!empty($intern['student_avatar'])): ?>
                                                        <img src="<?= e($intern['student_avatar']); ?>" class="w-full h-full object-cover" alt="Avatar" onerror="this.onerror=null; this.parentElement.innerHTML='<?= e(strtoupper(substr($intern['student_name'] ?? 'S', 0, 1))); ?>';">
                                                    <?php else: ?>
                                                        <?= e(strtoupper(substr($intern['student_name'] ?? 'S', 0, 1))); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-extrabold text-slate-950 text-sm intern-name break-words"><?= e($intern['student_name']); ?></p>
                                                    <p class="text-[11px] text-slate-600 font-semibold intern-id mt-0.5">ID: <?= e($intern['student_number'] ?? 'N/A'); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Section Badge -->
                                        <td class="py-4 px-4 whitespace-nowrap align-middle">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                Sec <?= e($intern['section'] ?? 'A'); ?>
                                            </span>
                                        </td>

                                        <!-- Pending for Review -->
                                        <td class="py-4 px-4 align-middle">
                                            <div class="space-y-1">
                                                <?php if ($hasPending): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-bold text-[11px] leading-tight">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        <?= $pendingCount; ?> <?= $pendingCount === 1 ? 'Report' : 'Reports'; ?> to Review
                                                    </span>
                                                    <?php if (!empty($latestPendingAt)): ?>
                                                        <p class="text-[11px] text-slate-600 font-medium pl-[22px]">
                                                            Submitted <?= e(date("M d, Y \a\\t g:i A", strtotime($latestPendingAt))); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 font-bold text-[11px] leading-tight">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        No pending for review
                                                    </span>
                                                    <p class="text-[11px] text-slate-500 font-medium pl-[22px]">
                                                        <?= $totalReports; ?> <?= $totalReports === 1 ? 'report' : 'reports'; ?> on file
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Action Button -->
                                        <td class="py-4 px-4 text-right whitespace-nowrap align-middle">
                                            <a href="view_report.php?student_id=<?= (int)$intern['student_id']; ?>&amp;report_id=<?= $focusReportId; ?>"
                                               data-ics-loading="Extracting entities from this report&hellip; This may take a moment."
                                               class="px-4 py-2 <?= $hasPending ? 'bg-[#0F2854] text-white hover:bg-blue-900' : 'bg-slate-100 text-slate-800 hover:bg-slate-200 border border-slate-300'; ?> text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                <span><?= $hasPending ? 'Review' : 'View Reports'; ?></span>
                                                <svg class="w-3.5 h-3.5 <?= $hasPending ? 'text-blue-200' : ''; ?>" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                            </a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <?php if ($internsLoadFailed): ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-rose-300">
                                !
                            </div>
                            <h4 class="text-sm font-black text-slate-900">Could not load your interns</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">
                                The list could not be retrieved. Use the banner above for details, then try again.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📄
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No submissions yet</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">
                                None of your interns have submitted a weekly report yet.
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>

        </main>
    </div>
</div>

<!-- Scripts -->
<script>
    // Instant Client-Side Search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.intern-row');

            rows.forEach(function(row) {
                const name = row.querySelector('.intern-name')?.textContent.toLowerCase() || '';
                const id   = row.querySelector('.intern-id')?.textContent.toLowerCase() || '';

                if (name.includes(query) || id.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
</script>
</body>
</html>