<?php
// coordinator/entities.php
session_start();

require_once __DIR__ . '/../config/db.php';

// Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

$message = $_SESSION['entity_message'] ?? '';
$error   = $_SESSION['entity_error'] ?? '';
unset($_SESSION['entity_message'], $_SESSION['entity_error']);

// ==========================================
// 1. HANDLE DATABASE CRUD OPERATIONS (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // INSERT INTO DB
    if ($action === 'create') {
        $name        = trim($_POST['entity_name'] ?? '');
        $aliases     = trim($_POST['aliases'] ?? '');
        $category    = trim($_POST['category'] ?? 'Other');
        $activity    = $_POST['activity_type'] ?? 'Other';
        $it_related  = $_POST['it_related'] ?? 'yes';
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $_SESSION['entity_error'] = "Entity name is required.";
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO predefined_entities (entity_name, aliases, category, activity_type, it_related, description)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $aliases ?: null, $category ?: 'Other', $activity, $it_related, $description ?: null]);
                $_SESSION['entity_message'] = "Entity '{$name}' created in database.";
            } catch (PDOException $e) {
                $_SESSION['entity_error'] = "Database Error: " . (str_contains($e->getMessage(), 'Duplicate') ? 'Entity name already exists.' : $e->getMessage());
            }
        }
        header("Location: entities.php");
        exit();
    }

    // UPDATE IN DB
    if ($action === 'update') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['entity_name'] ?? '');
        $aliases     = trim($_POST['aliases'] ?? '');
        $category    = trim($_POST['category'] ?? 'Other');
        $activity    = $_POST['activity_type'] ?? 'Other';
        $it_related  = $_POST['it_related'] ?? 'yes';
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || $name === '') {
            $_SESSION['entity_error'] = "Invalid entity ID or name.";
        } else {
            try {
                $pdo->beginTransaction();

                // Capture the old name/aliases so renamed entries still cascade.
                $oldStmt = $pdo->prepare("SELECT entity_name, aliases FROM predefined_entities WHERE id = ?");
                $oldStmt->execute([$id]);
                $old = $oldStmt->fetch(PDO::FETCH_ASSOC) ?: [];

                $stmt = $pdo->prepare("
                    UPDATE predefined_entities 
                    SET entity_name = ?, aliases = ?, category = ?, activity_type = ?, it_related = ?, description = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $aliases ?: null, $category ?: 'Other', $activity, $it_related, $description ?: null, $id]);

                // Auto-sync every previously extracted entity matching this
                // dictionary entry (canonical name, old name, or any alias).
                $terms = [];
                foreach ([$name, $old['entity_name'] ?? '', $aliases, $old['aliases'] ?? ''] as $source) {
                    foreach (preg_split('/[|;,\n]+/', (string) $source) as $term) {
                        $term = strtolower(trim($term));
                        if ($term !== '') {
                            $terms[$term] = $term;
                        }
                    }
                }
                $terms = array_values($terms);

                $synced = 0;
                if (!empty($terms)) {
                    $placeholders = implode(',', array_fill(0, count($terms), '?'));
                    $syncStmt = $pdo->prepare("
                        UPDATE report_entities
                        SET category = ?, activity_type = ?, it_related = ?
                        WHERE LOWER(TRIM(entity_name)) IN ($placeholders)
                           OR LOWER(TRIM(canonical_name)) IN ($placeholders)
                    ");
                    $syncStmt->execute(array_merge(
                        [$category ?: 'Other', $activity, $it_related],
                        $terms,
                        $terms
                    ));
                    $synced = $syncStmt->rowCount();
                }

                $pdo->commit();

                $_SESSION['entity_message'] = "Entity '{$name}' updated in database."
                    . ($synced > 0 ? " {$synced} previously extracted record" . ($synced === 1 ? '' : 's') . " synced automatically." : "");
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $_SESSION['entity_error'] = "Database Error: " . $e->getMessage();
            }
        }
        header("Location: entities.php");
        exit();
    }

    // ARCHIVE (soft delete)
    if ($action === 'archive') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE predefined_entities SET is_archived = 1 WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['entity_message'] = "Entity archived. Restore or permanently delete it from the Archive tab.";
            } catch (PDOException $e) {
                $_SESSION['entity_error'] = "Database Error: " . $e->getMessage();
            }
        }
        header("Location: entities.php");
        exit();
    }

    // RESTORE FROM ARCHIVE
    if ($action === 'restore') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE predefined_entities SET is_archived = 0 WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['entity_message'] = "Entity restored from archive.";
            } catch (PDOException $e) {
                $_SESSION['entity_error'] = "Database Error: " . $e->getMessage();
            }
        }
        header("Location: entities.php?view=archived");
        exit();
    }

    // PERMANENT DELETE (from archive only)
    if ($action === 'permanent_delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM predefined_entities WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['entity_message'] = "Entity permanently deleted from the archive.";
            } catch (PDOException $e) {
                $_SESSION['entity_error'] = "Database Error: " . $e->getMessage();
            }
        }
        header("Location: entities.php?view=archived");
        exit();
    }
}

// ==========================================
// 2. FETCH REAL DATA DIRECTLY FROM MYSQL
// ==========================================
$view           = ($_GET['view'] ?? 'active') === 'archived' ? 'archived' : 'active';
$archivedFlag   = $view === 'archived' ? 1 : 0;
$search         = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? 'All');
$typeFilter     = trim($_GET['activity_type'] ?? 'All');

$sql = "SELECT id, entity_name, aliases, category, activity_type, it_related, description, created_at, updated_at 
        FROM predefined_entities 
        WHERE is_archived = :archived";
$params = ['archived' => $archivedFlag];

if ($search !== '') {
    $sql .= " AND (entity_name LIKE :s OR aliases LIKE :s OR description LIKE :s)";
    $params['s'] = "%{$search}%";
}

if ($categoryFilter !== 'All' && $categoryFilter !== '') {
    $sql .= " AND category = :cat";
    $params['cat'] = $categoryFilter;
}

if ($typeFilter !== 'All' && $typeFilter !== '') {
    $sql .= " AND activity_type = :type";
    $params['type'] = $typeFilter;
}

$sql .= " ORDER BY entity_name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$entities = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Active/archived counters for the view switch.
$countStmt = $pdo->query("SELECT is_archived, COUNT(*) AS total FROM predefined_entities GROUP BY is_archived");
$activeCount = 0;
$archivedCount = 0;
foreach ($countStmt->fetchAll(PDO::FETCH_ASSOC) as $countRow) {
    if ((int) $countRow['is_archived'] === 1) {
        $archivedCount = (int) $countRow['total'];
    } else {
        $activeCount = (int) $countRow['total'];
    }
}

// Fetch distinct categories dynamically for the current view.
$catStmt = $pdo->prepare("SELECT DISTINCT category FROM predefined_entities WHERE is_archived = ? AND category IS NOT NULL AND category != '' ORDER BY category ASC");
$catStmt->execute([$archivedFlag]);
$allCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

require_once __DIR__ . '/../src/pages/coordinator/entitiesPage.php';