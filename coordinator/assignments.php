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

// 2. Handle POST Actions (Assign / Unlink Placement / Approve / Reject Requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_student'])) {
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
    } elseif (isset($_POST['action']) && in_array($_POST['action'], ['approve_placement', 'reject_placement'])) {
        $studentId = intval($_POST['student_id'] ?? 0);
        $action = $_POST['action'];

        if ($studentId > 0) {
            try {
                if ($action === 'approve_placement') {
                    $stmtReq = $pdo->prepare("SELECT requested_company_name, requested_supervisor_name, requested_supervisor_email FROM students WHERE id = ?");
                    $stmtReq->execute([$studentId]);
                    $reqData = $stmtReq->fetch(PDO::FETCH_ASSOC);

                    if ($reqData && !empty($reqData['requested_company_name'])) {
                        $companyName    = trim($reqData['requested_company_name']);
                        $supName        = trim($reqData['requested_supervisor_name'] ?? '');
                        $supEmail       = trim($reqData['requested_supervisor_email'] ?? '');

                        // 1. Match or auto-create company
                        $compStmt = $pdo->prepare("SELECT id FROM companies WHERE LOWER(name) = LOWER(?) LIMIT 1");
                        $compStmt->execute([$companyName]);
                        $matchedComp = $compStmt->fetch(PDO::FETCH_ASSOC);

                        if ($matchedComp) {
                            $newCompanyId = $matchedComp['id'];
                        } else {
                            $insertComp = $pdo->prepare("INSERT INTO companies (name, department) VALUES (?, 'Main Office')");
                            $insertComp->execute([$companyName]);
                            $newCompanyId = $pdo->lastInsertId();
                        }

                        $newSupervisorId = null;

                        // 2. Auto-create Supervisor Account if name & email were provided by the student
                        if (!empty($supEmail)) {
                            // Check if user already exists in master `users` table
                            $userCheck = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
                            $userCheck->execute([$supEmail]);
                            $existingUser = $userCheck->fetch(PDO::FETCH_ASSOC);

                            if ($existingUser) {
                                $userId = $existingUser['id'];
                            } else {
                                // Create user with 'supervisor' role
                                $insertUser = $pdo->prepare("INSERT INTO users (name, email, role, status) VALUES (?, ?, 'supervisor', 'active')");
                                $insertUser->execute([($supName !== '' ? $supName : 'Company Supervisor'), $supEmail]);
                                $userId = $pdo->lastInsertId();
                            }

                            // Check if supervisor profile exists for this user
                            $supCheck = $pdo->prepare("SELECT id FROM supervisors WHERE user_id = ? LIMIT 1");
                            $supCheck->execute([$userId]);
                            $existingSup = $supCheck->fetch(PDO::FETCH_ASSOC);

                            if ($existingSup) {
                                $newSupervisorId = $existingSup['id'];
                                // Update company link if needed
                                $updateSupComp = $pdo->prepare("UPDATE supervisors SET company_id = ? WHERE id = ?");
                                $updateSupComp->execute([$newCompanyId, $newSupervisorId]);
                            } else {
                                // Create supervisor profile linked to the new company
                                $insertSup = $pdo->prepare("INSERT INTO supervisors (user_id, company_id) VALUES (?, ?)");
                                $insertSup->execute([$userId, $newCompanyId]);
                                $newSupervisorId = $pdo->lastInsertId();
                            }
                        }

                        // 3. Update student placement record with the company and the newly created supervisor ID
                        $update = $pdo->prepare("
                            UPDATE students 
                            SET 
                                company_id = ?, 
                                supervisor_id = ?,
                                placement_request_status = 'approved',
                                requested_company_name = NULL,
                                requested_supervisor_name = NULL,
                                requested_supervisor_email = NULL
                            WHERE id = ?
                        ");
                        $update->execute([$newCompanyId, $newSupervisorId, $studentId]);
                        $_SESSION['flash_success'] = "Placement approved, company linked, and supervisor account created successfully!";
                    }

                } elseif ($action === 'reject_placement') {
                    $rejectionReason = trim($_POST['rejection_reason'] ?? 'Request rejected by coordinator.');
                    $update = $pdo->prepare("
                        UPDATE students 
                        SET 
                            placement_request_status = 'rejected',
                            placement_rejection_reason = ?
                        WHERE id = ?
                    ");
                    $update->execute([$rejectionReason, $studentId]);
                    $_SESSION['flash_success'] = "Placement request rejected.";
                }
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Action failed: " . $e->getMessage();
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
            u.name,
            u.email,
            u.avatar_url,
            c.name AS company_name,
            c.department AS company_dept,
            u_sup.name AS supervisor_name,
            s.requested_company_name,
            s.requested_supervisor_name,
            s.requested_supervisor_email,
            s.placement_request_status,
            s.placement_rejection_reason
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