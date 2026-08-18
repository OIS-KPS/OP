<?php
// coordinator/assignments.php
session_start();

require_once __DIR__ . '/../config/db.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$success = $_SESSION['success_msg'] ?? '';
$error   = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// 2. Handle Student Placement Link / Unlink POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_student'])) {
    $student_id    = intval($_POST['student_id'] ?? 0);
    $supervisor_id = !empty($_POST['supervisor_id']) ? intval($_POST['supervisor_id']) : null;
    $action_type   = $_POST['action_type'] ?? 'assign';

    if ($student_id > 0) {
        try {
            if ($action_type === 'unassign') {
                $stmt = $pdo->prepare("UPDATE students SET supervisor_id = NULL WHERE id = ?");
                $stmt->execute([$student_id]);
                $_SESSION['success_msg'] = "Student placement unlinked successfully.";
            } else {
                if ($supervisor_id) {
                    $stmt = $pdo->prepare("UPDATE students SET supervisor_id = ? WHERE id = ?");
                    $stmt->execute([$supervisor_id, $student_id]);
                    $_SESSION['success_msg'] = "Student placement successfully assigned!";
                } else {
                    $_SESSION['error_msg'] = "Please select an Industry Supervisor for placement.";
                }
            }
        } catch (Exception $e) {
            error_log("Assignment Error: " . $e->getMessage());
            $_SESSION['error_msg'] = "Failed to update placement: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = "Invalid student selected.";
    }

    header("Location: assignments.php");
    exit();
}

// 3. Fetch Dynamic Data (3NF Relational Structure)
$students    = [];
$companies   = [];
$supervisors = [];

try {
    // 1. All Students with company and supervisor linkages
    $stmtStudents = $pdo->query("
        SELECT 
            s.id,
            s.student_number,
            s.program,
            s.supervisor_id,
            u.name,
            u.email,
            u.avatar_url,
            sup.company_id,
            u_sup.name AS supervisor_name,
            c.name AS company_name,
            c.department AS company_dept
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        LEFT JOIN companies c ON sup.company_id = c.id
        ORDER BY u.name ASC
    ");
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 2. All Registered Partner Companies
    $stmtCompanies = $pdo->query("
        SELECT id, name, department 
        FROM companies 
        ORDER BY name ASC
    ");
    $companies = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 3. Supervisors mapped with user names and company_ids for JS filtering
    $stmtSup = $pdo->query("
        SELECT 
            sup.id, 
            u.name, 
            sup.company_id,
            c.name AS company_name
        FROM supervisors sup
        JOIN users u ON sup.user_id = u.id
        LEFT JOIN companies c ON sup.company_id = c.id
        ORDER BY u.name ASC
    ");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    error_log("Database Error in coordinator/assignments.php: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/coordinator/assignmentsPage.php';