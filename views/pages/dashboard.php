<?php
// Archivo: views/pages/dashboard.php

// Verificación de sesión: si no hay una sesión iniciada, la inicia
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario está logueado, si no, redirige al login
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "auth/login/");
    exit(); // Detiene la ejecución del script
}

// Obtiene el segmento de la URL para determinar el ítem activo en el menú
$uri = $_GET['url'] ?? 'dashboard'; // Si no hay parámetro 'url', usa 'dashboard'
$segment = explode('/', trim($uri, '/'))[0]; // Obtiene el primer segmento

// Inicia el buffer de salida para capturar el contenido HTML
ob_start();

// Escapa el nombre de usuario para evitar XSS
$username = htmlspecialchars($_SESSION['user']['username']);

// Conecta a la base de datos
require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

// Consulta para contar usuarios
$stmtUsers = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmtUsers->fetch()['total_users'] ?? 0;

// Consulta para contar productos
$stmtProducts = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
$totalProducts = $stmtProducts->fetch()['total_products'] ?? 0;

// Incluye el menú lateral personalizado del dashboard
require_once __DIR__ . '/../partials/layouts/lateral_menu_dashboard.php';
?>

<!-- Contenedor principal -->
<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
    <div class="row g-0">

        <!-- Menú lateral para pantallas medianas y grandes -->
        <nav class="col-md-2 d-none d-md-block sidebar min-vh-100">
            <div class="pt-4 px-3">
                <div class="text-center mb-4">
                    <!-- Icono circular del sistema -->
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center dashboard-nav-styles">
                        <i class="bi bi-speedometer2 text-primary fs-3"></i>
                    </div>
                    <!-- Nombre del sistema -->
                    <h6 class="mt-2 mb-0">Sistema</h6>
                </div>

                <!-- Menú lateral con ítems dinámicos -->
                <ul class="nav flex-column">
                    <?php foreach ($menuItems as $route => $item): ?>
                        <li class="nav-item mb-2">
                            <a
                                class="nav-link d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'bg-primary text-white fw-bold' : 'text-body' ?> dashboard-a-li-ul-styles"
                                href="<?= BASE_URL . $route ?>">
                                <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                                <span class="fw-medium"><?= $item['label'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>

        <!-- Contenido principal del dashboard -->
        <main class="col-12 col-md-10">

            <!-- Encabezado con breadcrumb -->
            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <!-- Breadcrumb para navegación -->
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2">
                                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                                </ol>
                            </nav>
                            <!-- Título y bienvenida -->
                            <h4 class="mb-0 fw-bold">Panel de Control</h4>
                            <small class="text-muted">Bienvenido, <?= $username ?></small>
                        </div>

                        <!-- Botón para menú móvil -->
                        <div class="d-md-none">
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                                <i class="bi bi-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menú lateral para pantallas pequeñas (offcanvas) -->
            <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileMenu">
                <div class="offcanvas-header bg-primary-subtle">
                    <h5 class="offcanvas-title"><i class="bi bi-speedometer2 me-2"></i>Sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body bg-body">
                    <!-- Ítems del menú móvil -->
                    <ul class="nav flex-column">
                        <?php foreach ($menuItems as $route => $item): ?>
                            <li class="nav-item mb-2">
                                <a
                                    class="nav-link text-body d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'active bg-primary text-white' : '' ?>"
                                    href="<?= BASE_URL . $route ?>">
                                    <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                                    <?= $item['label'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Contenedor de estadísticas -->
            <div class="container-fluid px-4 py-4">

                <!-- Tarjetas de resumen -->
                <div class="row g-3 mb-4">
                    <!-- Usuarios -->
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill fs-1 text-primary mb-2"></i>
                                <h6>Usuarios</h6>
                                <h3><?= $totalUsers ?></h3>
                            </div>
                        </div>
                    </div>
                    <!-- Productos -->
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-box-seam fs-1 text-success mb-2"></i>
                                <h6>Productos</h6>
                                <h3><?= $totalProducts ?></h3>
                            </div>
                        </div>
                    </div>
                    <!-- Órdenes (valor no disponible) -->
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-cart-check fs-1 text-warning mb-2"></i>
                                <h6>Órdenes</h6>
                                <h3>--</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Reportes (valor no disponible) -->
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up fs-1 text-danger mb-2"></i>
                                <h6>Reportes</h6>
                                <h3>--</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de actividad reciente -->
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">Actividad Reciente</h5>
                            </div>

                            <!-- Buscador y exportar -->
                            <div class="card-body p-4">
                                <div class="row g-3 mb-4">
                                    <!-- Campo de búsqueda -->
                                    <div class="col-md-6">
                                        <div class="position-relative">
                                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                            <input type="text" id="table-search" class="form-control form-control-lg ps-5 rounded-pill border-2" placeholder="Buscar por usuario o acción">
                                        </div>
                                    </div>

                                    <!-- Botón exportar -->
                                    <div class="col-md-6">
                                        <div class="d-flex gap-2 justify-content-md-end">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-download me-2"></i>Exportar
                                                </button>
                                                <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                                                    <li>
                                                        <h6 class="dropdown-header fw-bold">Formatos disponibles</h6>
                                                    </li>
                                                    <li><button id="exportCSVBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-filetype-csv text-success me-2"></i>Exportar a CSV</button></li>
                                                    <li><button id="exportExcelBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-file-earmark-excel text-success me-2"></i>Exportar a Excel</button></li>
                                                    <li><button id="exportPDFBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Exportar a PDF</button></li>
                                                    <li><button id="exportJSONBtn" class="dropdown-item d-flex align-items-center"><i class="bi bi-filetype-json text-info me-2"></i>Exportar a JSON</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fin buscador + exportar -->
                            </div>
                        </div>

                        <!-- Contenedor de la tabla Tabulator -->
                        <div class="card-body p-0">
                            <div id="recent-activity-table" class="border rounded-3"></div>
                        </div>
                    </div>
                </div>
        </main>
    </div>
</div>

<!-- Script JavaScript para cargar actividad reciente -->
<script src="<?= BASE_URL ?>assets/js/ajax/dashboard-activity.js"></script>

<?php
// Finaliza el buffer y guarda el contenido
$content = ob_get_clean();

// Incluye la plantilla con el navbar donde se mostrará $content
include __DIR__ . '/../partials/layouts/navbar.php';


/**
 Contenedores y estructuras generales
container-fluid: Contenedor de ancho completo (100%) que se adapta al tamaño de la pantalla.

row: Fila que contiene columnas. Utilizado dentro de un contenedor para dividir el espacio en columnas.

g-0: Elimina el espacio (gutter) entre columnas en una fila.

min-vh-100: Altura mínima del 100% del viewport (pantalla completa).

m-0: Elimina todos los márgenes exteriores.

p-0: Elimina todo el padding interior.

px-4: Padding horizontal (izquierda y derecha) de 1.5rem.

py-3, py-4: Padding vertical (arriba y abajo) de 1rem (3) o 1.5rem (4).

mb-4, mb-2, mb-0: Margen inferior de 1.5rem, 0.5rem o ninguno.

mt-2: Margen superior de 0.5rem.

pt-4: Padding superior de 1.5rem.

px-3, py-2: Padding interior horizontal y vertical.

p-4: Padding de todos los lados (1.5rem).

p-0: Sin padding.

🔷 Columnas responsivas
col-md-2: Columna que ocupa 2 de 12 columnas en pantallas medianas o mayores.

col-md-3: Columna que ocupa 3 espacios en pantallas md+.

col-md-6: Columna que ocupa 6 espacios en pantallas md+.

col-md-10: Columna que ocupa 10 espacios en pantallas md+.

col-12: Columna de ancho completo en pantallas pequeñas.

🔷 Utilidades de visualización
d-none: Oculta el elemento en todos los tamaños.

d-md-block: Muestra el elemento como bloque en pantallas md o mayores.

d-md-none: Oculta el elemento en pantallas medianas o mayores.

d-flex: Establece display: flex.

d-inline-flex: Flex en línea (permite que esté junto a otros elementos).

flex-column: Apila los elementos en dirección vertical.

align-items-center: Centra los elementos verticalmente en un contenedor flex.

justify-content-center: Centra los elementos horizontalmente.

justify-content-between: Espacia los elementos entre el inicio y el final del contenedor.

justify-content-md-end: Alinea a la derecha en pantallas medianas o mayores.

🔷 Textos y colores
text-center: Centra el texto horizontalmente.

text-primary: Texto en color primario del tema (generalmente azul).

text-success: Texto en verde (éxito).

text-warning: Texto en amarillo (advertencia).

text-danger: Texto en rojo (peligro o error).

text-info: Texto en azul claro (información).

text-body: Color estándar del cuerpo del texto.

text-white: Texto blanco.

text-muted: Texto en gris claro.

text-decoration-none: Elimina subrayado de enlaces.

🔷 Tipografía
fw-bold: Fuente en negrita.

fw-medium: Peso medio (menos que negrita).

fs-1, fs-3, fs-5: Tamaños de fuente predefinidos (1 = más grande).

h3, h4, h5, h6: Títulos jerárquicos, con estilo de encabezado.

🔷 Componentes de navegación
nav: Lista de navegación.

nav-item: Elemento de navegación individual.

nav-link: Estilo para enlaces de navegación.

active: Estilo para indicar un elemento activo.

breadcrumb: Estilo para rutas de navegación.

breadcrumb-item: Elemento dentro de una ruta.

dropdown: Contenedor de menú desplegable.

dropdown-toggle: Botón que despliega el menú.

dropdown-menu: Lista desplegable de opciones.

dropdown-header: Encabezado dentro del menú desplegable.

dropdown-item: Opción individual dentro del menú.

🔷 Botones
btn: Clase base para botones.

btn-outline-primary: Botón con borde azul y fondo transparente.

btn-close: Botón con ícono de cerrar.

btn-outline-*: Variante con solo borde del color.

rounded-pill: Bordes completamente redondeados (estilo píldora).

rounded-3: Bordes redondeados medianos.

🔷 Utilidades de espacio y posición
gap-2: Espacio entre elementos hijos de 0.5rem.

me-2, me-3: Margen derecho (espacio entre íconos/textos).

ms-3: Margen izquierdo.

top-50: Posiciona al 50% desde arriba.

start-0: Alinea completamente a la izquierda.

position-relative: Posición relativa para contenedores.

position-absolute: Posición absoluta (respecto al relativo).

translate-middle-y: Centra verticalmente usando transform: translateY(-50%).

🔷 Componentes específicos
sidebar: Clase personalizada, normalmente usada para una barra lateral.

card: Contenedor con sombra y borde redondeado.

card-body: Cuerpo principal de una tarjeta.

card-header: Encabezado superior de una tarjeta.

shadow-sm, shadow-lg: Sombra ligera o grande.

border-0: Sin borde.

border-bottom: Borde en la parte inferior.

border: Borde estándar.

form-control: Campo de formulario con estilos predeterminados.

form-control-lg: Campo más grande.

offcanvas, offcanvas-start: Panel lateral oculto que aparece desde el lado izquierdo.

offcanvas-header, offcanvas-body: Partes del panel lateral.

bg-body: Fondo que se adapta al tema claro/oscuro.

bg-primary-subtle: Fondo azul claro.

🔷 Otros
dashboard-nav-styles, dashboard-a-li-ul-styles: Clases personalizadas, no pertenecen a Bootstrap. Su función depende del CSS definido por ti.

bi bi-*: Clases de íconos de Bootstrap Icons, por ejemplo:

bi-speedometer2: velocímetro

bi-box-seam: caja de productos

bi-people-fill: usuarios

bi-graph-up: gráfico

bi-list: ícono de lista/hamburguesa

bi-download: ícono de descarga

bi-filetype-*: íconos según tipo de archivo (CSV, Excel, PDF, JSON) 
*/