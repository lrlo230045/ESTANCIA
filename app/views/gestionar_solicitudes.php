<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Solicitudes</title>

    <!-- Archivo CSS para los colores y estilos globales -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script del sistema para manejar el tema claro/oscuro -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Gestionar Solicitudes</h2>

    <!-- Si existe un mensaje desde el controlador (éxito), se muestra -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>


    <!-- =====================================================
         FORMULARIO PARA EDITAR ESTADO DE UNA SOLICITUD
    ====================================================== -->
    <form id="formSolicitud" action="<?= $actionEditar; ?>" method="POST"
          style="max-width:700px; margin:auto;">

        <!-- Campo oculto: se llena al presionar "Editar" -->
        <input type="hidden" name="id_solicitud" value="">

        <h3 id="tituloForm">Editar Estado de Solicitud</h3>

        <!-- Campo: cambio de estado -->
        <label>Nuevo Estado:</label>
        <select name="estado" required>
            <option value="pendiente">Pendiente</option>
            <option value="aprobada">Aprobada</option>
            <option value="rechazada">Rechazada</option>
            <option value="entregada">Entregada</option>
        </select>

        <!-- Observaciones del administrador -->
        <label>Observaciones:</label>
        <textarea name="observaciones" rows="3" style="width:100%; border-radius:10px;"></textarea>

        <br><br>

        <!-- Botón principal: guardar cambios -->
        <button type="submit" id="btnGuardar">Guardar Cambios</button>

        <!-- Botón de cancelar edición, inicialmente oculto -->
        <button type="button" onclick="cancelarEdicion()" id="btnCancelar"
                style="display:none;">Cancelar</button>
    </form>


    <!-- Separador visual para mejorar la estructura -->
    <hr style="margin:40px 0;">


    <!-- =====================================================
         TABLA DE SOLICITUDES EXISTENTES
    ====================================================== -->
    <table border="1" cellpadding="10"
           style="margin:auto; color:var(--text-main); border-color:var(--outline-main);">

        <!-- Encabezado de columnas -->
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
            <!-- Recorre todas las solicitudes que envió el controlador -->
            <?php foreach ($solicitudes as $s): ?>
                <tr>
                    <td><?= $s['id_solicitud']; ?></td>

                    <!-- Nombre completo del solicitante -->
                    <td><?= htmlspecialchars($s['nombre'] . ' ' . $s['apellido_pa'] . ' ' . $s['apellido_ma']); ?></td>

                    <!-- Nombre del material solicitado -->
                    <td><?= htmlspecialchars($s['nombre_material']); ?></td>

                    <!-- Cantidad solicitada -->
                    <td><?= $s['cantidad_solicitada']; ?></td>

                    <!-- Estado formateado -->
                    <td><?= ucfirst(htmlspecialchars($s['estado'])); ?></td>

                    <!-- Fecha formateada correctamente -->
                    <td><?= date("d/m/Y H:i", strtotime($s['fecha_solicitud'])); ?></td>

                    <!-- Observaciones -->
                    <td><?= htmlspecialchars($s['observaciones']); ?></td>

                    <td>
                        <!-- Botón para enviar datos al formulario de edición -->
                        <button onclick="editarSolicitud(
                            '<?= $s['id_solicitud']; ?>',
                            '<?= $s['estado']; ?>',
                            '<?= htmlspecialchars($s['observaciones'], ENT_QUOTES); ?>'
                        )">Editar</button>

                        <!-- Botón que abre confirmación antes de eliminar -->
                        <button onclick="confirmarEliminacion(
                        <?= $s['id_solicitud']; ?>
                        )">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <br>

    <!-- Enlace para regresar al panel principal -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


    <!-- =====================================================
         SCRIPTS PARA MANIPULAR EL FORMULARIO Y ACCIONES
    ====================================================== -->
    <script>

        /* ---------------------------------------------------
           CARGAR DATOS EN EL FORMULARIO PARA EDICIÓN
        ---------------------------------------------------- */
        function editarSolicitud(id, estado, observaciones) {
            document.querySelector("[name='id_solicitud']").value = id;
            document.querySelector("[name='estado']").value = estado;
            document.querySelector("[name='observaciones']").value = observaciones;

            document.getElementById("btnCancelar").style.display = "inline-block";

            // Subir hacia el formulario automáticamente
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        /* ---------------------------------------------------
           RESTAURAR FORMULARIO (modo normal)
        ---------------------------------------------------- */
        function cancelarEdicion() {
            document.getElementById("formSolicitud").reset();
            document.getElementById("btnCancelar").style.display = "none";
        }

        /* ---------------------------------------------------
           CONFIRMAR Y ELIMINAR UNA SOLICITUD
        ---------------------------------------------------- */
        function confirmarEliminacion(id) {
            const confirmar = confirm(
                "ADVERTENCIA:\nEsta acción eliminará la solicitud permanentemente.\n¿Deseas continuar?"
            );

            if (confirmar) {
                // Se usa la URL tal cual para evitar problemas de htmlspecialchars()
                window.location.href = "<?= $actionEliminar; ?>&id=" + id;
            }
        }

    </script>

</body>
</html>
