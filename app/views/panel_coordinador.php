<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Coordinador</title>

    <!-- Tema -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js" defer></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Datos desde PHP hacia JS -->
    <script>
        const datosMateriales = <?= json_encode($topMateriales); ?>;
        const datosCarreras   = <?= json_encode($topCarreras); ?>;
        const datosGenero     = <?= json_encode($generoStats); ?>;
    </script>

    <!-- Script de gráficas -->
    <script src="assets/js/graficas_coordinador.js" defer></script>
</head>

<body class="vista-panel">

    <!-- MENSAJES -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>


    <!-- PANEL CENTRAL -->
    <div class="contenido">
        <h1>Panel del Coordinador</h1>
        <p>Bienvenido, <?= htmlspecialchars($nombre); ?></p>

        <div class="botones-panel">
            <button onclick="location.href='index.php?controller=solicitudescoord&action=verSolicitudescoord'">
                Ver Solicitudes
            </button>

            <button onclick="location.href='index.php?controller=solicitudescoord&action=crearSolicitudcoord'">
                Crear Solicitud
            </button>

            <button onclick="location.href='index.php?controller=pdf&action=generarReportePastel'">
                Descargar Reporte
            </button>
        </div>

        <br>
        <a href="index.php?controller=login&action=logout">Cerrar sesión</a>
    </div>

    <!-- PANEL DE ESTADÍSTICAS -->
    <div class="panel-estadisticas">
        <h3>Estadísticas del Mes</h3>

        <div class="contenedor-grafica">
            <h4>Materiales Más Solicitados</h4>
            <canvas id="graficaMateriales"></canvas>
        </div>

        <hr>

        <div class="contenedor-grafica">
            <h4>Carreras con Más Solicitudes</h4>
            <canvas id="graficaCarreras"></canvas>
        </div>

        <hr>

        <div class="contenedor-grafica">
            <h4>Solicitudes por Género</h4>
            <canvas id="graficaGenero"></canvas>
        </div>
    </div>

</body>
</html>
