// public/js/userModal.js

/**
 * Toggle visibility of a modal by its element ID
 * @param {string} id 
 */
function toggleModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.toggle('hidden');
    }
}

/**
 * Switch form fields based on selected role (Student vs. Supervisor)
 */
function toggleRoleFields() {
    const roleSelect = document.getElementById('userRoleSelect');
    if (!roleSelect) return;

    const role = roleSelect.value;
    const studentFields = document.getElementById('studentFields');
    const supervisorFields = document.getElementById('supervisorFields');

    const studentNum = document.getElementById('studentNumberInput');
    const studentEmail = document.getElementById('studentEmailInput');
    const supEmail = document.getElementById('supervisorEmailInput');
    const supComp = document.getElementById('supervisorCompanySelect');

    if (role === 'student') {
        studentFields.classList.remove('hidden');
        supervisorFields.classList.add('hidden');

        // Enable Student inputs & Disable Supervisor inputs
        if (studentNum) studentNum.disabled = false;
        if (studentEmail) studentEmail.disabled = false;
        if (supEmail) supEmail.disabled = true;
        if (supComp) supComp.disabled = true;
    } else {
        studentFields.classList.add('hidden');
        supervisorFields.classList.remove('hidden');

        // Disable Student inputs & Enable Supervisor inputs
        if (studentNum) studentNum.disabled = true;
        if (studentEmail) studentEmail.disabled = true;
        if (supEmail) supEmail.disabled = false;
        if (supComp) supComp.disabled = false;
    }
}

/**
 * Show bulk import validation preview block
 */
function showMockPreview() {
    const previewArea = document.getElementById('importPreviewArea');
    if (previewArea) {
        previewArea.classList.remove('hidden');
    }
}