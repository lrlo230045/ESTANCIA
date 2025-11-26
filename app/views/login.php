<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body class="vista-login">

    <div class="contenedor-login">

        <!-- LOGO SUPERIOR -->
        <div class="logo-login">
            <img src="assets/logo/logo_siffiv.png" alt="Logo SIFFIV">
        </div>

        <h2>Iniciar Sesión</h2>

        <!-- MENSAJES CONTROLADOR -->
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

        <!-- FORMULARIO -->
        <form action="<?= htmlspecialchars($action ?? 'index.php?controller=login&action=login'); ?>" method="POST">

            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit">Entrar</button>
        </form>

        <p>Contacta a tu administrador para crear una cuenta.</p>

        <!-- INTERRUPTOR DE TEMA -->
        <div class="switch-container">
            <span class="switch-label">Modo Oscuro</span>
            <label class="switch">
                <input type="checkbox" id="theme-toggle">
                <span class="slider"></span>
            </label>
            <span class="switch-label">Modo Claro</span>
        </div>

    </div>

</body>
</html>
