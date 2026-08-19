<?php
// auth/password_creation.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Auth Guard — must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user info for display
$stmtUser = $pdo->prepare("SELECT name, email, password_hash FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['login_error'] = "User account not found. Please log in again.";
    header("Location: login.php");
    exit();
}

// If user already has a password, redirect them back
if ($user['password_hash'] !== null) {
    $redirectUrl = $_SESSION['redirect_after_password'] ?? '/ICS-PORTAL/dashboard.php';
    unset($_SESSION['redirect_after_password']);
    header("Location: " . $redirectUrl);
    exit();
}

$displayName = $user['name'] ?? 'User';
$displayEmail = $user['email'] ?? '';

// Handle form submission
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($password) || empty($confirmPassword)) {
        $error = 'Both password fields are required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = 'Password must contain at least one lowercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = 'Password must contain at least one number.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Hash and save
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $updateStmt->execute([$hashedPassword, $userId]);

        // Clear the session cache so the popup no longer appears
        unset($_SESSION['needs_password']);

        $success = true;

        // Determine redirect URL
        $redirectUrl = $_SESSION['redirect_after_password'] ?? null;
        unset($_SESSION['redirect_after_password']);

        // Fallback: redirect based on role
        if (!$redirectUrl) {
            $role = strtolower($_SESSION['role'] ?? 'student');
            switch ($role) {
                case 'supervisor':
                    $redirectUrl = '/ICS-PORTAL/supervisor/dashboard.php';
                    break;
                case 'coordinator':
                    $redirectUrl = '/ICS-PORTAL/coordinator/dashboard.php';
                    break;
                default:
                    $redirectUrl = '/ICS-PORTAL/dashboard.php';
                    break;
            }
        }

        $_SESSION['password_redirect_url'] = $redirectUrl;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Your Password — ICS OJT Portal</title>
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
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-8px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes checkPop {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes progressFill {
            from { width: 0%; }
        }
        @keyframes successPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.3); }
            50% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
        .animate-check-pop { animation: checkPop 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Password strength meter */
        .strength-bar { 
            height: 4px; 
            border-radius: 999px; 
            transition: width 0.4s ease, background 0.3s ease; 
        }

        /* Input focus ring animation */
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        /* Requirement check items */
        .req-item { 
            transition: all 0.25s ease; 
        }
        .req-item.met { color: #059669; }
        .req-item.met .req-dot { 
            background: #059669; 
            border-color: #059669; 
        }
        .req-item.met .req-dot svg { opacity: 1; }
        .req-item .req-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }
        .req-item .req-dot svg {
            opacity: 0;
            transition: opacity 0.2s ease;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 antialiased">

<?php if ($success): ?>
    <!-- ===================== SUCCESS STATE ===================== -->
    <div class="max-w-md w-full text-center animate-fade-in-up" style="animation-delay: 0.1s;">
        <!-- Success Icon -->
        <div class="mx-auto w-20 h-20 rounded-full bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center mb-6" style="animation: successPulse 2s ease-in-out infinite;">
            <div class="animate-check-pop">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Password Created!</h1>
        <p class="text-sm text-slate-500 mb-8 max-w-xs mx-auto leading-relaxed">
            Your account is now secured. You can use your email and password to log in next time.
        </p>

        <!-- Redirect Progress -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-lg p-5 mb-4">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-semibold">Redirecting you back...</span>
                <span id="countdown-text" class="font-bold text-[#0F2854]">3s</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div id="redirect-bar" class="h-full rounded-full bg-gradient-to-r from-[#0F2854] to-indigo-500" style="width: 0%; animation: progressFill 3s linear forwards;"></div>
            </div>
        </div>

        <a href="<?= htmlspecialchars($_SESSION['password_redirect_url'] ?? '/ICS-PORTAL/dashboard.php'); ?>" 
           id="redirect-link"
           class="inline-flex items-center gap-2 text-xs font-semibold text-[#0F2854] hover:text-indigo-700 transition-colors">
            <span>Click here if not redirected</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>

    <script>
        // Auto-redirect countdown
        const redirectUrl = <?= json_encode($_SESSION['password_redirect_url'] ?? '/ICS-PORTAL/dashboard.php'); ?>;
        let seconds = 3;
        const countdownEl = document.getElementById('countdown-text');
        
        const interval = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds + 's';
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = redirectUrl;
            }
        }, 1000);
    </script>

