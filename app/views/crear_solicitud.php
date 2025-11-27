<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Solicitud</title>

    <!-- Hoja de estilos del tema (oscuro/claro) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script que controla el cambio de tema -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Crear Nueva Solicitud</h2>

    <!-- Mensaje de éxito si existe -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Mensaje de error si existe -->
    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario que envía la solicitud al controlador -->
    <!-- $action contiene la ruta generada dinámicamente -->
    <form action="<?= htmlspecialchars($action); ?>" method="POST">

        <!-- Lista desplegable con todos los materiales activos -->
        <label>Material:</label>
        <select name="id_material" required>
            <option value="">Selecciona un material</option>

            <!-- Recorre el arreglo $materiales enviado por el controlador -->
            <?php foreach ($materiales as $m): ?>
                <option value="<?= htmlspecialchars($m['id_material']); ?>">
                    <?= htmlspecialchars($m['nombre_material']); ?>
                    (<?= htmlspecialchars($m['cantidad_disponible'] . ' ' . $m['unidad_medida']); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Captura la cantidad solicitada -->
        <label>Cantidad solicitada:</label>
        <input type="number" name="cantidad_solicitada" required min="1">

        <!-- Campo de texto para observaciones -->
        <label>Observaciones:</label>
        <textarea
            name="observaciones"
            rows="3"
            style="width:100%; border-radius:10px;"></textarea>

        <!-- Botón que envía el formulario -->
        <button type="submit">Enviar Solicitud</button>
    </form>

    <!-- Enlace para regresar al panel del usuario -->
    <p>
        <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>
    </p>

</body>
</html>
