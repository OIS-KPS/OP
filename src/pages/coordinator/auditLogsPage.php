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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased font-sans">

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
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    
                    <!-- 1. Header Toolbar Banner -->
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug">System Activity & Audit Logs</h1>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">
                                Chronological record of logins, report updates, and account events.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                                Showing last <?= count($logs); ?> events
                            </span>
                        </div>
                    </div>

                    <!-- 2. Integrated Filter & Live Search Toolbar -->
                    <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4 bg-slate-50/40">
                        
                        <!-- Role Filter Tabs -->
                        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
                            <button type="button" onclick="filterLogs('all', this)" class="role-tab px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-[#0F2854] text-white shadow-xs cursor-pointer">
                                <span>All Roles</span>
                            </button>
                            <button type="button" onclick="filterLogs('student', this)" class="role-tab px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 cursor-pointer">
                                <span>Students</span>
                            </button>
                            <button type="button" onclick="filterLogs('supervisor', this)" class="role-tab px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 cursor-pointer">
                                <span>Supervisors</span>
                            </button>
                            <button type="button" onclick="filterLogs('coordinator', this)" class="role-tab px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 cursor-pointer">
                                <span>Coordinators</span>
                            </button>
                        </div>

                        <!-- Live Search Input -->
                        <div class="relative w-full md:w-72">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <input 
                                type="text" 
                                id="logSearchInput"
                                placeholder="Search user, action, or details..." 
                                class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0F2854]"
                            >
                        </div>
                    </div>

                    <!-- 3. Audit Logs Table -->
                    <?php if (!empty($logs)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Timestamp</th>
                                        <th class="py-4 px-6">User Account</th>
                                        <th class="py-4 px-6">Role</th>
                                        <th class="py-4 px-6">Action Event</th>
                                        <th class="py-4 px-6">Activity Description</th>
                                        <th class="py-4 px-6 text-right">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
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
                                        <tr class="hover:bg-slate-50/70 transition-colors group log-row" data-role="<?= htmlspecialchars($role); ?>">
                                            
                                            <!-- Timestamp -->
                                            <td class="py-4 px-6 whitespace-nowrap text-slate-500 font-medium">
                                                <?= date("M d, Y \a\\t g:i A", strtotime($log['created_at'])); ?>
                                            </td>

                                            <!-- User Name & Email -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <p class="font-bold text-slate-900 text-xs user-target"><?= htmlspecialchars($userName); ?></p>
                                                <?php if (!empty($log['user_email'])): ?>
                                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5"><?= htmlspecialchars($log['user_email']); ?></p>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Role Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($role === 'student'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#0F2854] border border-blue-100">
                                                        Student
                                                    </span>
                                                <?php elseif ($role === 'supervisor'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                        Supervisor
                                                    </span>
                                                <?php elseif ($role === 'coordinator'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                        Coordinator
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-500 border border-slate-200">
                                                        System
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Name -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-[11px] action-target">
                                                    <?= htmlspecialchars($formattedAction); ?>
                                                </span>
                                            </td>

                                            <!-- Activity Description (Clean & Human-Readable) -->
                                            <td class="py-4 px-6 max-w-md text-slate-600 truncate desc-target" title="<?= htmlspecialchars($log['description']); ?>">
                                                <span class="font-medium text-slate-700"><?= htmlspecialchars($cleanDesc); ?></span>
                                            </td>

                                            <!-- IP Address -->
                                            <td class="py-4 px-6 text-right font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                                <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="py-16 text-center text-slate-500 text-xs italic">
                            No audit records logged yet.
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
                tab.className = 'role-tab px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 cursor-pointer';
            });
            tabElement.className = 'role-tab px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-[#0F2854] text-white shadow-xs cursor-pointer';

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