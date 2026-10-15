<?php
// config/evaluation_criteria.php
// Single source of truth for the OJT Performance Evaluation Form.
// Mirrors public/files/OJT_performance_evaluation_form.docx (CHED OJT form):
// 12 competencies, each rated 5 (Very Good) down to 1 (Very Poor).
//
// The 12 keys, their order and their group buckets are FIXED and live in
// ojtEvaluationCriteriaDefaults() below: evaluations.technical_score /
// work_ethics_score / communication_score and every stored criteria_ratings
// JSON blob are keyed on them.
//
// The WORDING of each competency is coordinator-editable and is read from the
// evaluation_questions table (migration_evaluation_questions.sql). If that table
// is missing, the defaults below are used, so an unmigrated database still
// runs the form unchanged.
//
// This file is PHP-include-only. Never reference it from client JS or markup.
// To share the criteria with the browser, encode ojtEvaluationCriteria()
// into a <script> block as JSON.

/**
 * The 12 competencies exactly as worded in the reference docx, in document order.
 *
 * `group` buckets each competency into one of the three summary groups that the
 * pre-existing `evaluations` columns already store:
 *   technical     -> evaluations.technical_score
 *   ethics        -> evaluations.work_ethics_score
 *   communication -> evaluations.communication_score
 *
 * This is the fallback and the authority for which competencies exist. Only the
 * `text` values are ever overridden from the database.
 *
 * @return array<int, array{key: string, group: string, text: string}>
 */
if (!function_exists('ojtEvaluationCriteriaDefaults')) {
    function ojtEvaluationCriteriaDefaults(): array
    {
        return [
            [
                'key'   => 'apply_knowledge',
                'group' => 'technical',
                'text'  => 'Ability to apply knowledge of information technology to solve IT problems',
            ],
            [
                'key'   => 'design_experiments',
                'group' => 'technical',
                'text'  => 'Ability to design and conduct experiments, as well as to analyze and interpret data',
            ],
            [
                'key'   => 'design_systems',
                'group' => 'technical',
                'text'  => 'Ability to design a system, component, or process to meet desired needs within realistic constraints such as economic, environmental, social, political, ethical, health and safety, manufacturability, and sustainability, in accordance with standards',
            ],
            [
                'key'   => 'multidisciplinary_teams',
                'group' => 'communication',
                'text'  => 'Ability to function on multidisciplinary teams',
            ],
            [
                'key'   => 'identify_solve_problems',
                'group' => 'technical',
                'text'  => 'Ability to identify, formulate, and solve information technology problems',
            ],
            [
                'key'   => 'professional_ethics',
                'group' => 'ethics',
                'text'  => 'Understanding of professional and ethical responsibility',
            ],
            [
                'key'   => 'communicate_effectively',
                'group' => 'communication',
                'text'  => 'Ability to communicate effectively',
            ],
            [
                'key'   => 'global_impact',
                'group' => 'communication',
                'text'  => 'Broad education necessary to understand the impact of IT solutions in a global, economic, environmental, and societal context',
            ],
            [
                'key'   => 'lifelong_learning',
                'group' => 'ethics',
                'text'  => 'Recognition of the need for, and an ability to engage in life-long learning',
            ],
            [
                'key'   => 'contemporary_issues',
                'group' => 'ethics',
                'text'  => 'Knowledge of contemporary issues',
            ],
            [
                'key'   => 'modern_it_tools',
                'group' => 'technical',
                'text'  => 'Ability to use techniques, skills, and modern IT tools necessary for information technology practice',
            ],
            [
                'key'   => 'it_management_principles',
                'group' => 'communication',
                'text'  => 'Knowledge and understanding of information technology and management principles as a member and leader in a team, to manage projects and in multidisciplinary environments',
            ],
        ];
    }
}

/**
 * The live competency list used to RENDER the form a supervisor is about to
 * fill in, so a coordinator's edit takes effect on the next submission.
 *
 * Reads the wording from evaluation_questions when that table exists, and
 * falls back to ojtEvaluationCriteriaDefaults() otherwise. The result is cached
 * for the lifetime of the request: this function is called from controllers,
 * views and the client-side payload builder, and re-querying per call would
 * turn one page render into a dozen identical SELECTs.
 *
 * @return array<int, array{key: string, group: string, text: string}>
 */
