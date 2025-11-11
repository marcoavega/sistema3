<?php
// controllers/ProductController.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Logger.php';


class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * Devuelve todos los productos, y añade en cada uno:
     *   - image_version: timestamp de última modificación del archivo, o null si no hay imagen.
     */
    public function getAllProducts()
    {
        $rows = $this->productModel->getAllProducts(); // array de assoc arrays
        foreach ($rows as &$row) {
            if (!empty($row['image_url'])) {
                // Construir ruta física: 
                // Si image_url es 'assets/images/products/product_12.jpg',
                // la ruta en disco es __DIR__ . '/../' . image_url
                $filePath = __DIR__ . '/../' . $row['image_url'];
                if (file_exists($filePath)) {
                    $row['image_version'] = filemtime($filePath);
                } else {
                    $row['image_version'] = null;
                }
            } else {
                $row['image_version'] = null;
            }
        }
        unset($row);
        return $rows;
    }

    /**
     * Crea un producto; aquí delegas al modelo. 
     * El endpoint (api/products.php) se encargará de procesar imagen y
     * luego llamar a este método, o directamente devolverá el resultado con version.
     */
   public function createProduct($data)
{
    // Verificar si el nombre del producto viene correctamente
    error_log("🟠 createProduct(): \$data = " . print_r($data, true));

    $result = $this->productModel->createProduct($data);

    if ($result['success']) {
        require_once __DIR__ . '/../models/Logger.php';
        $userId = $_SESSION['user']['user_id'] ?? 0;

        // Asegurar que product_name no esté vacío
        $productName = !empty($data['product_name']) ? $data['product_name'] : 'Producto sin nombre';

        // También lo logeamos por si acaso
        error_log("🟢 Producto creado. Nombre recibido para log: " . $productName);

        $logger = new Logger();
        $logger->log($userId, "Creó el producto: $productName");
    }

    return $result;
}



    /**
     * Actualiza un producto. Se espera que $data incluya image_url si ya se procesó imagen
     * en el endpoint. Luego el modelo actualiza y este método devuelve el producto actualizado.
     */
    public function updateProduct($id, $data)
    {
        // Nota: tu modelo updateProduct firma espera array con 'product_id' clave:
        return $this->productModel->updateProduct(array_merge(['product_id' => $id], $data));
    }

    public function deleteProduct($id)
    {
        return $this->productModel->deleteProduct($id);
    }

    public function getStatistics()
    {
        $products = $this->productModel->getAllProducts();

        $total = count($products);
        $inStock = 0;
        $lowStock = 0;
        $totalValue = 0;

        foreach ($products as $p) {
            if ((int)$p['stock'] > 0) {
                $inStock++;
            }
            if (isset($p['desired_stock']) && is_numeric($p['desired_stock']) && (int)$p['stock'] < (int)$p['desired_stock']) {
                $lowStock++;
            }
            $totalValue += ((float)$p['stock']) * ((float)$p['price']);
        }

        return [
            'total' => $total,
            'inStock' => $inStock,
            'lowStock' => $lowStock,
            'totalValue' => number_format($totalValue, 2)
        ];
    }
}




/*
 // Ajustar columnas y JOINs según tu esquema de categorías, proveedores, etc.
            $sql = "SELECT 
                p.product_id,
                p.product_code,
                p.product_name,
                p.location,
                p.price,
                p.stock,
                p.registration_date,
                p.image_url,
                p.category_id,
                c.name AS category_name,
                p.supplier_id,
                s.name AS supplier_name,
                p.unit_id,
                u.name AS unit_name,
                p.currency_id,
                m.code AS currency_code,
                p.subcategory_id,
                sc.name AS subcategory_name,
                p.desired_stock,
                p.status,
                p.sale_price,
                p.weight,
                p.height,
                p.length,
                p.width,
                p.diameter
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN currencies m ON p.currency_id = m.currency_id
            LEFT JOIN subcategories sc ON p.subcategory_id = sc.subcategory_id
            ORDER BY p.product_id DESC";
            */