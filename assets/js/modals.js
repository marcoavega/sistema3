// ==================================================================
// Archivo: modal.js
// Propósito: Código JavaScript que se ejecuta cuando la página se carga.
// Se encarga de mostrar un modal de error utilizando Bootstrap si hay
// un mensaje de error presente en el HTML.
// ==================================================================

document.addEventListener('DOMContentLoaded', function() {
    // ------------------------------------------------------------------
    // Espera a que todo el contenido del DOM esté completamente cargado
    // antes de ejecutar el código dentro de esta función.
    // Esto garantiza que los elementos HTML estén disponibles.
    // ------------------------------------------------------------------

    const errorModalElement = document.getElementById('messageModal');
    // ------------------------------------------------------------------
    // Busca en el DOM el elemento con ID "messageModal".
    // Este debe ser el modal de Bootstrap que se usará para mostrar errores.
    // Si no existe, no se ejecuta nada más.
    // ------------------------------------------------------------------

    if (errorModalElement) {
        const modalBody = errorModalElement.querySelector('.modal-body');
        // --------------------------------------------------------------
        // Dentro del modal, busca específicamente el contenido del cuerpo
        // (clase .modal-body) donde estará el texto del mensaje de error.
        // --------------------------------------------------------------

        const errorMessage = modalBody.textContent.trim();
        // --------------------------------------------------------------
        // Obtiene el texto del mensaje y elimina espacios en blanco al inicio y final.
        // Esto sirve para validar si realmente hay contenido o está vacío.
        // --------------------------------------------------------------

        if (errorMessage) {
            const errorModal = new bootstrap.Modal(errorModalElement);
            // ----------------------------------------------------------
            // Crea una nueva instancia del modal utilizando Bootstrap 5,
            // pasándole el elemento del modal encontrado anteriormente.
            // ----------------------------------------------------------

            errorModal.show();
            // ----------------------------------------------------------
            // Muestra el modal en pantalla de forma automática.
            // ----------------------------------------------------------

            errorModalElement.addEventListener('hidden.bs.modal', function() {
                modalBody.textContent = '';
            });
            // ----------------------------------------------------------
            // Cuando el modal se cierra (evento de Bootstrap `hidden.bs.modal`),
            // el contenido del mensaje se borra para evitar que se muestre
            // el mismo mensaje otra vez por error.
            // ----------------------------------------------------------
        }
    }
});
