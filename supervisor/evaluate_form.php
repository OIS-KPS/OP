<?php
// supervisor/evaluate_form.php
session_start();

require_once __DIR__ . '/../config/db.php';

$supervisor_id = $_SESSION['supervisor_id'] ?? 1;
$student_id    = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;

if (!$student_id) {
    header("Location: evaluate_interns.php");
    exit();
}

$message = '';
$error   = '';

// Modal Steps: 'form', 'otp', or 'success'
$currentStep = 'form';
$calculatedScore = 0;

// Step 1: Form Submitted -> Open OTP Modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_form'])) {
    $_SESSION['temp_eval_data'] = [
        'student_id'       => $student_id,
        'tech_skills'      => intval($_POST['tech_skills'] ?? 0),
        'quality_of_work'  => intval($_POST['quality_of_work'] ?? 0),
        'work_ethic'       => intval($_POST['work_ethic'] ?? 0),
        'communication'    => intval($_POST['communication'] ?? 0),
        'initiative'       => intval($_POST['initiative'] ?? 0),
        'remarks'          => trim($_POST['remarks'] ?? '')
    ];
    
    $_SESSION['eval_otp_code'] = '123456'; 
    $currentStep = 'otp';
}

// Step 2: OTP Verified -> Show Success Confirmation Modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $enteredOtp = trim($_POST['otp_code'] ?? '');
    
    if ($enteredOtp === ($_SESSION['eval_otp_code'] ?? '123456')) {
        $evalData = $_SESSION['temp_eval_data'] ?? [];
        
        // Calculate Total Score Out of 100%
        $calculatedScore = ($evalData['tech_skills'] + $evalData['quality_of_work'] + $evalData['work_ethic'] + $evalData['communication'] + $evalData['initiative']) * 4;

        try {
            // Save Evaluation to DB
            $sql = "INSERT INTO evaluations (student_id, supervisor_id, final_score, remarks, status, created_at) VALUES (?, ?, ?, ?, 'Verified', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$student_id, $supervisor_id, $calculatedScore, $evalData['remarks']]);
        } catch (Exception $e) {
            // Dev mode fallback
        }

        // Set state to show Success Confirmation Modal
        $currentStep = 'success';
        
        // Store in session for sample display state on roster page
        if (!isset($_SESSION['dev_evaluations'])) {
            $_SESSION['dev_evaluations'] = [];
        }
        $_SESSION['dev_evaluations'][$student_id] = [
            'status' => 'Verified',
            'score' => $calculatedScore
        ];

        unset($_SESSION['temp_eval_data'], $_SESSION['eval_otp_code']);
    } else {
        $error = "Invalid OTP code entered. Please try again (Dev OTP: 123456).";
        $currentStep = 'otp';
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $student = [
            'id' => $student_id,
            'name' => 'Katelyn Coming',
            'student_number' => '20231053',
            'program' => 'BSIT',
            'email' => '20231053@nbsc.edu.ph'
        ];
    }
} catch (Exception $e) {
    $student = [
        'id' => $student_id,
        'name' => 'Katelyn Coming',
        'student_number' => '20231053',
        'program' => 'BSIT',
        'email' => '20231053@nbsc.edu.ph'
    ];
}

require_once __DIR__ . '/../src/pages/supervisor/evaluateFormPage.php';