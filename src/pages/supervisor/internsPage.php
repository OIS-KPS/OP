<!-- src/pages/supervisor/internsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Interns - Supervisor Portal</title>
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
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1">

                <!-- Header Actions Card -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">My Interns</h1>
                        <p class="text-xs font-medium text-slate-500 mt-1">
                            <?= htmlspecialchars($supervisor['company_name'] ?? 'Host Company'); ?> &bull; Track weekly report progress for your students.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                        <?= count($interns ?? []); ?> Total <?= count($interns ?? []) === 1 ? 'Student' : 'Students'; ?>
                    </span>
                </div>

                <!-- Students Table Container -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/40">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Student List & Progress</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Approved weekly reports out of 10 target weeks</p>
                        </div>
                        <span class="text-xs font-medium text-slate-400">Target: 10 Weeks</span>
                    </div>

                    <?php if (!empty($interns) && count($interns) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Student</th>
                                        <th class="py-4 px-6">Program</th>
                                        <th class="py-4 px-6">Completed Weeks</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php foreach ($interns as $intern): 
                                        $submitted = intval($intern['submitted_reports'] ?? 0);
                                        $target = 10;
                                        $progressPercent = min(100, round(($submitted / $target) * 100));
                                    ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors group">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-200/80 group-hover:border-[#0F2854] transition-colors">
                                                        <?php if (!empty($intern['avatar_url'])): ?>
                                                            <img src="<?= htmlspecialchars($intern['avatar_url']); ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($intern['name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($intern['name']); ?></p>
                                                        <p class="text-[11px] text-slate-400 font-medium">ID: <?= htmlspecialchars($intern['student_number'] ?? 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Program Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200">
                                                    <?= htmlspecialchars($intern['program'] ?? 'BSIT'); ?>
                                                </span>
                                            </td>

                                            <!-- Progress Bar -->
                                            <td class="py-4 px-6 min-w-[200px]">
                                                <div class="space-y-1.5 max-w-xs">
                                                    <div class="flex items-center justify-between text-[11px] font-semibold text-slate-600">
                                                        <span><?= $submitted; ?> of <?= $target; ?> Weeks</span>
                                                        <span class="font-bold text-slate-900"><?= $progressPercent; ?>%</span>
                                                    </div>
                                                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200/70">
                                                        <div class="h-full bg-[#0F2854] rounded-full transition-all duration-300" style="width: <?= $progressPercent; ?>%"></div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                            </td>

                                            <!-- Action Link -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <a href="interns.php?id=<?= $intern['id']; ?>" 
                                                   class="px-3.5 py-1.5 bg-slate-100 hover:bg-[#0F2854] hover:text-white text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 shadow-2xs transition-all inline-flex items-center gap-1.5 group/btn cursor-pointer">
                                                    <span>View Reports</span>
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-14 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center mx-auto text-lg font-bold border border-slate-200">
                                👥
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">No students assigned</h4>
                            <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto">You do not have any students assigned to your department yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>