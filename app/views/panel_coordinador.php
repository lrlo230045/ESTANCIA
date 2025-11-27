<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Coordinador</title>

    <!-- ============================================================
         CARGA DE TEMA (modo claro/oscuro)
    ============================================================ -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js" defer></script>

    <!-- ============================================================
         CHART.JS — Librería para las gráficas del panel
    ============================================================ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ============================================================
         VARIABLES PHP → JAVASCRIPT
         Convierte arrays PHP en objetos JS usando json_encode()
    ============================================================ -->
    <script>
        const datosMateriales = <?= json_encode($topMateriales); ?>;  // Top materiales solicitados
        const datosCarreras   = <?= json_encode($topCarreras); ?>;    // Carreras con más solicitudes
        const datosGenero     = <?= json_encode($generoStats); ?>;    // Solicitudes por género
    </script>

    <!-- ============================================================
         ARCHIVO JS — Dibuja las gráficas usando Chart.js
    ============================================================ -->
    <script src="assets/js/graficas_coordinador.js" defer></script>
</head>

<!-- Estilo general aplicado para pantallas tipo panel -->
<body class="vista-panel">

    <!-- ============================================================
         MENSAJES DEL CONTROLADOR (éxito o error)
    ============================================================ -->
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


    <!-- ============================================================
         PANEL PRINCIPAL (sección central)
    ============================================================ -->
    <div class="contenido">

        <h1>Panel del Coordinador</h1>

        <!-- Bienvenida usando el nombre desde el controlador -->
        <p>Bienvenido, <?= htmlspecialchars($nombre); ?></p>

        <!-- Botones principales -->
        <div class="botones-panel">

            <!-- Ver solicitudes de su carrera -->
            <button onclick="location.href='index.php?controller=solicitudescoord&action=verSolicitudescoord'">
                Ver Solicitudes
            </button>

            <!-- Crear una nueva solicitud para su propia área -->
            <button onclick="location.href='index.php?controller=solicitudescoord&action=crearSolicitudcoord'">
                Crear Solicitud
            </button>

            <!-- Descargar reporte PDF con gráficas -->
            <button onclick="location.href='index.php?controller=pdf&action=generarReportePastel'">
                Descargar Reporte
            </button>
        </div>

        <br>

        <!-- Cerrar sesión -->
        <a href="index.php?controller=login&action=logout">Cerrar sesión</a>
    </div>



    <!-- ============================================================
         PANEL DE ESTADÍSTICAS — Gráficas generadas con Chart.js
    ============================================================ -->
    <div class="panel-estadisticas">

        <h3>Estadísticas del Mes</h3>

        <!-- ==== GRÁFICA: MATERIALES MÁS SOLICITADOS ==== -->
        <div class="contenedor-grafica">
            <h4>Materiales Más Solicitados</h4>
            <canvas id="graficaMateriales"></canvas>
        </div>

        <hr>

        <!-- ==== GRÁFICA: CARRERAS CON MÁS SOLICITUDES ==== -->
        <div class="contenedor-grafica">
            <h4>Carreras con Más Solicitudes</h4>
            <canvas id="graficaCarreras"></canvas>
        </div>

        <hr>

        <!-- ==== GRÁFICA: SOLICITUDES POR GÉNERO ==== -->
        <div class="contenedor-grafica">
            <h4>Solicitudes por Género</h4>
            <canvas id="graficaGenero"></canvas>
        </div>
    </div>

</body>
</html>
