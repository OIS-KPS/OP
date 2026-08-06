<?php
// auth/google-callback.php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

// Load .env variables
if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

$client = new Google\Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? '');
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? '');

if (isset($_GET['code'])) {
    try {
        // Exchange auth code for access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception("Google Auth Error: " . ($token['error_description'] ?? $token['error']));
        }

        $client->setAccessToken($token['access_token']);

        // Get user profile info from Google
        $googleOAuth = new Google\Service\Oauth2($client);
        $userInfo    = $googleOAuth->userinfo->get();

        $email = strtolower(trim($userInfo->email));
        $name  = $userInfo->name;

        // -------------------------------------------------------------
        // STEP 1: Check if user is a STUDENT
        // -------------------------------------------------------------
        $stmt = $pdo->prepare("SELECT * FROM students WHERE LOWER(email) = ?");
        $stmt->execute([$email]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $_SESSION['user_id']   = $student['id'];
            $_SESSION['user_name'] = $student['name'] ?? $name;
            $_SESSION['email']     = $student['email'];
            $_SESSION['role']      = 'student';

            header("Location: ../dashboard.php");
            exit();
        }

        // -------------------------------------------------------------
        // STEP 2: Check if user is STAFF / COORDINATOR / SUPERVISOR
        // -------------------------------------------------------------
        $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = ?");
        $stmt->execute([$email]);
        $staffUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staffUser) {
            $userRole = strtolower($staffUser['role'] ?? '');

            $_SESSION['user_id']   = $staffUser['id'];
            $_SESSION['user_name'] = $staffUser['name'] ?? $name;
            $_SESSION['email']     = $staffUser['email'];
            $_SESSION['role']      = $userRole;

            // Route dynamically based on role
            if ($userRole === 'coordinator') {
                header("Location: ../coordinator/dashboard.php");
            } elseif ($userRole === 'supervisor') {
                header("Location: ../supervisor/dashboard.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit();
        }

        // -------------------------------------------------------------
        // STEP 3: Email not registered in any table
        // -------------------------------------------------------------
        $_SESSION['login_error'] = "Access Denied: The email <strong>" . htmlspecialchars($email) . "</strong> is not pre-registered in the OJT Portal. Please contact your coordinator or administrator.";
        header("Location: login.php");
        exit();

    } catch (Exception $e) {
        $emailContext = isset($email) ? " for <strong>" . htmlspecialchars($email) . "</strong>" : "";
        $_SESSION['login_error'] = "Authentication error{$emailContext}: " . htmlspecialchars($e->getMessage());
        header("Location: login.php");
        exit();
    }
} else {
    // Direct access without code -> Return to login.php
    header("Location: login.php");
    exit();
}