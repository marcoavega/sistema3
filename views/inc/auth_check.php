<?php
// views/inc/auth_check.php

// --- 1. Iniciar sesión si no está iniciada ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 2. Verificar si el usuario está autenticado ---
if (!isset($_SESSION['user'])) {

    // Si la petición es AJAX / JSON → devolver 401
    $isAjax = (
        (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
        strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false
    );

    if ($isAjax) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit();
    }

    // Si es una vista → redirección normal
    header("Location: " . BASE_URL . "auth/login/");
    exit();
}
