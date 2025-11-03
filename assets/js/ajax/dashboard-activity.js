// assets/js/ajax/dashboard-activity.js

// Espera a que el documento HTML haya cargado completamente
document.addEventListener('DOMContentLoaded', () => {

  // ---------------- TABULATOR: CREACIÓN DE LA TABLA ----------------

  // 2) Crear la instancia de Tabulator
  const activityTable = new Tabulator("#recent-activity-table", {

    // Define que las columnas se ajusten automáticamente al espacio disponible
    layout: "fitColumns",

    // Mensaje que se muestra mientras se cargan los datos
    placeholder: "Cargando actividad reciente…",

    // Habilita paginación remota (los datos se piden al servidor)
    pagination: "remote",

    // Cantidad de registros por página por defecto
    paginationSize: 20,

    // Opciones de cantidad de registros por página
    paginationSizeSelector: [10, 20, 50, 100],

    // Cantidad de botones de paginación visibles
    paginationButtonCount: 5,

    // URL de la API que retorna los datos en formato JSON
    ajaxURL: BASE_URL + "api/logs.php",

    // Tipo de petición HTTP (en este caso GET)
    ajaxConfig: "GET",

    // Define cómo se envían los parámetros de paginación al servidor
    paginationDataSent: { page: "page", size: "size" },

    // Define cómo se reciben los datos de paginación del servidor
    paginationDataReceived: { last_page: "last_page", data: "data" },

    // Antes de enviar la petición, se baja la opacidad de la tabla para dar efecto de carga
    ajaxRequesting: () => {
      document.querySelector("#recent-activity-table").style.opacity = "0.6";
    },

    // Cuando se recibe la respuesta, se vuelve a la opacidad normal
    // y se retorna solo el array de datos si es válido
    ajaxResponse: (_url, _params, response) => {
      document.querySelector("#recent-activity-table").style.opacity = "1";
      return Array.isArray(response.data) ? response.data : [];
    },

    // ---------------- DEFINICIÓN DE COLUMNAS DE LA TABLA ----------------

    columns: [
      {
        // Título de la columna
        title: "Fecha",

        // Campo del JSON que se va a usar
        field: "timestamp",

        // Ordenador de tipo fecha/hora
        sorter: "datetime",

        // Alineación horizontal centrada
        hozAlign: "center",

        // Formateador personalizado para mostrar la fecha en formato local
        formatter: cell => {
          const d = new Date(cell.getValue());
          return isNaN(d) ? cell.getValue() : d.toLocaleString("es-ES");
        },

        // Tamaño proporcional de columna (1 unidad de crecimiento)
        widthGrow: 1,
      },

      {
        title: "Usuario",
        field: "username",
        hozAlign: "center",
        widthGrow: 1
      },

      {
        title: "Acción",
        field: "action",
        hozAlign: "left",
        widthGrow: 1
      },
    ],

    // ---------------- OPCIONES GENERALES DE LA TABLA ----------------

    // Habilita ordenamiento al hacer clic en los encabezados
    headerSort: true,

    // Permite ordenamiento de 3 estados (asc, desc, ninguno)
    headerSortTristate: true,

    // Desactiva mover columnas
    movableColumns: false,

    // Habilita redimensionamiento manual de columnas
    resizableColumns: true,

    // Habilita tooltips al pasar el cursor sobre celdas
    tooltips: true,
  });

  // ---------------- BOTONES DE EXPORTACIÓN ----------------

  // Exportar en formato CSV
  document.getElementById("exportCSVBtn").addEventListener("click", () => {
    activityTable.download("csv", "actividad_reciente.csv");
  });

  // Exportar en formato Excel (.xlsx)
  document.getElementById("exportExcelBtn").addEventListener("click", () => {
    activityTable.download("xlsx", "actividad_reciente.xlsx", {
      sheetName: "Actividad"
    });
  });

  // Exportar en formato PDF
  document.getElementById("exportPDFBtn").addEventListener("click", () => {
    activityTable.download("pdf", "actividad_reciente.pdf", {
      orientation: "portrait",
      title: "Actividad Reciente"
    });
  });

  // Exportar en formato JSON
  document.getElementById("exportJSONBtn").addEventListener("click", () => {
    activityTable.download("json", "actividad_reciente.json");
  });

  // ---------------- FUNCIÓN DEBILITADA (DEBOUNCE) PARA BUSCAR ----------------

  // Función para retrasar la ejecución de otra función hasta después de cierto tiempo
  function debounce(fn, delay) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout); // Reinicia el temporizador anterior
      timeout = setTimeout(() => fn.apply(this, args), delay); // Ejecuta después del retraso
    };
  }

  // ---------------- BÚSQUEDA EN TIEMPO REAL ----------------

  // Obtiene el input de búsqueda
  const input = document.getElementById("table-search");

  // Si no se encuentra el input, muestra error y detiene el script
  if (!input) {
    console.error("No encontré el input #table-search");
    return;
  }

  // Cuando el usuario escribe en el input
  input.addEventListener("keyup", debounce(function () {

    // Obtiene el valor ingresado
    const term = this.value.trim();

    // Cambia a la página 1
    activityTable.setPage(1).then(() => {

      if (term === "") {
        // Si no hay texto, recarga sin filtros
        activityTable.setData();
      } else {
        // Si hay texto, recarga con parámetro de búsqueda
        activityTable.setData(undefined, { search: term });
      }

    });

  }, 300)); // Espera 300ms después de dejar de escribir

});
