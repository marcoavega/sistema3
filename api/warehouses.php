<?php
// api/warehouses.php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../controllers/WarehouseController.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $input = $json;
        }
    }
}

try {
    $pdo = (new Database())->getConnection();
    $controller = new WarehouseController($pdo);

    switch ($action) {
        case 'list':
            echo json_encode(['success' => true, 'data' => $controller->list()]);
            break;

        case 'create':
            $id = $controller->create(trim($input['name'] ?? ''));
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'update':
            $controller->update((int)$input['id'], trim($input['name']));
            echo json_encode(['success' => true]);
            break;

        case 'delete':
            $controller->delete((int)$input['id']);
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Acción no válida');
    }
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
