<?php
// api/logs.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/Database.php';
session_start();

$db = (new Database())->getConnection();

// Parámetros GET con valores por defecto
$page   = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$size   = isset($_GET['size']) ? max(1, intval($_GET['size'])) : 99999999;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$offset = ($page - 1) * $size;

try {
    // Construcción del WHERE si hay búsqueda
    $whereSQL = '';
    $params   = [];
    if ($search !== '') {
        $whereSQL = "WHERE
            ul.`timestamp` LIKE :search
            OR u.username      LIKE :search
            OR ul.action       LIKE :search
        ";
        $params[':search'] = "%{$search}%";
    }

    // 1) total de registros (con filtro si aplica)
    $countSQL = "SELECT COUNT(*) AS total
                 FROM user_logs ul
                 LEFT JOIN users u ON ul.user_id = u.user_id
                 $whereSQL";
    $countStmt = $db->prepare($countSQL);
    foreach ($params as $k => $v) {
        $countStmt->bindValue($k, $v, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $lastPage = (int)ceil($total / $size);

    // 2) datos paginados + filtro
    $dataSQL = "
        SELECT
            ul.id,
            ul.user_id,
            u.username,
            ul.action,
            ul.`timestamp`
        FROM user_logs ul
        LEFT JOIN users u ON ul.user_id = u.user_id
        $whereSQL
        ORDER BY ul.`timestamp` DESC
        LIMIT :size OFFSET :offset
    ";
    $stmt = $db->prepare($dataSQL);
    // bindear filtro si existe
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
    $stmt->bindValue(':size',   $size,   PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Respuesta en formato Tabulator remoto
    echo json_encode([
        'data'      => $logs,
        'last_page' => $lastPage,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Excepción de base de datos',
        'message' => $e->getMessage(),
    ]);
}