if (!function_exists('ojtEvaluationCriteria')) {
    function ojtEvaluationCriteria(): array
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        $defaults = ojtEvaluationCriteriaDefaults();

        global $pdo;

        if ($pdo instanceof PDO) {
            try {
                require_once __DIR__ . '/../src/services/evaluation_questions_store.php';
                $cached = evaluationQuestionsLoad($pdo, $defaults);
            } catch (Throwable $exception) {
                error_log('Evaluation criteria load failed, using defaults: ' . $exception->getMessage());
                $cached = $defaults;
            }
        } else {
            $cached = $defaults;
        }

        return $cached;
    }
}

/**
 * The competency list as it read WHEN AN EVALUATION WAS SUBMITTED.
 *
 * Used by every read-only view of a stored evaluation. An evaluation that
 * carries criteria_snapshot keeps its own frozen wording forever, so
 * rewriting the form never re-labels a record a supervisor already signed.
 * Rows saved before the editable form existed have no snapshot; for those the
 * live wording is used, which matches the previous hardcoded behaviour.
 *
 * @param array $evaluation A row from `evaluations` (or a slice of one).
 * @return array<int, array{key: string, group: string, text: string}>
 */
if (!function_exists('ojtCriteriaForEvaluation')) {
    function ojtCriteriaForEvaluation(array $evaluation): array
    {
        $criteria = ojtEvaluationCriteria();

        $snapshotJson = trim((string) ($evaluation['criteria_snapshot'] ?? ''));

        if ($snapshotJson === '') {
            return $criteria;
        }

        $decoded = json_decode($snapshotJson, true);

        if (!is_array($decoded) || empty($decoded)) {
            return $criteria;
        }

        foreach ($criteria as $index => $criterion) {
            $frozen = $decoded[$criterion['key']] ?? null;
            if (is_string($frozen) && trim($frozen) !== '') {
                $criteria[$index]['text'] = $frozen;
            }
        }

        return $criteria;
    }
}

/**
 * Summary groups shown alongside the overall rating.
 * Order here is the display order.
 *
 * @return array<string, string>
 */
if (!function_exists('ojtEvaluationGroups')) {
    function ojtEvaluationGroups(): array
    {
        return [
            'technical'     => 'Technical Competence & Problem-Solving',
            'ethics'        => 'Professionalism, Ethics & Lifelong Learning',
            'communication' => 'Communication, Teamwork & IT Management',
        ];
    }
}

/**
 * Map each summary group to the legacy `evaluations` column that stores its average.
 *
 * @return array<string, string>
 */
if (!function_exists('ojtGroupColumnMap')) {
    function ojtGroupColumnMap(): array
    {
        return [
            'technical'     => 'technical_score',
            'ethics'        => 'work_ethics_score',
            'communication' => 'communication_score',
        ];
    }
}

/**
 * The docx legend, highest rating first: 5 - Very Good ... 1 - Very Poor.
 *
 * @return array<int, string>
 */
if (!function_exists('ojtRatingScale')) {
    function ojtRatingScale(): array
    {
        return [
            5 => 'Very Good',
            4 => 'Good',
            3 => 'Average',
            2 => 'Poor',
            1 => 'Very Poor',
        ];
    }
}

/**
 * The highest and lowest rating allowed by the form.
 */
if (!function_exists('ojtRatingBounds')) {
    function ojtRatingBounds(): array
    {
        return ['min' => 1, 'max' => 5];
    }
}

/**
 * Legend label for a 1-5 rating. Ratings are rounded to the nearest whole
 * point because the legend has no in-between terms.
 */
if (!function_exists('ojtRatingLabel')) {
    function ojtRatingLabel($rating): string
    {
        $scale = ojtRatingScale();
        $rounded = (int) round((float) $rating);
        return $scale[$rounded] ?? 'Unrated';
    }
}

