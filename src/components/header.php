<!-- src/components/header.php -->
<header class="bg-[#0F2854] text-white h-20 px-8 flex justify-between items-center sticky top-0 z-10 shadow-xs">
    <!-- Title & Department Scope -->
    <div>
        <h1 class="text-base font-bold tracking-wide text-white leading-tight">Dashboard</h1>
        <p class="text-[11px] text-blue-200/70 font-medium mt-0.5">ICS OJT Management System</p>
    </div>
    
    <!-- Right Header Controls -->
    <div class="flex items-center gap-4">
        <!-- Notification Bell -->
        <button class="relative p-2 text-slate-300 hover:text-white rounded-lg hover:bg-white/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-blue-500 rounded-full"></span>
        </button>

        <div class="h-5 w-px bg-white/20"></div>

        <!-- User Avatar Circle -->
        <div class="w-9 h-9 rounded-full bg-blue-600 border border-blue-400/40 text-white flex items-center justify-center font-bold text-sm shadow-xs">
            <?= !empty($student['name']) ? strtoupper(substr($student['name'], 0, 1)) : 'S'; ?>
        </div>
    </div>
</header>