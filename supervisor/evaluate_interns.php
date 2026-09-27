<?php
// supervisor/evaluate_interns.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/evaluation_criteria.php';

// -------------------------------------------------------------
// 1. Authorization Guard
// -------------------------------------------------------------
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

$supervisor = [
    'name' => $_SESSION['user_name'] ?? 'Supervisor',
    'company_name' => 'Host Company'
];
$students = [];
$activeEval = null;
$previewId = isset($_GET['preview']) ? intval($_GET['preview']) : 0;

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
        LIMIT 1
    ");
    $stmtSup->execute([$userId]);
    $supData = $stmtSup->fetch(PDO::FETCH_ASSOC);

    if (!$supData) {
        die("Supervisor profile record not found. Please contact administrator.");
    }

    $supervisorId = (int)$supData['supervisor_id'];
    $_SESSION['supervisor_id'] = $supervisorId;

    $supervisor['name'] = $supData['supervisor_name'];
    if (!empty($supData['company_name'])) {
        $supervisor['company_name'] = $supData['company_name'];
    }

    // -------------------------------------------------------------
    // 3. Fetch Students & Report Progress (Aligned with Database Schema)
    // -------------------------------------------------------------
    $studentsSql = "
        SELECT 
            s.id,
            s.student_number,
            s.program,
            s.evaluation_triggered,
            u.name,
            u.email,
            u.avatar_url,
            COUNT(DISTINCT r.id) AS submitted_wars,
            SUM(CASE WHEN LOWER(r.status) = 'approved' THEN 1 ELSE 0 END) AS approved_wars,
            e.id AS evaluation_id,
            e.final_score,
            e.grade_equivalent,
            e.otp_verified,
            e.otp_signed_at,
            CASE 
                WHEN e.id IS NOT NULL AND e.otp_verified = 1 THEN 'completed'
                ELSE 'pending'
            END AS evaluation_status
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN reports r ON s.id = r.student_id
        LEFT JOIN evaluations e ON s.id = e.student_id AND e.supervisor_id = s.supervisor_id
        WHERE s.supervisor_id = ?
        GROUP BY 
            s.id, 
            s.student_number, 
            s.program, 
            s.evaluation_triggered,
            u.name, 
            u.email, 
            u.avatar_url, 
            e.id, 
            e.final_score,
            e.grade_equivalent,
            e.otp_verified,
            e.otp_signed_at
        ORDER BY u.name ASC
    ";

    $stmtStudents = $pdo->prepare($studentsSql);
    $stmtStudents->execute([$supervisorId]);
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // -------------------------------------------------------------
    // 4. Load a single signed evaluation for the scorecard preview modal.
    //    Scoped to this supervisor so the id cannot be used to read
    //    another supervisor's intern.
    // -------------------------------------------------------------
    if ($previewId > 0) {
        $stmtPreview = $pdo->prepare("
            SELECT
                e.id AS evaluation_id,
                e.criteria_ratings,
                e.technical_score,
                e.work_ethics_score,
                e.communication_score,
                e.final_score,
                e.grade_equivalent,
                e.feedback,
                e.otp_verified,
                e.otp_signed_at,
                u_std.name AS student_name,
                u_std.avatar_url AS student_avatar,
                s.student_number,
                s.program,
                s.section
            FROM evaluations e
            JOIN students s ON e.student_id = s.id
            JOIN users u_std ON s.user_id = u_std.id
            WHERE e.id = ? AND s.supervisor_id = ?
            LIMIT 1
        ");
        $stmtPreview->execute([$previewId, $supervisorId]);
        $activeEval = $stmtPreview->fetch(PDO::FETCH_ASSOC) ?: null;
    }

} catch (Exception $e) {
    error_log("Database Error in supervisor/evaluate_interns.php: " . $e->getMessage());
}

// Render View Template
require_once __DIR__ . '/../src/pages/supervisor/evaluateInternsPage.php';