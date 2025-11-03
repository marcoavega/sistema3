// Espera a que el contenido del DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // Obtiene el botón con ID "logoutButton" (cerrar sesión)
    var logoutButton = document.getElementById('logoutButton');
    
    // Verifica si existe el botón "logoutButton"
    if (logoutButton) {
        
        // Agrega un evento al hacer clic en el botón de cerrar sesión
        logoutButton.addEventListener('click', function(e) {
            
            // Previene la acción por defecto del botón (por ejemplo, seguir el enlace)
            e.preventDefault();
            
            // Crea una instancia del modal de Bootstrap con el ID "logoutConfirmModal"
            var logoutModal = new bootstrap.Modal(document.getElementById('logoutConfirmModal'));
            
            // Muestra el modal de confirmación de cierre de sesión
            logoutModal.show();
        });
    }

    // Obtiene el botón de confirmación dentro del modal (ID "confirmLogout")
    var confirmLogout = document.getElementById('confirmLogout');
    
    // Verifica si existe el botón de confirmación
    if (confirmLogout) {
        
        // Agrega un evento al hacer clic en el botón de confirmar cierre de sesión
        confirmLogout.addEventListener('click', function() {
            
            // Redirige al usuario a la URL de logout utilizando la constante BASE_URL
            window.location.href = BASE_URL + "auth/logout";
        });
    }
});
