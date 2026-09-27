<!-- src/components/evaluation_scorecard.php -->
<?php
// Renders the answered OJT Performance Evaluation Form: the docx competency grid
// exactly as the supervisor filled it in, with the overall rating placed BELOW it.
//
// Expects:
//   $evaluation  array  row containing at least: criteria_ratings, final_score, grade_equivalent
// Optional:
//   $scorecardVariant  'full' (coordinator only: adds overall rating + group averages)
//                       | 'compact' (supervisor: competency grid + legend only)
//
// Reference: public/files/OJT_performance_evaluation_form.docx
// Docx section order preserved: COMPETENCY table -> Legend -> COMMENTS/REMARKS.

if (!function_exists('ojtScorecardRatingClasses')) {
    /**
     * Traffic-light classes for a 1-5 rating.
     *
 * $uniform collapses the whole scale to ONE colour, used by the supervisor
 * view and by the coordinator evaluation view so neither is colour-coded.
 * The coordinator scorecard modal keeps the per-value colour coding.
     */
    function ojtScorecardRatingClasses(int $rating, bool $uniform = false): array
    {
        if ($uniform) {
            return ['bg-slate-800', 'text-white', 'border-slate-900'];
        }
        $map = [
            5 => ['bg-emerald-600', 'text-white', 'border-emerald-700'],
            4 => ['bg-emerald-50', 'text-emerald-800', 'border-emerald-400'],
            3 => ['bg-blue-50', 'text-blue-800', 'border-blue-400'],
            2 => ['bg-amber-50', 'text-amber-800', 'border-amber-400'],
            1 => ['bg-rose-50', 'text-rose-800', 'border-rose-400'],
        ];
        return $map[$rating] ?? ['bg-slate-100', 'text-slate-400', 'border-slate-200'];
    }
}

$scorecardVariant = $scorecardVariant ?? 'full';
// 'compact' = supervisor: competency grid only.
// 'full'    = coordinator scorecard: grid + overall rating + group averages, colour-coded.
// 'uniform' = coordinator evaluation view: same content as 'full', one colour for every rating.
$isFullVariant    = ($scorecardVariant !== 'compact');
$scorecardUniform = in_array($scorecardVariant, ['compact', 'uniform'], true);

$scorecardRatings = ojtDecodeRatings($evaluation['criteria_ratings'] ?? null);
$scorecardHasRows = ojtRatedCount($scorecardRatings) > 0;
$scorecardOverall = $scorecardHasRows
    ? ojtOverallRating($scorecardRatings)
    : (float) ($evaluation['final_score'] ?? 0);

$scorecardGroupAvgs = ojtGroupAverages($scorecardRatings);
$scorecardGroupCols = [
    'technical'     => $evaluation['technical_score'] ?? null,
    'ethics'        => $evaluation['work_ethics_score'] ?? null,
    'communication' => $evaluation['communication_score'] ?? null,
];

$scorecardLegend = ojtRatingLabel($scorecardOverall);
$scorecardScale  = ojtRatingScale();
?>

