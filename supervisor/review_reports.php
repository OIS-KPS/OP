<?php

// ============================================================
// supervisor/review_reports.php
// ============================================================

session_start();

require_once __DIR__ . '/../config/db.php';


// ============================================================
// 1. AUTHORIZATION
// ============================================================

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'supervisor'
) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];


// ============================================================
// 2. MESSAGE
// ============================================================

$message = $_SESSION['review_message'] ?? '';

unset($_SESSION['review_message']);


// ============================================================
// 3. GET SUPERVISOR
// ============================================================

$stmtSup = $pdo->prepare("
    SELECT id
    FROM supervisors
    WHERE user_id = ?
    LIMIT 1
");

$stmtSup->execute([
    $userId
]);

$supervisorRecord = $stmtSup->fetch(
    PDO::FETCH_ASSOC
);

if (!$supervisorRecord) {
    die(
        "Supervisor profile not found. " .
        "Please contact administrator."
    );
}

$supervisorId = (int) $supervisorRecord['id'];

$_SESSION['supervisor_id'] = $supervisorId;


// ============================================================
// 4. INTERNS WITH PENDING REVIEWS
// ============================================================

/*
 * One row per intern, mirroring coordinator/approved_reports.php but
 * scoped to this supervisor and aimed at a single question: who still owes
 * me a verdict?
 *
 * There is deliberately no status filter and no GROUP BY. Every count is a
 * correlated subquery, so the rows never fan out and the "only interns who
 * have actually submitted" rule is a plain EXISTS in the WHERE rather than
 * a HAVING over an aggregate.
 */

$internsSql = "

    SELECT

        s.id AS student_id,

        s.student_number,

        COALESCE(
            NULLIF(s.section, ''),
            'A'
        ) AS section,

        u.name AS student_name,

        u.avatar_url AS student_avatar,

        (
            SELECT COUNT(*)
            FROM reports r
            WHERE r.student_id = s.id
        ) AS total_reports_count,

        (
            SELECT COUNT(*)
            FROM reports r
            WHERE r.student_id = s.id
              AND LOWER(r.status) = 'pending'
        ) AS pending_count,

        (
            SELECT MAX(r.submitted_at)
            FROM reports r
            WHERE r.student_id = s.id
        ) AS latest_submitted_at,

        /*
         * Newest PENDING submission, kept separate from
         * latest_submitted_at: an intern with a pending Week 3 but an
         * approved Week 5 must queue on the Week 3 date, otherwise the
         * approved week hides how long the pending one has waited.
         */

        (
            SELECT MAX(r.submitted_at)
            FROM reports r
            WHERE r.student_id = s.id
              AND LOWER(r.status) = 'pending'
        ) AS latest_pending_at,

        /*
         * Open the workspace on the week that still needs a verdict:
         * the newest pending report, else the newest report overall.
         */

        COALESCE(
            (
                SELECT r.id
                FROM reports r
                WHERE r.student_id = s.id
                  AND LOWER(r.status) = 'pending'
                ORDER BY
                    r.submitted_at DESC,
                    r.id DESC
                LIMIT 1
            ),

            (
                SELECT r.id
                FROM reports r
                WHERE r.student_id = s.id
                ORDER BY
                    r.submitted_at DESC,
                    r.id DESC
                LIMIT 1
            )
        ) AS focus_report_id

    FROM students s

    JOIN users u
        ON s.user_id = u.id

    WHERE
        s.supervisor_id = :supervisor_id

        AND

        u.status = 'active'

        /* Interns who have never submitted have nothing to review. */

        AND EXISTS (
            SELECT 1
            FROM reports r
            WHERE r.student_id = s.id
        )

    ORDER BY
        (pending_count > 0) DESC,
        latest_pending_at DESC,
        latest_submitted_at DESC,
        u.name ASC
";

$interns = [];
$pendingInterns = 0;

try {

    $stmtInterns = $pdo->prepare(
        $internsSql
    );

    $stmtInterns->execute([
        'supervisor_id' => $supervisorId
    ]);

    $interns =
        $stmtInterns->fetchAll(
            PDO::FETCH_ASSOC
        ) ?: [];

    // --------------------------------------------------------
    // Normalise counts and tally the review backlog
    // --------------------------------------------------------

    foreach ($interns as &$intern) {

        $intern['pending_count'] =
            (int) $intern['pending_count'];

        $intern['total_reports_count'] =
            (int) $intern['total_reports_count'];

        $intern['focus_report_id'] =
            (int) $intern['focus_report_id'];

        if ($intern['pending_count'] > 0) {
            $pendingInterns++;
        }
    }

    unset($intern);

} catch (PDOException $exception) {

    error_log(
        "Review Reports Error: " .
        $exception->getMessage()
    );

    $interns = [];
}


// ============================================================
// 5. RENDER VIEW
// ============================================================

require_once
    __DIR__ .
    '/../src/pages/supervisor/reviewReportsPage.php';