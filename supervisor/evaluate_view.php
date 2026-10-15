<?php
// supervisor/evaluate_view.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/evaluation_criteria.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId        = (int)$_SESSION['user_id'];
$evaluation_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$student_id    = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;

// Resolve this supervisor's own supervisors.id. Every sibling controller
// (interns.php, evaluate_interns.php, evaluate_form.php, view_report.php)
// scopes its queries on this value. Without it ?id= would happily read any
// evaluation row in the table, not just the ones this supervisor signed.
$stmtSup = $pdo->prepare("SELECT id FROM supervisors WHERE user_id = ? LIMIT 1");
$stmtSup->execute([$userId]);
$supervisorId = (int)($stmtSup->fetchColumn() ?: 0);

if ($supervisorId <= 0) {
    $_SESSION['review_message'] = "Supervisor profile not found. Please contact the administrator.";
    header("Location: evaluate_interns.php");
    exit();
}

$evaluation = null;

try {
    // Exact schema query for evaluations, students, users, supervisors, and companies
    $sql = "
        SELECT 
            e.id AS evaluation_id,
            e.student_id,
            e.supervisor_id,
            e.technical_score,
            e.work_ethics_score,
            e.communication_score,
            e.punctuality_score,
            e.criteria_ratings,
            e.criteria_snapshot,
            e.final_score,
            e.grade_equivalent,
            e.feedback AS remarks,
            e.otp_verified,
            e.otp_signed_at,
            e.otp_ip_address,
            e.created_at AS evaluated_at,
            
            -- Student Information
            u_std.name AS student_name,
            u_std.email AS student_email,
            u_std.avatar_url AS student_avatar,
            s.student_number,
            s.program,
            
            -- Supervisor & Company Information
            u_sup.name AS supervisor_name,
            u_sup.email AS supervisor_email,
            c.name AS company_name,
            c.department AS company_department

        FROM evaluations e
        JOIN students s ON e.student_id = s.id
        JOIN users u_std ON s.user_id = u_std.id
        JOIN supervisors sup ON e.supervisor_id = sup.id
        JOIN users u_sup ON sup.user_id = u_sup.id
        LEFT JOIN companies c ON sup.company_id = c.id
        WHERE " . ($evaluation_id ? "e.id = ?" : "e.student_id = ?") . "
          AND e.supervisor_id = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$evaluation_id ?: $student_id, $supervisorId]);
    $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Database Error in evaluate_view.php: " . $e->getMessage());
}

if (!$evaluation) {
    $_SESSION['review_message'] = "Evaluation report not found.";
    header("Location: evaluate_interns.php");
    exit();
}

require_once __DIR__ . '/../src/pages/supervisor/evaluateViewPage.php';