<?php
// models/Logger.php
require_once __DIR__ . '/Database.php';

class Logger
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Inserta una entrada en la tabla user_logs
     * @param int $userId
     * @param string $action Texto descriptivo de la acción (ej: "login", "Actualizó el producto: X")
     * @return bool true si se insertó, false si hubo error
     */
    public function log(int $userId, string $action): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO user_logs (user_id, action, `timestamp`) VALUES (:user_id, :action, NOW())"
            );
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':action', $action, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Para debug local puedes descomentar: error_log($e->getMessage());
            error_log("Logger::log Error: " . $e->getMessage());
            return false;
        }
    }
}
