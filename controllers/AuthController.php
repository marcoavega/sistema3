<?php
// controllers/AuthController.php
// Se importa el modelo `User`, que contiene la lógica para acceder a la base de datos de usuarios.
require_once __DIR__ . '/../models/User.php';

// Se importa la clase Logger que sirve para registrar acciones como login o logout de los usuarios.
require_once __DIR__ . '/Logger.php';  // 1. Importa el Logger

// Se define la clase `AuthController`, que maneja las acciones relacionadas con la autenticación del usuario.
class AuthController
{
    // Método que gestiona el inicio de sesión de los usuarios.
    public function login()
    {
        // Si la sesión no está iniciada, la inicia.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica si el formulario fue enviado mediante el método POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtiene el nombre de usuario desde el formulario, eliminando espacios en blanco al inicio y al final.
            $username = trim($_POST['username']);
            // Obtiene la contraseña tal como se ingresó (sin trim porque podría contener espacios válidos).
            $password = $_POST['password'];

            // Validación: el campo de nombre de usuario no puede estar vacío.
            if (empty($username)) {
                // Se guarda un mensaje de error en la sesión para mostrarlo en el formulario.
                $_SESSION['error'] = "El nombre de usuario es obligatorio";
                // Se registra el error en el archivo de logs del servidor.
                error_log("Error de sesión: " . $_SESSION['error']);
                // Se redirige al usuario nuevamente al formulario de login.
                header("Location: " . BASE_URL . "auth/login/");
                exit(); // Se detiene la ejecución del script.
            }

            // Validación: el campo de contraseña no puede estar vacío.
            if (empty($password)) {
                $_SESSION['error'] = "La contraseña es obligatoria";
                error_log("Error de sesión: " . $_SESSION['error']);
                header("Location: " . BASE_URL . "auth/login/");
                exit();
            }

            // Validación: la contraseña debe tener mínimo 6 caracteres.
            if (strlen($password) < 6) {
                $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres";
                error_log("Error de sesión: " . $_SESSION['error']);
                header("Location: " . BASE_URL . "auth/login/");
                exit();
            }

            // Se crea una instancia del modelo User para interactuar con la base de datos.
            $userModel = new User();
            // Se llama al método authenticate para verificar si el usuario y contraseña son válidos.
            $user = $userModel->authenticate($username, $password);

            // Si se encuentra un usuario válido
            if ($user) {
                // Se almacenan los datos relevantes del usuario en la sesión.
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'], // ID del usuario
                    'username' => $user['username'], // Nombre de usuario
                    'email' => $user['email'], // Correo electrónico
                    'level_user' => $user['level_user'], // Nivel o rol de usuario
                    'created_at' => $user['created_at'], // Fecha de creación del usuario
                    'updated_at' => $user['updated_at'], // Fecha de última modificación
                    'img_url'    => $user['img_url'], // URL de la imagen de perfil
                    'description_level' => $user['description_level'] // Descripción del rol
                ];

                // Se registra la acción de login del usuario en el sistema de bitácora.
                Logger::logAction($user['user_id'], 'login');

                // Se define un mensaje de bienvenida que será mostrado una vez redirigido.
                $_SESSION['flash'] = "Bienvenido, " . htmlspecialchars($user['username']);
                // Se redirige al panel principal del sistema.
                header("Location: " . BASE_URL . "dashboard");
                exit(); // Finaliza la ejecución para evitar continuar el script
            } else {
                // Si la autenticación falla, se guarda un mensaje de error en la sesión.
                $_SESSION['error'] = "Error en login, Usuario o Contraseña Incorrectos";
                // Se registra el error en el log del servidor.
                error_log("Error de sesión: " . $_SESSION['error']);
                // Se redirige nuevamente al login.
                header("Location: " . BASE_URL . "auth/login/");
                exit();
            }
        } else {
            // Si la solicitud no es POST, simplemente se muestra la vista del formulario de login.
            include __DIR__ . '/../views/pages/login.php';
        }
    }

    // Método que gestiona el cierre de sesión del usuario.
    public function logout()
    {
        // Se asegura de que la sesión esté iniciada.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si hay un usuario con sesión activa, se registra la acción de logout.
        if (isset($_SESSION['user']['user_id'])) {
            Logger::logAction($_SESSION['user']['user_id'], 'logout');
        }

        // Limpia todas las variables de sesión.
        session_unset();
        // Vuelve a asignar el array de sesión como vacío (doble seguridad).
        $_SESSION = [];

        // Verifica si se usan cookies para la sesión, y si es así, borra la cookie.
        if (ini_get("session.use_cookies")) {
            // Obtiene los parámetros actuales de la cookie de sesión.
            $params = session_get_cookie_params();
            // Establece la cookie con una fecha expirada para que el navegador la elimine.
            setcookie(
                session_name(), // Nombre de la cookie de sesión
                '',             // Valor vacío
                time() - 42000, // Expirada en el pasado
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruye completamente la sesión del usuario.
        session_destroy();

        // Redirige nuevamente al login.
        header("Location: " . BASE_URL . "auth/login/");
        exit();
    }
}
?>