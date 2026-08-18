<?php
// auth/login-process.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$email    = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// ------------------------------------------------------------------
// 1. Basic Input Validation
// ------------------------------------------------------------------
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both your email and password.';
    header("Location: login.php");
    exit();
}

// ------------------------------------------------------------------
// 2. Look Up User by Email
// ------------------------------------------------------------------
$stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['login_error'] = 'No account found with that email address.';
    header("Location: login.php");
    exit();
}

// ------------------------------------------------------------------
// 3. Check if User Has a Password Set
// ------------------------------------------------------------------
if ($user['password_hash'] === null) {
    $_SESSION['login_error'] = 'Your account doesn\'t have a password yet. Please sign in with Google first, then set up your password.';
    header("Location: login.php");
    exit();
}

// ------------------------------------------------------------------
// 4. Verify Password
// ------------------------------------------------------------------
if (!password_verify($password, $user['password_hash'])) {
    $_SESSION['login_error'] = 'Incorrect password. Please try again.';
    header("Location: login.php");
    exit();
}

// ------------------------------------------------------------------
// 5. Authentication Successful — Build Session
// ------------------------------------------------------------------
$userId   = $user['id'];
$userRole = strtolower($user['role'] ?? 'student');

$_SESSION['user_id']      = $userId;
$_SESSION['user_name']    = $user['name'];
$_SESSION['email']        = $user['email'];
$_SESSION['user_picture'] = $user['avatar_url'] ?? null;
$_SESSION['role']         = $userRole;

// ------------------------------------------------------------------
// 6. Role-Specific Extension Linking & Redirect
// ------------------------------------------------------------------
if ($userRole === 'student') {
    // Look up or auto-create student profile
    $extractedId = str_contains($email, '@') ? explode('@', $email)[0] : 'UNKNOWN';

    $stmtStudent = $pdo->prepare("SELECT * FROM students WHERE user_id = ? OR student_number = ?");
    $stmtStudent->execute([$userId, $extractedId]);
    $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $insertStudent = $pdo->prepare("INSERT INTO students (user_id, student_number, program) VALUES (?, ?, 'BSIT')");
        $insertStudent->execute([$userId, $extractedId]);
        $studentId = $pdo->lastInsertId();
    } else {
        $studentId = $student['id'];
        if (empty($student['user_id'])) {
            $pdo->prepare("UPDATE students SET user_id = ? WHERE id = ?")->execute([$userId, $studentId]);
        }
    }

    $_SESSION['student_id'] = $studentId;
    header("Location: ../dashboard.php");
    exit();

} elseif ($userRole === 'supervisor') {
    $stmtSupervisor = $pdo->prepare("SELECT * FROM supervisors WHERE user_id = ?");
    $stmtSupervisor->execute([$userId]);
    $supervisor = $stmtSupervisor->fetch(PDO::FETCH_ASSOC);

    if (!$supervisor) {
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
