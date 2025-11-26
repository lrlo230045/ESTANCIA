<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body class="vista-panel">

    <div class="contenido">

        <h1>Panel del Administrador</h1>

        <!-- MENSAJE DE ÉXITO -->
        <?php if (!empty($mensaje)): ?>
            <div class="alerta exito">
                <?= htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- MENSAJE DE ERROR -->
        <?php if (!empty($error)): ?>
            <div class="alerta error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <p>Bienvenido, <?= htmlspecialchars($nombre ?? "Administrador"); ?></p>

        <div class="botones-panel">

            <button onclick="location.href='index.php?controller=materialesAdmin&action=gestionar'">
                Gestionar Materiales
            </button>

            <button onclick="location.href='index.php?controller=ubicacionesAdmin&action=gestionar'">
                Gestionar Ubicaciones
            </button>

            <button onclick="location.href='index.php?controller=carrerasAdmin&action=gestionar'">
                Gestionar Carreras
            </button>

            <button onclick="location.href='index.php?controller=usuariosAdmin&action=gestionar'">
                Gestionar Usuarios
            </button>

            <button onclick="location.href='index.php?controller=solicitudesAdmin&action=gestionar'">
                Gestionar Solicitudes
            </button>

            <button onclick="location.href='index.php?controller=usuariosAdmin&action=registro'">
                Registrar Nuevo Usuario
            </button>

            <button onclick="window.location.href='index.php?controller=backup&action=generar'">
                Descargar Base de Datos
            </button>

            <button onclick="window.location.href='index.php?controller=backup&action=restaurarBD'">
                Restaurar Base de Datos
            </button>

        </div>

        <br>
        <a href="index.php?controller=login&action=logout">Cerrar sesión</a>

    </div>

    <style>
        body.vista-panel { 
            justify-content: center; 
            padding: 40px; 
        }
        .botones-panel button {
            width: 280px;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>

</body>
</html>
