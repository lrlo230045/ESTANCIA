<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Solicitudes</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Gestionar Solicitudes</h2>

    <!-- Mensaje del controlador -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO EDITAR ESTADO -->
    <!-- ✔ SE QUITÓ htmlspecialchars() para evitar &amp;action -->
    <form id="formSolicitud" action="<?= $actionEditar; ?>" method="POST"
          style="max-width:700px; margin:auto;">

        <input type="hidden" name="id_solicitud" value="">
        <h3 id="tituloForm">Editar Estado de Solicitud</h3>

        <label>Nuevo Estado:</label>
        <select name="estado" required>
            <option value="pendiente">Pendiente</option>
            <option value="aprobada">Aprobada</option>
            <option value="rechazada">Rechazada</option>
            <option value="entregada">Entregada</option>
        </select>

        <label>Observaciones:</label>
        <textarea name="observaciones" rows="3" style="width:100%; border-radius:10px;"></textarea>

        <br><br>
        <button type="submit" id="btnGuardar">Guardar Cambios</button>

        <button type="button" onclick="cancelarEdicion()" id="btnCancelar"
                style="display:none;">Cancelar</button>
    </form>

    <hr style="margin:40px 0;">

    <!-- TABLA DE SOLICITUDES -->
    <table border="1" cellpadding="10"
           style="margin:auto; color:var(--text-main); border-color:var(--outline-main);">
        <thead>
            <tr>
                <th>ID</th>
                <th>Solicitante</th>
                <th>Material</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($solicitudes as $s): ?>
                <tr>
                    <td><?= $s['id_solicitud']; ?></td>

                    <td><?= htmlspecialchars($s['nombre'] . ' ' . $s['apellido_pa'] . ' ' . $s['apellido_ma']); ?></td>

                    <td><?= htmlspecialchars($s['nombre_material']); ?></td>

                    <td><?= $s['cantidad_solicitada']; ?></td>

                    <td><?= ucfirst(htmlspecialchars($s['estado'])); ?></td>

                    <td><?= date("d/m/Y H:i", strtotime($s['fecha_solicitud'])); ?></td>

                    <td><?= htmlspecialchars($s['observaciones']); ?></td>

                    <td>
                        <button onclick="editarSolicitud(
                            '<?= $s['id_solicitud']; ?>',
                            '<?= $s['estado']; ?>',
                            '<?= htmlspecialchars($s['observaciones'], ENT_QUOTES); ?>'
                        )">Editar</button>

                        <button onclick="confirmarEliminacion(
                        <?= $s['id_solicitud']; ?>
                        )">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>

    <!-- SCRIPTS -->
    <script>
        // Cargar datos en formulario
        function editarSolicitud(id, estado, observaciones) {
            document.querySelector("[name='id_solicitud']").value = id;
            document.querySelector("[name='estado']").value = estado;
            document.querySelector("[name='observaciones']").value = observaciones;

            document.getElementById("btnCancelar").style.display = "inline-block";

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Cancelar edición
        function cancelarEdicion() {
            document.getElementById("formSolicitud").reset();
            document.getElementById("btnCancelar").style.display = "none";
        }

        // Confirmar eliminación
        function confirmarEliminacion(id) {
            const confirmar = confirm(
                "ADVERTENCIA:\nEsta acción eliminará la solicitud permanentemente.\n¿Deseas continuar?"
            );

            if (confirmar) {
                // ✔ SE QUITÓ htmlspecialchars() para prevenir &amp;action
                window.location.href = "<?= $actionEliminar; ?>&id=" + id;
            }
        }
    </script>

</body>
</html>
