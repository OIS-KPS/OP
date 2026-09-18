<?php
// auth/change_password.php
// Supports both email reset link and direct password change with advanced validation, strength meter, and 5-second redirect on success
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/MailerService.php';

// Auth Guard — must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user info including password hash for direct update validation
$stmtUser = $pdo->prepare("SELECT name, email, password_hash FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['login_error'] = "User account not found.";
    header("Location: login.php");
    exit();
}

$displayName = $user['name'];
$displayEmail = $user['email'];
$message = '';
$messageType = ''; // 'success' or 'error'
$activeTab = 'email'; // Default tab: 'email' or 'direct'
$passwordUpdatedSuccessfully = false;

// Determine the "go back" / dashboard URL based on role
$role = strtolower($_SESSION['role'] ?? 'student');
switch ($role) {
    case 'supervisor':
        $backUrl = '/ICS-PORTAL/supervisor/dashboard.php';
        break;
    case 'coordinator':
        $backUrl = '/ICS-PORTAL/coordinator/dashboard.php';
        break;
    default:
        $backUrl = '/ICS-PORTAL/dashboard.php';
        break;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionType = $_POST['action_type'] ?? 'email_link';

    if ($actionType === 'email_link') {
        $activeTab = 'email';
        try {
            // Invalidate any existing unused tokens for this user
            $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE user_id = ? AND used = 0")->execute([$userId]);

            // Generate a secure random token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Store token in database
            $insertStmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $insertStmt->execute([$userId, $token, $expiresAt]);

            // Build the reset link
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];
            $resetLink = $baseUrl . '/ICS-PORTAL/auth/reset_password.php?token=' . $token;

            // Send email using MailerService
            $mailer = new \services\MailerService();
            $sent = $mailer->sendPasswordResetEmail($displayEmail, $displayName, $resetLink, 15);

            if ($sent) {
                $message = 'A password change link has been sent to <strong>' . htmlspecialchars($displayEmail) . '</strong>. Please check your inbox.';
                $messageType = 'success';
            } else {
                $message = "We couldn't reach the mail server right now. This might be due to temporary network issues or email rate limits. Please try again in a few minutes, or contact the system administrator if the problem persists.";
                $messageType = 'error';
            }
        } catch (Exception $e) {
            error_log("Change Password Email Error: " . $e->getMessage());
            $message = "We couldn't reach the mail server right now. This might be due to temporary network issues or email rate limits. Please try again in a few minutes, or contact the system administrator if the problem persists.";
            $messageType = 'error';
        }
    } elseif ($actionType === 'direct_change') {
        $activeTab = 'direct';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $message = 'Please fill in all password fields.';
            $messageType = 'error';
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $message = 'Your current password is incorrect.';
            $messageType = 'error';
        } elseif (strlen($newPassword) < 8) {
            $message = 'New password must be at least 8 characters long.';
            $messageType = 'error';
        } elseif (!preg_match('/[A-Z]/', $newPassword)) {
            $message = 'New password must contain at least one uppercase letter.';
            $messageType = 'error';
        } elseif (!preg_match('/[a-z]/', $newPassword)) {
            $message = 'New password must contain at least one lowercase letter.';
            $messageType = 'error';
        } elseif (!preg_match('/[0-9]/', $newPassword)) {
            $message = 'New password must contain at least one number.';
            $messageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'New passwords do not match.';
            $messageType = 'error';
        } else {
            // Update password directly
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            if ($updateStmt->execute([$newHash, $userId])) {
                $passwordUpdatedSuccessfully = true;
                $message = 'Your password has been updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to update password. Please try again.';
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password — ICS OJT Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes checkPop {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes progressFill {
            from { width: 0%; }
            to { width: 100%; }
        }
        @keyframes successPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.3); }
            50% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-check-pop { animation: checkPop 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .strength-bar { height: 4px; border-radius: 999px; transition: width 0.4s ease, background 0.3s ease; }
        .req-item { transition: all 0.25s ease; }
        .req-item.met { color: #059669; }
        .req-item.met .req-dot { background: #059669; border-color: #059669; }
        .req-item.met .req-dot svg { opacity: 1; }
        .req-item .req-dot {
            width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #cbd5e1; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center; transition: all 0.25s ease; flex-shrink: 0;
        }
        .req-item .req-dot svg { opacity: 0; transition: opacity 0.2s ease; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="max-w-md w-full animate-fade-in-up">

        <?php if ($passwordUpdatedSuccessfully): ?>
            <!-- ===================== SUCCESS REDIRECT VIEW ===================== -->
            <div class="text-center py-6">
                <!-- Success Icon with Pulse -->
                <div class="mx-auto w-20 h-20 rounded-full bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center mb-6" style="animation: successPulse 2s ease-in-out infinite;">
                    <div class="animate-check-pop">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                </div>

                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight mb-2">Password Updated Successfully!</h1>
                <p class="text-xs text-slate-500 mb-6 max-w-xs mx-auto leading-relaxed">
                    Your password has been changed securely. You are being redirected back to your dashboard.
                </p>

                <!-- Countdown Progress Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-lg p-5 mb-4 text-left">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                        <span class="font-semibold">Redirecting to dashboard...</span>
                        <span id="countdown-text" class="font-bold text-[#0F2854]">5s</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div id="redirect-bar" class="h-full rounded-full bg-gradient-to-r from-[#0F2854] to-indigo-500" style="width: 0%; animation: progressFill 5s linear forwards;"></div>
                    </div>
                </div>

                <a href="<?= htmlspecialchars($backUrl); ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-[#0F2854] hover:text-indigo-700 transition-colors">
                    <span>Click here if not redirected</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>

            <script>
                // 5-second auto-redirect timer
                const targetUrl = <?= json_encode($backUrl); ?>;
                let timeLeft = 5;
                const countdownElement = document.getElementById('countdown-text');

                const timerInterval = setInterval(() => {
                    timeLeft--;
                    if (countdownElement) {
                        countdownElement.textContent = timeLeft + 's';
                    }
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        window.location.href = targetUrl;
                    }
                }, 1000);
            </script>

        <?php else: ?>
            <!-- ===================== STANDARD FORM VIEW ===================== -->

            <!-- Back link -->
            <a href="<?= htmlspecialchars($backUrl); ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </a>

            <!-- Top Header -->
            <div class="text-center mb-5">
                <h2 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Account Security</h2>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight mt-1">Change Password</h1>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xl shadow-slate-200/50 p-6 sm:p-8">

                <!-- User Info Badge -->
                <div class="flex items-center gap-3 bg-slate-50 border border-slate-200/80 rounded-xl p-3 mb-5">
                    <div class="w-9 h-9 rounded-full bg-[#0F2854] text-white font-bold flex items-center justify-center text-xs overflow-hidden ring-2 ring-slate-100 shrink-0">
                        <?php if (!empty($_SESSION['user_picture'])): ?>
                            <img src="<?= htmlspecialchars($_SESSION['user_picture']); ?>" alt="Profile" class="w-full h-full object-cover" referrerpolicy="no-referrer">
                        <?php else: ?>
                            <?= strtoupper(substr($displayName, 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate"><?= htmlspecialchars($displayName); ?></p>
                        <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($displayEmail); ?></p>
                    </div>
                </div>

                <!-- Global Error / Success Alert Message -->
                <?php if ($message): ?>
                    <div class="flex items-start gap-2 <?= $messageType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'; ?> text-xs p-3 rounded-xl mb-5">
                        <svg class="w-4 h-4 <?= $messageType === 'success' ? 'text-emerald-500' : 'text-red-500'; ?> shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?php if ($messageType === 'success'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            <?php else: ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            <?php endif; ?>
                        </svg>
                        <span class="font-medium leading-relaxed"><?= $message; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Tab Switcher Navigation -->
                <div class="flex bg-slate-100 p-1 rounded-xl mb-6 text-xs font-bold">
                    <button type="button" onclick="switchTab('email')" id="tab-email-btn" class="flex-1 py-2 rounded-lg transition-all <?= $activeTab === 'email' ? 'bg-white text-[#0F2854] shadow-xs' : 'text-slate-500 hover:text-slate-900'; ?>">
                        Email Link
                    </button>
                    <button type="button" onclick="switchTab('direct')" id="tab-direct-btn" class="flex-1 py-2 rounded-lg transition-all <?= $activeTab === 'direct' ? 'bg-white text-[#0F2854] shadow-xs' : 'text-slate-500 hover:text-slate-900'; ?>">
                        Direct Change
                    </button>
                </div>

                <!-- TAB 1: Email Link Form -->
                <div id="tab-email-content" class="<?= $activeTab === 'email' ? '' : 'hidden'; ?>">
                    <div class="flex items-start gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0110 0v4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 mb-1">Email Verification Link</p>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                We'll send a <strong>one-time link</strong> to your email. Click it to set a new password. Valid for 15 minutes.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <input type="hidden" name="action_type" value="email_link">
                        <button type="submit" class="w-full py-3 px-4 rounded-full font-bold text-xs tracking-wider uppercase transition-all duration-200
                                   bg-[#0F2854] hover:bg-[#1a3d6e] text-white shadow-md shadow-[#0F2854]/20 flex items-center justify-center gap-2 cursor-pointer">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            Send Password Change Link
                        </button>
                    </form>
                </div>

                <!-- TAB 2: Direct Change Form -->
                <div id="tab-direct-content" class="<?= $activeTab === 'direct' ? '' : 'hidden'; ?>">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 mb-1">Direct Password Update</p>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Choose this option if you want to update your password immediately using your current password, without waiting for an email link.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="" class="space-y-3">
                        <input type="hidden" name="action_type" value="direct_change">
                        
                        <!-- Current Password -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" required placeholder="Enter current password" 
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 pr-10 text-xs font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                                <button type="button" onclick="toggleVis('current_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">New Password</label>
                            <div class="relative">
                                <input type="password" name="new_password" id="new_password" oninput="checkStrength(this.value)" required placeholder="Minimum 8 characters" 
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 pr-10 text-xs font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                                <button type="button" onclick="toggleVis('new_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <!-- Strength Meter -->
                            <div class="mt-1.5 flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-1 overflow-hidden">
                                    <div id="strength-bar" class="strength-bar bg-slate-300" style="width: 0%"></div>
                                </div>
                                <span id="strength-label" class="text-[9px] font-bold text-slate-400 w-10 text-right">—</span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Confirm New Password</label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="confirm_password" oninput="checkMatch()" required placeholder="Re-enter new password" 
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 pr-10 text-xs font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                                <button type="button" onclick="toggleVis('confirm_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <div id="match-status" class="mt-1 text-[10px] font-semibold hidden"></div>
                        </div>

                        <!-- Requirements Checklist -->
                        <div class="bg-slate-50 rounded-xl border border-slate-200 p-3 space-y-1.5">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Requirements</p>
                            <div class="req-item flex items-center gap-2 text-[11px] text-slate-500" id="req-length">
                                <span class="req-dot"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span>At least 8 characters</span>
                            </div>
                            <div class="req-item flex items-center gap-2 text-[11px] text-slate-500" id="req-upper">
                                <span class="req-dot"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span>One uppercase letter (A-Z)</span>
                            </div>
                            <div class="req-item flex items-center gap-2 text-[11px] text-slate-500" id="req-lower">
                                <span class="req-dot"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span>One lowercase letter (a-z)</span>
                            </div>
                            <div class="req-item flex items-center gap-2 text-[11px] text-slate-500" id="req-number">
                                <span class="req-dot"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span>One number (0-9)</span>
                            </div>
                            <div class="req-item flex items-center gap-2 text-[11px] text-slate-500" id="req-match">
                                <span class="req-dot"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span>Passwords match</span>
                            </div>
                        </div>

                        <button type="submit" id="submit-btn" disabled class="w-full py-3 px-4 rounded-full font-bold text-xs tracking-wider uppercase transition-all duration-200 shadow-md
                                   disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed
                                   bg-[#0F2854] hover:bg-[#1a3d6e] text-white shadow-[#0F2854]/20 flex items-center justify-center gap-2 cursor-pointer mt-2">
                            Update Password Now
                        </button>
                    </form>
                </div>

            </div>
        <?php endif; ?>

        <!-- Footer -->
        <p class="text-center text-[10px] text-slate-400 mt-4">
            NBSC &middot; ICS OJT Internship Portal &middot; Secure Password Change
        </p>
    </div>

    <script>
        function switchTab(tab) {
            const emailContent = document.getElementById('tab-email-content');
            const directContent = document.getElementById('tab-direct-content');
            const emailBtn = document.getElementById('tab-email-btn');
            const directBtn = document.getElementById('tab-direct-btn');

            if (tab === 'email') {
                emailContent.classList.remove('hidden');
                directContent.classList.add('hidden');
                emailBtn.className = 'flex-1 py-2 rounded-lg transition-all bg-white text-[#0F2854] shadow-xs';
                directBtn.className = 'flex-1 py-2 rounded-lg transition-all text-slate-500 hover:text-slate-900';
            } else {
                directContent.classList.remove('hidden');
                emailContent.classList.add('hidden');
                directBtn.className = 'flex-1 py-2 rounded-lg transition-all bg-white text-[#0F2854] shadow-xs';
                emailBtn.className = 'flex-1 py-2 rounded-lg transition-all text-slate-500 hover:text-slate-900';
            }
        }

        function toggleVis(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function checkStrength(val) {
            const bar = document.getElementById('strength-bar');
            const label = document.getElementById('strength-label');
            
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;

            const levels = [
                { w: '0%',   c: '#cbd5e1', l: '—' },
                { w: '25%',  c: '#ef4444', l: 'Weak' },
                { w: '50%',  c: '#f97316', l: 'Fair' },
                { w: '75%',  c: '#eab308', l: 'Good' },
                { w: '100%', c: '#059669', l: 'Strong' }
            ];

            const lvl = levels[score];
            bar.style.width = lvl.w;
            bar.style.background = lvl.c;
            label.textContent = val.length > 0 ? lvl.l : '—';
            label.style.color = lvl.c;

            toggleReq('req-length', val.length >= 8);
            toggleReq('req-upper', /[A-Z]/.test(val));
            toggleReq('req-lower', /[a-z]/.test(val));
            toggleReq('req-number', /[0-9]/.test(val));

            checkMatch();
            validateForm();
        }

        function checkMatch() {
            const pw = document.getElementById('new_password').value;
            const cpw = document.getElementById('confirm_password').value;
            const statusEl = document.getElementById('match-status');
            const matched = pw.length > 0 && cpw.length > 0 && pw === cpw;

            toggleReq('req-match', matched);

            if (cpw.length > 0) {
                statusEl.classList.remove('hidden');
                if (matched) {
                    statusEl.textContent = '✓ Passwords match';
                    statusEl.style.color = '#059669';
                } else {
                    statusEl.textContent = '✗ Passwords do not match';
                    statusEl.style.color = '#ef4444';
                }
            } else {
                statusEl.classList.add('hidden');
            }

            validateForm();
        }

        function toggleReq(id, isMet) {
            const el = document.getElementById(id);
            if (isMet) {
                el.classList.add('met');
            } else {
                el.classList.remove('met');
            }
        }

        function validateForm() {
            const pw = document.getElementById('new_password').value;
            const cpw = document.getElementById('confirm_password').value;
            const btn = document.getElementById('submit-btn');

            const valid = pw.length >= 8 
                && /[A-Z]/.test(pw) 
                && /[a-z]/.test(pw) 
                && /[0-9]/.test(pw)
                && pw === cpw
                && cpw.length > 0;

            if (btn) btn.disabled = !valid;
        }
    </script>
</body>
</html>