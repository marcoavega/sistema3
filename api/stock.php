<?php
// api/stock.php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../models/Database.php';

try {
    $pdo = (new Database())->getConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error DB: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';

function json_resp($success, $msg, $extra = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $msg], $extra));
    exit;
}

function getSessionUser($pdo) {
    $userId = 0;
    $username = null;
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
        $u = $_SESSION['user'];
        foreach (['id','user_id','userid','uid','ID','userID'] as $k) {
            if (isset($u[$k]) && intval($u[$k]) > 0) {
                $userId = intval($u[$k]);
                break;
            }
        }
        foreach (['username','user','name','login','email'] as $k) {
            if (isset($u[$k]) && $u[$k] !== '') {
                $username = $u[$k];
                break;
            }
        }
    }

    if ($userId === 0 && $username) {
        $candidates = [
            "SELECT id, username FROM users WHERE username = ? LIMIT 1",
            "SELECT user_id, username FROM users WHERE username = ? LIMIT 1",
            "SELECT id, username FROM users WHERE email = ? LIMIT 1",
            "SELECT user_id, username FROM users WHERE email = ? LIMIT 1"
        ];
        foreach ($candidates as $q) {
            try {
                $st = $pdo->prepare($q);
                $st->execute([$username]);
                $r = $st->fetch(PDO::FETCH_NUM);
                if ($r) {
                    $possibleId = intval($r[0]);
                    if ($possibleId > 0) {
                        $userId = $possibleId;
                        if (isset($r[1]) && $r[1] !== '') $username = $r[1];
                        break;
                    }
                }
            } catch (Exception $e) { continue; }
        }
    }

    if (!$username && !empty($_SESSION['user']) && is_array($_SESSION['user'])) {
        foreach ($_SESSION['user'] as $k => $v) {
            if (is_string($v) && strlen($v) <= 60 && preg_match('/[a-zA-Z0-9\._@\-]/', $v)) {
                $username = $v;
                break;
            }
        }
    }

    return [(int)$userId, $username];
}

function writeLogFlexible($pdo, $userId, $username, $message) {
    $candidates = ['logs','activity_logs','activity','user_logs','log','system_logs','log_entries'];
    foreach ($candidates as $tbl) {
        try {
            $exists = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($tbl))->fetchColumn();
            if (!$exists) continue;
            $cols = $pdo->query("SHOW COLUMNS FROM `{$tbl}`")->fetchAll(PDO::FETCH_COLUMN, 0);
            if (!$cols || count($cols) === 0) continue;

            $insertCols = [];
            $params = [];

            if (in_array('user_id', $cols)) {
                $insertCols[] = 'user_id';
                $params[] = $userId;
            } elseif (in_array('user', $cols) || in_array('username', $cols)) {
                $field = in_array('username', $cols) ? 'username' : 'user';
                $insertCols[] = $field;
                $params[] = $username ?? '';
            } elseif (in_array('actor', $cols)) {
                $insertCols[] = 'actor';
                $params[] = $username ?? '';
            }

            if (in_array('action', $cols)) {
                $insertCols[] = 'action';
                $params[] = $message;
            } elseif (in_array('message', $cols)) {
                $insertCols[] = 'message';
                $params[] = $message;
            } elseif (in_array('description', $cols)) {
                $insertCols[] = 'description';
                $params[] = $message;
            } elseif (in_array('msg', $cols)) {
                $insertCols[] = 'msg';
                $params[] = $message;
            }

            if (in_array('created_at', $cols)) {
                $insertCols[] = 'created_at';
                $params[] = date('Y-m-d H:i:s');
            } elseif (in_array('created', $cols)) {
                $insertCols[] = 'created';
                $params[] = date('Y-m-d H:i:s');
            } elseif (in_array('timestamp', $cols)) {
                $insertCols[] = 'timestamp';
                $params[] = date('Y-m-d H:i:s');
            }

            if (count($insertCols) === 0) continue;

            $placeholders = implode(',', array_fill(0, count($insertCols), '?'));
            $colsString = implode(',', array_map(function($c){ return "`$c`"; }, $insertCols));
            $st = $pdo->prepare("INSERT INTO `{$tbl}` ({$colsString}) VALUES ({$placeholders})");
            $st->execute($params);
            return true;

        } catch (Exception $e) {
            continue;
        }
    }
    return false;
}

