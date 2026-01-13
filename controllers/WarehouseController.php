<?php
//controllers/WarehouseController.php
require_once __DIR__ . '/../models/Warehouse.php';

class WarehouseController
{
    private Warehouse $model;
    private int $level;
    private ?int $userId;

    public function __construct(PDO $db)
    {
        if (!isset($_SESSION['user'])) {
            throw new Exception('Sesión no válida');
        }

        $this->model = new Warehouse($db);
        $this->level = (int)($_SESSION['user']['level_user'] ?? 0);
        $this->userId = $_SESSION['user']['user_id'] ?? null;
    }


    private function canCreate(): bool
    {
        return in_array($this->level, [1, 2, 3]);
    }

    private function canEdit(): bool
    {
        return in_array($this->level, [1, 2]);
    }

    private function canDelete(): bool
    {
        return in_array($this->level, [1, 2]);
    }

    public function list()
    {
        return $this->model->all();
    }

    public function create(string $name)
    {
        if (!$this->canCreate()) {
            throw new Exception('No autorizado');
        }
        return $this->model->create($name);
    }

    public function update(int $id, string $name)
    {
        if (!$this->canEdit()) {
            throw new Exception('No autorizado');
        }
        return $this->model->update($id, $name);
    }

    public function delete(int $id)
    {
        if (!$this->canDelete()) {
            throw new Exception('No autorizado');
        }
        return $this->model->delete($id);
    }
}
