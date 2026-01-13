<?php
// Archivo: views/pages/warehouses.php
require_once __DIR__ . '/../inc/auth_check.php';

$levelUser = $_SESSION['user']['level_user'] ?? 0;

$canCreate = in_array($levelUser, [1, 2, 3]);
$canEdit   = in_array($levelUser, [1, 2]);
$canDelete = in_array($levelUser, [1, 2]);


$uri = $_GET['url'] ?? 'warehouses';
$segment = explode('/', trim($uri, '/'))[0];

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$username = htmlspecialchars($_SESSION['user']['username']);
?>

<style>
    /* Estilos Soft UI para botones de acción en tabla */
    .btn-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-soft-primary:hover {
        background-color: #0d6efd;
        color: white;
    }

    .btn-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-soft-danger:hover {
        background-color: #dc3545;
        color: white;
    }

    /* Ajuste responsivo Botón Nuevo Almacén */
    @media (max-width: 767px) {
        .btn-responsive-add {
            width: 100%;
        }
    }

    @media (min-width: 768px) {
        .btn-responsive-add {
            width: auto;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
    }
</style>

<div class="container-fluid m-0 p-0 min-vh-100 bg-body-tertiary" data-bs-theme="auto">
    <div class="row g-0">

        <?php require_once __DIR__ . '/../partials/layouts/laterals_menus/lateral_menu_warehouse.php'; ?>

        <main class="col-12 col-md-10">

            <div class="bg-body shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3 text-body">
                    <div class="row align-items-center g-3">
                        <div class="col-8 col-md-6">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2 small">
                                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                                    <li class="breadcrumb-item active text-muted border-0">Almacenes</li>
                                </ol>
                            </nav>
                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-building me-2 text-primary"></i>Gestión de Almacenes
                            </h4>
                        </div>

                        <div class="col-4 d-md-none text-end">
                            <button class="btn btn-outline-primary shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                                <i class="bi bi-list fs-5"></i>
                            </button>
                        </div>

                        <div class="col-12 col-md-6 text-md-end">
                            <?php if ($canCreate): ?>
                                <button id="addWarehouseBtn" class="btn btn-primary rounded-pill shadow-sm btn-responsive-add">
                                    <i class="fas fa-plus-circle me-2"></i>Nuevo Almacén
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 py-4">
                <div class="card shadow-sm border-0 rounded-4 bg-body">
                    <div class="card-header p-4 bg-transparent border-bottom-0">
                        <h5 class="mb-1 fw-bold text-primary">Listado de Ubicaciones</h5>
                        <p class="mb-0 text-muted small">Administra tus puntos de almacenamiento y stock físico</p>
                    </div>

                    <div class="card-body p-4 pt-0">
                        <div class="table-responsive border rounded-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 text-muted fw-bold small text-uppercase" style="width:100px;"># ID</th>
                                        <th class="py-3 text-muted fw-bold small text-uppercase">Nombre del Almacén</th>
                                        <th class="py-3 px-4 text-center text-muted fw-bold small text-uppercase" style="width:120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="warehouses-tbody" class="border-top-0">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold text-body" id="mobileMenuLabel">
                        <i class="bi bi-building text-primary me-2"></i>Almacenes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body p-0 d-flex flex-column h-100">
                    <div class="list-group list-group-flush mt-2">
                        <?php
                        $menuToRender = $menuItems ?? [];
                        if (!empty($menuToRender)):
                            foreach ($menuToRender as $route => $item):
                                $isActiveParent = ($segment === $route);
                                $isSubActive = isset($item['submenu']) && array_key_exists($segment, $item['submenu']);
                                $itemIcon = htmlspecialchars($item['icon'] ?? 'circle');
                                $itemLabel = htmlspecialchars($item['label'] ?? $route);
                        ?>
                                <a href="<?= BASE_URL . $route ?>"
                                    class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center <?= ($isActiveParent || $isSubActive) ? 'bg-primary-subtle text-primary border-start border-4 border-primary fw-bold' : 'text-body' ?>">
                                    <i class="bi bi-<?= $itemIcon ?> me-3 fs-5"></i>
                                    <?= $itemLabel ?>
                                </a>

                                <?php if (isset($item['submenu'])): ?>
                                    <div class="bg-body-tertiary shadow-inner">
                                        <?php foreach ($item['submenu'] as $subRoute => $subItem):
                                            $isSubItemActive = ($segment === $subRoute);
                                            $subIcon = htmlspecialchars($subItem['icon'] ?? 'circle');
                                            $subLabel = htmlspecialchars($subItem['label'] ?? $subRoute);
                                        ?>
                                            <a href="<?= BASE_URL . $subRoute ?>"
                                                class="list-group-item list-group-item-action border-0 py-2 ps-5 d-flex align-items-center <?= $isSubItemActive ? 'text-primary fw-bold' : 'text-muted' ?>" style="font-size: 0.85rem;">
                                                <i class="bi bi-<?= $subIcon ?> me-3 fs-6"></i>
                                                <?= $subLabel ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php
                            endforeach;
                        else:
                            ?>
                            <div class="p-4 text-center text-muted">
                                <small>No se pudo cargar el menú dinámico.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto border-top p-4 text-center">
                        <small class="text-muted d-block mb-1">Usuario conectado:</small>
                        <span class="badge border px-3 py-2 rounded-pill shadow-sm text-body">
                            <i class="bi bi-person-circle text-primary me-2"></i><?= $username ?>
                        </span>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<?php
include_once __DIR__ . '/../partials/modals/modal_add_warehouse.php';
include_once __DIR__ . '/../partials/modals/modal_edit_warehouse.php';

$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<script src="<?= BASE_URL; ?>assets/js/ajax/warehouses.js"></script>

<script>
    const CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
    const CAN_DELETE = <?= $canDelete ? 'true' : 'false' ?>;
</script>
