<?php
// In-app notification helpers.

if (!function_exists('createNotification')) {
    function createNotification(PDO $pdo, int $userId, string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        if ($userId <= 0) {
            return;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, link_url, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
            $stmt->execute([$userId, $title, $message, $type, $link]);
        } catch (Throwable $e) {
            error_log('Create notification failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('getUnreadNotificationCount')) {
    function getUnreadNotificationCount(PDO $pdo, int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$userId]);
            return (int) $stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log('Unread notification count failed: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('getRecentNotifications')) {
    function getRecentNotifications(PDO $pdo, int $userId, int $limit = 10): array
    {
        if ($userId <= 0) {
            return [];
        }
        try {
            $stmt = $pdo->prepare("SELECT id, title, message, type, link_url, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT ?");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log('Recent notifications failed: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('markNotificationRead')) {
    function markNotificationRead(PDO $pdo, int $id, int $userId): void
    {
        if ($id <= 0 || $userId <= 0) {
            return;
        }
        try {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
        } catch (Throwable $e) {
            error_log('Mark notification read failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('markAllNotificationsRead')) {
    function markAllNotificationsRead(PDO $pdo, int $userId): void
    {
        if ($userId <= 0) {
            return;
        }
        try {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$userId]);
        } catch (Throwable $e) {
            error_log('Mark all notifications read failed: ' . $e->getMessage());
        }
    }
}