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

    // 1. Single User Creation Handler
    if ($action === 'create_single_user') {
        $role = $_POST['role'] ?? 'student';

        if ($role === 'student') {
            $name           = trim($_POST['name'] ?? '');
            $student_number = trim($_POST['student_number'] ?? '');
            $email          = trim($_POST['email'] ?? '');
            $program        = 'BSIT'; // Defaulted to BSIT

            if (!empty($name) && !empty($student_number) && !empty($email)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO students (name, student_number, email, program, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$name, $student_number, $email, $program]);
                    $_SESSION['success_msg'] = "Student account for {$name} created successfully!";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $_SESSION['error_msg'] = "Student ID or Email already exists in the system.";
                    } else {
                        $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
                    }
                }
            } else {
                $_SESSION['error_msg'] = "Please fill in all required student fields.";
            }
            header("Location: users.php?tab=students");
            exit();

        } elseif ($role === 'supervisor') {
            $name       = trim($_POST['name'] ?? '');
            $email      = trim($_POST['email'] ?? '');
            $company_id = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;

            if (!empty($name) && !empty($email)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO supervisors (name, email, company_id, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$name, $email, $company_id]);
                    $_SESSION['success_msg'] = "Supervisor account for {$name} created successfully!";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $_SESSION['error_msg'] = "Supervisor Email address already exists.";
                    } else {
                        $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
                    }
                }
            } else {
                $_SESSION['error_msg'] = "Please fill in all required supervisor fields.";
            }
            header("Location: users.php?tab=supervisors");
            exit();
        }
    }
}

// 2. Fetch Real Records from Database
try {
    // Fetch Students
    $stmtStudents = $pdo->query("
        SELECT s.*, 
               c.name AS company_name, 
               sup.name AS supervisor_name 
        FROM students s 
        LEFT JOIN companies c ON s.company_id = c.id 
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id 
        ORDER BY s.id DESC
    ");
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Supervisors
    $stmtSup = $pdo->query("
        SELECT sup.*, 
               c.name AS company_name, 
               COUNT(s.id) AS assigned_interns 
        FROM supervisors sup 
        LEFT JOIN companies c ON sup.company_id = c.id 
        LEFT JOIN students s ON s.supervisor_id = sup.id 
        GROUP BY sup.id 
        ORDER BY sup.id DESC
    ");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Companies
    $stmtComp = $pdo->query("
        SELECT c.*, 
               COUNT(s.id) AS total_interns 
        FROM companies c 
        LEFT JOIN students s ON s.company_id = c.id 
        GROUP BY c.id 
        ORDER BY c.id DESC
    ");
    $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['error_msg'] = "Failed to fetch records: " . $e->getMessage();
    $students = [];
    $supervisors = [];
    $companies = [];
}

require_once __DIR__ . '/../src/pages/coordinator/usersPage.php';