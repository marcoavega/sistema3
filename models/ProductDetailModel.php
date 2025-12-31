<?php
// Archivo: models/ProductDetailModel.php

require_once __DIR__ . '/Database.php';

class ProductDetailModel
{
    /**
     * Obtiene toda la información necesaria
     * para la vista product_detail
     */
    public function getProductDetail($product_id)
    {
        $pdo = (new Database())->getConnection();

        // =====================================================
        // PRODUCTO
        // =====================================================
        $stmt = $pdo->prepare("
            SELECT 
                product_id,
                product_code,
                barcode,
                product_name,
                product_description,
                location,
                price,
                stock,
                registration_date,
                category_id,
                supplier_id,
                unit_id,
                currency_id,
                image_url,
                subcategory_id,
                desired_stock,
                status,
                sale_price,
                weight,
                height,
                length,
                width,
                diameter,
                updated_at
            FROM products
            WHERE product_id = :product_id
            LIMIT 1
        ");
        $stmt->execute(['product_id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        // =====================================================
        // ALMACENES
        // =====================================================
        try {
            $whs = $pdo->query("
                SELECT warehouse_id, name
                FROM warehouses
                ORDER BY warehouse_id
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $whs = [];
        }

        // =====================================================
        // STOCK POR ALMACÉN
        // =====================================================
        $warehouses_stock = [];

        try {
            $table = null;

            if ($pdo->query("SHOW TABLES LIKE 'warehouse_stock'")->fetch()) {
                $table = 'warehouse_stock';
            } elseif ($pdo->query("SHOW TABLES LIKE 'product_stock'")->fetch()) {
                $table = 'product_stock';
            }

            if ($table) {
                $sql = "
                    SELECT 
                        w.warehouse_id,
                        w.name AS warehouse_name,
                        COALESCE(s.stock, 0) AS stock
                    FROM warehouses w
                    LEFT JOIN {$table} s
                        ON s.warehouse_id = w.warehouse_id
                       AND s.product_id = :product_id
                    ORDER BY w.warehouse_id
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['product_id' => $product_id]);
                $warehouses_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                foreach ($whs as $w) {
                    $warehouses_stock[] = [
                        'warehouse_id'   => $w['warehouse_id'],
                        'warehouse_name' => $w['name'],
                        'stock'          => 0
                    ];
                }
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }

        return [
            'product'           => $product,
            'warehouses'        => $whs,
            'warehouses_stock'  => $warehouses_stock
        ];
    }
}
