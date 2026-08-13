<?php
// submit_report.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

// Resolve student_id dynamically
$userId = $_SESSION['user_id'];
$stmtStudent = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
$stmtStudent->execute([$userId]);
$studentRecord = $stmtStudent->fetch(PDO::FETCH_ASSOC);
$student_id = $studentRecord['id'] ?? ($_SESSION['student_id'] ?? null);

$weekNumber = isset($_GET['week']) ? intval($_GET['week']) : 1;
$errors = [];

// 🔒 BACKEND GUARD: Prevent modifying an already APPROVED report
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

            // Cleanup previous file if replacing a pending/needs revision report
            $stmtCheck = $pdo->prepare("SELECT file_path FROM reports WHERE student_id = ? AND week_number = ?");
            $stmtCheck->execute([$student_id, $weekNumber]);
            $existingReport = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingReport && !empty($existingReport['file_path'])) {
                $oldFileDiskPath = __DIR__ . '/' . $existingReport['file_path'];
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
            $sql = "INSERT INTO reports (student_id, week_number, file_path, status, submitted_at)
                    VALUES (?, ?, ?, 'pending', NOW())
                    ON DUPLICATE KEY UPDATE 
                        file_path = VALUES(file_path),
                        status = 'pending',
                        submitted_at = NOW()";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$student_id, $weekNumber, $filePath]);

            header("Location: reports.php?submitted=success");
            exit();

        } catch (Exception $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

// Render View
require_once __DIR__ . '/src/pages/student/submitReportPage.php';