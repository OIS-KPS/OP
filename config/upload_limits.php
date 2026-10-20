<?php
// config/upload_limits.php
/**
 * ============================================================
 * config/upload_limits.php
 * ============================================================
 *
 * Single source of truth for what a student is allowed to upload.
 *
 * WHY A SEPARATE FILE
 * Three different limits used to disagree, and none of them matched what the
 * page told the student:
 *
 *     upload UI label        10 MB
 *     php.ini                40 MB
 *     python/api/main.py     25 MiB   (MAX_PDF_BYTES)
 *
 * So a 30 MB PDF uploaded happily and was then rejected by the extractor
 * permanently, leaving the report with 0% entities and no visible reason. The
 * label was also simply wrong -- nothing enforced 10 MB.
 *
 * Rule going forward: ONE number here, and it must equal MAX_PDF_BYTES in
 * python/api/main.py. If the extractor limit changes, change it here in the
 * same commit.
 */

if (!defined('MAX_UPLOAD_PDF_BYTES')) {
    /**
     * 25 MiB. Matches MAX_PDF_BYTES = 25 * 1024 * 1024 in python/api/main.py.
     */
    define('MAX_UPLOAD_PDF_BYTES', 25 * 1024 * 1024);
}

if (!defined('MAX_UPLOAD_PDF_LABEL')) {
    /**
     * Shown to students. Derive it from the byte limit so the two cannot drift.
     * 26214400 bytes -> 25 MB
     */
    define('MAX_UPLOAD_PDF_LABEL', (MAX_UPLOAD_PDF_BYTES % (1024 * 1024) === 0)
        ? (MAX_UPLOAD_PDF_BYTES / (1024 * 1024)) . 'MB'
        : round(MAX_UPLOAD_PDF_BYTES / 1048576) . 'MB');
}

if (!defined('ALLOWED_UPLOAD_EXTENSIONS')) {
    define('ALLOWED_UPLOAD_EXTENSIONS', ['pdf']);
}

/**
 * ------------------------------------------------------------
 * Human-readable size, for error messages
 * ------------------------------------------------------------
 */
function uploadLimitLabel(): string
{
    return (string) MAX_UPLOAD_PDF_LABEL;
}

/**
 * ------------------------------------------------------------
 * Is this upload error "the file was too big"?
 * ------------------------------------------------------------
 *
 * UPLOAD_ERR_INI_SIZE and UPLOAD_ERR_FORM_SIZE mean PHP rejected the body before
 * the script ever ran, so $_FILES['size'] is 0 and there is nothing to measure.
 * They have to be recognised from the error code or the student is told they
 * picked the wrong kind of file.
 *
 * The original code checked only for UPLOAD_ERR_OK and lumped everything else
 * into "Please select a PDF file to upload."
 */
function uploadErrorIsTooLarge(int $errorCode): bool
{
    return $errorCode === UPLOAD_ERR_INI_SIZE
        || $errorCode === UPLOAD_ERR_FORM_SIZE;
}

/**
 * ------------------------------------------------------------
 * Did the file exceed MAX_UPLOAD_PDF_BYTES?
 * ------------------------------------------------------------
 *
 * The backstop for when php.ini is more generous than the extractor. Checked
 * before move_uploaded_file() so an oversized file is never written to disk.
 */
function uploadExceedsSizeLimit(int $sizeInBytes): bool
{
    return $sizeInBytes > MAX_UPLOAD_PDF_BYTES;
}
