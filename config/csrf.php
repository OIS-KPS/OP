<?php
// config/csrf.php
// Per-session CSRF tokens for state-changing POST requests.
//
// Every controller in this app calls session_start() and requires
// config/db.php before doing any work, so this file can assume a live session.
//
// Usage:
//   Controllers:  csrf_verify('users.php');
//   JSON APIs:    csrf_verify_json($decodedBody['csrf_token'] ?? '');
//   Views:        echo csrf_field();   (short echo tag)
//   After login:  csrf_rotate();
//
// The fallback URL is sent as a Location header, so it must be site-relative.
// __DIR__ is not usable here; see the note on csrf_verify() below.

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

if (!function_exists('csrf_check')) {
    /**
     * Compare a token against this session's token.
     *
     * Split out from csrf_verify() because a JSON endpoint receives its token in
     * the request body rather than in $_POST, and still needs the same
     * constant-time comparison.
     *
     * @param mixed $token Candidate token from the request.
     * @return bool True when the token matches the live session token.
     */
    function csrf_check($token) {
        $known = $_SESSION['csrf_token'] ?? '';

        return is_string($token)
            && is_string($known)
            && $token !== ''
            && $known !== ''
            && hash_equals($known, $token);
    }
}

if (!function_exists('csrf_verify')) {
    /**
     * Validate the posted token. On failure this never fatals: it sets a flash
     * message and redirects, so a missed form costs one action rather than
     * locking the user out of the portal.
     *
     * @param string $fallbackUrl Safe in-app URL to redirect to on rejection.
     *                           Must be a site-relative URL. Do not pass
     *                           __DIR__, which emits a Windows path such as
     *                           C:\xampp\htdocs\... into the Location header;
     *                           browsers read the drive letter as a URL scheme
     *                           and refuse to follow the redirect.
     * @param string $messageKey  Session key the page renders its error from.
     * @return bool True when the request is allowed to proceed.
     */
    function csrf_verify($fallbackUrl = 'index.php', $messageKey = 'flash_error') {
        if (csrf_check($_POST['csrf_token'] ?? '')) {
            return true;
        }

        $_SESSION[$messageKey] = 'Your session expired or the request could not be verified. Please try again.';

        header('Location: ' . $fallbackUrl);
        exit;
    }
}

if (!function_exists('csrf_verify_json')) {
    /**
     * JSON-endpoint counterpart to csrf_verify().
     *
     * A fetch() call cannot follow a Location header, so rejection is reported
     * in the response body with a 403 rather than as a redirect.
     *
     * @param mixed $token Candidate token taken from the decoded request body.
     * @return bool True when the request is allowed to proceed.
     */
    function csrf_verify_json($token) {
        if (csrf_check($token)) {
            return true;
        }

        http_response_code(403);

        echo json_encode([
            'success' => false,
            'error'   => 'Your session expired or the request could not be verified. Please refresh the page and try again.',
        ]);
        exit;
    }
}