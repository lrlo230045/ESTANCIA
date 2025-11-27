<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Solicitud</title>

    <!-- Carga la hoja de estilos del sistema -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script encargado de cambiar entre tema claro/oscuro -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Editar Solicitud</h2>

    <!-- Muestra un mensaje general del controlador (si existe) -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Si la solicitud no existe, se muestra un error -->
    <?php if (!$solicitud): ?>
        <p style="color:red; font-weight:bold;">
            Error: No se pudo cargar la información de la solicitud.
        </p>

    <?php else: ?>

        <!-- Si la solicitud no está en estado 'pendiente', no debe permitirse edición -->
        <?php if ($solicitud['estado'] !== 'pendiente'): ?>
            <p style="color:red; font-weight:bold;">
                Solo se pueden editar solicitudes en estado "pendiente".
            </p>
        <?php endif; ?>

        <!-- Formulario para editar la solicitud -->
        <!-- $action viene desde el controlador y contiene la URL correcta -->
        <form method="POST" action="<?= htmlspecialchars($action); ?>">

            <!-- Campo para seleccionar material -->
            <!-- Se desactiva si la solicitud ya no está pendiente -->
            <label>Material solicitado:</label>
            <select name="id_material" required 
                    <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>>
                <option value="">Selecciona un material</option>

                <!-- Ciclo que lista todos los materiales disponibles -->
                <?php foreach ($materiales as $m): ?>
                    
                        <!-- Marca como seleccionado el material correspondiente a la solicitud -->
                    <option value="<?= $m['id_material']; ?>"
                        <?= $m['id_material'] == $solicitud['id_material'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($m['nombre_material']); ?>
                        (<?= htmlspecialchars($m['cantidad_disponible'].' '.$m['unidad_medida']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Campo para editar cantidad -->
            <label>Cantidad solicitada:</label>
            
                   <!-- Deshabilitado si no está pendiente -->
            <input type="number" name="cantidad_solicitada"
                   value="<?= htmlspecialchars($solicitud['cantidad_solicitada']); ?>"
                   min="1" required
                   <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>>

            <!-- Campo de texto para observaciones -->
            <label>Observaciones:</label>
            <textarea name="observaciones" rows="3" style="width:100%; border-radius:10px;"
                      <?= $solicitud['estado'] !== 'pendiente' ? 'disabled' : ''; ?>>
                      <?= htmlspecialchars($solicitud['observaciones']); ?>
            </textarea>

            <br>

            <!-- Botón para guardar cambios -->
            <!-- Si no está pendiente, se deshabilita visual y funcionalmente -->
            <button type="submit"
                <?= $solicitud['estado'] !== 'pendiente' 
                        ? 'disabled style="opacity:0.6;cursor:not-allowed;"' 
                        : ''; ?>>
                Guardar cambios
            </button>

            <!-- Enlace para regresar a la lista de solicitudes -->
            <a href="<?= htmlspecialchars($volver); ?>" style="margin-left:15px;">
                Regresar
            </a>

        </form>

    <?php endif; ?>

</body>
</html>
