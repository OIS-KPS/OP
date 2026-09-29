<?php
// src/services/report_workspace.php
/**
 * Shared data layer for the per-student "report workspace" page.
 *
 * The same layout is served to every role; only the scoping differs:
 *   - coordinator/view_report.php  -> any assigned student, approved weeks
 *   - view_report.php              -> the signed-in student's own weeks
 *   - supervisor/view_report.php   -> an intern assigned to that supervisor
 */

/**
 * Turn a reports.file_path value into a browser-reachable URL.
 */
if (!function_exists('reportWorkspacePdfUrl')) {
    function reportWorkspacePdfUrl($filePath): string
    {
        $path = trim(str_replace('\\', '/', (string)$filePath));
        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (preg_match('#^/?ICS-PORTAL/#i', $path)) {
            return '/' . ltrim($path, '/');
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        $projectRoot = dirname(__DIR__, 2);
        $fileName = basename($path);
        if ($fileName !== '' && is_file($projectRoot . '/uploads/reports/' . $fileName)) {
            return '/ICS-PORTAL/uploads/reports/' . $fileName;
        }

        return '/ICS-PORTAL/' . ltrim($path, '/');
    }
}

/**
 * Load the student identity block rendered in the workspace banner.
 */
if (!function_exists('reportWorkspaceLoadStudent')) {
    function reportWorkspaceLoadStudent(PDO $pdo, int $studentId): ?array
    {
        if ($studentId <= 0) {
            return null;
        }

        $stmt = $pdo->prepare(
            'SELECT
                s.id AS student_id, s.student_number, s.program,
                COALESCE(s.section, "A") AS section,
                u.name AS student_name, u.email AS student_email, u.avatar_url AS student_avatar,
                c.name AS company_name, c.department AS company_dept,
                u_sup.name AS supervisor_name
             FROM students s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN companies c ON s.company_id = c.id
             LEFT JOIN supervisors sup ON s.supervisor_id = sup.id
             LEFT JOIN users u_sup ON sup.user_id = u_sup.id
             WHERE s.id = ?
             LIMIT 1'
        );
        $stmt->execute([$studentId]);

        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        return $student ?: null;
    }
}

/**
 * Load the student's weekly reports, oldest week first.
 *
 * @param string|null $status Restrict to a single reports.status value, or null for all.
 */
if (!function_exists('reportWorkspaceLoadReports')) {
    function reportWorkspaceLoadReports(PDO $pdo, int $studentId, ?string $status = null): array
    {
        if ($studentId <= 0) {
            return [];
        }

        $sql = 'SELECT id, week_number, file_path, status, supervisor_remarks,
                       submitted_at, approved_at, updated_at
                FROM reports
                WHERE student_id = ?';

        $params = [$studentId];
        if ($status !== null && $status !== '') {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }

        $sql .= ' ORDER BY week_number ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

/**
 * Resolve the student_id that owns a given report.
 */
if (!function_exists('reportWorkspaceStudentIdForReport')) {
    function reportWorkspaceStudentIdForReport(PDO $pdo, int $reportId): int
    {
        if ($reportId <= 0) {
            return 0;
        }

        $stmt = $pdo->prepare('SELECT student_id FROM reports WHERE id = ? LIMIT 1');
        $stmt->execute([$reportId]);

        return (int)($stmt->fetchColumn() ?: 0);
    }
}

/**
 * Load active + archived entities for a set of reports.
 */
if (!function_exists('reportWorkspaceLoadEntities')) {
    function reportWorkspaceLoadEntities(PDO $pdo, array $reportIds): array
    {
        $empty = ['all' => [], 'archived' => []];
        if (empty($reportIds)) {
            return $empty;
        }

        $placeholders = implode(',', array_fill(0, count($reportIds), '?'));

        $stmtAll = $pdo->prepare(
            "SELECT
                re.id, re.report_id, re.entity_name, re.canonical_name, re.category,
                re.activity_type, re.activity_type AS classification,
                re.it_related, re.source, re.confidence_score, re.created_at,
                r.week_number
             FROM report_entities re
             JOIN reports r ON re.report_id = r.id
             WHERE re.report_id IN ($placeholders) AND re.is_archived = 0
             ORDER BY r.week_number ASC, re.id DESC"
        );
        $stmtAll->execute($reportIds);

        $stmtArchived = $pdo->prepare(
            "SELECT
                re.id, re.report_id, re.entity_name, re.category, re.activity_type, r.week_number
             FROM report_entities re
             JOIN reports r ON re.report_id = r.id
             WHERE re.report_id IN ($placeholders) AND re.is_archived = 1
             ORDER BY r.week_number ASC, re.id DESC"
        );
        $stmtArchived->execute($reportIds);

        return [
            'all' => $stmtAll->fetchAll(PDO::FETCH_ASSOC) ?: [],
            'archived' => $stmtArchived->fetchAll(PDO::FETCH_ASSOC) ?: [],
        ];
    }
}

/**
 * Drop repeated entity names inside the same week, then attach display labels.
 */
if (!function_exists('reportWorkspacePrepareEntities')) {
    function reportWorkspacePrepareEntities(array $rawEntities): array
    {
        $seen = [];
        $entities = [];

        foreach ($rawEntities as $entity) {
            $dedupeKey = (int)($entity['week_number'] ?? 0)
                . '|'
                . strtolower(trim($entity['entity_name'] ?? ''));

            if (isset($seen[$dedupeKey])) {
                continue;
            }
            $seen[$dedupeKey] = true;
            $entities[] = $entity;
        }

        return $entities;
    }
}

/**
 * Split entities into the technical / clerical buckets used by the ratio bars.
 */
if (!function_exists('reportWorkspaceRatio')) {
    function reportWorkspaceRatio(array $entities): array
    {
        $technical = 0;
        $clerical = 0;

        foreach ($entities as $entity) {
            if (strtolower(trim((string)($entity['activity_type'] ?? ''))) === 'clerical') {
                $clerical++;
            } elseif (trim((string)($entity['activity_type'] ?? '')) !== '') {
                $technical++;
            }
        }

        $total = $technical + $clerical;

        return [
            'technical' => $technical,
            'clerical' => $clerical,
            'total' => $total,
            'itPct' => $total > 0 ? round(($technical / $total) * 100, 1) : 0.0,
            'clericalPct' => $total > 0 ? round(($clerical / $total) * 100, 1) : 0.0,
        ];
    }
}

/**
 * Everything a report workspace view needs for one student.
 *
 * @param array{reportId?:int, status?:?string, weekOrder?:string} $options
 */
if (!function_exists('reportWorkspaceBuild')) {
    function reportWorkspaceBuild(PDO $pdo, int $studentId, array $options = []): array
    {
        $requestedReportId = (int)($options['reportId'] ?? 0);
        $statusFilter = array_key_exists('status', $options) ? $options['status'] : null;

        $workspace = [
            'student' => null,
            'studentId' => $studentId,
            'reportsList' => [],
            'activeReport' => null,
            'activeReportId' => 0,
            'allEntities' => [],
            'weekEntities' => [],
            'archivedEntities' => [],
            'itPct' => 0.0,
            'clericalPct' => 0.0,
            'technicalCount' => 0,
            'clericalCount' => 0,
            'weekItPct' => 0.0,
            'weekClericalPct' => 0.0,
            'weekTotalCount' => 0,
            'pdfUrl' => '',
        ];

        $workspace['student'] = reportWorkspaceLoadStudent($pdo, $studentId);
        if ($workspace['student'] === null) {
            return $workspace;
        }

        $workspace['reportsList'] = reportWorkspaceLoadReports($pdo, $studentId, $statusFilter);

        if (!empty($workspace['reportsList'])) {
            foreach ($workspace['reportsList'] as $report) {
                if ($requestedReportId > 0 && (int)$report['id'] === $requestedReportId) {
                    $workspace['activeReport'] = $report;
                    break;
                }
            }

            if ($workspace['activeReport'] === null) {
                $workspace['activeReport'] = end($workspace['reportsList']);
                reset($workspace['reportsList']);
            }
        }

        $entities = reportWorkspaceLoadEntities($pdo, array_column($workspace['reportsList'], 'id'));
        $workspace['archivedEntities'] = $entities['archived'];
        $workspace['allEntities'] = reportWorkspacePrepareEntities($entities['all']);

        $workspace['activeReportId'] = $workspace['activeReport'] !== null
            ? (int)$workspace['activeReport']['id']
            : 0;

        $workspace['weekEntities'] = array_values(array_filter(
            $workspace['allEntities'],
            static function ($entity) use ($workspace) {
                return (int)$entity['report_id'] === $workspace['activeReportId'];
            }
        ));

        $overall = reportWorkspaceRatio($workspace['allEntities']);
        $workspace['technicalCount'] = $overall['technical'];
        $workspace['clericalCount'] = $overall['clerical'];
        $workspace['itPct'] = $overall['itPct'];
        $workspace['clericalPct'] = $overall['clericalPct'];

        $week = reportWorkspaceRatio($workspace['weekEntities']);
        $workspace['weekTotalCount'] = $week['total'];
        $workspace['weekItPct'] = $week['itPct'];
        $workspace['weekClericalPct'] = $week['clericalPct'];

        $workspace['pdfUrl'] = reportWorkspacePdfUrl($workspace['activeReport']['file_path'] ?? '');

        return $workspace;
    }
}

/**
 * Stream the weekly IT / clerical split of one student as CSV.
 */
if (!function_exists('reportWorkspaceExportCsv')) {
    function reportWorkspaceExportCsv(?array $student, array $reportsList, array $allEntities): void
    {
        $safeStudentName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $student['student_name'] ?? 'student');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $safeStudentName . '_weekly_reports_summary.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Week Number', 'Report Status', 'Submitted Date', 'IT Percentage (%)', 'Clerical Percentage (%)']);

        foreach ($reportsList as $report) {
            $weekEntities = array_values(array_filter(
                $allEntities,
                static function ($entity) use ($report) {
                    return (int)$entity['report_id'] === (int)$report['id'];
                }
            ));

            $ratio = reportWorkspaceRatio($weekEntities);

            fputcsv($output, [
                'Week ' . (int)$report['week_number'],
                ucfirst($report['status'] ?? 'N/A'),
                $report['submitted_at'] ?? 'N/A',
                $ratio['itPct'] . '%',
                $ratio['clericalPct'] . '%',
            ]);
        }

        fclose($output);
    }
}
