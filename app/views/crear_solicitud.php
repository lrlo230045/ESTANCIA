<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Solicitud</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Crear Nueva Solicitud</h2>

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

    <form action="<?= htmlspecialchars($action); ?>" method="POST">

        <label>Material:</label>
        <select name="id_material" required>
            <option value="">Selecciona un material</option>

            <?php foreach ($materiales as $m): ?>
                <option value="<?= htmlspecialchars($m['id_material']); ?>">
                    <?= htmlspecialchars($m['nombre_material']); ?>
                    (<?= htmlspecialchars($m['cantidad_disponible'] . ' ' . $m['unidad_medida']); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label>Cantidad solicitada:</label>
        <input type="number" name="cantidad_solicitada" required min="1">

        <label>Observaciones:</label>
        <textarea
            name="observaciones"
            rows="3"
            style="width:100%; border-radius:10px;"></textarea>

        <button type="submit">Enviar Solicitud</button>
    </form>

    <p>
        <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>
    </p>

</body>
</html>
