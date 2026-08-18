<?php
// auth/change_password.php
// Page that sends a one-time password reset link to the user's email
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/MailerService.php';

// Auth Guard — must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user info
$stmtUser = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Invalidate any existing unused tokens for this user
        $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE user_id = ? AND used = 0")->execute([$userId]);

        // Generate a secure random token (32 bytes = 64 hex chars)
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
            $message = 'Failed to send the email. Please try again.';
            $messageType = 'error';
        }
    } catch (Exception $e) {
        error_log("Change Password Error: " . $e->getMessage());
        $message = 'An unexpected error occurred. Please try again.';
        $messageType = 'error';
    }
}

// Determine the "go back" URL based on role
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
        @keyframes envelopeFly {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(-2deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-envelope { animation: envelopeFly 2.5s ease-in-out infinite; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="max-w-md w-full animate-fade-in-up">

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

            <?php if ($message && $messageType === 'success'): ?>
                <!-- Success State -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center mb-5 animate-envelope">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Check Your Email</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-5"><?= $message; ?></p>
                    
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-left mb-5">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-[11px] text-amber-800 font-medium leading-relaxed">The link expires in <strong>15 minutes</strong>. Check your spam folder if you don't see the email.</p>
                        </div>
                    </div>

                    <a href="<?= htmlspecialchars($backUrl); ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-[#0F2854] hover:text-indigo-700 transition-colors">
                        Return to Dashboard
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>

            <?php else: ?>
                <!-- Request Form State -->

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

                <!-- Error Alert -->
                <?php if ($message && $messageType === 'error'): ?>
                    <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-800 text-xs p-3 rounded-xl mb-5">
                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium leading-relaxed"><?= $message; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Explanation -->
                <div class="flex items-start gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0110 0v4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 mb-1">How it works</p>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            We'll send a <strong>one-time link</strong> to your registered email. Click that link to set a new password. The link expires in 15 minutes for security.
                        </p>
                    </div>
                </div>

                <!-- Send Link Form -->
                <form method="POST" action="">
                    <button type="submit" class="w-full py-3 px-4 rounded-full font-bold text-xs tracking-wider uppercase transition-all duration-200
                               bg-[#0F2854] hover:bg-[#1a3d6e] active:bg-[#0a1e3f] text-white shadow-md shadow-[#0F2854]/20 hover:shadow-lg
                               flex items-center justify-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        Send Password Change Link
                    </button>
                </form>

            <?php endif; ?>
        </div>

        <!-- Footer -->
        <p class="text-center text-[10px] text-slate-400 mt-4">
            NBSC &middot; ICS OJT Internship Portal &middot; Secure Password Change
        </p>
    </div>

</body>
</html>
