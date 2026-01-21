<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/ExchangeRateModel.php';
require_once __DIR__ . '/../controllers/Logger.php';

$model = new ExchangeRateModel();
$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user']['user_id'] ?? 0;

try {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? $_POST;

    switch ($action) {
        case 'list_currencies':
            echo json_encode(['success' => true, 'data' => $model->getCurrencies()]);
            break;

        case 'list_rates':
            // 1. Recibir parámetros GET para el filtro
            $start = $_GET['start'] ?? null;
            $end   = $_GET['end'] ?? null;
            // Pasamos los filtros al modelo
            echo json_encode(['success' => true, 'data' => $model->getRates(1000, $start, $end)]);
            break;

        case 'create_currency':
            $id = $model->createCurrency($input['currency_code'], $input['currency_name'], $input['country'] ?? '');
            Logger::logAction($user_id, "Creó moneda: " . strtoupper($input['currency_code']));
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'update_currency':
            $res = $model->updateCurrency((int)$input['currency_id'], $input['currency_code'], $input['currency_name']);
            Logger::logAction($user_id, "Actualizó moneda ID: " . $input['currency_id']);
            echo json_encode(['success' => $res]);
            break;

        case 'create_rate':
            $id = $model->createRate((int)$input['currency_id'], $input['rate'], $input['rate_date'], $input['notes'] ?? '');
            Logger::logAction($user_id, "Registró tasa para Moneda ID: {$input['currency_id']}");
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'update_rate':
            $res = $model->updateRate((int)$input['rate_id'], (float)$input['rate'], $input['rate_date'], $input['notes'] ?? '');
            Logger::logAction($user_id, "Actualizó registro de tasa ID: " . $input['rate_id']);
            echo json_encode(['success' => $res]);
            break;

        case 'delete_currency':
            // 2. Intentar eliminar. Si el modelo devuelve false, es porque tiene datos.
            if ($model->deleteCurrency($input['currency_id'])) {
                Logger::logAction($user_id, "Eliminó moneda ID: " . $input['currency_id']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar: tiene historial de tasas.']);
            }
            break;

        case 'delete_rate':
            $model->deleteRate($input['rate_id']);
            Logger::logAction($user_id, "Eliminó tasa ID: " . $input['rate_id']);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de operación', 'error' => $e->getMessage()]);
}