<?php
// Archivo: views/pages/404.php
// -----------------------------------------------------------------------------
// Vista encargada de mostrar el error 404 cuando el usuario intenta acceder
// a una ruta inexistente o no permitida dentro de la aplicación.
// -----------------------------------------------------------------------------

/* =============================================================================
   1. CONTROL DE ACCESO / AUTENTICACIÓN
   ============================================================================= */

/**
 * require_once:
 * Incluye obligatoriamente el archivo encargado de validar la sesión.
 * 
 * __DIR__:
 * Se utiliza para obtener la ruta absoluta del archivo actual y evitar
 * errores por rutas relativas incorrectas.
 * 
 * Este archivo normalmente:
 * - Verifica si el usuario está autenticado
 * - Redirige al login si la sesión no existe
 */
require_once __DIR__ . '/../inc/auth_check.php';

/* =============================================================================
   2. INICIO DEL BUFFER DE SALIDA
   ============================================================================= */

/**
 * ob_start():
 * Activa el buffer de salida.
 * 
 * Todo el HTML que se genere a partir de este punto:
 * - No se envía directamente al navegador
 * - Se almacena temporalmente en memoria
 * 
 * Esto permite insertar el contenido dentro de un layout principal
 * (navbar, sidebar, footer, etc.).
 */
ob_start();
?>

<!-- ==========================================================================
     CONTENIDO HTML DE LA PÁGINA 404
     ========================================================================== -->

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 text-center">
  
  <!-- Ícono visual representativo del error -->
  <div class="mb-4">
    <i class="bi bi-emoji-frown display-1 text-danger"></i>
  </div>

  <!-- Título principal del error -->
  <h1 class="display-4 fw-bold">
    404 - Página no encontrada
  </h1>

  <!-- Mensaje descriptivo para el usuario -->
  <p class="lead text-muted mb-4">
    Lo sentimos, la página que estás buscando no existe o ha sido movida.
  </p>

  <!-- Botón para regresar al dashboard principal -->
  <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary btn-lg">
    <i class="bi bi-house-door-fill me-1"></i>
    Volver al inicio
  </a>
  
</div>

<?php
/* =============================================================================
   3. FINALIZACIÓN DEL BUFFER DE SALIDA
   ============================================================================= */

/**
 * ob_get_clean():
 * - Finaliza el buffer de salida
 * - Obtiene todo el HTML capturado
 * - Lo guarda en la variable $content
 * - Limpia el buffer
 * 
 * Esta variable será utilizada dentro del layout principal.
 */
$content = ob_get_clean();

/* =============================================================================
   4. CARGA DEL LAYOUT PRINCIPAL
   ============================================================================= */

/**
 * include:
 * Carga el archivo del layout principal (navbar).
 * 
 * Este layout:
 * - Imprime la variable $content
 * - Mantiene una estructura visual consistente
 *   (menú, encabezado, estilos globales, etc.)
 */
include __DIR__ . '/../partials/layouts/navbar.php';

/* =============================================================================
   5. REFERENCIA DE CLASES BOOTSTRAP UTILIZADAS
   =============================================================================
 *
 * Clase                    | Descripción
 * -------------------------|--------------------------------------------------
 * container                | Contenedor con ancho responsivo centrado.
 * d-flex                   | Convierte el contenedor en un flexbox.
 * flex-column              | Acomoda los elementos hijos en columna.
 * justify-content-center   | Centra verticalmente los elementos.
 * align-items-center       | Centra horizontalmente los elementos.
 * min-vh-100               | Altura mínima del 100% de la ventana.
 * text-center              | Alinea el texto al centro.
 * display-1 / display-4    | Tipografías grandes para títulos destacados.
 * text-danger              | Color rojo, usado para indicar error.
 * fw-bold                  | Texto en negrita.
 * lead                     | Texto más grande y legible para descripciones.
 * text-muted               | Color gris suave para textos secundarios.
 * mb-4 / me-1              | Espaciados Bootstrap (margin bottom / margin end).
 * btn / btn-primary / btn-lg | Botón principal de tamaño grande.
 * bi-*                     | Íconos de Bootstrap Icons.
 * =============================================================================
 */
?>
