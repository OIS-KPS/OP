<?php
// auth/google-callback.php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

// Load .env variables safely
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
        // Exchange authorization code for access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception("Google Auth Error: " . ($token['error_description'] ?? $token['error']));
        }

        $client->setAccessToken($token['access_token']);

        // Fetch user profile information from Google OAuth
        $googleOAuth = new Google\Service\Oauth2($client);
        $userInfo    = $googleOAuth->userinfo->get();

        $email   = strtolower(trim($userInfo->email));
        $name    = $userInfo->name;
        $picture = $userInfo->picture ?? null;

        // Extract default Student ID from institutional email (e.g., "20231053" from "20231053@nbsc.edu.ph")
        $extractedId = str_contains($email, '@') ? explode('@', $email)[0] : 'N/A';

        // -------------------------------------------------------------
        // STEP 1: Query Master `users` Table
        // -------------------------------------------------------------
        $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // -------------------------------------------------------------
        // STEP 2: Account Creation or Profile Syncing
        // -------------------------------------------------------------
        if (!$user) {
            // Register brand new user defaulting to 'student' role
            $insertUser = $pdo->prepare("INSERT INTO users (name, email, role, avatar_url) VALUES (?, ?, 'student', ?)");
            $insertUser->execute([$name, $email, $picture]);
            $userId   = $pdo->lastInsertId();
            $userRole = 'student';
        } else {
            $userId   = $user['id'];
            $userRole = strtolower($user['role'] ?? 'student');

            // Sync latest Google avatar picture if updated
            if ($picture && ($user['avatar_url'] ?? '') !== $picture) {
                $updateAvatar = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
                $updateAvatar->execute([$picture, $userId]);
            }
        }

        // Establish Core Base Sessions
        $_SESSION['user_id']      = $userId;
        $_SESSION['user_name']    = $user['name'] ?? $name;
        $_SESSION['email']        = $email;
        $_SESSION['user_picture'] = $picture;
        $_SESSION['role']         = $userRole;

        // -------------------------------------------------------------
        // STEP 3: Student Extension Check / Auto-Linking
        // -------------------------------------------------------------
        if ($userRole === 'student') {
            // Guarantee extractedId is not empty
            if (empty($extractedId) || $extractedId === 'N/A') {$extractedId = str_contains($email, '@') ? explode('@', $email)[0] : 'UNKNOWN';
            }

            // Query by user_id OR student_number to prevent duplicate key conflicts
            $stmtStudent =$pdo->prepare("SELECT * FROM students WHERE user_id = ? OR student_number = ?");
            $stmtStudent->execute([$userId,$extractedId]);
            $student =$stmtStudent->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                // First-time student login: create linked record with valid student_number
                $insertStudent =$pdo->prepare("INSERT INTO students (user_id, student_number, program) VALUES (?, ?, 'BSIT')");
                $insertStudent->execute([$userId,$extractedId]);
                $studentId =$pdo->lastInsertId();
            } else {
                $studentId =$student['id'];

                // If pre-seeded record exists but user_id isn't linked yet, update it
                if (empty($student['user_id'])) {
                    $updateStudentLink =$pdo->prepare("UPDATE students SET user_id = ? WHERE id = ?");
                    $updateStudentLink->execute([$userId,$studentId]);
                }
            }

            $_SESSION['student_id'] =$studentId;
            header("Location: ../dashboard.php");
            exit();
            
        } elseif ($userRole === 'supervisor') {
            // Find or link supervisor profile
            $stmtSupervisor = $pdo->prepare("SELECT * FROM supervisors WHERE user_id = ?");
            $stmtSupervisor->execute([$userId]);
            $supervisor = $stmtSupervisor->fetch(PDO::FETCH_ASSOC);

            if (!$supervisor) {
                // Auto-create supervisor extension if missing
                $insertSupervisor = $pdo->prepare("INSERT INTO supervisors (user_id) VALUES (?)");
                $insertSupervisor->execute([$userId]);
                $supervisorId = $pdo->lastInsertId();
            } else {
                $supervisorId = $supervisor['id'];
            }

            $_SESSION['supervisor_id'] = $supervisorId;
            header("Location: ../supervisor/dashboard.php");
            exit();

        } elseif ($userRole === 'coordinator') {
            header("Location: ../coordinator/dashboard.php");
            exit();

        } elseif ($userRole === 'admin') {
            header("Location: ../admin/dashboard.php");
            exit();

        } else {
            header("Location: ../dashboard.php");
            exit();
        }

    } catch (Exception $e) {
        $emailContext = isset($email) ? " for <strong>" . htmlspecialchars($email) . "</strong>" : "";
        $_SESSION['login_error'] = "Authentication error{$emailContext}: " . htmlspecialchars($e->getMessage());
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}