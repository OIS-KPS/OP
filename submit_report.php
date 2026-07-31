<?php
// submit_report.php
session_start();

require_once __DIR__ . '/config/db.php';

// Auth Guard (comment out if testing without session login)
$student_id = $_SESSION['student_id'] ?? 1;
$weekNumber = isset($_GET['week']) ? intval($_GET['week']) : 1;

$errors = [];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filePath = null;

    // Validate File Upload
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

    // Save Record to Database
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO reports (student_id, week_number, attachment_path, status, created_at)
                    VALUES (?, ?, ?, 'Pending', NOW())
                    ON DUPLICATE KEY UPDATE 
                        attachment_path = VALUES(attachment_path),
                        status = 'Pending',
                        created_at = NOW()";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$student_id, $weekNumber, $filePath]);

            // Redirect back to My Reports with success alert
            header("Location: reports.php?submitted=success");
            exit();

        } catch (Exception $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

// Render View Template
require_once __DIR__ . '/src/pages/student/submitReportPage.php';