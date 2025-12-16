document.addEventListener("DOMContentLoaded", function () {
    
  // VERIFICACIÓN DE SEGURIDAD:
  // Si Tabulator no está definido, significa que el JS cargó antes que el footer.
  if (typeof Tabulator === 'undefined') {
      console.error("Error: Tabulator no está cargado. Verifica que este script se ejecute DESPUÉS del footer.");
      return;
  }

  const usersTableElement = document.getElementById("users-table");
  if (!usersTableElement) return;

  // --- 1. CONFIGURACIÓN DE LA TABLA (TABULATOR) ---
  const table = new Tabulator("#users-table", {
      index: "user_id",
      ajaxURL: BASE_URL + "api/users.php?action=get",
      ajaxConfig: "GET",
      layout: "fitColumns",
      responsiveLayout: "collapse",
      placeholder: "Sin registros encontrados",
      pagination: "local",
      paginationSize: 10,
      locale: "es-mx",
      columns: [
          { title: "ID", field: "user_id", width: 60, sorter: "number" },
          { title: "Usuario", field: "username", widthGrow: 1 },
          { title: "Email", field: "email", widthGrow: 1 },
          { title: "Nivel", field: "description_level", widthGrow: 1, hozAlign: "center" },
          {
              title: "Fecha Creación", field: "created_at", widthGrow: 1,
              formatter: cell => {
                  const val = cell.getValue();
                  return val ? new Date(val).toLocaleDateString('es-MX') : "-";
              }
          },
          {
              title: "Acciones", widthGrow: 1, hozAlign: "center",
              formatter: () => `
                  <div class='btn-group'>
                      <button class='btn btn-sm btn-outline-primary edit-btn' title="Editar"><i class="bi bi-pencil-square"></i></button>
                      <button class='btn btn-sm btn-outline-danger delete-btn' title="Eliminar"><i class="bi bi-trash3"></i></button>
                  </div>`,
              cellClick: (e, cell) => {
                  const rowData = cell.getRow().getData();
                  
                  // --- CLICK EN EDITAR ---
                  if (e.target.closest(".edit-btn")) {
                      document.getElementById("edit-user-id").value = rowData.user_id;
                      document.getElementById("edit-username").value = rowData.username;
                      document.getElementById("edit-email").value = rowData.email;
                      document.getElementById("edit-level").value = rowData.level_user;
                      
                      // Usamos getOrCreateInstance para evitar conflictos con otros scripts
                      const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("editUserModal"));
                      modal.show();
                  }

                  // --- CLICK EN ELIMINAR ---
                  if (e.target.closest(".delete-btn")) {
                      eliminarUsuario(rowData);
                  }
              }
          },
      ],
  });

  // --- 2. BUSCADOR EN TIEMPO REAL ---
  const searchInput = document.getElementById("table-search");
  if (searchInput) {
      searchInput.addEventListener("keyup", function () {
          const term = this.value.toLowerCase();
          table.setFilter(data => 
              (data.username && data.username.toLowerCase().includes(term)) ||
              (data.email && data.email.toLowerCase().includes(term))
          );
      });
  }

  // --- 3. EXPORTAR DATOS (Usa las librerías del footer) ---
  document.getElementById("exportCSVBtn")?.addEventListener("click", () => table.download("csv", "usuarios.csv"));
  
  document.getElementById("exportExcelBtn")?.addEventListener("click", () => {
      // Tabulator detecta automáticamente xlsx.full.min.js del footer
      table.download("xlsx", "usuarios.xlsx", { sheetName: "Usuarios" });
  });

  document.getElementById("exportPDFBtn")?.addEventListener("click", () => {
      // Tabulator detecta jspdf y autotable del footer
      table.download("pdf", "usuarios.pdf", {
          orientation: "landscape",
          title: "Reporte de Usuarios",
      });
  });

  document.getElementById("exportJSONBtn")?.addEventListener("click", () => table.download("json", "usuarios.json"));


  // --- 4. MODAL: AGREGAR NUEVO USUARIO ---
  const addUserBtn = document.getElementById("addUserBtn");
  if (addUserBtn) {
      addUserBtn.addEventListener("click", () => {
          document.getElementById("addUserForm").reset();
          const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("addUserModal"));
          modal.show();
      });
  }

  const saveNewUserBtn = document.getElementById("saveNewUserBtn");
  if (saveNewUserBtn) {
      saveNewUserBtn.addEventListener("click", () => {
          const username = document.getElementById("new-username").value.trim();
          const email = document.getElementById("new-email").value.trim();
          const password = document.getElementById("new-password").value;
          const levelSelect = document.getElementById("new-level");

          if (!username || !email || !password) {
              return Swal.fire("Atención", "Todos los campos son obligatorios", "warning");
          }

          mostrarCargando(saveNewUserBtn, true);

          fetch(BASE_URL + "api/users.php?action=create", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ userData: { 
                  username, email, password, level_user: levelSelect.value 
              }})
          })
          .then(res => res.json())
          .then(data => {
              mostrarCargando(saveNewUserBtn, false);
              if (!data.success) throw new Error(data.message);

              // Agregar a la tabla visualmente
              data.newUser.description_level = levelSelect.options[levelSelect.selectedIndex].text;
              table.addData([data.newUser], true); 

              bootstrap.Modal.getInstance(document.getElementById("addUserModal")).hide();
              Swal.fire("Registrado", "Usuario creado correctamente", "success");
          })
          .catch(err => {
              mostrarCargando(saveNewUserBtn, false);
              Swal.fire("Error", err.message || "No se pudo crear el usuario", "error");
          });
      });
  }


  // --- 5. MODAL: GUARDAR EDICIÓN ---
  const saveChangesBtn = document.getElementById("saveChangesBtn");
  if (saveChangesBtn) {
      saveChangesBtn.addEventListener("click", () => {
          const id = document.getElementById("edit-user-id").value;
          const username = document.getElementById("edit-username").value.trim();
          const email = document.getElementById("edit-email").value.trim();
          const levelSelect = document.getElementById("edit-level");

          mostrarCargando(saveChangesBtn, true);

          fetch(BASE_URL + "api/users.php?action=update", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ userData: { 
                  user_id: id, username, email, level_user: levelSelect.value 
              }})
          })
          .then(res => res.json())
          .then(data => {
              mostrarCargando(saveChangesBtn, false);
              if (!data.success) throw new Error(data.message);

              // Actualizar fila
              table.updateData([{
                  user_id: id,
                  username: username,
                  email: email,
                  level_user: levelSelect.value,
                  description_level: levelSelect.options[levelSelect.selectedIndex].text
              }]);

              bootstrap.Modal.getInstance(document.getElementById("editUserModal")).hide();
              Swal.fire("Actualizado", "Datos guardados correctamente", "success");
          })
          .catch(err => {
              mostrarCargando(saveChangesBtn, false);
              Swal.fire("Error", err.message, "error");
          });
      });
  }

  // --- FUNCIONES AUXILIARES ---
  function eliminarUsuario(rowData) {
      Swal.fire({
          title: "¿Estás seguro?",
          text: `Se eliminará al usuario: ${rowData.username}`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar"
      }).then((result) => {
          if (result.isConfirmed) {
              fetch(BASE_URL + "api/users.php?action=delete", {
                  method: "POST",
                  headers: { "Content-Type": "application/json" },
                  body: JSON.stringify({ user_id: rowData.user_id })
              })
              .then(res => res.json())
              .then(data => {
                  if (!data.success) throw new Error(data.message);
                  table.deleteRow(rowData.user_id);
                  Swal.fire("Eliminado", "El usuario ha sido eliminado.", "success");
              })
              .catch(err => Swal.fire("Error", err.message, "error"));
          }
      });
  }

  function mostrarCargando(btn, estado) {
      if(estado) {
          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
      } else {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-save"></i> Guardar'; // Restaura el icono original si gustas
      }
  }
});