<!-- src/components/header.php -->
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
?>
<header class="bg-white/95 backdrop-blur-md h-20 px-8 flex justify-between items-center sticky top-0 z-10 border-b border-slate-200/80 transition-all select-none">
    
    <!-- Department Scope & Identity -->
    <div class="flex items-center gap-2.5">
        <span class="w-2 h-2 rounded-full bg-[#0F2854] ring-4 ring-[#0F2854]/10"></span>
        <p class="text-sm font-bold text-slate-900 tracking-tight">NBSC - Institute for Computer Studies</p>
    </div>

    <!-- Right Header Controls -->
    <div class="flex items-center gap-4">
        
        <!-- Notification Bell -->
        <button class="relative p-2 text-slate-400 hover:text-[#0F2854] rounded-xl hover:bg-slate-100 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#0F2854] rounded-full ring-2 ring-white"></span>
        </button>

        <div class="h-6 w-px bg-slate-200"></div>

        <!-- User Profile Pill Card -->
        <div class="flex items-center gap-3 pl-1">
            <div class="relative w-9 h-9 shrink-0">
                <div class="w-9 h-9 rounded-full bg-[#0F2854] text-white font-bold flex items-center justify-center text-xs overflow-hidden ring-2 ring-slate-100 shadow-2xs">
                    <?php if (!empty($_SESSION['user_picture'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user_picture']); ?>" alt="Profile" class="w-full h-full object-cover" referrerpolicy="no-referrer">
                    <?php else: ?>
                        <?= strtoupper(substr($displayName, 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <!-- Dynamic Active Indicator Dot -->
                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 <?= $dotColor; ?> rounded-full ring-2 ring-white"></span>
            </div>
            <div class="text-left hidden sm:block">
                <p class="text-xs font-bold text-slate-900 leading-tight"><?= htmlspecialchars($displayName); ?></p>
                <!-- Dynamic Role Label -->
                <p class="text-[10px] <?= $statusColor; ?> font-semibold mt-0.5"><?= htmlspecialchars($roleLabel); ?></p>
            </div>
        </div>
        
    </div>
</header>