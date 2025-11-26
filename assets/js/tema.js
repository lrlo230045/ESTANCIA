document.addEventListener("DOMContentLoaded", function () {
    const themeLink = document.getElementById("theme-link");
    const toggle = document.getElementById("theme-toggle");

    const savedTheme = localStorage.getItem("theme") || "colores.css";
    themeLink.href = "assets/css/" + savedTheme;

    if (toggle) {
        toggle.checked = savedTheme === "colores2.css";

        toggle.addEventListener("change", () => {
            const newTheme = toggle.checked ? "colores2.css" : "colores.css";
            themeLink.href = "assets/css/" + newTheme;
            localStorage.setItem("theme", newTheme);
        });
    }
});
