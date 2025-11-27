<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Solicitud</title>

    <!-- Hoja de estilos del tema del proyecto -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script para alternar entre tema claro y oscuro -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Editar Solicitud</h2>

    <!-- Mensaje general enviado desde el controlador (si existe) -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Si no se pudo cargar la solicitud, se muestra un error y no se muestra el formulario -->
    <?php if (!$solicitud): ?>
        <p style="color:red; font-weight:bold;">
            Error: No se pudo cargar la información de la solicitud.
        </p>

    <?php else: ?>

        <!-- Validación visual: solo se editan solicitudes pendientes -->
        <?php if ($solicitud['estado'] !== 'pendiente'): ?>
            <p style="color:red; font-weight:bold;">
                Solo se pueden editar solicitudes en estado "pendiente".
                Si esta solicitud ya fue aprobada, rechazada o cancelada, no podrás guardar cambios.
            </p>
        <?php endif; ?>

        <!-- Formulario para editar la solicitud -->
        <!-- La URL del action es enviada desde el controlador -->
        <form method="POST" action="<?= htmlspecialchars($action); ?>">

            <!-- Selección del material solicitado -->
            <label>Material solicitado:</label>
            <select name="id_material" required
                    <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>>
                <option value="">Selecciona un material</option>

                <!-- Se listan todos los materiales disponibles -->
                <?php foreach ($materiales as $m): ?>
                    <option value="<?= $m['id_material']; ?>"
                        <?= $m['id_material'] == $solicitud['id_material'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($m['nombre_material']); ?>
                        (<?= htmlspecialchars($m['cantidad_disponible'].' '.$m['unidad_medida']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Campo para cantidad solicitada -->
            <label>Cantidad solicitada:</label>
            <input type="number" name="cantidad_solicitada"
                   value="<?= htmlspecialchars($solicitud['cantidad_solicitada']); ?>"
                   min="1" required
                   <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>>

            <!-- Área de texto para observaciones -->
            <label>Observaciones:</label>
            <textarea name="observaciones" rows="3"
                      style="width:100%; border-radius:10px;"
                      <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>><?= htmlspecialchars($solicitud['observaciones']); ?></textarea>

            <br>

            <!-- Botón para guardar cambios -->
            <!-- También se deshabilita si la solicitud ya no puede editarse -->
            <button type="submit"
                <?= $solicitud['estado'] !== 'pendiente'
                    ? 'disabled style="opacity:0.6;cursor:not-allowed;"'
                    : ''; ?>>
                Guardar cambios
            </button>

            <!-- Enlace para volver a la pantalla anterior -->
            <a href="<?= htmlspecialchars($volver); ?>" style="margin-left:15px;">
                Regresar
            </a>

        </form>

    <?php endif; ?>

</body>
</html>