<div class="space-y-4">

    <?php if ($scorecardHasRows): ?>

        <!-- ============ ANSWERED COMPETENCY GRID (docx table) ============ -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between gap-3">
                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Competency Rating</span>
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-black border border-slate-300">
                    <?= count($scorecardRatings); ?> items answered
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full border-collapse min-w-[560px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left text-[10px] font-black uppercase tracking-wider text-slate-500 px-4 py-2.5 w-[64%]">Competency</th>
                            <?php foreach ($scorecardScale as $value => $label): ?>
                                <th class="px-2 py-2.5 text-center w-[7.2%]" title="<?= htmlspecialchars($label); ?>">
                                    <span class="block text-[11px] font-black text-slate-900"><?= $value; ?></span>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach (ojtEvaluationCriteria() as $index => $criterion):
                            $rowRating = (int) ($scorecardRatings[$criterion['key']] ?? 0);
                            [$cellBg, $cellText, $cellBorder] = ojtScorecardRatingClasses($rowRating, $scorecardUniform);
                            // The row counter is not a score: keep it neutral in uniform mode
                            // so only the rating chip carries colour.
                            if ($rowRating > 0) {
                                $rowBadgeBg   = $scorecardUniform ? 'bg-slate-200' : $cellBg;
                                $rowBadgeText = $scorecardUniform ? 'text-slate-700' : $cellText;
                            } else {
                                $rowBadgeBg   = 'bg-slate-100';
                                $rowBadgeText = 'text-slate-400';
                            }
                        ?>
                            <tr class="<?= $rowRating > 0 ? 'bg-slate-50/60' : ''; ?>">
                                <td class="px-4 py-2.5 align-top">
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-5 h-5 rounded-md <?= $rowBadgeBg ?> <?= $rowBadgeText ?> flex items-center justify-center font-black text-[10px] shrink-0 mt-0.5"><?= $index + 1; ?></span>
                                        <span class="text-[11px] font-semibold text-slate-700 leading-relaxed"><?= htmlspecialchars($criterion['text']); ?></span>
                                    </div>
                                </td>
                                <?php foreach (array_keys($scorecardScale) as $value): ?>
                                    <td class="px-2 py-2.5 text-center align-top">
                                        <?php if ($rowRating === $value): ?>
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg <?= $cellBg ?> <?= $cellText ?> border <?= $cellBorder ?> text-[10px] font-black"><?= $value; ?></span>
                                        <?php else: ?>
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Legend (docx places it directly under the competency table) -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Legend</span>
                <?php foreach ($scorecardScale as $value => $label):
                    [$legendBg, $legendText] = ojtScorecardRatingClasses($value, $scorecardUniform);
                ?>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-600">
                        <span class="w-4 h-4 rounded <?= $legendBg ?> <?= $legendText ?> flex items-center justify-center font-black text-[9px]"><?= $value; ?></span>
                        <?= htmlspecialchars($label); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($isFullVariant): ?>
            <div class="border-t border-slate-200/70"></div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Legacy rows saved before the docx form shipped have no per-competency data -->
        <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2.5">
            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-[11px] font-medium text-amber-900 leading-relaxed">
                This evaluation was recorded before the 12-competency OJT form was adopted, so no per-competency answers were captured.
                <?php if ($isFullVariant): ?>
                    The overall rating below is the score stored on the legacy 1-100 scale.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($isFullVariant): ?>
    <!-- ============ OVERALL RATING (coordinator only) ============ -->
    <!-- Deliberately withheld from the supervisor: the 'compact' variant is used by
         the supervisor's preview modal and report view. The overall rating and the
         per-group breakdown are coordinator-only. -->
    <div class="space-y-3">
        <div class="flex items-center gap-2.5">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Overall Rating</span>
            <span class="flex-1 h-px bg-slate-200"></span>
        </div>

        <!-- Overall score, computed from the 12 answers above -->
        <div class="flex flex-wrap items-center justify-between gap-4 p-5 bg-[#0F2854] rounded-2xl text-white">
            <div>
                <span class="block text-[10px] font-black uppercase tracking-wider text-blue-200">Average of <?= count($scorecardRatings); ?> Competency Ratings</span>
                <p class="text-4xl font-black mt-1"><?= number_format($scorecardOverall, 2); ?><span class="text-lg text-blue-200 font-bold">/5.00</span></p>
            </div>
            <div class="text-right">
                <span class="block text-[10px] font-black uppercase tracking-wider text-blue-200">Equivalent Rating</span>
                <span class="inline-block mt-1 px-4 py-2 rounded-xl bg-white/10 border border-white/25 font-black text-sm"><?= htmlspecialchars($scorecardLegend); ?></span>
            </div>
        </div>

        <!-- Per-group breakdown of the same answers -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <?php foreach (ojtEvaluationGroups() as $groupKey => $groupLabel):
                $groupValue = $scorecardHasRows
                    ? ($scorecardGroupAvgs[$groupKey] ?? 0.0)
                    : (float) ($scorecardGroupCols[$groupKey] ?? 0);
            ?>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400 leading-tight"><?= htmlspecialchars($groupLabel); ?></span>
                    <p class="text-lg font-black text-slate-900 mt-1">
                        <?= $groupValue > 0 ? number_format($groupValue, 2) . '<span class="text-[11px] text-slate-400 font-bold">/5</span>' : '<span class="text-slate-300">&mdash;</span>'; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
