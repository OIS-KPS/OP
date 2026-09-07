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

$coordinatorId   = $_SESSION['user_id'];
$tab             = $_GET['tab'] ?? 'students';
$selectedSection = $_GET['section'] ?? 'all';
$success         = $_SESSION['flash_success'] ?? null;
$error           = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$mailer = new MailerService();

// 2. Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Action A: Create Student Account (with Optional Direct Placement Link)
    if ($action === 'create_student') {
        $name          = trim($_POST['name'] ?? '');
        $studentNumber = trim($_POST['student_number'] ?? '');
        $email         = strtolower(trim($_POST['email'] ?? ''));
        $section       = strtoupper(trim($_POST['section'] ?? 'A'));
        $companyId     = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;
        $supervisorId  = !empty($_POST['supervisor_id']) ? intval($_POST['supervisor_id']) : null;

        if (!empty($name) && !empty($studentNumber) && !empty($email)) {
            try {
                $pdo->beginTransaction();

                $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $chk->execute([$email]);
                $user = $chk->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $userId = $user['id'];
                    $pdo->prepare("UPDATE users SET name = ?, status = 'active', archived_at = NULL WHERE id = ?")->execute([$name, $userId]);
                } else {
                    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'student', 'active', NOW())");
                    $stmtUser->execute([$name, $email]);
                    $userId = $pdo->lastInsertId();
                }

                $stmtStudent = $pdo->prepare("
                    INSERT INTO students (user_id, student_number, program, section, company_id, supervisor_id) 
                    VALUES (?, ?, 'BSIT', ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        student_number = VALUES(student_number), 
                        program = 'BSIT', 
                        section = VALUES(section),
                        company_id = VALUES(company_id),
                        supervisor_id = VALUES(supervisor_id)
                ");
                $stmtStudent->execute([$userId, $studentNumber, $section, $companyId, $supervisorId]);

                logActivity($pdo, $coordinatorId, 'coordinator', 'STUDENT_CREATED', "Added student {$name} ({$studentNumber} - Sec {$section}).");
                $pdo->commit();

                $mailSent = $mailer->sendWelcomeEmail($email, $name, 'student');
                $_SESSION['flash_success'] = "Student {$name} (Section {$section}) added successfully!" . ($mailSent ? " Welcome email sent." : "");
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

    // Action B: Create Unified Partner Company & Supervisor
    if ($action === 'create_company_supervisor') {
        $companyName    = trim($_POST['company_name'] ?? '');
        $department     = trim($_POST['department'] ?? 'Main Office');
        $supervisorName = trim($_POST['supervisor_name'] ?? '');
        $supervisorMail = strtolower(trim($_POST['supervisor_email'] ?? ''));

        if (!empty($companyName) && !empty($supervisorName) && !empty($supervisorMail)) {
            try {
                $pdo->beginTransaction();

                // 1. Insert Company
                $stmtComp = $pdo->prepare("INSERT INTO companies (name, department) VALUES (?, ?)");
                $stmtComp->execute([$companyName, $department ?: 'Main Office']);
                $companyId = $pdo->lastInsertId();

                // 2. Insert or Reactivate Supervisor User
                $chkUser = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $chkUser->execute([$supervisorMail]);
                $existingUser = $chkUser->fetch(PDO::FETCH_ASSOC);

                if ($existingUser) {
                    $userId = $existingUser['id'];
                    $pdo->prepare("UPDATE users SET name = ?, role = 'supervisor', status = 'active', archived_at = NULL WHERE id = ?")->execute([$supervisorName, $userId]);
                } else {
                    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'supervisor', 'active', NOW())");
                    $stmtUser->execute([$supervisorName, $supervisorMail]);
                    $userId = $pdo->lastInsertId();
                }

                // 3. Link Supervisor to Company
                $stmtSup = $pdo->prepare("
                    INSERT INTO supervisors (user_id, company_id) 
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE company_id = VALUES(company_id)
                ");
                $stmtSup->execute([$userId, $companyId]);

                logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', "Added {$companyName} with supervisor {$supervisorName} ({$supervisorMail}).");
                $pdo->commit();

                $mailSent = $mailer->sendWelcomeEmail($supervisorMail, $supervisorName, 'supervisor');
                $_SESSION['flash_success'] = "Company '{$companyName}' and supervisor '{$supervisorName}' registered successfully!" . ($mailSent ? " Welcome email sent." : "");
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['flash_error'] = "Failed to register company and supervisor: " . $e->getMessage();
            }
        } else {
            $_SESSION['flash_error'] = "Company name, supervisor name, and email are required.";
        }
        header("Location: users.php?tab=supervisors");
        exit();
    }

    // Action C: Bulk Import Students (CSV)
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
                        $stdSec    = strtoupper(trim($data[3] ?? 'A'));

                        if (!empty($stdName) && !empty($stdNumber) && !empty($stdEmail)) {
                            $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                            $chk->execute([$stdEmail]);
                            $u = $chk->fetch(PDO::FETCH_ASSOC);

                            if ($u) {
                                $uid = $u['id'];
                                $pdo->prepare("UPDATE users SET name = ?, status = 'active', archived_at = NULL WHERE id = ?")->execute([$stdName, $uid]);
                            } else {
                                $insU = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'student', 'active', NOW())");
                                $insU->execute([$stdName, $stdEmail]);
                                $uid = $pdo->lastInsertId();
                            }

                            $insS = $pdo->prepare("
                                INSERT INTO students (user_id, student_number, program, section)
                                VALUES (?, ?, 'BSIT', ?)
                                ON DUPLICATE KEY UPDATE student_number = VALUES(student_number), program = 'BSIT', section = VALUES(section)
                            ");
                            $insS->execute([$uid, $stdNumber, $stdSec ?: 'A']);
                            $importedCount++;

                            $studentsToNotify[] = ['email' => $stdEmail, 'name' => $stdName];
                        }
                    }
                    fclose($handle);
                    logActivity($pdo, $coordinatorId, 'coordinator', 'BULK_IMPORT', "Imported {$importedCount} student records via CSV.");
                    $pdo->commit();

                    foreach ($studentsToNotify as $recipient) {
                        $mailer->sendWelcomeEmail($recipient['email'], $recipient['name'], 'student');
                    }

                    $_SESSION['flash_success'] = "Successfully imported {$importedCount} student records!";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $_SESSION['flash_error'] = "Import failed on row {$rowNumber}: " . $e->getMessage();
                }
            }
        }
        header("Location: users.php?tab=students");
        exit();
    }

    // Action D: Edit User
    if ($action === 'edit_user') {
        $userId      = (int)($_POST['user_id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $email       = strtolower(trim($_POST['email'] ?? ''));
        $role        = strtolower(trim($_POST['role'] ?? ''));
        $redirectTab = $_POST['redirect_tab'] ?? 'students';

        if ($userId > 0 && !empty($name) && !empty($email)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $userId]);

                if ($role === 'student') {
                    $stdNumber    = trim($_POST['student_number'] ?? '');
                    $section      = strtoupper(trim($_POST['section'] ?? 'A'));
                    $companyId    = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;
                    $supervisorId = !empty($_POST['supervisor_id']) ? intval($_POST['supervisor_id']) : null;

                    $pdo->prepare("UPDATE students SET student_number = ?, section = ?, company_id = ?, supervisor_id = ? WHERE user_id = ?")
                        ->execute([$stdNumber, $section, $companyId, $supervisorId, $userId]);
                } elseif ($role === 'supervisor') {
                    $companyId = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;
                    $pdo->prepare("UPDATE supervisors SET company_id = ? WHERE user_id = ?")->execute([$companyId, $userId]);
                }

                $_SESSION['flash_success'] = "User details updated successfully.";
            } catch (Exception $e) {
                $_SESSION['flash_error'] = "Update failed: " . $e->getMessage();
            }
        }
        header("Location: users.php?tab=" . $redirectTab);
        exit();
    }

    // Action E: Archive User
    if ($action === 'archive_user') {
        $userId      = (int)($_POST['user_id'] ?? 0);
        $redirectTab = $_POST['redirect_tab'] ?? 'students';

        if ($userId > 0 && $userId !== $coordinatorId) {
            $pdo->prepare("UPDATE users SET status = 'archived', archived_at = NOW() WHERE id = ?")->execute([$userId]);
            logActivity($pdo, $coordinatorId, 'coordinator', 'USER_ARCHIVED', "Archived user account ID {$userId}.");
            $_SESSION['flash_success'] = "Account archived successfully.";
        }
        header("Location: users.php?tab=" . $redirectTab);
        exit();
    }

    // Action F: Restore User
    if ($action === 'restore_user') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId > 0) {
            $pdo->prepare("UPDATE users SET status = 'active', archived_at = NULL WHERE id = ?")->execute([$userId]);
            logActivity($pdo, $coordinatorId, 'coordinator', 'USER_RESTORED', "Restored user ID {$userId}.");
            $_SESSION['flash_success'] = "Account restored to active status.";
        }
        header("Location: users.php?tab=archived");
        exit();
    }

    // Action G: Permanently Delete User
    if ($action === 'delete_user_permanently') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId > 0 && $userId !== $coordinatorId) {
            try {
                $pdo->beginTransaction();
                $pdo->prepare("UPDATE students SET supervisor_id = NULL WHERE supervisor_id IN (SELECT id FROM supervisors WHERE user_id = ?)")->execute([$userId]);
                $pdo->prepare("DELETE FROM students WHERE user_id = ?")->execute([$userId]);
                $pdo->prepare("DELETE FROM supervisors WHERE user_id = ?")->execute([$userId]);
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
                $pdo->commit();
                $_SESSION['flash_success'] = "Account permanently deleted.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['flash_error'] = "Deletion failed: " . $e->getMessage();
            }
        }
        header("Location: users.php?tab=archived");
        exit();
    }
}

