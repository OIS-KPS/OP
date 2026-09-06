<?php
// coordinator/export_summary.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$format = strtolower(trim($_GET['format'] ?? 'csv'));
$selectedWeek = $_GET['week'] ?? 'all';
$selectedCompany = $_GET['company_id'] ?? 'all';
$searchQuery = trim($_GET['search'] ?? '');

// Build filtered query
$whereClauses = ["LOWER(r.status) = 'approved'"];
$params = [];

if ($selectedWeek !== 'all' && is_numeric($selectedWeek)) {
    $whereClauses[] = "r.week_number = :week";
    $params['week'] = (int)$selectedWeek;
}

if ($selectedCompany !== 'all' && is_numeric($selectedCompany)) {
    $whereClauses[] = "c.id = :comp_id";
    $params['comp_id'] = (int)$selectedCompany;
}

if (!empty($searchQuery)) {
    $whereClauses[] = "(u.name LIKE :search OR s.student_number LIKE :search)";
    $params['search'] = "%{$searchQuery}%";
}

$whereSQL = "WHERE " . implode(' AND ', $whereClauses);

// Schema-Accurate Query (r.updated_at removed; sorted by submitted_at ASC)
$sql = "
    SELECT 
        r.week_number,
        u.name AS student_name,
        s.student_number,
        s.program,
        COALESCE(c.name, 'Host Company') AS company_name,
        COALESCE(u_sup.name, 'Assigned Supervisor') AS supervisor_name,
        r.submitted_at
    FROM reports r
    JOIN students s ON r.student_id = s.id
    JOIN users u ON s.user_id = u.id
    LEFT JOIN companies c ON s.company_id = c.id
    LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
    LEFT JOIN users u_sup ON sup.user_id = u_sup.id
    {$whereSQL}
    ORDER BY r.submitted_at ASC, r.week_number ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// CSV Export
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=OJT_Approved_Reports_Summary_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, ['Week #', 'Student Name', 'Student ID', 'Program', 'Host Company', 'Supervisor', 'Date Verified']);

    foreach ($rows as $r) {
        fputcsv($output, [
            'Week ' . $r['week_number'],
            $r['student_name'],
            $r['student_number'],
            $r['program'],
            $r['company_name'],
            $r['supervisor_name'],
            !empty($r['submitted_at']) ? date("M d, Y g:i A", strtotime($r['submitted_at'])) : '—'
        ]);
    }
    fclose($output);
    exit();
}

// PDF View
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accomplishment Reports Summary - <?= date('Y-m-d'); ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #0f172a; margin: 30px; font-size: 12px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f2854; padding-bottom: 12px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 18px; color: #0f2854; font-weight: bold; }
        p { margin: 2px 0 0; color: #475569; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #f1f5f9; color: #0f172a; font-weight: bold; padding: 8px 10px; border: 1px solid #cbd5e1; font-size: 11px; text-transform: uppercase; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 11px; }
        tr:nth-child(even) { background: #f8fafc; }
        .print-btn { background: #0f2854; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
        <button class="print-btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="header">
        <div>
            <h1>OJT Accomplishment Reports Summary</h1>
            <p>NBSC &bull; Institute for Computer Studies &bull; Total Reports: <?= count($rows); ?></p>
        </div>
        <div style="text-align: right;">
            <p><strong>Generated:</strong> <?= date('F d, Y g:i A'); ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Week</th>
                <th>Student Intern</th>
                <th>Program</th>
                <th>Host Partner</th>
                <th>Supervisor</th>
                <th>Date Verified</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="6" style="text-align: center; padding: 20px;">No approved reports found.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><strong>Week <?= htmlspecialchars($r['week_number']); ?></strong></td>
                        <td><strong><?= htmlspecialchars($r['student_name']); ?></strong> (<?= htmlspecialchars($r['student_number']); ?>)</td>
                        <td><?= htmlspecialchars($r['program']); ?></td>
                        <td><?= htmlspecialchars($r['company_name']); ?></td>
                        <td><?= htmlspecialchars($r['supervisor_name']); ?></td>
                        <td><?= !empty($r['submitted_at']) ? date("M d, Y g:i A", strtotime($r['submitted_at'])) : '—'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>