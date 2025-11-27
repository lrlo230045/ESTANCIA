// recibe variables globales generadas en PHP: 
// datosMateriales, datosCarreras, datosGenero

// función que genera una gráfica de pastel (pie chart)
function generarPie(idCanvas, datos) {

    // extrae las etiquetas del arreglo recibido
    const etiquetas = datos.map(d => d.etiqueta);

    // extrae los valores porcentuales
    const valores   = datos.map(d => d.porcentaje);

    // crea una nueva gráfica en el canvas indicado
    new Chart(document.getElementById(idCanvas), {
        type: 'pie', // tipo de gráfica
        data: {
            labels: etiquetas, // nombres que aparecerán en la leyenda
            datasets: [{
                data: valores, // valores de cada segmento
                
                // colores predefinidos para cada sector de la gráfica
                backgroundColor: [
                    '#a0e7e5', '#b4c6d1', '#d8c7f1', '#ffb6b9',
                    '#527b92', '#a0c4ff', '#ffc6ff', '#bde0fe',
                    '#c7f9cc', '#fcd5ce'
                ],
                borderColor: 'transparent' // sin borde en los segmentos
            }]
        },
        options: {
            plugins: {
                // configuración de la leyenda
                legend: {
                    labels: {
                        // usa el color definido en CSS para texto
                        color: getComputedStyle(document.documentElement)
                                 .getPropertyValue('--text-main').trim(),

                        // estilo del texto de la leyenda
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

    // genera gráfica para materiales más solicitados
    generarPie("graficaMateriales", datosMateriales);

    // genera gráfica para carreras con más solicitudes
    generarPie("graficaCarreras", datosCarreras);

    // genera gráfica para distribución por género
    generarPie("graficaGenero", datosGenero);
});
