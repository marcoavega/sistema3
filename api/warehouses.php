<?php
// api/warehouses.php
header('Content-Type: application/json; charset=utf-8');

// sesión
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../models/Database.php';
$pdo = (new Database())->getConnection();

// determinar acción (soporta GET/POST y body JSON)
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');


// helper: obtener input (POST o raw JSON)
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) $input = $json;
    }
}

// helper: registrar log directamente en la tabla user_logs
function register_log(PDO $pdo, $userId, $actionText) {
    try {
        // Ajusta los nombres de columnas si tu tabla es distinta
        $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action, `timestamp`) VALUES (:uid, :act, NOW())");
        $stmt->execute([':uid' => $userId ?? 0, ':act' => $actionText]);
    } catch (Throwable $t) {
        // no interrumpir el flujo por un fallo en logging — solo lo dejamos en error_log PHP
        error_log("register_log error: " . $t->getMessage());
    }
}

// usuario que realiza la acción (suponiendo que en session tengas user_id)
$user_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'] ?? null;

try {
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT warehouse_id AS id, name, created_at, updated_at FROM warehouses ORDER BY warehouse_id ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }

    if ($action === 'create') {
        $name = trim($input['name'] ?? '');
        if ($name === '') {
            echo json_encode(['success' => false, 'message' => 'Nombre obligatorio']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO warehouses (name, created_at) VALUES (:name, NOW())");
        $stmt->execute([':name' => $name]);
        $id = $pdo->lastInsertId();

        // registrar log
        register_log($pdo, $user_id, "Creó almacén Número {$id}: {$name}");

        $stmt = $pdo->prepare("SELECT warehouse_id AS id, name, created_at, updated_at FROM warehouses WHERE warehouse_id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'warehouse' => $row]);
        exit;
    }

    if ($action === 'update') {
        $id = (int)($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        if ($id <= 0 || $name === '') {
            echo json_encode(['success' => false, 'message' => 'ID y nombre son obligatorios']);
            exit;
        }

        // obtener nombre anterior (para log)
        $q = $pdo->prepare("SELECT name FROM warehouses WHERE warehouse_id = :id LIMIT 1");
        $q->execute([':id' => $id]);
        $oldName = $q->fetchColumn();

        $stmt = $pdo->prepare("UPDATE warehouses SET name = :name, updated_at = NOW() WHERE warehouse_id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);

        // registrar log
        register_log($pdo, $user_id, "Actualizó almacén id {$id}: '{$oldName}' → '{$name}'");

        $stmt = $pdo->prepare("SELECT warehouse_id AS id, name, created_at, updated_at FROM warehouses WHERE warehouse_id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'warehouse' => $row]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            exit;
        }

        // obtener nombre antes de borrar (para log)
        $q = $pdo->prepare("SELECT name FROM warehouses WHERE warehouse_id = :id LIMIT 1");
        $q->execute([':id' => $id]);
        $oldName = $q->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM warehouses WHERE warehouse_id = :id");
        $stmt->execute(['id' => $id]);

        // registrar log
        register_log($pdo, $user_id, "Eliminó almacén id {$id}: '{$oldName}'");

        echo json_encode(['success' => true, 'deleted_id' => $id]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida: ' . htmlspecialchars($action)]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de BD', 'error' => $e->getMessage()]);
}
