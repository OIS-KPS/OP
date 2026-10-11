<?php
// coordinator/assignments.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$coordinatorId   = $_SESSION['user_id'];
$selectedSection = $_GET['section'] ?? 'all';
$success         = $_SESSION['flash_success'] ?? null;
$error           = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// CSRF Guard: covers the whole POST surface of this controller.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify(__DIR__ . '/assignments.php');
}

// 2. Handle POST Actions (Assign / Unlink Placement)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_student'])) {
        $studentId  = intval($_POST['student_id'] ?? 0);
        $officeId   = !empty($_POST['office_id']) ? intval($_POST['office_id']) : null;
        $actionType = $_POST['action_type'] ?? 'assign';

        if ($studentId > 0) {
            try {
                if ($actionType === 'unassign') {
                    $stmt = $pdo->prepare("UPDATE students SET office_id = NULL, company_id = NULL, supervisor_id = NULL WHERE id = ?");
                    $stmt->execute([$studentId]);
                    logActivity($pdo, $coordinatorId, 'coordinator', 'PLACEMENT_UNLINKED', "Removed placement link for student ID {$studentId}.");
                    $_SESSION['flash_success'] = "Student placement unlinked successfully.";
                } else {
                    if ($officeId) {
                        // Resolve company + supervisor from the office (1 supervisor per office)
                        $stmtOff = $pdo->prepare("
                            SELECT o.company_id, sup.id AS supervisor_id
                            FROM offices o
                            LEFT JOIN supervisors sup ON sup.office_id = o.id
                            WHERE o.id = ?
                        ");
                        $stmtOff->execute([$officeId]);
                        $office = $stmtOff->fetch(PDO::FETCH_ASSOC);

                        if ($office && $office['company_id']) {
                            $companyId    = $office['company_id'];
                            $supervisorId = $office['supervisor_id'] ?: null;

                            if (!$supervisorId) {
                                $_SESSION['flash_error'] = "The selected office has no supervisor yet. Add one first.";
                            } else {
                                $stmt = $pdo->prepare("UPDATE students SET office_id = ?, company_id = ?, supervisor_id = ? WHERE id = ?");
                                $stmt->execute([$officeId, $companyId, $supervisorId, $studentId]);
                                logActivity($pdo, $coordinatorId, 'coordinator', 'STUDENT_ASSIGNED', "Assigned student ID {$studentId} to office ID {$officeId} (company ID {$companyId}, supervisor ID {$supervisorId}).");
                                $_SESSION['flash_success'] = "Student placement updated successfully.";
                            }
                        } else {
                            $_SESSION['flash_error'] = "Please select a valid office.";
                        }
                    } else {
                        $_SESSION['flash_error'] = "Please select an office.";
                    }
                }
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Failed to update placement: " . userFacingError($e, 'assignments.php:assign');
            }
        }
    }
    header("Location: assignments.php" . ($selectedSection !== 'all' ? "?section=" . urlencode($selectedSection) : ""));
    exit();
}

// 3. Fetch Data
$students       = [];
$companies      = [];
$supervisors    = [];
$activeSections = [];

try {
    // Distinct Sections
    $stmtSec = $pdo->query("SELECT DISTINCT COALESCE(NULLIF(section, ''), 'A') AS sec FROM students ORDER BY sec ASC");
    $activeSections = $stmtSec->fetchAll(PDO::FETCH_COLUMN) ?: ['A', 'B', 'C'];

    // Query Students
    $whereStudent = ["u.status = 'active'"];
    $stdParams = [];

    if ($selectedSection !== 'all') {
        $whereStudent[] = "s.section = :sec";
        $stdParams['sec'] = $selectedSection;
    }

    $whereStudentSql = "WHERE " . implode(' AND ', $whereStudent);

    $sqlStudents = "
        SELECT 
            s.id,
            s.student_number,
            s.program,
            COALESCE(s.section, 'A') AS section,
            s.company_id,
            s.supervisor_id,
            s.office_id,
            u.name,
            u.email,
            u.avatar_url,
            c.name AS company_name,
            c.department AS company_dept,
            o.name AS office_name,
            u_sup.name AS supervisor_name
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN offices o ON s.office_id = o.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        {$whereStudentSql}
        ORDER BY s.section ASC, u.name ASC
    ";
    $stmtStd = $pdo->prepare($sqlStudents);
    $stmtStd->execute($stdParams);
    $students = $stmtStd->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Query Companies
    $stmtComp = $pdo->query("SELECT id, name, department FROM companies WHERE status = 'active' ORDER BY name ASC");
    $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Query Supervisors
    $stmtSup = $pdo->query("
        SELECT sup.id, sup.company_id, u.name, u.email 
        FROM supervisors sup
        JOIN users u ON sup.user_id = u.id
        WHERE u.status = 'active'
        ORDER BY u.name ASC
    ");
    $supervisors = $stmtSup->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Query Offices (for placement dropdowns)
    $stmtOffices = $pdo->query("
        SELECT
            o.id,
            o.name AS office_name,
            o.company_id,
            c.name AS company_name
        FROM offices o
        JOIN companies c ON o.company_id = c.id
        WHERE c.status = 'active'
        ORDER BY c.name ASC, o.name ASC
    ");
    $offices = $stmtOffices->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (PDOException $e) {
    error_log("Assignments Query Error: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/coordinator/assignmentsPage.php';