<?php
// lateral_menu_dashboard.php
// Define aquí la estructura del menú.
// Si quieres cambiar el menú por página, solo modifica este archivo.

$menuItems = [
  'admin_users' => ['icon' => 'people-fill', 'label' => 'Usuarios'],
  // puedes agregar más items aquí (ej. 'inventory' => [...])
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
$sidebarTitle = 'Usuarios';
$sidebarIcon  = 'people-fill';

// incluimos el HTML reutilizable
include __DIR__ . '/main_lateral_menu.php';
