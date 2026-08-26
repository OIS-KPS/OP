<?php
// supervisor/evaluate_form.php
session_start();

require_once __DIR__ . '/../config/db.php';

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
$stmtSup = $pdo->prepare("SELECT id FROM supervisors WHERE user_id = ? LIMIT 1");
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
        u.name,
        u.email,
        u.avatar_url
    FROM students s
    JOIN users u ON s.user_id = u.id
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

// Render Evaluation Form View
require_once __DIR__ . '/../src/pages/supervisor/evaluateFormPage.php';