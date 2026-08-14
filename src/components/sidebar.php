<!-- src/components/sidebar.php -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 h-screen sticky top-0 z-20">
    <div>
        <!-- Portal Brand Header (colored) -->
        <div class="h-20 px-6 bg-[#0F2854] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 text-white flex items-center justify-center font-bold text-base shrink-0">
                N
            </div>
            <div class="min-w-0">
                <h2 class="font-bold text-sm text-white leading-tight truncate">Student Portal</h2>
                <p class="text-[11px] text-blue-200/70 font-medium truncate mt-0.5">NBSC &middot; ICS Department</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1.5">

            <?php $isDashboard = ($currentPage === 'dashboard.php'); ?>
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isDashboard ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                <svg class="w-5 h-5 <?= $isDashboard ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>

            <?php $isReports = ($currentPage === 'reports.php' || $currentPage === 'submit_report.php'); ?>
            <a href="reports.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isReports ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                <svg class="w-5 h-5 <?= $isReports ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                My Reports
            </a>

            <?php $isProfile = ($currentPage === 'profile.php' || $currentPage === 'edit_profile.php'); ?>
            <a href="profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isProfile ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                <svg class="w-5 h-5 <?= $isProfile ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile
            </a>

        </nav>
    </div>

    <!-- Bottom Logout -->
    <div class="p-4 border-t border-slate-200/80">
        <a href="auth/logout.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-500 hover:bg-rose-50 hover:text-rose-600 text-sm font-semibold transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Logout
        </a>
    </div>
</aside>