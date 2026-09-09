<!-- src/pages/supervisor/internProfilePage.php -->
<?php
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$studentName = $student['name'] ?? 'Student';
$studentNumber = $student['student_number'] ?? 'N/A';
$studentProgram = $student['program'] ?? 'BSIT';
$studentEmail = $student['email'] ?? 'N/A';
$studentAvatar = $student['avatar_url'] ?? '';
$reportsList = $reports ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($studentName); ?> - Weekly Reports</title>
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

                <!-- Navigation Action -->
                <div>
                    <a href="interns.php" class="inline-flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-950 bg-white hover:bg-slate-100 px-4 py-2 rounded-xl border border-slate-300 shadow-2xs transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Back to Students</span>
                    </a>
                </div>

                <!-- Student Profile Banner -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-[#0F2854] flex items-center justify-center font-black text-lg shrink-0 border border-slate-300 overflow-hidden shadow-2xs">
                            <?php if (!empty($studentAvatar)): ?>
                                <img src="<?= e($studentAvatar); ?>" class="w-full h-full object-cover" alt="<?= e($studentName); ?>" onerror="this.onerror=null; this.parentElement.innerHTML='<?= e(strtoupper(substr($studentName, 0, 1))); ?>';">
                            <?php else: ?>
                                <?= e(strtoupper(substr($studentName, 0, 1))); ?>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-0.5">
                            <h1 class="text-base sm:text-lg font-black text-slate-950 tracking-tight leading-snug"><?= e($studentName); ?></h1>
                            <p class="text-xs font-semibold text-slate-600">
                                ID: <strong class="text-slate-900"><?= e($studentNumber); ?></strong> &bull; 
                                Program: <strong class="text-slate-900"><?= e($studentProgram); ?></strong> &bull; 
                                <span class="text-slate-500 font-medium"><?= e($studentEmail); ?></span>
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Active Student
                    </span>
                </div>

                <!-- Weekly Reports Table -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-200/70 flex justify-between items-center bg-slate-50/60">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 tracking-wider uppercase">Weekly Reports</h3>
                            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">All submissions from <?= e($studentName); ?></p>
                        </div>
                        <span class="inline-flex items-center px-3.5 py-1 rounded-lg bg-white border border-slate-300 text-xs font-black text-slate-900 shadow-2xs">
                            <?= count($reportsList); ?> <?= count($reportsList) === 1 ? 'Report' : 'Reports'; ?>
                        </span>
                    </div>

                    <?php if (!empty($reportsList) && count($reportsList) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/70 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                        <th class="py-4 px-6">Week #</th>
                                        <th class="py-4 px-6">Date Submitted</th>
                                        <th class="py-4 px-6">Attached File</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/80 text-slate-800">
                                    <?php foreach ($reportsList as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                        $filePath = $report['file_path'] ?? $report['attachment_path'] ?? '';
                                        $submittedAt = $report['submitted_at'] ?? $report['created_at'] ?? null;
                                        $approvedAt = $report['approved_at'] ?? null;
                                        $isApproved = ($status === 'approved');
                                        $isPending = ($status === 'pending');
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            
                                            <!-- Week Number Chip -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-100 text-slate-900 font-extrabold text-xs border border-slate-300">
                                                    Week <?= e($report['week_number']); ?>
                                                </span>
                                            </td>

                                            <!-- Submitted Date -->
                                            <td class="py-4 px-6 text-slate-700 font-semibold whitespace-nowrap">
                                                <?= !empty($submittedAt) ? e(date("M d, Y \a\\t g:i A", strtotime($submittedAt))) : '—'; ?>
                                            </td>

                                            <!-- File Link -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if (!empty($filePath)): ?>
                                                    <a href="/ICS-PORTAL/<?= e(ltrim(str_replace('\\', '/', $filePath), '/')); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 shadow-2xs transition-all cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                        <span>View File</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-xs italic">No file</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Status Badge with Approval Timestamp -->
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                <?php if ($isApproved): ?>
                                                    <div class="flex flex-col items-start gap-1">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                            Approved
                                                        </span>
                                                        <?php if (!empty($approvedAt)): ?>
                                                            <span class="text-[11px] font-semibold text-slate-500 pl-0.5">
                                                                <?= e(date("M d, Y \a\\t g:i A", strtotime($approvedAt))); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif ($isPending): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        Waiting for Review
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100/80 text-rose-900 border border-rose-300 text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                        Needs Changes
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <a href="review_reports.php?review_id=<?= (int)$report['id']; ?>&status=All" 
                                                   class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                    <span>View Details</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 px-4 space-y-2">
                            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto text-lg font-black border border-slate-300">
                                📂
                            </div>
                            <h4 class="text-sm font-black text-slate-900">No reports submitted yet</h4>
                            <p class="text-xs font-semibold text-slate-600 max-w-xs mx-auto">This student has not uploaded any weekly reports for review.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

</body>
</html>