<?php
// config/db.php

require_once __DIR__ . '/../vendor/autoload.php';

if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

$host     = $_ENV['DB_HOST'] ?? 'localhost';
$dbname   = $_ENV['DB_NAME'] ?? 'nbsc_ojt'; 
$username = $_ENV['DB_USER'] ?? 'root';      
$password = $_ENV['DB_PASSWORD'] ?? '';          

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

/**
 * Retrieve the most accurate client IP address
 */
if (!function_exists('getClientIp')) {
    function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

/**
 * Universal System Audit Logging Helper
 *
 * @param PDO $pdo Active PDO connection instance
 * @param int|null $userId ID of the user performing the action (from users table)
 * @param string $role Role of the user (student, supervisor, coordinator, admin, system)
 * @param string $action Category/Action key (e.g. USER_LOGIN, REPORT_SUBMIT, REPORT_APPROVED)
 * @param string $description Plain-text description of the event
 * @return bool True if logged successfully, false otherwise
 */
if (!function_exists('logActivity')) {
    function logActivity($pdo, $userId, $role, $action, $description) {
        try {
            $ipAddress = getClientIp();
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

            $stmt = $pdo->prepare("
                INSERT INTO audit_logs (user_id, role, action, description, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([
                $userId,
                $role,
                $action,
                $description,
                $ipAddress,
                $userAgent
            ]);
        } catch (Exception $e) {
            error_log("Audit Log Failure: " . $e->getMessage());
            return false;
        }
    }
}