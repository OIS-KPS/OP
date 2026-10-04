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

// Forces Google to show the "Choose an account" prompt every time
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
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col items-center justify-center min-h-screen p-4">

    <div class="max-w-sm w-full space-y-5">

        <!-- Top Header & Brand Icon -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#0F2854] text-white font-extrabold text-base shadow-xs">
                ICS
            </div>
            <div>
                <p class="text-xs text-slate-500">Northern Bukidnon State College</p>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">

            <!-- Error Notification -->
            <?php if ($loginError): ?>
                <div class="flex items-start gap-2.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3 rounded-xl leading-relaxed">
                    <span class="text-sm shrink-0">⚠️</span>
                    <div class="font-medium"><?= htmlspecialchars($loginError); ?></div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="login-process.php" method="POST" class="space-y-3.5 text-xs">
                
                <!-- Email Field -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="e.g. user@nbsc.edu.ph" 
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-all"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-700">Password</label>
                        <a href="reset_password.php" class="text-[11px] font-semibold text-[#0F2854] hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="••••••••" 
                            class="w-full text-xs px-3.5 py-2.5 pr-10 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-all"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                        >
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-[11px] pt-0.5">
                    <label class="flex items-center gap-2 text-slate-600 select-none cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-[#0F2854] focus:ring-[#0F2854]">
                        <span>Remember me</span>
                    </label>
                </div>

                <!-- Sign In Button -->
                <button 
                    type="submit" 
                    class="w-full bg-[#0F2854] hover:bg-blue-900 active:bg-blue-950 text-white font-bold py-2.5 px-4 rounded-xl shadow-xs transition-all text-xs tracking-wide cursor-pointer"
                >
                    Sign In
                </button>
            </form>

            <!-- Clean OR Divider -->
            <div class="relative flex items-center justify-center my-3">
                <div class="border-t border-slate-200 w-full"></div>
                <span class="bg-white px-3 text-[10px] font-bold tracking-wider text-slate-400 uppercase absolute">OR</span>
            </div>

            <!-- Google Sign-In Button -->
            <div>
                <a 
                    href="<?= htmlspecialchars($googleLoginUrl); ?>" 
                    class="w-full flex items-center justify-center gap-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-semibold py-2.5 px-3 rounded-xl transition-all text-xs group cursor-pointer"
                >
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

        <!-- Footer -->
        <p class="text-center text-[11px] text-slate-400">
            NBSC Institute for Computer Studies &bull; Intern Portal
        </p>

    </div>

    <!-- Password Visibility Toggle -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
        }
    </script>
</body>
</html>