// users-table.js

// Este evento se dispara cuando el DOM ha sido completamente cargado.
document.addEventListener("DOMContentLoaded", function () {

  // Función reutilizable para cerrar modal y reenfocar
  function cerrarModalYReenfocar(modalId, focusTargetId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    if (document.activeElement instanceof HTMLElement) {
      document.activeElement.blur();
    }
    const modalInst = bootstrap.Modal.getInstance(modalEl);
    if (modalInst) {
      modalInst.hide();
    }
    if (focusTargetId) {
      setTimeout(() => {
        document.getElementById(focusTargetId)?.focus();
      }, 300);
    }
  }

  const usersTableElement = document.getElementById("users-table");
  if (!usersTableElement) return;

  let deleteUserID = null;

  const table = new Tabulator("#users-table", {
    index: "user_id",
    ajaxURL: BASE_URL + "api/users.php?action=get",
    ajaxConfig: "GET",
    layout: "fitColumns",
    responsiveLayout: "collapse",
    placeholder: "Cargando usuarios...",
    columns: [
    { title: "ID",         field: "user_id",          widthGrow: 1, sorter: "number" },
    { title: "Usuario",    field: "username",         widthGrow: 1 },
    { title: "Email",      field: "email",            widthGrow: 1 },
    { title: "Nivel",      field: "description_level",widthGrow: 1, hozAlign: "center" },
    {
      title: "Creado",
      field: "created_at",
      widthGrow: 1,
      formatter: cell => {
        const d = new Date(cell.getValue());
        return isNaN(d) ? "" : `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
      },
    },
    {
      title: "Actualizado",
      field: "updated_at",
      widthGrow: 1,
      formatter: cell => {
        const d = new Date(cell.getValue());
        return isNaN(d) ? "" : `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
      },
    },
    {
      title: "Acciones",
      widthGrow: 1,
      hozAlign: "center",
      formatter: () => `
        <div class='btn-group'>
          <button class='btn btn-sm btn-outline-primary edit-btn me-1'>
            <i class="bi bi-pencil-square"></i>
          </button>
          <button class='btn btn-sm btn-outline-danger delete-btn'>
            <i class="bi bi-trash3"></i>
          </button>
        </div>`,
      cellClick: (e, cell) => {
        const rowData = cell.getRow().getData();
        if (e.target.closest(".edit-btn")) {
          document.getElementById("edit-user-id").value = rowData.user_id;
          document.getElementById("edit-username").value = rowData.username;
          document.getElementById("edit-email").value    = rowData.email;
          document.getElementById("edit-level").value    = rowData.level_user;
          new bootstrap.Modal(document.getElementById("editUserModal")).show();
        }
        if (e.target.closest(".delete-btn")) {
          deleteUserID = rowData.user_id;
          new bootstrap.Modal(document.getElementById("deleteUserModal")).show();
        }
      },
    },
  ],
  });

  // busqueda en la tabla
  const searchInput = document.getElementById("table-search");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase();
      table.setFilter(data =>
        data.username.toLowerCase().includes(query) ||
        data.email.toLowerCase().includes(query)
      );
    });
  }

  /*
  document.getElementById("saveChangesBtn").addEventListener("click", () => {
    const updateData = {
      user_id: parseInt(document.getElementById("edit-user-id").value),
      username: document.getElementById("edit-username").value,
      email: document.getElementById("edit-email").value,
      level_user: parseInt(document.getElementById("edit-level").value),
    };

    fetch(BASE_URL + "api/users.php?action=update", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ userData: updateData }),
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) return alert("Error al actualizar usuario: " + data.message);
        table.updateOrAddData([data.updatedData]);
        bootstrap.Modal.getInstance(document.getElementById("editUserModal")).hide();
      });
  });
*/

  document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
    if (!deleteUserID) return;

    fetch(BASE_URL + "api/users.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: parseInt(deleteUserID) }),
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) return alert("Error al eliminar usuario: " + data.message);
        table.deleteRow(deleteUserID);
        deleteUserID = null;
        bootstrap.Modal.getInstance(document.getElementById("deleteUserModal")).hide();
      });
  });


  // Editar usuario
  document.getElementById("saveChangesBtn").addEventListener("click", () => {
    const updateData = {
      user_id: parseInt(document.getElementById("edit-user-id").value),
      username: document.getElementById("edit-username").value,
      email: document.getElementById("edit-email").value,
      level_user: parseInt(document.getElementById("edit-level").value),
    };

    fetch(BASE_URL + "api/users.php?action=update", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ userData: updateData }),
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          Swal.fire({ icon: "error", title: "Error al actualizar", text: data.message });
          return;
        }
        table.updateOrAddData([data.updatedData]);
        bootstrap.Modal.getInstance(document.getElementById("editUserModal")).hide();
        Swal.fire({
          icon: "success",
          title: "Usuario actualizado",
          toast: true,
          position: "top-end",
          timer: 2000,
          showConfirmButton: false,
        });
      });
  });

  // Eliminar usuario
  document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
    if (!deleteUserID) return;

    fetch(BASE_URL + "api/users.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: parseInt(deleteUserID) }),
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          Swal.fire({ icon: "error", title: "Error al eliminar", text: data.message });
          return;
        }
        table.deleteRow(deleteUserID);
        deleteUserID = null;
        bootstrap.Modal.getInstance(document.getElementById("deleteUserModal")).hide();
        Swal.fire({
          icon: "success",
          title: "Usuario eliminado",
          toast: true,
          position: "top-end",
          timer: 2000,
          showConfirmButton: false,
        });
      });
  });

  // Crear usuario
  document.getElementById("addUserBtn").addEventListener("click", () => {
    new bootstrap.Modal(document.getElementById("addUserModal")).show();
  });

  document.getElementById("saveNewUserBtn").addEventListener("click", () => {
    const username = document.getElementById("new-username").value.trim();
    const email = document.getElementById("new-email").value.trim();
    const password = document.getElementById("new-password").value;
    const selectElement = document.getElementById("new-level");
    const levelNew = selectElement.value;
    const descriptionLevel = selectElement.options[selectElement.selectedIndex].text.trim();

    fetch(BASE_URL + "api/users.php?action=create", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        userData: { username, email, password, level_user: levelNew },
      }),
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          Swal.fire({ icon: "error", title: "Error al crear", text: data.message });
          return;
        }
        data.newUser.description_level = descriptionLevel;
        table.addData([data.newUser]);
        bootstrap.Modal.getInstance(document.getElementById("addUserModal")).hide();
        Swal.fire({
          icon: "success",
          title: "Usuario registrado",
          toast: true,
          position: "top-end",
          timer: 2000,
          showConfirmButton: false,
        });
      });
  });



  // Exportar CSV
  document.getElementById("exportCSVBtn").addEventListener("click", () => {
    const datos = table.getData();
    let csvContent = `"REPORTE DE LISTA DE USUARIOS"\n"EMPRESA DEMO S.A. DE C.V."\n"Formato: L001"\n\n`;
    csvContent += "ID,Usuario,Email,Nivel,Creado,Actualizado\n";
    datos.forEach(row => {
      csvContent += `${row.user_id},${row.username},${row.email},${row.description_level},${row.created_at},${row.updated_at}\n`;
    });
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "usuarios.csv";
    a.click();
    URL.revokeObjectURL(url);
  });

  // Exportar Excel
  document.getElementById("exportExcelBtn").addEventListener("click", () => {
    const dataToExport = table.getData().map(({ img_url, ...rest }) => rest);
    table.download("xlsx", "usuarios.xlsx", {
      sheetName: "Reporte Usuarios",
      documentProcessing: workbook => {
        const sheet = workbook.Sheets["Reporte Usuarios"];
        sheet["A1"].s = { font: { bold: true, color: { rgb: "FF0000" } } };
        return workbook;
      },
      rows: dataToExport,
    });
  });

  // Exportar PDF
  document.getElementById("exportPDFBtn").addEventListener("click", () => {
    table.download("pdf", "usuarios.pdf", {
      orientation: "landscape",
      autoTable: {
        styles: { fontSize: 8, cellPadding: 2, halign: "center" },
        margin: { top: 70, left: 10, right: 10 },
        headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: "bold" },
        columns: [
          { header: "ID", dataKey: "user_id" },
          { header: "Usuario", dataKey: "username" },
          { header: "Email", dataKey: "email" },
          { header: "Nivel", dataKey: "description_level" },
          { header: "Creado", dataKey: "created_at" },
          { header: "Actualizado", dataKey: "updated_at" },
        ],
        didDrawPage: data => {
          const doc = data.doc;
          const pageWidth = doc.internal.pageSize.getWidth();
          let y = 25;
          doc.setFontSize(16).setFont(undefined, "bold").text("REPORTE DE LISTA DE USUARIOS", pageWidth / 2, y, { align: "center" });
          y += 10;
          doc.setFontSize(12).setFont(undefined, "normal").text("EMPRESA DEMO S.A. DE C.V.", pageWidth / 2, y, { align: "center" });
          y += 10;
          doc.setFontSize(10).text("Formato: L001", pageWidth / 2, y, { align: "center" });
          doc.setFontSize(9).text("Generado: " + new Date().toLocaleDateString(), data.settings.margin.left, y + 10);
        },
      },
    });
  });

 

});
