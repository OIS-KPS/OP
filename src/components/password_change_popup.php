<!-- src/components/password_change_popup.php -->
<?php
/**
 * Password Setup Popup Component
 * 
 * Include this component on any authenticated page.
 * It checks if the logged-in user has a NULL password_hash
 * and displays a non-dismissible modal prompting them to set one.
 * 
 * Requirements:
 *   - Session must be started
 *   - $pdo database connection must be available
 *   - $_SESSION['user_id'] must be set
 */

// Guard: Only run if user is logged in and $pdo exists
if (isset($_SESSION['user_id']) && isset($pdo)) {

    // Use session cache to avoid querying the DB on every page load.
    // The flag is checked once per session and cached in $_SESSION['needs_password'].
    if (!isset($_SESSION['needs_password'])) {
        $__pwCheckStmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $__pwCheckStmt->execute([$_SESSION['user_id']]);
        $__pwResult = $__pwCheckStmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['needs_password'] = ($__pwResult && $__pwResult['password_hash'] === null);
    }

    $__needsPassword = $_SESSION['needs_password'];

    if ($__needsPassword) {
        // Save the current full URL to session so we can redirect back after password creation
        $__protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $__currentUrl = $__protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $_SESSION['redirect_after_password'] = $__currentUrl;
?>

<!-- Password Setup Required — Modal Overlay -->
<div id="pw-setup-overlay" style="
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    animation: pwOverlayFadeIn 0.3s ease-out;
">

    <!-- Modal Card -->
    <div style="
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(15, 40, 84, 0.18), 0 0 0 1px rgba(15, 40, 84, 0.06);
        max-width: 420px;
        width: 92%;
        padding: 36px 32px 32px;
        text-align: center;
        position: relative;
        animation: pwModalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    ">
        <!-- Animated Shield Icon -->
        <div style="
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border: 1px solid rgba(99, 102, 241, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pwIconPulse 2s ease-in-out infinite;
        ">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                <circle cx="12" cy="16" r="1"></circle>
            </svg>
        </div>

        <!-- Title -->
        <h2 style="
            font-family: 'Inter', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px;
            letter-spacing: -0.01em;
        ">Password Setup Required</h2>

        <!-- Description -->
        <p style="
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin: 0 0 8px;
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        ">
            Your account doesn't have a password yet. Please create one to secure your account and enable email-based login.
        </p>

        <!-- Security Note Badge -->
        <div style="
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 8px;
            padding: 6px 12px;
            margin-bottom: 24px;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: #92400E;
        ">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            This action is required before you can continue
        </div>

        <!-- CTA Button -->
        <div>
            <?php
                // Determine the correct relative path to auth/password_creation.php
                // based on the current script's location
                $__scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                if ($__scriptDir === '/' || $__scriptDir === '\\') {
                    $__pwLink = '/ICS-PORTAL/auth/password_creation.php';
                } else {
                    // Calculate relative path from current page to auth/password_creation.php
                    $__pwLink = '/ICS-PORTAL/auth/password_creation.php';
                }
            ?>
            <a href="<?= htmlspecialchars($__pwLink); ?>" id="pw-setup-btn" style="
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 12px 24px;
                background: linear-gradient(135deg, #0F2854 0%, #1e3a6e 100%);
                color: #ffffff;
                font-family: 'Inter', sans-serif;
                font-size: 13px;
                font-weight: 700;
                letter-spacing: 0.03em;
                text-transform: uppercase;
                text-decoration: none;
                border: none;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 4px 14px rgba(15, 40, 84, 0.25);
            "
            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(15,40,84,0.35)'"
            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(15,40,84,0.25)'"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
                Set Up My Password
            </a>
        </div>

        <!-- Subtle footer -->
        <p style="
            font-family: 'Inter', sans-serif;
            font-size: 10px;
            color: #94a3b8;
            margin: 16px 0 0;
            letter-spacing: 0.02em;
        ">You'll be redirected back to this page after setup</p>
    </div>
</div>

<!-- Popup Animations -->
<style>
    @keyframes pwOverlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes pwModalSlideUp {
        from { 
            opacity: 0; 
            transform: translateY(20px) scale(0.97); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1); 
        }
    }
    @keyframes pwIconPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.06); }
    }
    /* Prevent scrolling when popup is visible */
    body:has(#pw-setup-overlay) {
        overflow: hidden !important;
    }
</style>

<?php
    } // end if ($__needsPassword)
} // end if (session + pdo check)
?>
