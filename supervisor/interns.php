<?php
// supervisor/interns.php
session_start();

require_once __DIR__ . '/../config/db.php';

// DEV MODE: Default supervisor ID
$supervisor_id = $_SESSION['supervisor_id'] ?? 1;

// Capture the 'id' parameter from the URL query string (?id=1)
$selected_student_id = isset($_GET['id']) && !empty($_GET['id']) ? intval($_GET['id']) : null;

try {
    // Fetch Supervisor Details
    $stmt = $pdo->prepare("SELECT sup.id, sup.name, c.name AS company_name FROM supervisors sup LEFT JOIN companies c ON sup.company_id = c.id WHERE sup.id = ?");
    $stmt->execute([$supervisor_id]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supervisor) {
        $supervisor = ['name' => '[Supervisor Full Name]', 'company_name' => 'NBSC SASDD'];
    }

    // =========================================================
    // CONDITION 1: IF AN INTERN ID IS PASSED IN THE URL (?id=X)
    // =========================================================
    if ($selected_student_id) {
        
        // Fetch selected student details
        $studentStmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $studentStmt->execute([$selected_student_id]);
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            $student = [
                'id'             => $selected_student_id,
                'name'           => 'Katelyn Coming',
                'student_number' => '20231053',
                'program'        => 'BSIT',
                'email'          => '20231053@nbsc.edu.ph'
            ];
        }

        // Fetch reports submitted by this student
        $reportsStmt = $pdo->prepare("SELECT * FROM reports WHERE student_id = ? ORDER BY week_number ASC");
        $reportsStmt->execute([$selected_student_id]);
        $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Dev placeholder reports if database table is empty
        if (empty($reports)) {
            $reports = [
                ['id' => 1, 'week_number' => 1, 'status' => 'Approved', 'attachment_path' => 'uploads/reports/sample.pdf', 'created_at' => date('Y-m-d')],
                ['id' => 2, 'week_number' => 2, 'status' => 'Approved', 'attachment_path' => 'uploads/reports/sample.pdf', 'created_at' => date('Y-m-d')],
                ['id' => 3, 'week_number' => 3, 'status' => 'Pending',  'attachment_path' => 'uploads/reports/sample.pdf', 'created_at' => date('Y-m-d')],
                ['id' => 4, 'week_number' => 4, 'status' => 'Pending',  'attachment_path' => 'uploads/reports/sample.pdf', 'created_at' => date('Y-m-d')]
            ];
        }

        // LOAD SINGLE PORTFOLIO VIEW AND STOP EXECUTION
        require_once __DIR__ . '/../src/pages/supervisor/internProfilePage.php';
        exit(); 
    }

    // =========================================================
    // CONDITION 2: DEFAULT ROSTER LIST (NO ?id= PARAMETER)
    // =========================================================
    $internsSql = "
        SELECT 
            s.id,
            s.name,
            s.student_number,
            s.program,
            s.email,
            COUNT(r.id) AS submitted_reports
        FROM students s
        LEFT JOIN reports r ON s.id = r.student_id
        WHERE s.supervisor_id = ?
        GROUP BY s.id
    ";
    $internsStmt = $pdo->prepare($internsSql);
    $internsStmt->execute([$supervisor_id]);
    $interns = $internsStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($interns)) {
        $interns = [
            ['id' => 1, 'name' => 'Katelyn Coming', 'student_number' => '20231053', 'program' => 'BSIT', 'submitted_reports' => 4],
            ['id' => 2, 'name' => 'Pauline May Coming', 'student_number' => '20231054', 'program' => 'BSIT', 'submitted_reports' => 4],
            ['id' => 3, 'name' => 'Sander Perejan', 'student_number' => '20231055', 'program' => 'BSIT', 'submitted_reports' => 4]
        ];
    }

    // LOAD ALL INTERNS LIST VIEW
    require_once __DIR__ . '/../src/pages/supervisor/internsPage.php';

} catch (Exception $e) {
    // Fallback error handling
    $supervisor = ['name' => '[Supervisor Full Name]', 'company_name' => 'NBSC SASDD'];
    
    if ($selected_student_id) {
        $student = [
            'id' => $selected_student_id,
            'name' => 'Katelyn Coming',
            'student_number' => '20231053',
            'program' => 'BSIT',
            'email' => '20231053@nbsc.edu.ph'
        ];
        $reports = [
            ['id' => 1, 'week_number' => 1, 'status' => 'Approved', 'attachment_path' => 'uploads/reports/sample.pdf', 'created_at' => date('Y-m-d')]
        ];
        require_once __DIR__ . '/../src/pages/supervisor/internProfilePage.php';
        exit();
    } else {
        $interns = [
            ['id' => 1, 'name' => 'Katelyn Coming', 'student_number' => '20231053', 'program' => 'BSIT', 'submitted_reports' => 4]
        ];
        require_once __DIR__ . '/../src/pages/supervisor/internsPage.php';
    }
}