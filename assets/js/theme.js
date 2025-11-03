// Espera a que todo el contenido del DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {

  // Obtiene los elementos necesarios del DOM
  const btn = document.getElementById('themeToggleBtn');     // Botón que cambia el tema
  const iconLight = document.getElementById('iconLight');    // Icono del sol (tema claro)
  const iconDark = document.getElementById('iconDark');      // Icono de la luna (tema oscuro)
  const html = document.documentElement;                     // Etiqueta <html> principal del documento
  const tabulatorStyle = document.getElementById('tabulator-theme'); // <link> donde se carga el estilo de Tabulator

  // Verifica que todos los elementos requeridos existan, si falta alguno, se detiene el script
  if (!btn || !iconLight || !iconDark || !tabulatorStyle) return;

  // Define las rutas a los estilos CSS de Tabulator según el tema
  const tabulatorThemes = {
    light: BASE_URL + 'assets/tabulator/css/tabulator.min.css',              // Tema claro
    dark: BASE_URL + 'assets/tabulator/css/tabulator_site_dark.min.css'      // Tema oscuro
  };

  // Obtiene el tema almacenado en localStorage (persistente entre recargas), o usa 'dark' por defecto
  let theme = localStorage.getItem('theme') || 'dark';

  // Aplica el tema actual al cargar la página
  applyTheme(theme);

  // Evento que se activa al hacer clic en el botón de cambiar tema
  btn.addEventListener('click', () => {
    // Cambia el valor de tema entre 'light' y 'dark'
    theme = (theme === 'light') ? 'dark' : 'light';

    // Guarda el nuevo tema en localStorage para mantenerlo la próxima vez que se cargue la página
    localStorage.setItem('theme', theme);

    // Aplica el nuevo tema visual
    applyTheme(theme);
  });

  // Función que aplica el tema indicado
  function applyTheme(theme) {
    // Cambia el atributo data-bs-theme en <html> (esto controla el modo claro/oscuro de Bootstrap)
    html.setAttribute('data-bs-theme', theme);

    // Muestra u oculta el ícono según el tema
    iconLight.classList.toggle('d-none', theme === 'dark'); // Oculta el ícono claro si es tema oscuro
    iconDark.classList.toggle('d-none', theme === 'light'); // Oculta el ícono oscuro si es tema claro

    // Cambia la hoja de estilo usada por Tabulator según el tema
    tabulatorStyle.setAttribute('href', tabulatorThemes[theme]);

    // Si Tabulator está definido, se redibujan las tablas para que adopten el nuevo tema
    if (typeof Tabulator !== 'undefined') {
      const tables = Tabulator.findTable('.tabulator'); // Encuentra todas las tablas con clase .tabulator

      if (Array.isArray(tables)) {
        // Si hay varias tablas, las redibuja una por una
        tables.forEach(table => table.redraw(true));
      } else if (tables) {
        // Si solo hay una tabla, la redibuja
        tables.redraw(true);
      }
    }
  }
});
