<?php
/*
// controllers/api/update_product.php
require_once __DIR__ . '/../ProductController.php';

header('Content-Type: application/json; charset=utf-8');

// Leer datos JSON del cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos o faltan campos.']);
    exit;
}

try {
    $controller = new ProductController();
    $result = $controller->updateProduct((int)$input['id'], $input);
    echo json_encode($result);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
