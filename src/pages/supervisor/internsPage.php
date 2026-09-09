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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1">

                <!-- Header Actions Card -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/90 shadow-xs">
                    <div>
                        <h1 class="text-base font-extrabold text-slate-950 leading-snug tracking-tight">My Interns</h1>
                        <p class="text-xs font-semibold text-slate-600 mt-1">
                            <?= htmlspecialchars($supervisor['company_name'] ?? 'Host Company'); ?> &bull; Track student accomplishment reports and review submissions.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 text-xs font-bold shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0F2854]"></span>
                        <?= count($interns ?? []); ?> Total <?= count($interns ?? []) === 1 ? 'Student' : 'Students'; ?>
                    </span>
                </div>

                <!-- Students Table Container -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-200/70 flex justify-between items-center bg-slate-50/60">
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-900 tracking-wider uppercase">Assigned Students</h3>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Overview of submitted weekly reports per intern</p>
                        </div>
                    </div>

                    <?php if (!empty($interns) && count($interns) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Student</th>
                                        <th class="py-4 px-6">Program</th>
                                        <th class="py-4 px-6">Reports Submitted</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($interns as $intern): 
                                        $submitted = intval($intern['submitted_reports'] ?? 0);
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors group">
                                            
                                            <!-- Student Name & ID -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-300 group-hover:border-[#0F2854] transition-colors">
                                                        <?php if (!empty($intern['avatar_url'])): ?>
                                                            <img src="<?= htmlspecialchars($intern['avatar_url']); ?>" class="w-full h-full object-cover" alt="Avatar">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($intern['name'] ?? 'S', 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-extrabold text-slate-950 text-sm tracking-tight"><?= htmlspecialchars($intern['name']); ?></p>
                                                        <p class="text-[11px] text-slate-500 font-semibold">ID: <?= htmlspecialchars($intern['student_number'] ?? 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Program Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                    <?= htmlspecialchars($intern['program'] ?? 'BSIT'); ?>
                                                </span>
                                            </td>

                                            <!-- Reports Submitted Count -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-extrabold text-slate-950 text-xs"><?= $submitted; ?> <?= $submitted === 1 ? 'Report' : 'Reports'; ?></span>
                                                    <?php if ($submitted > 0): ?>
                                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 text-[10px] font-bold rounded-md border border-blue-200">
                                                            Active Logs
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-md border border-slate-200">
                                                            None Yet
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    Active
                                                </span>
                                            </td>

                                            <!-- Action Link -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <a href="interns.php?id=<?= $intern['id']; ?>" 
                                                   class="px-3.5 py-1.5 bg-slate-100 hover:bg-[#0F2854] hover:text-white text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-all inline-flex items-center gap-1.5 group/btn cursor-pointer">
                                                    <span>View Reports</span>
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-14 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                👥
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No students assigned</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">You do not have any students assigned to your department yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>