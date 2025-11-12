<?php
// Verifica si hay una sesión activa del usuario.
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "auth/login/");
    exit();
}

$user = $_SESSION['user'];

$username   = htmlspecialchars($user['username']);
$email      = htmlspecialchars($user['email']);
$level_user = htmlspecialchars($user['level_user']);
$level_name = $user['description_level'] ?? 'No disponible';
$created_at = isset($user['created_at']) ? htmlspecialchars($user['created_at']) : 'No disponible';
$updated_at = isset($user['updated_at']) ? htmlspecialchars($user['updated_at']) : 'No disponible';

$img = !empty($user['img_url'])
    ? BASE_URL . $user['img_url']
    : BASE_URL . "assets/images/users/user.png";

ob_start();
?>

<div class="container-fluid px-4 py-4 min-vh-100">
  <!-- Header -->
  <div class="bg-body shadow-sm border-bottom mb-4">
    <div class="d-flex justify-content-between align-items-center px-2 py-3">
      <div>
        <h4 class="fw-bold mb-0">Mi Perfil</h4>
        <small class="text-muted">Bienvenido, <?= $username ?></small>
      </div>
      <button class="btn btn-outline-primary" id="edit-profile-btn">
        <i class="bi bi-pencil me-2"></i>Editar Perfil
      </button>
    </div>
  </div>

  <!-- Perfil -->
  <div class="card shadow-sm mb-4 border-0 rounded-4">
    <div class="card-body py-4 px-5">
      <div class="row">
        <div class="col-md-4 text-center mb-3">
          <img src="<?= $img ?>" class="img-fluid rounded-circle shadow" style="max-width: 200px; object-fit: cover;" alt="Perfil">
        </div>
        <div class="col-md-8">
          <h5 class="fw-semibold mb-3">Información del Usuario</h5>
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Usuario:</strong> <?= $username ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?= $email ?></li>
            <li class="list-group-item"><strong>Nivel:</strong> <?= $level_name ?></li>
            <li class="list-group-item"><strong>Creado:</strong> <?= $created_at ?></li>
            <li class="list-group-item"><strong>Actualizado:</strong> <?= $updated_at ?></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Actividad reciente -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-body-secondary py-3">
      <h5 class="mb-0">Actividad Reciente</h5>
    </div>
    <div class="card-body">
      <div id="activity-table"></div>
    </div>
  </div>
</div>

<!-- Modal de edición -->
<?php include __DIR__ . '/../partials/modals/modal_edit_profile.php'; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>

<!-- JS de perfil -->
<script src="<?= BASE_URL ?>assets/js/ajax/profile.js"></script>

<!-- Tabulator para actividad reciente -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    new Tabulator("#activity-table", {
      layout: "fitColumns",
      placeholder: "No hay actividad reciente",
      data: [],
      columns: [
        { title: "Fecha", field: "date", sorter: "date", hozAlign: "center" },
        { title: "Acción", field: "action", hozAlign: "center" },
      ]
    });
  });
</script>
