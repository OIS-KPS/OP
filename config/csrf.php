<?php
// config/csrf.php
// Per-session CSRF tokens for state-changing POST requests.
//
// Every controller in this app calls session_start() and requires
// config/db.php before doing any work, so this file can assume a live session.
//
// Usage:
//   Controllers:  csrf_verify(__DIR__ . '/users.php');
//   Views:        echo csrf_field();   (short echo tag)
//   After login:  csrf_rotate();

if (!function_exists('csrf_token')) {
    /**
     * Return this session's token, generating one on first use.
     */
    function csrf_token() {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Hidden input to drop inside a POST form.
     */
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="'
             . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrf_rotate')) {
    /**
     * Issue a fresh token. Call after authentication so a token planted
     * beforehand cannot be reused against the authenticated session.
     */
    function csrf_rotate() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_verify')) {
    /**
     * Validate the posted token. On failure this never fatals: it sets a flash
     * message and redirects, so a missed form costs one action rather than
     * locking the user out of the portal.
     *
     * @param string $fallbackUrl Safe in-app URL to redirect to on rejection.
     * @param string $messageKey  Session key the page renders its error from.
     * @return bool True when the request is allowed to proceed.
     */
    function csrf_verify($fallbackUrl = 'index.php', $messageKey = 'flash_error') {
        $posted = $_POST['csrf_token'] ?? '';
        $known  = $_SESSION['csrf_token'] ?? '';

        $isValid = is_string($posted)
                && is_string($known)
                && $posted !== ''
                && $known !== ''
                && hash_equals($known, $posted);

        if ($isValid) {
            return true;
        }

        $_SESSION[$messageKey] = 'Your session expired or the request could not be verified. Please try again.';

        header('Location: ' . $fallbackUrl);
        exit;
    }
}