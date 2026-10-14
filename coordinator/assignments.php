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
$selectedSection = ($_GET['section'] ?? 'all') === 'all'
    ? 'all'
    : normalizeSectionCode($_GET['section']);
$success         = $_SESSION['flash_success'] ?? null;
$error           = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// CSRF Guard: covers the whole POST surface of this controller.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify(__DIR__ . '/assignments.php');
}

// 2. Handle POST Actions (Assign / Unlink Placement)
// The placement picker submits several students at once (student_ids[]); the
// per-row "Edit Link" button still submits a single student_id.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_student'])) {
        $officeId   = !empty($_POST['office_id']) ? intval($_POST['office_id']) : null;
        $actionType = $_POST['action_type'] ?? 'assign';

        $rawIds    = $_POST['student_ids'] ?? ($_POST['student_id'] ?? null);
        $studentIds = is_array($rawIds) ? $rawIds : [$rawIds];
        $studentIds = array_values(array_unique(array_filter(array_map('intval', $studentIds), static function ($id) {
            return $id > 0;
        })));

        // Human-readable names for the flash message and the audit trail
        $studentNames = [];
        if (!empty($studentIds)) {
            $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
            $stmtNames    = $pdo->prepare("SELECT s.id, u.name FROM students s JOIN users u ON u.id = s.user_id WHERE s.id IN ($placeholders)");
            $stmtNames->execute($studentIds);
            foreach ($stmtNames->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $studentNames[(int)$row['id']] = $row['name'];
            }
        }

        // "Katelyn Coming" for one student, "3 student(s): A, B, C +1 more" for many
        $describe = static function (array $studentIds, array $studentNames): string {
            if (count($studentIds) === 1) {
                $only = reset($studentIds);
                return (string)($studentNames[$only] ?? "student ID {$only}");
            }
            $names = array_slice(array_values($studentNames), 0, 3);
            $more  = count($studentIds) - count($names);
            return count($studentIds) . ' student(s): ' . implode(', ', $names) . ($more > 0 ? " +{$more} more" : '');
        };

        if (empty($studentIds)) {
            $_SESSION['flash_error'] = "Please select at least one student.";
        } else {
            try {
                if ($actionType === 'unassign') {
                    $pdo->beginTransaction();

                    $stmt = $pdo->prepare("UPDATE students SET office_id = NULL, company_id = NULL, supervisor_id = NULL WHERE id = ?");
                    foreach ($studentIds as $sid) {
                        $stmt->execute([$sid]);
                    }

                    logActivity(
                        $pdo,
                        $coordinatorId,
                        'coordinator',
                        'PLACEMENT_UNLINKED',
                        "Removed placement link for " . $describe($studentIds, $studentNames) . '.'
                    );

                    $pdo->commit();

                    $_SESSION['flash_success'] = count($studentIds) === 1
                        ? "Student placement unlinked successfully."
                        : "Placements unlinked for " . count($studentIds) . " students.";
                } else {
                    if (!$officeId) {
                        $_SESSION['flash_error'] = "Please select an office.";
                    } else {
                        // Resolve company + supervisor from the office (1 supervisor per office)
                        $stmtOff = $pdo->prepare("
                            SELECT o.company_id, sup.id AS supervisor_id
                            FROM offices o
                            LEFT JOIN supervisors sup ON sup.office_id = o.id
                            WHERE o.id = ?
                        ");
                        $stmtOff->execute([$officeId]);
                        $office = $stmtOff->fetch(PDO::FETCH_ASSOC);

                        if (!$office || !$office['company_id']) {
                            $_SESSION['flash_error'] = "Please select a valid office.";
                        } elseif (!$office['supervisor_id']) {
                            $_SESSION['flash_error'] = "The selected office has no supervisor yet. Add one first.";
                        } else {
                            $companyId    = $office['company_id'];
                            $supervisorId = $office['supervisor_id'];

                            // Anchor OJT week 1 to the Monday of the current assignment week.
                            $assignmentMonday = (new DateTime('now'))->modify('-' . (((int)(new DateTime('now'))->format('N')) - 1) . ' days')->format('Y-m-d');

                            $pdo->beginTransaction();

                            $stmt = $pdo->prepare("UPDATE students SET office_id = ?, company_id = ?, supervisor_id = ?, ojt_start_date = ? WHERE id = ?");
                            foreach ($studentIds as $sid) {
                                $stmt->execute([$officeId, $companyId, $supervisorId, $assignmentMonday, $sid]);
                            }

                            logActivity(
                                $pdo,
                                $coordinatorId,
                                'coordinator',
                                'STUDENT_ASSIGNED',
                                "Assigned " . $describe($studentIds, $studentNames) . " to office ID {$officeId} (company ID {$companyId}, supervisor ID {$supervisorId})."
                            );

                            $pdo->commit();

                            $_SESSION['flash_success'] = count($studentIds) === 1
                                ? "Student placement updated successfully."
                                : count($studentIds) . " students assigned successfully.";
                        }
                    }
                }
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $_SESSION['flash_error'] = "Failed to update placement: " . userFacingError($e, 'assignments.php:assign');
            }
        }
    }
    header("Location: assignments.php" . ($selectedSection !== 'all' ? "?section=" . urlencode($selectedSection) : ""));
    exit();
}

// 3. Fetch Data
$students       = [];
$rosterStudents = [];
$companies      = [];
$supervisors    = [];
$activeSections = [];

$defaultSection = section_course_codes()[0];

try {
    // Distinct course codes present in the roster
    $stmtSec = $pdo->prepare("SELECT DISTINCT COALESCE(NULLIF(section, ''), :def) AS sec FROM students ORDER BY sec ASC");
    $stmtSec->execute(['def' => $defaultSection]);
    $activeSections = $stmtSec->fetchAll(PDO::FETCH_COLUMN) ?: section_course_codes();

    // Query Students
    $whereStudent = ["u.status = 'active'"];
    $stdParams = [];

    if ($selectedSection !== 'all') {
        $whereStudent[] = "s.section = :sec";
        $stdParams['sec'] = $selectedSection;
    }

    $whereStudentSql = "WHERE " . implode(' AND ', $whereStudent);
    $stdParams['defaultSection'] = $defaultSection;

    $sqlStudents = "
        SELECT 
            s.id,
            s.student_number,
            s.program,
            COALESCE(s.section, :defaultSection) AS section,
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

    // Full active roster for the bulk placement picker. It is deliberately NOT
    // filtered by the course code shown on the page: the picker's own dropdown
    // switches course codes without a reload, so it needs every student.
    $stmtRoster = $pdo->prepare("
        SELECT
            s.id,
            s.student_number,
            COALESCE(s.section, :defaultSection) AS section,
            s.office_id,
            s.company_id,
            s.supervisor_id,
            u.name,
            u.avatar_url,
            c.name AS company_name
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        WHERE u.status = 'active'
        ORDER BY s.section ASC, u.name ASC
    ");
    $stmtRoster->execute(['defaultSection' => $defaultSection]);
    $rosterStudents = $stmtRoster->fetchAll(PDO::FETCH_ASSOC) ?: [];

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