<?php
// Obtiene el segmento desde el parámetro 'url' en GET (si no está definido, usa 'dashboard' por defecto).
$uri = $_GET['url'] ?? 'dashboard';

// Divide la URL en partes separadas utilizando "/" para obtener el primer segmento (nombre de la página o sección actual).
$segment = explode('/', trim($uri, '/'))[0];
?>
<!-- Navbar Moderno -->
<nav class="navbar navbar-expand-lg navbar-modern fixed-top">
    <div class="container-fluid px-3">
        
        <!-- Brand/Logo -->
        <!-- Muestra el nombre de la app con un ícono y enlace al dashboard -->
        <a class="navbar-brand navbar-brand-modern d-flex align-items-center" href="<?= BASE_URL ?>dashboard">
            <i class="bi bi-gem me-2" style="font-size: 1.8rem;"></i>
            <span>Mi Aplicación</span>
        </a>

        <!-- Toggler para móviles -->
        <!-- Botón que aparece en pantallas pequeñas para mostrar/ocultar el menú -->
        <button class="navbar-toggler navbar-toggler-modern" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar" aria-controls="mainNavbar"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            
            <!-- Menú principal -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                <!-- Opción: Inicio -->
                <li class="nav-item">
                    <!-- Agrega la clase "active" si el segmento actual es 'dashboard' -->
                    <a class="nav-link nav-link-modern d-flex flex-column align-items-center <?= $segment === 'dashboard' ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>dashboard">
                        <i class="bi bi-house-door-fill nav-icon"></i>
                        <span class="small">Inicio</span>
                    </a>
                </li>

                <!-- Opción: Usuarios -->
                <li class="nav-item">
                    <!-- Clase activa si el segmento es 'admin_users' -->
                    <a class="nav-link nav-link-modern d-flex flex-column align-items-center <?= $segment === 'admin_users' ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>admin_users">
                        <i class="bi bi-people-fill nav-icon"></i>
                        <span class="small">Usuarios</span>
                    </a>
                </li>

                <!-- Opción: Inventario -->
                <li class="nav-item">
                    <!-- Clase activa si el segmento es 'inventory' o 'list_product' -->
                    <a class="nav-link nav-link-modern d-flex flex-column align-items-center <?= ($segment === 'inventory' || $segment === 'list_product') ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>inventory">
                        <i class="bi bi-box-seam nav-icon"></i>
                        <span class="small">Inventario</span>
                    </a>
                </li>

            </ul>

            <!-- Controles del lado derecho -->
            <div class="d-flex align-items-center gap-2">

                <!-- Botón para cambiar el tema claro/oscuro -->
                <button class="btn btn-theme-toggle" id="themeToggleBtn" title="Cambiar tema">
                    <i class="bi bi-sun-fill" id="iconLight"></i>
                    <i class="bi bi-moon-fill d-none" id="iconDark"></i>
                </button>

                <!-- Menú de usuario -->
                <div class="dropdown">
                    <button class="btn btn-options dropdown-toggle d-flex align-items-center" type="button" id="optionsMenu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Si hay imagen del usuario, la muestra. Si no, icono por defecto -->
                        <?php if (!empty($_SESSION['user']['img_url'])): ?>
                            <img src="<?= BASE_URL . ltrim($_SESSION['user']['img_url'], '/') ?>"
                                alt="Avatar" class="user-avatar me-2">
                        <?php else: ?>
                            <i class="bi bi-person-circle me-2" style="font-size: 1.5rem;"></i>
                        <?php endif; ?>
                        <span class="d-none d-lg-inline">
                            <?= $_SESSION['user']['name'] ?? 'Usuario' ?>
                        </span>
                    </button>

                    <!-- Menú desplegable de opciones del usuario -->
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-modern" aria-labelledby="optionsMenu">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="bi bi-person-circle me-2"></i>
                                Mi Cuenta
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item dropdown-item-modern" href="<?= BASE_URL ?>profile">
                                <i class="bi bi-person-fill me-3"></i>
                                <div>
                                    <strong>Perfil</strong>
                                    <small class="text-muted d-block">Editar información personal</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-modern" href="<?= BASE_URL ?>preferences">
                                <i class="bi bi-gear me-3"></i>
                                <div>
                                    <strong>Preferencias</strong>
                                    <small class="text-muted d-block">Configurar aplicación</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-modern" href="<?= BASE_URL ?>help">
                                <i class="bi bi-question-circle me-3"></i>
                                <div>
                                    <strong>Ayuda</strong>
                                    <small class="text-muted d-block">Soporte y documentación</small>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <!-- Botón para cerrar sesión -->
                            <button class="dropdown-item dropdown-item-modern text-danger" id="logoutButton">
                                <i class="bi bi-box-arrow-right me-3"></i>
                                <div>
                                    <strong>Cerrar Sesión</strong>
                                    <small class="text-muted d-block">Salir de la aplicación</small>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</nav>

<!-- Espaciador para evitar que el contenido quede detrás del navbar fijo -->
<div class="space-navbar-stick"></div>

<!-- Contenedor principal donde se carga dinámicamente el contenido -->
<div class="container-fluid">
    <?php
    // Se muestra el contenido generado por el controlador
    echo $content;
    ?>
</div>

<?php
// Se incluye el modal de cierre de sesión, que se activa al hacer clic en "Cerrar sesión"
include __DIR__ . '/../modals/modal-logout.php';
?>
