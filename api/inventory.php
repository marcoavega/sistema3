<?php
// api/inventory.php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../controllers/InventoryController.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'stats';

$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) $input = $json;
    }
}

try {
    $pdo = (new Database())->getConnection();
    $controller = new InventoryController($pdo);

    switch ($action) {
        case 'stats':
            $stats = $controller->stats();
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
        default:
            throw new Exception('Acción no válida');
    }
} catch (Throwable $e) {
    // Si es "No autorizado" o "Sesión no válida" devolvemos 403 para que el cliente lo detecte
    $msg = $e->getMessage();
    if (stripos($msg, 'No autorizado') !== false || stripos($msg, 'Sesión no válida') !== false) {
        http_response_code(403);
    } else {
        http_response_code(200);
    }
    echo json_encode(['success' => false, 'message' => $msg]);
}
