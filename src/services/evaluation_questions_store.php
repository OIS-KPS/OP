<?php
// src/services/evaluation_questions_store.php
/**
 * Read/write access to the coordinator-editable wording of the 12 OJT
 * evaluation competencies.
 *
 * The canonical shape of a competency (key, group, order) is fixed in
 * config/evaluation_criteria.php and must never drift: evaluations
 * .technical_score / work_ethics_score / communication_score and every stored
 * criteria_ratings JSON blob are keyed on those keys and groups. Only the
 * wording is editable, and it lives here.
 *
 * Two things are deliberately kept apart:
 *
 *   evaluationQuestionsLoad()   the LIVE wording. Used to render the form a
 *                               supervisor is about to fill in, so a
 *                               coordinator's edit shows up on the very next
 *                               submission.
 *
 *   ojtCriteriaForEvaluation()  the wording FROZEN on a submitted evaluation.
 *                               Used by every read-only view, so a signed
 *                               record keeps displaying the 12 competencies
 *                               that were actually answered.
 *
 * Every read falls back to the hardcoded defaults rather than throwing: a
 * portal whose database has not been migrated must still be able to run an
 * evaluation form.
 */

if (!function_exists('evaluationQuestionsTableExists')) {
    /**
     * True when the migration has been applied.
     */
    function evaluationQuestionsTableExists(PDO $pdo): bool
    {
        try {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
            );
            $stmt->execute(['evaluation_questions']);

            return (int) $stmt->fetchColumn() > 0;
        } catch (Throwable $exception) {
            error_log('evaluation_questions table probe failed: ' . $exception->getMessage());

            return false;
        }
    }
}

if (!function_exists('evaluationQuestionsLoad')) {
    /**
     * The live competency list, in display order.
     *
     * Rows are matched back onto the canonical criteria list so a stray or
     * reordered database row can never change which 12 competencies exist,
     * what group they belong to, or the order they print in.
     *
     * @return array<int, array{key: string, group: string, text: string}>
     */
    function evaluationQuestionsLoad(PDO $pdo, array $defaults): array
    {
        if (empty($defaults) || !evaluationQuestionsTableExists($pdo)) {
            return $defaults;
        }

        try {
            $stmt = $pdo->query('SELECT criterion_key, question_text FROM evaluation_questions');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $exception) {
            error_log('evaluation_questions read failed: ' . $exception->getMessage());

            return $defaults;
        }

        $textByKey = [];
        foreach ($rows as $row) {
            $key = trim((string) ($row['criterion_key'] ?? ''));
            $text = trim((string) ($row['question_text'] ?? ''));

            // An empty stored question would render a blank competency on the
            // form, so the default wording is used instead.
            if ($key !== '' && $text !== '') {
                $textByKey[$key] = $text;
            }
        }

        if (empty($textByKey)) {
            return $defaults;
        }

        foreach ($defaults as $index => $criterion) {
            $override = $textByKey[$criterion['key']] ?? null;
            if ($override !== null) {
                $defaults[$index]['text'] = $override;
            }
        }

        return $defaults;
    }
}

if (!function_exists('evaluationQuestionsSave')) {
    /**
     * Persist edited wording for the 12 fixed competency keys.
     *
     * Keys are taken from the canonical list, never from the request, so a
     * crafted POST cannot introduce a new competency, retarget an existing
     * one's ratings, or reorder the form.
     *
     * @param array $defaults Canonical criteria (source of the 12 keys).
     * @param array $textByKey Submitted wording keyed by criterion key.
     * @return array{ok: bool, saved: int, errors: array<string, string>}
     */
    function evaluationQuestionsSave(PDO $pdo, array $defaults, array $textByKey, ?int $updatedBy = null): array
    {
        $result = ['ok' => false, 'saved' => 0, 'errors' => []];

        if (!evaluationQuestionsTableExists($pdo)) {
            $result['errors']['_form'] = 'The evaluation questions table has not been created. Run the migration before saving.';

            return $result;
        }

        $values = [];

        foreach ($defaults as $criterion) {
            $key = (string) $criterion['key'];
            $text = trim((string) ($textByKey[$key] ?? ''));

            if ($text === '') {
                $result['errors'][$key] = 'This competency cannot be left blank.';
                continue;
            }

            if (mb_strlen($text) > 1000) {
                $result['errors'][$key] = 'Please keep this to 1000 characters or fewer.';
                continue;
            }

            $values[$key] = $text;
        }

        if (!empty($result['errors'])) {
            return $result;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'UPDATE evaluation_questions
                 SET question_text = ?, updated_by = ?
                 WHERE criterion_key = ?'
            );

            foreach ($values as $key => $text) {
                $stmt->execute([$text, $updatedBy, $key]);
            }

            $pdo->commit();
            $result['ok'] = true;
            $result['saved'] = count($values);
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('evaluation_questions save failed: ' . $exception->getMessage());
            $result['errors']['_form'] = 'The questions could not be saved. Please try again.';
        }

        return $result;
    }
}

if (!function_exists('evaluationQuestionsSnapshot')) {
    /**
     * Freeze the live wording into the JSON blob stored on an evaluation row.
     *
     * @return string JSON of {criterion_key: question_text}.
     */
    function evaluationQuestionsSnapshot(array $criteria): string
    {
        $snapshot = [];

        foreach ($criteria as $criterion) {
            $key = trim((string) ($criterion['key'] ?? ''));
            if ($key !== '') {
                $snapshot[$key] = (string) ($criterion['text'] ?? '');
            }
        }

        $json = json_encode($snapshot);

        return $json === false ? '{}' : $json;
    }
}

if (!function_exists('evaluationQuestionsHasSnapshot')) {
    /**
     * True when an evaluation row carries its own frozen wording.
     */
    function evaluationQuestionsHasSnapshot(array $evaluation): bool
    {
        return trim((string) ($evaluation['criteria_snapshot'] ?? '')) !== '';
    }
}