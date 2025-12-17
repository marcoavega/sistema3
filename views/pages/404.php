<?php
// Archivo: views/pages/404.php

// Verifica si el usuario está logueado, si no, redirige
require_once __DIR__ . '/../inc/auth_check.php';

// Inicia el buffer de salida
ob_start();
?>

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 text-center">
  <div class="mb-4">
    <i class="bi bi-emoji-frown display-1 text-danger"></i>
  </div>
  <h1 class="display-4 fw-bold">404 - Página no encontrada</h1>
  <p class="lead text-muted mb-4">Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
  <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary btn-lg">
    <i class="bi bi-house-door-fill me-1"></i> Volver al inicio
  </a>
</div>

<?php
// Finaliza el buffer y guarda el contenido
$content = ob_get_clean();

// Incluye la plantilla con el navbar donde se mostrará $content
include __DIR__ . '/../partials/layouts/navbar.php';
?>