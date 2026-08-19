<!-- src/pages/supervisor/internsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Interns - Supervisor Portal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Global Custom Stylesheet -->
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Page Scrollable Body -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1">

                <!-- Header Banner -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Assigned Interns</h1>
                        <p class="text-slate-500 text-xs mt-0.5">
                            <span class="font-semibold text-slate-700"><?= htmlspecialchars($supervisor['company_name']); ?></span> • 
                            <?= count($interns); ?> <?= count($interns) === 1 ? 'intern' : 'interns'; ?> assigned this semester
                        </p>
                    </div>
                    <div class="px-3 py-1 rounded-full bg-blue-50 text-[#0F2854] border border-blue-200/60 text-[11px] font-semibold tracking-wide">
                        ● <?= count($interns); ?> Total Assigned
                    </div>
                </div>

                <!-- Interns List Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 px-5 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Intern Roster & Submission Progress</h3>
                            <p class="text-slate-400 text-[11px] mt-0.5">Track weekly accomplishment report progress for each student</p>
                        </div>
                    </div>

                    <?php if (!empty($interns) && count($interns) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Intern Name</th>
                                        <th class="py-3 px-5">Program</th>
                                        <th class="py-3 px-5">WAR Progress</th>
                                        <th class="py-3 px-5">Status</th>
                                        <th class="py-3 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                                    <?php foreach ($interns as $intern): ?>
                                        <?php 
                                            $submitted = intval($intern['submitted_reports'] ?? 0);
                                            $target = 10; // Target 10 weeks
                                            $progressPercent = min(100, round(($submitted / $target) * 100));
                                        ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <!-- Intern Name & ID -->
                                            <td class="py-3 px-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-200">
                                                        <?php if (!empty($intern['avatar_url'])): ?>
                                                            <img src="<?= htmlspecialchars($intern['avatar_url']); ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($intern['name'], 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900"><?= htmlspecialchars($intern['name']); ?></p>
                                                        <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($intern['student_number']); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Program Badge -->
                                            <td class="py-3 px-5 font-semibold text-slate-700">
                                                <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded-md text-[10px] font-bold text-slate-600">
                                                    <?= htmlspecialchars($intern['program'] ?? 'BSIT'); ?>
                                                </span>
                                            </td>

                                            <!-- Progress Bar -->
                                            <td class="py-3 px-5 min-w-[180px]">
                                                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-600 mb-1">
                                                    <span><?= $submitted; ?> / <?= $target; ?> Reports</span>
                                                    <span class="text-[#0F2854] font-bold"><?= $progressPercent; ?>%</span>
                                                </div>
                                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200/60">
                                                    <div class="h-full bg-[#0F2854] rounded-full transition-all duration-300" style="width: <?= $progressPercent; ?>%"></div>
                                                </div>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-3 px-5">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200/50">
                                                    ● Active Intern
                                                </span>
                                            </td>

                                            <!-- Action Link -->
                                            <td class="py-3 px-5 text-right">
                                                <a href="interns.php?id=<?= $intern['id']; ?>" 
                                                   class="px-3.5 py-1.5 bg-slate-100 hover:bg-[#0F2854] hover:text-white text-slate-700 text-[11px] font-semibold rounded-xl border border-slate-200 transition-all shadow-2xs inline-flex items-center gap-1.5 group">
                                                    <span>View Portfolio</span>
                                                    <span class="transition-transform duration-200 group-hover:translate-x-0.5">→</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 px-4">
                            <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center mx-auto mb-2 text-lg font-bold">
                                👥
                            </div>
                            <h4 class="text-sm font-semibold text-slate-800">No assigned interns found</h4>
                            <p class="text-xs text-slate-500 max-w-xs mx-auto mt-0.5">You currently do not have any students assigned by the OJT coordinator.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>