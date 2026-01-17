<?php
// models/Inventory.php

class Inventory
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function totalProducts(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM products");
        return (int)$stmt->fetchColumn();
    }

    public function inStockCount(): int
    {
        // Consideramos stock > 0
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE COALESCE(stock,0) > 0");
        return (int)$stmt->fetchColumn();
    }

    public function lowStockCount(): int
    {
        // stock < desired_stock, evita nulls
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE COALESCE(desired_stock,0) > 0 AND COALESCE(stock,0) < COALESCE(desired_stock,0)");
        return (int)$stmt->fetchColumn();
    }

    public function totalValue(): float
    {
        // SUM(price * stock) as total. Evita nulls
        $stmt = $this->db->query("SELECT SUM(COALESCE(price,0) * COALESCE(stock,0)) FROM products");
        $val = $stmt->fetchColumn();
        return $val === null ? 0.0 : (float)$val;
    }
}
