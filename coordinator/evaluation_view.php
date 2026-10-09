<?php
// coordinator/evaluation_view.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/evaluation_criteria.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$evaluation_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$evaluation_id) {
    $_SESSION['review_message'] = "Evaluation report not found.";
    header("Location: evaluations.php");
    exit();
}

$evaluation = null;

try {
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
            c.department AS company_department,
            o.name AS office_name

        FROM evaluations e
        JOIN students s ON e.student_id = s.id
        JOIN users u_std ON s.user_id = u_std.id
        JOIN supervisors sup ON e.supervisor_id = sup.id
        JOIN users u_sup ON sup.user_id = u_sup.id
        LEFT JOIN companies c ON sup.company_id = c.id
        LEFT JOIN offices o ON sup.office_id = o.id
        WHERE e.id = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$evaluation_id]);
    $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Database Error in coordinator/evaluation_view.php: " . $e->getMessage());
}

if (!$evaluation) {
    $_SESSION['review_message'] = "Evaluation report not found.";
    header("Location: evaluations.php");
    exit();
}

// Render Evaluation View
require_once __DIR__ . '/../src/pages/coordinator/evaluationViewPage.php';
