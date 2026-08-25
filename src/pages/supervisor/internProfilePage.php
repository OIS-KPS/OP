<!-- src/pages/supervisor/internProfilePage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($student['name'] ?? 'Student'); ?> - Weekly Reports</title>
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

                <!-- Navigation Action -->
                <div>
                    <a href="interns.php" class="inline-flex items-center gap-2 text-xs font-bold text-[#0F2854] hover:text-blue-900 bg-white px-4 py-2 rounded-xl border border-slate-200/80 shadow-2xs transition-all hover:bg-slate-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Back to Students</span>
                    </a>
                </div>

                <!-- Student Profile Banner -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-13 h-13 rounded-2xl bg-blue-50 text-[#0F2854] flex items-center justify-center font-bold text-base shrink-0 border border-blue-100 overflow-hidden shadow-2xs">
                            <?php if (!empty($student['avatar_url'])): ?>
                                <img src="<?= htmlspecialchars($student['avatar_url']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= !empty($student['name']) ? strtoupper(substr($student['name'], 0, 1)) : 'S'; ?>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-0.5">
                            <h1 class="text-base font-bold text-slate-900 leading-snug"><?= htmlspecialchars($student['name'] ?? 'Student'); ?></h1>
                            <p class="text-xs font-medium text-slate-500">
                                ID: <span class="text-slate-800 font-semibold"><?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?></span> &bull; 
                                Program: <span class="text-slate-800 font-semibold"><?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></span> &bull; 
                                <?= htmlspecialchars($student['email'] ?? 'N/A'); ?>
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Active Student
                    </span>
                </div>

                <!-- Weekly Reports Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/40">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Weekly Reports</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">All submissions from <?= htmlspecialchars($student['name'] ?? 'this student'); ?></p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1 rounded-lg border border-slate-200/70 shadow-2xs">
                            <?= count($reports ?? []); ?> <?= count($reports ?? []) === 1 ? 'Report' : 'Reports'; ?>
                        </span>
                    </div>

                    <?php if (!empty($reports) && count($reports) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100 font-bold">
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Date Submitted</th>
                                        <th class="py-4 px-6">Attached File</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php foreach ($reports as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                        $filePath = $report['file_path'] ?? $report['attachment_path'] ?? '';
                                        $submittedAt = $report['submitted_at'] ?? $report['created_at'] ?? null;
                                        $isApproved = ($status === 'approved');
                                    ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            
                                            <!-- Week Number Chip -->
                                            <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                                                    Week <?= htmlspecialchars($report['week_number']); ?>
                                                </span>
                                            </td>

                                            <!-- Submitted Date -->
                                            <td class="py-4 px-6 text-slate-600 font-medium whitespace-nowrap">
                                                <?= !empty($submittedAt) ? date("M d, Y \a\\t g:i A", strtotime($submittedAt)) : '—'; ?>
                                            </td>

                                            <!-- File Link -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if (!empty($filePath)): ?>
                                                    <a href="/ICS-PORTAL/<?= htmlspecialchars(ltrim($filePath, '/')); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200/80 transition-all cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                        <span>View File</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-xs italic">No file</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isApproved): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Approved
                                                    </span>
                                                <?php elseif ($status === 'pending'): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Waiting for Review
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                        Needs Changes
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <?php if (!empty($filePath)): ?>
                                                    <a href="review_reports.php?id=<?= $report['id'] ?? ''; ?>" 
                                                       class="px-4 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-all shadow-xs inline-flex items-center gap-1.5">
                                                        <span><?= $isApproved ? 'View Details' : 'Review Report'; ?></span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-14 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center mx-auto text-lg font-bold border border-slate-200">
                                📂
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">No reports submitted yet</h4>
                            <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto">This student has not uploaded any weekly reports for review.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>