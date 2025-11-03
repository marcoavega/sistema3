<footer class="text-center py-3">
    <!-- Pie de página centrado con padding vertical -->
    <p class="mb-0">
        &copy; <?php echo date("Y"); ?> - Sistema. <!-- Muestra el año actual automáticamente -->
    </p>
</footer>

<!-- Bootstrap 5.3.7 Bundle: incluye Popper.js (necesario para tooltips, dropdowns, modales, etc.) -->
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap-5.3.7-dist/bootstrap.bundle.min.js"></script>

<!-- Tabulator 6.3: Biblioteca para crear tablas dinámicas e interactivas -->
<script src="<?php echo BASE_URL; ?>assets/tabulator-6.3/dist/js/tabulator.min.js"></script>

<!-- Script para alternar entre tema claro y oscuro -->
<script src="<?php echo BASE_URL; ?>assets/js/theme.js"></script>

<!-- Script para controlar modales generales -->
<script src="<?php echo BASE_URL; ?>assets/js/modals.js"></script>

<!-- Script específico para el modal de cierre de sesión -->
<script src="<?php echo BASE_URL; ?>assets/js/modal_logout.js"></script>

<!-- Script para el modal de registro de nuevos elementos (usuarios, productos, etc.) 
<script src="<?php echo BASE_URL; ?>assets/js/modals_register.js"></script>-->

<!-- Script con funciones generales reutilizables en todo el sistema 
<script src="<?php echo BASE_URL; ?>assets/js/general-scripts.js"></script> -->

<!-- jsPDF: Librería para generar archivos PDF desde el navegador -->
<script src="<?php echo BASE_URL; ?>assets/js/export-tabulator/jspdf.umd.min.js"></script> 

<!-- jsPDF AutoTable: Plugin para crear tablas dentro de archivos PDF -->
<script src="<?php echo BASE_URL; ?>assets/js/export-tabulator/jspdf.plugin.autotable.min.js"></script> 

<!-- XLSX.js: Librería para exportar datos a Excel (.xlsx) desde el navegador -->
<script src="<?php echo BASE_URL; ?>assets/js/export-tabulator/xlsx.full.min.js"></script> 

<!-- SweetAlert2: Alertas modernas y personalizadas (reemplaza los alert() clásicos) -->
<script src="<?php echo BASE_URL; ?>assets/sweetalert/sweetalert2-11.js"></script> 

</body>
</html>
