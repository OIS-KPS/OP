<!-- src/pages/coordinator/auditLogsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Audit Logs - Coordinator Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <div class="flex min-h-screen">
        
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">System Activity & Audit Logs</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Chronological record of user actions, logins, and report status changes.</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
                        Showing last <?= count($logs); ?> events
                    </span>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                    <th class="py-3 px-5">Timestamp</th>
                                    <th class="py-3 px-5">User</th>
                                    <th class="py-3 px-5">Role</th>
                                    <th class="py-3 px-5">Action</th>
                                    <th class="py-3 px-5">Description</th>
                                    <th class="py-3 px-5 text-right">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-slate-400 italic">No audit records logged yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-3.5 px-5 whitespace-nowrap font-mono text-[11px] text-slate-500">
                                                <?= date("M d, Y \a\\t g:i A", strtotime($log['created_at'])); ?>
                                            </td>
                                            <td class="py-3.5 px-5 font-bold text-slate-900 whitespace-nowrap">
                                                <?= htmlspecialchars($log['user_name'] ?? 'System / Anonymous'); ?>
                                            </td>
                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                                    <?= htmlspecialchars($log['role']); ?>
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-5 font-semibold text-[#0F2854] whitespace-nowrap">
                                                <?= htmlspecialchars($log['action']); ?>
                                            </td>
                                            <td class="py-3.5 px-5 max-w-md text-slate-600 truncate" title="<?= htmlspecialchars($log['description']); ?>">
                                                <?= htmlspecialchars($log['description']); ?>
                                            </td>
                                            <td class="py-3.5 px-5 text-right font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                                <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

</body>
</html>