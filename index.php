<?php
// Carga las configuraciones generales del sistema (constantes, rutas, etc.)
require_once 'config/config.php';

// Inicia la sesión del usuario
include __DIR__ . '/views/inc/session_start.php';

// Carga la cabecera del HTML (etiquetas meta, CSS, etc.)
include __DIR__ . '/views/partials/head.php';

// Toma la URL enviada por GET, o 'login' por defecto si no hay ninguna
$url = isset($_GET['url']) ? $_GET['url'] : 'login';

// Limpia la URL eliminando espacios y barras
$url = trim($url, "/ ");

// Separa las partes de la URL usando "/"
$parts = explode('/', $url);

// Validación básica para evitar llamadas maliciosas a controladores o métodos
if (!empty($parts[0]) && preg_match('/^[a-zA-Z0-9_]+$/', $parts[0])) {

    // Si el primer segmento es 'auth', se trata de autenticación (login, logout, register...)
    if ($parts[0] === 'auth') {

        require_once 'controllers/AuthController.php';
        $auth = new AuthController();

        // Se obtiene la acción deseada, por ejemplo: login, logout, register...
        $action = isset($parts[1]) ? trim($parts[1]) : 'login';

        // Se verifica que el método exista y no sea un método mágico
        if (method_exists($auth, $action) && strpos($action, '__') !== 0) {
            $auth->$action(); // Se ejecuta la acción
        } else {
            echo "<h1>Error: Acción no válida en AuthController.</h1>";
        }

    } else {
        // Cualquier otra ruta se gestiona como una página normal mediante RouteController
        require_once 'controllers/RouteController.php';
        $controller = new RouteController();
        $controller->loadPage($url);
    }

} else {
    // Si no hay URL válida, se carga por defecto la página login
    require_once 'controllers/RouteController.php';
    $controller = new RouteController();
    $controller->loadPage('login');
}

// Pie de página HTML y scripts comunes
include __DIR__ . '/views/partials/footer.php';
