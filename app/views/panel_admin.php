<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>

    <!-- Hoja de estilos base (con variables de colores y modo oscuro) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script que controla el interruptor de tema oscuro/claro -->
    <script src="assets/js/tema.js"></script>
</head>

<!-- Clase que define el estilo visual del panel -->
<body class="vista-panel">

    <div class="contenido">

        <!-- Título principal del panel -->
        <h1>Panel del Administrador</h1>

        <!-- Mensaje enviado por el controlador (éxito) -->
        <?php if (!empty($mensaje)): ?>
            <div class="alerta exito">
                <?= htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Mensaje enviado por el controlador (error) -->
        <?php if (!empty($error)): ?>
            <div class="alerta error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Saludo personalizado con el nombre del usuario -->
        <p>Bienvenido, <?= htmlspecialchars($nombre ?? "Administrador"); ?></p>

        <!-- Contenedor de todos los botones de navegación del panel -->
        <div class="botones-panel">

            <!-- Acceso al CRUD de materiales -->
            <button onclick="location.href='index.php?controller=materialesAdmin&action=gestionar'">
                Gestionar Materiales
            </button>

            <!-- Acceso al CRUD de ubicaciones -->
            <button onclick="location.href='index.php?controller=ubicacionesAdmin&action=gestionar'">
                Gestionar Ubicaciones
            </button>

            <!-- Acceso al CRUD de carreras -->
            <button onclick="location.href='index.php?controller=carrerasAdmin&action=gestionar'">
                Gestionar Carreras
            </button>

            <!-- Acceso al CRUD de usuarios -->
            <button onclick="location.href='index.php?controller=usuariosAdmin&action=gestionar'">
                Gestionar Usuarios
            </button>

            <!-- Acceso a la gestión de solicitudes académicas -->
            <button onclick="location.href='index.php?controller=solicitudesAdmin&action=gestionar'">
                Gestionar Solicitudes
            </button>

            <!-- Registro manual de un nuevo usuario -->
            <button onclick="location.href='index.php?controller=usuariosAdmin&action=registro'">
                Registrar Nuevo Usuario
            </button>

            <!-- Generar respaldo completo de la base de datos -->
            <button onclick="window.location.href='index.php?controller=backup&action=generar'">
                Descargar Base de Datos
            </button>

            <!-- Restaurar base de datos desde el último backup encontrado -->
            <button onclick="window.location.href='index.php?controller=backup&action=restaurarBD'">
                Restaurar Base de Datos
            </button>

        </div>

        <br>

        <!-- Cerrar sesión y volver al login -->
        <a href="index.php?controller=login&action=logout">Cerrar sesión</a>

    </div>

    <!-- Estilos específicos de esta vista -->
    <style>
        /* Alinea al centro el panel */
        body.vista-panel { 
            justify-content: center; 
            padding: 40px; 
        }

        /* Estilo unificado de los botones del panel */
        .botones-panel button {
            width: 280px;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>

</body>
</html>
