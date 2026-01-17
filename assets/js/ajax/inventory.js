// assets/js/ajax/inventory.js
document.addEventListener('DOMContentLoaded', function () {
    // Si la variable global CAN_VIEW_INVENTORY no existe, asumimos false
    const canView = (typeof CAN_VIEW_INVENTORY !== 'undefined') ? Boolean(CAN_VIEW_INVENTORY) : false;
  
    // Elementos
    const elTotal = document.getElementById('totalProducts');
    const elInStock = document.getElementById('inStock');
    const elLowStock = document.getElementById('lowStock');
    const elTotalValue = document.getElementById('totalValue');
  
    // Función para mostrar placeholders (cuando NO tiene permiso)
    function hideStats() {
      if (elTotal) elTotal.textContent = '—';
      if (elInStock) elInStock.textContent = '—';
      if (elLowStock) elLowStock.textContent = '—';
      if (elTotalValue) elTotalValue.textContent = '—';
    }
  
    // Si no tiene permiso, no hacemos fetch y mostramos placeholders
    if (!canView) {
      hideStats();
      return;
    }
  
    const API_STATS = (typeof BASE_URL !== 'undefined' ? BASE_URL : '/') + 'api/inventory.php?action=stats';
  
    async function loadStats() {
      try {
        const res = await fetch(API_STATS, { credentials: 'same-origin' });
        // Si el servidor devuelve 403 (no autorizado) o error HTTP, mostramos placeholders
        if (!res.ok) {
          console.warn('inventory stats http error', res.status);
          hideStats();
          return;
        }
        const j = await res.json();
        if (!j.success) {
          console.warn('inventory stats:', j.message || 'no success');
          hideStats();
          return;
        }
        const data = j.data || {};
  
        if (elTotal) elTotal.textContent = (data.total !== undefined) ? data.total : '—';
        if (elInStock) elInStock.textContent = (data.inStock !== undefined) ? data.inStock : '—';
        if (elLowStock) elLowStock.textContent = (data.lowStock !== undefined) ? data.lowStock : '—';
        if (elTotalValue) {
          const tv = Number(data.totalValue ?? 0);
          elTotalValue.textContent = `$${tv.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
      } catch (err) {
        console.error('Error cargando estadísticas de inventario:', err);
        hideStats();
      }
    }
  
    loadStats();
  });
  