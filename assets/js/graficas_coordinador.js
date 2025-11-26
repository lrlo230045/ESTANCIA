// recibe variables globales generadas en PHP: 
// datosMateriales, datosCarreras, datosGenero

function generarPie(idCanvas, datos) {
    const etiquetas = datos.map(d => d.etiqueta);
    const valores   = datos.map(d => d.porcentaje);

    new Chart(document.getElementById(idCanvas), {
        type: 'pie',
        data: {
            labels: etiquetas,
            datasets: [{
                data: valores,
                backgroundColor: [
                    '#a0e7e5', '#b4c6d1', '#d8c7f1', '#ffb6b9',
                    '#527b92', '#a0c4ff', '#ffc6ff', '#bde0fe',
                    '#c7f9cc', '#fcd5ce'
                ],
                borderColor: 'transparent'
            }]
        },
        options: {
            plugins: {
    legend: {
        labels: {
            color: getComputedStyle(document.documentElement)
                     .getPropertyValue('--text-main').trim(),
            font: {
                size: 14,
                weight: 'bold'
            }
        }
    }
}     
        }
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
    generarPie("graficaMateriales", datosMateriales);
    generarPie("graficaCarreras", datosCarreras);
    generarPie("graficaGenero", datosGenero);
});
