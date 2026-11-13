<?php
// Resolve logged in user's role dynamically
$userRole = strtolower($_SESSION['role'] ?? 'student');

switch ($userRole) {
    case 'supervisor':
        $roleLabel = 'Industry Supervisor';
        $statusColor = 'text-blue-700';
        $dotColor = 'bg-blue-600';
        break;
    case 'coordinator':
        $roleLabel = 'OJT Coordinator';
        $statusColor = 'text-indigo-700';
        $dotColor = 'bg-indigo-600';
        break;
    case 'student':
    default:
        $roleLabel = 'Intern active';
        $statusColor = 'text-emerald-600';
        $dotColor = 'bg-emerald-500';
        break;
}

$displayName = $_SESSION['user_name'] ?? (!empty($student['name']) ? $student['name'] : 'User');
$displayEmail = $_SESSION['email'] ?? '';

$notificationUserId = (int) ($_SESSION['user_id'] ?? 0);
$unreadNotificationCount = 0;
$recentNotifications = [];
if (isset($pdo) && $notificationUserId > 0) {
    $unreadNotificationCount = getUnreadNotificationCount($pdo, $notificationUserId);
    $recentNotifications = getRecentNotifications($pdo, $notificationUserId, 8);
}
?>
<header
    class="bg-white/95 backdrop-blur-md h-20 px-4 sm:px-8 flex justify-between items-center sticky top-0 z-10 border-b border-slate-200/80 transition-all select-none">

    <!-- Student navigation toggle.
         Role-gated on purpose: this header is shared by all four roles, and
         only the student sidebar is an off-canvas drawer below lg. The
         coordinator and supervisor sidebars are static columns at every width,
         so rendering this button for them would give a control that opens
         nothing. $userRole is resolved at the top of this file. -->
    <?php if ($userRole === 'student'): ?>
        <button type="button" id="student-menu-btn" onclick="toggleStudentSidebar()"
            class="lg:hidden -ml-2 p-2 mr-1 shrink-0 rounded-xl text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300"
            aria-label="Open navigation" aria-controls="student-sidebar" aria-expanded="false">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    <?php endif; ?>

    <!-- Department Scope & Identity -->
    <div class="flex items-center gap-2.5 min-w-0">

        <p class="text-sm font-bold text-blue-900 tracking-tight truncate min-w-0 ">LaunchPad - Institute for Computer
            Studies</p>
    </div>

    <!-- Right Header Controls -->
    <div class="flex items-center gap-4">

        <!-- Notifications -->
        <div class="relative" id="notification-wrapper">
            <button id="notification-btn" type="button" onclick="toggleNotificationDropdown()"
                class="relative p-2 rounded-xl hover:bg-slate-50 transition-all cursor-pointer focus:outline-none"
                aria-label="Notifications">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <?php if ($unreadNotificationCount > 0): ?>
                    <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 rounded-full bg-rose-600 text-white text-[10px] font-bold flex items-center justify-center">
                        <?= $unreadNotificationCount > 99 ? '99+' : (int) $unreadNotificationCount; ?>
                    </span>
                <?php endif; ?>
            </button>

            <div id="notification-menu"
                class="absolute right-0 top-full mt-2 w-80 max-h-96 overflow-y-auto bg-white rounded-xl border border-slate-200/90 shadow-xl shadow-slate-200/50 opacity-0 invisible translate-y-1 transition-all duration-200 z-50">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <p class="text-xs font-bold text-slate-900">Notifications</p>
                    <?php if ($unreadNotificationCount > 0): ?>
                        <a href="<?= BASE_PATH; ?>/notifications_mark_read.php?all=1"
                            class="text-[10px] font-semibold text-[#0F2854] hover:underline">Mark all as read</a>
                    <?php endif; ?>
                </div>
                <div class="py-1.5">
                    <?php if (empty($recentNotifications)): ?>
                        <div class="px-4 py-2 border-b border-slate-100">
                            <a href="<?= BASE_PATH; ?>/notifications.php" class="text-[10px] font-semibold text-[#0F2854] hover:underline">View all notifications</a>
                        </div>
                        <p class="px-4 py-6 text-xs text-slate-500 text-center">No notifications yet.</p>
                    <?php else: ?>
                        <?php foreach ($recentNotifications as $notif): ?>
                            <?php
                            $notifLink = !empty($notif['link_url']) ? (BASE_PATH . '/' . ltrim($notif['link_url'], '/')) : null;
                            $isUnread = empty($notif['is_read']);
                            ?>
                            <a href="<?= $notifLink ? htmlspecialchars($notifLink) . (strpos($notifLink, '?') !== false ? '&' : '?') . 'notif_id=' . (int) $notif['id'] : '#'; ?>"
                                class="block px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition-colors <?= $isUnread ? 'bg-blue-50/40' : ''; ?>">
                                <p class="text-xs font-bold text-slate-900"><?= htmlspecialchars($notif['title']); ?></p>
                                <p class="text-[11px] text-slate-600 mt-0.5"><?= htmlspecialchars($notif['message']); ?></p>
                                <p class="text-[10px] text-slate-400 mt-1"><?= date('M d, Y g:i A', strtotime($notif['created_at'])); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown Trigger -->
        <div class="relative" id="profile-dropdown-wrapper">
            <button id="profile-dropdown-btn" onclick="toggleProfileDropdown()"
                class="flex items-center gap-3 pl-1 pr-2 py-1.5 rounded-xl hover:bg-slate-50 transition-all cursor-pointer focus:outline-none">
                <div class="relative w-9 h-9 shrink-0">
                    <div
                        class="w-9 h-9 rounded-full bg-[#0F2854] text-white font-bold flex items-center justify-center text-xs overflow-hidden ring-2 ring-slate-100 shadow-2xs">
                        <?php if (!empty($_SESSION['user_picture'])): ?>
                            <img src="<?= htmlspecialchars($_SESSION['user_picture']); ?>" alt="Profile"
                                class="w-full h-full object-cover" referrerpolicy="no-referrer">
                        <?php else: ?>
                            <?= strtoupper(substr($displayName, 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <!-- Dynamic Active Indicator Dot -->
                    <span
                        class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 <?= $dotColor; ?> rounded-full ring-2 ring-white"></span>
                </div>
                <div class="text-left hidden sm:block">
                    <p class="text-xs font-bold text-slate-900 leading-tight"><?= htmlspecialchars($displayName); ?></p>
                    <!-- Dynamic Role Label -->
                    <p class="text-[10px] <?= $statusColor; ?> font-semibold mt-0.5">
                        <?= htmlspecialchars($roleLabel); ?>
                    </p>
                </div>
                <!-- Chevron -->
                <svg id="profile-chevron"
                    class="w-4 h-4 text-slate-400 transition-transform duration-200 hidden sm:block" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div id="profile-dropdown-menu"
                class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl border border-slate-200/90 shadow-xl shadow-slate-200/50 opacity-0 invisible translate-y-1 transition-all duration-200 z-50">
                <!-- User Info Header -->
                <div class="px-4 py-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-[#0F2854] text-white font-bold flex items-center justify-center text-sm overflow-hidden ring-2 ring-slate-100 shrink-0">
                            <?php if (!empty($_SESSION['user_picture'])): ?>
                                <img src="<?= htmlspecialchars($_SESSION['user_picture']); ?>" alt="Profile"
                                    class="w-full h-full object-cover" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <?= strtoupper(substr($displayName, 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate"><?= htmlspecialchars($displayName); ?>
                            </p>
                            <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($displayEmail); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-1.5">
                    <!-- Change Password -->
                    <a href="<?= BASE_PATH; ?>/auth/change_password.php"
                        class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                        <span>Change Password</span>
                    </a>

                    <div class="mx-3 my-1 border-t border-slate-100"></div>

                    <!-- Logout -->
                    <a href="<?= BASE_PATH; ?>/auth/logout.php"
                        class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</header>

<!-- Profile Dropdown & Global Auto-Dismiss Script -->
<script>
    function toggleProfileDropdown() {
        const menu = document.getElementById('profile-dropdown-menu');
        const chevron = document.getElementById('profile-chevron');
        const isOpen = menu.classList.contains('opacity-100');

        if (isOpen) {
            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            menu.classList.add('opacity-0', 'invisible', 'translate-y-1');
            chevron.style.transform = 'rotate(0deg)';
        } else {
            menu.classList.remove('opacity-0', 'invisible', 'translate-y-1');
            menu.classList.add('opacity-100', 'visible', 'translate-y-0');
            chevron.style.transform = 'rotate(180deg)';
        }
    }

    function toggleNotificationDropdown() {
        const menu = document.getElementById('notification-menu');
        if (!menu) return;
        const isOpen = menu.classList.contains('opacity-100');
        if (isOpen) {
            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            menu.classList.add('opacity-0', 'invisible', 'translate-y-1');
        } else {
            menu.classList.remove('opacity-0', 'invisible', 'translate-y-1');
            menu.classList.add('opacity-100', 'visible', 'translate-y-0');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('profile-dropdown-wrapper');
        const menu = document.getElementById('profile-dropdown-menu');
        const chevron = document.getElementById('profile-chevron');

        if (wrapper && !wrapper.contains(e.target)) {
            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            menu.classList.add('opacity-0', 'invisible', 'translate-y-1');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    });

    // Cross-tab logout: if another tab signals a password reset, redirect to login
    window.addEventListener('storage', function (e) {
        if (e.key === 'logout_event') {
            window.location.href = '<?= BASE_PATH; ?>/auth/login.php';
        }
    });

    // Global Auto-Dismiss for Flash Notification Banners
    document.addEventListener("DOMContentLoaded", function () {
        const flashAlerts = document.querySelectorAll('.bg-emerald-50, .bg-rose-50, .bg-blue-50, .bg-amber-50');

        flashAlerts.forEach(alert => {
            const dismissTrigger = alert.querySelector('a, button');
            if (dismissTrigger && (dismissTrigger.textContent.includes('Dismiss') || dismissTrigger.textContent.includes('×') || dismissTrigger.textContent.includes('dismiss'))) {

                setTimeout(() => {
                    alert.style.transition = "all 0.5s ease-in-out";
                    alert.style.opacity = "0";
                    alert.style.transform = "translateY(-10px)";
                    alert.style.marginBottom = "-" + alert.offsetHeight + "px";

                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 4000); // 4 seconds delay before auto-dismissal
            }
        });
    });
</script>

<?php include __DIR__ . '/password_change_popup.php'; ?>