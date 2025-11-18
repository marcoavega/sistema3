document.addEventListener('DOMContentLoaded', function () {
    // obtener user_id desde PHP inyectado en la página
    const USER_ID = parseInt(window.PROFILE_USER_ID || '0', 10) || 0;
    if (!USER_ID) return;
  
    // inicializar Tabulator
    const table = new Tabulator("#activity-table", {
      layout: "fitColumns",
      placeholder: "Cargando actividad...",
      pagination: "remote",
      paginationSize: 10,
      ajaxURL: BASE_URL + "api/logs.php",
      ajaxParams: { user_id: USER_ID, page: 1, size: 10 },
      ajaxConfig: "GET",
      paginationDataSent: { page: "page", size: "size" },
      paginationDataReceived: { last_page: "last_page", data: "data" },
      ajaxResponse: function(url, params, response) {
        // response.data ya contiene los logs
        return response.data || [];
      },
      columns: [
        { title: "Fecha / Hora", field: "timestamp", hozAlign: "left", formatter: function(cell){
            const v = cell.getValue();
            if (!v) return '';
            const d = new Date(v);
            if (isNaN(d.getTime())) return v;
            const day = String(d.getDate()).padStart(2,'0');
            const mo  = String(d.getMonth()+1).padStart(2,'0');
            const yr  = d.getFullYear();
            const hh  = String(d.getHours()).padStart(2,'0');
            const mm  = String(d.getMinutes()).padStart(2,'0');
            const ss  = String(d.getSeconds()).padStart(2,'0');
            return `${day}/${mo}/${yr} ${hh}:${mm}:${ss}`;
        }, headerSort:false, widthGrow:2 },
        { title: "Acción", field: "action", hozAlign: "left", headerSort:false, widthGrow:4 },
      ],
      tooltips: true,
      ajaxError: function (xhr, textStatus, errorThrown) {
        console.error("Error cargando logs:", textStatus, errorThrown, xhr.responseText);
      }
    });
  
    // función para recargar (por si quieres un botón)
    window.reloadProfileLogs = function () {
      table.setData(BASE_URL + "api/logs.php", { user_id: USER_ID, page: 1, size: 10 });
    };
  });
  