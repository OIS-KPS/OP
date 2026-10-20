<?php
// api/report_entities_status.php
// Reports whether entity extraction has finished for one report, so an open
// report page can pick up the result without the member of staff reloading.
//
// Polled by src/pages/{coordinator,supervisor}/viewReportPage.php every few
// seconds while a background extraction is in flight.

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/entity_extraction.php';

// Staff only. This exposes no report content, but it is still a per-report
// signal and must not be reachable by a student session.
$role = $_SESSION['role'] ?? '';

if ($role !== 'coordinator' && $role !== 'supervisor') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Not authorised.']);
    exit();
}

header('Content-Type: application/json; charset=utf-8');
// Nothing here is a secret and it is polled repeatedly; a short cache keeps the
// page from being revalidated on every navigation.
header('Cache-Control: no-store');

$reportId = isset($_GET['report_id']) ? (int) $_GET['report_id'] : 0;

if ($reportId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'A report id is required.']);
    exit();
}

/*
 * A supervisor may only poll reports belonging to interns assigned to them.
 *
 * entityExtractionReportStatus() exposes only timestamps and a failure reason,
 * never report content, so this is not a data leak -- but without it a
 * supervisor could enumerate which report ids exist and whether their PDF was
 * readable, which is not theirs to know.
 */
if ($role === 'supervisor') {
    $supervisorId = (int) ($_SESSION['supervisor_id'] ?? 0);

    $stmt = $pdo->prepare(
        'SELECT 1
         FROM reports r
         INNER JOIN students s ON s.id = r.student_id
         WHERE r.id = ? AND s.supervisor_id = ?
         LIMIT 1'
    );

    $stmt->execute([$reportId, $supervisorId]);

    if ($stmt->fetchColumn() === false) {
        http_response_code(403);
        echo json_encode(['error' => 'Not authorised.']);
        exit();
    }
}

$status = entityExtractionReportStatus($pdo, $reportId);

echo json_encode([
    'report_id'    => $status['report_id'],
    // The client compares this against what it rendered. A change means the
    // entities on screen are out of date and the page should reload.
    'extracted_at' => $status['extracted_at'],
    'started_at'   => $status['started_at'],
    'pending'      => $status['pending'],
    // Only present when extraction failed permanently.
    'error'        => $status['error'],
]);
