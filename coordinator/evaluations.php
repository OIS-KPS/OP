<?php
// coordinator/evaluations.php

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/evaluation_criteria.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Final Evaluations";

// Flash Messages
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;

unset($_SESSION['flash_success'], $_SESSION['flash_error']);

/*
|--------------------------------------------------------------------------
| Helper: Redirect back to evaluations page
|--------------------------------------------------------------------------
*/
function redirectToEvaluations(): void
{
    $queryString = $_SERVER['QUERY_STRING'] ?? '';

    header(
        "Location: evaluations.php" .
        ($queryString !== '' ? '?' . $queryString : '')
    );
    exit();
}

/*
|--------------------------------------------------------------------------
| 1.5. Handle Evaluation POST Actions
|--------------------------------------------------------------------------
|
| Supported actions:
|   - request_evaluation
|   - cancel_evaluation
|
*/

/*
|--------------------------------------------------------------------------
| REQUEST EVALUATION
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'request_evaluation'
) {

    /*
     * Supports:
     *   student_ids[] = multiple students
     *
     * Also supports:
     *   student_id = single student
     *
     * This keeps the backend compatible with both the bulk
     * request modal and the individual request modal.
     */

    $studentIds = $_POST['student_ids'] ?? [];

    // Individual request support
    if (
        empty($studentIds) &&
        isset($_POST['student_id'])
    ) {
        $studentIds = [$_POST['student_id']];
    }

    if (!empty($studentIds) && is_array($studentIds)) {

        $count = 0;

        $stmtTrigger = $pdo->prepare("
            UPDATE students
            SET evaluation_triggered = 1
            WHERE id = ?
        ");

        $stmtStud = $pdo->prepare("
            SELECT u.name
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.id = ?
        ");

        foreach ($studentIds as $rawId) {

            $studentId = intval($rawId);

            if ($studentId <= 0) {
                continue;
            }

            /*
             * Only trigger students who have an assigned supervisor.
             * This follows the same eligibility rule used by the
             * existing bulk selection.
             */
            $stmtSupervisor = $pdo->prepare("
                SELECT supervisor_id
                FROM students
                WHERE id = ?
                LIMIT 1
            ");

            $stmtSupervisor->execute([$studentId]);

            $studentRecord = $stmtSupervisor->fetch(PDO::FETCH_ASSOC);

            if (
                !$studentRecord ||
                empty($studentRecord['supervisor_id'])
            ) {
                continue;
            }

            $stmtTrigger->execute([$studentId]);

            /*
             * Get student name for activity log.
             */
            $stmtStud->execute([$studentId]);

            $stud = $stmtStud->fetch(PDO::FETCH_ASSOC);

            $studName = $stud['name'] ?? "Student #{$studentId}";

            /*
             * Activity Log
             */
            logActivity(
                $pdo,
                $_SESSION['user_id'] ?? null,
                'coordinator',
                'EVAL_TRIGGERED',
                "Coordinator triggered final evaluation request for student {$studName}."
            );

            $count++;
        }

        if ($count > 0) {

            $_SESSION['flash_success'] =
                "Evaluation requests successfully sent for {$count} selected student(s).";

        } else {

            $_SESSION['flash_error'] =
                "No valid students were selected for evaluation request.";
        }

    } else {

        $_SESSION['flash_error'] =
            'Please select at least one student for evaluation request.';
    }

    redirectToEvaluations();
}


/*
|--------------------------------------------------------------------------
| CANCEL EVALUATION REQUEST
|--------------------------------------------------------------------------
|
| Cancelling a request does NOT delete the evaluation record.
|
| It simply resets:
|
|     students.evaluation_triggered = 0
|
| This returns the student to the normal state and allows the
| coordinator to request the evaluation again later.
|
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'cancel_evaluation'
) {

    $studentId = intval($_POST['student_id'] ?? 0);

    if ($studentId <= 0) {

        $_SESSION['flash_error'] =
            'Invalid student selected for cancellation.';

        redirectToEvaluations();
    }

    try {

        /*
         * First verify that the student exists and currently
         * has an active evaluation request.
         */
        $stmtCheck = $pdo->prepare("
            SELECT
                s.id,
                s.evaluation_triggered,
                u.name AS student_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.id = ?
            LIMIT 1
        ");

        $stmtCheck->execute([$studentId]);

        $student = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$student) {

            $_SESSION['flash_error'] =
                'Student record could not be found.';

            redirectToEvaluations();
        }

        /*
         * Only cancel an active evaluation request.
         */
        if ((int)($student['evaluation_triggered'] ?? 0) !== 1) {

            $_SESSION['flash_error'] =
                'There is no active evaluation request to cancel for this student.';

            redirectToEvaluations();
        }

        /*
         * Cancel the evaluation request.
         *
         * We do NOT delete the evaluation record.
         */
        $stmtCancel = $pdo->prepare("
            UPDATE students
            SET evaluation_triggered = 0
            WHERE id = ?
              AND evaluation_triggered = 1
        ");

        $stmtCancel->execute([$studentId]);

        if ($stmtCancel->rowCount() > 0) {

            $studentName =
                $student['student_name'] ??
                "Student #{$studentId}";

            /*
             * Activity Log
             */
            logActivity(
                $pdo,
                $_SESSION['user_id'] ?? null,
                'coordinator',
                'EVAL_REQUEST_CANCELLED',
                "Coordinator cancelled the final evaluation request for student {$studentName}."
            );

            $_SESSION['flash_success'] =
                "Evaluation request for {$studentName} has been cancelled successfully.";

        } else {

            $_SESSION['flash_error'] =
                'The evaluation request could not be cancelled.';
        }

    } catch (PDOException $e) {

        error_log(
            "Cancel Evaluation Error: " .
            $e->getMessage()
        );

        $_SESSION['flash_error'] =
            'Unable to cancel the evaluation request. Please try again.';
    }

    redirectToEvaluations();
}


/*
|--------------------------------------------------------------------------
| 2. Filter Inputs
|--------------------------------------------------------------------------
*/

$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedStatus  = $_GET['status'] ?? 'all';
$selectedSection = $_GET['section'] ?? 'all';
$searchQuery     = trim($_GET['search'] ?? '');
$viewEvalId      = isset($_GET['view_id'])
    ? intval($_GET['view_id'])
    : null;

$filteredEvals   = [];
$companiesList   = [];
$activeSections  = [];
$eligibleStudents = [];
$activeEval      = null;

$totalCount      = 0;
$completedCount  = 0;
$pendingCount    = 0;


/*
|--------------------------------------------------------------------------
| 3. Load Evaluation Data
|--------------------------------------------------------------------------
*/

try {

    /*
     * Distinct Companies
     */
    $stmtCompanies = $pdo->query("
        SELECT id, name
        FROM companies
        WHERE name IS NOT NULL
          AND name != ''
        ORDER BY name ASC
    ");

    $companiesList =
        $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];


    /*
     * Distinct Sections
     */
    $stmtSec = $pdo->query("
        SELECT DISTINCT
            COALESCE(NULLIF(section, ''), 'A') AS sec
        FROM students
        ORDER BY sec ASC
    ");

    $activeSections =
        $stmtSec->fetchAll(PDO::FETCH_COLUMN)
        ?: ['A', 'B', 'C'];


    /*
     * Build Query Filters
     */
    $whereClauses = ["1=1"];
    $params = [];


    /*
     * Company Filter
     */
    if (
        $selectedCompany !== 'all' &&
        is_numeric($selectedCompany) &&
        intval($selectedCompany) > 0
    ) {

        $whereClauses[] = "c.id = :comp_id";

        $params['comp_id'] =
            intval($selectedCompany);
    }


    /*
     * Section Filter
     */
    if ($selectedSection !== 'all') {

        $whereClauses[] = "s.section = :sec";

        $params['sec'] =
            $selectedSection;
    }


    /*
     * Status Filter
     */
    if ($selectedStatus === 'Completed') {

        $whereClauses[] =
            "e.id IS NOT NULL AND e.otp_verified = 1";

    } elseif ($selectedStatus === 'Pending') {

        $whereClauses[] =
            "(e.id IS NULL OR e.otp_verified = 0)";
    }


    /*
     * Search Filter
     */
    if ($searchQuery !== '') {

        $whereClauses[] =
            "(LOWER(u.name) LIKE :search_name
              OR LOWER(s.student_number) LIKE :search_num)";

        $searchParam =
            '%' . strtolower($searchQuery) . '%';

        $params['search_name'] =
            $searchParam;

        $params['search_num'] =
            $searchParam;
    }


    $whereSql =
        "WHERE " . implode(' AND ', $whereClauses);


    /*
     * Main Evaluation Query
     */
    $sql = "
        SELECT

            s.id AS student_id,
            s.student_number,
            s.program,
            s.completion_requested,
            s.evaluation_triggered,

            COALESCE(s.section, 'A') AS section,

            u.name AS student_name,
            u.email AS student_email,
            u.avatar_url AS student_avatar,

            c.id AS company_id,
            COALESCE(c.name, 'Unassigned') AS company_name,

            COALESCE(
                u_sup.name,
                'Pending Assignment'
            ) AS supervisor_name,

            (
                SELECT COUNT(*)
                FROM reports r
                WHERE r.student_id = s.id
                  AND r.status = 'approved'
            ) AS approved_reports_count,

            e.id AS eval_id,

            e.technical_score,
            e.work_ethics_score,
            e.communication_score,
            e.punctuality_score,
            e.criteria_ratings,
            e.final_score,
            e.grade_equivalent,
            e.feedback,

            e.otp_verified,
            e.otp_signed_at,
            e.otp_ip_address,

            CASE
                WHEN e.id IS NOT NULL
                     AND e.otp_verified = 1
                THEN 'Completed'
                ELSE 'Pending'
            END AS status

        FROM students s

        JOIN users u
            ON s.user_id = u.id

        LEFT JOIN companies c
            ON s.company_id = c.id

        LEFT JOIN supervisors sup
            ON s.supervisor_id = sup.id

        LEFT JOIN users u_sup
            ON sup.user_id = u_sup.id

        LEFT JOIN evaluations e
            ON s.id = e.student_id

        {$whereSql}

        ORDER BY
            s.section ASC,
            u.name ASC
    ";


    $stmt = $pdo->prepare($sql);

    $stmt->execute($params);

    $filteredEvals =
        $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];


    /*
     * Eligible Students for Bulk Evaluation Request
     *
     * A student is eligible when:
     *
     * 1. Has a supervisor
     * 2. Does not currently have an active evaluation request
     */
    $stmtEligible = $pdo->query("
        SELECT
            s.id,
            u.name,
            s.student_number,
            s.section

        FROM students s

        JOIN users u
            ON s.user_id = u.id

        WHERE s.supervisor_id IS NOT NULL

          AND (
                s.evaluation_triggered IS NULL
                OR s.evaluation_triggered = 0
          )

        ORDER BY u.name ASC
    ");

    $eligibleStudents =
        $stmtEligible->fetchAll(PDO::FETCH_ASSOC) ?: [];


    /*
     * 4. Metric Counts
     */
    $stmtTotals = $pdo->query("
        SELECT

            COUNT(s.id) AS total_interns,

            SUM(
                CASE
                    WHEN e.id IS NOT NULL
                         AND e.otp_verified = 1
                    THEN 1
                    ELSE 0
                END
            ) AS completed_evals,

            SUM(
                CASE
                    WHEN e.id IS NULL
                         OR e.otp_verified = 0
                    THEN 1
                    ELSE 0
                END
            ) AS pending_evals

        FROM students s

        LEFT JOIN evaluations e
            ON s.id = e.student_id
    ");

    $stats =
        $stmtTotals->fetch(PDO::FETCH_ASSOC);


    $totalCount =
        intval($stats['total_interns'] ?? 0);

    $completedCount =
        intval($stats['completed_evals'] ?? 0);

    $pendingCount =
        intval($stats['pending_evals'] ?? 0);


    /*
     * 5. Modal Record Loader
     */
    if ($viewEvalId) {

        /*
         * First try evaluation ID.
         */
        foreach ($filteredEvals as $ev) {

            if (
                intval($ev['eval_id'] ?? 0)
                === $viewEvalId
            ) {

                $activeEval = $ev;
                break;
            }
        }


        /*
         * If not found, try student ID.
         */
        if (!$activeEval) {

            foreach ($filteredEvals as $ev) {

                if (
                    intval($ev['student_id'])
                    === $viewEvalId
                ) {

                    $activeEval = $ev;
                    break;
                }
            }
        }
    }

} catch (PDOException $e) {

    error_log(
        "Evaluations Error: " .
        $e->getMessage()
    );

    $filteredEvals = [];

    $eligibleStudents = [];
}


/*
|--------------------------------------------------------------------------
| Render Evaluation Page
|--------------------------------------------------------------------------
*/

require_once __DIR__ .
    '/../src/pages/coordinator/evaluationsPage.php';