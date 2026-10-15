<?php
// coordinator/export_evaluation.php
// Exports the OJT Performance Evaluation Form as a PDF (mPDF).
//
// SCOPE
// Only evaluations the supervisor has completed are exportable. "Completed"
// is the same definition the Evaluations tab uses (coordinator/evaluations.php):
//
//     e.id IS NOT NULL AND e.otp_verified = 1
//
// An unsigned evaluation is refused with 403 rather than exported as a partial
// record, because a PDF that looks official but was never signed is worse than
// no PDF at all.
//
// LAYOUT
// The form is sized to land on a single A4 page with a typical evaluation. The
// competency wording is never shortened -- it mirrors the CHED docx and is the
// whole point of the document -- so the density comes from type size and
// padding instead. When a supervisor writes remarks long enough to overflow,
// the document grows to a second page rather than truncating, and the signature
// block moves whole rather than splitting across the page break.
//
// ROUTES
//   ?student_id=N   one intern's evaluation
//   ?bulk=1         every signed evaluation, one page each
//
// STORAGE
// Generated files are archived on the server under
//
//     public/files/OJT Evaluation/Coming, Katelyn.pdf
//
// The folder is created on demand, so there is no setup step. Keeping the
// document here means the signed evaluation survives the coordinator losing
// their laptop, and travels with the project when it is backed up or
// redeployed. ?download=1 still streams a copy straight to the browser.
//
// The scorecard is rebuilt here rather than reusing
// src/components/evaluation_scorecard.php because that component is written in
// Tailwind utility classes, which mPDF cannot resolve. Everything below is
// inline CSS, themed on the portal navy #0F2854.
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header('Location: ../auth/login.php');
    exit();
}

// Notices and deprecations would otherwise be prepended to the PDF stream.
@ini_set('display_errors', '0');

require_once __DIR__ . '/../config/evaluation_criteria.php';

$NAVY = '#0F2854';

if (!function_exists('evalPdfE')) {
    function evalPdfE($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * "Katelyn Coming" -> "Coming, Katelyn"
 * "Coming, Katelyn" -> "Coming, Katelyn"  (already reordered, left alone)
 *
 * users.name is a single free-text field. Every row in the live database is
 * stored "Givenname Surname" with no comma, so the surname has to be inferred
 * from the last token. A comma means the name was already entered in
 * "Surname, Givenname" order and is passed through untouched.
 *
 * A trailing generational suffix (Jr, Sr, II ...) is skipped so
 * "Maria Santos Jr" becomes "Santos, Maria Jr" rather than "Jr, Maria Santos".
 */
if (!function_exists('evalPdfFilenameFor')) {
    function evalPdfFilenameFor(string $fullName): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $fullName) ?? '');
        if ($name === '') {
            return 'Evaluation';
        }

        if (strpos($name, ',') !== false) {
            [$last, $first] = array_pad(explode(',', $name, 2), 2, '');
            $last  = trim($last);
            $first = trim($first);
            return ($last !== '' && $first !== '') ? $last . ', ' . $first : $name;
        }

        $parts    = explode(' ', $name);
        $lastIdx  = count($parts) - 1;
        $suffix   = '';

        $suffixes = ['jr', 'jr.', 'sr', 'sr.', 'ii', 'iii', 'iv'];
        if ($lastIdx > 0 && in_array(strtolower($parts[$lastIdx]), $suffixes, true)) {
            $suffix  = array_pop($parts);
            $lastIdx = count($parts) - 1;
        }

        // A single token has no surname to separate, so leave it as typed.
        if ($lastIdx < 1) {
            return $name;
        }

        $surname = implode(' ', array_slice($parts, $lastIdx));
        $given   = implode(' ', array_slice($parts, 0, $lastIdx));
        if ($suffix !== '') {
            $given .= ' ' . $suffix;
        }

        return $surname . ', ' . $given;
    }
}

/**
 * Strip characters that are illegal or awkward in a Windows/Mac filename.
 * The comma and space are deliberately kept -- they are part of the requested
 * "Lastname, Firstname" format.
 */
