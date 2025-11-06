<?php
// Archivo: views/pages/admin_users.php

if (!isset($_SESSION['user'])){
  header("Location: " . BASE_URL . "auth/login/");
  exit();
}

$uri = $_GET['url'] ?? 'admin_users';
$segment =explode('/', trim($uri, '/'))[0];

ob_start();

require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

$stmt = $pdo->query("SELECT id_level_user, description_level FROM levels_users ORDER BY level");
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

$username = htmlspecialchars($_SESSION['user']['username']);

require_once __DIR__ . '/../partials/layouts/lateral_menu_users.php';
?>