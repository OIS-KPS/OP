<!-- src/components/coordinator_sidebar.php -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 h-screen sticky top-0 z-20">
    <div>
        <!-- Portal Brand Header -->
        <div class="h-20 px-6 border-b border-slate-200/80 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#0F2854] text-white flex items-center justify-center font-extrabold text-sm shadow-xs shrink-0 tracking-tight">
                ICS
            </div>
            <div class="min-w-0">
                <h2 class="font-bold text-sm text-slate-900 leading-tight truncate">OJT Coordinator</h2>
                <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">NBSC CQI Portal</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-4">
            
            <!-- ANALYTICS & CQI SECTION -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Analytics & CQI</p>
                <div class="space-y-1">
                    
                    <!-- CQI Dashboard -->
                    <?php $isDashboard = ($currentPage === 'dashboard.php'); ?>
                    <a href="dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isDashboard ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isDashboard ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        CQI Dashboard
                    </a>

                    <!-- Approved WARs -->
                    <?php $isApprovedWARs = ($currentPage === 'approved_reports.php'); ?>
                    <a href="approved_reports.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isApprovedWARs ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isApprovedWARs ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Approved WARs
                    </a>

                </div>
            </div>

            <!-- ADMINISTRATION SECTION -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Administration</p>
                <div class="space-y-1">
                    
                    <!-- User Management -->
                    <?php $isUserManagement = ($currentPage === 'users.php'); ?>
                    <a href="users.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isUserManagement ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isUserManagement ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        User Management
                    </a>

                    <!-- Student Assignments -->
                    <?php $isAssignments = ($currentPage === 'assignments.php'); ?>
                    <a href="assignments.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isAssignments ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isAssignments ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Student Assignments
                    </a>

                    <!-- Final Evaluations -->
                    <?php $isEvaluations = ($currentPage === 'evaluations.php'); ?>
                    <a href="evaluations.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-xs <?= $isEvaluations ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-4 h-4 <?= $isEvaluations ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Final Evaluations
                    </a>
                    
                </div>
            </div>

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