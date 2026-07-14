<?php
// api/users.php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/Database.php';
session_start();

// Validar que el usuario sea administrador o tenga permisos
if (!isset($_SESSION['user']) || empty($_SESSION['user']['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$db = (new Database())->getConnection();
$action = $_GET['action'] ?? '';
$currentUserId = $_SESSION['user']['user_id']; // El ID del administrador que hace la acción
$currentUserName = $_SESSION['user']['username']; // Nombre del admin (opcional para logs complejos)

// --- FUNCIÓN AUXILIAR PARA GUARDAR LOGS ---
function registrarLog($db, $userId, $accion) {
    try {
        // Asegúrate de que tu tabla se llame 'user_logs' y tenga las columnas 'user_id', 'action', 'timestamp'
        $sql = "INSERT INTO user_logs (user_id, action, timestamp) VALUES (:user_id, :action, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':action'  => $accion
        ]);
    } catch (Exception $e) {
        // Silenciamos error del log para no detener la operación principal, 
        // pero podrías hacer error_log($e->getMessage()) si quisieras depurar.
    }
}

try {
    // Leer el cuerpo JSON de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        
        case 'get':
            // Obtener lista de usuarios para la tabla
            $stmt = $db->prepare("SELECT user_id, username, email, level_user, created_at FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Mapeo simple de niveles para mostrar texto en lugar de números
            foreach ($users as &$user) {
                $user['description_level'] = ($user['level_user'] == 1) ? 'Administrador' : 'Usuario';
            }
            echo json_encode($users);
            break;

        case 'create':
            // Crear usuario
            if (!isset($input['userData'])) throw new Exception("Datos incompletos");
            
            $data = $input['userData'];
            
            // Validar duplicados
            $check = $db->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
            $check->execute([$data['email'], $data['username']]);
            if ($check->rowCount() > 0) throw new Exception("El usuario o correo ya existen");

            $sql = "INSERT INTO users (username, email, password, level_user, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $db->prepare($sql);
            // Recuerda hashear la contraseña en producción: password_hash($data['password'], PASSWORD_DEFAULT)
            $stmt->execute([$data['username'], $data['email'], $data['password'], $data['level_user']]);
            
            $newId = $db->lastInsertId();

            // --- AQUÍ ESTÁ LA MAGIA: REGISTRAR EL LOG ---
            registrarLog($db, $currentUserId, "Registró nuevo usuario: " . $data['username']);

            echo json_encode([
                'success' => true, 
                'newUser' => [
                    'user_id' => $newId,
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'level_user' => $data['level_user'],
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
            break;

        case 'update':
            // Editar usuario
            if (!isset($input['userData'])) throw new Exception("Datos incompletos");
            $data = $input['userData'];

            $sql = "UPDATE users SET username = ?, email = ?, level_user = ? WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$data['username'], $data['email'], $data['level_user'], $data['user_id']]);

            // --- REGISTRAR LOG DE EDICIÓN ---
            registrarLog($db, $currentUserId, "Actualizó datos del usuario: " . $data['username']);

            echo json_encode(['success' => true]);
            break;

        case 'delete':
            // Eliminar usuario
            if (!isset($input['user_id'])) throw new Exception("ID no proporcionado");
            $targetId = $input['user_id'];

            // Opcional: Obtener el nombre del usuario antes de borrarlo para que el log se vea bonito
            $stmtName = $db->prepare("SELECT username FROM users WHERE user_id = ?");
            $stmtName->execute([$targetId]);
            $targetUser = $stmtName->fetch(PDO::FETCH_ASSOC);
            $targetName = $targetUser ? $targetUser['username'] : "ID $targetId";

            $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$targetId]);

            // --- REGISTRAR LOG DE ELIMINACIÓN ---
            registrarLog($db, $currentUserId, "Eliminó al usuario: " . $targetName);

            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception("Acción no válida");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}