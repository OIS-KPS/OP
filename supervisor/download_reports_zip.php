<?php
// supervisor/download_reports_zip.php
// Streams a ZIP of the submitted report PDFs for the supervisor's interns.
//   ?student_id=N   ZIP one intern's reports
//   (no param)      ZIP all reports for all of the supervisor's interns
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/report_workspace.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header('Location: ../auth/login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$stmtSup = $pdo->prepare("SELECT id FROM supervisors WHERE user_id = ? LIMIT 1");
$stmtSup->execute([$userId]);
$supervisorId = (int) ($stmtSup->fetchColumn() ?: 0);
if ($supervisorId <= 0) {
    http_response_code(403);
    exit('Supervisor profile not found.');
}

$studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;

// Verify ownership when a single intern is requested.
if ($studentId > 0) {
    $stmtOwner = $pdo->prepare("SELECT id FROM students WHERE id = ? AND supervisor_id = ? LIMIT 1");
    $stmtOwner->execute([$studentId, $supervisorId]);
    if (!$stmtOwner->fetchColumn()) {
        http_response_code(403);
        exit('You do not have access to this intern.');
    }
}

$sql = "
    SELECT
        r.id AS report_id,
        r.week_number,
        r.submitted_at,
        r.file_path,
        s.id AS student_id,
        u.name AS student_name,
        s.student_number
    FROM reports r
    JOIN students s ON s.id = r.student_id
    JOIN users u ON u.id = s.user_id
    WHERE s.supervisor_id = ?
";
$params = [$supervisorId];
if ($studentId > 0) {
    $sql .= " AND s.id = ?";
    $params[] = $studentId;
}
$sql .= " ORDER BY u.name ASC, r.week_number ASC, r.submitted_at ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Group reports by intern so the ZIP contains one folder per student.
$grouped = [];
foreach ($reports as $report) {
    $path = resolveReportPdfPath((string) ($report['file_path'] ?? ''));
    if ($path === null) {
        continue;
    }
    $studentSlug = preg_replace('/[^A-Za-z0-9_-]+/', '_', trim((string) $report['student_name'])) ?: 'Student';
    $week = (int) $report['week_number'];
    $grouped[$studentSlug][] = [
        'path' => $path,
        'week' => $week,
    ];
}

if (empty($grouped)) {
    http_response_code(404);
    exit('No report PDFs are available to download.');
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    exit('The ZIP extension is not available on this server.');
}

$zipFile = tempnam(sys_get_temp_dir(), 'rep_') . '.zip';
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('Unable to create the ZIP archive.');
}

foreach ($grouped as $folder => $items) {
    $zip->addEmptyDir($folder);
    $usedNames = [];
    foreach ($items as $item) {
        $fileName = 'Week_' . $item['week'] . '_Report.pdf';
        if (isset($usedNames[$fileName])) {
            $usedNames[$fileName]++;
            $fileName = 'Week_' . $item['week'] . '_Report_' . $usedNames[$fileName] . '.pdf';
        } else {
            $usedNames[$fileName] = 1;
        }
        $zip->addFile($item['path'], $folder . '/' . $fileName);
    }
}
$zip->close();

$downloadName = $studentId > 0
    ? 'Intern_Reports_' . $studentId . '_' . date('Y-m-d') . '.zip'
    : 'All_Intern_Reports_' . date('Y-m-d') . '.zip';

while (ob_get_level() > 0) {
    ob_end_clean();
}
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($zipFile));
header('Cache-Control: private, max-age=0, must-revalidate');
readfile($zipFile);
@unlink($zipFile);
exit();