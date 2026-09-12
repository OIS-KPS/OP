<!-- src/pages/student/profilePage.php -->
<?php
$placementStatus    = strtolower($student['placement_request_status'] ?? 'none');
$isPlacementPending = ($placementStatus === 'pending');
$isPlacementRejected= ($placementStatus === 'rejected');
$hasActivePlacement = !empty($student['company_name']);
$studentNumber      = !empty($student['student_number']) ? $student['student_number'] : 'N/A';
$studentName        = !empty($student['student_name']) ? $student['student_name'] : 'Student';
$studentEmail       = !empty($student['student_email']) ? $student['student_email'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ICS OJT Portal</title>
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
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Sticky Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-5xl w-full mx-auto space-y-6 flex-1">

                <!-- Alert Messages -->
                <?php if (!empty($flashSuccess)): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black text-sm">✓</span>
                            <p class="font-bold text-xs"><?= htmlspecialchars($flashSuccess); ?></p>
                        </div>
                        <a href="profile.php" class="text-xs font-bold text-emerald-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flashError)): ?>
                    <div class="bg-rose-50 border border-rose-300 text-rose-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-700 font-black text-sm">✕</span>
                            <p class="font-bold text-xs"><?= htmlspecialchars($flashError); ?></p>
                        </div>
                        <a href="profile.php" class="text-xs font-bold text-rose-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <!-- Page Header Title -->
                <div>
                    <h2 class="text-base font-extrabold text-slate-950 leading-snug tracking-tight">Student Profile</h2>
                    <p class="text-slate-600 text-xs font-semibold mt-0.5">View your account details and manage your assigned internship placement.</p>
                </div>

                <!-- Clean Profile Identity Card -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/90 shadow-xs flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-300 flex items-center justify-center text-[#0F2854] text-lg font-black overflow-hidden shrink-0 shadow-2xs">
                            <?php 
                            $avatar = !empty($student['avatar_url']) ? $student['avatar_url'] : ($_SESSION['user_picture'] ?? null);
                            ?>
                            <?php if (!empty($avatar)): ?>
                                <img src="<?= htmlspecialchars($avatar); ?>" alt="Profile Photo" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.onerror=null; this.parentElement.innerHTML='<?= strtoupper(substr($studentName, 0, 1)); ?>';">
                            <?php else: ?>
                                <?= strtoupper(substr($studentName, 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug"><?= htmlspecialchars($studentName); ?></h3>
                            <p class="text-xs font-semibold text-slate-600"><?= htmlspecialchars($studentEmail); ?></p>
                            <p class="text-[11px] font-extrabold text-[#0F2854] mt-0.5">ID: <?= htmlspecialchars($studentNumber); ?></p>
                        </div>
                    </div>

                </div>

                <!-- Two-Column Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
                    
                    <!-- 1. Academic Information Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="border-b border-slate-200/70 pb-3">
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Academic Information</h4>
                            </div>

                            <div class="space-y-3.5 pt-3">
                                <div>
                                    <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Full Name</p>
                                    <p class="text-xs font-extrabold text-slate-950 mt-0.5"><?= htmlspecialchars($studentName); ?></p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Email Address</p>
                                    <p class="text-xs font-semibold text-slate-800 mt-0.5"><?= htmlspecialchars($studentEmail); ?></p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Student ID</p>
                                    <p class="text-xs font-extrabold text-slate-950 mt-0.5"><?= htmlspecialchars($studentNumber); ?></p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Program / Department</p>
                                    <p class="text-xs font-semibold text-slate-800 mt-0.5"><?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Internship Placement Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 flex flex-col justify-between space-y-5">
                        <div class="space-y-4">
                            
                            <!-- Card Header with Dynamic Badge -->
                            <div class="border-b border-slate-200/70 pb-3 flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Internship Placement</h4>
                                </div>

                                <?php if ($isPlacementPending): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 border border-amber-300 text-xs font-bold shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                        Pending Approval
                                    </span>
                                <?php elseif ($hasActivePlacement): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Active Placement
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-300 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Unassigned
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-3.5 text-xs">
                                <div>
                                    <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Company / Agency</p>
                                    <p class="text-xs font-extrabold text-slate-950 mt-0.5">
                                        <?= !empty($student['company_name']) ? htmlspecialchars($student['company_name']) : '<span class="text-slate-400 font-semibold italic">Not Assigned Yet</span>'; ?>
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Assigned Supervisor</p>
                                    <p class="text-xs font-extrabold text-slate-950 mt-0.5">
                                        <?= !empty($student['supervisor_name']) ? htmlspecialchars($student['supervisor_name']) : '<span class="text-slate-400 font-semibold italic">Not Assigned Yet</span>'; ?>
                                    </p>
                                    <?php if (!empty($student['supervisor_email'])): ?>
                                        <p class="text-[11px] font-semibold text-slate-500 mt-0.5"><?= htmlspecialchars($student['supervisor_email']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Pending Request Notice Box -->
                                <?php if ($isPlacementPending): ?>
                                    <div class="p-3.5 bg-amber-50/80 border border-amber-300 rounded-xl space-y-1">
                                        <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-[11px]">
                                            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Update Request Submitted</span>
                                        </div>
                                        <p class="text-[11px] text-amber-900 font-medium leading-snug">
                                            Requested Company: <strong class="font-black text-slate-950"><?= htmlspecialchars($student['requested_company_name'] ?? ''); ?></strong>. Awaiting review by the OJT Coordinator.
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <!-- Rejected Request Notice Box -->
                                <?php if ($isPlacementRejected && !empty($student['placement_rejection_reason'])): ?>
                                    <div class="p-3.5 bg-rose-50 border border-rose-300 rounded-xl space-y-1">
                                        <div class="flex items-center gap-1.5 text-rose-900 font-extrabold text-[11px]">
                                            <span>✕ Request Rejected by Coordinator</span>
                                        </div>
                                        <p class="text-[11px] text-rose-800 font-medium">
                                            <?= htmlspecialchars($student['placement_rejection_reason']); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Update Placement Button -->
                        <div class="pt-4 border-t border-slate-200/70 flex justify-end">
                            <?php if (!$isPlacementPending): ?>
                                <button type="button" onclick="openPlacementModal()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    <span><?= $hasActivePlacement ? 'Request Placement Transfer' : 'Update Placement Details'; ?></span>
                                </button>
                            <?php else: ?>
                                <button disabled class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed">
                                    Request Under Review
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            </main>
        </div>
    </div>

    <!-- Placement Request Modal -->
    <div id="placementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-300 shadow-2xl max-w-lg w-full p-7 space-y-5 animate-in fade-in zoom-in duration-200">
            
            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-950 tracking-tight">Request Internship Placement</h3>
                    <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Subject to review and confirmation by the OJT Coordinator</p>
                </div>
                <button onclick="closePlacementModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-xs font-bold flex items-center justify-center border border-slate-300 transition-all cursor-pointer">✕</button>
            </div>

            <form method="POST" action="profile.php" class="space-y-4 text-xs">
                <input type="hidden" name="action" value="request_placement_update">

                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Host Company / Agency Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required placeholder="e.g., NBSC IT Dept" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-900 font-semibold focus:outline-none focus:border-[#0F2854]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Designated Supervisor Name</label>
                        <input type="text" name="supervisor_name" placeholder="e.g., Engr. keyt" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-900 font-semibold focus:outline-none focus:border-[#0F2854]">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Supervisor Email</label>
                        <input type="email" name="supervisor_email" placeholder="e.g., coming.katelyn08@gmail.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-900 font-semibold focus:outline-none focus:border-[#0F2854]">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="closePlacementModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                        Submit for Approval
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openPlacementModal() {
        document.getElementById('placementModal').classList.remove('hidden');
    }
    function closePlacementModal() {
        document.getElementById('placementModal').classList.add('hidden');
    }
    </script>
</body>
</html>