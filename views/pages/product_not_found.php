<?php
// Archivo: views/pages/product_not_found.php
// -----------------------------------------------------------------------------
// Vista encargada de mostrar el error cuando un producto no existe
// o cuando se intenta acceder a un producto inválido.
// Mantiene la misma estructura que la vista 404 del sistema.
// -----------------------------------------------------------------------------

/* =============================================================================
   1. CONTROL DE ACCESO / AUTENTICACIÓN
   ============================================================================= */

/**
 * Se incluye el archivo encargado de validar la sesión del usuario.
 * Si el usuario no está autenticado, se redirige automáticamente.
 */
require_once __DIR__ . '/../inc/auth_check.php';

/* =============================================================================
   2. INICIO DEL BUFFER DE SALIDA
   ============================================================================= */

/**
 * Se activa el buffer de salida para capturar el HTML
 * y posteriormente inyectarlo dentro del layout principal.
 */
ob_start();
?>

<!-- ========================================================================== -->
<!-- CONTENIDO HTML DE LA PÁGINA PRODUCT NOT FOUND                               -->
<!-- ========================================================================== -->

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 text-center">
  
  <!-- Ícono visual representativo del error -->
  <div class="mb-4">
    <i class="bi bi-exclamation-triangle-fill display-1 text-warning"></i>
  </div>

  <!-- Título principal -->
  <h1 class="display-4 fw-bold">
    Producto no encontrado
  </h1>

  <!-- Mensaje explicativo -->
  <p class="lead text-muted mb-4">
    El producto que intentas consultar no existe, fue eliminado o el enlace es incorrecto.
  </p>

  <!-- Botón para regresar al listado de productos -->
  <a href="<?= BASE_URL ?>list_product" class="btn btn-primary btn-lg">
    <i class="bi bi-box-seam me-1"></i>
    Volver al listado de productos
  </a>
  
</div>

<?php
/* =============================================================================
   3. FINALIZACIÓN DEL BUFFER DE SALIDA
   ============================================================================= */

/**
 * Se obtiene todo el contenido generado y se limpia el buffer.
 * El HTML queda almacenado en la variable $content.
 */
$content = ob_get_clean();

/* =============================================================================
   4. CARGA DEL LAYOUT PRINCIPAL
   ============================================================================= */

/**
 * Se incluye el layout principal que contiene:
 * - Navbar
 * - Estructura general
 * - Impresión de la variable $content
 */
include __DIR__ . '/../partials/layouts/navbar.php';

/* =============================================================================
   5. NOTAS
   =============================================================================
   - Esta vista es cargada desde:
     header("Location: BASE_URL . 'product_not_found'")
   - No contiene lógica de negocio.
   - Solo muestra información visual al usuario.
   =============================================================================
 */
?>
