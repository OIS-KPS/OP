<?php
// submit_report.php
session_start();
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/report_deadlines.php';
require_once __DIR__ . '/config/csrf.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: auth/login.php");
    exit();
}

// CSRF Guard: covers the report upload branch below. The fallback keeps the
// requested week so a rejected submission does not reset the form to Week 1.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfWeek = intval($_GET['week'] ?? $_POST['week'] ?? 1);
    csrf_verify('submit_report.php?week=' . ($csrfWeek > 0 ? $csrfWeek : 1), 'error_message');
}

$sessionUserId = (int)$_SESSION['user_id'];

// Resolve student profile and check evaluation trigger flag
$stmtStudent = $pdo->prepare("
    SELECT 
        s.id, 
        u.name, 
        s.student_number, 
        s.company_id,
        s.evaluation_triggered,
        s.ojt_start_date,
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

// ONLY lock if the coordinator specifically triggered the evaluation
$isLocked = !empty($studentRow['evaluation_triggered']) && (int)$studentRow['evaluation_triggered'] === 1;

// Block report submission until the student has an assigned placement
$isAssigned = !empty($studentRow['company_id']);

$student_id  = (int)$studentRow['id'];
$studentName = $studentRow['name'] ?? 'Student';
$weekNumber  = isset($_GET['week']) ? intval($_GET['week']) : (isset($_POST['week']) ? intval($_POST['week']) : 1);
$errors      = [];

$ojtStart    = isset($studentRow) && $studentRow ? warOjtStart($studentRow) : null;
$weekDue     = $ojtStart ? warWeekDue($ojtStart, $weekNumber) : null;
$isLatePreview = $weekDue ? ((new DateTimeImmutable()) > $weekDue) : false;

// Prevent modifying an already APPROVED report
$stmtCheckApproved = $pdo->prepare("SELECT status FROM reports WHERE student_id = ? AND week_number = ?");
$stmtCheckApproved->execute([$student_id, $weekNumber]);
$existingStatus = strtolower($stmtCheckApproved->fetchColumn() ?: '');

if ($existingStatus === 'approved') {
    $_SESSION['error_message'] = "Week {$weekNumber} report has already been approved and cannot be re-uploaded.";
    header("Location: reports.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAssigned) {
    if ($isLocked) {
        $_SESSION['error_message'] = "Account locked for final evaluation.";
        header("Location: reports.php");
        exit();
    }

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
    } elseif (isset($_FILES['report_file']) && in_array(
        $_FILES['report_file']['error'],
        [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE],
        true
    )) {
        // The drop zone used to promise "Max size 10MB" and nothing enforced it --
        // not the browser, not here. That claim is gone from the copy, but PHP's own
        // upload_max_filesize still caps every upload, and an oversize file reports
        // as a size failure here. Without this branch it fell through to the
        // generic message below, which tells the intern to pick a different file
        // format -- blaming the wrong thing entirely for a size problem.
        $maxUpload = ini_get('upload_max_filesize');
        $errors[] = "That file is larger than this server accepts ({$maxUpload}). Please compress it or upload a smaller PDF.";
    } else {
        $errors[] = "Please select a PDF file to upload.";
    }

    if (empty($errors)) {
        try {
            $stmtCheckRow = $pdo->prepare("SELECT id, file_path, previous_file_path FROM reports WHERE student_id = ? AND week_number = ?");
            $stmtCheckRow->execute([$student_id, $weekNumber]);
            $existingReport = $stmtCheckRow->fetch(PDO::FETCH_ASSOC);

            if ($existingReport) {
                $archivedOldFile = !empty($existingReport['file_path']) ? $existingReport['file_path'] : $existingReport['previous_file_path'];

                // Recompute timeliness for this new submission.
                $submittedAt = new DateTimeImmutable();
                $isLate = ($ojtStart && $submittedAt > warWeekDue($ojtStart, $weekNumber)) ? 1 : 0;
                $deadlineDb = $ojtStart ? warWeekDue($ojtStart, $weekNumber)->setTime(0, 0, 0)->format('Y-m-d') : null;

                $stmtUpdate = $pdo->prepare("
                    UPDATE reports 
                    SET previous_file_path = ?,
                        file_path = ?, 
                        status = 'pending', 
                        submitted_at = NOW(),
                        is_late = ?,
                        deadline_date = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$archivedOldFile, $filePath, $isLate, $deadlineDb, $existingReport['id']]);

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
                $submittedAt = new DateTimeImmutable();
                $isLate = ($ojtStart && $submittedAt > warWeekDue($ojtStart, $weekNumber)) ? 1 : 0;
                $deadlineDb = $ojtStart ? warWeekDue($ojtStart, $weekNumber)->setTime(0, 0, 0)->format('Y-m-d') : null;

                $stmtInsert = $pdo->prepare("
                    INSERT INTO reports (student_id, week_number, file_path, status, submitted_at, is_late, deadline_date)
                    VALUES (?, ?, ?, 'pending', NOW(), ?, ?)
                ");
                $stmtInsert->execute([$student_id, $weekNumber, $filePath, $isLate, $deadlineDb]);

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

// Surface the session-level errors this controller writes at the approval check,
// the lock check and the CSRF guard. They used to be stored and never read, so
// the intern saw a blank page with no explanation. Unset either way so a stale
// message cannot survive into the next submission.
if (!empty($_SESSION['error_message'])) {
    $errors[] = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

require_once __DIR__ . '/src/pages/student/submitReportPage.php';