<?php
// auth/logout.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Audit Log: Record Logout Event before clearing session data
if (isset($_SESSION['user_id'])) {
    $userId   = $_SESSION['user_id'];
    $userRole = $_SESSION['role'] ?? 'guest';
    $userName = $_SESSION['user_name'] ?? 'User';

    logActivity($pdo, $userId, $userRole, 'USER_LOGOUT', "User {$userName} logged out of the system.");
}

// Clear session variables
$_SESSION = array();

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html>
<head><title>Logging out...</title></head>
<body>
<script>
    // Signal all other open tabs to redirect to the login page
    localStorage.setItem('logout_event', Date.now().toString());
    localStorage.removeItem('logout_event');

    // Redirect this tab to login
    window.location.href = '/ICS-PORTAL/auth/login.php';
</script>
<!-- Fallback for browsers with JS disabled -->
<noscript>
    <meta http-equiv="refresh" content="0;url=/ICS-PORTAL/auth/login.php">
</noscript>
</body>
</html>