<?php
// supervisor/interns.php
session_start();

require_once __DIR__ . '/../config/db.php';

// -------------------------------------------------------------
// 1. Authorization Guard
// -------------------------------------------------------------
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Capture 'id' parameter from query string (?id=1) for single intern portfolio view
$selected_student_id = isset($_GET['id']) && !empty($_GET['id']) ? intval($_GET['id']) : null;

try {
    // -------------------------------------------------------------
    // 2. Fetch Supervisor & Host Company Profile
    // -------------------------------------------------------------
    $stmtSup = $pdo->prepare("
        SELECT 
            s.id AS supervisor_id,
            u.name AS supervisor_name,
            c.name AS company_name
        FROM supervisors s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        WHERE s.user_id = ?
    ");
    $stmtSup->execute([$userId]);
    $supData = $stmtSup->fetch(PDO::FETCH_ASSOC);

    if (!$supData) {
        die("Supervisor profile record not found. Please contact the administrator.");
    }

    $supervisor_id = $supData['supervisor_id'];
    $_SESSION['supervisor_id'] = $supervisor_id;

    $supervisor = [
        'name' => $supData['supervisor_name'],
        'company_name' => !empty($supData['company_name']) ? $supData['company_name'] : 'Host Company'
    ];

    // =========================================================
    // CONDITION 1: SINGLE INTERN PORTFOLIO VIEW (?id=X)
    // =========================================================
    if ($selected_student_id) {
        // Fetch selected student details joining users table (3NF compliant)
        $studentStmt = $pdo->prepare("
            SELECT 
                s.id,
                s.student_number,
                s.program,
                u.name,
                u.email,
                u.avatar_url
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.id = ? AND s.supervisor_id = ?
        ");
        $studentStmt->execute([$selected_student_id, $supervisor_id]);
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

        // Security Guard: Ensure student exists and is assigned to THIS supervisor
        if (!$student) {
            header("Location: interns.php");
            exit();
        }

        // Fetch accomplishment reports submitted by this specific student
        $reportsStmt = $pdo->prepare("
            SELECT id, week_number, file_path, ocr_activities, status, submitted_at 
            FROM reports 
            WHERE student_id = ? 
            ORDER BY week_number ASC
        ");
        $reportsStmt->execute([$selected_student_id]);
        $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Load Single Intern Portfolio View
        require_once __DIR__ . '/../src/pages/supervisor/internProfilePage.php';
        exit();
    }

    // =========================================================
    // CONDITION 2: DEFAULT INTERNS ROSTER LIST (NO ?id= PARAMETER)
    // =========================================================
    $internsSql = "
        SELECT 
            s.id,
            s.student_number,
            s.program,
            u.name,
            u.email,
            u.avatar_url,
            COUNT(r.id) AS submitted_reports
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN reports r ON s.id = r.student_id
        WHERE s.supervisor_id = ?
        GROUP BY s.id, s.student_number, s.program, u.name, u.email, u.avatar_url
        ORDER BY u.name ASC
    ";
    
    $internsStmt = $pdo->prepare($internsSql);
    $internsStmt->execute([$supervisor_id]);
    $interns = $internsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Load All Interns List View
    require_once __DIR__ . '/../src/pages/supervisor/internsPage.php';

} catch (Exception $e) {
    error_log("Database Error in supervisor/interns.php: " . $e->getMessage());
    $supervisor = ['name' => $_SESSION['user_name'] ?? 'Supervisor', 'company_name' => 'Host Company'];
    $interns = [];
    require_once __DIR__ . '/../src/pages/supervisor/internsPage.php';
}