<?php else: ?>
    <!-- ===================== FORM STATE ===================== -->
    <div class="max-w-md w-full animate-fade-in-up">

        <!-- Top Header -->
        <div class="text-center mb-5">
            <h2 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Account Security</h2>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight mt-1">Create Your Password</h1>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xl shadow-slate-200/50 p-6 sm:p-8">

            <!-- User Info Badge -->
            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200/80 rounded-xl p-3 mb-6">
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
            <?php if ($error): ?>
                <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-800 text-xs p-3 rounded-xl mb-5 animate-slide-in">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium leading-relaxed"><?= htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Password Form -->
            <form method="POST" action="" class="space-y-4" id="pw-form">

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">New Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            minlength="8"
                            placeholder="Minimum 8 characters"
                            class="input-field w-full text-sm px-4 py-2.5 pr-10 rounded-xl bg-slate-50/50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all"
                            oninput="checkStrength(this.value)"
                        >
                        <button type="button" onclick="toggleVis('password', 'eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                            <svg id="eye1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Strength Meter -->
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex-1 bg-slate-100 rounded-full h-1 overflow-hidden">
                            <div id="strength-bar" class="strength-bar bg-slate-300" style="width: 0%"></div>
                        </div>
                        <span id="strength-label" class="text-[10px] font-bold text-slate-400 w-12 text-right">—</span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-xs font-semibold text-slate-700 mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required 
                            minlength="8"
                            placeholder="Re-enter your password"
                            class="input-field w-full text-sm px-4 py-2.5 pr-10 rounded-xl bg-slate-50/50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all"
                            oninput="checkMatch()"
                        >
                        <button type="button" onclick="toggleVis('confirm_password', 'eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                            <svg id="eye2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Match indicator -->
                    <div id="match-status" class="mt-1.5 text-[10px] font-semibold hidden"></div>
                </div>

                <!-- Password Requirements Checklist -->
                <div class="bg-slate-50 rounded-xl border border-slate-100 p-3.5 space-y-2">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Requirements</p>
                    
                    <div class="req-item flex items-center gap-2 text-xs text-slate-500" id="req-length">
                        <span class="req-dot"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span>At least 8 characters</span>
                    </div>
                    <div class="req-item flex items-center gap-2 text-xs text-slate-500" id="req-upper">
                        <span class="req-dot"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span>One uppercase letter (A-Z)</span>
                    </div>
                    <div class="req-item flex items-center gap-2 text-xs text-slate-500" id="req-lower">
                        <span class="req-dot"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span>One lowercase letter (a-z)</span>
                    </div>
                    <div class="req-item flex items-center gap-2 text-xs text-slate-500" id="req-number">
                        <span class="req-dot"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span>One number (0-9)</span>
                    </div>
                    <div class="req-item flex items-center gap-2 text-xs text-slate-500" id="req-match">
                        <span class="req-dot"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span>Passwords match</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submit-btn"
                    disabled
                    class="w-full py-3 px-4 rounded-full font-bold text-xs tracking-wider uppercase transition-all duration-200 shadow-md
                           disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed
                           bg-[#0F2854] hover:bg-[#1a3d6e] active:bg-[#0a1e3f] text-white shadow-[#0F2854]/20 hover:shadow-lg"
                >
                    Create Password
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-[10px] text-slate-400 mt-4">
            NBSC &middot; ICS OJT Internship Portal &middot; Secure Account Setup
        </p>
    </div>

    <script>
        function toggleVis(inputId, iconId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        function checkStrength(val) {
            const bar = document.getElementById('strength-bar');
            const label = document.getElementById('strength-label');
            
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { w: '0%',   c: '#cbd5e1', l: '—' },
                { w: '20%',  c: '#ef4444', l: 'Weak' },
                { w: '40%',  c: '#f97316', l: 'Fair' },
                { w: '60%',  c: '#eab308', l: 'Good' },
                { w: '80%',  c: '#22c55e', l: 'Strong' },
                { w: '100%', c: '#059669', l: 'Excellent' }
            ];

            const lvl = levels[score];
            bar.style.width = lvl.w;
            bar.style.background = lvl.c;
            label.textContent = val.length > 0 ? lvl.l : '—';
            label.style.color = lvl.c;

            // Update individual requirements
            toggleReq('req-length', val.length >= 8);
            toggleReq('req-upper', /[A-Z]/.test(val));
            toggleReq('req-lower', /[a-z]/.test(val));
            toggleReq('req-number', /[0-9]/.test(val));

            checkMatch();
            validateForm();
        }

        function checkMatch() {
            const pw = document.getElementById('password').value;
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
            const pw = document.getElementById('password').value;
            const cpw = document.getElementById('confirm_password').value;
            const btn = document.getElementById('submit-btn');

            const valid = pw.length >= 8 
                && /[A-Z]/.test(pw) 
                && /[a-z]/.test(pw) 
                && /[0-9]/.test(pw)
                && pw === cpw
                && cpw.length > 0;

            btn.disabled = !valid;
        }
    </script>

<?php endif; ?>

</body>
</html>
