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
$totalReports = 0;
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
$lowItCompanies = [];
$companiesWithoutEvaluations = [];
$highestItCompany = null;
$lowestItCompany = null;
$topFrequentEntities = [];
$entityPrioritySummary = [];
$cqiSummary = [];
$cqiRecommendations = [];
$cqiOverallRecommendation = '';
// Backward-compatible alias for controllers or views that still use the old name.
$cqiActionPlan =& $cqiRecommendations;
$entityFrequencyAnalysis = [];

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

    // 3. Total submitted reports used as the report-coverage denominator.
    $stmt = $pdo->query('SELECT COUNT(*) FROM reports');
    $totalReports = (int) ($stmt->fetchColumn() ?: 0);

    // 4. Technical share of all persisted extracted/report entities.
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

    // 6. Company performance from verified evaluation final scores.
    // Companies with no verified evaluations remain visible with 0.0.
    // 6. Company evaluation performance and entity activity are aggregated
    // separately so multiple report entities cannot duplicate evaluation scores.
    $companyEvaluationRows = [];
    $stmt = $pdo->query(
        "SELECT
            s.company_id,
            COUNT(DISTINCT s.id) AS student_count,
            COUNT(DISTINCT CASE WHEN e.otp_verified = 1 THEN e.student_id END) AS verified_evaluations,
            COUNT(DISTINCT CASE WHEN e.otp_verified = 1 AND e.final_score IS NOT NULL THEN e.student_id END) AS scored_evaluations,
            COALESCE(ROUND(AVG(CASE WHEN e.otp_verified = 1 THEN e.final_score END), 1), 0) AS percentage
         FROM students s
         LEFT JOIN evaluations e ON e.student_id = s.id
         GROUP BY s.company_id"
    );
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $companyEvaluationRows[(string) $row['company_id']] = $row;
    }

    $companyEntityRows = [];
    $stmt = $pdo->query(
        "SELECT
            s.company_id,
            COUNT(re.id) AS entity_occurrences,
            SUM(CASE WHEN re.it_related = 'yes' THEN 1 ELSE 0 END) AS it_related_occurrences
         FROM students s
         INNER JOIN reports r ON r.student_id = s.id
         INNER JOIN report_entities re ON re.report_id = r.id
         GROUP BY s.company_id"
    );
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $companyEntityRows[(string) $row['company_id']] = $row;
    }

    $stmt = $pdo->query('SELECT id, name FROM companies ORDER BY name ASC');
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $company) {
        $companyId = (string) $company['id'];
        $evaluation = $companyEvaluationRows[$companyId] ?? [];
        $entity = $companyEntityRows[$companyId] ?? [];
        $percentage = (float) ($evaluation['percentage'] ?? 0);
        $entityOccurrences = (int) ($entity['entity_occurrences'] ?? 0);
        $itOccurrences = (int) ($entity['it_related_occurrences'] ?? 0);

        $companyPerformance[] = [
            'name' => (string) $company['name'],
            'student_count' => (int) ($evaluation['student_count'] ?? 0),
            'verified_evaluations' => (int) ($evaluation['verified_evaluations'] ?? 0),
            'scored_evaluations' => (int) ($evaluation['scored_evaluations'] ?? 0),
            'entity_occurrences' => $entityOccurrences,
            'it_related_occurrences' => $itOccurrences,
            'it_task_ratio' => $entityOccurrences > 0
                ? round(($itOccurrences / $entityOccurrences) * 100, 1)
                : null,
            'it_task_level' => $entityOccurrences > 0
                ? dashboardLevel(round(($itOccurrences / $entityOccurrences) * 100, 1))
                : 'No data',
            'percentage' => $percentage,
            'level' => dashboardLevel($percentage)
        ];
    }

    // Only companies with entity evidence can be ranked by IT-task ratio.
    $companiesWithEntityEvidence = array_values(array_filter(
        $companyPerformance,
        static fn (array $company): bool => (int) $company['entity_occurrences'] > 0
    ));
    usort($companiesWithEntityEvidence, static function (array $left, array $right): int {
        return ($right['it_task_ratio'] <=> $left['it_task_ratio'])
            ?: strcasecmp($left['name'], $right['name']);
    });
    $highestItCompany = $companiesWithEntityEvidence[0] ?? null;
    $lowestItCompany = $companiesWithEntityEvidence ? $companiesWithEntityEvidence[array_key_last($companiesWithEntityEvidence)] : null;
    $lowItCompanies = array_values(array_filter(
        $companiesWithEntityEvidence,
        static fn (array $company): bool => (float) ($company['it_task_ratio'] ?? 0) < 60
    ));

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

    // Build one compact row per entity/category/classification for the
    // scrollable analysis card. Frequencies remain based on COUNT(*) from the
    // report_entities rows already grouped by company and report date.
    $frequencyRows = [];
    foreach ($entitiesData as $entity) {
        $analysisKey = strtolower(trim($entity['entity'])) . '|' . strtolower(trim($entity['category'])) . '|' . strtolower(trim($entity['classification']));
        if (!isset($frequencyRows[$analysisKey])) {
            $frequencyRows[$analysisKey] = [
                'entity' => $entity['entity'],
                'category' => $entity['category'],
                'classification' => $entity['classification'],
                'frequency' => 0,
                'companies' => []
            ];
        }
        $frequencyRows[$analysisKey]['frequency'] += max(0, (int) $entity['frequency']);
        $frequencyRows[$analysisKey]['companies'][$entity['company']] = true;
    }

    foreach ($frequencyRows as $row) {
        $row['company_count'] = count($row['companies']);
        unset($row['companies']);
        $entityFrequencyAnalysis[] = $row;
    }

    usort($entityFrequencyAnalysis, static function (array $left, array $right): int {
        $frequencyOrder = $right['frequency'] <=> $left['frequency'];
        return $frequencyOrder !== 0
            ? $frequencyOrder
            : strcasecmp($left['entity'], $right['entity']);
    });

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

    // Priority is based on each verified entity's share of all occurrences.
    // This is relative to the current academic dataset and does not invent
    // frequencies when no entity records exist.
    foreach ($entityFrequencyAnalysis as $entity) {
        $frequency = max(0, (int) ($entity['frequency'] ?? 0));
        $sharePct = $totalEntityOccurrences > 0
            ? round(($frequency / $totalEntityOccurrences) * 100, 1)
            : 0.0;
        $priority = $sharePct >= 20
            ? 'High'
            : ($sharePct >= 5 ? 'Medium' : 'Low');
        $entityPrioritySummary[] = [
            'entity' => (string) $entity['entity'],
            'category' => (string) $entity['category'],
            'classification' => (string) $entity['classification'],
            'frequency' => $frequency,
            'share_pct' => $sharePct,
            'priority' => $priority,
            'company_count' => (int) ($entity['company_count'] ?? 0)
        ];
    }
    $topFrequentEntities = array_slice($entityPrioritySummary, 0, 10);

    usort($topEntities, static function (array $left, array $right): int {
        return $right['frequency'] <=> $left['frequency'];
    });
    $topEntities = array_slice($topEntities, 0, 5);

    $stmt = $pdo->query('SELECT COUNT(DISTINCT report_id) FROM report_entities');
    $totalReportsWithEntities = (int) ($stmt->fetchColumn() ?: 0);

    foreach ($companyPerformance as $company) {
        if ((int) ($company['verified_evaluations'] ?? 0) === 0) {
            $companiesWithoutEvaluations[] = $company['name'];
        } elseif ((float) ($company['percentage'] ?? 0) < 60) {
            $lowPerformingCompanies[] = $company;
        }
    }

    $dataStatus = 'On track';
    if ($totalStudents === 0 || $totalReports === 0 || $totalReportsWithEntities === 0) {
        $dataStatus = 'Insufficient data';
    } elseif ($evaluationCoveragePct < 90 || $spacyConfidence < 90 || $lowPerformingCompanies || $companiesWithoutEvaluations || $totalReportsWithEntities < $totalReports) {
        $dataStatus = 'Needs attention';
    }

    $companyComparisonText = 'No company has enough extracted-entity evidence for an IT-ratio comparison.';
    if ($highestItCompany && $lowestItCompany) {
        $companyComparisonText = sprintf(
            'The highest IT-related task ratio is recorded by %s at %.1f%%, while the lowest among companies with entity evidence is %s at %.1f%%.',
            $highestItCompany['name'],
            $highestItCompany['it_task_ratio'],
            $lowestItCompany['name'],
            $lowestItCompany['it_task_ratio']
        );
    }

    $cqiSummary = [
        'status' => $dataStatus,
        'period' => 'Current academic records in the database',
        'narrative' => sprintf(
            'The dashboard covers %d students, %d verified evaluations, and %d submitted reports, of which %d have persisted extracted entities. Entity activity is %s technical and %s clerical by recorded occurrence. %s The most frequent category is %s with %d occurrence(s).',
            $totalStudents,
            $evaluatedStudents,
            $totalReports,
            $totalReportsWithEntities,
            number_format($totalEntityOccurrences > 0 ? ($technicalEntityOccurrences / $totalEntityOccurrences) * 100 : 0, 1) . '%',
            number_format($totalEntityOccurrences > 0 ? ($clericalEntityOccurrences / $totalEntityOccurrences) * 100 : 0, 1) . '%',
            $companyComparisonText,
            $topCategoryName,
            $topCategoryOccurrences
        ),
        'evidence' => [
            'evaluation_coverage_pct' => $evaluationCoveragePct,
            'verified_evaluations' => $evaluatedStudents,
            'total_students' => $totalStudents,
            'total_reports' => $totalReports,
            'technical_entity_occurrences' => $technicalEntityOccurrences,
            'clerical_entity_occurrences' => $clericalEntityOccurrences,
            'other_entity_occurrences' => $otherEntityOccurrences,
            'reports_with_entities' => $totalReportsWithEntities,
            'average_extraction_confidence' => $spacyConfidence,
            'top_category' => $topCategoryName,
            'top_category_occurrences' => $topCategoryOccurrences,
            'low_performing_companies' => count($lowPerformingCompanies),
            'companies_without_evaluations' => count($companiesWithoutEvaluations),
            'highest_it_company' => $highestItCompany,
            'lowest_it_company' => $lowestItCompany,
            'low_it_companies' => $lowItCompanies,
            'entity_priorities' => $entityPrioritySummary
        ],
        'top_entities' => $topEntities
    ];

    // Strengths and gaps are generated from live thresholds and evidence.
    $cqiStrengths = [];
    $cqiGaps = [];
    if ($totalStudents > 0 && $evaluationCoveragePct >= 90) {
        $cqiStrengths[] = sprintf('Evaluation coverage is %.1f%%, meeting the 90%% monitoring threshold.', $evaluationCoveragePct);
    }
    if ($spacyConfidence >= 90) {
        $cqiStrengths[] = sprintf('Average verified extraction confidence is %.2f%%.', $spacyConfidence);
    }
    if ($totalReportsWithEntities > 0) {
        $cqiStrengths[] = sprintf('%d report(s) contain persisted extracted-entity evidence.', $totalReportsWithEntities);
    }
    if ($evaluationCoveragePct < 100 && $totalStudents > 0) {
        $cqiGaps[] = sprintf('Evaluation coverage is %.1f%%, below the 100%% completion target.', $evaluationCoveragePct);
    }
    if ($totalReports > $totalReportsWithEntities) {
        $cqiGaps[] = sprintf('%d submitted report(s) do not yet have persisted entity records.', $totalReports - $totalReportsWithEntities);
    }
    if ($totalEntityOccurrences > 0 && $spacyConfidence < 90) {
        $cqiGaps[] = sprintf('Average extraction confidence is %.2f%%, below the 90%% review threshold.', $spacyConfidence);
    }
    foreach ($lowPerformingCompanies as $company) {
        $cqiGaps[] = sprintf('%s has an average verified score of %.1f%%.', $company['name'], $company['percentage']);
    }
    if ($companiesWithoutEvaluations) {
        $cqiGaps[] = 'No verified evaluation is recorded for: ' . implode(', ', $companiesWithoutEvaluations) . '.';
    }
    if (!$cqiStrengths && !$cqiGaps) {
        $cqiGaps[] = 'No CQI evidence is available yet; establish the first reporting baseline.';
    }
    $cqiSummary['strengths'] = $cqiStrengths;
    $cqiSummary['gaps'] = $cqiGaps;

    // One overall academic recommendation replaces multiple repetitive entity actions.
    $recommendationPriority = 'Continuous';
    $recommendationParts = [];
    if ($totalStudents === 0) {
        $recommendationPriority = 'High';
        $recommendationParts[] = 'establish a reliable student and evaluation baseline before interpreting academic outcomes';
    } elseif ($evaluationCoveragePct < 100) {
        $recommendationPriority = $evaluationCoveragePct < 90 ? 'High' : 'Medium';
        $recommendationParts[] = sprintf('complete the remaining student evaluations to move coverage from %.1f%% to 100%%', $evaluationCoveragePct);
    }
    if ($totalReports === 0 || $totalReportsWithEntities < $totalReports) {
        $recommendationPriority = 'High';
        $recommendationParts[] = sprintf('run and verify entity extraction for the %d submitted report(s) without persisted entity records', max(0, $totalReports - $totalReportsWithEntities));
    }
    if ($lowestItCompany) {
        $lowestRatio = (float) $lowestItCompany['it_task_ratio'];
        if ($lowestRatio < 60) {
            $recommendationPriority = $lowestRatio < 40 ? 'High' : ($recommendationPriority === 'High' ? 'High' : 'Medium');
            $recommendationParts[] = sprintf('support %s in increasing its IT-related task exposure from %.1f%% toward at least 60%% using approved activities aligned with academic learning outcomes', $lowestItCompany['name'], $lowestRatio);
        }
    }
    if ($highestItCompany && $lowestItCompany && $highestItCompany['name'] !== $lowestItCompany['name']) {
        $recommendationParts[] = sprintf('use %s, currently at %.1f%% IT-related task exposure, as a benchmark for improving lower-performing placements', $highestItCompany['name'], $highestItCompany['it_task_ratio']);
    }
    if ($totalEntityOccurrences > 0 && $spacyConfidence < 90) {
        $recommendationPriority = 'High';
        $recommendationParts[] = 'review predefined aliases and rerun low-confidence matches before using the entity distribution for academic decisions';
    }
    if (!$recommendationParts) {
        $recommendationParts[] = 'continue monthly review of verified evaluations, company IT-task exposure, and predefined entity classifications';
    }
    $cqiOverallRecommendation = 'For the next academic CQI cycle, the program should ' . implode('; ', $recommendationParts) . '.';

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

    if ($totalReports === 0 || $totalReportsWithEntities < $totalReports) {
        $cqiActionPlan[] = [
            'priority' => 'High',
            'area' => 'Entity extraction coverage',
            'finding' => sprintf('%d of %d submitted report(s) have persisted entity records.', $totalReportsWithEntities, $totalReports),
            'action' => 'Run the spaCy extraction for submitted reports and verify that matched entities are persisted to report_entities.',
            'owner' => 'System Administrator',
            'timeframe' => 'Before publishing CQI findings',
            'measure' => 'At least one extracted-entity record for each submitted report',
            'status' => 'Open'
        ];
    }

    if ($totalEntityOccurrences > 0 && $spacyConfidence < 90) {
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
            'area' => 'Company evaluation performance',
            'finding' => sprintf('%s has an average verified evaluation score of %.1f%%.', $company['name'], $company['percentage']),
            'action' => 'Review supervisor feedback and create a targeted academic support plan for the affected placement site.',
            'owner' => 'Coordinator and Company Supervisor',
            'timeframe' => 'Within 30 days',
            'measure' => 'Increase the company evaluation average to at least 60% and recheck next cycle',
            'status' => 'Open'
        ];
    }

    // Every company below the academic IT-ratio threshold receives a separate
    // recommendation. Companies without entity evidence are not assigned a
    // ratio and are handled by the data-completeness action instead.
    foreach ($lowItCompanies as $company) {
        $itRatio = (float) ($company['it_task_ratio'] ?? 0);
        $cqiActionPlan[] = [
            'priority' => $itRatio < 40 ? 'High' : 'Medium',
            'area' => 'Company IT-task exposure',
            'finding' => sprintf('%s has %.1f%% IT-related task occurrences (%d of %d).', $company['name'], $itRatio, $company['it_related_occurrences'], $company['entity_occurrences']),
            'action' => 'Coordinate with the placement supervisor to assign or document additional approved IT-related activities aligned with the student learning outcomes.',
            'owner' => 'Coordinator and Company Supervisor',
            'timeframe' => 'Before the next academic review cycle',
            'measure' => 'Reach at least 60% IT-related task occurrences with verified report evidence',
            'status' => 'Open'
        ];
    }

    // Promote highly recurring entities for curriculum and catalog review.
    foreach ($entityPrioritySummary as $entity) {
        if (!in_array($entity['priority'], ['High', 'Medium'], true)) {
            continue;
        }
        $cqiActionPlan[] = [
            'priority' => $entity['priority'],
            'area' => 'Entity priority: ' . $entity['entity'],
            'finding' => sprintf('%s occurs %d time(s), representing %.1f%% of extracted occurrences.', $entity['entity'], $entity['frequency'], $entity['share_pct']),
            'action' => $entity['priority'] === 'High'
                ? 'Review this recurring activity against the academic learning outcomes and standardize its approved aliases and documentation guidance.'
                : 'Monitor this activity and verify that its classification and aliases remain accurate in predefined_entities.',
            'owner' => 'Coordinator and Academic Adviser',
            'timeframe' => $entity['priority'] === 'High' ? 'Within 30 days' : 'During the next monthly review',
            'measure' => 'Validated entity classification and documented alignment with the academic program outcomes',
            'status' => 'Planned'
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
