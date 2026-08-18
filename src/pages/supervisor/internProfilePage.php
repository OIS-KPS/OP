<!-- src/pages/supervisor/internProfilePage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($student['name'] ?? 'Intern'); ?> - Intern Portfolio</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Global Custom Stylesheet -->
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Side Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Scrollable Body -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1">

                <!-- Back Navigation Link -->
                <div>
                    <a href="interns.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0F2854] hover:underline bg-white px-3.5 py-2 rounded-xl border border-slate-200/80 shadow-2xs transition-all hover:bg-slate-50">
                        <span>← Back to Interns List</span>
                    </a>
                </div>

                <!-- Intern Profile Summary Banner Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#0F2854]/10 text-[#0F2854] flex items-center justify-center font-extrabold text-lg shrink-0 border border-[#0F2854]/20 overflow-hidden">
                            <?php if (!empty($student['avatar_url'])): ?>
                                <img src="<?= htmlspecialchars($student['avatar_url']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= !empty($student['name']) ? strtoupper(substr($student['name'], 0, 1)) : 'I'; ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-base font-bold text-slate-900 leading-snug"><?= htmlspecialchars($student['name'] ?? 'Intern'); ?></h1>
                            <p class="text-slate-500 text-xs mt-0.5">
                                ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?></span> • 
                                Program: <span class="font-semibold text-slate-700"><?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></span> • 
                                Email: <span class="font-semibold text-slate-700"><?= htmlspecialchars($student['email'] ?? 'N/A'); ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-semibold tracking-wide">
                        ● Active Intern
                    </div>
                </div>

                <!-- Submitted Accomplishment Reports Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 px-5 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Submitted Accomplishment Reports</h3>
                            <p class="text-slate-400 text-[11px] mt-0.5">Weekly logs submitted by <?= htmlspecialchars($student['name'] ?? 'student'); ?></p>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium"><?= count($reports); ?> Submissions</span>
                    </div>

                    <?php if (!empty($reports) && count($reports) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Week</th>
                                        <th class="py-3 px-5">Date Submitted</th>
                                        <th class="py-3 px-5">Document Attachment</th>
                                        <th class="py-3 px-5">Status</th>
                                        <th class="py-3 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                                    <?php foreach ($reports as $report): 
                                        $status = strtolower($report['status'] ?? 'pending');
                                        $filePath = $report['file_path'] ?? $report['attachment_path'] ?? '';
                                        $submittedAt = $report['submitted_at'] ?? $report['created_at'] ?? null;
                                    ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <!-- Week Number -->
                                            <td class="py-3 px-5 font-bold text-slate-900">
                                                Week <?= htmlspecialchars($report['week_number']); ?>
                                            </td>

                                            <!-- Date Submitted -->
                                            <td class="py-3 px-5 text-slate-500">
                                                <?= !empty($submittedAt) ? date("M d, Y - h:i A", strtotime($submittedAt)) : '—'; ?>
                                            </td>

                                            <!-- PDF Attachment Link -->
                                            <td class="py-3 px-5">
                                                <?php if (!empty($filePath)): ?>
                                                    <a href="/ICS-PORTAL/<?= htmlspecialchars($filePath); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium rounded-lg border border-slate-200 transition-all">
                                                        <span>📄 PDF Document</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-[11px] italic">No document</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Status Badge -->
                                            <td class="py-3 px-5">
                                                <?php if ($status === 'approved'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200/50">● Approved</span>
                                                <?php elseif ($status === 'pending'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-200/50">● Under Review</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[11px] font-medium border border-rose-200/50">● Needs Revision</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Review / Details Action -->
                                            <td class="py-3 px-5 text-right space-x-1.5">
                                                <?php if (!empty($filePath)): ?>
                                                    <a href="/ICS-PORTAL/<?= htmlspecialchars($filePath); ?>" 
                                                       target="_blank" 
                                                       class="px-3.5 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-[11px] font-semibold rounded-xl transition-all shadow-2xs inline-block">
                                                        Review PDF
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 px-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 text-base font-bold">📂</div>
                            <h4 class="text-sm font-semibold text-slate-800">No reports submitted yet</h4>
                            <p class="text-xs text-slate-500 max-w-xs mx-auto mt-0.5">This intern has not submitted any accomplishment reports yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

<?php include __DIR__ . '/../../components/password_change_popup.php'; ?>
</body>
</html>