<?php
// controllers/InventoryController.php
require_once __DIR__ . '/../models/Inventory.php';

class InventoryController
{
    private Inventory $model;
    private int $level;
    private ?int $userId;

    public function __construct(PDO $db)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user'])) {
            throw new Exception('Sesión no válida');
        }

        $this->model = new Inventory($db);
        $this->level = (int)($_SESSION['user']['level_user'] ?? $_SESSION['user']['level'] ?? 0);
        $this->userId = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'] ?? null;

        if (!$this->canView()) {
            throw new Exception('No autorizado');
        }
    }

    private function canView(): bool
    {
        // Solo ver: niveles 1,2,3,4
        return in_array($this->level, [1,2,3,4], true);
    }

    public function stats(): array
    {
        return [
            'total' => (int)$this->model->totalProducts(),
            'inStock' => (int)$this->model->inStockCount(),
            'lowStock' => (int)$this->model->lowStockCount(),
            'totalValue' => (float)$this->model->totalValue(),
        ];
    }
}
