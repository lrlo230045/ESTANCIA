<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Alumno</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body class="vista-panel">

    <!-- PANEL IZQUIERDO -->
    <div class="panel-lateral">
        <h3>Noticias</h3>

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

        <?php if (!empty($noticias)): ?>
            <?php foreach ($noticias as $img): ?>
                <img src="<?= htmlspecialchars($img); ?>" alt="Noticia">
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-main); text-align:center;">
                No hay noticias disponibles.
            </p>
        <?php endif; ?>
    </div>

    <!-- CONTENIDO CENTRAL -->
    <div class="contenido">
        <h1>Panel del Alumno</h1>
        <p>Bienvenido, <?= htmlspecialchars($nombre); ?></p>

        <div class="botones-panel">
            <button onclick="window.location.href='index.php?controller=solicitudes&action=verSolicitudes'">
                Ver Solicitudes
            </button>

            <button onclick="window.location.href='index.php?controller=solicitudes&action=crearSolicitud'">
                Crear Solicitud
            </button>

            <button onclick="window.location.href='index.php?controller=materiales&action=verMateriales'">
                Ver Materiales
            </button>
        </div>

        <a href="index.php?controller=login&action=logout">Cerrar sesión</a>
    </div>

    <!-- PANEL DERECHO -->
    <div class="panel-lateral">
        <h3>Noticias</h3>

        <?php if (!empty($noticiasReverso)): ?>
            <?php foreach ($noticiasReverso as $img): ?>
                <img src="<?= htmlspecialchars($img); ?>" alt="Noticia">
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-main); text-align:center;">
                No hay noticias disponibles.
            </p>
        <?php endif; ?>
    </div>

</body>
</html>
