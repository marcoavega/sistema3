<?php
// Se incluye el archivo que contiene los metadatos, enlaces a hojas de estilo y scripts generales del sistema.
include __DIR__ . '/../partials/head.php';

// Se incluye el botón flotante que permite al usuario cambiar entre modo claro y modo oscuro.
include __DIR__ . '/../partials/button-theme.php';
?>

<!-- Contenedor principal que ocupa toda la altura de la pantalla -->
<div class="d-flex flex-column style-first-div-login">
    <!--
        d-flex: convierte el contenedor en un flexbox.
        flex-column: alinea los hijos en forma de columna (vertical).
        style-first-div-login: clase personalizada para estilos específicos (como fondo, altura, padding).
    -->

    <!-- Contenedor del formulario de login, centrado en la pantalla -->
    <div class="container-fluid flex-grow-1 d-flex align-items-center justify-content-center">
        <!--
            container-fluid: ocupa el 100% del ancho disponible.
            flex-grow-1: expande el contenedor verticalmente para que llene todo el espacio restante.
            align-items-center: centra verticalmente el contenido dentro del contenedor.
            justify-content-center: centra horizontalmente el contenido.
        -->

        <div class="card shadow-lg style-third-div-login">
            <!--
                card: componente visual de Bootstrap con borde y padding.
                shadow-lg: aplica una sombra grande para resaltar la tarjeta.
                style-third-div-login: clase personalizada para aplicar estilos como el ancho y márgenes.
            -->

            <div class="card-body text-center">
                <!-- Contenedor interno de la tarjeta, con el contenido centrado -->

                <!-- Logo de la empresa -->
                <img src="<?php echo BASE_URL; ?>assets/images/logo/logo_empresa.png"
                    alt="Logo de la Empresa"
                    class="img-fluid rounded-circle mb-3 img-login">
                <!--
                    src: ruta dinámica al logo utilizando la constante BASE_URL.
                    img-fluid: imagen responsiva que se adapta al ancho de su contenedor.
                    rounded-circle: imagen con borde circular.
                    mb-3: margen inferior para separación con el título.
                    img-login: clase personalizada que puede definir altura o ajuste de imagen.
                -->

                <!-- Título del formulario -->
                <h2 class="card-title text-center mb-4">Iniciar Sesión.</h2>
                <!--
                    card-title: estilo específico para títulos dentro de tarjetas.
                    text-center: texto centrado.
                    mb-4: margen inferior para separar el título del formulario.
                -->

                <!-- Formulario de inicio de sesión -->
                <form action="<?php echo BASE_URL; ?>auth/login" method="POST">
                    <!--
                        action: define a dónde se enviarán los datos del formulario (ruta al controlador de login).
                        method="POST": indica que los datos se enviarán de forma segura.
                    -->

                    <!-- Campo para el nombre de usuario -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <!-- Etiqueta que describe el campo -->

                        <div class="input-group">
                            <!-- input-group: agrupa íconos y campos para una mejor interfaz visual -->
                            <span class="input-group-text" id="username-addon">
                                <i class="bi bi-person"></i>
                            </span>
                            <!--
                                ícono de usuario (Bootstrap Icons).
                                input-group-text: clase para el fondo y estilo del ícono.
                            -->

                            <input type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                placeholder="Tu nombre de usuario"
                                aria-describedby="username-addon"
                                autocomplete="username">
                            <!--
                                type="text": campo de texto.
                                form-control: clase Bootstrap para estilos de formularios.
                                id y name: identificadores del campo para el formulario y el backend.
                                placeholder: texto guía que aparece en el campo.
                                autocomplete="username": ayuda a los navegadores a sugerir el nombre guardado.
                            -->
                        </div>
                    </div>

                    <!-- Campo para la contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>

                        <div class="input-group">
                            <span class="input-group-text" id="password-addon">
                                <i class="bi bi-lock"></i>
                            </span>
                            <!-- Ícono de candado para representar la contraseña -->

                            <input type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Tu contraseña">
                            <!--
                                type="password": oculta el texto escrito (caracteres reemplazados por puntos).
                                form-control: aplica estilos.
                            -->
                        </div>
                    </div>

                    <!-- Botón para enviar el formulario -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info">Ingresar</button>
                        <!--
                            d-grid: hace que el botón se expanda al ancho del contenedor.
                            btn btn-info: botón con estilo azul (informativo).
                        -->
                    </div>

                    <!-- Línea divisoria opcional (puede usarse para mostrar enlaces como "¿Olvidaste tu contraseña?") -->
                    <hr>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Se incluye un archivo con modales relacionados al login (por ejemplo, errores como "usuario no encontrado").
include __DIR__ . '/../partials/modals/modals-login.php';
?>
