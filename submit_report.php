<?php
// submit_report.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$sessionUserId = (int)$_SESSION['user_id'];

// Resolve student profile and check evaluation state (Joining users for name)
$stmtStudent = $pdo->prepare("
    SELECT 
        s.id, 
        u.name, 
        s.student_number, 
        e.id AS evaluation_id 
    FROM students s 
    INNER JOIN users u ON s.user_id = u.id
    LEFT JOIN evaluations e ON e.student_id = s.id
    WHERE s.user_id = ? 
    LIMIT 1
");
$stmtStudent->execute([$sessionUserId]);
$studentRow = $stmtStudent->fetch(PDO::FETCH_ASSOC);

if (!$studentRow) {
    $_SESSION['error_message'] = "Student profile record not found.";
    header("Location: reports.php");
    exit();
}

// Lock reports if already evaluated
if (!empty($studentRow['evaluation_id'])) {
    $_SESSION['error_message'] = "Your final evaluation has already been completed. No further report modifications are allowed.";
    header("Location: reports.php");
    exit();
}

$student_id  = (int)$studentRow['id'];
$studentName = $studentRow['name'] ?? 'Student';
$weekNumber  = isset($_GET['week']) ? intval($_GET['week']) : (isset($_POST['week']) ? intval($_POST['week']) : 1);
$errors      = [];

// Prevent modifying an already APPROVED report
$stmtCheckApproved = $pdo->prepare("SELECT status FROM reports WHERE student_id = ? AND week_number = ?");
$stmtCheckApproved->execute([$student_id, $weekNumber]);
$existingStatus = strtolower($stmtCheckApproved->fetchColumn() ?: '');

if ($existingStatus === 'approved') {
    $_SESSION['error_message'] = "Week {$weekNumber} report has already been approved and cannot be re-uploaded.";
    header("Location: reports.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filePath = null;

    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['report_file']['tmp_name'];
        $fileName      = $_FILES['report_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension === 'pdf') {
            $uploadDir = __DIR__ . '/uploads/reports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = "WAR_Week_{$weekNumber}_Student_{$student_id}_" . time() . ".pdf";
            $destPath    = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $filePath = 'uploads/reports/' . $newFileName;
            } else {
                $errors[] = "Failed to save the uploaded file to the server.";
            }
        } else {
            $errors[] = "Invalid file format. Please upload a PDF file.";
        }
    } else {
        $errors[] = "Please select a PDF file to upload.";
    }

    if (empty($errors)) {
        try {
            // Check existing report for re-upload tracking
            $stmtCheckRow = $pdo->prepare("SELECT id, file_path, previous_file_path FROM reports WHERE student_id = ? AND week_number = ?");
            $stmtCheckRow->execute([$student_id, $weekNumber]);
            $existingReport = $stmtCheckRow->fetch(PDO::FETCH_ASSOC);

            if ($existingReport) {
                // Archive current file to previous_file_path so the flagged version remains trackable
                $archivedOldFile = !empty($existingReport['file_path']) ? $existingReport['file_path'] : $existingReport['previous_file_path'];

                $stmtUpdate = $pdo->prepare("
                    UPDATE reports 
                    SET previous_file_path = ?,
                        file_path = ?, 
                        status = 'pending', 
                        submitted_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$archivedOldFile, $filePath, $existingReport['id']]);

                // Direct insert to audit_logs (aligned with nbsc_ojt schema)
                $logDesc = "Student {$studentName} submitted revised Week {$weekNumber} accomplishment report.";
                $stmtLog = $pdo->prepare("
                    INSERT INTO audit_logs (user_id, role, action, description, ip_address, user_agent, created_at)
                    VALUES (?, 'student', 'REPORT_SUBMISSION', ?, ?, ?, NOW())
                ");
                $stmtLog->execute([
                    $sessionUserId,
                    $logDesc,
                    $_SERVER['REMOTE_ADDR'] ?? '::1',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);
            } else {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO reports (student_id, week_number, file_path, status, submitted_at)
                    VALUES (?, ?, ?, 'pending', NOW())
                ");
                $stmtInsert->execute([$student_id, $weekNumber, $filePath]);

                // Direct insert to audit_logs
                $logDesc = "Student {$studentName} submitted Week {$weekNumber} accomplishment report.";
                $stmtLog = $pdo->prepare("
                    INSERT INTO audit_logs (user_id, role, action, description, ip_address, user_agent, created_at)
                    VALUES (?, 'student', 'REPORT_SUBMISSION', ?, ?, ?, NOW())
                ");
                $stmtLog->execute([
                    $sessionUserId,
                    $logDesc,
                    $_SERVER['REMOTE_ADDR'] ?? '::1',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);
            }

            header("Location: reports.php?submitted=success");
            exit();

        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/src/pages/student/submitReportPage.php';