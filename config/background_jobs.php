<?php
// config/background_jobs.php
/**
 * ============================================================
 * config/background_jobs.php
 * ============================================================
 *
 * Settings for running entity extraction in the background instead of blocking
 * the page that noticed it was needed.
 *
 * WHY
 * Opening a report triggers a transformer inference that takes seconds. Done
 * inline, the coordinator waits on a spinner for a result they may not even be
 * looking at. Done in the background, the page renders immediately and the
 * entities land on refresh.
 *
 * The worker is a separate PHP CLI process (src/cli/extract_report.php) spawned
 * with popen(). If that cannot work the portal must not degrade -- see
 * entityExtractionSpawn() and its callers, which fall back to running inline.
 */

/**
 * ------------------------------------------------------------
 * Locate a PHP CLI binary
 * ------------------------------------------------------------
 *
 * PHP_BINARY is NOT usable for this. Under Apache on Windows it points at
 * php-cgi.exe, which does not run a script the way the CLI does. So the binary
 * is located explicitly, with the usual XAMPP layout inferred from the project
 * path rather than hardcoded: this project sits at <xampp>/htdocs/<name>, so the
 * interpreter is two levels up under php/.
 */
if (!function_exists('backgroundPhpBinary')) {
    function backgroundPhpBinary(): ?string
    {
        // Explicit override always wins.
        $configured = $_ENV['BACKGROUND_PHP_BINARY'] ?? '';
        if (is_string($configured) && trim($configured) !== '') {
            return trim($configured);
        }

        $projectRoot = dirname(__DIR__);
        $candidates = [
            // <xampp>/htdocs/<project> -> <xampp>/php/php.exe
            dirname($projectRoot, 2) . DIRECTORY_SEPARATOR . 'php'
                . DIRECTORY_SEPARATOR . 'php.exe',
            dirname($projectRoot, 2) . DIRECTORY_SEPARATOR . 'php'
                . DIRECTORY_SEPARATOR . 'php',
            'C:' . DIRECTORY_SEPARATOR . 'xampp' . DIRECTORY_SEPARATOR . 'php'
                . DIRECTORY_SEPARATOR . 'php.exe',
            '/usr/bin/php',
            '/usr/local/bin/php',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

if (!defined('BACKGROUND_JOBS_ENABLED')) {
    /**
     * Set BACKGROUND_JOBS=false in .env to force the old blocking behaviour.
     * Useful for comparing the two paths, and as an escape hatch if spawning
     * turns out to be unreliable on a particular host.
     */
    $enabled = strtolower(trim((string) ($_ENV['BACKGROUND_JOBS'] ?? 'true')));
    define('BACKGROUND_JOBS_ENABLED', $enabled !== 'false' && $enabled !== '0');
}

if (!defined('EXTRACTION_DEBOUNCE_SECONDS')) {
    /**
     * How long a spawn is considered "in flight" before another view is allowed
     * to try again.
     *
     * Without this, a report whose extraction always fails transiently would be
     * respawned on every single view, for ever. With it, a stuck job costs one
     * attempt per window.
     *
     * Must comfortably exceed a normal extraction. A large report takes well
     * under a minute; the worker also holds the per-report MySQL lock, so the
     * worst case is a few redundant workers that serialise and then find the work
     * already stamped.
     */
    define('EXTRACTION_DEBOUNCE_SECONDS', 900); // 15 minutes
}
