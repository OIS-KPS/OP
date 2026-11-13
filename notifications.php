<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];

// Mark one as read
if (isset($_GET['mark'])) {
    markNotificationRead($pdo, (int) $_GET['mark'], $userId);
    header('Location: notifications.php' . (isset($_GET['page']) ? '?page=' . (int) $_GET['page'] : ''));
    exit();
}

// Mark all as read
if (isset($_GET['markall'])) {
    markAllNotificationsRead($pdo, $userId);
    header('Location: notifications.php' . (isset($_GET['page']) ? '?page=' . (int) $_GET['page'] : ''));
    exit();
}

$perPage = 20;
$page = max(1, (int) ($_GET['page'] ?? 1));

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
$stmtCount->execute([$userId]);
$total = (int) $stmtCount->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("
    SELECT id, title, message, type, link_url, is_read, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC, id DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $userId, PDO::PARAM_INT);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="<?= BASE_PATH; ?>/public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php include __DIR__ . '/src/components/header.php'; ?>
    <main class="max-w-4xl mx-auto p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-[#0F2854]">Notifications</h1>
            <?php if ($total > 0): ?>
                <a href="notifications.php?markall=1" class="text-sm font-semibold text-[#0F2854] hover:underline">Mark all as read</a>
            <?php endif; ?>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-500">
                No notifications yet.
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100">
                <?php foreach ($notifications as $notif): ?>
                    <?php
                    $link = !empty($notif['link_url']) ? (BASE_PATH . '/' . ltrim($notif['link_url'], '/')) : null;
                    ?>
                    <div class="p-4 <?= empty($notif['is_read']) ? 'bg-blue-50/40' : ''; ?>">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($notif['title']); ?></p>
                                <p class="text-sm text-slate-600 mt-1"><?= htmlspecialchars($notif['message']); ?></p>
                                <p class="text-xs text-slate-400 mt-2"><?= date('M d, Y g:i A', strtotime($notif['created_at'])); ?></p>
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                <?php if ($link): ?>
                                    <a href="<?= htmlspecialchars($link); ?>?notif_id=<?= (int) $notif['id']; ?>" class="text-xs font-semibold text-[#0F2854] hover:underline">Open</a>
                                <?php endif; ?>
                                <?php if (empty($notif['is_read'])): ?>
                                    <a href="notifications.php?mark=<?= (int) $notif['id']; ?>&page=<?= $page; ?>" class="text-xs font-semibold text-slate-500 hover:underline">Mark read</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="flex items-center justify-center gap-2 pt-2">
                    <?php if ($page > 1): ?>
                        <a href="notifications.php?page=<?= $page - 1; ?>" class="px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Prev</a>
                    <?php endif; ?>
                    <span class="text-sm text-slate-500">Page <?= $page; ?> of <?= $totalPages; ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="notifications.php?page=<?= $page + 1; ?>" class="px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>