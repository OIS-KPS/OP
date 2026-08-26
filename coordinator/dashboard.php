<?php
// coordinator/dashboard.php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header('Location: ../auth/login.php');
    exit();
}

$pageTitle = 'CQI Analytics Dashboard';

// Dashboard-compatible defaults. These are empty/zero states, not simulated data.
$totalStudents = 0;
$evaluatedStudents = 0;
$overallTechPct = 0.0;
$spacyConfidence = 0.0;
$companyPerformance = [];
$entitiesData = [];
$categoryCounts = [];
$topCategoryName = 'No data';
$topCategoryOccurrences = 0;
$dashboardError = '';

// CQI variables consumed by dashboardPage.php.
$evaluationCoveragePct = 0.0;
$totalEntityOccurrences = 0;
$technicalEntityOccurrences = 0;
$clericalEntityOccurrences = 0;
$otherEntityOccurrences = 0;
$totalReportsWithEntities = 0;
$topEntities = [];
$lowPerformingCompanies = [];
$companiesWithoutEvaluations = [];
$cqiSummary = [];
$cqiActionPlan = [];

function dashboardLevel(float $percentage): string
{
    if ($percentage >= 80) {
        return 'High';
    }
    if ($percentage >= 60) {
        return 'Moderate';
    }
    return 'Low';
}

