<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>

    <!-- Hoja de estilos principal (usa variables CSS personalizadas) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script para alternar entre modo oscuro y modo claro -->
    <script src="assets/js/tema.js"></script>
</head>

<!-- Clase vista-login da estilos específicos para el diseño del login -->
<body class="vista-login">

    <div class="contenedor-login">

        <!-- =======================================================
             LOGO SUPERIOR
        ======================================================== -->
        <div class="logo-login">
            <img src="assets/logo/logo_siffiv.png" alt="Logo SIFFIV">
        </div>

        <h2>Iniciar Sesión</h2>

        <!-- =======================================================
             MENSAJES ENVIADOS DESDE EL CONTROLADOR LOGIN
             (Mensajes flash almacenados en sesión)
        ======================================================== -->
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


        <!-- =======================================================
             FORMULARIO DE AUTENTICACIÓN
        ======================================================== -->
        <form action="<?= htmlspecialchars($action ?? 'index.php?controller=login&action=login'); ?>" 
              method="POST">

            <!-- Matrícula del usuario -->
            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" required>

            <!-- Contraseña cifrada en la base de datos -->
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit">Entrar</button>
        </form>

        <!-- Texto informativo adicional -->
        <p>Contacta a tu administrador para crear una cuenta.</p>


        <!-- =======================================================
             INTERRUPTOR DE TEMA (DARK / LIGHT MODE)
             Controlado por theme.js
        ======================================================== -->
        <div class="switch-container">

            <span class="switch-label">Modo Oscuro</span>

            <!-- Switch visual -->
            <label class="switch">
                <input type="checkbox" id="theme-toggle">
                <span class="slider"></span>
            </label>

            <span class="switch-label">Modo Claro</span>
        </div>

    </div>

</body>
</html>
