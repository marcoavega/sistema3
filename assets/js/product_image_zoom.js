document.addEventListener("DOMContentLoaded", () => {
    // Selector de imágenes que abrirán el modal: puedes usar #productImage u otras con clase 'zoomable'
    const zoomableSelector = ".zoomable, #productImage";
    const previewModalEl = document.getElementById("imagePreviewModal");
    const previewImg = document.getElementById("imagePreviewModalImg");

    if (!previewModalEl || !previewImg) return;

    const modalInstance = new bootstrap.Modal(previewModalEl, {
      keyboard: true,
      backdrop: true
    });

    // Añadimos listener a todas las imágenes existentes que encajen con el selector
    function attachZoomListeners() {
      const imgs = document.querySelectorAll(zoomableSelector);
      imgs.forEach(img => {
        // evitar múltiples listeners
        if (img.dataset.zoomAttached) return;
        img.dataset.zoomAttached = "1";

        img.addEventListener("click", (e) => {
          const src = img.getAttribute("src") || img.dataset.src;
          if (!src) return;
          // usar cache-buster para forzar recarga si se actualizó la imagen
          const cacheBusted = src + (src.includes('?') ? '&' : '?') + 'v=' + Date.now();
          previewImg.src = cacheBusted;
          previewImg.alt = img.alt || 'Imagen del producto';
          modalInstance.show();
        });
      });
    }

    attachZoomListeners();

    // Si cargas/actualizas la imagen dinámicamente y quieres re-attach (por ejemplo tras editar),
    // llama a attachZoomListeners() de nuevo desde donde actualizas la imagen.
  });