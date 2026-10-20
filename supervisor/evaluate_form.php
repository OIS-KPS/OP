<?php
// supervisor/evaluate_form.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/evaluation_criteria.php';

// 1. Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;

if (!$student_id) {
    header("Location: evaluate_interns.php");
    exit();
}

// 2. Fetch Supervisor Record
$stmtSup = $pdo->prepare("
    SELECT sup.id, u.name, u.email, c.name AS company_name, c.department AS company_department
    FROM supervisors sup
    JOIN users u ON sup.user_id = u.id
    LEFT JOIN companies c ON sup.company_id = c.id
    WHERE sup.user_id = ?
    LIMIT 1
");
$stmtSup->execute([$userId]);
$supervisor = $stmtSup->fetch(PDO::FETCH_ASSOC);

if (!$supervisor) {
    die("Supervisor record not found.");
}
$supervisorId = (int)$supervisor['id'];

// 3. Fetch Student Details assigned to this supervisor
$stmt = $pdo->prepare("
    SELECT 
        s.id,
        s.student_number,
        s.program,
        s.section,
        s.evaluation_triggered,
        u.name,
        u.email,
        u.avatar_url,
        c.name AS company_name
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN companies c ON s.company_id = c.id
    WHERE s.id = ? AND s.supervisor_id = ?
    LIMIT 1
");
$stmt->execute([$student_id, $supervisorId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    $_SESSION['review_message'] = "Student not found or not assigned to you.";
    header("Location: evaluate_interns.php");
    exit();
}

// 4. Inclusive dates of training, derived from the approved WAR window
$stmtDates = $pdo->prepare("
    SELECT
        MIN(COALESCE(approved_at, submitted_at)) AS training_start,
        MAX(COALESCE(approved_at, submitted_at)) AS training_end
    FROM reports
    WHERE student_id = ? AND status = 'approved'
");
$stmtDates->execute([$student_id]);
$trainingWindow = $stmtDates->fetch(PDO::FETCH_ASSOC) ?: [];

$trainingStart = !empty($trainingWindow['training_start']) ? strtotime($trainingWindow['training_start']) : null;
$trainingEnd   = !empty($trainingWindow['training_end']) ? strtotime($trainingWindow['training_end']) : null;

if ($trainingStart && $trainingEnd) {
    $trainingPeriod = date('F d, Y', $trainingStart) . ' - ' . date('F d, Y', $trainingEnd);
} elseif ($trainingStart) {
    $trainingPeriod = date('F d, Y', $trainingStart) . ' - Present';
} else {
    $trainingPeriod = 'No approved reports yet';
}

// 5. Verify Coordinator Triggered the Evaluation Request
if (empty($student['evaluation_triggered'])) {
    $_SESSION['review_message'] = "Final evaluation has not been authorized by the OJT Coordinator yet.";
    header("Location: evaluate_interns.php");
    exit();
}

// Render Evaluation Form View
require_once __DIR__ . '/../src/pages/supervisor/evaluateFormPage.php';