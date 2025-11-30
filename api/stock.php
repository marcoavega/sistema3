<?php
// api/stock.php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../models/Database.php';

$pdo = (new Database())->getConnection();
$action = $_GET['action'] ?? '';

function json($success, $msg, $extra = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $msg], $extra));
    exit;
}

function getCurrentStock($pdo, $pid, $wid) {
    $st = $pdo->prepare("SELECT stock FROM warehouse_stock WHERE product_id=? AND warehouse_id=?");
    $st->execute([$pid, $wid]);
    return $st->fetchColumn() ?: 0;
}

function updateStock($pdo, $pid, $wid, $new) {
    $st = $pdo->prepare("
        INSERT INTO warehouse_stock (product_id, warehouse_id, stock)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE stock = VALUES(stock)
    ");
    $st->execute([$pid, $wid, $new]);
}

function logMovement($pdo, $pid, $wid, $qty, $prev, $new, $reason) {
    $user = $_SESSION['user']['id'] ?? 0;

    $st = $pdo->prepare("
        INSERT INTO stock_movements (product_id, warehouse_id, qty_change, prev_stock, new_stock, user_id, reason)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $st->execute([$pid, $wid, $qty, $prev, $new, $user, $reason]);
}

switch ($action) {

    // ----------------------
    // ENTRADA
    // ----------------------
    case "entry":
        $pid = $_POST['product_id'];
        $wid = $_POST['warehouse_id'];
        $qty = intval($_POST['quantity']);
        $reason = $_POST['notes'] ?? 'Entrada';

        if ($qty <= 0) json(false, "Cantidad no válida");

        $prev = getCurrentStock($pdo, $pid, $wid);
        $new = $prev + $qty;

        updateStock($pdo, $pid, $wid, $new);
        logMovement($pdo, $pid, $wid, $qty, $prev, $new, $reason);

        json(true, "Entrada registrada");
        break;


    // ----------------------
    // SALIDA
    // ----------------------
    case "exit":
        $pid = $_POST['product_id'];
        $wid = $_POST['warehouse_id'];
        $qty = intval($_POST['quantity']);
        $reason = $_POST['notes'] ?? 'Salida';

        if ($qty <= 0) json(false, "Cantidad no válida");

        $prev = getCurrentStock($pdo, $pid, $wid);
        if ($prev < $qty) json(false, "Stock insuficiente");

        $new = $prev - $qty;

        updateStock($pdo, $pid, $wid, $new);
        logMovement($pdo, $pid, $wid, -$qty, $prev, $new, $reason);

        json(true, "Salida registrada");
        break;


    // ----------------------
    // TRANSFERENCIA
    // ----------------------
    case "transfer":

        $pid  = $_POST['product_id'];
        $from = $_POST['from_warehouse_id'];
        $to   = $_POST['to_warehouse_id'];
        $qty  = intval($_POST['quantity']);
        $notes = $_POST['notes'] ?? 'Transferencia';

        if ($from == $to) json(false, "El almacén origen y destino deben ser distintos");
        if ($qty <= 0) json(false, "Cantidad no válida");

        // stock origen
        $prevFrom = getCurrentStock($pdo, $pid, $from);
        if ($prevFrom < $qty) json(false, "Stock insuficiente en el almacén origen");

        $newFrom = $prevFrom - $qty;

        // stock destino
        $prevTo = getCurrentStock($pdo, $pid, $to);
        $newTo = $prevTo + $qty;

        // actualizar ambos
        updateStock($pdo, $pid, $from, $newFrom);
        updateStock($pdo, $pid, $to, $newTo);

        // registrar movimiento doble
        logMovement($pdo, $pid, $from, -$qty, $prevFrom, $newFrom, "Transferencia a almacén $to. $notes");
        logMovement($pdo, $pid, $to,  $qty,  $prevTo,  $newTo,  "Transferencia desde almacén $from. $notes");

        json(true, "Transferencia realizada");
        break;

    default:
        json(false, "Acción inválida");
}
