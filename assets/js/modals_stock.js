// assets/js/modals_stock.js

document.addEventListener("DOMContentLoaded", () => {

    const BASE = window.BASE_URL || "/";

    async function post(endpoint, data) {
        const resp = await fetch(`${BASE}api/stock.php?action=${endpoint}`, {
            method: "POST",
            body: data
        });

        const txt = await resp.text();
        if (!resp.ok) throw new Error(txt);

        return JSON.parse(txt);
    }

    function toast(msg, error = false) {
        const t = document.createElement("div");
        t.textContent = msg;
        t.style.position = "fixed";
        t.style.bottom = "25px";
        t.style.right = "25px";
        t.style.padding = "10px 15px";
        t.style.borderRadius = "8px";
        t.style.background = error ? "#dc3545" : "#198754";
        t.style.color = "white";
        t.style.zIndex = "9999";
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }

    // -------------------
    // ENTRADA DE STOCK
    // -------------------
    document.getElementById("formStockEntry")?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);

        try {
            const data = await post("entry", fd);
            if (!data.success) return toast(data.message, true);

            toast("Entrada registrada");
            location.reload();

        } catch (err) {
            console.error(err);
            toast("Error en la operación", true);
        }
    });

    // -------------------
    // SALIDA DE STOCK
    // -------------------
    document.getElementById("formStockExit")?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);

        try {
            const data = await post("exit", fd);
            if (!data.success) return toast(data.message, true);

            toast("Salida registrada");
            location.reload();

        } catch (err) {
            console.error(err);
            toast("Error en la operación", true);
        }
    });


    // -------------------
    // TRANSFERENCIA
    // -------------------
    document.getElementById("formStockTransfer")?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);

        try {
            const data = await post("transfer", fd);
            if (!data.success) return toast(data.message, true);

            toast("Transferencia realizada");
            location.reload();

        } catch (err) {
            console.error(err);
            toast("Error en la operación", true);
        }
    });

});
