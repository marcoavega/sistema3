<?php
// Archivo: api/warehouses.php
header('Content-Type: application/json; charset=utf-8');

// Inicializar sesión (si tu sistema lo requiere)
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../models/Database.php';
$pdo = (new Database())->getConnection();

// Restricción de acceso: opcional (mantener si quieres que solo admin=1 maneje)
// if (!isset($_SESSION['user']) || $_SESSION['user']['level_user'] != 1) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
//     exit;
// }

$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

try {
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT warehouse_id AS id, name, created_at, updated_at FROM warehouses ORDER BY warehouse_id ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }

    // recibir datos (POST). soporte x-www-form-urlencoded o JSON.
    $input = $_POST;
    // si JSON raw
    if (empty($input)) {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) $input = $json;
        }
    }

    if ($action === 'create') {
        $name = trim($input['name'] ?? '');
        if ($name === '') {
            echo json_encode(['success' => false, 'message' => 'Nombre obligatorio']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO warehouses (name, created_at) VALUES (:name, NOW())");
        $stmt->execute(['name' => $name]);
        $id = $pdo->lastInsertId();
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
        $stmt = $pdo->prepare("UPDATE warehouses SET name = :name, updated_at = NOW() WHERE warehouse_id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);
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
        $stmt = $pdo->prepare("DELETE FROM warehouses WHERE warehouse_id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['success' => true, 'deleted_id' => $id]);
        exit;
    }

    // acción no reconocida
    echo json_encode(['success' => false, 'message' => 'Acción no válida: ' . htmlspecialchars($action)]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de BD', 'error' => $e->getMessage()]);
}
