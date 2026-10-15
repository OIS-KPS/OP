<?php
// coordinator/approved_reports.php
session_start();

require_once __DIR__ . '/../config/db.php';

// 1. Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$pageTitle = "Accomplishment Reports";

// 2. Filter Inputs
$selectedCompany = $_GET['company_id'] ?? 'all';
$selectedSection = ($_GET['section'] ?? 'all') === 'all' ? 'all' : normalizeSectionCode($_GET['section']); // Default to 'all'
$searchQuery     = trim($_GET['search'] ?? '');

$filteredStudents = [];
$companiesList    = [];
$defaultSection   = section_course_codes()[0];

// Every course code the portal knows about, used to populate the dropdown.
$courseCodeOptions = section_course_codes();

// Paging. 25 per page, matching coordinator/audit_logs.php and
// coordinator/evaluations.php so all three coordinator list screens behave the
// same way.
$perPage       = 25;
$page          = 1;
$totalPages    = 1;
$totalFiltered = 0;

try {
    // Distinct Companies
    $stmtCompanies = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' AND name IS NOT NULL AND name != '' ORDER BY name ASC");
    $companiesList = $stmtCompanies->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Course codes actually assigned to students, merged with the canonical list
    // so the dropdown never hides a block that simply has no students yet.
    $stmtSec = $pdo->query("SELECT DISTINCT NULLIF(TRIM(section), '') AS sec FROM students WHERE section IS NOT NULL AND TRIM(section) != ''");
    $assignedSections = $stmtSec->fetchAll(PDO::FETCH_COLUMN) ?: [];

    $mergedCodes = array_map(
        fn($code) => strtoupper(trim((string)$code)),
        array_merge($courseCodeOptions, $assignedSections)
    );

    // Keep a code that came in through the URL (e.g. a bookmarked link to a
    // block that no longer has students) visible in the dropdown, so the
    // filter on screen always matches the results underneath it.
    if ($selectedSection !== 'all') {
        $mergedCodes[] = $selectedSection;
    }

    $courseCodeOptions = array_values(array_unique(array_filter($mergedCodes, 'strlen')));
    sort($courseCodeOptions);

    // 3. Build Query Filters
    // Only interns who already have at least one approved weekly report are
    // listed here (the coordinator's "accomplishment" view).
    $whereClauses = ["u.status = 'active'", "EXISTS (SELECT 1 FROM reports r WHERE r.student_id = s.id AND r.status = 'approved')"];
    // NOTE: every placeholder below is unique on purpose. The connection runs
    // with PDO::ATTR_EMULATE_PREPARES = false, which rejects both a repeated
    // named marker and a bound value that has no marker in the SQL (HY093
    // "Invalid parameter number"), so each name is used exactly once and the
    // bound array only carries the values the final query actually uses.
    //
    // This array therefore holds FILTER parameters only. :rowSection backs the
    // COALESCE in the list query's SELECT list, which a COUNT(*) has no use for,
    // so it is added further down, right before the list runs. Seeding it here
    // would bind a value with no marker and break the count.
    $params = [];

    if ($selectedCompany !== 'all' && is_numeric($selectedCompany) && intval($selectedCompany) > 0) {
        $whereClauses[] = "c.id = :comp_id";
        $params['comp_id'] = intval($selectedCompany);
    }

    if ($selectedSection !== 'all' && $selectedSection !== '') {
        $whereClauses[] = "COALESCE(NULLIF(TRIM(s.section), ''), :filterSection) = :sectionFilter";
        $params['filterSection'] = $defaultSection;
        $params['sectionFilter'] = $selectedSection;
    }

    if ($searchQuery !== '') {
        $whereClauses[] = "(LOWER(u.name) LIKE :search_name OR LOWER(s.student_number) LIKE :search_num)";
        $searchParam = '%' . strtolower($searchQuery) . '%';
        $params['search_name'] = $searchParam;
        $params['search_num']  = $searchParam;
    }

    $whereSql = "WHERE " . implode(' AND ', $whereClauses);


    /*
     * |----------------------------------------------------------------------
     * Total rows matching the current filters, which drives the pager.
     * |----------------------------------------------------------------------
     *
     * The count repeats the same FROM/JOIN chain and the same WHERE as the list
     * below, because the filters reach through u.name, s.student_number, c.id
     * and s.section. Only the SELECT list is dropped.
     */
    $stmtCount = $pdo->prepare("
        SELECT COUNT(*)
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN offices o ON s.office_id = o.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        {$whereSql}
    ");

    foreach ($params as $key => $value) {
        $stmtCount->bindValue(':' . $key, $value);
    }

    $stmtCount->execute();

    $totalFiltered = (int) $stmtCount->fetchColumn();


    /*
     * |----------------------------------------------------------------------
     * Requested page, clamped into range
     * |----------------------------------------------------------------------
     *
     * A ?page= beyond the end shows the last page rather than an empty table.
     */
    $totalPages = max(1, (int) ceil($totalFiltered / $perPage));

    $page = max(1, (int) ($_GET['page'] ?? 1));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;


    // Only now, because this placeholder exists solely in the list query below.
    $params['rowSection'] = $defaultSection;

    $sql = "
        SELECT 
            s.id AS student_id,
            s.student_number,
            s.program,
            COALESCE(NULLIF(TRIM(s.section), ''), :rowSection) AS section,
            u.name AS student_name,
            u.email AS student_email,
            u.avatar_url AS student_avatar,
            COALESCE(c.name, 'Host Company') AS company_name,
            o.name AS office_name,
            u_sup.name AS supervisor_name,
            (SELECT COUNT(id) FROM reports WHERE student_id = s.id AND status = 'approved') AS approved_reports_count,
            (SELECT COUNT(id) FROM reports WHERE student_id = s.id AND status = 'approved' AND is_late = 1) AS late_approved_count,
            (SELECT COUNT(re2.id) FROM report_entities re2 WHERE re2.report_id IN (SELECT r2.id FROM reports r2 WHERE r2.student_id = s.id AND r2.status = 'approved') AND re2.is_archived = 0 AND re2.it_related <> 'no') AS tech_count,
            (SELECT COUNT(re2.id) FROM report_entities re2 WHERE re2.report_id IN (SELECT r2.id FROM reports r2 WHERE r2.student_id = s.id AND r2.status = 'approved') AND re2.is_archived = 0 AND re2.it_related = 'no') AS clerical_count
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        LEFT JOIN offices o ON s.office_id = o.id
        LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
        LEFT JOIN users u_sup ON sup.user_id = u_sup.id
        {$whereSql}
        ORDER BY s.section ASC, SUBSTRING_INDEX(u.name, ' ', -1) ASC, u.name ASC,

            /*
             * Tiebreaker. Surname and full name are not unique, so without a final
             * deterministic key MySQL may order equal rows differently per query,
             * which makes a row land on two pages or none as the LIMIT window
             * slides. s.id is stable and unique.
             */
            s.id ASC

        LIMIT :lim OFFSET :off
    ";

    $stmt = $pdo->prepare($sql);

    // EMULATE_PREPARES is off, so LIMIT/OFFSET must be bound as integers; they
    // cannot ride along in the string params array.
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }

    $stmt->execute();
    $rawStudents = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Compute Percentages (entity counts already come back per student)
    foreach ($rawStudents as $st) {
        $tCount = (int)($st['tech_count'] ?? 0);
        $cCount = (int)($st['clerical_count'] ?? 0);
        $sumEntities = $tCount + $cCount;

        $st['it_percentage'] = $sumEntities > 0 ? round(($tCount / $sumEntities) * 100, 1) : 0.0;
        $st['clerical_percentage'] = $sumEntities > 0 ? round(($cCount / $sumEntities) * 100, 1) : 0.0;
        $st['total_entities'] = $sumEntities;

        $filteredStudents[] = $st;
    }

} catch (PDOException $e) {
    error_log("Approved Reports Error: " . $e->getMessage());
    $filteredStudents = [];

    // The view renders the records pill and the pager outside the try block, so
    // they need a fallback too -- otherwise they would report stale numbers
    // next to an empty table.
    $totalFiltered = 0;
}

require_once __DIR__ . '/../src/pages/coordinator/approvedReportsPage.php';