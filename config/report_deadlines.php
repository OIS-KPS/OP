<?php
// include-only helper for weekly accomplishment report deadlines
date_default_timezone_set('Asia/Manila');

function warOjtStart(?array $student): ?DateTimeImmutable {
    if (empty($student['ojt_start_date'])) {
        return null;
    }

    $date = new DateTimeImmutable($student['ojt_start_date']);
    $weekday = (int) $date->format('N'); // 1 = Monday ... 7 = Sunday

    // Normalize any stored date to the Monday that starts its week.
    return $date->modify('-' . ($weekday - 1) . ' days')->setTime(0, 0, 0);
}

function warWeekStart(DateTimeImmutable $start, int $week): DateTimeImmutable {
    return $start->modify('+' . (($week - 1) * 7) . ' days');
}

function warWeekDue(DateTimeImmutable $start, int $week): DateTimeImmutable {
    return warWeekStart($start, $week)->modify('+6 days')->setTime(23, 59, 59);
}

function warCurrentWeek(DateTimeImmutable $start): ?int {
    $today = new DateTimeImmutable('today');

    if ($today < $start) {
        return null;
    }

    $days = (int) $start->diff($today)->format('%a');
    return intdiv($days, 7) + 1;
}

function warIsLate(DateTimeImmutable $submittedAt, DateTimeImmutable $start, int $week): bool {
    return $submittedAt > warWeekDue($start, $week);
}

function warDeadlineNotice(?DateTimeImmutable $start, ?int $currentWeek, bool $hasSubmittedCurrentWeek): ?array {
    if (!$start || !$currentWeek) {
        return null;
    }

    $today = new DateTimeImmutable('today');
    $due = warWeekDue($start, $currentWeek)->setTime(0, 0, 0);
    $days = (int) $today->diff($due)->format('%r%a');

    if ($today == $due) {
        return [
            'level' => 'warning',
            'text'  => "Week {$currentWeek} report is due today (Sunday). Submit before end of day to avoid a Late submission.",
        ];
    }

    if ($days > 0 && $days <= 2) {
        return [
            'level' => 'warning',
            'text'  => "Week {$currentWeek} report deadline is in {$days} day(s) (due {$due->format('M d, Y')}).",
        ];
    }

    if ($days < 0 && !$hasSubmittedCurrentWeek) {
        return [
            'level' => 'danger',
            'text'  => "Week {$currentWeek} report deadline passed on {$due->format('M d, Y')}. Any submission now will be marked Late.",
        ];
    }

    return null;
}
?>