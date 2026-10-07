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

// The standalone Supervisors tab was merged into Companies; keep old links working.

if ($tab === 'supervisors') {

    $tab = 'companies';

}

$selectedSection = $_GET['section'] ?? 'all';

$success         = $_SESSION['flash_success'] ?? null;

$error           = $_SESSION['flash_error'] ?? null;

unset($_SESSION['flash_success'], $_SESSION['flash_error']);



$mailer = new MailerService();



/**

 * Validate the optional supervisor job title / contact number inputs.

 * Returns an error message, or '' when valid. (Same rules as supervisor/profile.php)

 */

function validateSupervisorContact(string $jobTitle, string $contactNumber): string

{

    if (mb_strlen($jobTitle) > 150) {

        return 'Job title must be 150 characters or fewer.';

    }

    if ($contactNumber !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $contactNumber)) {

        return 'Contact number must be 7-20 characters and contain only digits, +, -, spaces or parentheses.';

    }

    return '';

}



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

        $address        = trim($_POST['address'] ?? '');

        $supervisorName = trim($_POST['supervisor_name'] ?? '');

        $supervisorMail = strtolower(trim($_POST['supervisor_email'] ?? ''));

        $jobTitle       = trim($_POST['job_title'] ?? '');

        $contactNumber  = trim($_POST['contact_number'] ?? '');



        $contactError = validateSupervisorContact($jobTitle, $contactNumber);

        if ($contactError === '' && mb_strlen($address) > 255) {

            $contactError = 'Company address must be 255 characters or fewer.';

        }



        if ($contactError !== '') {

            $_SESSION['flash_error'] = $contactError;

        } elseif (!empty($companyName) && !empty($supervisorName) && !empty($supervisorMail)) {

            try {

                $pdo->beginTransaction();



                // 1. Insert Company

                $stmtComp = $pdo->prepare("INSERT INTO companies (name, department, address) VALUES (?, ?, ?)");

                $stmtComp->execute([$companyName, $department ?: 'Main Office', $address !== '' ? $address : null]);

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

                    INSERT INTO supervisors (user_id, company_id, job_title, contact_number)

                    VALUES (?, ?, ?, ?)

                    ON DUPLICATE KEY UPDATE

                        company_id = VALUES(company_id),

                        job_title = COALESCE(VALUES(job_title), job_title),

                        contact_number = COALESCE(VALUES(contact_number), contact_number)

                ");

                $stmtSup->execute([

                    $userId,

                    $companyId,

                    $jobTitle !== '' ? $jobTitle : null,

                    $contactNumber !== '' ? $contactNumber : null

                ]);



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

        header("Location: users.php?tab=companies");

        exit();

    }



    // Action B2: Create / Reactivate Coordinator Account

    if ($action === 'create_coordinator') {

        $name  = trim($_POST['name'] ?? '');

        $email = strtolower(trim($_POST['email'] ?? ''));



        if (!empty($name) && !empty($email)) {

            try {

                $pdo->beginTransaction();



                $chk = $pdo->prepare("SELECT id, role, status FROM users WHERE email = ?");

                $chk->execute([$email]);

                $existing = $chk->fetch(PDO::FETCH_ASSOC);



                $isReactivation = false;



                if ($existing) {

                    $userId = $existing['id'];

                    $isReactivation = true;

                    $pdo->prepare("UPDATE users SET name = ?, role = 'coordinator', status = 'active', archived_at = NULL WHERE id = ?")

                        ->execute([$name, $userId]);

                } else {

                    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'coordinator', 'active', NOW())");

                    $stmtUser->execute([$name, $email]);

                    $userId = $pdo->lastInsertId();

                }



                logActivity($pdo, $coordinatorId, 'coordinator', 'COORDINATOR_CREATED', "Added coordinator {$name} ({$email}).");

                $pdo->commit();



                if ($isReactivation) {

                    $mailSent = $mailer->sendRoleAssignmentEmail($email, $name, 'coordinator');

                    $_SESSION['flash_success'] = "Coordinator '{$name}' account updated successfully!" . ($mailSent ? " Notification email sent." : "");

                } else {

                    $mailSent = $mailer->sendWelcomeEmail($email, $name, 'coordinator');

                    $_SESSION['flash_success'] = "Coordinator '{$name}' added successfully!" . ($mailSent ? " Welcome email sent." : "");

                }

            } catch (Exception $e) {

                $pdo->rollBack();

                $_SESSION['flash_error'] = "Failed to add coordinator: " . $e->getMessage();

            }

        } else {

            $_SESSION['flash_error'] = "Coordinator name and email are required.";

        }

        header("Location: users.php?tab=coordinators");

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

        if ($redirectTab === 'supervisors') {

            $redirectTab = 'companies';

        }



        // Validate optional supervisor contact fields before touching any data

        if ($role === 'supervisor') {

            $contactError = validateSupervisorContact(

                trim($_POST['job_title'] ?? ''),

                trim($_POST['contact_number'] ?? '')

            );

            if ($contactError !== '') {

                $_SESSION['flash_error'] = $contactError;

                header("Location: users.php?tab=" . $redirectTab);

                exit();

            }

        }



        if ($userId > 0 && !empty($name) && !empty($email)) {

            try {

                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");

                $stmt->execute([$name, $email, $userId]);



                // Allow role changes when editing a coordinator row

                if ($role === 'coordinator') {

                    $newRole = strtolower(trim($_POST['new_role'] ?? 'coordinator'));

                    $allowedRoles = ['coordinator', 'supervisor', 'student'];



                    if (in_array($newRole, $allowedRoles, true)) {

                        if ($userId === (int)$coordinatorId && $newRole !== 'coordinator') {

                            $_SESSION['flash_error'] = "You cannot demote your own coordinator account.";

                            header("Location: users.php?tab=" . $redirectTab);

                            exit();

                        }

                        $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$newRole, $userId]);

                    }

                }



                if ($role === 'student') {

                    $stdNumber    = trim($_POST['student_number'] ?? '');

                    $section      = strtoupper(trim($_POST['section'] ?? 'A'));

                    $companyId    = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;

                    $supervisorId = !empty($_POST['supervisor_id']) ? intval($_POST['supervisor_id']) : null;



                    $pdo->prepare("UPDATE students SET student_number = ?, section = ?, company_id = ?, supervisor_id = ? WHERE user_id = ?")

                        ->execute([$stdNumber, $section, $companyId, $supervisorId, $userId]);

                } elseif ($role === 'supervisor') {

                    $companyId     = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;

                    $jobTitle      = trim($_POST['job_title'] ?? '');

                    $contactNumber = trim($_POST['contact_number'] ?? '');

                    $pdo->prepare("UPDATE supervisors SET company_id = ?, job_title = ?, contact_number = ? WHERE user_id = ?")

                        ->execute([

                            $companyId,

                            $jobTitle !== '' ? $jobTitle : null,

                            $contactNumber !== '' ? $contactNumber : null,

                            $userId

                        ]);

                }



                $_SESSION['flash_success'] = "User details updated successfully.";

            } catch (Exception $e) {

                $_SESSION['flash_error'] = "Update failed: " . $e->getMessage();

            }

        }

        header("Location: users.php?tab=" . $redirectTab);

        exit();

    }



    // Action D2: Edit Company

    if ($action === 'edit_company') {

        $companyId   = (int)($_POST['company_id'] ?? 0);

        $companyName = trim($_POST['company_name'] ?? '');

        $department  = trim($_POST['department'] ?? '');

        $address     = trim($_POST['address'] ?? '');



        if ($companyId <= 0 || $companyName === '') {

            $_SESSION['flash_error'] = "Company name is required.";

        } elseif (mb_strlen($companyName) > 255 || mb_strlen($department) > 255 || mb_strlen($address) > 255) {

            $_SESSION['flash_error'] = "Company name, department and address must each be 255 characters or fewer.";

        } else {

            try {

                $pdo->prepare("UPDATE companies SET name = ?, department = ?, address = ? WHERE id = ?")

                    ->execute([

                        $companyName,

                        $department !== '' ? $department : 'Main Office',

                        $address !== '' ? $address : null,

                        $companyId

                    ]);



                logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_UPDATED', "Updated company '{$companyName}' (ID {$companyId}).");

                $_SESSION['flash_success'] = "Company '{$companyName}' updated successfully.";

            } catch (Exception $e) {

                $_SESSION['flash_error'] = "Failed to update company: " . $e->getMessage();

            }

        }

        header("Location: users.php?tab=companies");

        exit();

    }



    // Action E: Archive User

    if ($action === 'archive_user') {

        $userId      = (int)($_POST['user_id'] ?? 0);

        $redirectTab = $_POST['redirect_tab'] ?? 'students';

        if ($redirectTab === 'supervisors') {

            $redirectTab = 'companies';

        }



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

$coordinators   = [];

$supervisorsByCompany  = [];

$unassignedSupervisors = [];



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

            sup.job_title,

            sup.contact_number,

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

        GROUP BY u.id, sup.id, sup.company_id, sup.job_title, sup.contact_number, u.name, u.email, u.avatar_url, u.status, c.name

        ORDER BY u.name ASC

    ");

    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC) ?: [];



    // Query Companies

    $stmtComp = $pdo->query("

        SELECT

            c.id,

            c.name,

            c.department,

            c.address,

            COUNT(s.id) AS total_interns

        FROM companies c

        LEFT JOIN supervisors sup ON c.id = sup.company_id

        LEFT JOIN students s ON sup.id = s.supervisor_id

        GROUP BY c.id, c.name, c.department, c.address

        ORDER BY c.name ASC

    ");

    $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC) ?: [];



    // Active interns grouped by supervisor (supervisors.id)

    $stmtInterns = $pdo->query("

        SELECT s.supervisor_id, s.id AS student_id, s.student_number, u.name

        FROM students s

        JOIN users u ON s.user_id = u.id

        WHERE s.supervisor_id IS NOT NULL AND u.status = 'active'

        ORDER BY u.name ASC

    ");

    $internsBySupervisor = [];

    foreach ($stmtInterns->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {

        $internsBySupervisor[(int)$row['supervisor_id']][] = $row;

    }



    // Group supervisors under their company; leftovers go to "Unassigned"

    $supervisorsByCompany   = [];

    $unassignedSupervisors  = [];

    $knownCompanyIds        = array_map('intval', array_column($companies, 'id'));

    foreach ($supervisors as $sup) {

        $sup['interns'] = $internsBySupervisor[(int)$sup['id']] ?? [];

        $cid = (int)($sup['company_id'] ?? 0);

        if ($cid > 0 && in_array($cid, $knownCompanyIds, true)) {

            $supervisorsByCompany[$cid][] = $sup;

        } else {

            $unassignedSupervisors[] = $sup;

        }

    }



    // Company intern totals reflect the active interns listed under its supervisors

    foreach ($companies as &$compRef) {

        $total = 0;

        foreach ($supervisorsByCompany[(int)$compRef['id']] ?? [] as $s) {

            $total += count($s['interns']);

        }

        $compRef['total_interns'] = $total;

    }

    unset($compRef);



    // Archived Accounts

    $stmtArch = $pdo->query("

        SELECT id, name, email, role, archived_at, avatar_url

        FROM users

        WHERE status = 'archived'

        ORDER BY archived_at DESC

    ");

    $archivedUsers = $stmtArch->fetchAll(PDO::FETCH_ASSOC) ?: [];



    // Active Coordinators

    $stmtCoord = $pdo->query("

        SELECT id AS user_id, name, email, avatar_url, status, created_at

        FROM users

        WHERE role = 'coordinator' AND status = 'active'

        ORDER BY name ASC

    ");

    $coordinators = $stmtCoord->fetchAll(PDO::FETCH_ASSOC) ?: [];



} catch (Exception $e) {

    error_log("Database Error in coordinator/users.php: " . $e->getMessage());

}



require_once __DIR__ . '/../src/pages/coordinator/usersPage.php';