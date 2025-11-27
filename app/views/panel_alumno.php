<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Alumno</title>

    <!-- Hoja de estilos del sistema (modo claro/oscuro + variables) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script que gestiona el interruptor de tema -->
    <script src="assets/js/tema.js"></script>
</head>

<!-- Clase general para vistas tipo panel -->
<body class="vista-panel">

    <!-- ============================================================
         PANEL IZQUIERDO — Muestra imágenes de noticias recientes
    ============================================================ -->
    <div class="panel-lateral">
        <h3>Noticias</h3>

        <!-- Mensaje de éxito enviado por el controlador -->
        <?php if (!empty($mensaje)): ?>
            <div class="alerta exito">
                <?= htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Mensaje de error enviado por el controlador -->
        <?php if (!empty($error)): ?>
            <div class="alerta error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar lista de imágenes si existen -->
        <?php if (!empty($noticias)): ?>
            <?php foreach ($noticias as $img): ?>
                <img src="<?= htmlspecialchars($img); ?>" alt="Noticia">
            <?php endforeach; ?>

        <!-- Si no hay imágenes disponibles -->
        <?php else: ?>
            <p style="color: var(--text-main); text-align:center;">
                No hay noticias disponibles.
            </p>
        <?php endif; ?>
    </div>


    <!-- ============================================================
         CONTENIDO CENTRAL — Funciones principales del alumno
    ============================================================ -->
    <div class="contenido">

        <!-- Título del panel -->
        <h1>Panel del Alumno</h1>

        <!-- Bienvenida personalizada con el nombre obtenido del controlador -->
        <p>Bienvenido, <?= htmlspecialchars($nombre); ?></p>

        <!-- Botones principales del panel -->
        <div class="botones-panel">

            <!-- Ver solicitudes del alumno -->
            <button onclick="window.location.href='index.php?controller=solicitudes&action=verSolicitudes'">
                Ver Solicitudes
            </button>

            <!-- Crear una nueva solicitud -->
            <button onclick="window.location.href='index.php?controller=solicitudes&action=crearSolicitud'">
                Crear Solicitud
            </button>

            <!-- Ver listado de materiales disponibles -->
            <button onclick="window.location.href='index.php?controller=materiales&action=verMateriales'">
                Ver Materiales
            </button>
        </div>

        <!-- Enlace para cerrar sesión -->
        <a href="index.php?controller=login&action=logout">Cerrar sesión</a>
    </div>


    <!-- ============================================================
         PANEL DERECHO — Muestra las noticias en orden inverso
    ============================================================ -->
    <div class="panel-lateral">
        <h3>Noticias</h3>

        <!-- Mostrar imágenes invertidas si hay contenido -->
        <?php if (!empty($noticiasReverso)): ?>
            <?php foreach ($noticiasReverso as $img): ?>
                <img src="<?= htmlspecialchars($img); ?>" alt="Noticia">
            <?php endforeach; ?>

        <!-- Si no existen imágenes -->
        <?php else: ?>
            <p style="color: var(--text-main); text-align:center;">
                No hay noticias disponibles.
            </p>
        <?php endif; ?>
    </div>

</body>
</html>
