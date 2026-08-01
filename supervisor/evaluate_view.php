<?php
// supervisor/evaluate_view.php
session_start();

require_once __DIR__ . '/../config/db.php';

$supervisor_id = $_SESSION['supervisor_id'] ?? 1;
$evaluation_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$student_id    = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;

$evaluation = null;

try {
    // Query evaluation with student details
    $sql = "
        SELECT 
            e.id AS evaluation_id,
            e.final_score,
            e.remarks,
            e.status,
            e.created_at AS evaluated_at,
            s.id AS student_id,
            s.name AS student_name,
            s.student_number,
            s.program,
            s.email AS student_email,
            sup.name AS supervisor_name,
            c.name AS company_name
        FROM evaluations e
        JOIN students s ON e.student_id = s.id
        LEFT JOIN supervisors sup ON e.supervisor_id = sup.id
        LEFT JOIN companies c ON sup.company_id = c.id
        WHERE " . ($evaluation_id ? "e.id = ?" : "e.student_id = ?") . "
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$evaluation_id ?: $student_id]);
    $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Database fallback
}

// Fallback sample data for development display
if (!$evaluation) {
    $evaluation = [
        'evaluation_id'   => $evaluation_id ?: 501,
        'student_id'      => 3,
        'student_name'    => 'Sander Perejan',
        'student_number'  => '20231055',
        'program'         => 'BSIT',
        'student_email'   => 'sander.perejan@nbsc.edu.ph',
        'supervisor_name' => 'Supervisor Portal User',
        'company_name'    => 'ICS Host Company',
        'final_score'     => 95.5,
        'status'          => 'Verified',
        'evaluated_at'    => '2026-07-28 14:30:00',
        'remarks'         => 'Sander has shown exceptional performance throughout his 486-hour internship. His work in network setup, system administration, and software installation was consistent and reliable.',
        // Score breakdown mock details (Out of 5 points each)
        'scores' => [
            'tech_skills'     => 5,
            'quality_of_work' => 5,
            'work_ethic'      => 5,
            'communication'   => 4,
            'initiative'      => 5
        ]
    ];
} else {
    // Estimated criteria score calculation from total final_score
    $avgRating = round($evaluation['final_score'] / 20, 1);
    $evaluation['scores'] = [
        'tech_skills'     => min(5, ceil($avgRating)),
        'quality_of_work' => min(5, floor($avgRating)),
        'work_ethic'      => 5,
        'communication'   => min(5, floor($avgRating)),
        'initiative'      => 5
    ];
}

require_once __DIR__ . '/../src/pages/supervisor/evaluateViewPage.php';