<!-- src/components/coordinator_sidebar.php -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Dynamic Database Fetch for Pending Evaluation Requests Badge
$pendingRequestBadgeCount = 0;
if (isset($pdo)) {
    try {
        $stmtPendingReqBadge = $pdo->query("
            SELECT COUNT(s.id)
            FROM students s
            WHERE s.completion_requested = 1
              AND (s.evaluation_triggered IS NULL OR s.evaluation_triggered = 0)
              AND NOT EXISTS (SELECT 1 FROM evaluations e WHERE e.student_id = s.id)
        ");
        $pendingRequestBadgeCount = (int)$stmtPendingReqBadge->fetchColumn();
    } catch (Exception $e) {
        $pendingRequestBadgeCount = 0;
    }
}
?>

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 h-screen sticky top-0 z-20">
    <div>
        <!-- Portal Brand Header -->
        <div class="h-20 px-6 bg-[#0F2854] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 text-white flex items-center justify-center font-bold text-base shrink-0">
                N
            </div>
            <div class="min-w-0">
                <h2 class="font-bold text-sm text-white leading-tight truncate">OJT Coordinator</h2>
                <p class="text-[11px] text-blue-200/70 font-medium truncate mt-0.5">NBSC &middot; ICS Department</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-4">
            
            <!-- MONITORING SECTION -->
            <div>
                <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Monitoring</p>
                <div class="space-y-1">
                    
                    <!-- CQI Dashboard -->
                    <?php $isDashboard = in_array($currentPage, ['dashboard.php', 'index.php']); ?>
                    <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isDashboard ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-5 h-5 <?= $isDashboard ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        CQI Dashboard
                    </a>

                    <!-- Accomplishment Reports -->
                    <?php $isReports = in_array($currentPage, ['approved_reports.php', 'view_report.php']); ?>
                    <a href="approved_reports.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isReports ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-5 h-5 <?= $isReports ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Accomplishment Reports
                    </a>

                    <!-- Final Evaluations with Pending Request Notification Badge -->
                    <?php $isEvaluations = ($currentPage === 'evaluations.php'); ?>
                    <a href="evaluations.php" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all text-sm <?= $isEvaluations ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 <?= $isEvaluations ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            <span class="truncate">Final Evaluations</span>
                        </div>

                        <!-- Dynamic Pending Evaluation Request Badge -->
                        <?php if ($pendingRequestBadgeCount > 0): ?>
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white font-bold text-xs shrink-0 shadow-2xs">
                                <?= $pendingRequestBadgeCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>

                </div>
            </div>

            <!-- ADMINISTRATION SECTION -->
            <div>
                <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Administration</p>
                <div class="space-y-1">
                    
                    <!-- User Management -->
                    <?php $isUserManagement = ($currentPage === 'users.php'); ?>
                    <a href="users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isUserManagement ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-5 h-5 <?= $isUserManagement ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        User Management
                    </a>

                    <!-- Student Assignments -->
                    <?php $isAssignments = ($currentPage === 'assignments.php'); ?>
                    <a href="assignments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isAssignments ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-5 h-5 <?= $isAssignments ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Student Assignments
                    </a>

                    <!-- Manage Entities (NEW) -->
                    <?php $isEntities = in_array($currentPage, ['entities.php', 'manage_entities.php']); ?>
                    <a href="entities.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isEntities ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-5 h-5 <?= $isEntities ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Manage Entities
                    </a>

                    <!-- Audit Logs -->
                    <?php $isAuditLogs = ($currentPage === 'audit_logs.php'); ?>
                    <a href="audit_logs.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isAuditLogs ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-5 h-5 <?= $isAuditLogs ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Audit Logs
                    </a>

                </div>
            </div>

            <!-- ACCOUNT SECTION -->
            <div>
                <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Account</p>
                <div class="space-y-1">

                    <!-- Profile -->
                    <?php $isProfile = ($currentPage === 'profile.php'); ?>
                    <a href="profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm <?= $isProfile ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854] shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium' ?>">
                        <svg class="w-5 h-5 <?= $isProfile ? 'text-[#0F2854]' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>

                </div>
            </div>

        </nav>
    </div>
</aside>