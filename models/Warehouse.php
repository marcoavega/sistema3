<?php

class Warehouse
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all()
    {
        $stmt = $this->db->query(
            "SELECT warehouse_id AS id, name, created_at, updated_at 
             FROM warehouses ORDER BY warehouse_id ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $name)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO warehouses (name, created_at) VALUES (:name, NOW())"
        );
        $stmt->execute(['name' => $name]);
        return $this->db->lastInsertId();
    }

    public function update(int $id, string $name)
    {
        $stmt = $this->db->prepare(
            "UPDATE warehouses SET name = :name, updated_at = NOW() WHERE warehouse_id = :id"
        );
        return $stmt->execute(['id' => $id, 'name' => $name]);
    }

    public function delete(int $id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM warehouses WHERE warehouse_id = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    public function findName(int $id)
    {
        $stmt = $this->db->prepare(
            "SELECT name FROM warehouses WHERE warehouse_id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn();
    }
}
