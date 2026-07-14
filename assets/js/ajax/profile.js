document.addEventListener("DOMContentLoaded", function () {
  const editProfileBtn = document.getElementById("edit-profile-btn");
  const saveProfileChangesBtn = document.getElementById("saveProfileChanges");

  if (!editProfileBtn || !saveProfileChangesBtn) return;

  // Función para mostrar toasts
  function toast(msg, err = false) {
    const t = document.createElement("div");
    t.textContent = msg;
    t.style.position = "fixed";
    t.style.bottom = "25px";
    t.style.right = "25px";
    t.style.padding = "10px 15px";
    t.style.borderRadius = "8px";
    t.style.background = err ? "#dc3545" : "#198754";
    t.style.color = "white";
    t.style.zIndex = 9999;
    t.style.boxShadow = "0 3px 10px rgba(0,0,0,0.3)";
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
  }

  // Abrir modal
  editProfileBtn.addEventListener("click", function () {
    const modal = new bootstrap.Modal(document.getElementById("editProfileModal"));
    modal.show();
  });

  // Guardar cambios
  saveProfileChangesBtn.addEventListener("click", function () {
    const form = document.getElementById("editProfileForm");
    const formData = new FormData(form);

    const password = document.getElementById("edit-password").value;
    const confirmPassword = document.getElementById("edit-password-confirm").value;

    if (password !== "" && password !== confirmPassword) {
      toast("Las contraseñas no coinciden", true);
      return;
    }

    formData.append("username", document.getElementById("edit-username").value);
    formData.append("email", document.getElementById("edit-email").value);
    formData.append("password", password);

    fetch(BASE_URL + "api/profile.php?action=update", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) throw new Error("Error HTTP: " + response.status);
        return response.json();
      })
      .then((data) => {
        if (!data.success) {
          toast("Error al actualizar el perfil: " + data.message, true);
        } else {
          toast("Perfil actualizado correctamente");
          setTimeout(() => location.reload(), 1200);
        }
      })
      .catch((error) => {
        console.error("Error en la solicitud AJAX:", error);
        toast("Error de conexión con el servidor", true);
      });
  });
});