function getCurrentStock($pdo, $pid, $wid) {
    $st = $pdo->prepare("SELECT stock FROM warehouse_stock WHERE product_id = ? AND warehouse_id = ? LIMIT 1");
    $st->execute([$pid, $wid]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if ($r && isset($r['stock'])) return intval($r['stock']);
    return 0;
}

function updateStock($pdo, $pid, $wid, $new) {
    $st = $pdo->prepare("
        INSERT INTO warehouse_stock (product_id, warehouse_id, stock)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE stock = VALUES(stock)
    ");
    $st->execute([$pid, $wid, $new]);
}

function getProductName($pdo, $pid) {
    try {
        $st = $pdo->prepare("SELECT product_name FROM products WHERE product_id = ? LIMIT 1");
        $st->execute([$pid]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if ($r && !empty($r['product_name'])) return $r['product_name'];
    } catch (Exception $e) { }
    return "Producto #{$pid}";
}

function getWarehouseName($pdo, $wid) {
    try {
        $st = $pdo->prepare("SELECT name FROM warehouses WHERE warehouse_id = ? LIMIT 1");
        $st->execute([$wid]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if ($r && !empty($r['name'])) return $r['name'];
    } catch (Exception $e) { }
    return "Almacén #{$wid}";
}

function logMovement($pdo, $pid, $wid, $qty, $prev, $new, $userId, $reason = null) {
    try {
        $st = $pdo->prepare("
            INSERT INTO stock_movements (product_id, warehouse_id, qty_change, prev_stock, new_stock, user_id, reason, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $st->execute([$pid, $wid, $qty, $prev, $new, $userId, $reason, date('Y-m-d H:i:s')]);
        return true;
    } catch (Exception $e) {
        try {
            $st2 = $pdo->prepare("
                INSERT INTO stock_movements (product_id, warehouse_id, qty_change, prev_stock, new_stock, user_id, reason)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $st2->execute([$pid, $wid, $qty, $prev, $new, $userId, $reason]);
            return true;
        } catch (Exception $e2) {
            try {
                $st3 = $pdo->prepare("
                    INSERT INTO stock_movements (product_id, warehouse_id, qty_change, prev_stock, new_stock, user_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $st3->execute([$pid, $wid, $qty, $prev, $new, $userId]);
                return true;
            } catch (Exception $e3) {
                error_log("Error al insertar stock_movements: " . $e3->getMessage());
                return false;
            }
        }
    }
}

list($currentUserId, $currentUsername) = getSessionUser($pdo);
if ($currentUserId === 0 && !$currentUsername) {
    $currentUserId = 0;
}

switch ($action) {

    case "entry":
        $pid = $_POST['product_id'] ?? null;
        $wid = $_POST['warehouse_id'] ?? null;
        $qty = intval($_POST['quantity'] ?? 0);
        $notes = trim($_POST['notes'] ?? '') ?: null;

        if (!$pid || !$wid) json_resp(false, "Falta product_id o warehouse_id");
        if ($qty <= 0) json_resp(false, "Cantidad inválida");

        $prev = getCurrentStock($pdo, $pid, $wid);
        $new = $prev + $qty;

        try {
            $pdo->beginTransaction();
            updateStock($pdo, $pid, $wid, $new);
            logMovement($pdo, $pid, $wid, $qty, $prev, $new, $currentUserId, $notes);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            json_resp(false, "Error al registrar entrada: " . $e->getMessage());
        }

        $productName = getProductName($pdo, $pid);
        $warehouseName = getWarehouseName($pdo, $wid);
        $msg = "Entrada +{$qty} {$productName} en {$warehouseName}";
        if ($notes) $msg .= " — Nota: {$notes}";

        writeLogFlexible($pdo, $currentUserId, $currentUsername, $msg);

        json_resp(true, "Entrada registrada", ['updated_stocks' => [['warehouse_id' => intval($wid), 'stock' => $new]]]);
        break;

    case "exit":
        $pid = $_POST['product_id'] ?? null;
        $wid = $_POST['warehouse_id'] ?? null;
        $qty = intval($_POST['quantity'] ?? 0);
        $notes = trim($_POST['notes'] ?? '') ?: null;

        if (!$pid || !$wid) json_resp(false, "Falta product_id o warehouse_id");
        if ($qty <= 0) json_resp(false, "Cantidad inválida");

        $prev = getCurrentStock($pdo, $pid, $wid);
        if ($prev < $qty) json_resp(false, "Stock insuficiente en el almacén seleccionado");

        $new = $prev - $qty;

        try {
            $pdo->beginTransaction();
            updateStock($pdo, $pid, $wid, $new);
            logMovement($pdo, $pid, $wid, -$qty, $prev, $new, $currentUserId, $notes);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            json_resp(false, "Error al registrar salida: " . $e->getMessage());
        }

        $productName = getProductName($pdo, $pid);
        $warehouseName = getWarehouseName($pdo, $wid);
        $msg = "Salida -{$qty} {$productName} en {$warehouseName}";
        if ($notes) $msg .= " — Nota: {$notes}";

        writeLogFlexible($pdo, $currentUserId, $currentUsername, $msg);

        json_resp(true, "Salida registrada", ['updated_stocks' => [['warehouse_id' => intval($wid), 'stock' => $new]]]);
        break;

    case "transfer":
        $pid = $_POST['product_id'] ?? null;
        $from = $_POST['from_warehouse_id'] ?? null;
        $to = $_POST['to_warehouse_id'] ?? null;
        $qty = intval($_POST['quantity'] ?? 0);
        $notes = trim($_POST['notes'] ?? '') ?: null;

        if (!$pid || !$from || !$to) json_resp(false, "Faltan datos (product_id / from / to)");
        if ($from == $to) json_resp(false, "Origen y destino deben ser distintos");
        if ($qty <= 0) json_resp(false, "Cantidad inválida");

        $prevFrom = getCurrentStock($pdo, $pid, $from);
        if ($prevFrom < $qty) json_resp(false, "Stock insuficiente en el almacén origen");

        $prevTo = getCurrentStock($pdo, $pid, $to);

        $newFrom = $prevFrom - $qty;
        $newTo   = $prevTo + $qty;

        try {
            $pdo->beginTransaction();
            updateStock($pdo, $pid, $from, $newFrom);
            updateStock($pdo, $pid, $to, $newTo);

            logMovement($pdo, $pid, $from, -$qty, $prevFrom, $newFrom, $currentUserId, $notes);
            logMovement($pdo, $pid, $to, $qty, $prevTo, $newTo, $currentUserId, $notes);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            json_resp(false, "Error al realizar transferencia: " . $e->getMessage());
        }

        $productName = getProductName($pdo, $pid);
        $fromName = getWarehouseName($pdo, $from);
        $toName = getWarehouseName($pdo, $to);
        $msg = "Transferencia {$productName} qty={$qty} de {$fromName} a {$toName}";
        if ($notes) $msg .= " — Nota: {$notes}";

        writeLogFlexible($pdo, $currentUserId, $currentUsername, $msg);

        json_resp(true, "Transferencia realizada", ['updated_stocks' => [
            ['warehouse_id' => intval($from), 'stock' => $newFrom],
            ['warehouse_id' => intval($to), 'stock' => $newTo]
        ]]);
        break;

    default:
        json_resp(false, "Acción inválida");
}
