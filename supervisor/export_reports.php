<?php
// supervisor/export_reports.php
// Lists the report PDFs submitted by the supervisor's own interns, with
// links to view/download each file. This is a lightweight print-friendly
// page rather than a generated PDF.
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

$stmt = $pdo->prepare("
    SELECT
        r.id AS report_id,
        r.week_number,
        r.status,
        r.submitted_at,
        r.file_path,
        s.id AS student_id,
        u.name AS student_name,
        s.student_number
    FROM reports r
    JOIN students s ON s.id = r.student_id
    JOIN users u ON u.id = s.user_id
    WHERE s.supervisor_id = ?
    ORDER BY u.name ASC, r.week_number ASC, r.submitted_at ASC
");
$stmt->execute([$supervisorId]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

$stmtInterns = $pdo->prepare("
    SELECT
        s.id AS student_id,
        u.name AS student_name,
        s.student_number,
        COUNT(r.id) AS report_count
    FROM students s
    JOIN users u ON u.id = s.user_id
    LEFT JOIN reports r ON r.student_id = s.id
    WHERE s.supervisor_id = ?
    GROUP BY s.id, u.name, s.student_number
    ORDER BY u.name ASC
");
$stmtInterns->execute([$supervisorId]);
$interns = $stmtInterns->fetchAll(PDO::FETCH_ASSOC) ?: [];

foreach ($reports as &$report) {
    $report['pdf_path'] = resolveReportPdfPath((string) ($report['file_path'] ?? ''));
}
unset($report);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submitted Reports - Supervisor</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #0f172a; margin: 30px; font-size: 12px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f2854; padding-bottom: 12px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 18px; color: #0f2854; font-weight: bold; }
        p { margin: 2px 0 0; color: #475569; }
        table { width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 20px; }
        th { background: #f1f5f9; color: #0f172a; font-weight: bold; padding: 8px 10px; border: 1px solid #cbd5e1; font-size: 11px; text-transform: uppercase; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 11px; vertical-align: middle; }
        tr:nth-child(even) { background: #f8fafc; }
        .print-btn { background: #0f2854; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .link { color: #0f2854; font-weight: bold; text-decoration: none; }
        .link:hover { text-decoration: underline; }
        .muted { color: #94a3b8; font-style: italic; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button class="print-btn" onclick="window.print()">Print List</button>
    </div>

    <div class="header">
        <div>
            <h1>Submitted Reports</h1>
            <p>Intern-submitted report PDFs under your supervision &bull; Total: <?= count($reports); ?></p>
        </div>
        <div style="text-align: right;">
            <p><strong>Generated:</strong> <?= date('F d, Y g:i A'); ?></p>
        </div>
    </div>

    <div class="no-print" style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h2 style="margin: 0; font-size: 14px; color: #0f172a;">Download Report PDFs (ZIP)</h2>
            <a class="print-btn" href="download_reports_zip.php" style="background: #059669;">Download All Reports (ZIP)</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Student Intern</th>
                    <th>Reports</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($interns)): ?>
                    <tr><td colspan="3" style="text-align: center; padding: 12px;">No interns found.</td></tr>
                <?php else: ?>
                    <?php foreach ($interns as $intern): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars((string) $intern['student_name']); ?></strong><br>
                                <span style="color: #64748b; font-size: 10px;">ID: <?= htmlspecialchars((string) $intern['student_number']); ?></span>
                            </td>
                            <td><?= (int) $intern['report_count']; ?></td>
                            <td>
                                <?php if ((int) $intern['report_count'] > 0): ?>
                                    <a class="link" href="download_reports_zip.php?student_id=<?= (int) $intern['student_id']; ?>">Download ZIP</a>
                                <?php else: ?>
                                    <span class="muted">No reports</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student Intern</th>
                <th>Week</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Report PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr><td colspan="5" style="text-align: center; padding: 20px;">No submitted reports found.</td></tr>
            <?php else: ?>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars((string) $report['student_name']); ?></strong><br>
                            <span style="color: #64748b; font-size: 10px;">ID: <?= htmlspecialchars((string) $report['student_number']); ?></span>
                        </td>
                        <td>Week <?= (int) $report['week_number']; ?></td>
                        <td><?= htmlspecialchars(ucfirst((string) $report['status'])); ?></td>
                        <td><?= !empty($report['submitted_at']) ? date('M d, Y g:i A', strtotime((string) $report['submitted_at'])) : '—'; ?></td>
                        <td>
                            <?php if (!empty($report['pdf_path'])): ?>
                                <a class="link" href="view_report.php?student_id=<?= (int) $report['student_id']; ?>&report_id=<?= (int) $report['report_id']; ?>" target="_blank">View / Print PDF</a>
                            <?php else: ?>
                                <span class="muted">No file available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>