/**
 * Convert a 1-5 rating to the equivalent 0-100 percentage.
 * Used for reporting only; the stored score stays on the 1-5 scale.
 */
if (!function_exists('ojtRatingPercent')) {
    function ojtRatingPercent($rating): float
    {
        $bounds = ojtRatingBounds();
        $rating = (float) $rating;
        if ($rating <= 0) {
            return 0.0;
        }
        return round((($rating - $bounds['min']) / ($bounds['max'] - $bounds['min'])) * 100, 1);
    }
}

/**
 * Decode a stored `evaluations.criteria_ratings` JSON blob into key => rating.
 * Missing, malformed or out-of-range entries come back as 0 (unrated) so a
 * partially saved or legacy row can never produce a wrong average.
 *
 * @return array<string, int>
 */
if (!function_exists('ojtDecodeRatings')) {
    function ojtDecodeRatings(?string $json): array
    {
        $ratings = [];
        $decoded = $json ? json_decode($json, true) : null;
        if (!is_array($decoded)) {
            $decoded = [];
        }

        $bounds = ojtRatingBounds();
        foreach (ojtEvaluationCriteria() as $criterion) {
            $value = $decoded[$criterion['key']] ?? 0;
            $value = is_numeric($value) ? (int) round((float) $value) : 0;
            $ratings[$criterion['key']] = ($value >= $bounds['min'] && $value <= $bounds['max'])
                ? $value
                : 0;
        }

        return $ratings;
    }
}

/**
 * How many of the 12 competencies carry a valid rating.
 *
 * @param array<string, int> $ratings
 */
if (!function_exists('ojtRatedCount')) {
    function ojtRatedCount(array $ratings): int
    {
        return count(array_filter($ratings, static fn($value) => (int) $value > 0));
    }
}

/**
 * True when all 12 competencies are rated. Used to gate form submission.
 *
 * @param array<string, int> $ratings
 */
if (!function_exists('ojtIsComplete')) {
    function ojtIsComplete(array $ratings): bool
    {
        return ojtRatedCount($ratings) === count(ojtEvaluationCriteria());
    }
}

/**
 * Simple average of every rated competency, on the raw 1-5 scale.
 * Returns 0.0 when nothing is rated.
 *
 * @param array<string, int> $ratings
 */
if (!function_exists('ojtOverallRating')) {
    function ojtOverallRating(array $ratings): float
    {
        $rated = array_filter($ratings, static fn($value) => (int) $value > 0);
        if (empty($rated)) {
            return 0.0;
        }
        return round(array_sum($rated) / count($rated), 2);
    }
}

/**
 * Average rating per summary group, on the raw 1-5 scale.
 * Groups with nothing rated come back as 0.0.
 *
 * @param array<string, int> $ratings
 * @return array<string, float>
 */
if (!function_exists('ojtGroupAverages')) {
    function ojtGroupAverages(array $ratings): array
    {
        $totals = [];
        $counts = [];

        foreach (ojtEvaluationCriteria() as $criterion) {
            $value = (int) ($ratings[$criterion['key']] ?? 0);
            if ($value <= 0) {
                continue;
            }
            $group = $criterion['group'];
            $totals[$group] = ($totals[$group] ?? 0) + $value;
            $counts[$group] = ($counts[$group] ?? 0) + 1;
        }

        $averages = [];
        foreach (array_keys(ojtEvaluationGroups()) as $group) {
            $averages[$group] = isset($counts[$group])
                ? round($totals[$group] / $counts[$group], 2)
                : 0.0;
        }

        return $averages;
    }
}

/**
 * Per-competency ratings shaped for the client-side live score summary.
 * Only safe, non-sensitive descriptor data is exposed.
 *
 * @return array<int, array{key: string, group: string}>
 */
if (!function_exists('ojtCriteriaForClient')) {
    function ojtCriteriaForClient(): array
    {
        return array_map(
            static fn(array $criterion) => [
                'key'   => $criterion['key'],
                'group' => $criterion['group'],
            ],
            ojtEvaluationCriteria()
        );
    }
}
