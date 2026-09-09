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
                $stmt = $pdo->prepare("
                    UPDATE predefined_entities 
                    SET entity_name = ?, aliases = ?, category = ?, activity_type = ?, it_related = ?, description = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $aliases ?: null, $category ?: 'Other', $activity, $it_related, $description ?: null, $id]);
                $_SESSION['entity_message'] = "Entity '{$name}' updated in database.";
            } catch (PDOException $e) {
                $_SESSION['entity_error'] = "Database Error: " . $e->getMessage();
            }
        }
        header("Location: entities.php");
        exit();
    }

    // DELETE FROM DB
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM predefined_entities WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['entity_message'] = "Entity deleted from database.";
            } catch (PDOException $e) {
                $_SESSION['entity_error'] = "Database Error: " . $e->getMessage();
            }
        }
        header("Location: entities.php");
        exit();
    }
}

// ==========================================
// 2. FETCH REAL DATA DIRECTLY FROM MYSQL
// ==========================================
$search         = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? 'All');
$typeFilter     = trim($_GET['activity_type'] ?? 'All');

$sql = "SELECT id, entity_name, aliases, category, activity_type, it_related, description, created_at, updated_at 
        FROM predefined_entities 
        WHERE 1=1";
$params = [];

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

// Fetch distinct categories dynamically from database rows
$catStmt = $pdo->query("SELECT DISTINCT category FROM predefined_entities WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$allCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

require_once __DIR__ . '/../src/pages/coordinator/entitiesPage.php';