<?php
// controllers/Logger.php

// Incluye el archivo de conexión a la base de datos
require_once __DIR__ . '/../models/Database.php';

// Declaración de la clase Logger
class Logger
{
    // Método estático para registrar una acción en la tabla user_logs
    public static function logAction(int $user_id, string $action): void
    {
        try {
            // Crea una nueva instancia de Database y obtiene la conexión PDO
            $db = (new Database())->getConnection();

            // Prepara la consulta SQL para insertar el registro en la tabla de logs
            $stmt = $db->prepare("INSERT INTO user_logs (user_id, action) VALUES (:user_id, :action)");

            // Ejecuta la consulta pasando los valores del usuario y la acción
            $stmt->execute([
                ':user_id' => $user_id,   // ID del usuario que realizó la acción
                ':action'  => $action     // Descripción de la acción realizada
            ]);

        } catch (\PDOException $e) {
            // Si ocurre un error con la base de datos, se registra en el log del servidor
            error_log("Logger error: " . $e->getMessage());
        }
    }
}
