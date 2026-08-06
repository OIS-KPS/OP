<?php
// coordinator/users.php
session_start();

// 1. Load Composer dependencies (PHPMailer & Dotenv)
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/services/MailerService.php';

use services\MailerService;

// 2. Load .env variables into $_ENV
if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

// Active Tab: 'students', 'supervisors', or 'companies'
$tab = $_GET['tab'] ?? 'students';

// Message handling
$success = $_SESSION['success_msg'] ?? '';
$error   = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Handle Account Creation & Import POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // A. Handle Single Student Account Creation
    if ($action === 'create_student' || $action === 'create_single_user') {
        $mailer = new MailerService();

        $name           = trim($_POST['name'] ?? '');
        $student_number = trim($_POST['student_number'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $program        = 'BSIT';

        if (!empty($name) && !empty($student_number) && !empty($email)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO students (name, student_number, email, program, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $student_number, $email, $program]);

                // Send Welcome/Invitation Email
                $mailSent = $mailer->sendWelcomeEmail($email, $name, 'student');
                
                if ($mailSent) {
                    $_SESSION['success_msg'] = "Student account for {$name} created & invitation email sent!";
                } else {
                    $_SESSION['success_msg'] = "Student account created, but invitation email could not be sent (check SMTP settings).";
                }
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

    // B. Handle Single Supervisor Account Creation
    } elseif ($action === 'create_supervisor') {
        $mailer     = new MailerService();
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['supervisor_email'] ?? $_POST['email'] ?? '');
        $company_id = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;

        if (!empty($name) && !empty($email)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO supervisors (name, email, company_id, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $company_id]);

                // Send Welcome/Invitation Email
                $mailSent = $mailer->sendWelcomeEmail($email, $name, 'supervisor');

                if ($mailSent) {
                    $_SESSION['success_msg'] = "Supervisor account for {$name} created & invitation email sent!";
                } else {
                    $_SESSION['success_msg'] = "Supervisor account created, but invitation email could not be sent (check SMTP settings).";
                }
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

    // C. Handle Bulk Excel (.xlsx / .csv) Student Import
    } elseif ($action === 'bulk_import_students') {
        $mailer = new MailerService();

        if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
            $filePath = $_FILES['excel_file']['tmp_name'];
            
            try {
                // Load Spreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $successCount   = 0;
                $emailSentCount = 0;
                $failCount      = 0;

                // Prepare PDO Statement
                $stmt = $pdo->prepare("INSERT INTO students (name, student_number, email, program, created_at) VALUES (?, ?, ?, ?, NOW())");

                // Loop through rows (Skip Row 1 header)
                $isHeader = true;
                foreach ($sheetData as $row) {
                    if ($isHeader) {
                        $isHeader = false;
                        continue;
                    }

                    $name           = trim($row['A'] ?? '');
                    $student_number = trim($row['B'] ?? '');
                    $email          = trim($row['C'] ?? '');
                    $program        = !empty($row['D']) ? trim($row['D']) : 'BSIT';

                    if (!empty($name) && !empty($student_number) && !empty($email)) {
                        try {
                            $stmt->execute([$name, $student_number, $email, $program]);
                            $successCount++;

                            // Send welcome invitation email with a 0.2s micro-delay
                            $mailSent = $mailer->sendWelcomeEmail($email, $name, 'student');
                            if ($mailSent) {
                                $emailSentCount++;
                            }
                            
                            usleep(200000); // 0.2 seconds pause between emails
                        } catch (PDOException $e) {
                            $failCount++; // Skip duplicate ID or Email
                        }
                    }
                }

                $_SESSION['success_msg'] = "Bulk import completed! {$successCount} students added ({$emailSentCount} emails sent successfully). " . ($failCount > 0 ? "({$failCount} skipped due to duplicates)." : "");

            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Error parsing Excel file: " . $e->getMessage();
            }
        } else {
            $_SESSION['error_msg'] = "Please select a valid Excel (.xlsx / .xls / .csv) file to upload.";
        }

        header("Location: users.php?tab=students");
        exit();
    }
}

// Fetch Real Records from Database
try {
    $stmtStudents = $pdo->query("SELECT s.*, c.name AS company_name, sup.name AS supervisor_name FROM students s LEFT JOIN companies c ON s.company_id = c.id LEFT JOIN supervisors sup ON s.supervisor_id = sup.id ORDER BY s.id DESC");
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);

    $stmtSup = $pdo->query("SELECT sup.*, c.name AS company_name, COUNT(s.id) AS assigned_interns FROM supervisors sup LEFT JOIN companies c ON sup.company_id = c.id LEFT JOIN students s ON s.supervisor_id = sup.id GROUP BY sup.id ORDER BY sup.id DESC");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC);

    $stmtComp = $pdo->query("SELECT c.*, COUNT(s.id) AS total_interns FROM companies c LEFT JOIN students s ON s.company_id = c.id GROUP BY c.id ORDER BY c.id DESC");
    $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['error_msg'] = "Failed to fetch records: " . $e->getMessage();
    $students = [];
    $supervisors = [];
    $companies = [];
}

require_once __DIR__ . '/../src/pages/coordinator/usersPage.php';