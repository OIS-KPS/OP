<!-- src/pages/student/profilePage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ICS OJT Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="public/assets/css/styles.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        
        <!-- Sidebar Component -->
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <!-- Right Side: Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Sticky Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <!-- Main Scrollable Body -->
            <main class="p-6 max-w-5xl w-full mx-auto space-y-5 flex-1">

                <!-- Page Header Title -->
                <div>
                    <h2 class="text-base font-bold text-slate-900">Student Profile</h2>
                    <p class="text-slate-500 text-xs mt-0.5">View your account details and assigned internship placement.</p>
                </div>

                <!-- Profile Header Banner -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <!-- Avatar / Photo -->
                        <div class="w-12 h-12 rounded-xl bg-[#0F2854]/5 border border-[#0F2854]/10 flex items-center justify-center text-[#0F2854] text-base font-bold overflow-hidden shrink-0">
                            <?php 
                            $avatar = !empty($student['avatar_url']) ? $student['avatar_url'] : ($_SESSION['user_picture'] ?? null);
                            ?>
                            <?php if (!empty($avatar)): ?>
                                <img src="<?= htmlspecialchars($avatar); ?>" alt="Profile Photo" class="w-full h-full object-cover" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <?= !empty($student['name']) ? strtoupper(substr($student['name'], 0, 1)) : 'S'; ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900"><?= htmlspecialchars($student['name'] ?? 'Student'); ?></h3>
                            <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($student['email'] ?? $_SESSION['email'] ?? ''); ?></p>
                            <p class="text-[11px] font-semibold text-[#0F2854] mt-0.5">ID: <?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?></p>
                        </div>
                    </div>

                    <!-- Edit Profile Button -->
                    <a href="edit_profile.php" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200/80 transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                </div>

                <!-- Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- 1. Academic Information Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#0F2854]">Academic Information</h4>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Full Name</p>
                                <p class="text-xs font-medium text-slate-900 mt-0.5"><?= htmlspecialchars($student['name'] ?? 'Student'); ?></p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Email Address</p>
                                <p class="text-xs font-medium text-slate-900 mt-0.5"><?= htmlspecialchars($student['email'] ?? $_SESSION['email'] ?? ''); ?></p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Student ID</p>
                                <p class="text-xs font-medium text-slate-900 mt-0.5"><?= htmlspecialchars($student['student_number'] ?? 'N/A'); ?></p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Program / Department</p>
                                <p class="text-xs font-medium text-slate-900 mt-0.5"><?= htmlspecialchars($student['program'] ?? 'BSIT'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Internship Placement Details Card (Read-Only) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                        <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#0F2854]">Internship Placement</h4>
                            <span class="text-[10px] bg-slate-100 text-slate-500 font-medium px-2 py-0.5 rounded-md">Read-Only</span>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Company / Agency</p>
                                <p class="text-xs font-medium text-slate-900 mt-0.5">
                                    <?= !empty($student['company_name']) ? htmlspecialchars($student['company_name']) : '<span class="text-slate-400 italic">Not Assigned Yet</span>'; ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Assigned Supervisor</p>
                                <p class="text-xs font-medium text-slate-900 mt-0.5">
                                    <?= !empty($student['supervisor_name']) ? htmlspecialchars($student['supervisor_name']) : '<span class="text-slate-400 italic">Not Assigned Yet</span>'; ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Placement Status</p>
                                <div class="mt-1">
                                    <?php if (!empty($student['company_name'])): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200/50">
                                            ● Active Placement
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-200/50">
                                            ● Pending Assignment
                                        </span>
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