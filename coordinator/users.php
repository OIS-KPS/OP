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

        $officeId      = !empty($_POST['office_id']) ? intval($_POST['office_id']) : null;



        if (!empty($name) && !empty($studentNumber) && !empty($email)) {

            try {

                $pdo->beginTransaction();



                // Resolve placement: the office determines company + supervisor
                $companyId    = null;
                $supervisorId = null;

                if ($officeId) {
                    $stmtOffice = $pdo->prepare("
                        SELECT o.company_id, sup.id AS supervisor_id
                        FROM offices o
                        LEFT JOIN supervisors sup ON sup.office_id = o.id
                        WHERE o.id = ?
                        LIMIT 1
                    ");
                    $stmtOffice->execute([$officeId]);
                    $office = $stmtOffice->fetch(PDO::FETCH_ASSOC);

                    if ($office) {
                        $companyId    = $office['company_id'];
                        $supervisorId = $office['supervisor_id'] ?: null;
                    }
                }



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

                    INSERT INTO students (user_id, student_number, program, section, office_id, company_id, supervisor_id)

                    VALUES (?, ?, 'BSIT', ?, ?, ?, ?)

                    ON DUPLICATE KEY UPDATE

                        student_number = VALUES(student_number),

                        program = 'BSIT',

                        section = VALUES(section),

                        office_id = VALUES(office_id),

                        company_id = VALUES(company_id),

                        supervisor_id = VALUES(supervisor_id)

                ");

                $stmtStudent->execute([$userId, $studentNumber, $section, $officeId, $companyId, $supervisorId]);



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

        $officeName     = trim($_POST['department'] ?? '');

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



                // 1. Find or create company by exact name

                $stmtFindComp = $pdo->prepare("SELECT id, status FROM companies WHERE name = ? LIMIT 1");

                $stmtFindComp->execute([$companyName]);

                $existingCompany = $stmtFindComp->fetch(PDO::FETCH_ASSOC);

                if ($existingCompany && ($existingCompany['status'] ?? 'active') !== 'active') {

                    throw new Exception("Company '{$companyName}' is archived. Restore it before adding a new office.");

                }

                if ($existingCompany) {

                    $companyId = (int)$existingCompany['id'];

                } else {

                    $stmtComp = $pdo->prepare("INSERT INTO companies (name, department, address) VALUES (?, ?, ?)");

                    $stmtComp->execute([$companyName, $officeName !== '' ? $officeName : 'Main Office', $address !== '' ? $address : null]);

                    $companyId = $pdo->lastInsertId();

                }



                // 2. Find or create the office within the company

                $officeName = $officeName !== '' ? $officeName : 'Main Office';

                $stmtOff = $pdo->prepare("SELECT id FROM offices WHERE company_id = ? AND name = ? LIMIT 1");

                $stmtOff->execute([$companyId, $officeName]);

                $officeId = $stmtOff->fetchColumn() ?: null;

                if (!$officeId) {

                    $stmtOffIns = $pdo->prepare("INSERT INTO offices (company_id, name, address) VALUES (?, ?, ?)");

                    $stmtOffIns->execute([$companyId, $officeName, $address !== '' ? $address : null]);

                    $officeId = $pdo->lastInsertId();

                }



                // 3. Enforce 1 supervisor per office

                $stmtOffSup = $pdo->prepare("SELECT id FROM supervisors WHERE office_id = ? LIMIT 1");

                $stmtOffSup->execute([$officeId]);

                if ($stmtOffSup->fetchColumn()) {

                    throw new Exception("Office '{$officeName}' at '{$companyName}' already has a supervisor.");

                }



                // 4. Insert or Reactivate Supervisor User

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



                // 5. Link Supervisor to Company + Office

                $stmtSup = $pdo->prepare("

                    INSERT INTO supervisors (user_id, company_id, office_id, job_title, contact_number)

                    VALUES (?, ?, ?, ?, ?)

                    ON DUPLICATE KEY UPDATE

                        company_id = VALUES(company_id),

                        office_id = VALUES(office_id),

                        job_title = COALESCE(VALUES(job_title), job_title),

                        contact_number = COALESCE(VALUES(contact_number), contact_number)

                ");

                $stmtSup->execute([

                    $userId,

                    $companyId,

                    $officeId,

                    $jobTitle !== '' ? $jobTitle : null,

                    $contactNumber !== '' ? $contactNumber : null

                ]);



                logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', "Added {$companyName} ({$officeName}) with supervisor {$supervisorName} ({$supervisorMail}).");

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
            $skippedRows   = [];
            $seenEmails    = [];
            $seenNumbers   = [];

           

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



                        // Validation
                        if ($stdName === '' || $stdNumber === '' || $stdEmail === '') {
                            $skippedRows[] = "Row {$rowNumber}: missing required fields";
                            continue;
                        }
                        if (!filter_var($stdEmail, FILTER_VALIDATE_EMAIL)) {
                            $skippedRows[] = "Row {$rowNumber}: invalid email '{$stdEmail}'";
                            continue;
                        }
                        if (isset($seenEmails[$stdEmail])) {
                            $skippedRows[] = "Row {$rowNumber}: duplicate email '{$stdEmail}' in file";
                            continue;
                        }
                        if (isset($seenNumbers[$stdNumber])) {
                            $skippedRows[] = "Row {$rowNumber}: duplicate student ID '{$stdNumber}' in file";
                            continue;
                        }

                        // Role conflict: email belongs to a non-student account
                        $chkRole = $pdo->prepare("SELECT role FROM users WHERE email = ?");
                        $chkRole->execute([$stdEmail]);
                        $existingRole = $chkRole->fetchColumn();

                        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");

                            $chk->execute([$stdEmail]);

                            $u = $chk->fetch(PDO::FETCH_ASSOC);

                        if ($u && $existingRole !== 'student') {
                            $skippedRows[] = "Row {$rowNumber}: email '{$stdEmail}' is already a {$existingRole} account";
                            continue;
                        }

                        // Student number conflict: belongs to a different student
                        $chkNum = $pdo->prepare("SELECT user_id FROM students WHERE student_number = ? LIMIT 1");
                        $chkNum->execute([$stdNumber]);
                        $numOwner = $chkNum->fetchColumn();
                        if ($numOwner && (!$u || $numOwner != $u['id'])) {
                            $skippedRows[] = "Row {$rowNumber}: student ID '{$stdNumber}' already belongs to another student";
                            continue;
                        }



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

                            $seenEmails[$stdEmail] = true;
                            $seenNumbers[$stdNumber] = true;
                            $importedCount++;



                            $studentsToNotify[] = ['email' => $stdEmail, 'name' => $stdName];

                    }

                    fclose($handle);

                    logActivity($pdo, $coordinatorId, 'coordinator', 'BULK_IMPORT', "Imported {$importedCount} student records via CSV.");

                    $pdo->commit();



                    foreach ($studentsToNotify as $recipient) {

                        $mailer->sendWelcomeEmail($recipient['email'], $recipient['name'], 'student');

                    }



                    $skipMsg = '';
                    if (!empty($skippedRows)) {
                        $preview = implode(' | ', array_slice($skippedRows, 0, 5));
                        $skipMsg = ' Skipped ' . count($skippedRows) . ' row(s): ' . $preview . (count($skippedRows) > 5 ? '…' : '');
                    }
                    $_SESSION['flash_success'] = "Successfully imported {$importedCount} student record(s)!{$skipMsg}";

                } catch (Exception $e) {

                    $pdo->rollBack();

                    $_SESSION['flash_error'] = "Import failed on row {$rowNumber}: " . $e->getMessage();

                }

            }

        }

        header("Location: users.php?tab=students");

        exit();

    }



    // Action C2: Bulk Import Coordinators (CSV)

    if ($action === 'bulk_import_coordinators' && isset($_FILES['excel_file'])) {

        $file = $_FILES['excel_file'];

        if ($file['error'] === UPLOAD_ERR_OK) {

            $handle = fopen($file['tmp_name'], "r");

            $importedCount = 0;
            $skippedRows   = [];
            $seenEmails    = [];

            if ($handle !== FALSE) {

                $pdo->beginTransaction();

                try {

                    $rowNumber = 0;
                    $coordinatorsToNotify = [];

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                        $rowNumber++;

                        if ($rowNumber === 1 && (stripos($data[0] ?? '', 'name') !== false || stripos($data[1] ?? '', 'email') !== false)) {
                            continue;
                        }

                        $name  = trim($data[0] ?? '');
                        $email = strtolower(trim($data[1] ?? ''));

                        if ($name === '' || $email === '') {
                            $skippedRows[] = "Row {$rowNumber}: missing required fields";
                            continue;
                        }
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $skippedRows[] = "Row {$rowNumber}: invalid email '{$email}'";
                            continue;
                        }
                        if (isset($seenEmails[$email])) {
                            $skippedRows[] = "Row {$rowNumber}: duplicate email '{$email}' in file";
                            continue;
                        }

                        // Role conflict: email belongs to a non-coordinator account
                        $chkRole = $pdo->prepare("SELECT role FROM users WHERE email = ?");
                        $chkRole->execute([$email]);
                        $existingRole = $chkRole->fetchColumn();

                        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                        $chk->execute([$email]);
                        $u = $chk->fetch(PDO::FETCH_ASSOC);

                        if ($u && $existingRole !== 'coordinator') {
                            $skippedRows[] = "Row {$rowNumber}: email '{$email}' is already a {$existingRole} account";
                            continue;
                        }

                        if ($u) {
                            $uid = $u['id'];
                            $pdo->prepare("UPDATE users SET name = ?, role = 'coordinator', status = 'active', archived_at = NULL WHERE id = ?")
                                ->execute([$name, $uid]);
                        } else {
                            $insU = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'coordinator', 'active', NOW())");
                            $insU->execute([$name, $email]);
                            $uid = $pdo->lastInsertId();
                        }

                        $seenEmails[$email] = true;
                        $importedCount++;
                        $coordinatorsToNotify[] = ['email' => $email, 'name' => $name];
                    }

                    fclose($handle);

                    logActivity($pdo, $coordinatorId, 'coordinator', 'BULK_IMPORT_COORDINATORS', "Imported {$importedCount} coordinator records via CSV.");

                    $pdo->commit();

                    foreach ($coordinatorsToNotify as $recipient) {
                        $mailer->sendWelcomeEmail($recipient['email'], $recipient['name'], 'coordinator');
                    }

                    $skipMsg = '';
                    if (!empty($skippedRows)) {
                        $preview = implode(' | ', array_slice($skippedRows, 0, 5));
                        $skipMsg = ' Skipped ' . count($skippedRows) . ' row(s): ' . $preview . (count($skippedRows) > 5 ? '…' : '');
                    }
                    $_SESSION['flash_success'] = "Successfully imported {$importedCount} coordinator record(s)!{$skipMsg}";

                } catch (Exception $e) {
                    $pdo->rollBack();
                    $_SESSION['flash_error'] = "Import failed on row {$rowNumber}: " . $e->getMessage();
                }
            }
        }

        header("Location: users.php?tab=coordinators");
        exit();
    }



    // Action C3: Bulk Import Companies & Supervisors (CSV)

    if ($action === 'bulk_import_companies_supervisors' && isset($_FILES['excel_file'])) {

        $file = $_FILES['excel_file'];

        if ($file['error'] === UPLOAD_ERR_OK) {

            $handle = fopen($file['tmp_name'], "r");

            $importedCount = 0;
            $skippedRows   = [];
            $seenEmails    = [];

            if ($handle !== FALSE) {

                $pdo->beginTransaction();

                try {

                    $rowNumber = 0;
                    $supervisorsToNotify = [];

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                        $rowNumber++;

                        if ($rowNumber === 1 && (stripos($data[0] ?? '', 'company') !== false || stripos($data[3] ?? '', 'supervisor') !== false)) {
                            continue;
                        }

                        // Columns: company_name, office_name, address, supervisor_name, supervisor_email, job_title, contact_number
                        $companyName    = trim($data[0] ?? '');
                        $officeName     = trim($data[1] ?? '');
                        $address        = trim($data[2] ?? '');
                        $supervisorName = trim($data[3] ?? '');
                        $supervisorMail = strtolower(trim($data[4] ?? ''));
                        $jobTitle       = trim($data[5] ?? '');
                        $contactNumber  = trim($data[6] ?? '');

                        if ($companyName === '' || $supervisorName === '' || $supervisorMail === '') {
                            $skippedRows[] = "Row {$rowNumber}: missing required fields (company, supervisor name, supervisor email)";
                            continue;
                        }
                        if (!filter_var($supervisorMail, FILTER_VALIDATE_EMAIL)) {
                            $skippedRows[] = "Row {$rowNumber}: invalid email '{$supervisorMail}'";
                            continue;
                        }
                        if (isset($seenEmails[$supervisorMail])) {
                            $skippedRows[] = "Row {$rowNumber}: duplicate supervisor email '{$supervisorMail}' in file";
                            continue;
                        }
                        $contactError = validateSupervisorContact($jobTitle, $contactNumber);
                        if ($contactError !== '') {
                            $skippedRows[] = "Row {$rowNumber}: {$contactError}";
                            continue;
                        }
                        if (mb_strlen($address) > 255) {
                            $skippedRows[] = "Row {$rowNumber}: address must be 255 characters or fewer";
                            continue;
                        }

                        // Find or create company by exact name
                        $chkComp = $pdo->prepare("SELECT id FROM companies WHERE name = ? LIMIT 1");
                        $chkComp->execute([$companyName]);
                        $companyId = $chkComp->fetchColumn() ?: null;
                        if (!$companyId) {
                            $stmtComp = $pdo->prepare("INSERT INTO companies (name, department, address) VALUES (?, ?, ?)");
                            $stmtComp->execute([$companyName, $officeName !== '' ? $officeName : 'Main Office', $address !== '' ? $address : null]);
                            $companyId = $pdo->lastInsertId();
                        }

                        // Find or create office within the company
                        $officeName = $officeName !== '' ? $officeName : 'Main Office';
                        $stmtOff = $pdo->prepare("SELECT id FROM offices WHERE company_id = ? AND name = ? LIMIT 1");
                        $stmtOff->execute([$companyId, $officeName]);
                        $officeId = $stmtOff->fetchColumn() ?: null;
                        if (!$officeId) {
                            $stmtOffIns = $pdo->prepare("INSERT INTO offices (company_id, name, address) VALUES (?, ?, ?)");
                            $stmtOffIns->execute([$companyId, $officeName, $address !== '' ? $address : null]);
                            $officeId = $pdo->lastInsertId();
                        }

                        // Enforce 1 supervisor per office
                        $stmtOffSup = $pdo->prepare("SELECT id FROM supervisors WHERE office_id = ? LIMIT 1");
                        $stmtOffSup->execute([$officeId]);
                        if ($stmtOffSup->fetchColumn()) {
                            $skippedRows[] = "Row {$rowNumber}: office '{$officeName}' at '{$companyName}' already has a supervisor";
                            continue;
                        }

                        // Role conflict: email belongs to a non-supervisor account
                        $chkRole = $pdo->prepare("SELECT role FROM users WHERE email = ?");
                        $chkRole->execute([$supervisorMail]);
                        $existingRole = $chkRole->fetchColumn();

                        $chkUser = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                        $chkUser->execute([$supervisorMail]);
                        $u = $chkUser->fetch(PDO::FETCH_ASSOC);

                        if ($u && $existingRole !== 'supervisor') {
                            $skippedRows[] = "Row {$rowNumber}: email '{$supervisorMail}' is already a {$existingRole} account";
                            continue;
                        }

                        if ($u) {
                            $userId = $u['id'];
                            $pdo->prepare("UPDATE users SET name = ?, role = 'supervisor', status = 'active', archived_at = NULL WHERE id = ?")
                                ->execute([$supervisorName, $userId]);
                        } else {
                            $insU = $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'supervisor', 'active', NOW())");
                            $insU->execute([$supervisorName, $supervisorMail]);
                            $userId = $pdo->lastInsertId();
                        }

                        $stmtSup = $pdo->prepare("
                            INSERT INTO supervisors (user_id, company_id, office_id, job_title, contact_number)
                            VALUES (?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE
                                company_id = VALUES(company_id),
                                office_id = VALUES(office_id),
                                job_title = COALESCE(VALUES(job_title), job_title),
                                contact_number = COALESCE(VALUES(contact_number), contact_number)
                        ");
                        $stmtSup->execute([
                            $userId,
                            $companyId,
                            $officeId,
                            $jobTitle !== '' ? $jobTitle : null,
                            $contactNumber !== '' ? $contactNumber : null
                        ]);

                        $seenEmails[$supervisorMail] = true;
                        $importedCount++;
                        $supervisorsToNotify[] = ['email' => $supervisorMail, 'name' => $supervisorName];
                    }

                    fclose($handle);

                    logActivity($pdo, $coordinatorId, 'coordinator', 'BULK_IMPORT_COMPANIES_SUPERVISORS', "Imported {$importedCount} company/supervisor records via CSV.");

                    $pdo->commit();

                    foreach ($supervisorsToNotify as $recipient) {
                        $mailer->sendWelcomeEmail($recipient['email'], $recipient['name'], 'supervisor');
                    }

                    $skipMsg = '';
                    if (!empty($skippedRows)) {
                        $preview = implode(' | ', array_slice($skippedRows, 0, 5));
                        $skipMsg = ' Skipped ' . count($skippedRows) . ' row(s): ' . $preview . (count($skippedRows) > 5 ? '…' : '');
                    }
                    $_SESSION['flash_success'] = "Successfully imported {$importedCount} company/supervisor record(s)!{$skipMsg}";

                } catch (Exception $e) {
                    $pdo->rollBack();
                    $_SESSION['flash_error'] = "Import failed on row {$rowNumber}: " . $e->getMessage();
                }
            }
        }

        header("Location: users.php?tab=companies");
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

                    $stdNumber = trim($_POST['student_number'] ?? '');
                    $section   = strtoupper(trim($_POST['section'] ?? 'A'));
                    $officeId  = !empty($_POST['office_id']) ? intval($_POST['office_id']) : null;

                    // Resolve company + supervisor from the office
                    $companyId    = null;
                    $supervisorId = null;
                    if ($officeId) {
                        $stmtOff = $pdo->prepare("SELECT company_id FROM offices WHERE id = ?");
                        $stmtOff->execute([$officeId]);
                        $companyId = $stmtOff->fetchColumn() ?: null;

                        $stmtOffSup = $pdo->prepare("SELECT id FROM supervisors WHERE office_id = ? LIMIT 1");
                        $stmtOffSup->execute([$officeId]);
                        $supervisorId = $stmtOffSup->fetchColumn() ?: null;
                    }



                    $pdo->prepare("UPDATE students SET student_number = ?, section = ?, office_id = ?, company_id = ?, supervisor_id = ? WHERE user_id = ?")

                        ->execute([$stdNumber, $section, $officeId, $companyId, $supervisorId, $userId]);

                } elseif ($role === 'supervisor') {
                    $companyId     = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;
                    $jobTitle      = trim($_POST['job_title'] ?? '');
                    $contactNumber = trim($_POST['contact_number'] ?? '');

                    // The form posts office_id (a select); office_name is kept as a fallback
                    $submittedOfficeId = !empty($_POST['office_id'])
                        ? intval($_POST['office_id'])
                        : null;
                    $fallbackOfficeName = trim($_POST['office_name'] ?? '');

                    // Current assignment for this supervisor
                    $stmtCurrent = $pdo->prepare("SELECT company_id, office_id FROM supervisors WHERE user_id = ? LIMIT 1");
                    $stmtCurrent->execute([$userId]);
                    $current = $stmtCurrent->fetch(PDO::FETCH_ASSOC) ?: [];
                    $currentOfficeId = !empty($current['office_id']) ? (int)$current['office_id'] : null;

                    $pdo->beginTransaction();

                    // Resolve the office being assigned
                    $officeId = $submittedOfficeId;

                    if ($officeId) {
                        $stmtOffice = $pdo->prepare("SELECT id, company_id, name FROM offices WHERE id = ? LIMIT 1");
                        $stmtOffice->execute([$officeId]);
                        $office = $stmtOffice->fetch(PDO::FETCH_ASSOC) ?: [];

                        if (!$office) {
                            throw new Exception("The selected office no longer exists. Refresh and try again.");
                        }

                        if ($companyId && (int)$office['company_id'] !== $companyId) {
                            throw new Exception("The selected office does not belong to the selected company.");
                        }

                        $companyId = (int)$office['company_id'];
                    } elseif ($companyId && $fallbackOfficeName !== '') {
                        // Fallback: resolve (or create) by name within the company
                        $stmtOff = $pdo->prepare("SELECT id FROM offices WHERE company_id = ? AND LOWER(name) = LOWER(?) LIMIT 1");
                        $stmtOff->execute([$companyId, $fallbackOfficeName]);
                        $officeId = $stmtOff->fetchColumn() ?: null;

                        if (!$officeId) {
                            $stmtOffIns = $pdo->prepare("INSERT INTO offices (company_id, name) VALUES (?, ?)");
                            $stmtOffIns->execute([$companyId, $fallbackOfficeName]);
                            $officeId = $pdo->lastInsertId();
                        }
                    }

                    // A supervisor belongs permanently to their office
                    if ($currentOfficeId !== null && $officeId !== null && $currentOfficeId !== $officeId) {
                        throw new Exception("A supervisor cannot be moved to a different office. Each office has its own supervisor.");
                    }

                    // One supervisor per office
                    if ($officeId !== null && $currentOfficeId !== $officeId) {
                        $stmtExisting = $pdo->prepare("SELECT id FROM supervisors WHERE office_id = ? AND user_id <> ? LIMIT 1");
                        $stmtExisting->execute([$officeId, $userId]);

                        if ($stmtExisting->fetchColumn()) {
                            throw new Exception("That office already has its own supervisor.");
                        }
                    }

                    $pdo->prepare("UPDATE supervisors SET company_id = ?, office_id = ?, job_title = ?, contact_number = ? WHERE user_id = ?")

                        ->execute([

                            $companyId,

                            $officeId ?? $currentOfficeId,

                            $jobTitle !== '' ? $jobTitle : null,

                            $contactNumber !== '' ? $contactNumber : null,

                            $userId

                        ]);

                    $pdo->commit();

                }



                $_SESSION['flash_success'] = "User details updated successfully.";

            } catch (Exception $e) {

                if ($pdo->inTransaction()) {

                    $pdo->rollBack();

                }

                $_SESSION['flash_error'] = "Update failed: " . $e->getMessage();

            }

        }

        header("Location: users.php?tab=" . $redirectTab);

        exit();

    }



    // Action D2: Edit Company

    if ($action === 'edit_company') {

        $companyId = (int)($_POST['company_id'] ?? 0);

        // Company name/address are read-only here; only new offices are added
        $stmtCompany = $pdo->prepare("SELECT name, address FROM companies WHERE id = ? LIMIT 1");

        $stmtCompany->execute([$companyId]);

        $companyRow = $stmtCompany->fetch(PDO::FETCH_ASSOC) ?: [];

        $companyName = (string)($companyRow['name'] ?? '');

        $address     = (string)($companyRow['address'] ?? '');

        // One new office + its supervisor (flat field names from the form)
        $newOfficeName = trim($_POST['office_name'] ?? '');

        $newSupName    = trim($_POST['supervisor_name'] ?? '');

        $newSupMail    = strtolower(trim($_POST['supervisor_email'] ?? ''));

        $newSupJob     = trim($_POST['job_title'] ?? '');

        $newSupContact = trim($_POST['contact_number'] ?? '');



        if ($companyId <= 0 || $companyName === '') {

            $_SESSION['flash_error'] = "Company name is required.";

        } elseif (mb_strlen($companyName) > 255 || mb_strlen($address) > 255) {

            $_SESSION['flash_error'] = "Company name and address must each be 255 characters or fewer.";

        } else {

            try {

                $pdo->beginTransaction();

                // Block a rename that collides with another company's name
                // Company identity is not edited from here; nothing to update on the company row.



                // ---- Add one new office with its own supervisor ----

                $addedOffices = 0;

                $addedSupervisors = [];

                if ($newOfficeName !== '') {

                    $newName = $newOfficeName;

                    if (mb_strlen($newName) > 255) {

                        throw new Exception("Office name must be 255 characters or fewer.");
                    }

                    $supName = $newSupName;

                    $supMail = $newSupMail;

                    $supJob = $newSupJob;

                    $supContact = $newSupContact;

                    if ($supName === '' || $supMail === '') {

                        throw new Exception("Office '{$newName}' needs a supervisor name and email.");
                    }

                    if (!filter_var($supMail, FILTER_VALIDATE_EMAIL)) {

                        throw new Exception("Supervisor email '{$supMail}' is not a valid email address.");
                    }

                    $contactIssue = validateSupervisorContact($supJob, $supContact);

                    if ($contactIssue !== '') {

                        throw new Exception($contactIssue);
                    }

                    // Office must be new within this company
                    $chkOfficeDup = $pdo->prepare("SELECT id FROM offices WHERE company_id = ? AND LOWER(name) = LOWER(?) LIMIT 1");

                    $chkOfficeDup->execute([$companyId, $newName]);

                    if ($chkOfficeDup->fetchColumn()) {

                        throw new Exception("Office '{$newName}' already exists under this company.");
                    }

                    $pdo->prepare("INSERT INTO offices (company_id, name, address) VALUES (?, ?, ?)")
                        ->execute([$companyId, $newName, $address !== '' ? $address : null]);

                    $newOfficeId = (int)$pdo->lastInsertId();

                    // The email must not belong to a student or coordinator account
                    $chkSupRole = $pdo->prepare("SELECT id, role FROM users WHERE email = ? LIMIT 1");

                    $chkSupRole->execute([$supMail]);

                    $existingSupUser = $chkSupRole->fetch(PDO::FETCH_ASSOC);

                    if ($existingSupUser && ($existingSupUser['role'] ?? '') !== 'supervisor') {

                        throw new Exception("Email '{$supMail}' is already a {$existingSupUser['role']} account.");
                    }

                    if ($existingSupUser) {

                        $supUserId = (int)$existingSupUser['id'];

                        $pdo->prepare("UPDATE users SET name = ?, role = 'supervisor', status = 'active', archived_at = NULL WHERE id = ?")
                            ->execute([$supName, $supUserId]);

                    } else {

                        $pdo->prepare("INSERT INTO users (name, email, role, status, created_at) VALUES (?, ?, 'supervisor', 'active', NOW())")
                            ->execute([$supName, $supMail]);

                        $supUserId = (int)$pdo->lastInsertId();

                    }

                    // This supervisor cannot already run another office
                    $stmtPriorOffice = $pdo->prepare("SELECT id FROM supervisors WHERE user_id = ? AND office_id IS NOT NULL LIMIT 1");

                    $stmtPriorOffice->execute([$supUserId]);

                    if ($stmtPriorOffice->fetchColumn()) {

                        throw new Exception("Supervisor '{$supName}' is already assigned to another office.");
                    }

                    $pdo->prepare("
                        INSERT INTO supervisors (user_id, company_id, office_id, job_title, contact_number)
                        VALUES (?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                            company_id = VALUES(company_id),
                            office_id = VALUES(office_id),
                            job_title = COALESCE(VALUES(job_title), job_title),
                            contact_number = COALESCE(VALUES(contact_number), contact_number)
                    ")->execute([
                        $supUserId,
                        $companyId,
                        $newOfficeId,
                        $supJob !== '' ? $supJob : null,
                        $supContact !== '' ? $supContact : null
                    ]);

                    $addedOffices++;
                    $addedSupervisors[] = ['email' => $supMail, 'name' => $supName];
                }

                $pdo->commit();



                logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_UPDATED', "Updated company '{$companyName}' (ID {$companyId}).");

                foreach ($addedSupervisors as $newRecipient) {

                    $mailer->sendWelcomeEmail($newRecipient['email'], $newRecipient['name'], 'supervisor');

                }

                $_SESSION['flash_success'] = $addedOffices > 0
                    ? "Added {$addedOffices} office(s) with their supervisors to '{$companyName}'."
                    : "Company '{$companyName}' updated successfully.";

            } catch (Exception $e) {

                if ($pdo->inTransaction()) {

                    $pdo->rollBack();

                }

                $_SESSION['flash_error'] = "Failed to update company: " . $e->getMessage();

            }

        }

        header("Location: users.php?tab=companies");

        exit();

    }



    // Action D3: Archive Company

    if ($action === 'archive_company') {

        $companyId = (int)($_POST['company_id'] ?? 0);

        if ($companyId <= 0) {

            $_SESSION['flash_error'] = "Invalid company selected.";

            header("Location: users.php?tab=companies");

            exit();

        }

        try {

            $stmtName = $pdo->prepare("SELECT name, status FROM companies WHERE id = ?");

            $stmtName->execute([$companyId]);

            $company = $stmtName->fetch(PDO::FETCH_ASSOC);

            if (!$company) {

                $_SESSION['flash_error'] = "Company record could not be found.";

            } elseif (($company['status'] ?? 'active') !== 'active') {

                $_SESSION['flash_error'] = "Company '{$company['name']}' is already archived.";

            } else {

                // Guard: block archiving while interns are still assigned to this company's offices
                $stmtAssigned = $pdo->prepare("
                    SELECT COUNT(s.id) AS assigned_count
                    FROM students s
                    JOIN offices o ON s.office_id = o.id
                    WHERE o.company_id = ?
                      AND s.office_id IS NOT NULL
                ");

                $stmtAssigned->execute([$companyId]);

                $assignedCount = (int)$stmtAssigned->fetchColumn();

                if ($assignedCount > 0) {

                    $_SESSION['flash_error'] = "Cannot archive '{$company['name']}': {$assignedCount} intern(s) are still assigned to its offices. Unassign them first.";

                } else {

                    $stmtArchive = $pdo->prepare("
                        UPDATE companies
                        SET status = 'archived', archived_at = NOW()
                        WHERE id = ?
                    ");

                    $stmtArchive->execute([$companyId]);

                    logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_ARCHIVED', "Archived company '{$company['name']}' (ID {$companyId}).");

                    $_SESSION['flash_success'] = "Company '{$company['name']}' archived successfully.";

                }

            }

        } catch (Exception $e) {

            $_SESSION['flash_error'] = "Failed to archive company: " . $e->getMessage();

        }

        header("Location: users.php?tab=companies");

        exit();

    }



    // Action D4: Restore Company

    if ($action === 'restore_company') {

        $companyId = (int)($_POST['company_id'] ?? 0);

        if ($companyId > 0) {

            try {

                $stmtName = $pdo->prepare("SELECT name FROM companies WHERE id = ?");

                $stmtName->execute([$companyId]);

                $company = $stmtName->fetch(PDO::FETCH_ASSOC);

                $pdo->prepare("
                    UPDATE companies
                    SET status = 'active', archived_at = NULL
                    WHERE id = ?
                ")->execute([$companyId]);

                logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_RESTORED', "Restored company '{$company['name']}' (ID {$companyId}).");

                $_SESSION['flash_success'] = "Company '{$company['name']}' restored to active status.";

            } catch (Exception $e) {

                $_SESSION['flash_error'] = "Failed to restore company: " . $e->getMessage();

            }

        }

        header("Location: users.php?tab=archived");

        exit();

    }



    // Action D5: Permanently Delete Company (archived only, must be empty)

    if ($action === 'delete_company_permanently') {

        $companyId = (int)($_POST['company_id'] ?? 0);

        if ($companyId <= 0) {

            $_SESSION['flash_error'] = "Invalid company selected.";

            header("Location: users.php?tab=archived");

            exit();

        }

        try {

            $stmtName = $pdo->prepare("SELECT name, status FROM companies WHERE id = ?");

            $stmtName->execute([$companyId]);

            $company = $stmtName->fetch(PDO::FETCH_ASSOC);

            if (!$company) {

                $_SESSION['flash_error'] = "Company record could not be found.";

            } elseif (($company['status'] ?? 'active') !== 'archived') {

                $_SESSION['flash_error'] = "Only archived companies can be permanently deleted. Archive '{$company['name']}' first.";

            } else {

                // Guard: refuse deletion while anything is still linked
                $stmtLinks = $pdo->prepare("
                    SELECT
                        (SELECT COUNT(*) FROM supervisors sup WHERE sup.company_id = ?)
                        +
                        (SELECT COUNT(*) FROM supervisors sup WHERE sup.office_id IN (SELECT o.id FROM offices o WHERE o.company_id = ?))
                        AS linked_supervisors,
                        (SELECT COUNT(*) FROM students s WHERE s.company_id = ?)
                        +
                        (SELECT COUNT(*) FROM students s WHERE s.office_id IN (SELECT o.id FROM offices o WHERE o.company_id = ?))
                        AS linked_students
                ");

                $stmtLinks->execute([$companyId, $companyId, $companyId, $companyId]);

                $links = $stmtLinks->fetch(PDO::FETCH_ASSOC) ?: [];

                $linkedSupervisors = (int)($links['linked_supervisors'] ?? 0);

                $linkedStudents    = (int)($links['linked_students'] ?? 0);

                if ($linkedSupervisors > 0 || $linkedStudents > 0) {

                    $parts = [];

                    if ($linkedSupervisors > 0) {

                        $parts[] = "{$linkedSupervisors} supervisor(s)";

                    }

                    if ($linkedStudents > 0) {

                        $parts[] = "{$linkedStudents} intern(s)";

                    }

                    $_SESSION['flash_error'] = "Cannot delete '{$company['name']}': still linked to " . implode(' and ', $parts) . ". Unlink them first.";

                } else {

                    // Offices are removed automatically via ON DELETE CASCADE
                    $pdo->prepare("DELETE FROM companies WHERE id = ?")->execute([$companyId]);

                    logActivity($pdo, $coordinatorId, 'coordinator', 'COMPANY_DELETED', "Permanently deleted company '{$company['name']}' (ID {$companyId}).");

                    $_SESSION['flash_success'] = "Company '{$company['name']}' permanently deleted.";

                }

            }

        } catch (Exception $e) {

            $_SESSION['flash_error'] = "Failed to delete company: " . $e->getMessage();

        }

        header("Location: users.php?tab=archived");

        exit();

    }



    // Action D6: Delete Office

    if ($action === 'delete_office') {

        $officeId = (int)($_POST['office_id'] ?? 0);

        if ($officeId <= 0) {

            $_SESSION['flash_error'] = "Invalid office selected.";

            header("Location: users.php?tab=companies");

            exit();

        }

        try {

            $stmtOffice = $pdo->prepare("
                SELECT o.id, o.name AS office_name, c.name AS company_name
                FROM offices o
                JOIN companies c ON c.id = o.company_id
                WHERE o.id = ?
                LIMIT 1
            ");

            $stmtOffice->execute([$officeId]);

            $office = $stmtOffice->fetch(PDO::FETCH_ASSOC);

            if (!$office) {

                $_SESSION['flash_error'] = "Office record could not be found.";

            } else {

                // Guard: refuse deletion while anything is still linked to the office
                $stmtLinks = $pdo->prepare("
                    SELECT
                        (SELECT COUNT(*) FROM supervisors sup WHERE sup.office_id = ?) AS linked_supervisors,
                        (SELECT COUNT(*) FROM students s WHERE s.office_id = ?) AS linked_students
                ");

                $stmtLinks->execute([$officeId, $officeId]);

                $links = $stmtLinks->fetch(PDO::FETCH_ASSOC) ?: [];

                $linkedSupervisors = (int)($links['linked_supervisors'] ?? 0);

                $linkedStudents    = (int)($links['linked_students'] ?? 0);

                if ($linkedSupervisors > 0 || $linkedStudents > 0) {

                    $parts = [];

                    if ($linkedSupervisors > 0) {

                        $parts[] = "{$linkedSupervisors} supervisor(s)";

                    }

                    if ($linkedStudents > 0) {

                        $parts[] = "{$linkedStudents} intern(s)";

                    }

                    $_SESSION['flash_error'] = "Cannot delete office '{$office['office_name']}': still linked to " . implode(' and ', $parts) . ". Unlink them first.";

                } else {

                    $pdo->prepare("DELETE FROM offices WHERE id = ?")->execute([$officeId]);

                    logActivity($pdo, $coordinatorId, 'coordinator', 'OFFICE_DELETED', "Deleted office '{$office['office_name']}' at '{$office['company_name']}' (ID {$officeId}).");

                    $_SESSION['flash_success'] = "Office '{$office['office_name']}' deleted successfully.";

                }

            }

        } catch (Exception $e) {

            $_SESSION['flash_error'] = "Failed to delete office: " . $e->getMessage();

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

$archivedCompanies = [];

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

            s.office_id,

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

            sup.office_id,

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

        WHERE c.status = 'active'

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



    // Query Offices (for placement dropdowns and the office-grouped company cards)
    $stmtOffices = $pdo->query("
        SELECT
            o.id,
            o.name AS office_name,
            o.company_id,
            c.name AS company_name,
            (SELECT COUNT(*) FROM supervisors sup WHERE sup.office_id = o.id) AS linked_supervisors,
            (SELECT COUNT(*) FROM students s WHERE s.office_id = o.id) AS linked_students
        FROM offices o
        JOIN companies c ON o.company_id = c.id
        WHERE c.status = 'active'
        ORDER BY c.name ASC, o.name ASC
    ");
    $offices = $stmtOffices->fetchAll(PDO::FETCH_ASSOC) ?: [];



    // Group supervisors under their company; leftovers go to "Unassigned"

    $supervisorsByCompany   = [];

    $unassignedSupervisors  = [];

    $supervisorByOffice     = [];

    $knownCompanyIds        = array_map('intval', array_column($companies, 'id'));

    foreach ($supervisors as $sup) {

        $sup['interns'] = $internsBySupervisor[(int)$sup['id']] ?? [];

        $cid = (int)($sup['company_id'] ?? 0);

        if (!empty($sup['office_id'])) {

            $supervisorByOffice[(int)$sup['office_id']] = $sup;

        }

        if ($cid > 0 && in_array($cid, $knownCompanyIds, true)) {

            $supervisorsByCompany[$cid][] = $sup;

        } else {

            $unassignedSupervisors[] = $sup;

        }

    }



    // Group offices under their company, each carrying its single supervisor

    $officesByCompany = [];

    $orphanOffices    = [];

    foreach ($offices as $office) {

        $officeSupervisor = $supervisorByOffice[(int)$office['id']] ?? null;

        if ($officeSupervisor !== null) {

            $office['supervisor'] = $officeSupervisor;

            $office['interns']    = $officeSupervisor['interns'];

        } else {

            $office['supervisor'] = null;

            $office['interns']    = [];

        }

        $office['intern_count'] = count($office['interns']);

        $cid = (int)($office['company_id'] ?? 0);

        if ($cid > 0 && in_array($cid, $knownCompanyIds, true)) {

            $officesByCompany[$cid][] = $office;

        } else {

            $orphanOffices[] = $office;

        }

    }

    usort($orphanOffices, static function ($a, $b) {

        return strcasecmp((string)$a['office_name'], (string)$b['office_name']);

    });



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



    // Archived Companies
    $stmtArchivedCompanies = $pdo->query("
        SELECT
            c.id,
            c.name,
            c.department,
            c.address,
            c.archived_at
        FROM companies c
        WHERE c.status = 'archived'
        ORDER BY c.archived_at DESC
    ");
    $archivedCompanies = $stmtArchivedCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];



} catch (Exception $e) {

    error_log("Database Error in coordinator/users.php: " . $e->getMessage());

}



require_once __DIR__ . '/../src/pages/coordinator/usersPage.php';