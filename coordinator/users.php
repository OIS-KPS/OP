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

$coordinatorId = $_SESSION['user_id'];
$tab           = $_GET['tab'] ?? 'students';
$success       = $_SESSION['flash_success'] ?? null;
$error         = $_SESSION['flash_error'] ?? null;
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
                    INSERT INTO students (user_id, student_number, program) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE student_number = VALUES(student_number), program = VALUES(program)
                ");
                $stmtStudent->execute([$userId, $studentNumber, $program]);

                logActivity($pdo, $coordinatorId, 'coordinator', 'STUDENT_CREATED', "Added student {$name} ({$studentNumber} - {$email}).");
                $pdo->commit();

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
                    $pdo->prepare("UPDATE users SET name = ?, status = 'active', archived_at = NULL WHERE id = ?")->execute([$name, $userId]);
                } else {
                    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'supervisor', 'active', NOW())");
                    $stmtUser->execute([$name, $email]);
                    $userId = $pdo->lastInsertId();
                }

                $stmtSup = $pdo->prepare("
                    INSERT INTO supervisors (user_id, company_id) 
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE company_id = VALUES(company_id)
                ");
                $stmtSup->execute([$userId, $companyId]);

                logActivity($pdo, $coordinatorId, 'coordinator', 'SUPERVISOR_CREATED', "Added supervisor {$name} ({$email}).");
                $pdo->commit();

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
                logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_CREATED', "Created partner company: {$name} - {$department}.");
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

    // Action D: Bulk Import Students
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
                                $pdo->prepare("UPDATE users SET name = ?, status = 'active', archived_at = NULL WHERE id = ?")->execute([$stdName, $uid]);
                            } else {
                                $insU = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'student', 'active', NOW())");
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
                    logActivity($pdo, $coordinatorId, 'coordinator', 'BULK_IMPORT', "Imported {$importedCount} student records via CSV.");
                    $pdo->commit();

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

    // Action E: Edit User Record
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
                    $stdNumber = trim($_POST['student_number'] ?? '');
                    $program   = trim($_POST['program'] ?? 'BSIT');
                    $pdo->prepare("UPDATE students SET student_number = ?, program = ? WHERE user_id = ?")->execute([$stdNumber, $program, $userId]);
                } elseif ($role === 'supervisor') {
                    $companyId = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;
                    $pdo->prepare("UPDATE supervisors SET company_id = ? WHERE user_id = ?")->execute([$companyId, $userId]);
                }

                logActivity($pdo, $coordinatorId, 'coordinator', 'USER_UPDATED', "Updated user ID {$userId} ({$name} - {$email}).");
                $_SESSION['flash_success'] = "User details updated successfully.";
            } catch (Exception $e) {
                $_SESSION['flash_error'] = "Update failed: " . $e->getMessage();
            }
        }
        header("Location: users.php?tab=" . $redirectTab);
        exit();
    }

    // Action F: Archive User (Soft Delete)
    if ($action === 'archive_user') {
        $userId      = (int)($_POST['user_id'] ?? 0);
        $redirectTab = $_POST['redirect_tab'] ?? 'students';

        if ($userId > 0 && $userId !== $coordinatorId) {
            $stmt = $pdo->prepare("UPDATE users SET status = 'archived', archived_at = NOW() WHERE id = ?");
            $stmt->execute([$userId]);

            logActivity($pdo, $coordinatorId, 'coordinator', 'USER_ARCHIVED', "Archived user account ID {$userId}.");
            $_SESSION['flash_success'] = "Account archived successfully.";
        } else {
            $_SESSION['flash_error'] = "Unable to archive this account.";
        }
        header("Location: users.php?tab=" . $redirectTab);
        exit();
    }

    // Action G: Restore User
    if ($action === 'restore_user') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId > 0) {
            $stmt = $pdo->prepare("UPDATE users SET status = 'active', archived_at = NULL WHERE id = ?");
            $stmt->execute([$userId]);

            logActivity($pdo, $coordinatorId, 'coordinator', 'USER_RESTORED', "Restored archived user ID {$userId} to active status.");
            $_SESSION['flash_success'] = "Account restored to active status.";
        }
        header("Location: users.php?tab=archived");
        exit();
    }

    // Action H: Permanently Delete User (Hard Delete)
    if ($action === 'delete_user_permanently') {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId > 0 && $userId !== $coordinatorId) {
            try {
                $pdo->beginTransaction();

                $stmtCheck = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
                $stmtCheck->execute([$userId]);
                $targetUser = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($targetUser) {
                    $pdo->prepare("UPDATE students SET supervisor_id = NULL WHERE supervisor_id IN (SELECT id FROM supervisors WHERE user_id = ?)")->execute([$userId]);
                    $pdo->prepare("DELETE FROM students WHERE user_id = ?")->execute([$userId]);
                    $pdo->prepare("DELETE FROM supervisors WHERE user_id = ?")->execute([$userId]);
                    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);

                    logActivity($pdo, $coordinatorId, 'coordinator', 'USER_DELETED_PERMANENTLY', "Permanently deleted user {$targetUser['name']} ({$targetUser['email']}).");

                    $pdo->commit();
                    $_SESSION['flash_success'] = "Account permanently deleted from the database.";
                } else {
                    $pdo->rollBack();
                    $_SESSION['flash_error'] = "User record not found.";
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['flash_error'] = "Deletion failed: " . $e->getMessage();
            }
        } else {
            $_SESSION['flash_error'] = "Cannot delete the active coordinator account.";
        }
        header("Location: users.php?tab=archived");
        exit();
    }
}

// 3. Fetch Dynamic Data for View
$students      = [];
$supervisors   = [];
$companies     = [];
$archivedUsers = [];

try {
    // Active Students
    $stmtStd = $pdo->query("
        SELECT 
            u.id AS user_id,
            s.id AS student_id,
            s.student_number,
            s.program,
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
        LEFT JOIN companies c ON sup.company_id = c.id
        WHERE u.status = 'active'
        ORDER BY u.name ASC
    ");
    $students = $stmtStd->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Active Supervisors
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

    // Companies
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