if (!function_exists('evalPdfSafeFilename')) {
    function evalPdfSafeFilename(string $filename): string
    {
        $filename = preg_replace('/[\\\\\/:*?"<>|]+/', '', $filename) ?? '';
        $filename = trim(preg_replace('/\s+/', ' ', $filename) ?? '');
        if ($filename === '') {
            $filename = 'Evaluation';
        }

        // A trailing dot would be read as an extension by Windows.
        return rtrim($filename, '. ') . '.pdf';
    }
}

/**
 * Strip any tags a supervisor typed into the remarks field, so free text can
 * never inject structure into the generated document.
 */
if (!function_exists('evalPdfClean')) {
    function evalPdfClean(string $text): string
    {
        return preg_replace('/<[^>]*>/', '', $text) ?? '';
    }
}

/**
 * public/files/OJT Evaluation, created on first use.
 *
 * public/ is already web-served and already holds the reference docx forms, so
 * putting the archive beside them needs no .htaccess change. The directory is
 * created recursively and only when missing; an existing archive is never
 * touched.
 *
 * The base path is resolved once at file scope, where __DIR__ reliably means
 * "the coordinator directory". Reading __DIR__ from inside the function would
 * resolve against whichever file happened to be executing, which is wrong the
 * moment this logic is reused or tested.
 *
 * Returns null when the folder cannot be created, so the caller falls back to
 * streaming the file rather than failing outright.
 */
if (!function_exists('evalPdfArchiveDir')) {
    function evalPdfArchiveDir(?string $base = null): ?string
    {
        static $resolved = false;
        static $dir      = null;

        if ($resolved) {
            return $dir;
        }
        $resolved = true;

        $base = $base ?? (__DIR__ . '/../public/files');
        if (!is_dir($base)) {
            return $dir = null;
        }

        $target = $base . '/OJT Evaluation';
        if (!is_dir($target) && !@mkdir($target, 0775, true) && !is_dir($target)) {
            error_log('Evaluation export: could not create ' . $target);
            return $dir = null;
        }

        return $dir = realpath($target) ?: $target;
    }
}

/**
 * "Coming, Katelyn.pdf" -> "Coming, Katelyn_20231053.pdf"
 *
 * The student number is appended on the server copy so two interns who share a
 * name cannot silently overwrite one another's archived record.
 */
if (!function_exists('evalPdfArchiveFilename')) {
    function evalPdfArchiveFilename(string $filename, string $studentNumber): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $id   = trim($studentNumber);
        if ($id === '') {
            return $filename;
        }
        $id = preg_replace('/[^A-Za-z0-9_-]+/', '', $id) ?? '';

        return $id !== '' ? $base . '_' . $id . '.pdf' : $filename;
    }
}

/**
 * Load one signed evaluation with the student and supervisor names attached.
 * Returns null when there is no evaluation, or when it is not yet signed.
 */
