// public/js/exportMenu.js

function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Close menu when clicking outside
window.addEventListener('click', function(e) {
    const menu = document.getElementById('exportMenu');
    const button = e.target.closest('button');
    if (!button || !button.getAttribute('onclick')?.includes('toggleExportMenu')) {
        if (menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    }
});