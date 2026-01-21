// assets/js/ajax/dashboard-activity.js

document.addEventListener('DOMContentLoaded', () => {
  // 1) Inyectar estilos idénticos a la tabla de productos
  const style = document.createElement("style");
  style.textContent = `
    .tabulator .tabulator-col,
    .tabulator .tabulator-cell { white-space: nowrap !important; }
    .tabulator {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      width: 100%;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    .tabulator-table {
      min-width: 600px;
      touch-action: pan-x;
      width: 100% !important;
    }
    .tabulator-header {
      background: linear-gradient(135deg,#f8f9fa,#e9ecef);
      border-bottom: 2px solid #dee2e6;
    }
   .tabulator-row:hover { background-color: rgba(255, 255, 255, 0.08) !important; }


    @media (max-width: 767px) {
      .tabulator::-webkit-scrollbar { height: 12px; }
      .tabulator::-webkit-scrollbar-thumb { background: #007bff; }
      .tabulator::after {
        content: "← Desliza para ver más columnas →";
        position: absolute; bottom: -25px; left: 50%;
        transform: translateX(-50%); font-size: 12px;
        color: #007bff; font-weight: 500;
      }
      .tabulator-cell { padding: 8px 6px !important; font-size: 13px; }
      .tabulator-col  { padding: 10px 6px !important; font-size: 12px; font-weight: 600; }
    }
    @media (min-width: 768px) {
      .tabulator::-webkit-scrollbar { height: 8px; }
    }
  `;
  document.head.appendChild(style);

  // 2) Crear la instancia de Tabulator
  const activityTable = new Tabulator("#recent-activity-table", {
    layout:               "fitColumns",
    placeholder:          "Cargando actividad reciente…",
    pagination:           "remote",
    paginationSize:       20,
    paginationSizeSelector: [10,20,50,100],
    paginationButtonCount: 5,

    ajaxURL:    BASE_URL + "api/logs.php",
    ajaxConfig: "GET",

    // enviamos siempre page+size, y luego añadiremos search dinámicamente
    paginationDataSent:     { page:"page", size:"size" },
    paginationDataReceived: { last_page:"last_page", data:"data" },

    ajaxRequesting: () => {
      document.querySelector("#recent-activity-table").style.opacity = "0.6";
    },
    ajaxResponse: (_url,_params,response) => {
      document.querySelector("#recent-activity-table").style.opacity = "1";
      return Array.isArray(response.data) ? response.data : [];
    },

    columns: [
      {
        title:     "Fecha",
        field:     "timestamp",
        sorter:    "datetime",
        hozAlign:  "center",
        formatter: cell => {
          const d = new Date(cell.getValue());
          return isNaN(d) ? cell.getValue() : d.toLocaleString("es-ES");
        },
        widthGrow: 1,
      },
      { title:"Usuario", field:"username", hozAlign:"center", widthGrow:1 },
      { title:"Acción",  field:"action",   hozAlign:"left",   widthGrow:1 },
    ],

    headerSort:         true,
    headerSortTristate: true,
    movableColumns:     false,
    resizableColumns:   true,
    tooltips:           true,
  });

  // 3) Exportar
  document.getElementById("exportCSVBtn").addEventListener("click", () => {
    activityTable.download("csv",  "actividad_reciente.csv");
  });
  document.getElementById("exportExcelBtn").addEventListener("click", () => {
    activityTable.download("xlsx", "actividad_reciente.xlsx", {
      sheetName: "Actividad"
    });
  });
  document.getElementById("exportPDFBtn").addEventListener("click", () => {
    activityTable.download("pdf", "actividad_reciente.pdf", {
      orientation: "landscape", // <--- CAMBIADO A HORIZONTAL
      title: "Actividad Reciente",
      autoTable: {
        theme: 'grid',
        headStyles: { fillColor: [0, 123, 255] } // Opcional: azul para el encabezado
      }
    });
  });
  document.getElementById("exportJSONBtn").addEventListener("click", () => {
    activityTable.download("json", "actividad_reciente.json");
  });

  // 4) Debounce helper
  function debounce(fn, delay) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // 5) Buscador: recarga la tabla en página 1 con ?search=…
  const input = document.getElementById("table-search");
  if (!input) {
    console.error("No encontré el input #table-search");
    return;
  }
  input.addEventListener("keyup", debounce(function() {
    const term = this.value.trim();
    // volver a la página 1 antes de recarga
    activityTable.setPage(1).then(() => {
      if (term === "") {
        // sin filtro, simplemente recarga
        activityTable.setData();
      } else {
        // recarga con search
        activityTable.setData(undefined, { search: term });
      }
    });
  }, 300));
});
