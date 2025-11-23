<?php
// lateral_menu_dashboard.php
// Define aquí la estructura del menú.
// Si quieres cambiar el menú por página, solo modifica este archivo.

$menuItems = [
  'warehouses' => ['icon' => 'building', 'label' => 'ALmacenes', 'sidebarTitle' => 'Almacenes'],
];

// Titulo y icono del sidebar
$sidebarTitle = 'Almacenes';
$sidebarIcon  = 'building';




// incluimos el HTML reutilizable
include __DIR__ . '/main_lateral_menu.php';
