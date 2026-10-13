<?php
// coordinator/entities.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

// Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'coordinator') {
    header("Location: ../auth/login.php");
    exit();
}

// CSRF Guard: covers the whole POST surface of this controller.
// This page renders its error from entity_error, not flash_error.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify(__DIR__ . '/entities.php', 'entity_error');
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
        $name       = trim($_POST['entity_name'] ?? '');
        $aliases    = trim($_POST['aliases'] ?? '');
        $it_related = ($_POST['it_related'] ?? 'yes') === 'no' ? 'no' : 'yes';

        // The form now offers only IT or Clerical. activity_type still drives
        // the IT-vs-clerical reporting on report_entities, so derive it:
        // Clerical keeps its own value, IT is recorded as 'Other'.
        $activity = ($it_related === 'no') ? 'Clerical' : 'Other';

        if ($name === '') {
            $_SESSION['entity_error'] = "Entity name is required.";
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO predefined_entities (entity_name, aliases, category, activity_type, it_related)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $aliases ?: null, 'Other', $activity, $it_related]);
                $_SESSION['entity_message'] = "Entity '{$name}' created in database.";
            } catch (PDOException $e) {
                $_SESSION['entity_error'] = str_contains($e->getMessage(), 'Duplicate')
                            ? 'Entity name already exists.'
                            : 'Database Error: ' . userFacingError($e, 'entities.php:create');
            }
        }
        header("Location: entities.php");
        exit();
    }

    // UPDATE IN DB
    if ($action === 'update') {
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['entity_name'] ?? '');
        $aliases    = trim($_POST['aliases'] ?? '');
        $it_related = ($_POST['it_related'] ?? 'yes') === 'no' ? 'no' : 'yes';

        if ($id <= 0 || $name === '') {
            $_SESSION['entity_error'] = "Invalid entity ID or name.";
        } else {
            try {
                $pdo->beginTransaction();

                // Capture the old name/aliases so renamed entries still cascade.
                $oldStmt = $pdo->prepare("SELECT entity_name, aliases FROM predefined_entities WHERE id = ?");
                $oldStmt->execute([$id]);
                $old = $oldStmt->fetch(PDO::FETCH_ASSOC) ?: [];

                // category, activity_type and description are no longer posted by
                // the form, so they are left untouched. Only a switch to Clerical
                // is reflected in activity_type, so the IT-vs-clerical reporting
                // on report_entities stays correct; existing Software/Hardware
                // classifications survive an unrelated edit.
                $stmt = $pdo->prepare("
                    UPDATE predefined_entities
                    SET entity_name = ?, aliases = ?, it_related = ?,
                        activity_type = CASE WHEN ? = 'no' THEN 'Clerical' ELSE activity_type END
                    WHERE id = ?
                ");
                $stmt->execute([$name, $aliases ?: null, $it_related, $it_related, $id]);

                // Auto-sync the IT/Clerical flag onto previously extracted
                // records matching this dictionary entry (canonical name, old
                // name, or any alias), so a rename or alias fix still
                // propagates.
                //
                // Classification (category, activity_type) is deliberately NOT
                // written here: report_entities is historical record and the
                // coordinator corrects it per report. This block previously
                // bound an undefined variable, so activity_type was overwritten
                // with the empty string and category with the literal 'Other'
                // on every matching extracted row across all students.
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
                        SET it_related = ?
                        WHERE LOWER(TRIM(entity_name)) IN ($placeholders)
                           OR LOWER(TRIM(canonical_name)) IN ($placeholders)
                    ");
                    $syncStmt->execute(array_merge(
                        [$it_related],
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
                $_SESSION['entity_error'] = "Database Error: " . userFacingError($e, 'entities.php:update');
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
                $_SESSION['entity_error'] = "Database Error: " . userFacingError($e, 'entities.php:archive');
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
                $_SESSION['entity_error'] = "Database Error: " . userFacingError($e, 'entities.php:restore');
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
                $_SESSION['entity_error'] = "Database Error: " . userFacingError($e, 'entities.php:permanent_delete');
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
$typeFilter     = trim($_GET['it_related'] ?? 'All');

$sql = "SELECT id, entity_name, aliases, category, activity_type, it_related, description, created_at, updated_at 
        FROM predefined_entities 
        WHERE is_archived = :archived";
$params = ['archived' => $archivedFlag];

if ($search !== '') {
    // Native prepares (EMULATE_PREPARES is off) cannot reuse a named
    // placeholder, so each column needs its own parameter name.
    $sql .= " AND (entity_name LIKE :s_name OR aliases LIKE :s_alias OR description LIKE :s_desc)";
    $params['s_name']  = "%{$search}%";
    $params['s_alias'] = "%{$search}%";
    $params['s_desc']  = "%{$search}%";
}

if ($categoryFilter !== 'All' && $categoryFilter !== '') {
    $sql .= " AND category = :cat";
    $params['cat'] = $categoryFilter;
}

if ($typeFilter === 'yes' || $typeFilter === 'no') {
    $sql .= " AND it_related = :type";
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

// The distinct-category query that fed the old Category filter was removed with
// it. $categoryFilter below is still honoured so bookmarked ?category= URLs keep
// working; there is simply no UI left to set it.

require_once __DIR__ . '/../src/pages/coordinator/entitiesPage.php';