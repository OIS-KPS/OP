<?php
// supervisor/evaluate_interns.php
session_start();

require_once __DIR__ . '/../config/db.php';

// DEV MODE: Default supervisor ID
$supervisor_id = $_SESSION['supervisor_id'] ?? 1;

try {
    // 1. Fetch Supervisor Details
    $stmt = $pdo->prepare("SELECT sup.id, sup.name, c.name AS company_name FROM supervisors sup LEFT JOIN companies c ON sup.company_id = c.id WHERE sup.id = ?");
    $stmt->execute([$supervisor_id]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['name' => 'Supervisor', 'company_name' => 'NBSC Host Company'];

    // 2. Fetch Students with WAR progress
    $studentsSql = "
        SELECT 
            s.id,
            s.name,
            s.student_number,
            s.program,
            COUNT(r.id) AS submitted_wars,
            SUM(CASE WHEN r.status = 'Approved' THEN 1 ELSE 0 END) AS approved_wars,
            e.id AS evaluation_id,
            e.final_score,
            e.status AS evaluation_status
        FROM students s
        LEFT JOIN reports r ON s.id = r.student_id
        LEFT JOIN evaluations e ON s.id = e.student_id
        WHERE s.supervisor_id = ?
        GROUP BY s.id
    ";

    $stmtStudents = $pdo->prepare($studentsSql);
    $stmtStudents->execute([$supervisor_id]);
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    $supervisor = ['name' => 'Supervisor', 'company_name' => 'NBSC Host Company'];
    $students = [];
}

// Fallback sample data for development display (12 Weeks / 486 Hours Benchmark)
if (empty($students)) {
    $students = [
        [
            'id' => 1,
            'name' => 'Katelyn Coming',
            'student_number' => '20231053',
            'program' => 'BSIT',
            'submitted_wars' => 12, // Completed all 12 WAR weeks (486 hrs)
            'approved_wars' => 12,
            'evaluation_id' => null,
            'final_score' => null,
            'evaluation_status' => 'Pending'
        ],
        [
            'id' => 2,
            'name' => 'Pauline May Coming',
            'student_number' => '20231054',
            'program' => 'BSIT',
            'submitted_wars' => 8, // Still on Week 8
            'approved_wars' => 7,
            'evaluation_id' => null,
            'final_score' => null,
            'evaluation_status' => 'Incomplete'
        ],
        [
            'id' => 3,
            'name' => 'Sander Perejan',
            'student_number' => '20231055',
            'program' => 'BSIT',
            'submitted_wars' => 12,
            'approved_wars' => 12,
            'evaluation_id' => 501,
            'final_score' => 95.5,
            'evaluation_status' => 'Verified' // Evaluated & verified via OTP
        ]
    ];
}

require_once __DIR__ . '/../src/pages/supervisor/evaluateInternsPage.php';