<?php
// coordinator/evaluations.php

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/evaluation_criteria.php';
require_once __DIR__ . '/../config/csrf.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

// CSRF Guard: placed once here so it covers both POST branches below
// (request_evaluation at :56 and cancel_evaluation at :192).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify(__DIR__ . '/evaluations.php');
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
| SAVE EVALUATION FORM QUESTIONS
|--------------------------------------------------------------------------
|
| The 12 competency keys and their group buckets are fixed. Only the wording
| is editable, and it applies to evaluations submitted from now on: every
| record already signed keeps the questions it was answered against, because
| the wording is frozen onto evaluations.criteria_snapshot at submission time.
|
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'save_evaluation_questions'
) {

    require_once __DIR__ . '/../src/services/evaluation_questions_store.php';

    $submittedQuestions = $_POST['question'] ?? [];

    if (!is_array($submittedQuestions)) {
        $_SESSION['flash_error'] = 'No questions were submitted.';
        redirectToEvaluations();
    }

    // Captured BEFORE the save, and cached for the request by
    // ojtEvaluationCriteria(). Reading it after the write would compare the
    // submitted text against itself and report every edit as "no change".
    $liveBeforeSave = ojtEvaluationCriteria();

    $saveResult = evaluationQuestionsSave(
        $pdo,
        ojtEvaluationCriteriaDefaults(),
        $submittedQuestions,
        (int) ($_SESSION['user_id'] ?? 0)
    );

    if ($saveResult['ok']) {

        // Count how many competencies actually changed, so the audit trail
        // records what was edited rather than just that "something" was.
        // Comparing against the live wording (not the typed draft) means a
        // save that rewrote a field with identical text is not logged as a
        // change.
        $changedCount = 0;

        foreach ($liveBeforeSave as $criterion) {
            $key = (string) $criterion['key'];
            $newText = trim((string) ($submittedQuestions[$key] ?? ''));

            if ($newText !== '' && $newText !== trim((string) $criterion['text'])) {
                $changedCount++;
            }
        }

        $competencyTotal = count($liveBeforeSave);

        // Kept deliberately short. An earlier version listed the changed
        // criterion keys and closed with "Evaluations already submitted keep
        // their original questions." That second sentence is explained in the
        // editor itself, and repeating it in every log line only made the
        // entry harder to read at a glance.
        logActivity(
            $pdo,
            (int) ($_SESSION['user_id'] ?? 0),
            'coordinator',
            'EVALUATION_FORM_UPDATED',
            $changedCount > 0
                ? sprintf('%d of %d competencies changed.', $changedCount, $competencyTotal)
                : sprintf('Resaved all %d competencies with no wording changes.', $competencyTotal)
        );

        $_SESSION['flash_success'] = $changedCount > 0
            ? "Evaluation form updated. {$changedCount} of {$competencyTotal} competencies changed."
            : 'Evaluation form saved. No wording had changed.';

    } else {

        $formErrors = $saveResult['errors'];
        $formMessage = $formErrors['_form'] ?? sprintf(
            '%d competency/competencies could not be saved. Please correct the highlighted field(s).',
            count($formErrors)
        );

        unset($formErrors['_form']);

        // Per-field messages and the typed draft ride along in the session so
        // the reloaded modal marks the failing textarea and keeps the wording
        // the coordinator had already entered.
        $_SESSION['evaluation_form_errors']     = $formErrors;
        $_SESSION['evaluation_question_draft']   = $submittedQuestions;

        $_SESSION['flash_error'] = $formMessage;
    }

    redirectToEvaluations();
}


/*
|--------------------------------------------------------------------------
| 2. Filter Inputs
|--------------------------------------------------------------------------
*/

$selectedCompany = $_GET['company_id'] ?? 'all';

// Whitelisted rather than compared inline further down, so a hand-edited
// ?status= falls back to 'all' instead of silently returning an unfiltered
// list under a bogus selection.
$selectedStatus  = in_array($_GET['status'] ?? '', ['all', 'Completed', 'Pending'], true)
    ? $_GET['status']
    : 'all';

$selectedSection = ($_GET['section'] ?? 'all') === 'all'
    ? 'all'
    : normalizeSectionCode($_GET['section']);
$searchQuery     = trim($_GET['search'] ?? '');

$filteredEvals   = [];
$companiesList   = [];
$defaultSection  = section_course_codes()[0];
$activeSections  = [];
$eligibleStudents = [];

// Data for the "Update Evaluation Form" editor. The draft wins over the live
// wording only when a save failed validation, so the coordinator does not lose
// what they typed.
$evaluationFormCriteria = ojtEvaluationCriteria();
$evaluationFormDraft    = $_SESSION['evaluation_question_draft'] ?? null;
$evaluationFormErrors   = $_SESSION['evaluation_form_errors'] ?? [];

