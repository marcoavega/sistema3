<?php
// api/products.php
// Mostrar errores en desarrollo (retirar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$action = $_GET['action'] ?? '';
$productController = new ProductController();

// Caso paginación remota:
if ($action === 'list') {
    require_once __DIR__ . '/../models/Database.php';
    $db = (new Database())->getConnection();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $size = isset($_GET['size']) ? (int)$_GET['size'] : 20000;
    if ($page < 1) $page = 1;
    if ($size < 1) $size = 5000;
    $offset = ($page - 1) * $size;

    // Leer filtros desde JavaScript
    $status     = $_GET['status'] ?? '';
    $stock      = $_GET['stock'] ?? '';
    $priceFrom  = $_GET['priceFrom'] ?? '';
    $priceTo    = $_GET['priceTo'] ?? '';

    // Construir consulta base
    $sql = "
        SELECT 
            product_id, product_code, barcode, product_name, product_description, location, price, stock, registration_date,
            category_id, supplier_id, unit_id, currency_id, image_url, subcategory_id,
            desired_stock, status, sale_price, weight, height, length, width, diameter
        FROM products
        WHERE 1=1
    ";
    $countSql = "SELECT COUNT(*) FROM products WHERE 1=1";

    $params = [];

    // Agregar filtros dinámicamente
    if ($status !== '') {
        $sql      .= " AND status = :status";
        $countSql .= " AND status = :status";
        $params[':status'] = $status;
    }

    if ($priceFrom !== '') {
        $sql      .= " AND price >= :priceFrom";
        $countSql .= " AND price >= :priceFrom";
        $params[':priceFrom'] = $priceFrom;
    }

    if ($priceTo !== '') {
        $sql      .= " AND price <= :priceTo";
        $countSql .= " AND price <= :priceTo";
        $params[':priceTo'] = $priceTo;
    }

    if ($stock !== '') {
        if ($stock === 'low') {
            $sql      .= " AND stock < desired_stock";
            $countSql .= " AND stock < desired_stock";
        } elseif ($stock === 'normal') {
            $sql      .= " AND stock = desired_stock";
            $countSql .= " AND stock = desired_stock";
        } elseif ($stock === 'high') {
            $sql      .= " AND stock > desired_stock";
            $countSql .= " AND stock > desired_stock";
        }
    }

    // Agregar orden y límite
    $sql .= " ORDER BY product_id DESC LIMIT :offset, :size";

    try {
        // Calcular total con filtros aplicados
        $countStmt = $db->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        // Obtener productos con filtros y paginación
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':size', $size, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "last_page" => ceil($total / $size),
            "data" => $rows
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "last_page" => 0,
            "data" => [],
            "error" => $e->getMessage(),
        ]);
    }
    exit;
}



