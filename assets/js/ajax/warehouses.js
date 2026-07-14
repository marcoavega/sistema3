// assets/js/ajax/warehouses.js
document.addEventListener("DOMContentLoaded", function () {
  const tbody = document.getElementById("warehouses-tbody");
  const API_URL = `${BASE_URL}api/warehouses.php`;

  const addModalEl = document.getElementById("addWarehouseModal");
  const editModalEl = document.getElementById("editWarehouseModal");

  let addModal, editModal;
  if (addModalEl) addModal = new bootstrap.Modal(addModalEl);
  if (editModalEl) editModal = new bootstrap.Modal(editModalEl);

  // arreglo global con los datos cargados (para exportar)
  let currentWarehouses = [];

  // arreglo global con los datos filtrados (para exportar)
  let filteredWarehouses = [];

  // función para cargar los almacenes
  async function loadWarehouses() {
    try {
      const res = await fetch(`${API_URL}?action=list`);
      const j = await res.json();
      if (!j.success) throw new Error(j.message || "Error al listar");
      currentWarehouses = Array.isArray(j.data) ? j.data : [];
      filteredWarehouses = [...currentWarehouses];
      renderRows(filteredWarehouses);
    } catch (err) {
      console.error(err);
      showToast("Error al cargar almacenes", true);
    }
  }

  function renderRows(items) {
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!Array.isArray(items) || items.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="3" class="text-center text-muted p-5">No hay almacenes registrados</td></tr>';
      return;
    }

    items.forEach((row) => {
      const tr = document.createElement("tr");
      // Construimos botones según permisos: CAN_EDIT, CAN_DELETE pueden venir definidos en el template PHP
      let actionsHtml = "";

      if (typeof CAN_EDIT !== "undefined" ? CAN_EDIT : true) {
        actionsHtml += `
                    <button class="btn btn-soft-primary btn-sm rounded-3 btn-edit-warehouse"
                            data-id="${escapeAttr(row.id)}"
                            data-name="${escapeAttr(row.name)}"
                            title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                `;
      }

      if (typeof CAN_DELETE !== "undefined" ? CAN_DELETE : true) {
        actionsHtml += `
                    <button class="btn btn-soft-danger btn-sm rounded-3 btn-delete-warehouse"
                            data-id="${escapeAttr(row.id)}"
                            data-name="${escapeAttr(row.name)}"
                            title="Eliminar">
                        <i class="bi bi-trash3"></i>
                    </button>
                `;
      }

      tr.innerHTML = `
                <td class="px-4 fw-bold text-muted small">#${escapeHtml(
                  row.id
                )}</td>
                <td class="fw-semibold text-body">${escapeHtml(row.name)}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        ${
                          actionsHtml ||
                          '<span class="text-muted small">Sin acciones</span>'
                        }
                    </div>
                </td>
            `;
      tbody.appendChild(tr);
    });

    // Re-asignar eventos solo si existen botones
    if (typeof CAN_EDIT !== "undefined" ? CAN_EDIT : true) {
      document
        .querySelectorAll(".btn-edit-warehouse")
        .forEach((btn) => btn.addEventListener("click", onEditClick));
    }

    if (typeof CAN_DELETE !== "undefined" ? CAN_DELETE : true) {
      document
        .querySelectorAll(".btn-delete-warehouse")
        .forEach((btn) => btn.addEventListener("click", onDeleteClick));
    }
  }

  // Apertura de modal agregar
  const addBtn = document.getElementById("addWarehouseBtn");
  if (addBtn) {
    addBtn.addEventListener("click", () => {
      const form = document.getElementById("addWarehouseForm");
      if (form) form.reset();
      addModal.show();
    });
  }

  function onEditClick() {
    const id = this.dataset.id;
    const name = this.dataset.name;
    document.getElementById("edit-warehouse-id").value = id;
    document.getElementById("edit-warehouse-name").value = name;
    editModal.show();
  }

  // Guardar Nuevo
  const addForm = document.getElementById("addWarehouseForm");
  if (addForm) {
    addForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const nameInput = document.getElementById("new-warehouse-name");
      const btn = document.getElementById("saveNewWarehouseBtn");
      if (!nameInput.value.trim()) return;

      try {
        toggleLoading(btn, true);
        const fd = new FormData();
        fd.append("name", nameInput.value.trim());

        const res = await fetch(`${API_URL}?action=create`, {
          method: "POST",
          body: fd,
        });
        const j = await res.json();
        if (!j.success) throw new Error(j.message || "Error creación");

        addModal.hide();
        showToast("Almacén creado con éxito");
        loadWarehouses();
      } catch (err) {
        showToast(err.message, true);
      } finally {
        toggleLoading(btn, false);
      }
    });
  }

  // Guardar Cambios
  const editForm = document.getElementById("editWarehouseForm");
  if (editForm) {
    editForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const id = document.getElementById("edit-warehouse-id").value;
      const name = document.getElementById("edit-warehouse-name").value.trim();
      const btn = document.getElementById("saveChangesWarehouseBtn");

      try {
        toggleLoading(btn, true);
        const fd = new FormData();
        fd.append("id", id);
        fd.append("name", name);

        const res = await fetch(`${API_URL}?action=update`, {
          method: "POST",
          body: fd,
        });
        const j = await res.json();
        if (!j.success) throw new Error(j.message || "Error actualización");

        editModal.hide();
        showToast("Actualizado correctamente");
        loadWarehouses();
      } catch (err) {
        showToast(err.message, true);
      } finally {
        toggleLoading(btn, false);
      }
    });
  }

  // Borrar
  function onDeleteClick() {
    const id = this.dataset.id;
    const name = this.dataset.name;

    Swal.fire({
      title: "¿Eliminar almacén?",
      html: `¿Estás seguro de eliminar <b>${name}</b>?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
      customClass: {
        confirmButton: "btn btn-danger me-2",
        cancelButton: "btn btn-light",
      },
      buttonsStyling: false,
    }).then(async (result) => {
      if (result.isConfirmed) {
        try {
          const fd = new FormData();
          fd.append("id", id);
          const res = await fetch(`${API_URL}?action=delete`, {
            method: "POST",
            body: fd,
          });
          const j = await res.json();
          if (!j.success) throw new Error(j.message || "Error borrado");
          showToast("Eliminado correctamente");
          loadWarehouses();
        } catch (err) {
          Swal.fire("Error", err.message, "error");
        }
      }
    });
  }

  // Utilitarios
  function toggleLoading(btn, loading) {
    if (!btn) return;
    const icon = btn.querySelector("i");
    if (loading) {
      btn.disabled = true;
      if (icon) {
        icon.dataset.oldClass = icon.className;
        icon.className = "fas fa-spinner fa-spin me-2";
      }
    } else {
      btn.disabled = false;
      if (icon) icon.className = icon.dataset.oldClass || "fas fa-save me-2";
    }
  }

  function showToast(msg, isError = false) {
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: isError ? "error" : "success",
      title: msg,
      showConfirmButton: false,
      timer: 2000,
    });
  }

  function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str ?? "";
    return div.innerHTML;
  }

  function escapeAttr(str) {
    return String(str ?? "")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  // ============================
  // EXPORTACIONES (CSV / Excel / PDF)
  // ============================
  // Usa currentWarehouses (rellenado por loadWarehouses)

  function formatDateForExport(dateStr) {
    if (!dateStr) return "";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
  }

  function downloadBlob(content, mime, filename) {
    const blob = new Blob([content], { type: mime });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
  }

  function exportCSV() {
    if (!currentWarehouses || !currentWarehouses.length) {
      return showToast("No hay datos para exportar", true);
    }
    const headers = ["ID", "Nombre", "Creado", "Actualizado"];
    let csv = headers.join(",") + "\n";
    currentWarehouses.forEach((r) => {
      const row = [
        r.id ?? "",
        `"${(r.name ?? "").replace(/"/g, '""')}"`,
        formatDateForExport(r.created_at),
        formatDateForExport(r.updated_at),
      ];
      csv += row.join(",") + "\n";
    });
    downloadBlob(csv, "text/csv;charset=utf-8;", "almacenes.csv");
  }

  async function exportExcel() {
    if (!currentWarehouses || !currentWarehouses.length) {
      return showToast("No hay datos para exportar", true);
    }
    if (window.XLSX) {
      const rows = currentWarehouses.map((r) => ({
        ID: r.id ?? "",
        Nombre: r.name ?? "",
        Creado: formatDateForExport(r.created_at),
        Actualizado: formatDateForExport(r.updated_at),
      }));
      const ws = window.XLSX.utils.json_to_sheet(rows);
      const wb = window.XLSX.utils.book_new();
      window.XLSX.utils.book_append_sheet(wb, ws, "Almacenes");
      window.XLSX.writeFile(wb, "almacenes.xlsx");
    } else {
      // fallback: crear CSV si no hay SheetJS
      showToast("SheetJS no detectado. Se descargará CSV en su lugar.", true);
      setTimeout(exportCSV, 600);
    }
  }

  function exportPDF() {
    if (!currentWarehouses || !currentWarehouses.length) {
      return showToast("No hay datos para exportar", true);
    }

    const jsPDFlib =
      window.jspdf && window.jspdf.jsPDF
        ? window.jspdf.jsPDF
        : window.jsPDF
        ? window.jsPDF
        : null;
    if (!jsPDFlib) {
      showToast("jsPDF no está disponible. Instala jsPDF + autotable.", true);
      return;
    }

    const doc = new jsPDFlib("p", "pt", "a4");

    // título
    const pageWidth = doc.internal.pageSize.getWidth();
    doc.setFontSize(16);
    doc.text("REPORTE DE ALMACENES", pageWidth / 2, 40, { align: "center" });
    doc.setFontSize(10);
    doc.text("Generado: " + new Date().toLocaleString(), 40, 58);

    // preparar head y body para autoTable
    const head = [["ID", "Nombre", "Creado", "Actualizado"]];
    const body = currentWarehouses.map((r) => [
      r.id ?? "",
      r.name ?? "",
      formatDateForExport(r.created_at),
      formatDateForExport(r.updated_at),
    ]);

    if (typeof doc.autoTable !== "function") {
      // Si autoTable no está registrado (plugin), avisar
      showToast(
        "autoTable no registrado en jsPDF. No se puede generar PDF.",
        true
      );
      return;
    }

    doc.autoTable({
      head: head,
      body: body,
      startY: 80,
      styles: { fontSize: 9, cellPadding: 3 },
      headStyles: { fillColor: [22, 160, 133], textColor: 255 },
      theme: "striped",
    });

    doc.save("almacenes.pdf");
  }

  // Conectar botones (mismos IDs que en products)
  const exportCSVBtn = document.getElementById("exportCSVBtn");
  if (exportCSVBtn) exportCSVBtn.addEventListener("click", exportCSV);

  const exportExcelBtn = document.getElementById("exportExcelBtn");
  if (exportExcelBtn) exportExcelBtn.addEventListener("click", exportExcel);

  const exportPDFBtn = document.getElementById("exportPDFBtn");
  if (exportPDFBtn) exportPDFBtn.addEventListener("click", exportPDF);

// ============================
// BUSCADOR (igual que products)
// ============================
const searchInput = document.getElementById('table-search');

if (searchInput) {
    searchInput.addEventListener('input', function () {
        const term = this.value.toLowerCase().trim();

        if (!term) {
            filteredWarehouses = [...currentWarehouses];
        } else {
            filteredWarehouses = currentWarehouses.filter(w =>
                String(w.id).includes(term) ||
                (w.name && w.name.toLowerCase().includes(term))
            );
        }

        renderRows(filteredWarehouses);
    });
}


  // carga inicial
  loadWarehouses();
});
