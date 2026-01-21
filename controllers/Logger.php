<?php
require_once __DIR__ . '/../models/Database.php';

class Logger {
    public static function logAction(int $user_id, string $action): void {
        try {
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("INSERT INTO user_logs (user_id, action, `timestamp`) VALUES (?, ?, NOW())");
            $stmt->execute([$user_id, $action]);
        } catch (Exception $e) {
            error_log("Error en Logger: " . $e->getMessage());
        }
    }
}