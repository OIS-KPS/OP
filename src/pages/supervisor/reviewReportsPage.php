<!-- src/pages/supervisor/reviewReportsPage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Reports - Supervisor Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/styles.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/supervisor_sidebar.php'; ?>

        <!-- Right Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Shared Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Scroll Area -->
            <main class="p-6 max-w-7xl w-full mx-auto space-y-5 flex-1 relative">

                <!-- Header Banner Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h1 class="text-base font-bold text-slate-900 leading-snug">Review Weekly Accomplishment Reports</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Filter, evaluate, and approve submitted accomplishment reports from your interns.</p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/60 text-xs">
                        <a href="review_reports.php?status=All" class="px-3.5 py-1.5 rounded-lg font-semibold transition-all <?= $filter_status === 'All' ? 'bg-white text-[#0F2854] shadow-2xs' : 'text-slate-500 hover:text-slate-800'; ?>">
                            All
                        </a>
                        <a href="review_reports.php?status=Pending" class="px-3.5 py-1.5 rounded-lg font-semibold transition-all <?= $filter_status === 'Pending' ? 'bg-white text-amber-600 shadow-2xs' : 'text-slate-500 hover:text-slate-800'; ?>">
                            Pending
                        </a>
                        <a href="review_reports.php?status=Approved" class="px-3.5 py-1.5 rounded-lg font-semibold transition-all <?= $filter_status === 'Approved' ? 'bg-white text-emerald-600 shadow-2xs' : 'text-slate-500 hover:text-slate-800'; ?>">
                            Approved
                        </a>
                        <a href="review_reports.php?status=Needs Revision" class="px-3.5 py-1.5 rounded-lg font-semibold transition-all <?= $filter_status === 'Needs Revision' ? 'bg-white text-rose-600 shadow-2xs' : 'text-slate-500 hover:text-slate-800'; ?>">
                            Revisions
                        </a>
                    </div>
                </div>

                <!-- Alert Success Messages -->
                <?php if (!empty($message)): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                        <span>✓ <?= htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Submissions Queue Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 px-5 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Submissions Queue (<?= count($reports); ?>)</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Prioritizing pending submissions awaiting review</p>
                        </div>
                    </div>

                    <?php if (!empty($reports) && count($reports) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100 font-semibold">
                                        <th class="py-3 px-5">Student Name</th>
                                        <th class="py-3 px-5">Week / Submissions</th>
                                        <th class="py-3 px-5">Date & Time Submitted</th>
                                        <th class="py-3 px-5">Status</th>
                                        <th class="py-3 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                                    <?php foreach ($reports as $item): ?>
                                            <tr class="transition-colors <?= $item['status'] === 'Pending' ? 'bg-amber-50/40 hover:bg-amber-100/50 border-l-4 border-l-amber-500' : 'hover:bg-slate-50/80'; ?>">                                            <!-- Student Info -->
                                            <td class="py-3.5 px-5">
                                                <p class="font-bold text-slate-900"><?= htmlspecialchars($item['student_name']); ?></p>
                                                <p class="text-[11px] text-slate-400">ID: <?= htmlspecialchars($item['student_number']); ?> • <?= htmlspecialchars($item['program'] ?? 'BSIT'); ?></p>
                                            </td>

                                            <!-- Week Number & Count -->
                                            <td class="py-3.5 px-5">
                                                <p class="font-bold text-slate-800">Week <?= htmlspecialchars($item['week_number']); ?></p>
                                                <p class="text-[10px] font-semibold text-[#0F2854]"><?= intval($item['total_submitted_count'] ?? 1); ?> WARs Submitted</p>
                                            </td>

                                            <!-- Date & Time -->
                                            <td class="py-3.5 px-5 text-slate-600 font-medium">
                                                <?= !empty($item['created_at']) ? date("M d, Y \a\\t g:i A", strtotime($item['created_at'])) : '—'; ?>
                                            </td>

                                            <!-- Status -->
                                            <td class="py-3.5 px-5">
                                                <?php if ($item['status'] === 'Approved'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200/50">● Approved</span>
                                                <?php elseif ($item['status'] === 'Pending'): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-200/50">● Pending</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[11px] font-medium border border-rose-200/50">● Revision Needed</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Button -->
                                            <td class="py-3.5 px-5 text-right">
                                                <a href="review_reports.php?review_id=<?= $item['id']; ?><?= $filter_status !== 'All' ? '&status=' . $filter_status : ''; ?>" class="px-4 py-1.5 <?= $item['status'] === 'Pending' ? 'bg-[#0F2854] text-white hover:bg-blue-900' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?> text-[11px] font-semibold rounded-full border border-slate-200 transition-all shadow-2xs inline-block">
                                                    <?= $item['status'] === 'Pending' ? 'Review' : 'View Details'; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 px-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 text-base font-bold">📋</div>
                            <h4 class="text-sm font-semibold text-slate-800">No reports found</h4>
                            <p class="text-xs text-slate-500 max-w-xs mx-auto mt-0.5">There are currently no reports matching the selected filter criteria.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- DYNAMIC EVALUATION / READ-ONLY MODAL OVERLAY -->
    <!-- ======================================================= -->
    <?php if ($activeReport): ?>
        <?php $isApproved = ($activeReport['status'] === 'Approved'); ?>

        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 sm:p-6 overflow-y-auto">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-3xl w-full overflow-hidden space-y-4 p-6 relative my-auto">
                
                <!-- Close Button 'X' -->
                <a href="review_reports.php<?= $filter_status !== 'All' ? '?status=' . $filter_status : ''; ?>" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm font-bold transition-all z-10">
                    ✕
                </a>

                <!-- Modal Title & Header -->
                <div class="border-b border-slate-100 pb-3 pr-10 flex flex-wrap justify-between items-start gap-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            <?= htmlspecialchars($activeReport['student_name']); ?> - Week <?= htmlspecialchars($activeReport['week_number']); ?>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Submitted on <?= date("F d, Y \a\\t g:i A", strtotime($activeReport['created_at'])); ?></p>
                    </div>

                    <!-- Status Pill Header Tag -->
                    <div class="mr-6">
                        <?php if ($isApproved): ?>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold rounded-full">
                                ● Approved
                            </span>
                        <?php elseif ($activeReport['status'] === 'Needs Revision'): ?>
                            <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold rounded-full">
                                ● Revision Requested
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-bold rounded-full">
                                ● Pending Review
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2-Column Split: Document Viewer vs Details/Action -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- LEFT COLUMN: PDF Document View -->
                    <div class="bg-slate-50 rounded-xl border border-slate-200/80 p-4 flex flex-col justify-between space-y-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Attached PDF Document</p>
                            <p class="text-xs text-slate-500 mt-0.5">Submitted accomplishment report document</p>
                        </div>

                        <?php if (!empty($activeReport['attachment_path'])): ?>
                            <div class="bg-white rounded-xl p-5 border border-slate-200 flex-1 flex flex-col items-center justify-center text-center space-y-3 shadow-2xs min-h-[180px]">
                                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-2xl font-bold">📄</div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Week <?= htmlspecialchars($activeReport['week_number']); ?> Document</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">PDF File Attachment</p>
                                </div>
                                <a href="../<?= htmlspecialchars($activeReport['attachment_path']); ?>" target="_blank" class="px-4 py-1.5 bg-[#0F2854] text-white text-xs font-semibold rounded-xl hover:bg-blue-900 transition-all shadow-2xs">
                                    Open PDF ↗
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="flex-1 flex items-center justify-center text-xs text-slate-400 italic">
                                No attachment uploaded.
                            </div>
                        <?php endif; ?>

                        <!-- Quick Link to Full Student History -->
                        <div class="pt-2 border-t border-slate-200/60">
                            <a href="interns.php?id=<?= $activeReport['student_id']; ?>" class="w-full text-center text-xs font-semibold text-[#0F2854] hover:underline flex items-center justify-center gap-1.5 py-1">
                                <span>View <?= htmlspecialchars($activeReport['student_name']); ?>'s Full WAR History</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Extracted Entities, Ratios, & Form / Read-Only Display -->
                    <div class="space-y-4 flex flex-col justify-between">
                        
                        <!-- Extracted IT Entities -->
                        <div class="bg-slate-50 rounded-xl border border-slate-200/80 p-3.5 space-y-2">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Extracted Entities (IT Competencies)</p>
                            <ul class="text-xs text-slate-700 font-medium space-y-1.5">
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 font-bold">✓</span> Network Configuration
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 font-bold">✓</span> Hardware Troubleshooting
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 font-bold">✓</span> Software Installation
                                </li>
                            </ul>
                        </div>

                        <!-- Task Breakdown Progress Ratios -->
                        <div class="space-y-2">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Task Breakdown Ratio</p>
                            
                            <!-- IT Related -->
                            <div>
                                <div class="flex justify-between text-[11px] font-semibold mb-1">
                                    <span class="text-[#0F2854]">IT Related Work</span>
                                    <span><?= intval($activeReport['it_percent'] ?? 85); ?>%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                                    <div class="h-full bg-[#0F2854] rounded-full" style="width: <?= intval($activeReport['it_percent'] ?? 85); ?>%"></div>
                                </div>
                            </div>

                            <!-- Clerical -->
                            <div>
                                <div class="flex justify-between text-[11px] font-semibold mb-1">
                                    <span class="text-slate-600">Clerical Work</span>
                                    <span><?= intval($activeReport['clerical_percent'] ?? 15); ?>%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                                    <div class="h-full bg-slate-400 rounded-full" style="width: <?= intval($activeReport['clerical_percent'] ?? 15); ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- DYNAMIC CONDITION: APPROVED READ-ONLY VS PENDING EDITABLE FORM -->
                        <?php if ($isApproved): ?>
                            <!-- READ-ONLY BLOCK FOR APPROVED REPORTS -->
                            <div class="space-y-3 pt-2">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Supervisor Feedback</p>
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 font-medium italic">
                                        "<?= htmlspecialchars(!empty($activeReport['remarks']) ? $activeReport['remarks'] : 'Report verified and approved.'); ?>"
                                    </div>
                                </div>

                                <div class="flex items-center justify-end pt-2 border-t border-slate-100">
                                    <a href="review_reports.php<?= $filter_status !== 'All' ? '?status=' . $filter_status : ''; ?>" class="px-5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-full border border-slate-200 transition-all">
                                        Close
                                    </a>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- EDITABLE FORM FOR PENDING / REVISION REPORTS -->
                            <form method="POST" action="review_reports.php<?= $filter_status !== 'All' ? '?status=' . $filter_status : ''; ?>" class="space-y-3 pt-2">
                                <input type="hidden" name="action_report_id" value="<?= $activeReport['id']; ?>">
                                <input type="hidden" name="student_name" value="<?= htmlspecialchars($activeReport['student_name']); ?>">

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Supervisor Feedback</label>
                                    <textarea name="supervisor_remarks" rows="2" placeholder="Enter comments or instructions for required revisions..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-[#0F2854] resize-none"><?= htmlspecialchars($activeReport['remarks'] ?? ''); ?></textarea>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                    <a href="review_reports.php<?= $filter_status !== 'All' ? '?status=' . $filter_status : ''; ?>" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-full border border-slate-200 transition-all">
                                        Cancel
                                    </a>
                                    <button type="submit" name="status" value="Needs Revision" class="px-4 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-full border border-rose-200 transition-all cursor-pointer">
                                        Request Revision
                                    </button>
                                    <button type="submit" name="status" value="Approved" class="px-4 py-1.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-semibold rounded-full border border-[#0F2854] transition-all shadow-2xs cursor-pointer">
                                        Approve Report
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                    </div>

                </div>

            </div>
        </div>
    <?php endif; ?>

</body>
</html>