<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Solicitud</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Editar Solicitud</h2>

    <!-- Mensaje del controlador -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <?php if (!$solicitud): ?>
        <p style="color:red; font-weight:bold;">
            Error: No se pudo cargar la información de la solicitud.
        </p>

    <?php else: ?>

        <?php if ($solicitud['estado'] !== 'pendiente'): ?>
            <p style="color:red; font-weight:bold;">
                Solo se pueden editar solicitudes en estado "pendiente".
                Si esta solicitud ya fue aprobada, rechazada o cancelada, no podrás guardar cambios.
            </p>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($action); ?>">

            <label>Material solicitado:</label>
            <select name="id_material" required
                    <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>>
                <option value="">Selecciona un material</option>

                <?php foreach ($materiales as $m): ?>
                    <option value="<?= $m['id_material']; ?>"
                        <?= $m['id_material'] == $solicitud['id_material'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($m['nombre_material']); ?>
                        (<?= htmlspecialchars($m['cantidad_disponible'].' '.$m['unidad_medida']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Cantidad solicitada:</label>
            <input type="number" name="cantidad_solicitada"
                   value="<?= htmlspecialchars($solicitud['cantidad_solicitada']); ?>"
                   min="1" required
                   <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>>

            <label>Observaciones:</label>
            <textarea name="observaciones" rows="3"
                      style="width:100%; border-radius:10px;"
                      <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>><?= htmlspecialchars($solicitud['observaciones']); ?></textarea>

            <br>

            <button type="submit"
                <?= $solicitud['estado'] !== 'pendiente'
                    ? 'disabled style="opacity:0.6;cursor:not-allowed;"'
                    : ''; ?>>
                Guardar cambios
            </button>

            <a href="<?= htmlspecialchars($volver); ?>" style="margin-left:15px;">
                Regresar
            </a>

        </form>

    <?php endif; ?>

</body>
</html>
