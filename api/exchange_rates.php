<?php
// api/exchange_rates.php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/ExchangeRateModel.php';

$pdo = (new Database())->getConnection();
$model = new ExchangeRateModel($pdo);

$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

try {
    if ($action === 'list_currencies') {
        echo json_encode(['success' => true, 'data' => $model->getCurrencies()]);
        exit;
    }

    if ($action === 'list_rates') {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 500;
        echo json_encode(['success' => true, 'data' => $model->getRates($limit)]);
        exit;
    }

    // leer input (POST o JSON raw)
    $input = $_POST;
    if (empty($input)) {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) $input = $json;
        }
    }

    if ($action === 'create_currency') {
        $code = strtoupper(trim($input['currency_code'] ?? ''));
        $name = trim($input['currency_name'] ?? '');
        $country = trim($input['country'] ?? '');
        if ($code === '' || $name === '' || $country === '') {
            echo json_encode(['success' => false, 'message' => 'Faltan campos']);
            exit;
        }
        $id = $model->createCurrency($code, $name, $country);
        echo json_encode(['success' => true, 'id' => $id, 'currency' => $model->getCurrency($id)]);
        exit;
    }

    if ($action === 'create_rate') {
        $currency_id = (int)($input['currency_id'] ?? 0);
        $rate = $input['rate'] ?? null;
        $rate_date = $input['rate_date'] ?? null;
        $notes = $input['notes'] ?? null;

        if ($currency_id <= 0 || $rate === null || !$rate_date) {
            echo json_encode(['success' => false, 'message' => 'Faltan campos para tipo de cambio']);
            exit;
        }
        if (!is_numeric($rate)) {
            echo json_encode(['success' => false, 'message' => 'Tipo debe ser numérico']);
            exit;
        }

        $id = $model->createRate($currency_id, $rate, $rate_date, $notes);
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'delete_currency') {
        $id = (int)($input['currency_id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID inválido']); exit; }
        $ok = $model->deleteCurrency($id);
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }

    if ($action === 'delete_rate') {
        $id = (int)($input['rate_id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID inválido']); exit; }
        $ok = $model->deleteRate($id);
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no definida']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error BD', 'error' => $e->getMessage()]);
}