// 3. Fetch Dynamic Data
$students       = [];
$supervisors    = [];
$companies      = [];
$archivedUsers  = [];
$activeSections = [];

try {
    $stmtSec = $pdo->query("SELECT DISTINCT COALESCE(NULLIF(section, ''), 'A') AS sec FROM students ORDER BY sec ASC");
    $activeSections = $stmtSec->fetchAll(PDO::FETCH_COLUMN) ?: ['A', 'B', 'C'];

    $whereStudent = ["u.status = 'active'"];
    $stdParams = [];

    if ($selectedSection !== 'all') {
        $whereStudent[] = "s.section = :sec";
        $stdParams['sec'] = $selectedSection;
    }

    $whereStudentSql = "WHERE " . implode(' AND ', $whereStudent);

    $stmtStd = $pdo->prepare("
        SELECT 
            u.id AS user_id,
            s.id AS student_id,
            s.student_number,
            s.program,
            COALESCE(s.section, 'A') AS section,
            s.company_id,
            s.supervisor_id,
            u.name,
            u.email,
            u.avatar_url,
            u.status,
            u_sup.name AS supervisor_name,
            c.name AS company_name
        FROM users u
        JOIN students s ON s.user_id = u.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        LEFT JOIN companies c ON s.company_id = c.id
        {$whereStudentSql}
        ORDER BY s.section ASC, u.name ASC
    ");
    $stmtStd->execute($stdParams);
    $students = $stmtStd->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Query All Active Supervisors (for dropdowns)
    $stmtSup = $pdo->query("
        SELECT 
            u.id AS user_id,
            sup.id,
            sup.company_id,
            u.name,
            u.email,
            u.avatar_url,
            u.status,
            c.name AS company_name,
            COUNT(s.id) AS assigned_interns
        FROM users u
        JOIN supervisors sup ON sup.user_id = u.id
        LEFT JOIN companies c ON sup.company_id = c.id
        LEFT JOIN students s ON sup.id = s.supervisor_id AND s.user_id IN (SELECT id FROM users WHERE status = 'active')
        WHERE u.status = 'active'
        GROUP BY u.id, sup.id, sup.company_id, u.name, u.email, u.avatar_url, u.status, c.name
        ORDER BY u.name ASC
    ");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Query Companies
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

    // Archived Accounts
    $stmtArch = $pdo->query("
        SELECT id, name, email, role, archived_at, avatar_url
        FROM users
        WHERE status = 'archived'
        ORDER BY archived_at DESC
    ");
    $archivedUsers = $stmtArch->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    error_log("Database Error in coordinator/users.php: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/coordinator/usersPage.php';