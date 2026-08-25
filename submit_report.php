<?php
// submit_report.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

$sessionUserId = $_SESSION['user_id'];

// Resolve student_id directly from students table using user_id
$stmtStudent = $pdo->prepare("SELECT id FROM students WHERE user_id = ? LIMIT 1");
$stmtStudent->execute([$sessionUserId]);
$student_id = $stmtStudent->fetchColumn();

if (!$student_id) {
    $_SESSION['error_message'] = "Student profile record not found.";
    header("Location: reports.php");
    exit();
}

$weekNumber = isset($_GET['week']) ? intval($_GET['week']) : 1;
$errors = [];

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

            // Remove existing file from disk if re-uploading
            $stmtCheck = $pdo->prepare("SELECT file_path FROM reports WHERE student_id = ? AND week_number = ?");
            $stmtCheck->execute([$student_id, $weekNumber]);
            $oldPath = $stmtCheck->fetchColumn();

            if ($oldPath) {
                $oldFileDiskPath = __DIR__ . '/' . ltrim($oldPath, '/');
                if (file_exists($oldFileDiskPath)) {
                    unlink($oldFileDiskPath);
                }
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
            $stmtCheckRow = $pdo->prepare("SELECT id FROM reports WHERE student_id = ? AND week_number = ?");
            $stmtCheckRow->execute([$student_id, $weekNumber]);
            $existingId = $stmtCheckRow->fetchColumn();

            if ($existingId) {
                $stmtUpdate = $pdo->prepare("
                    UPDATE reports 
                    SET file_path = ?, status = 'pending', submitted_at = NOW() 
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$filePath, $existingId]);
            } else {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO reports (student_id, week_number, file_path, status, submitted_at)
                    VALUES (?, ?, ?, 'pending', NOW())
                ");
                $stmtInsert->execute([$student_id, $weekNumber, $filePath]);
            }

            $logAction = $existingId ? 'REPORT_REUPLOAD' : 'REPORT_SUBMISSION';
            $logDesc   = "Student submitted Week {$weekNumber} accomplishment report.";
            
            logActivity($pdo, $sessionUserId, 'student', $logAction, $logDesc);

            header("Location: reports.php?submitted=success");
            exit();

        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/src/pages/student/submitReportPage.php';