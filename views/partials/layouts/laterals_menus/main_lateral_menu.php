<?php
// main_lateral_menu.php
// Este archivo asume que las variables siguientes están disponibles:
// $menuItems (array), $segment (string), $sidebarTitle (string), $sidebarIcon (string)

$safeTitle = htmlspecialchars($sidebarTitle ?? '');
$safeIcon  = htmlspecialchars($sidebarIcon ?? 'speedometer2');
?>

<!-- NAV LATERAL -->
<nav class="col-md-2 d-none d-md-block sidebar min-vh-100">
  <div class="pt-4 px-3">
    <div class="text-center mb-4">
      <div class="rounded-circle d-inline-flex align-items-center justify-content-center dashboard-nav-styles" style="width:60px;height:60px;">
        <i class="bi bi-<?= $safeIcon ?> text-primary fs-3"></i>
      </div>
      <h6 class="mt-2 mb-0"><?= $safeTitle ?></h6>
    </div>

    <ul class="nav flex-column">
      <?php foreach ($menuItems as $route => $item): ?>

        <?php
          $isActiveParent = ($segment === $route);
          $isSubActive = isset($item['submenu']) && array_key_exists($segment, $item['submenu']);
          $itemIcon = htmlspecialchars($item['icon'] ?? 'circle');
          $itemLabel = htmlspecialchars($item['label'] ?? $route);
        ?>

        <?php if (!isset($item['submenu'])): ?>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'bg-primary text-white fw-bold' : 'text-body' ?>"
               href="<?= BASE_URL . $route ?>">
              <i class="bi bi-<?= $itemIcon ?> me-3 fs-5"></i>
              <span class="fw-medium"><?= $itemLabel ?></span>
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center px-3 py-2 rounded-3 <?= ($isActiveParent || $isSubActive) ? 'bg-primary text-white fw-bold' : 'text-body' ?>"
               href="<?= BASE_URL . $route ?>">
              <i class="bi bi-<?= $itemIcon ?> me-3 fs-5"></i>
              <span class="fw-medium"><?= $itemLabel ?></span>
            </a>

            <ul class="nav flex-column ms-4 mt-1" style="<?= ($isActiveParent || $isSubActive) ? '' : 'display:none;' ?>">
              <?php foreach ($item['submenu'] as $subRoute => $subItem): ?>
                <?php
                  $subIcon = htmlspecialchars($subItem['icon'] ?? 'circle');
                  $subLabel = htmlspecialchars($subItem['label'] ?? $subRoute);
                ?>
                <li class="nav-item mb-1">
                  <a class="nav-link d-flex align-items-center px-3 py-1 rounded-3 <?= $segment === $subRoute ? 'bg-primary text-white fw-bold' : 'text-body' ?>"
                     href="<?= BASE_URL . $subRoute ?>">
                    <i class="bi bi-<?= $subIcon ?> me-3 fs-6"></i>
                    <span><?= $subLabel ?></span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>

      <?php endforeach; ?>
    </ul>
  </div>
</nav>
<!-- FIN NAV LATERAL -->
