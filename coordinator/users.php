<?php
// coordinator/users.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/MailerService.php';

use services\MailerService;

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$tab     = $_GET['tab'] ?? 'students';
$success = $_SESSION['flash_success'] ?? null;
$error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Initialize Mailer Service
$mailer = new MailerService();

// 2. Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Action A: Create Student Account & Send Invite
    if ($action === 'create_student') {
        $name          = trim($_POST['name'] ?? '');
        $studentNumber = trim($_POST['student_number'] ?? '');
        $email         = strtolower(trim($_POST['email'] ?? ''));
        $program       = trim($_POST['program'] ?? 'BSIT');

        if (!empty($name) && !empty($studentNumber) && !empty($email)) {
            try {
                $pdo->beginTransaction();

                // Check if user already exists
                $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $chk->execute([$email]);
                $user = $chk->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $userId = $user['id'];
                } else {
                    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, role, created_at) VALUES (?, ?, 'student', NOW())");
                    $stmtUser->execute([$name, $email]);
                    $userId = $pdo->lastInsertId();
                }

                // Insert or update student profile
                $stmtStudent = $pdo->prepare("
                    INSERT INTO students (user_id, student_number, program) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE student_number = VALUES(student_number), program = VALUES(program)
                ");
                $stmtStudent->execute([$userId, $studentNumber, $program]);

                $pdo->commit();

                // 📧 Dispatch Welcome Email
                $mailSent = $mailer->sendWelcomeEmail($email, $name, 'student');

                $_SESSION['flash_success'] = "Student {$name} added successfully!" . ($mailSent ? " Invitation email dispatched." : " (Email notification could not be sent).");
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['flash_error'] = "Failed to add student: " . $e->getMessage();
            }
        } else {
            $_SESSION['flash_error'] = "All student fields are required.";
        }
        header("Location: users.php?tab=students");
        exit();
    }

    // Action B: Create Supervisor Account & Send Invite
    if ($action === 'create_supervisor') {
        $name      = trim($_POST['name'] ?? '');
        $email     = strtolower(trim($_POST['email'] ?? ''));
        $companyId = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;

        if (!empty($name) && !empty($email)) {
            try {
                $pdo->beginTransaction();

                $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $chk->execute([$email]);
                $user = $chk->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $userId = $user['id'];
                } else {
                    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, role, created_at) VALUES (?, ?, 'supervisor', NOW())");
                    $stmtUser->execute([$name, $email]);
                    $userId = $pdo->lastInsertId();
                }

                $stmtSup = $pdo->prepare("
                    INSERT INTO supervisors (user_id, company_id) 
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE company_id = VALUES(company_id)
                ");
                $stmtSup->execute([$userId, $companyId]);

                $pdo->commit();

                // 📧 Dispatch Welcome Email
                $mailSent = $mailer->sendWelcomeEmail($email, $name, 'supervisor');

                $_SESSION['flash_success'] = "Supervisor {$name} added successfully!" . ($mailSent ? " Invitation email dispatched." : " (Email notification could not be sent).");
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['flash_error'] = "Failed to add supervisor: " . $e->getMessage();
            }
        } else {
            $_SESSION['flash_error'] = "Name and email are required.";
        }
        header("Location: users.php?tab=supervisors");
        exit();
    }

    // Action C: Create Partner Company
    if ($action === 'create_company') {
        $name       = trim($_POST['name'] ?? '');
        $department = trim($_POST['department'] ?? 'Main Office');

        if (!empty($name)) {
            try {
                $stmtComp = $pdo->prepare("INSERT INTO companies (name, department) VALUES (?, ?)");
                $stmtComp->execute([$name, $department]);
                $_SESSION['flash_success'] = "Partner company '{$name}' created successfully!";
            } catch (Exception $e) {
                $_SESSION['flash_error'] = "Failed to create company: " . $e->getMessage();
            }
        } else {
            $_SESSION['flash_error'] = "Company name is required.";
        }
        header("Location: users.php?tab=companies");
        exit();
    }

    // Action D: Bulk Import Students & Send Invites
    if ($action === 'bulk_import_students' && isset($_FILES['excel_file'])) {
        $file = $_FILES['excel_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $handle = fopen($file['tmp_name'], "r");
            $importedCount = 0;
            
            if ($handle !== FALSE) {
                $pdo->beginTransaction();
                try {
                    $rowNumber = 0;
                    $studentsToNotify = [];

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $rowNumber++;
                        if ($rowNumber === 1 && (stripos($data[0], 'name') !== false || stripos($data[1], 'id') !== false)) {
                            continue;
                        }

                        $stdName   = trim($data[0] ?? '');
                        $stdNumber = trim($data[1] ?? '');
                        $stdEmail  = strtolower(trim($data[2] ?? ''));
                        $stdProg   = trim($data[3] ?? 'BSIT');

                        if (!empty($stdName) && !empty($stdNumber) && !empty($stdEmail)) {
                            $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                            $chk->execute([$stdEmail]);
                            $u = $chk->fetch(PDO::FETCH_ASSOC);

                            if ($u) {
                                $uid = $u['id'];
                            } else {
                                $insU = $pdo->prepare("INSERT INTO users (name, email, role, created_at) VALUES (?, ?, 'student', NOW())");
                                $insU->execute([$stdName, $stdEmail]);
                                $uid = $pdo->lastInsertId();
                            }

                            $insS = $pdo->prepare("
                                INSERT INTO students (user_id, student_number, program)
                                VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE student_number = VALUES(student_number), program = VALUES(program)
                            ");
                            $insS->execute([$uid, $stdNumber, $stdProg ?: 'BSIT']);
                            $importedCount++;

                            $studentsToNotify[] = ['email' => $stdEmail, 'name' => $stdName];
                        }
                    }
                    fclose($handle);
                    $pdo->commit();

                    // Dispatch batch emails
                    foreach ($studentsToNotify as $recipient) {
                        $mailer->sendWelcomeEmail($recipient['email'], $recipient['name'], 'student');
                    }

                    $_SESSION['flash_success'] = "Successfully imported {$importedCount} student records and sent invitations!";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $_SESSION['flash_error'] = "Import failed on row {$rowNumber}: " . $e->getMessage();
                }
            }
        } else {
            $_SESSION['flash_error'] = "File upload error. Please select a valid CSV file.";
        }
        header("Location: users.php?tab=students");
        exit();
    }
}

