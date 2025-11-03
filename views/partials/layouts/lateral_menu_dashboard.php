<?php
// Este archivo PHP define los elementos que se mostrarán en el menú lateral de la aplicación.

// Creamos un arreglo asociativo llamado $menuItems que contiene las opciones del menú lateral.
// Cada clave del arreglo representa una ruta o nombre interno del módulo,
// y su valor es otro arreglo con dos claves: 'icon' y 'label'.
//  - 'icon': es el nombre del ícono de Bootstrap Icons que se mostrará.
//  - 'label': es el texto que se mostrará al usuario en la interfaz.

// Este mismo formato es utilizado en otros archivos, como list_product.php,
// por lo tanto, mantiene coherencia en toda la estructura del sistema.

$menuItems = [
  'dashboard'   => ['icon' => 'house-fill', 'label' => 'Panel de Control'], // Ícono de casa, etiqueta "Panel de Control"
  'settings'    => ['icon' => 'gear-fill',  'label' => 'Configuración'],    // Ícono de engranaje, etiqueta "Configuración"
];
?>
