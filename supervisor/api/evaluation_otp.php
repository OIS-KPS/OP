<?php
// supervisor/api/evaluation_otp.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/evaluation_criteria.php';
require_once __DIR__ . '/../../src/services/MailerService.php';

use services\MailerService;

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit();
}

$supervisorUserId = (int)$_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? '';

// Fetch Supervisor DB ID
$stmtSup = $pdo->prepare("SELECT id, user_id FROM supervisors WHERE user_id = ? LIMIT 1");
$stmtSup->execute([$supervisorUserId]);
$supervisor = $stmtSup->fetch(PDO::FETCH_ASSOC);

if (!$supervisor) {
    echo json_encode(['success' => false, 'error' => 'Supervisor profile not found.']);
    exit();
}
$supervisorId = (int)$supervisor['id'];

// -------------------------------------------------------------
// 1. ACTION: REQUEST OTP
// -------------------------------------------------------------
if ($action === 'request_otp') {
    $studentId = (int)($input['student_id'] ?? 0);

    if ($studentId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid student selected.']);
        exit();
    }

    // Verify student is assigned to this supervisor
    $stmt = $pdo->prepare("
        SELECT s.id AS student_id, s.evaluation_triggered, u_std.name AS student_name, u_sup.email AS supervisor_email, u_sup.name AS supervisor_name
        FROM students s
        JOIN users u_std ON s.user_id = u_std.id
        JOIN supervisors sup ON s.supervisor_id = sup.id
        JOIN users u_sup ON sup.user_id = u_sup.id
        WHERE s.id = ? AND sup.id = ?
        LIMIT 1
    ");
    $stmt->execute([$studentId, $supervisorId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Student not assigned to your supervision.']);
        exit();
    }

    if (empty($data['evaluation_triggered'])) {
        echo json_encode(['success' => false, 'error' => 'Final evaluation not yet authorized by the OJT Coordinator.']);
        exit();
    }

    // Generate 6-digit OTP
    $otpCode   = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $otpHash   = password_hash($otpCode, PASSWORD_DEFAULT);
    $expiresAt = date('Y-m-d H:i:s', time() + 300); // 5 mins

    // Clear previous unused OTPs
    $pdo->prepare("DELETE FROM evaluation_otps WHERE student_id = ? AND supervisor_user_id = ?")->execute([$studentId, $supervisorUserId]);

    // Save new OTP
    $ins = $pdo->prepare("INSERT INTO evaluation_otps (student_id, supervisor_user_id, otp_hash, expires_at) VALUES (?, ?, ?, ?)");
    $ins->execute([$studentId, $supervisorUserId, $otpHash, $expiresAt]);

    // Send email
    $mailer = new MailerService();
    $sent = $mailer->sendEvaluationOtpEmail($data['supervisor_email'], $data['supervisor_name'], $otpCode, $data['student_name']);

    // Mask supervisor email
    $emailParts = explode('@', $data['supervisor_email']);
    $userPart = $emailParts[0];
    $maskedUser = (strlen($userPart) > 2) ? substr($userPart, 0, 2) . str_repeat('*', strlen($userPart) - 2) : $userPart . '*';
    $maskedEmail = $maskedUser . '@' . ($emailParts[1] ?? 'nbsc.edu.ph');

    if ($sent) {
        echo json_encode(['success' => true, 'email' => $maskedEmail]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Could not dispatch OTP email. Check mail server settings.']);
    }
    exit();
}

// -------------------------------------------------------------
// 2. ACTION: VERIFY OTP & SAVE FINAL EVALUATION
// -------------------------------------------------------------
if ($action === 'verify_and_submit_evaluation') {
    $studentId   = (int)($input['student_id'] ?? 0);
    $otpEntered  = trim($input['otp'] ?? '');
    $feedback    = trim($input['feedback'] ?? '');

    if ($studentId <= 0 || strlen($otpEntered) !== 6) {
        echo json_encode(['success' => false, 'error' => 'Please provide a valid 6-digit OTP code.']);
        exit();
    }

    // Validate every competency on the OJT form is rated 1-5 before anything is stored.
    $submitted = $input['criteria_ratings'] ?? null;
    if (!is_array($submitted)) {
        echo json_encode(['success' => false, 'error' => 'No competency ratings were received. Please rate all 12 items.']);
        exit();
    }

    $bounds = ojtRatingBounds();
    $ratings = [];
    foreach (ojtEvaluationCriteria() as $criterion) {
        $raw = $submitted[$criterion['key']] ?? null;
        if ($raw === null || !is_numeric($raw)) {
            echo json_encode(['success' => false, 'error' => 'Please rate all 12 competencies before submitting.']);
            exit();
        }
        $value = (int) round((float) $raw);
        if ($value < $bounds['min'] || $value > $bounds['max']) {
            echo json_encode(['success' => false, 'error' => 'Invalid rating received. Each competency must be rated ' . $bounds['min'] . ' to ' . $bounds['max'] . '.']);
            exit();
        }
        $ratings[$criterion['key']] = $value;
    }

    // Ensure the coordinator has authorized this evaluation
    $stmtAuth = $pdo->prepare("SELECT evaluation_triggered FROM students WHERE id = ? LIMIT 1");
    $stmtAuth->execute([$studentId]);
    $authRow = $stmtAuth->fetch(PDO::FETCH_ASSOC);

    if (!$authRow || empty($authRow['evaluation_triggered'])) {
        echo json_encode(['success' => false, 'error' => 'Final evaluation not yet authorized by the OJT Coordinator.']);
        exit();
    }

    // Verify OTP Record
    $stmt = $pdo->prepare("
        SELECT id, otp_hash, expires_at 
        FROM evaluation_otps 
        WHERE student_id = ? AND supervisor_user_id = ? 
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$studentId, $supervisorUserId]);
    $otpRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$otpRow) {
        echo json_encode(['success' => false, 'error' => 'No active OTP verification code found. Please request a new code.']);
        exit();
    }

    if (strtotime($otpRow['expires_at']) < time()) {
        echo json_encode(['success' => false, 'error' => 'Verification code expired. Please request a new code.']);
        exit();
    }

    if (!password_verify($otpEntered, $otpRow['otp_hash'])) {
        echo json_encode(['success' => false, 'error' => 'Incorrect verification code. Please check your email.']);
        exit();
    }

    // Scores stay on the docx's raw 1-5 scale: the overall rating is the simple
    // average of all 12 competencies, and each group column is its own average.
    $finalScore  = ojtOverallRating($ratings);
    $groupScores = ojtGroupAverages($ratings);
    $columnMap   = ojtGroupColumnMap();
    $gradeLabel  = ojtRatingLabel($finalScore);
    $criteriaJson = json_encode($ratings);

    // Capture Client IP Address
    $clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (str_contains($clientIp, ',')) {
        $clientIp = trim(explode(',', $clientIp)[0]);
    }

    // Save into evaluations table
    try {
        $pdo->beginTransaction();

        $stmtSave = $pdo->prepare("
            INSERT INTO evaluations
            (student_id, supervisor_id, technical_score, work_ethics_score, communication_score, punctuality_score, criteria_ratings, final_score, grade_equivalent, feedback, otp_verified, otp_signed_at, otp_ip_address)
            VALUES (?, ?, ?, ?, ?, 0.00, ?, ?, ?, ?, 1, NOW(), ?)
            ON DUPLICATE KEY UPDATE
                technical_score = VALUES(technical_score),
                work_ethics_score = VALUES(work_ethics_score),
                communication_score = VALUES(communication_score),
                punctuality_score = 0.00,
                criteria_ratings = VALUES(criteria_ratings),
                final_score = VALUES(final_score),
                grade_equivalent = VALUES(grade_equivalent),
                feedback = VALUES(feedback),
                otp_verified = 1,
                otp_signed_at = NOW(),
                otp_ip_address = VALUES(otp_ip_address)
        ");
        $stmtSave->execute([
            $studentId,
            $supervisorId,
            $groupScores['technical'] ?? 0,
            $groupScores['ethics'] ?? 0,
            $groupScores['communication'] ?? 0,
            $criteriaJson,
            $finalScore,
            $gradeLabel,
            $feedback,
            $clientIp
        ]);

        // Cleanup used OTP
        $pdo->prepare("DELETE FROM evaluation_otps WHERE student_id = ?")->execute([$studentId]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid action.']);