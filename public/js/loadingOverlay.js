/**
 * public/js/loadingOverlay.js
 *
 * Full-screen loading overlay used while a page runs server-side entity
 * extraction (e.g. clicking "Review" on a report that has no extracted
 * entities yet).
 *
 * Usage (source page):  mark the link with data-ics-loading="Your message"
 * Usage (target page):  include this file in <head> before </head>.
 *
 * The flag survives navigation (sessionStorage), so the overlay stays on
 * screen from the click until the destination page has finished loading.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'ics_loading_overlay';
    var FLAG_TTL_MS = 60000;      // ignore stale flags older than this
    var AUTO_HIDE_MS = 15000;     // safety net: never trap the user
    var overlay = null;
    var autoHideTimer = null;

    function readFlag() {
        try {
            var raw = window.sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return null;
            var flag = JSON.parse(raw);
            if (!flag || typeof flag.time !== 'number') return null;
            if (Date.now() - flag.time > FLAG_TTL_MS) {
                window.sessionStorage.removeItem(STORAGE_KEY);
                return null;
            }
            return flag;
        } catch (e) {
            return null;
        }
    }

    function writeFlag(message) {
        try {
            window.sessionStorage.setItem(
                STORAGE_KEY,
                JSON.stringify({ message: message || '', time: Date.now() })
            );
        } catch (e) { /* storage unavailable */ }
    }

    function clearFlag() {
        try {
            window.sessionStorage.removeItem(STORAGE_KEY);
        } catch (e) { /* storage unavailable */ }
    }

    function ensureStyles() {
        if (document.getElementById('ics-loading-overlay-style')) return;

        var style = document.createElement('style');
        style.id = 'ics-loading-overlay-style';
        style.textContent =
            '#ics-loading-overlay{' +
            'position:fixed;inset:0;z-index:9999;display:none;' +
            'align-items:center;justify-content:center;flex-direction:column;' +
            'background:rgba(15,23,42,.65);backdrop-filter:blur(4px);' +
            'font-family:Inter,system-ui,-apple-system,sans-serif;' +
            'opacity:0;transition:opacity .2s ease;}' +
            '#ics-loading-overlay.ics-show{opacity:1;}' +
            '#ics-loading-overlay .ics-card{' +
            'background:#fff;border-radius:1rem;padding:28px 36px;' +
            'box-shadow:0 20px 45px rgba(2,6,23,.35);' +
            'display:flex;flex-direction:column;align-items:center;' +
            'gap:14px;max-width:340px;text-align:center;}' +
            '#ics-loading-overlay .ics-spinner{' +
            'width:44px;height:44px;border-radius:50%;' +
            'border:4px solid #e2e8f0;border-top-color:#0F2854;' +
            'animation:ics-spin .8s linear infinite;}' +
            '#ics-loading-overlay .ics-title{' +
            'font-size:14px;font-weight:800;color:#0f172a;margin:0;}' +
            '#ics-loading-overlay .ics-text{' +
            'font-size:12px;font-weight:600;color:#64748b;margin:0;line-height:1.5;}' +
            '#ics-loading-overlay .ics-bar{' +
            'width:100%;height:4px;border-radius:999px;overflow:hidden;' +
            'background:#e2e8f0;}' +
            '#ics-loading-overlay .ics-bar span{' +
            'display:block;height:100%;width:40%;border-radius:999px;' +
            'background:#0F2854;animation:ics-slide 1.1s ease-in-out infinite;}' +
            '@keyframes ics-spin{to{transform:rotate(360deg);}}' +
            '@keyframes ics-slide{' +
            '0%{margin-left:-40%;}50%{margin-left:60%;}100%{margin-left:100%;}}';
        document.head.appendChild(style);
    }

    function show(message) {
        ensureStyles();

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'ics-loading-overlay';
            overlay.setAttribute('role', 'status');
            overlay.setAttribute('aria-live', 'polite');
            overlay.setAttribute('aria-busy', 'true');
            overlay.innerHTML =
                '<div class="ics-card">' +
                    '<div class="ics-spinner"></div>' +
                    '<p class="ics-title">Loading report</p>' +
                    '<p class="ics-text"></p>' +
                    '<div class="ics-bar"><span></span></div>' +
                '</div>';
            document.body
                ? document.body.appendChild(overlay)
                : document.documentElement.appendChild(overlay);
        }

        var textEl = overlay.querySelector('.ics-text');
        if (textEl) {
            textEl.textContent = message ||
                'Extracting entities from the submitted report. This may take a moment\u2026';
        }

        // Paint first, then flip opacity on so the fade-in transition plays.
        overlay.style.display = 'flex';
        void overlay.offsetWidth;
        overlay.classList.add('ics-show');

        if (autoHideTimer) clearTimeout(autoHideTimer);
        autoHideTimer = setTimeout(hide, AUTO_HIDE_MS);
    }

    function hide() {
        if (autoHideTimer) {
            clearTimeout(autoHideTimer);
            autoHideTimer = null;
        }
        if (!overlay) return;
        overlay.classList.remove('ics-show');
        setTimeout(function () {
            if (overlay && !overlay.classList.contains('ics-show')) {
                overlay.style.display = 'none';
            }
        }, 220);
    }

    // Expose for pages that want manual control.
    window.ICSLoadingOverlay = { show: show, hide: hide };

    // 1. Resume the overlay immediately when arriving on a page that runs
    //    extraction (script sits in <head>, so this paints before the body).
    var pendingFlag = readFlag();
    if (pendingFlag) {
        show(pendingFlag.message);
    }

    // 2. Clicking a data-ics-loading link shows the overlay before leaving.
    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        var target = event.target;
        var link = target && target.closest
            ? target.closest('a[data-ics-loading]')
            : null;
        if (!link || !link.href) return;
        if (link.target && link.target !== '_self') return;

        writeFlag(link.getAttribute('data-ics-loading') || '');
        show(link.getAttribute('data-ics-loading') || '');
    });

    // 3. Clear the flag once the destination has fully rendered.
    window.addEventListener('load', function () {
        clearFlag();
        setTimeout(hide, 300);
    });
})();
