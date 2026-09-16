<?php
// coordinator/export_summary.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$format          = strtolower(trim($_GET['format'] ?? 'csv'));
$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedSection = $_GET['section'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');

$whereClauses = ["u.status = 'active'"];
$params = [];

if ($selectedCompany !== 'all' && is_numeric($selectedCompany) && intval($selectedCompany) > 0) {
    $whereClauses[] = "c.id = :comp_id";
    $params['comp_id'] = intval($selectedCompany);
}

if ($selectedSection !== 'all' && $selectedSection !== '') {
    $whereClauses[] = "COALESCE(s.section, 'A') = :sec";
    $params['sec'] = $selectedSection;
}

if ($searchQuery !== '') {
    $whereClauses[] = "(LOWER(u.name) LIKE :search_name OR LOWER(s.student_number) LIKE :search_num)";
    $searchParam = '%' . strtolower($searchQuery) . '%';
    $params['search_name'] = $searchParam;
    $params['search_num']  = $searchParam;
}

$whereSQL = "WHERE " . implode(' AND ', $whereClauses);

$sql = "
    SELECT 
        s.id AS student_id,
        s.student_number,
        s.program,
        COALESCE(s.section, 'A') AS section,
        u.name AS student_name,
        COALESCE(c.name, 'Host Company') AS company_name,
        COALESCE(u_sup.name, 'Unassigned') AS supervisor_name,
        (SELECT COUNT(id) FROM reports WHERE student_id = s.id AND status = 'approved') AS approved_reports_count,
        SUM(CASE WHEN re.activity_type != 'Clerical' AND re.activity_type != '' AND re.is_archived = 0 THEN 1 ELSE 0 END) AS tech_count,
        SUM(CASE WHEN re.activity_type = 'Clerical' AND re.is_archived = 0 THEN 1 ELSE 0 END) AS clerical_count
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN companies c ON s.company_id = c.id
    LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
    LEFT JOIN users u_sup ON sup.user_id = u_sup.id
    LEFT JOIN reports r ON r.student_id = s.id
    LEFT JOIN report_entities re ON re.report_id = r.id
    {$whereSQL}
    GROUP BY s.id
    ORDER BY s.section ASC, SUBSTRING_INDEX(u.name, ' ', -1) ASC, u.name ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rawRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

$studentsData = [];
foreach ($rawRows as $row) {
    $tCount = (int)($row['tech_count'] ?? 0);
    $cCount = (int)($row['clerical_count'] ?? 0);
    $total  = $tCount + $cCount;

    $row['it_pct']       = $total > 0 ? round(($tCount / $total) * 100, 1) : 0.0;
    $row['clerical_pct'] = $total > 0 ? round(($cCount / $total) * 100, 1) : 0.0;
    $studentsData[]      = $row;
}

// CSV Export
if ($format === 'csv') {
    $filenameSuffix = ($selectedSection !== 'all') ? 'Section_' . $selectedSection : 'All_Sections';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=OJT_Performance_' . $filenameSuffix . '_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, ['Section', 'Student Name', 'Student ID', 'Host Company', 'Approved Reports', 'IT Percentage (%)', 'Clerical Percentage (%)']);

    foreach ($studentsData as $st) {
        fputcsv($output, [
            'Section ' . $st['section'],
            $st['student_name'],
            $st['student_number'],
            $st['company_name'],
            $st['approved_reports_count'] . ' Approved',
            $st['it_pct'] . '%',
            $st['clerical_pct'] . '%'
        ]);
    }
    fclose($output);
    exit();
}

// Print / PDF View
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
        table { width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 20px; }
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
            <h1>OJT Student Performance Summary <?= ($selectedSection !== 'all') ? '— Section ' . htmlspecialchars($selectedSection) : '— All Sections'; ?></h1>
            <p>NBSC &bull; Institute for Computer Studies &bull; Total Students: <?= count($studentsData); ?></p>
        </div>
        <div style="text-align: right;">
            <p><strong>Generated:</strong> <?= date('F d, Y g:i A'); ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Student Intern</th>
                <th>Host Partner &amp; Supervisor</th>
                <th>Approved Reports</th>
                <th>IT vs Clerical Ratio</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($studentsData)): ?>
                <tr><td colspan="5" style="text-align: center; padding: 20px;">No student records found.</td></tr>
            <?php else: ?>
                <?php foreach ($studentsData as $st): ?>
                    <tr>
                        <td><strong>Sec <?= htmlspecialchars($st['section']); ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($st['student_name']); ?></strong><br>
                            <span style="color: #64748b; font-size: 10px;">ID: <?= htmlspecialchars($st['student_number']); ?> &bull; <?= htmlspecialchars($st['program']); ?></span>
                        </td>
                        <td>
                            <?= htmlspecialchars($st['company_name']); ?><br>
                            <span style="color: #64748b; font-size: 10px;">Supervisor: <?= htmlspecialchars($st['supervisor_name']); ?></span>
                        </td>
                        <td><strong><?= (int)$st['approved_reports_count']; ?> Approved</strong></td>
                        <td>
                            <strong>IT: <?= $st['it_pct']; ?>%</strong> &bull; Clerical: <?= $st['clerical_pct']; ?>%
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>