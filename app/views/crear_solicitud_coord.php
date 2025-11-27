<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Solicitud</title>

    <!-- Enlace al archivo de colores (tema oscuro/claro) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script que controla el cambio de tema -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Crear Solicitud</h2>

    <!-- Formulario para crear una solicitud -->
    <!-- action viene desde el controlador y apunta a solicitudesController->crearSolicitud -->
    <form action="<?php echo htmlspecialchars($action); ?>" method="POST">

        <!-- Selección de material -->
        <label>Material:</label>
        <select name="id_material" required>
            <option value="">Selecciona un material</option>

            <!-- Se listan todos los materiales activos -->
            <!-- $materiales es un arreglo enviado por el controlador -->
            <?php foreach ($materiales as $m): ?>
                <option value="<?php echo htmlspecialchars($m['id_material']); ?>">
                    <?php echo htmlspecialchars($m['nombre_material']); ?>
                    (<?php echo htmlspecialchars($m['cantidad_disponible'] . ' ' . $m['unidad_medida']); ?>)
                </option>
            <?php endforeach; ?>

        </select>

        <!-- Campo para capturar la cantidad -->
        <label>Cantidad solicitada:</label>
        <input type="number" name="cantidad_solicitada" required min="1">

        <!-- Campo de texto para observaciones adicionales -->
        <label>Observaciones:</label>
        <textarea name="observaciones" rows="3" style="width:100%; border-radius:10px;"></textarea>

        <!-- Botón para enviar la solicitud -->
        <button type="submit">Enviar Solicitud</button>
    </form>

    <br>

    <!-- Enlace para volver al panel principal del alumno -->
    <a href="<?php echo htmlspecialchars($volver); ?>">Volver al Panel</a>

</body>
</html>