try {
    // 1. Total registered students.
    $stmt = $pdo->query('SELECT COUNT(*) FROM students');
    $totalStudents = (int) ($stmt->fetchColumn() ?: 0);

    // 2. Students with a verified evaluation.
    $stmt = $pdo->query(
        'SELECT COUNT(DISTINCT student_id)
         FROM evaluations
         WHERE otp_verified = 1'
    );
    $evaluatedStudents = (int) ($stmt->fetchColumn() ?: 0);

    // 3. Technical share of all persisted extracted/report entities.
    $stmt = $pdo->query(
        "SELECT COALESCE(
            ROUND(
                100 * SUM(CASE WHEN activity_type <> 'Clerical' THEN 1 ELSE 0 END)
                / NULLIF(COUNT(*), 0),
                2
            ),
            0
         )
         FROM report_entities"
    );
    $overallTechPct = (float) ($stmt->fetchColumn() ?: 0);

    // 4. Average confidence from the extractor/database records.
    $stmt = $pdo->query(
        'SELECT COALESCE(ROUND(AVG(confidence_score), 2), 0)
         FROM report_entities
         WHERE confidence_score IS NOT NULL'
    );
    $spacyConfidence = (float) ($stmt->fetchColumn() ?: 0);

    // 5. Company performance from verified evaluation final scores.
    // Companies with no verified evaluations remain visible with 0.0.
    $stmt = $pdo->query(
        "SELECT
            c.id,
            c.name,
            COALESCE(
                ROUND(AVG(CASE WHEN e.otp_verified = 1 THEN e.final_score END), 1),
                0
            ) AS percentage
         FROM companies c
         LEFT JOIN students s ON s.company_id = c.id
         LEFT JOIN evaluations e ON e.student_id = s.id
         GROUP BY c.id, c.name
         ORDER BY percentage DESC, c.name ASC"
    );

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $company) {
        $percentage = (float) ($company['percentage'] ?? 0);
        $companyPerformance[] = [
            'name' => (string) $company['name'],
            'percentage' => $percentage,
            'level' => dashboardLevel($percentage)
        ];
    }

    // 6. Entity frequency by predefined/extracted entity, company, and report date.
    // report_entities has one row per report entity, so COUNT(*) is the real
    // frequency represented by the current schema.
    $stmt = $pdo->query(
        "SELECT
            re.entity_name AS entity,
            COALESCE(NULLIF(re.category, ''), 'Other') AS category,
            COUNT(*) AS frequency,
            CASE
                WHEN re.activity_type = 'Clerical' THEN 'Clerical'
                ELSE 'Technical'
            END AS classification,
            COALESCE(c.name, 'Unassigned') AS company,
            DATE(r.submitted_at) AS report_date
         FROM report_entities re
         INNER JOIN reports r ON r.id = re.report_id
         INNER JOIN students s ON s.id = r.student_id
         LEFT JOIN companies c ON c.id = s.company_id
         GROUP BY
            re.entity_name,
            re.category,
            re.activity_type,
            c.id,
            c.name,
            DATE(r.submitted_at)
         ORDER BY frequency DESC, re.entity_name ASC"
    );

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $entity) {
        $entitiesData[] = [
            'entity' => (string) $entity['entity'],
            'category' => (string) $entity['category'],
            'frequency' => (int) $entity['frequency'],
            'classification' => (string) $entity['classification'],
            'company' => (string) $entity['company'],
            'date' => (string) ($entity['report_date'] ?? '')
        ];
    }

    // 7. Category totals are calculated from the live entity rows above.
    foreach ($entitiesData as $entity) {
        $category = $entity['category'];
        $categoryCounts[$category] =
            ($categoryCounts[$category] ?? 0) + $entity['frequency'];
    }

    arsort($categoryCounts);
    if ($categoryCounts) {
        $topCategoryName = (string) array_key_first($categoryCounts);
        $topCategoryOccurrences = (int) ($categoryCounts[$topCategoryName] ?? 0);
    }

    // 8. CQI evidence derived from the same live dashboard data.
    $evaluationCoveragePct = $totalStudents > 0
        ? round(($evaluatedStudents / $totalStudents) * 100, 1)
        : 0.0;

    foreach ($entitiesData as $entity) {
        $frequency = max(0, (int) $entity['frequency']);
        $totalEntityOccurrences += $frequency;

        if ($entity['classification'] === 'Clerical') {
            $clericalEntityOccurrences += $frequency;
        } else {
            $technicalEntityOccurrences += $frequency;
        }

        $entityKey = $entity['entity'] . '|' . $entity['category'];
        if (!isset($topEntities[$entityKey])) {
            $topEntities[$entityKey] = [
                'entity' => $entity['entity'],
                'category' => $entity['category'],
                'classification' => $entity['classification'],
                'frequency' => 0
            ];
        }
        $topEntities[$entityKey]['frequency'] += $frequency;
    }

    $otherEntityOccurrences = max(
        0,
        $totalEntityOccurrences
            - $technicalEntityOccurrences
            - $clericalEntityOccurrences
    );

    usort($topEntities, static function (array $left, array $right): int {
        return $right['frequency'] <=> $left['frequency'];
    });
    $topEntities = array_slice($topEntities, 0, 5);

    $stmt = $pdo->query('SELECT COUNT(DISTINCT report_id) FROM report_entities');
    $totalReportsWithEntities = (int) ($stmt->fetchColumn() ?: 0);

    foreach ($companyPerformance as $company) {
        if ($company['percentage'] <= 0) {
            $companiesWithoutEvaluations[] = $company['name'];
        } elseif ($company['percentage'] < 60) {
            $lowPerformingCompanies[] = $company;
        }
    }

    $dataStatus = 'On track';
    if ($totalStudents === 0 || $totalReportsWithEntities === 0) {
        $dataStatus = 'Insufficient data';
    } elseif ($evaluationCoveragePct < 90 || $spacyConfidence < 90 || $lowPerformingCompanies) {
        $dataStatus = 'Needs attention';
    }

    $cqiSummary = [
        'status' => $dataStatus,
        'period' => 'Current records in the database',
        'narrative' => sprintf(
            'The dashboard currently covers %d students, %d verified evaluations, and %d reports with extracted entities. Entity activity is %s technical and %s clerical by recorded occurrence. The average extraction confidence is %.2f%%.',
            $totalStudents,
            $evaluatedStudents,
            $totalReportsWithEntities,
            number_format($totalEntityOccurrences > 0 ? ($technicalEntityOccurrences / $totalEntityOccurrences) * 100 : 0, 1) . '%',
            number_format($totalEntityOccurrences > 0 ? ($clericalEntityOccurrences / $totalEntityOccurrences) * 100 : 0, 1) . '%',
            $spacyConfidence
        ),
        'evidence' => [
            'evaluation_coverage_pct' => $evaluationCoveragePct,
            'verified_evaluations' => $evaluatedStudents,
            'total_students' => $totalStudents,
            'technical_entity_occurrences' => $technicalEntityOccurrences,
            'clerical_entity_occurrences' => $clericalEntityOccurrences,
            'other_entity_occurrences' => $otherEntityOccurrences,
            'reports_with_entities' => $totalReportsWithEntities,
            'average_extraction_confidence' => $spacyConfidence,
            'top_category' => $topCategoryName,
            'top_category_occurrences' => $topCategoryOccurrences,
            'low_performing_companies' => count($lowPerformingCompanies),
            'companies_without_evaluations' => count($companiesWithoutEvaluations)
        ],
        'top_entities' => $topEntities
    ];

    // 9. Build actions from measured gaps. No action is based on sample data.
    if ($totalStudents === 0) {
        $cqiActionPlan[] = [
            'priority' => 'High',
            'area' => 'Data baseline',
            'finding' => 'No students are currently recorded.',
            'action' => 'Confirm the student import and registration workflow before interpreting CQI trends.',
            'owner' => 'Coordinator',
            'timeframe' => 'Before the next review cycle',
            'measure' => 'Student records greater than zero',
            'status' => 'Open'
        ];
    } elseif ($evaluationCoveragePct < 100) {
        $cqiActionPlan[] = [
            'priority' => $evaluationCoveragePct < 90 ? 'High' : 'Medium',
            'area' => 'Evaluation completion',
            'finding' => sprintf('%d of %d students have verified evaluations (%.1f%% coverage).', $evaluatedStudents, $totalStudents, $evaluationCoveragePct),
            'action' => 'Follow up on students without verified evaluations and resolve OTP or submission blockers.',
            'owner' => 'Coordinator and Supervisors',
            'timeframe' => 'Before the next evaluation cycle',
            'measure' => 'Reach 100% verified evaluation coverage',
            'status' => 'Open'
        ];
    }

    if ($totalReportsWithEntities === 0) {
        $cqiActionPlan[] = [
            'priority' => 'High',
            'area' => 'Entity extraction coverage',
            'finding' => 'No report entity records are available for analysis.',
            'action' => 'Run the spaCy extraction for submitted reports and verify that matched entities are persisted to report_entities.',
            'owner' => 'System Administrator',
            'timeframe' => 'Before publishing CQI findings',
            'measure' => 'At least one extracted-entity record for each submitted report',
            'status' => 'Open'
        ];
    } elseif ($spacyConfidence < 90) {
        $cqiActionPlan[] = [
            'priority' => 'High',
            'area' => 'Extraction quality',
            'finding' => sprintf('Average stored extraction confidence is %.2f%%.', $spacyConfidence),
            'action' => 'Review low-confidence matches, improve aliases in predefined_entities, and rerun extraction for affected reports.',
            'owner' => 'Coordinator and System Administrator',
            'timeframe' => 'Within the next review cycle',
            'measure' => 'Raise average confidence to at least 90%',
            'status' => 'Open'
        ];
    }

    foreach ($lowPerformingCompanies as $company) {
        $cqiActionPlan[] = [
            'priority' => 'High',
            'area' => 'Company performance',
            'finding' => sprintf('%s has an average verified evaluation score of %.1f%%.', $company['name'], $company['percentage']),
            'action' => 'Review supervisor feedback and create a targeted support plan for the affected placement site.',
            'owner' => 'Coordinator and Company Supervisor',
            'timeframe' => 'Within 30 days',
            'measure' => 'Increase the company average to at least 60% and recheck next cycle',
            'status' => 'Open'
        ];
    }

    if ($companiesWithoutEvaluations) {
        $cqiActionPlan[] = [
            'priority' => 'Medium',
            'area' => 'Company data completeness',
            'finding' => 'No verified evaluations are recorded for: ' . implode(', ', $companiesWithoutEvaluations) . '.',
            'action' => 'Confirm student-company assignments and complete the evaluation workflow for those placements.',
            'owner' => 'Coordinator',
            'timeframe' => 'Before the next dashboard refresh',
            'measure' => 'Every active company has at least one verified evaluation',
            'status' => 'Open'
        ];
    }

    $cqiActionPlan[] = [
        'priority' => 'Continuous',
        'area' => 'Entity taxonomy maintenance',
        'finding' => sprintf('The current leading category is %s with %d recorded occurrences.', $topCategoryName, $topCategoryOccurrences),
        'action' => 'Review the top entities and aliases monthly; merge duplicates, remove generic terms, and add validated clerical or technical terms from new reports.',
        'owner' => 'Coordinator',
        'timeframe' => 'Monthly',
        'measure' => 'Zero duplicate/generic terms and all new approved terms classified in predefined_entities',
        'status' => 'Planned'
    ];
} catch (Throwable $exception) {
    error_log('Coordinator dashboard data error: ' . $exception->getMessage());
    $dashboardError = 'Some dashboard data could not be loaded from the database.';
}

require_once __DIR__ . '/../src/pages/coordinator/dashboardPage.php';
