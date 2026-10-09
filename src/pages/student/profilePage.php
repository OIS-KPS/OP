<!-- src/pages/student/profilePage.php -->
<?php
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
                    <p class="text-slate-600 text-xs font-semibold mt-0.5">View your account details and your assigned internship placement.</p>
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

                                <?php if ($hasActivePlacement): ?>
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
                                    <?php if (!empty($student['office_name'])): ?>
                                        <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Office: <?= htmlspecialchars($student['office_name']); ?></p>
                                    <?php endif; ?>
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
                            </div>
                        </div>
                    </div>

                </div>

            </main>
        </div>
    </div>
</body>
</html>