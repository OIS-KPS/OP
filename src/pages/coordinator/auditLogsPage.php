<!-- src/pages/coordinator/auditLogsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Coordinator Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Workspace -->
            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

                <!-- Unified Audit Logs Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
                    
                    <!-- 1. Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-200/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/60 shrink-0">
                        <div>
                            <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">System Activity &amp; Audit Logs</h1>
                            <p class="text-xs font-semibold text-slate-600 mt-1">
                                Chronological record of logins, report updates, and account events across the portal.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                                Showing last <?= count($logs); ?> events
                            </span>
                        </div>
                    </div>

                    <!-- 2. Integrated Filter & Live Search Toolbar -->
                    <div class="p-4 border-b border-slate-200/70 flex flex-col md:flex-row items-center justify-between gap-4 bg-white shrink-0">
                        
                        <!-- Role Filter Tabs -->
                        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
                            <button type="button" onclick="filterLogs('all', this)" class="role-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-[#0F2854] text-white border border-[#0F2854] cursor-pointer">
                                <span>All Roles</span>
                            </button>
                            <button type="button" onclick="filterLogs('student', this)" class="role-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 cursor-pointer">
                                <span>Students</span>
                            </button>
                            <button type="button" onclick="filterLogs('supervisor', this)" class="role-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 cursor-pointer">
                                <span>Supervisors</span>
                            </button>
                            <button type="button" onclick="filterLogs('coordinator', this)" class="role-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 cursor-pointer">
                                <span>Coordinators</span>
                            </button>
                        </div>

                        <!-- Live Search Input -->
                        <div class="relative w-full md:w-72">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <input 
                                type="text" 
                                id="logSearchInput"
                                placeholder="Search user, action, or details..." 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"
                            >
                        </div>
                    </div>

                    <!-- 3. Audit Logs Scrollable Table -->
                    <?php if (!empty($logs)): ?>
                        <div class="overflow-y-auto overflow-x-auto max-h-[620px] thin-scrollbar">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="sticky top-0 z-10 bg-slate-100 shadow-2xs">
                                    <tr class="text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6 bg-slate-100">Timestamp</th>
                                        <th class="py-4 px-6 bg-slate-100">User Account</th>
                                        <th class="py-4 px-6 bg-slate-100">Role</th>
                                        <th class="py-4 px-6 bg-slate-100">Action Event</th>
                                        <th class="py-4 px-6 bg-slate-100">Activity Description</th>
                                        <th class="py-4 px-6 bg-slate-100 text-right">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800 bg-white">
                                    <?php foreach ($logs as $log): 
                                        $role = strtolower(trim($log['role'] ?? 'system'));
                                        $rawAction = strtoupper(trim($log['action'] ?? ''));
                                        $formattedAction = ucwords(str_replace(['_', '-'], ' ', strtolower($rawAction)));
                                        $userName = !empty($log['user_name']) ? $log['user_name'] : 'System Action';
                                        
                                        // Clean & Developer-Standard Description Parser
                                        $cleanDesc = $log['description'] ?? '';
                                        if (stripos($rawAction, 'LOGIN') !== false) {
                                            $cleanDesc = 'Signed in successfully via Google OAuth2';
                                        } elseif (stripos($rawAction, 'LOGOUT') !== false) {
                                            $cleanDesc = 'User signed out and terminated active session';
                                        } elseif (stripos($rawAction, 'REGISTER') !== false) {
                                            $cleanDesc = 'New user account provisioned via Google OAuth';
                                        } elseif (stripos($rawAction, 'STUDENT_CREATED') !== false) {
                                            $cleanDesc = 'Created student profile and dispatched welcome email';
                                        } elseif (stripos($rawAction, 'SUPERVISOR_CREATED') !== false) {
                                            $cleanDesc = 'Created supervisor account and dispatched invite';
                                        } elseif (stripos($rawAction, 'COMPANY_CREATED') !== false) {
                                            $cleanDesc = 'Registered new host agency / partner office';
                                        } elseif (stripos($rawAction, 'REPORT_SUBMITTED') !== false) {
                                            $cleanDesc = 'Submitted weekly accomplishment report for review';
                                        } elseif (stripos($rawAction, 'REPORT_APPROVED') !== false) {
                                            $cleanDesc = 'Verified and approved weekly accomplishment report';
                                        } elseif (stripos($rawAction, 'EVALUATION') !== false) {
                                            $cleanDesc = 'Signed final performance evaluation with digital OTP';
                                        }
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors group log-row" data-role="<?= htmlspecialchars($role); ?>">
                                            
                                            <!-- Timestamp -->
                                            <td class="py-4 px-6 whitespace-nowrap text-slate-600 font-semibold align-middle">
                                                <?= date("M d, Y \a\\t g:i A", strtotime($log['created_at'])); ?>
                                            </td>

                                            <!-- User Name & Email -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <p class="font-extrabold text-slate-950 text-xs user-target"><?= htmlspecialchars($userName); ?></p>
                                                <?php if (!empty($log['user_email'])): ?>
                                                    <p class="text-[11px] text-slate-600 font-semibold mt-0.5"><?= htmlspecialchars($log['user_email']); ?></p>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Role Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <?php if ($role === 'student'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-[#0F2854] border border-blue-200">
                                                        Student
                                                    </span>
                                                <?php elseif ($role === 'supervisor'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                                        Supervisor
                                                    </span>
                                                <?php elseif ($role === 'coordinator'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-900 border border-indigo-200">
                                                        Coordinator
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                                        System
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Name -->
                                            <td class="py-4 px-6 whitespace-nowrap align-middle">
                                                <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 border border-slate-300 font-bold text-[11px] action-target">
                                                    <?= htmlspecialchars($formattedAction); ?>
                                                </span>
                                            </td>

                                            <!-- Activity Description -->
                                            <td class="py-4 px-6 max-w-md text-slate-700 font-medium truncate desc-target align-middle" title="<?= htmlspecialchars($log['description']); ?>">
                                                <span><?= htmlspecialchars($cleanDesc); ?></span>
                                            </td>

                                            <!-- IP Address -->
                                            <td class="py-4 px-6 text-right font-mono text-[11px] text-slate-500 font-semibold whitespace-nowrap align-middle">
                                                <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📜
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No audit records logged yet</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">System events and user activities will appear here.</p>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <!-- Live Client Filtering Script -->
    <script>
        let currentRoleFilter = 'all';

        function filterLogs(role, tabElement) {
            currentRoleFilter = role;

            document.querySelectorAll('.role-tab').forEach(tab => {
                tab.className = 'role-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 cursor-pointer';
            });
            tabElement.className = 'role-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-colors bg-[#0F2854] text-white border border-[#0F2854] cursor-pointer';

            applyCombinedFilter();
        }

        const searchInput = document.getElementById('logSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', applyCombinedFilter);
        }

        function applyCombinedFilter() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const rows = document.querySelectorAll('.log-row');

            rows.forEach(row => {
                const rowRole = row.getAttribute('data-role');
                const user = row.querySelector('.user-target')?.textContent.toLowerCase() || '';
                const act  = row.querySelector('.action-target')?.textContent.toLowerCase() || '';
                const desc = row.querySelector('.desc-target')?.textContent.toLowerCase() || '';

                const matchesSearch = user.includes(query) || act.includes(query) || desc.includes(query);
                const matchesRole   = (currentRoleFilter === 'all') || (rowRole === currentRoleFilter);

                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>