<?php
// lateral_menu_dashboard.php
// Define $menuItems y provee fallbacks para $sidebarTitle, $sidebarIcon, $segment
// Luego incluye main_lateral_menu.php

// Si la vista ya definió $sidebarTitle/$sidebarIcon no se sobrescriben
$sidebarTitle = $sidebarTitle ?? 'Sistema';
$sidebarIcon  = $sidebarIcon  ?? 'speedometer2';

// menu structure
$menuItems = [
  'dashboard' => [
    'icon'  => 'house-fill',
    'label' => 'Panel de Control'
  ],
  'settings' => [
    'icon'  => 'gear-fill',
    'label' => 'Configuración',
    'submenu' => [
      'exchange_rates' => [
        'icon'  => 'currency-exchange',
        'label' => 'Tipo de Cambio'
      ],
      // agregar más subitems si hace falta
    ]
  ]
];

// Determinar segmento actual si no viene desde la vista
$segment = $segment ?? (explode('/', trim($_GET['url'] ?? 'dashboard', '/'))[0]);

// incluir plantilla reutilizable
require __DIR__ . '/main_lateral_menu.php';
