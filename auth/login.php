<?php
// auth/login.php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

// Setup Google Client
$client = new Google\Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? '');
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? '');
$client->addScope("email");
$client->addScope("profile");

// 🔑 ADD THIS LINE: Forces Google to show the "Choose an account" prompt every time
$client->setPrompt('select_account');

$googleLoginUrl = $client->createAuthUrl();

$loginError = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS OJT Internship Portal - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 flex flex-col items-center justify-center min-h-screen p-4 text-slate-800 antialiased overflow-x-hidden">

    <!-- Top Headers (Above Card) -->
    <div class="text-center mb-3.5 shrink-0">
        <h2 class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-widest">Northern Bukidnon State College</h2>
        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight mt-0.5">ICS OJT Internship Portal</h1>
    </div>

    <!-- Main Compact Card -->
    <div class="max-w-sm w-full bg-white rounded-2xl border border-slate-200/90 shadow-xl shadow-slate-200/50 p-5 sm:p-6 space-y-4">

        <!-- Logo Icon Badge -->
        <div class="flex justify-center">
            <div class="w-12 h-12 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
        </div>

        <!-- Alert Notification -->
        <?php if ($loginError): ?>
            <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 text-amber-900 text-[11px] p-2.5 rounded-xl leading-relaxed">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1 font-medium"><?= $loginError; ?></div>
            </div>
        <?php endif; ?>

        <!-- Form Section -->
        <form action="login-process.php" method="POST" class="space-y-3.5">
            <!-- Email Field -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required placeholder="Enter your email" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50/50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <!-- Password Field -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required placeholder="Enter your password" class="w-full text-xs px-3 py-2 pr-9 rounded-xl bg-slate-50/50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-[11px] pt-0.5">
                <label class="flex items-center gap-1.5 text-slate-600 select-none cursor-pointer">
                    <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span>Remember me</span>
                </label>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium hover:underline">Forgot Password?</a>
            </div>

            <!-- Clean Wireframe Login Button -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-full shadow-md shadow-blue-500/20 transition-all text-xs tracking-wider uppercase mt-1">
                LOGIN
            </button>
        </form>

        <!-- Clean OR Divider -->
        <div class="relative flex items-center justify-center my-3">
            <div class="border-t border-slate-200 w-full"></div>
            <span class="bg-white px-3 text-[10px] font-bold tracking-widest text-slate-400 uppercase absolute">OR</span>
        </div>

        <!-- Google Sign-In Section -->
        <div class="space-y-1.5">
            <span class="text-[10px] font-medium text-slate-400 block text-center">Institutional Email Access</span>
            <a href="<?= htmlspecialchars($googleLoginUrl); ?>" class="w-full flex items-center justify-center gap-2.5 bg-white border border-slate-300 hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-semibold py-2.5 px-3 rounded-xl shadow-2xs transition-all text-xs group">
                <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-105" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Sign in with Google</span>
            </a>
        </div>

    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>
</html>