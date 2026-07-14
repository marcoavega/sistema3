<?php
// lateral_menu_dashboard.php
// Define aquí la estructura del menú.
// Si quieres cambiar el menú por página, solo modifica este archivo.

$menuItems = [
  'inventory' => ['icon' => 'box-seam', 'label' => 'Inventario', 'sidebarTitle' => 'Inventario'],
  'list_product' => [ 'icon' => 'list-ul', 'label' => 'Listado de Productos', 'sidebarTitle' => 'Listado de Productos'],
];

// Determinar segmento actual de forma robusta:
// Si en la página ya defines $segment antes de incluir este archivo, se respeta.
if (!isset($segment) || !$segment) {
  if (isset($_GET['url']) && $_GET['url'] !== '') {
    $segment = explode('/', trim($_GET['url'], '/'))[0];
  } else {
    // fallback seguro
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $segment = pathinfo($script, PATHINFO_FILENAME) ?: 'dashboard';
  }
}

// Titulo y icono del sidebar
$sidebarTitle = 'Inventario';
$sidebarIcon  = 'box-seam';




// incluimos el HTML reutilizable
include __DIR__ . '/main_lateral_menu.php';
