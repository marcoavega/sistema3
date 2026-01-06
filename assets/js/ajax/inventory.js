/*assets/js/ajax/inventory.js*/
/*
 |-------------------------------------------------------
 | Cargar estadísticas vía AJAX
 |-------------------------------------------------------
 | Al cargar la página, se hace una petición fetch
 | a la API para obtener estadísticas del inventario.
 */
document.addEventListener('DOMContentLoaded', function () {
    fetch(BASE_URL + "api/products.php?action=stats")
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalProducts').textContent = data.total;
                document.getElementById('inStock').textContent = data.inStock;
                document.getElementById('lowStock').textContent = data.lowStock;
                document.getElementById('totalValue').textContent = `$${data.totalValue}`;
            }
        })
        .catch(err => console.error("Error stats:", err));
});
