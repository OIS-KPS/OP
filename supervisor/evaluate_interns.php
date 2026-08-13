<?php
// supervisor/evaluate_interns.php
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

$supervisor = [
    'name' => $_SESSION['user_name'] ?? 'Supervisor',
    'company_name' => 'Host Company'
];
$students = [];

try {
    // -------------------------------------------------------------
    // 2. Fetch Supervisor Profile & Host Company
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
        die("Supervisor profile record not found. Please contact administrator.");
    }

    $supervisorId = $supData['supervisor_id'];
    $_SESSION['supervisor_id'] = $supervisorId;

    $supervisor['name'] = $supData['supervisor_name'];
    if (!empty($supData['company_name'])) {
        $supervisor['company_name'] = $supData['company_name'];
    }

    // -------------------------------------------------------------
    // 3. Fetch Students & WAR Progress (3NF Relational Query)
    // -------------------------------------------------------------
    $studentsSql = "
        SELECT 
            s.id,
            s.student_number,
            s.program,
            u.name,
            u.email,
            u.avatar_url,
            COUNT(DISTINCT r.id) AS submitted_wars,
            SUM(CASE WHEN LOWER(r.status) = 'approved' THEN 1 ELSE 0 END) AS approved_wars,
            e.id AS evaluation_id,
            e.final_score,
            e.status AS evaluation_status
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN reports r ON s.id = r.student_id
        LEFT JOIN evaluations e ON s.id = e.student_id
        WHERE s.supervisor_id = ?
        GROUP BY 
            s.id, 
            s.student_number, 
            s.program, 
            u.name, 
            u.email, 
            u.avatar_url, 
            e.id, 
            e.final_score, 
            e.status
        ORDER BY u.name ASC
    ";

    $stmtStudents = $pdo->prepare($studentsSql);
    $stmtStudents->execute([$supervisorId]);
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    error_log("Database Error in supervisor/evaluate_interns.php: " . $e->getMessage());
}

// Render View Template
require_once __DIR__ . '/../src/pages/supervisor/evaluateInternsPage.php';