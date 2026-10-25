<?php
// coordinator/assignment_roster.php
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Coordinator authentication is required.',
    ]);
    exit();
}

require_once __DIR__ . '/../config/db.php';

try {
    $defaultSection = section_course_codes()[0];
    $stmt = $pdo->prepare("
        SELECT
            s.id,
            s.student_number,
            COALESCE(s.section, :defaultSection) AS section,
            s.office_id,
            s.company_id,
            s.supervisor_id,
            u.name,
            u.avatar_url,
            c.name AS company_name
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN companies c ON s.company_id = c.id
        WHERE u.status = 'active'
        ORDER BY s.section ASC, u.name ASC
    ");
    $stmt->execute(['defaultSection' => $defaultSection]);

    echo json_encode([
        'success' => true,
        'students' => $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [],
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
} catch (Throwable $e) {
    error_log('Assignment roster refresh failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'The intern list could not be refreshed.',
    ]);
}