unset($_SESSION['evaluation_question_draft'], $_SESSION['evaluation_form_errors']);

$totalCount      = 0;
$completedCount  = 0;
$pendingCount    = 0;

// Paging. 25 per page, matching coordinator/audit_logs.php so both list
// screens paginate identically.
$perPage         = 25;
$page            = 1;
$totalPages      = 1;
$totalFiltered   = 0;


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
        WHERE status = 'active'
          AND name IS NOT NULL
          AND name != ''
        ORDER BY name ASC
    ");

    $companiesList =
        $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];


    /*
     * Distinct Course Codes
     */
    $stmtSec = $pdo->prepare("
        SELECT DISTINCT
            COALESCE(NULLIF(section, ''), :def) AS sec
        FROM students
        ORDER BY sec ASC
    ");

    $stmtSec->execute(['def' => $defaultSection]);

    $activeSections =
        $stmtSec->fetchAll(PDO::FETCH_COLUMN)
        ?: section_course_codes();


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
     * |----------------------------------------------------------------------
     * Total rows matching the current filters, which drives the pager.
     * |----------------------------------------------------------------------
     *
     * The count repeats the same FROM/JOIN chain as the list below because the
     * filters reference c.id, s.section, u.name, s.student_number and the
     * evaluations columns. The offices/supervisors joins are not needed here:
     * nothing filters or selects through them.
     *
     * $params['defSection'] is only added further down, for the list query's
     * COALESCE placeholder, so this statement must not receive it. Binding from
     * a filtered copy keeps the two statements from drifting apart.
     */
    $stmtCount = $pdo->prepare("
        SELECT COUNT(*)
        FROM students s

        JOIN users u
            ON s.user_id = u.id

        LEFT JOIN companies c
            ON s.company_id = c.id

        LEFT JOIN evaluations e
            ON s.id = e.student_id

        {$whereSql}
    ");

    foreach ($params as $key => $value) {
        $stmtCount->bindValue(':' . $key, $value);
    }

    $stmtCount->execute();

    $totalFiltered =
        (int) $stmtCount->fetchColumn();


    /*
     * |----------------------------------------------------------------------
     * Requested page, clamped into range
     * |----------------------------------------------------------------------
     *
     * A ?page= beyond the end shows the last page rather than an empty table.
     */
    $totalPages =
        max(1, (int) ceil($totalFiltered / $perPage));

    $page = max(1, (int) ($_GET['page'] ?? 1));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;


    $params['defSection'] =
        $defaultSection;


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

            COALESCE(s.section, :defSection) AS section,

            u.name AS student_name,
            u.email AS student_email,
            u.avatar_url AS student_avatar,

            c.id AS company_id,
            COALESCE(c.name, 'Unassigned') AS company_name,
            o.name AS office_name,

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
            e.criteria_snapshot,
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

        LEFT JOIN offices o
            ON s.office_id = o.id

        LEFT JOIN supervisors sup
            ON s.supervisor_id = sup.id

        LEFT JOIN users u_sup
            ON sup.user_id = u_sup.id

        LEFT JOIN evaluations e
            ON s.id = e.student_id

        {$whereSql}

        ORDER BY
            s.section ASC,
            u.name ASC,

            /*
             * Tiebreaker. Section and name are not unique, so without a final
             * deterministic key MySQL is free to order equal rows differently
             * per query -- which makes a row appear on two pages, or on none,
             * as the LIMIT window slides. s.id is stable and unique.
             */
            s.id ASC

        LIMIT :lim OFFSET :off
    ";


    $stmt = $pdo->prepare($sql);

    /*
     * EMULATE_PREPARES is off, so LIMIT/OFFSET have to be bound as integers.
     * They cannot ride along in the string params array.
     */
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }

    $stmt->execute();

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
     *
     * Programme-wide totals, deliberately NOT scoped to the active filters.
     * These feed the three stat cards, and $completedCount also drives the
     * "Export All Signed (N)" label, which has to mean every signed record
     * rather than the current page. The view labels the cards as
     * all-cohort figures so they are not read as filter-aware.
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


} catch (PDOException $e) {

    error_log(
        "Evaluations Error: " .
        $e->getMessage()
    );

    $filteredEvals = [];

    $eligibleStudents = [];

    // The view renders these outside the try block, so they need a sane
    // fallback here too -- otherwise the pager and the records pill would
    // report stale numbers next to an empty table.
    $totalFiltered = 0;
}


/*
|--------------------------------------------------------------------------
| Render Evaluation Page
|--------------------------------------------------------------------------
*/

require_once __DIR__ .
    '/../src/pages/coordinator/evaluationsPage.php';