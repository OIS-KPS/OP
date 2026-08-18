<?php
// coordinator/view_report.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Report Inspection & Entities";
$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($reportId <= 0) {
    header("Location: approved_reports.php");
    exit();
}

// 1. Handle Coordinator Adding New Entity Manually
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_entity') {
    $entityName      = trim($_POST['entity_name'] ?? '');
    $category        = trim($_POST['category'] ?? 'Software Dev');
    $classification  = trim($_POST['classification'] ?? 'Technical');
    $confidenceScore = isset($_POST['confidence_score']) && is_numeric($_POST['confidence_score']) 
                        ? floatval($_POST['confidence_score']) 
                        : 100.00;

    if (!empty($entityName)) {
        try {
            $stmtInsert = $pdo->prepare("
                INSERT INTO report_entities (report_id, entity_name, category, classification, confidence_score)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmtInsert->execute([$reportId, $entityName, $category, $classification, $confidenceScore]);
            $_SESSION['flash_success'] = "Entity added successfully.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Failed to add entity: " . $e->getMessage();
        }
    }
    header("Location: view_report.php?id=" . $reportId);
    exit();
}

// 2. Handle Deleting Entity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_entity') {
    $entityId = intval($_POST['entity_id'] ?? 0);
    if ($entityId > 0) {
        try {
            $stmtDel = $pdo->prepare("DELETE FROM report_entities WHERE id = ? AND report_id = ?");
            $stmtDel->execute([$entityId, $reportId]);
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
        }
    }
    header("Location: view_report.php?id=" . $reportId);
    exit();
}

$report = null;
$extractedEntities = [];
$itPct = 0;
$clericalPct = 0;
$technicalCount = 0;
$clericalCount = 0;

try {
    // 3. Fetch Report & Student Details
    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.student_id,
            r.week_number,
            r.file_path,
            r.ocr_activities,
            r.status,
            r.submitted_at,
            s.student_number,
            s.program,
            u.name AS student_name,
            u.email AS student_email,
            c.name AS company_name,
            c.department AS company_dept,
            u_sup.name AS supervisor_name
        FROM reports r
        JOIN students s ON r.student_id = s.id
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        WHERE r.id = ?
        LIMIT 1
    ");
    $stmt->execute([$reportId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        header("Location: approved_reports.php");
        exit();
    }

    // 4. Fetch Extracted Entities for this Report
    $stmtEnt = $pdo->prepare("
        SELECT id, entity_name, category, classification, confidence_score, created_at 
        FROM report_entities 
        WHERE report_id = ? 
        ORDER BY id DESC
    ");
    $stmtEnt->execute([$reportId]);
    $extractedEntities = $stmtEnt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 5. Calculate IT vs. Clerical Ratios Dynamically
    foreach ($extractedEntities as $ent) {
        if (strtolower($ent['classification']) === 'technical') {
            $technicalCount++;
        } else {
            $clericalCount++;
        }
    }

    $totalEntities = count($extractedEntities);
    if ($totalEntities > 0) {
        $itPct = round(($technicalCount / $totalEntities) * 100, 1);
        $clericalPct = round(($clericalCount / $totalEntities) * 100, 1);
    }

} catch (PDOException $e) {
    error_log("Error in view_report.php: " . $e->getMessage());
    $extractedEntities = [];
}

require_once __DIR__ . '/../src/pages/coordinator/viewReportPage.php';