// 3. Fetch Dynamic Data for View
$students    = [];
$supervisors = [];
$companies   = [];

try {
    $stmtStd = $pdo->query("
        SELECT 
            s.id AS student_id,
            s.student_number,
            s.program,
            u.name,
            u.email,
            u.avatar_url,
            u_sup.name AS supervisor_name,
            c.name AS company_name
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        LEFT JOIN companies c ON sup.company_id = c.id
        ORDER BY u.name ASC
    ");
    $students = $stmtStd->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $stmtSup = $pdo->query("
        SELECT 
            sup.id,
            u.name,
            u.email,
            u.avatar_url,
            c.name AS company_name,
            COUNT(s.id) AS assigned_interns
        FROM supervisors sup
        JOIN users u ON sup.user_id = u.id
        LEFT JOIN companies c ON sup.company_id = c.id
        LEFT JOIN students s ON sup.id = s.supervisor_id
        GROUP BY sup.id, u.name, u.email, u.avatar_url, c.name
        ORDER BY u.name ASC
    ");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $stmtComp = $pdo->query("
        SELECT 
            c.id,
            c.name,
            c.department,
            COUNT(s.id) AS total_interns
        FROM companies c
        LEFT JOIN supervisors sup ON c.id = sup.company_id
        LEFT JOIN students s ON sup.id = s.supervisor_id
        GROUP BY c.id, c.name, c.department
        ORDER BY c.name ASC
    ");
    $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    error_log("Database Error in coordinator/users.php: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/coordinator/usersPage.php';