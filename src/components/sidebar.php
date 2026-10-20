<!-- src/components/sidebar.php -->
<!-- Student-only navigation. Included by the four student views, and by
     report_workspace.php whenever $sidebarPath resolves here; the coordinator
     and supervisor have their own sidebars, so nothing in this file is shared.

     Below the lg breakpoint (1024px) the sidebar is an off-canvas drawer driven
     by the hamburger in header.php, which is rendered only when the session
     role is student. At lg and above it returns to the original in-flow sticky
     column, so the desktop layout is unchanged.

     lg:inset-y-auto is required, not decorative: inset-y-0 pins both top and
     bottom, which fights position:sticky once the element goes back into flow.

     INCLUDE ORDER: every page includes this before header.php, because the
     drawer is the first column of the flex row and the header is the second
     element inside the content column. That means the toggle button does not
     exist yet when the script below runs, so it is looked up on each use
     instead of being captured once. -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="student-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 h-screen -translate-x-full lg:translate-x-0 lg:inset-y-auto lg:sticky lg:top-0 lg:z-20 transition-transform duration-200">
    <div>
        <!-- Portal Brand Header (colored) -->
        <div class="h-20 px-6 bg-[#0F2854] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 text-white flex items-center justify-center font-bold text-base shrink-0">
                N
            </div>
            <div class="min-w-0">
                <h2 class="font-bold text-sm text-white leading-tight truncate">Student Portal</h2>
                <p class="text-[11px] text-blue-200/70 font-medium truncate mt-0.5">NBSC</p>
            </div>
            <!-- Explicit close control. Below lg the backdrop is the only other
                 way out, and on a 375px screen it is a thin strip to hit. -->
            <button type="button" onclick="closeStudentSidebar()" class="lg:hidden ml-auto -mr-2 w-9 h-9 shrink-0 flex items-center justify-center rounded-lg text-white/80 hover:bg-white/10 transition-colors cursor-pointer" aria-label="Close navigation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
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

            <?php $isReports = ($currentPage === 'reports.php' || $currentPage === 'submit_report.php' || $currentPage === 'view_report.php'); ?>
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
</aside>

<!-- Backdrop. A sibling of the aside rather than a child, so the aside stays a
     single element; position:fixed lifts it out of the flex flow either way. -->
<div id="student-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 opacity-0 invisible transition-opacity duration-200 lg:hidden" aria-hidden="true"></div>

<script>
    // Off-canvas drawer state for the student sidebar.
    //
    // Every class name toggled below is also written literally in this file,
    // because cdn.tailwindcss.com generates utilities by scanning the served
    // document. A class that only ever appears as a JS argument still gets
    // picked up -- the scanner reads script text too -- but keeping them
    // visible here means a browser holding a cached stylesheet cannot end up
    // toggling a rule that was never generated.
    (function () {
        var sidebar = document.getElementById('student-sidebar');
        var backdrop = document.getElementById('student-sidebar-backdrop');

        if (!sidebar || !backdrop) {
            return;
        }

        // header.php is included after this component, so the toggle button does
        // not exist yet at parse time. Resolve it per call rather than capturing
        // it once, otherwise aria-expanded never tracks the drawer.
        function getButton() {
            return document.getElementById('student-menu-btn');
        }

        function isOpen() {
            return !sidebar.classList.contains('-translate-x-full');
        }

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('opacity-0', 'invisible');
            backdrop.classList.add('opacity-100', 'visible');
            document.body.classList.add('overflow-hidden');
            if (getButton()) {
                getButton().setAttribute('aria-expanded', 'true');
            }
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.remove('opacity-100', 'visible');
            backdrop.classList.add('opacity-0', 'invisible');
            document.body.classList.remove('overflow-hidden');
            if (getButton()) {
                getButton().setAttribute('aria-expanded', 'false');
            }
        }

        window.toggleStudentSidebar = function () {
            if (isOpen()) {
                closeSidebar();
            } else {
                openSidebar();
            }
        };
        window.closeStudentSidebar = closeSidebar;

        backdrop.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && isOpen()) {
                closeSidebar();
            }
        });

        // Rotating a phone or widening the window past lg returns the sidebar to
        // the static column. Reset the drawer at the same moment so it is not
        // left latched off-screen behind the visible sidebar, and so the body
        // scroll lock does not outlive the drawer.
        var wide = window.matchMedia('(min-width: 1024px)');

        function onBreakpointChange(event) {
            if (event.matches) {
                closeSidebar();
            }
        }

        if (wide.addEventListener) {
            wide.addEventListener('change', onBreakpointChange);
        } else if (wide.addListener) {
            wide.addListener(onBreakpointChange);
        }
    })();
</script>