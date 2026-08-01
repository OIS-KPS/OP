<?php
// coordinator/users.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Active Tab: 'students', 'supervisors', or 'companies'
$tab = $_GET['tab'] ?? 'students';

// Message handling
$success = $_SESSION['success_msg'] ?? '';
$error   = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Handle Account Creation POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_student') {
        $name           = trim($_POST['name'] ?? '');
        $student_number = trim($_POST['student_number'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $program        = trim($_POST['program'] ?? 'BSIT');

        if ($name && $student_number && $email) {
            try {
                $stmt = $pdo->prepare("INSERT INTO students (name, student_number, email, program, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $student_number, $email, $program]);
                $_SESSION['success_msg'] = "Student account for {$name} created successfully!";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Failed to create student account or ID already exists.";
            }
        }
        header("Location: users.php?tab=students");
        exit();
    }

    if ($action === 'create_supervisor') {
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $company_id = intval($_POST['company_id'] ?? 0);

        if ($name && $email && $company_id) {
            try {
                $stmt = $pdo->prepare("INSERT INTO supervisors (name, email, company_id, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $company_id]);
                $_SESSION['success_msg'] = "Supervisor account for {$name} created successfully!";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Failed to create supervisor account.";
            }
        }
        header("Location: users.php?tab=supervisors");
        exit();
    }
}

// Fetch Data for Display (With Dev Fallbacks)
try {
    // 1. Students
    $stmtStudents = $pdo->query("SELECT s.*, c.name AS company_name, sup.name AS supervisor_name FROM students s LEFT JOIN companies c ON s.company_id = c.id LEFT JOIN supervisors sup ON s.supervisor_id = sup.id ORDER BY s.id DESC");
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 2. Supervisors
    $stmtSup = $pdo->query("SELECT sup.*, c.name AS company_name, COUNT(s.id) AS assigned_interns FROM supervisors sup LEFT JOIN companies c ON sup.company_id = c.id LEFT JOIN students s ON s.supervisor_id = sup.id GROUP BY sup.id ORDER BY sup.id DESC");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // 3. Companies
    $stmtComp = $pdo->query("SELECT c.*, COUNT(s.id) AS total_interns FROM companies c LEFT JOIN students s ON s.company_id = c.id GROUP BY c.id ORDER BY c.id DESC");
    $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    $students = [];
    $supervisors = [];
    $companies = [];
}

// Dev Sample Fallback Data
if (empty($students)) {
    $students = [
        ['id' => 1, 'name' => 'Katelyn Coming', 'student_number' => '20231053', 'program' => 'BSIT', 'email' => '20231053@nbsc.edu.ph', 'company_name' => 'ICS IT Dept', 'supervisor_name' => 'John Doe'],
        ['id' => 2, 'name' => 'Pauline May Coming', 'student_number' => '20231054', 'program' => 'BSIT', 'email' => '20231054@nbsc.edu.ph', 'company_name' => 'LGU Manolo Fortich', 'supervisor_name' => 'Jane Smith'],
        ['id' => 3, 'name' => 'Sander Perejan', 'student_number' => '20231055', 'program' => 'BSIT', 'email' => '20231055@nbsc.edu.ph', 'company_name' => 'Unassigned', 'supervisor_name' => 'Unassigned']
    ];
}

if (empty($supervisors)) {
    $supervisors = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@company.com', 'company_name' => 'ICS IT Dept', 'assigned_interns' => 2],
        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'janesmith@lgu.gov.ph', 'company_name' => 'LGU Manolo Fortich', 'assigned_interns' => 1]
    ];
}

if (empty($companies)) {
    $companies = [
        ['id' => 1, 'name' => 'ICS IT Dept', 'department' => 'Software & Network Lab', 'total_interns' => 2],
        ['id' => 2, 'name' => 'LGU Manolo Fortich', 'department' => 'Management Information Systems (MIS)', 'total_interns' => 1]
    ];
}

require_once __DIR__ . '/../src/pages/coordinator/usersPage.php';