switch ($action) {


    case 'get':
        $products = $productController->getAllProducts();
        echo json_encode($products);
        break;


    case 'create':
        header('Content-Type: application/json');
        session_start();
        
if (!($_SESSION['permissions']['products']['create'] ?? false)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado (create)']);
    exit;
}

        require_once __DIR__ . '/../models/Database.php';
        $db = (new Database())->getConnection();

        require_once __DIR__ . '/../controllers/ProductController.php';
        $productController = new ProductController();

        // DEBUG: Ver qué datos llegan por POST
        error_log("🟢 POST completo en create: " . print_r($_POST, true));


        // 1) Recoger $_POST campos en $data...
        $data = [];
        if (isset($_POST['product_code']))   $data['product_code'] = trim($_POST['product_code']);
        if (isset($_POST['barcode']))        $data['barcode'] = trim($_POST['barcode']);
        if (isset($_POST['product_name']))   $data['product_name'] = trim($_POST['product_name']);
        if (isset($_POST['product_description'])) $data['product_description'] = trim($_POST['product_description']);
        if (isset($_POST['location']))       $data['location'] = trim($_POST['location']);
        if (isset($_POST['price']))          $data['price'] = $_POST['price'];
        if (isset($_POST['stock']))          $data['stock'] = $_POST['stock'];
        if (isset($_POST['category_id']))    $data['category_id'] = $_POST['category_id'];
        if (isset($_POST['supplier_id']))    $data['supplier_id'] = $_POST['supplier_id'];
        if (isset($_POST['unit_id']))        $data['unit_id'] = $_POST['unit_id'];
        if (isset($_POST['currency_id']))    $data['currency_id'] = $_POST['currency_id'];
        if (isset($_POST['subcategory_id'])) $data['subcategory_id'] = $_POST['subcategory_id'];
        if (isset($_POST['desired_stock']))  $data['desired_stock'] = $_POST['desired_stock'];
        if (isset($_POST['status']))         $data['status'] = $_POST['status'];
        // Campos nuevos: aceptar valor o null si viene vacío
        if (isset($_POST['sale_price']) && $_POST['sale_price'] !== '') $data['sale_price'] = str_replace(',', '.', $_POST['sale_price']);
        if (isset($_POST['weight']) && $_POST['weight'] !== '')       $data['weight']     = str_replace(',', '.', $_POST['weight']);
        if (isset($_POST['height']) && $_POST['height'] !== '')       $data['height']     = str_replace(',', '.', $_POST['height']);
        if (isset($_POST['length']) && $_POST['length'] !== '')       $data['length']     = str_replace(',', '.', $_POST['length']);
        if (isset($_POST['width']) && $_POST['width'] !== '')         $data['width']      = str_replace(',', '.', $_POST['width']);
        if (isset($_POST['diameter']) && $_POST['diameter'] !== '')   $data['diameter']   = str_replace(',', '.', $_POST['diameter']);


        // Validaciones básicas:
        if (empty($data['product_code']) || empty($data['product_name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Código y nombre son obligatorios']);
            exit;
        }

        // 2) Insertar producto usando el método del controlador
        $result = $productController->createProduct($data);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $result['message']]);
            exit;
        }

        $product = $result['product'];
        $newId = $product['product_id'];

        // 3) Procesar imagen subida
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image_file']['tmp_name'];
            $originalName = basename($_FILES['image_file']['name']);
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowedExt)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Extensión de imagen no permitida.']);
                exit;
            }
            $uploadDir = __DIR__ . '/../assets/images/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $newName = "product_{$newId}.{$ext}";
            $fullPath = $uploadDir . $newName;
            if (!move_uploaded_file($tmp_name, $fullPath)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen.']);
                exit;
            }
            $relativePath = 'assets/images/products/' . $newName;
            try {
                $stmtUpd = $db->prepare("UPDATE products SET image_url = :img WHERE product_id = :id");
                $stmtUpd->bindValue(':img', $relativePath, PDO::PARAM_STR);
                $stmtUpd->bindValue(':id', $newId, PDO::PARAM_INT);
                $stmtUpd->execute();
                $product['image_url'] = $relativePath;
            } catch (PDOException $e) {
                error_log("Error al actualizar image_url: " . $e->getMessage());
            }
        }

        // 4) Añadir image_version
        if (!empty($product['image_url'])) {
            $filePath = __DIR__ . '/../' . $product['image_url'];
            $product['image_version'] = file_exists($filePath) ? filemtime($filePath) : null;
        } else {
            $product['image_version'] = null;
        }

        echo json_encode(['success' => true, 'product' => $product]);
        
        break;



    case 'update':
        header('Content-Type: application/json');

        session_start();
        if (!($_SESSION['permissions']['products']['edit'] ?? false)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado (update)']);
    exit;
}

        require_once __DIR__ . '/../models/Database.php';
        $db = (new Database())->getConnection();
        require_once __DIR__ . '/../controllers/ProductController.php';
        $productController = new ProductController();

        $product_id = $_POST['product_id'] ?? null;
        if (!$product_id || !is_numeric($product_id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'product_id inválido']);
            exit;
        }
        $product_id = (int)$product_id;
        $data = [];
        if (isset($_POST['product_code']))   $data['product_code'] = trim($_POST['product_code']);
        if (isset($_POST['barcode']))        $data['barcode'] = trim($_POST['barcode']);
        if (isset($_POST['product_name']))   $data['product_name'] = trim($_POST['product_name']);
        if (isset($_POST['location']))       $data['location'] = trim($_POST['location']);
        if (isset($_POST['price']))          $data['price'] = $_POST['price'];
        if (isset($_POST['stock']))          $data['stock'] = $_POST['stock'];
        if (isset($_POST['category_id']))    $data['category_id'] = $_POST['category_id'];
        if (isset($_POST['supplier_id']))    $data['supplier_id'] = $_POST['supplier_id'];
        if (isset($_POST['unit_id']))        $data['unit_id'] = $_POST['unit_id'];
        if (isset($_POST['currency_id']))    $data['currency_id'] = $_POST['currency_id'];
        if (isset($_POST['subcategory_id'])) $data['subcategory_id'] = $_POST['subcategory_id'];
        if (isset($_POST['desired_stock']))  $data['desired_stock'] = $_POST['desired_stock'];
        if (isset($_POST['status']))         $data['status'] = $_POST['status'];
        if (isset($_POST['product_description'])) $data['product_description'] = trim($_POST['product_description']);
        // Campos nuevos: aceptar valor o null si viene vacío
        if (isset($_POST['sale_price'])) $data['sale_price'] = $_POST['sale_price'] !== '' ? str_replace(',', '.', $_POST['sale_price']) : null;
        if (isset($_POST['weight']))     $data['weight']     = $_POST['weight']     !== '' ? str_replace(',', '.', $_POST['weight']) : null;
        if (isset($_POST['height']))     $data['height']     = $_POST['height']     !== '' ? str_replace(',', '.', $_POST['height']) : null;
        if (isset($_POST['length']))     $data['length']     = $_POST['length']     !== '' ? str_replace(',', '.', $_POST['length']) : null;
        if (isset($_POST['width']))      $data['width']      = $_POST['width']      !== '' ? str_replace(',', '.', $_POST['width']) : null;
        if (isset($_POST['diameter']))   $data['diameter']   = $_POST['diameter']   !== '' ? str_replace(',', '.', $_POST['diameter']) : null;


        // Otros opcionales...

        // Procesar imagen subida
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image_file']['tmp_name'];
            $originalName = basename($_FILES['image_file']['name']);
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($ext, $allowedExt)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Extensión de imagen no permitida.']);
                exit;
            }
            $uploadDir = __DIR__ . '/../assets/images/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            // Eliminar imagen previa si existe
            try {
                $stmtOld = $db->prepare("SELECT image_url FROM products WHERE product_id = :id");
                $stmtOld->bindParam(':id', $product_id, PDO::PARAM_INT);
                $stmtOld->execute();
                $old = $stmtOld->fetch(PDO::FETCH_ASSOC);
                if ($old && !empty($old['image_url'])) {
                    $oldPath = __DIR__ . '/../' . $old['image_url'];
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
            } catch (PDOException $e) {
                error_log("Error obteniendo imagen previa: " . $e->getMessage());
            }
            // Mover nueva con nombre fijo
            $newName = "product_{$product_id}.{$ext}";
            $fullPath = $uploadDir . $newName;
            if (!move_uploaded_file($tmp_name, $fullPath)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al guardar imagen.']);
                exit;
            }
            $data['image_url'] = 'assets/images/products/' . $newName;
        }

        // Llamar al controlador -> modelo
        $result = $productController->updateProduct($product_id, $data);



        if ($result['success']) {

            //Registrar en bitácora los cambios realizados
            require_once __DIR__ . '/../models/Logger.php';
            session_start();
            $userId = $_SESSION['user']['user_id'] ?? null;
            $productName = $data['product_name'] ?? '';
            if ($userId && $productName) {
                $logger = new Logger();
                $logger->log($userId, "Actualizó el producto: $productName");
            }

            // Recuperar registro actualizado
            try {
                $stmt2 = $db->prepare("SELECT * FROM products WHERE product_id = :id");
                $stmt2->bindValue(':id', $product_id, PDO::PARAM_INT);
                $stmt2->execute();
                $product = $stmt2->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo json_encode(['success' => true, 'product' => ['product_id' => $product_id]]);
                exit;
            }
            // Añadir image_version
            if (!empty($product['image_url'])) {
                $filePath = __DIR__ . '/../' . $product['image_url'];
                $product['image_version'] = file_exists($filePath) ? filemtime($filePath) : null;
            } else {
                $product['image_version'] = null;
            }
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        break;


    case 'delete':

        if (!($_SESSION['permissions']['products']['edit'] ?? false)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado (update)']);
    exit;
}

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'JSON inválido']);
            exit;
        }

        if (!isset($payload['product_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Falta product_id']);
            exit;
        }

        $id = (int)$payload['product_id'];

        // Obtener nombre del producto ANTES de eliminarlo
        require_once __DIR__ . '/../models/Database.php';
        $db = (new Database())->getConnection();
        $productName = '';
        try {
            $stmt = $db->prepare("SELECT product_name FROM products WHERE product_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $productName = $row['product_name'] ?? '';
        } catch (PDOException $e) {
            error_log("Error al obtener nombre del producto para log: " . $e->getMessage());
        }

        // Eliminar producto
        $result = $productController->deleteProduct($id);

        if ($result['success']) {
            session_start();
            $userId = $_SESSION['user']['user_id'] ?? null;

            if (!empty($productName) && $userId) {
                require_once __DIR__ . '/../models/Logger.php';
                $logger = new Logger();
                $logger->log($userId, "Eliminó el producto: $productName");
            }

            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        break;


    case 'stats':
        $products = $productController->getAllProducts();

        $total = count($products);
        $inStock = 0;
        $lowStock = 0;
        $totalValue = 0;

        foreach ($products as $product) {
            $stock = (int)$product['stock'];
            $desired = (int)$product['desired_stock'];
            $price = (float)$product['price'];

            if ($stock > 0) {
                $inStock++;
            }

            if ($desired > 0 && $stock < $desired) {
                $lowStock++;
            }

            $totalValue += $stock * $price;
        }

        echo json_encode([
            "last_page" => max(1, (int) ceil($total / $size)),
            "page"      => $page,
            "total"     => $total,
            "data"      => $rows
        ]);
        break;



    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción no definida']);
}
exit;
