<!-- src/pages/supervisor/profilePage.php -->
<?php
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$supName    = !empty($profile['name']) ? $profile['name'] : 'Supervisor';
$supEmail   = $profile['email'] ?? '';
$supCompany = $profile['company_name'] ?? null;
$supAddress = $profile['company_address'] ?? '';
$supJob     = $profile['job_title'] ?? '';
$supPhone   = $profile['contact_number'] ?? '';
$supAvatar  = !empty($profile['avatar_url']) ? $profile['avatar_url'] : ($_SESSION['user_picture'] ?? null);
$isActive   = strtolower($profile['status'] ?? 'active') === 'active';
$initial    = strtoupper(substr($supName, 0, 1));
$notProvided = '<span class="text-slate-400 font-semibold italic">Not provided</span>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Supervisor Portal</title>
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

            <main class="p-8 max-w-5xl w-full mx-auto space-y-6 flex-1">

                <!-- Alert Messages -->
                <?php if (!empty($flashSuccess)): ?>
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-emerald-700 font-black text-sm">✓</span>
                            <p class="font-bold text-xs"><?= e($flashSuccess); ?></p>
                        </div>
                        <a href="profile.php" class="text-xs font-bold text-emerald-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flashError)): ?>
                    <div class="bg-rose-50 border border-rose-300 text-rose-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-700 font-black text-sm">✕</span>
                            <p class="font-bold text-xs"><?= e($flashError); ?></p>
                        </div>
                        <a href="profile.php" class="text-xs font-bold text-rose-800 hover:underline">Dismiss</a>
                    </div>
                <?php endif; ?>

                <!-- Page Header Title -->
                <div>
                    <h2 class="text-base font-extrabold text-slate-950 leading-snug tracking-tight">Supervisor Profile</h2>
                    <p class="text-slate-600 text-xs font-semibold mt-0.5">View your account details and manage your professional contact information.</p>
                </div>

                <!-- Identity Card -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/90 shadow-xs flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-300 flex items-center justify-center text-[#0F2854] text-lg font-black overflow-hidden shrink-0 shadow-2xs">
                            <?php if (!empty($supAvatar)): ?>
                                <img src="<?= e($supAvatar); ?>" alt="Profile Photo" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.onerror=null; this.parentElement.innerHTML='<?= e($initial); ?>';">
                            <?php else: ?>
                                <?= e($initial); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug"><?= e($supName); ?></h3>
                            <p class="text-xs font-semibold text-slate-600"><?= e($supEmail); ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100/80 text-blue-900 border border-blue-300 text-xs font-bold shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Industry Supervisor
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

                <!-- Account & Professional Information Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-200/70 pb-3 flex justify-between items-center">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Account &amp; Professional Information</h4>
                        <button type="button" onclick="openProfileModal()" class="px-4 py-2 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                            <span>Edit</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Full Name</p>
                            <p class="text-xs font-extrabold text-slate-950 mt-0.5"><?= e($supName); ?></p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Email Address</p>
                            <p class="text-xs font-semibold text-slate-800 mt-0.5"><?= e($supEmail); ?></p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Company / Organization</p>
                            <p class="text-xs font-extrabold text-slate-950 mt-0.5">
                                <?= !empty($supCompany) ? e($supCompany) : '<span class="text-slate-400 font-semibold italic">Not Assigned Yet</span>'; ?>
                            </p>
                            <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Managed by the OJT Coordinator.</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Company Address</p>
                            <p class="text-xs font-extrabold text-slate-950 mt-0.5">
                                <?= $supAddress !== '' ? e($supAddress) : $notProvided; ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Job Title / Position</p>
                            <p class="text-xs font-extrabold text-slate-950 mt-0.5">
                                <?= $supJob !== '' ? e($supJob) : $notProvided; ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-700 uppercase tracking-wider">Contact Number</p>
                            <p class="text-xs font-extrabold text-slate-950 mt-0.5">
                                <?= $supPhone !== '' ? e($supPhone) : $notProvided; ?>
                            </p>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-300 shadow-2xl max-w-lg w-full p-7 space-y-5">

            <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-950 tracking-tight">Edit Professional Information</h3>
                    <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Name, email and company are managed by the OJT Coordinator</p>
                </div>
                <button type="button" onclick="closeProfileModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-xs font-bold flex items-center justify-center border border-slate-300 transition-all cursor-pointer">✕</button>
            </div>

            <form method="POST" action="profile.php" class="space-y-4 text-xs">
                <input type="hidden" name="action" value="update_profile">

                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Job Title / Position</label>
                    <input type="text" name="job_title" maxlength="150" value="<?= e($supJob); ?>" placeholder="Enter job title or position" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-900 font-semibold focus:outline-none focus:border-[#0F2854]">
                </div>

                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Contact Number</label>
                    <input type="tel" name="contact_number" maxlength="20" pattern="[0-9+\-\s()]{7,20}" title="7-20 characters: digits, +, -, spaces or parentheses" value="<?= e($supPhone); ?>" placeholder="Enter contact number" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-900 font-semibold focus:outline-none focus:border-[#0F2854]">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200/70">
                    <button type="button" onclick="closeProfileModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openProfileModal() {
        document.getElementById('profileModal').classList.remove('hidden');
    }
    function closeProfileModal() {
        document.getElementById('profileModal').classList.add('hidden');
    }
    </script>
</body>
</html>
