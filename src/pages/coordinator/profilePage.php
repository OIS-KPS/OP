<!-- src/pages/coordinator/profilePage.php -->
<?php
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$coName    = !empty($profile['name']) ? $profile['name'] : 'Coordinator';
$coEmail   = $profile['email'] ?? '';
$coAvatar  = !empty($profile['avatar_url']) ? $profile['avatar_url'] : ($_SESSION['user_picture'] ?? null);
$isActive  = strtolower($profile['status'] ?? 'active') === 'active';
$initial   = strtoupper(substr($coName, 0, 1));
$memberSince = !empty($profile['created_at']) ? date('F j, Y', strtotime($profile['created_at'])) : null;
$lastSignInLabel = !empty($lastSignIn) ? date('F j, Y \a\t g:i A', strtotime($lastSignIn)) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Coordinator Portal</title>
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
        <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Component -->
            <?php include __DIR__ . '/../../components/header.php'; ?>

            <main class="p-8 max-w-5xl w-full mx-auto space-y-6 flex-1">

                <!-- Page Header Title -->
                <div>
                    <h2 class="text-base font-extrabold text-slate-950 leading-snug tracking-tight">Coordinator Profile</h2>
                    <p class="text-slate-600 text-xs font-semibold mt-0.5">View your account details.</p>
                </div>

                <!-- Identity Card -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/90 shadow-xs flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-300 flex items-center justify-center text-[#0F2854] text-lg font-black overflow-hidden shrink-0 shadow-2xs">
                            <?php if (!empty($coAvatar)): ?>
                                <img src="<?= e($coAvatar); ?>" alt="Profile Photo" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.onerror=null; this.parentElement.innerHTML='<?= e($initial); ?>';">
                            <?php else: ?>
                                <?= e($initial); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug"><?= e($coName); ?></h3>
                            <p class="text-xs font-semibold text-slate-600"><?= e($coEmail); ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100/80 text-indigo-900 border border-indigo-300 text-xs font-bold shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            OJT Coordinator
                        </span>
                        <?php if ($isActive): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-xs font-bold shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                Active
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-300 text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <?= e(ucfirst($profile['status'] ?? 'inactive')); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Account Information Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6">
                    <div class="border-b border-slate-200/70 pb-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Account Information</h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 pt-3">
                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Member Since</p>
                            <p class="text-xs font-semibold text-slate-800 mt-0.5">
                                <?= $memberSince ? e($memberSince) : '<span class="text-slate-400 font-semibold italic">Not available</span>'; ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Last Sign-in</p>
                            <p class="text-xs font-semibold text-slate-800 mt-0.5">
                                <?= $lastSignInLabel ? e($lastSignInLabel) : '<span class="text-slate-400 font-semibold italic">No record</span>'; ?>
                            </p>
                        </div>
                    </div>
                </div>


            </main>
        </div>
    </div>

</body>
</html>
