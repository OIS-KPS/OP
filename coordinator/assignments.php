<?php
// coordinator/assignments.php
session_start();

require_once __DIR__ . '/../config/db.php';

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

// 2. Handle POST Actions (Assign / Unlink Placement)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_student'])) {
    $studentId    = intval($_POST['student_id'] ?? 0);
    $companyId    = !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;
    $supervisorId = !empty($_POST['supervisor_id']) ? intval($_POST['supervisor_id']) : null;
    $actionType   = $_POST['action_type'] ?? 'assign';

    if ($studentId > 0) {
        try {
            if ($actionType === 'unassign') {
                $stmt = $pdo->prepare("UPDATE students SET company_id = NULL, supervisor_id = NULL WHERE id = ?");
                $stmt->execute([$studentId]);
                logActivity($pdo, $coordinatorId, 'coordinator', 'PLACEMENT_UNLINKED', "Removed placement link for student ID {$studentId}.");
                $_SESSION['flash_success'] = "Student placement unlinked successfully.";
            } else {
                if ($supervisorId && $companyId) {
                    $stmt = $pdo->prepare("UPDATE students SET company_id = ?, supervisor_id = ? WHERE id = ?");
                    $stmt->execute([$companyId, $supervisorId, $studentId]);
                    logActivity($pdo, $coordinatorId, 'coordinator', 'STUDENT_ASSIGNED', "Assigned student ID {$studentId} to company ID {$companyId} and supervisor ID {$supervisorId}.");
                    $_SESSION['flash_success'] = "Student placement updated successfully.";
                } else {
                    $_SESSION['flash_error'] = "Please select both a company and a supervisor.";
                }
            }
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Failed to update placement: " . $e->getMessage();
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
            u.name,
            u.email,
            u.avatar_url,
            c.name AS company_name,
            c.department AS company_dept,
            u_sup.name AS supervisor_name
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        {$whereStudentSql}
        ORDER BY s.section ASC, u.name ASC
    ";
    $stmtStd = $pdo->prepare($sqlStudents);
    $stmtStd->execute($stdParams);
    $students = $stmtStd->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Query Companies
    $stmtComp = $pdo->query("SELECT id, name, department FROM companies ORDER BY name ASC");
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

} catch (PDOException $e) {
    error_log("Assignments Query Error: " . $e->getMessage());
}

require_once __DIR__ . '/../src/pages/coordinator/assignmentsPage.php';