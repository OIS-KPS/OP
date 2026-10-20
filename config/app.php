<?php
// config/app.php

require_once __DIR__ . '/../vendor/autoload.php';

if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

if (!defined('SUB_URL')) {
    $subUrl = isset($_ENV['SUB_URL']) ? trim($_ENV['SUB_URL'], '/') : 'ICS-PORTAL';
    define('SUB_URL', $subUrl);
}

if (!defined('APP_URL')) {
    $appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/');
    define('APP_URL', $appUrl);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', SUB_URL !== '' ? APP_URL . '/' . SUB_URL : APP_URL);
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', SUB_URL !== '' ? '/' . SUB_URL : '');
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return $path !== '' ? BASE_URL . '/' . $path : BASE_URL;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return $path !== '' ? BASE_PATH . '/' . $path : BASE_PATH;
    }
}