if (!function_exists('evalPdfFetch')) {
    function evalPdfFetch(PDO $pdo, int $studentId): ?array
    {
        $stmt = $pdo->prepare("
            SELECT
                e.*,
                s.student_number,
                s.program,
                COALESCE(s.section, 'A') AS section,
                COALESCE(c.name, 'Host Company') AS company_name,
                COALESCE(o.name, 'Unassigned')     AS office_name,
                u.name AS student_name,
                COALESCE(sup_user.name, '')       AS supervisor_name
            FROM evaluations e
            JOIN students s ON s.id = e.student_id
            JOIN users u    ON u.id = s.user_id
            LEFT JOIN companies c     ON c.id = s.company_id
            LEFT JOIN offices o       ON o.id = s.office_id
            LEFT JOIN supervisors sup ON sup.id = e.supervisor_id
            LEFT JOIN users sup_user  ON sup_user.id = sup.user_id
            WHERE e.student_id = ?
            LIMIT 1
        ");
        $stmt->execute([$studentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row && (int) $row['otp_verified'] === 1) ? $row : null;
    }
}

/**
 * Every signed evaluation, oldest signature first so the bundle reads
 * chronologically.
 */
if (!function_exists('evalPdfFetchAll')) {
    function evalPdfFetchAll(PDO $pdo): array
    {
        $stmt = $pdo->prepare("
            SELECT
                e.*,
                s.student_number,
                s.program,
                COALESCE(s.section, 'A') AS section,
                COALESCE(c.name, 'Host Company') AS company_name,
                COALESCE(o.name, 'Unassigned')     AS office_name,
                u.name AS student_name,
                COALESCE(sup_user.name, '')       AS supervisor_name
            FROM evaluations e
            JOIN students s ON s.id = e.student_id
            JOIN users u    ON u.id = s.user_id
            LEFT JOIN companies c     ON c.id = s.company_id
            LEFT JOIN offices o       ON o.id = s.office_id
            LEFT JOIN supervisors sup ON sup.id = e.supervisor_id
            LEFT JOIN users sup_user  ON sup_user.id = sup.user_id
            WHERE e.otp_verified = 1
            ORDER BY e.otp_signed_at ASC, u.name ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

/**
 * One intern's evaluation as a full page of HTML.
 */
if (!function_exists('evalPdfDocument')) {
    function evalPdfDocument(array $evaluation, string $navy): string
    {
        $ratings  = ojtDecodeRatings($evaluation['criteria_ratings'] ?? null);
        $rated    = ojtRatedCount($ratings);
        $overall  = $rated > 0 ? ojtOverallRating($ratings) : (float) ($evaluation['final_score'] ?? 0);
        $grade    = (string) ($evaluation['grade_equivalent'] ?? '');
        $grade    = $grade !== '' ? $grade : ojtRatingLabel($overall);
        $remarks  = evalPdfClean(trim((string) ($evaluation['feedback'] ?? '')));
        $scale    = ojtRatingScale();
        $bounds   = ojtRatingBounds();
        $groups   = ojtEvaluationGroups();
        $criteria = ojtCriteriaForEvaluation($evaluation);

        $signedAt = !empty($evaluation['otp_signed_at'])
            ? date('M d, Y \a\t g:i A', strtotime((string) $evaluation['otp_signed_at']))
            : '';

        // ---- competency rows -------------------------------------------------
        $rows = '';
        foreach ($criteria as $criterion) {
            $key   = $criterion['key'];
            $value = (int) ($ratings[$key] ?? 0);
            $label = $value > 0 ? (string) ($scale[$value] ?? '') : 'Not rated';
            $group = (string) ($groups[$criterion['group']] ?? '');

            $ratingCell = $value > 0
                ? '<span style="font-weight:700;color:' . $navy . ';font-size:8pt;">' . $value . '</span>'
                  . '<span style="color:#94a3b8;font-size:6pt;"> / ' . (int) $bounds['max'] . '</span>'
                  . '<div style="font-size:6pt;color:#64748b;line-height:1.15;">' . evalPdfE($label) . '</div>'
                : '<span style="color:#cbd5e1;">&mdash;</span>';

            $rows .= '<tr>'
                . '<td style="padding:3px 5px;border-bottom:1px solid #e2e8f0;font-size:7pt;line-height:1.25;">'
                .   evalPdfE($criterion['text'])
                .   '<div style="font-size:6pt;color:#94a3b8;margin-top:0.5px;">' . evalPdfE($group) . '</div>'
                . '</td>'
                . '<td style="padding:3px 5px;border-bottom:1px solid #e2e8f0;text-align:center;width:62px;">'
                .   $ratingCell
                . '</td>'
                . '</tr>';
        }

        // ---- group averages --------------------------------------------------
        $groupAverages = ojtGroupAverages($ratings);
        $columnMap     = ojtGroupColumnMap();
        $groupRows     = '';
        foreach ($groups as $key => $label) {
            $avg = (float) ($groupAverages[$key] ?? 0.0);
            // Fall back to the stored column when nothing was rated inline.
            if ($avg <= 0 && isset($columnMap[$key]) && isset($evaluation[$columnMap[$key]])) {
                $avg = (float) $evaluation[$columnMap[$key]];
            }

            $groupRows .= '<tr>'
                . '<td style="padding:2.5px 5px;border-bottom:1px solid #e2e8f0;font-size:7.5pt;">'
                .   evalPdfE($label)
                . '</td>'
                . '<td style="padding:2.5px 5px;border-bottom:1px solid #e2e8f0;text-align:center;'
                .   'width:62px;font-weight:700;color:' . $navy . ';">'
                .   ($avg > 0 ? number_format($avg, 2) : '&mdash;')
                . '</td>'
                . '</tr>';
        }

        // ---- legend ----------------------------------------------------------
        $legend = '';
        foreach ($scale as $value => $label) {
            $legend .= '<span style="display:inline-block;margin-right:12px;">'
                . '<strong>' . (int) $value . '</strong> &mdash; ' . evalPdfE($label)
                . '</span>';
        }

        // ---- meta block ------------------------------------------------------
        $metaRows = [
            'Intern'         => (string) ($evaluation['student_name'] ?? ''),
            'Student No.'    => (string) ($evaluation['student_number'] ?? ''),
            'Program'        => (string) ($evaluation['program'] ?? ''),
            'Section'        => (string) ($evaluation['section'] ?? ''),
            'Host Company'   => (string) ($evaluation['company_name'] ?? ''),
            'Office'         => (string) ($evaluation['office_name'] ?? ''),
            'Supervisor'     => (string) ($evaluation['supervisor_name'] ?? ''),
        ];

        $metaCells = '';
        foreach ($metaRows as $label => $value) {
            $metaCells .= '<div style="margin-bottom:3px;">'
                .   '<span style="display:inline-block;width:74px;color:#64748b;font-size:7pt;'
                .   'text-transform:uppercase;letter-spacing:.3pt;font-weight:700;">'
                .     evalPdfE($label)
                .   '</span>'
                .   '<span style="font-size:8pt;font-weight:600;color:#0f172a;">'
                .     evalPdfE($value !== '' ? $value : 'Not recorded')
                .   '</span>'
                . '</div>';
        }

        $remarksBlock = $remarks !== ''
            ? '<p style="font-size:7.5pt;line-height:1.35;margin:0;color:#334155;">'
              . nl2br(evalPdfE($remarks)) . '</p>'
            : '<p style="font-size:7.5pt;margin:0;color:#94a3b8;font-style:italic;">'
              . 'No remarks were recorded.</p>';

        $supervisorName = (string) ($evaluation['supervisor_name'] ?? '');
        // page-break-inside keeps the block whole. Without it mPDF can slice the
        // signature box across a page boundary, which reads as a cut-off form.
        $signatureBlock = '<div style="border:1px solid ' . $navy . ';border-radius:4px;'
            . 'padding:7px 9px;margin-top:9px;background:#f8fafc;page-break-inside:avoid;">'
            . '<div style="font-size:6.5pt;text-transform:uppercase;letter-spacing:.4pt;'
            . 'color:' . $navy . ';font-weight:800;margin-bottom:4px;">Digitally Signed</div>'
            . '<table style="width:100%;border-collapse:collapse;">'
            . '<tr>'
            . '<td style="font-size:8pt;color:#0f172a;width:50%;">'
            .   '<strong>' . evalPdfE($supervisorName !== '' ? $supervisorName : 'Supervisor') . '</strong>'
            .   '<div style="font-size:6.5pt;color:#64748b;">Supervisor</div>'
            . '</td>'
            . '<td style="font-size:8pt;color:#0f172a;">'
            .   ($signedAt !== ''
                ?   '<strong>' . evalPdfE($signedAt) . '</strong>'
                :   '<span style="color:#94a3b8;">Signed date not recorded</span>')
            .   '<div style="font-size:6.5pt;color:#64748b;">Signed via OTP verification</div>'
            . '</td>'
            . '</tr>'
            . '</table>'
            . '<div style="font-size:6pt;color:#94a3b8;margin-top:5px;'
            . 'border-top:1px solid #e2e8f0;padding-top:4pt;">'
            .   'Verified through the OJT Portal one-time password. No physical signature is required.'
            . '</div>'
            . '</div>';

        return '<div style="font-family:dejavusans,sans-serif;color:#0f172a;">'

            // ---- header ----
            . '<div style="border-bottom:2.5px solid ' . $navy . ';padding-bottom:7px;margin-bottom:11px;">'
            .   '<div style="font-size:7pt;color:' . $navy . ';font-weight:800;letter-spacing:1.1pt;'
            .     'text-transform:uppercase;">OJT Performance Evaluation</div>'
            .   '<div style="font-size:14pt;font-weight:800;color:#0f172a;margin-top:2px;">'
            .     'Evaluation Form'
            .   '</div>'
            .   '<div style="font-size:7pt;color:#64748b;margin-top:1px;">'
            .     'Completed and signed by the intern&rsquo;s assigned supervisor.'
            .   '</div>'
            . '</div>'

            // ---- details + overall rating ----
            . '<table style="width:100%;border-collapse:collapse;margin-bottom:12px;">'
            . '<tr>'
            . '<td style="width:58%;vertical-align:top;">' . $metaCells . '</td>'
            . '<td style="vertical-align:top;">'
            .   '<div style="border:1px solid #e2e8f0;border-radius:4px;padding:8px 10px;text-align:center;">'
            .     '<div style="font-size:6.5pt;text-transform:uppercase;letter-spacing:.4pt;'
            .       'color:#64748b;font-weight:700;">Overall Rating</div>'
            .     '<div style="font-size:16pt;font-weight:800;color:' . $navy . ';line-height:1.05;">'
            .       ($overall > 0 ? number_format($overall, 2) : '&mdash;')
            .     '</div>'
            .     '<div style="font-size:6.5pt;color:#94a3b8;">out of '
            .       (int) $bounds['max'] . '</div>'
            .     '<div style="margin-top:5px;padding-top:5px;border-top:1px solid #e2e8f0;'
            .       'font-size:8pt;font-weight:700;color:' . $navy . ';">'
            .       evalPdfE($grade)
            .     '</div>'
            .   '</div>'
            .   '<div style="margin-top:7px;font-size:6.5pt;color:#64748b;text-align:center;">'
            .     ($rated > 0
                ?     $rated . ' of ' . count($criteria) . ' competencies rated'
                :     'No competency ratings recorded')
            .   '</div>'
            . '</td>'
            . '</tr>'
            . '</table>'

            // ---- competency grid ----
            . '<div style="font-size:7pt;text-transform:uppercase;letter-spacing:.6pt;'
            .   'font-weight:800;color:' . $navy . ';margin-bottom:4px;">Competency Rating</div>'
            . '<table style="width:100%;border-collapse:collapse;border:1px solid #cbd5e1;">'
            . '<thead><tr style="background:' . $navy . ';">'
            . '<th style="padding:5px 6px;font-size:6.5pt;text-transform:uppercase;'
            . 'letter-spacing:.4pt;color:#ffffff;text-align:left;">Competency</th>'
            . '<th style="padding:5px 6px;font-size:6.5pt;text-transform:uppercase;'
            . 'letter-spacing:.4pt;color:#ffffff;text-align:center;width:74px;">Rating</th>'
            . '</tr></thead>'
            . '<tbody>' . $rows . '</tbody>'
            . '</table>'

            // ---- group averages ----
            . '<div style="font-size:7pt;text-transform:uppercase;letter-spacing:.6pt;'
            .   'font-weight:800;color:' . $navy . ';margin:11px 0 4px;">Group Averages</div>'
            . '<table style="width:100%;border-collapse:collapse;border:1px solid #cbd5e1;">'
            . '<tbody>' . $groupRows . '</tbody>'
            . '</table>'

            // ---- legend ----
            . '<div style="margin-top:11px;padding:7px 9px;background:#f1f5f9;border-radius:4px;'
            .   'font-size:7pt;color:#475569;">'
            .   '<strong style="color:' . $navy . ';">Rating Scale &nbsp;</strong>' . $legend
            . '</div>'

            // ---- remarks ----
            . '<div style="font-size:7pt;text-transform:uppercase;letter-spacing:.6pt;'
            .   'font-weight:800;color:' . $navy . ';margin:11px 0 4px;">'
            .   'Comments / Remarks</div>'
            . '<div style="border:1px solid #e2e8f0;border-radius:4px;padding:8px 10px;">'
            .   $remarksBlock
            . '</div>'

            . $signatureBlock

            . '</div>';
    }
}

// ---------------------------------------------------------------
// Resolve the request
// ---------------------------------------------------------------
$isBulk      = isset($_GET['bulk']) && $_GET['bulk'] === '1';
$studentId   = (int) ($_GET['student_id'] ?? 0);
$generatedAt = date('M d, Y');
// Without this the PDF renders inside the page frame instead of as a document.
$isDownload  = isset($_GET['download']) && $_GET['download'] === '1';

$autoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

if (!class_exists('\Mpdf\Mpdf')) {
    http_response_code(500);
    exit('The PDF library is not installed. Run composer install.');
}

if ($isBulk) {
    $evaluations = evalPdfFetchAll($pdo);
    if (empty($evaluations)) {
        http_response_code(404);
        exit('There are no signed evaluations to export yet.');
    }
} else {
    if ($studentId <= 0) {
        http_response_code(400);
        exit('No student was selected.');
    }

    $evaluation = evalPdfFetch($pdo, $studentId);
    if ($evaluation === null) {
        http_response_code(403);
        exit('This evaluation has not been signed by the supervisor, so it cannot be exported.');
    }
    $evaluations = [$evaluation];
}

// A bundle of exactly one is the same document as that intern's own export, so
// it is named after the intern rather than after the batch. "Export All Signed
// (1)" should not hand back "OJT Evaluations - 1 signed - Oct 14, 2026".
$isBundleName = $isBulk && count($evaluations) > 1;

$filename = $isBundleName
    ? evalPdfSafeFilename('OJT Evaluations - ' . count($evaluations) . ' signed - ' . date('M d, Y'))
    : evalPdfSafeFilename(evalPdfFilenameFor((string) ($evaluations[0]['student_name'] ?? 'Evaluation')));

// A single record is archived under a collision-proof name so two interns who
// share a name cannot overwrite one another. A multi-record bundle is never
// archived -- its name changes every time the signed count changes.
$archiveName = $isBundleName
    ? null
    : evalPdfArchiveFilename($filename, (string) ($evaluations[0]['student_number'] ?? ''));

// Serve the archived copy if it already exists. Regenerating a large bundle on
// every click is wasteful, and the archived copy is already correct.
$archiveDir  = evalPdfArchiveDir();
$archivePath = ($archiveDir !== null && $archiveName !== null)
    ? $archiveDir . DIRECTORY_SEPARATOR . $archiveName
    : null;

if (!$isDownload && $archivePath !== null && is_file($archivePath)) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($archivePath));
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    readfile($archivePath);
    exit;
}

// No page footer. The signature block already carries the provenance, and a
// "Generated ... Page X of Y" strip along the bottom edge made the document
// read like a system printout rather than a form.

$html = '';
$total = count($evaluations);
foreach ($evaluations as $index => $evaluation) {
    $html .= evalPdfDocument($evaluation, $NAVY);
    // mPDF needs an explicit break between records in a bundle.
    if ($index < $total - 1) {
        $html .= '<pagebreak />';
    }
}

try {
    $mpdf = new \Mpdf\Mpdf([
        'tempDir'      => sys_get_temp_dir(),
        'default_font' => 'dejavusans',
        'margin_left'  => 12,
        'margin_right' => 12,
        'margin_top'   => 11,
        'margin_bottom' => 11,
    ]);

    $mpdf->SetTitle('OJT Performance Evaluation');
    $mpdf->SetAuthor('OJT Portal');
    $mpdf->SetHTMLFooter('');
    $mpdf->WriteHTML($html);

    // Render once, then distribute from the bytes. mPDF cannot be asked to
    // Output() twice -- the second call throws -- so the archive copy and the
    // response both come from this single string.
    $pdfBytes = $mpdf->Output('', 'S');
    if (!is_string($pdfBytes) || $pdfBytes === '') {
        throw new \RuntimeException('mPDF returned no content');
    }

    // Archive before responding, so the record is stored even if the browser
    // request is cut short mid-download. A failed write is logged but never
    // blocks the export -- the coordinator still gets their copy.
    if ($archivePath !== null && @file_put_contents($archivePath, $pdfBytes) === false) {
        error_log('Evaluation export: could not write archive ' . $archivePath);
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfBytes));
    header('Content-Disposition: ' . ($isDownload ? 'attachment' : 'inline')
        . '; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    echo $pdfBytes;
} catch (\Throwable $exception) {
    error_log('Evaluation PDF export error: ' . $exception->getMessage());
    http_response_code(500);
    echo 'Unable to generate the evaluation PDF export.';
}
exit;