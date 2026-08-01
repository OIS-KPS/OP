<?php
// coordinator/assignments.php
session_start();

require_once __DIR__ . '/../config/db.php';

$success = $_SESSION['success_msg'] ?? '';
$error   = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Handle Student Placement / Linkage POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_student'])) {
    $student_id    = intval($_POST['student_id'] ?? 0);
    $company_id    = intval($_POST['company_id'] ?? 0);
    $supervisor_id = intval($_POST['supervisor_id'] ?? 0);

    if ($student_id && $company_id && $supervisor_id) {
        try {
            $stmt = $pdo->prepare("UPDATE students SET company_id = ?, supervisor_id = ? WHERE id = ?");
            $stmt->execute([$company_id, $supervisor_id, $student_id]);
            $_SESSION['success_msg'] = "Student placement successfully assigned!";
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Failed to assign student placement.";
        }
    } else {
        $_SESSION['error_msg'] = "Please select a Student, Host Company, and Supervisor.";
    }

    header("Location: assignments.php");
    exit();
}

// Fetch Unassigned & Assigned Students, Companies, and Supervisors
try {
    // 1. All Students with company and supervisor details
    $stmtStudents = $pdo->query("
        SELECT s.*, c.name AS company_name, c.department AS company_dept, sup.name AS supervisor_name 
        FROM students s 
        LEFT JOIN companies c ON s.company_id = c.id 
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id 
        ORDER BY s.name ASC
    ");
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 2. Companies
    $stmtCompanies = $pdo->query("SELECT * FROM companies ORDER BY name ASC");
    $companies = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 3. Supervisors mapped by company_id for quick JSON lookup
    $stmtSup = $pdo->query("SELECT id, name, company_id FROM supervisors ORDER BY name ASC");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    $students = [];
    $companies = [];
    $supervisors = [];
}

// Dev Fallbacks
if (empty($students)) {
    $students = [
        ['id' => 1, 'name' => 'Katelyn Coming', 'student_number' => '20231053', 'program' => 'BSIT', 'company_id' => 1, 'company_name' => 'ICS IT Dept', 'company_dept' => 'Software Lab', 'supervisor_id' => 1, 'supervisor_name' => 'Engr. John Doe'],
        ['id' => 2, 'name' => 'Pauline May Coming', 'student_number' => '20231054', 'program' => 'BSIT', 'company_id' => 2, 'company_name' => 'LGU Manolo Fortich', 'company_dept' => 'MIS', 'supervisor_id' => 2, 'supervisor_name' => 'Jane Smith'],
        ['id' => 3, 'name' => 'Sander Perejan', 'student_number' => '20231055', 'program' => 'BSIT', 'company_id' => null, 'company_name' => null, 'company_dept' => null, 'supervisor_id' => null, 'supervisor_name' => null]
    ];
}

if (empty($companies)) {
    $companies = [
        ['id' => 1, 'name' => 'ICS IT Dept', 'department' => 'Software & Network Lab'],
        ['id' => 2, 'name' => 'LGU Manolo Fortich', 'department' => 'Management Information Systems (MIS)']
    ];
}

if (empty($supervisors)) {
    $supervisors = [
        ['id' => 1, 'name' => 'Engr. John Doe', 'company_id' => 1],
        ['id' => 2, 'name' => 'Jane Smith', 'company_id' => 2]
    ];
}

require_once __DIR__ . '/../src/pages/coordinator/assignmentsPage.php';