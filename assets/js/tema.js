document.addEventListener("DOMContentLoaded", function () {

    // Obtiene el <link> que controla el tema actual
    const themeLink = document.getElementById("theme-link");

    // Obtiene el interruptor (checkbox) del cambio de tema
    const toggle = document.getElementById("theme-toggle");

    // Obtiene el tema guardado en localStorage, o usa el tema por defecto
    const savedTheme = localStorage.getItem("theme") || "colores.css";

    // Aplica el archivo CSS guardado a la página
    themeLink.href = "assets/css/" + savedTheme;

    // Si existe el interruptor en esta vista...
    if (toggle) {

        // Marca el switch dependiendo del tema guardado
        toggle.checked = savedTheme === "colores2.css";

        // Escucha el evento al cambiar el switch
        toggle.addEventListener("change", () => {

            // Si está activado -> usa colores2.css, si no colores.css
            const newTheme = toggle.checked ? "colores2.css" : "colores.css";

            // Cambia el archivo CSS del tema
            themeLink.href = "assets/css/" + newTheme;

            // Guarda el nuevo tema en localStorage para recordar la elección
            localStorage.setItem("theme", newTheme);
        });
    }
});
