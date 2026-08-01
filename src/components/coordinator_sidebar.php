<!-- src/components/coordinator_sidebar.php -->
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between p-4 shrink-0 min-h-screen">
    <div class="space-y-6">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-2 py-1">
            <div class="w-8 h-8 rounded-xl bg-[#0F2854] text-white flex items-center justify-center font-extrabold text-sm shadow-xs">
                ICS
            </div>
            <div>
                <h2 class="text-xs font-bold text-slate-900 leading-tight">OJT Coordinator</h2>
                <p class="text-[10px] text-slate-400">NBSC CQI Portal</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-1">
            
            <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Analytics & CQI</p>

            <a href="dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs <?= $currentPage === 'dashboard.php' ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854]' : 'text-slate-600 hover:bg-slate-100' ?>">
                <span>📊</span> CQI Dashboard
            </a>

            <a href="approved_reports.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs <?= $currentPage === 'approved_reports.php' ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854]' : 'text-slate-600 hover:bg-slate-100' ?>">
                <span>📋</span> Approved WARs
            </a>

            <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 my-2 pt-2">Administration</p>

            <a href="users.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs <?= $currentPage === 'users.php' ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854]' : 'text-slate-600 hover:bg-slate-100' ?>">
                <span>👥</span> User Management
            </a>

            <a href="assignments.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs <?= $currentPage === 'assignments.php' ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854]' : 'text-slate-600 hover:bg-slate-100' ?>">
                <span>🔗</span> Student Assignments
            </a>

            <a href="evaluations.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs <?= $currentPage === 'evaluations.php' ? 'bg-blue-50/80 text-[#0F2854] font-bold border-l-4 border-[#0F2854]' : 'text-slate-600 hover:bg-slate-100' ?>">
                <span>📝</span> Final Evaluations
            </a>

        </nav>
    </div>

    <div class="border-t border-slate-100 pt-3 px-2">
        <a href="/ICS-PORTAL/public/logout.php" class="text-xs font-semibold text-rose-600 hover:underline flex items-center gap-2">
            <span>🚪</span> Logout
        </a>
    </div>
</aside>