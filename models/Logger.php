<?php
require_once __DIR__ . '/../config/config.php';

class Logger
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/Database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function log($userId, $action)
    {
        try {
            // Obtener nombre del usuario
            $stmtUser = $this->db->prepare("SELECT username FROM users WHERE user_id = :user_id");
            $stmtUser->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmtUser->execute();
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            $username = $user['username'] ?? "Usuario $userId";

            $stmt = $this->db->prepare("INSERT INTO user_logs (user_id, action) VALUES (:user_id, :action)");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':action', $action);
            $stmt->execute();

            error_log("🟡 Logger: user_id=$userId ($username), action=$action");
        } catch (PDOException $e) {
            error_log("Logger::log Error: " . $e->getMessage());
        }
    }
}
