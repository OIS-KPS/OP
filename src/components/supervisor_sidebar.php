<!-- src/components/supervisor_sidebar.php -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Count pending reports dynamically for the badge
if (!isset($pendingCount)) {
    if (isset($_SESSION['dev_reports'])) {
        $pendingCount = count(array_filter($_SESSION['dev_reports'], fn($r) => $r['status'] === 'Pending'));
    } else {
        $pendingCount = 0;
    }
}
?>

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 h-screen sticky top-0 z-20">
    <div>
        <!-- Portal Brand Header -->
        <div class="h-20 px-6 border-b border-slate-200/80 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#0F2854] text-white flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                👔
            </div>
            <div class="min-w-0">
                <h2 class="font-bold text-sm text-slate-900 leading-tight truncate">Supervisor Portal</h2>
                <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">OJT Host Company</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-4">
            
            <!-- MAIN SECTION -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Main</p>
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <?php $isDashboard = ($currentPage === 'dashboard.php'); ?>
                    <a href="dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isDashboard ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isDashboard ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- My Interns -->
                    <?php $isInterns = ($currentPage === 'interns.php'); ?>
                    <a href="interns.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isInterns ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isInterns ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        My Interns
                    </a>

                </div>
            </div>

            <!-- ACTIONS SECTION -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Actions</p>
                <div class="space-y-1">
                    <!-- Review Reports with Notification Badge -->
                    <?php $isReview = ($currentPage === 'review_reports.php' || $currentPage === 'review_report.php'); ?>
                    <a href="review_reports.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isReview ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-4 h-4 <?= $isReview ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="truncate">Review Reports</span>
                        </div>

                        <!-- Dynamic Pending Counter Badge -->
                        <?php if ($pendingCount > 0): ?>
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white font-bold text-[10px] shrink-0 animate-pulse">
                                <?= $pendingCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Evaluate Students Link -->
                    <?php $isEvaluate = ($currentPage === 'evaluate_interns.php' || $currentPage === 'evaluate_form.php'); ?>
                    <a href="evaluate_interns.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isEvaluate ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isEvaluate ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Evaluate Students
                    </a>
                    
                </div>
            </div>

        </nav>
    </div>

    <!-- Bottom Logout -->
    <div class="p-4 border-t border-slate-200/80">
        <a href="../logout.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-500 hover:bg-rose-50 hover:text-rose-600 text-sm font-semibold transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Logout
        </a>
    </div>
</aside>