<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Materiales</title>

    <!-- Hoja de estilos del sistema (tema oscuro/claro) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script que maneja el cambio de tema -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Gestionar Materiales</h2>

    <!-- Mensaje de éxito si existe en la sesión -->
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


    <!-- =====================================================
         FORMULARIO: AGREGAR / EDITAR MATERIAL
         Funciona para ambos procesos según la acción asignada
    ====================================================== -->
    <form id="formMaterial" action="<?= htmlspecialchars($actionAgregar); ?>" method="POST" style="max-width:700px; margin:auto;">

        <!-- Campo oculto usado solo en edición -->
        <input type="hidden" name="id_material" value="">

        <!-- Título dinámico que cambia entre agregar / editar -->
        <h3 id="tituloForm">Agregar Nuevo Material</h3>

        <!-- Campo de nombre del material -->
        <label>Nombre:</label>
        <input type="text" name="nombre_material" required>

        <!-- Descripción del material -->
        <label>Descripción:</label>
        <input type="text" name="descripcion">

        <!-- Cantidad disponible en inventario -->
        <label>Cantidad Disponible:</label>
        <input type="number" name="cantidad_disponible" min="0" required>

        <!-- Unidad de medida (piezas, litros, metros, etc.) -->
        <label>Unidad de Medida:</label>
        <input type="text" name="unidad_medida" required>

        <!-- Lista de ubicaciones existentes -->
        <label>Ubicación:</label>
        <select name="id_ubicacion" required>
            <option value="">Selecciona una ubicación</option>

            <!-- Ciclo que imprime las ubicaciones enviadas desde el controlador -->
            <?php foreach ($ubicaciones as $u): ?>
                <option value="<?= $u['id_ubicacion']; ?>">
                    <?= htmlspecialchars($u['nombre_ubicacion']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Estado del material -->
        <label>Estado:</label>
        <select name="estado">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select>

        <br><br>

        <!-- Botón principal: cambia entre agregar / guardar cambios -->
        <button type="submit" id="btnGuardar">Agregar</button>

        <!-- Botón para cancelar edición, oculto por defecto -->
        <button type="button" onclick="cancelarEdicion()" style="display:none;" id="btnCancelar">Cancelar</button>

    </form>


    <!-- Separador visual -->
    <hr style="margin:40px 0;">


    <!-- =====================================================
         TABLA DE MATERIALES REGISTRADOS
    ====================================================== -->
    <table border="1" cellpadding="10" style="margin:auto; color:var(--text-main); border-color:var(--outline-main);">

        <!-- Encabezados de tabla -->
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <!-- Ciclo que imprime cada material de la base de datos -->
            <?php foreach ($materiales as $m): ?>
                <tr>
                    <td><?= $m['id_material']; ?></td>
                    <td><?= htmlspecialchars($m['nombre_material']); ?></td>
                    <td><?= htmlspecialchars($m['descripcion']); ?></td>
                    <td><?= $m['cantidad_disponible']; ?></td>
                    <td><?= htmlspecialchars($m['unidad_medida']); ?></td>
                    <td><?= htmlspecialchars($m['nombre_ubicacion']); ?></td>
                    <td><?= ucfirst($m['estado']); ?></td>

                    <td>
                        <!-- Botón para cargar datos en el formulario y editar -->
                        <button onclick="editarMaterial(
                            '<?= $m['id_material']; ?>',
                            '<?= htmlspecialchars($m['nombre_material']); ?>',
                            '<?= htmlspecialchars($m['descripcion']); ?>',
                            '<?= $m['cantidad_disponible']; ?>',
                            '<?= $m['unidad_medida']; ?>',
                            '<?= $m['estado']; ?>'
                        )">Editar</button>

                        <!-- Botón para eliminar el material -->
                        <button onclick="confirmarEliminacion(<?= $m['id_material']; ?>)">
                            Eliminar
                        </button>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>

    </table>

    <br>

    <!-- Enlace para volver al panel del administrador -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


    <!-- =====================================================
         SCRIPTS JAVASCRIPT PARA MANEJAR EDICIÓN / ELIMINACIÓN
    ====================================================== -->
    <script>

        /* -------------------------------------------------------
           Función que llena el formulario con los datos del material
           para permitir la edición.
        -------------------------------------------------------- */
        function editarMaterial(id, nombre, desc, cantidad, unidad, estado) {

            document.querySelector("[name='id_material']").value = id;
            document.querySelector("[name='nombre_material']").value = nombre;
            document.querySelector("[name='descripcion']").value = desc;
            document.querySelector("[name='cantidad_disponible']").value = cantidad;
            document.querySelector("[name='unidad_medida']").value = unidad;
            document.querySelector("[name='estado']").value = estado;

            document.getElementById("tituloForm").innerText = "Editar Material";
            document.getElementById("btnGuardar").innerText = "Guardar Cambios";
            document.getElementById("btnCancelar").style.display = "inline-block";

            /* Ruta enviada desde el controlador para editar */
            document.getElementById("formMaterial").action = "<?= $actionEditar ?>";

            /* Subir el scroll al formulario al iniciar la edición */
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        /* -------------------------------------------------------
           Restaurar el formulario al modo "Agregar"
        -------------------------------------------------------- */
        function cancelarEdicion() {

            document.getElementById("formMaterial").reset();

            document.getElementById("tituloForm").innerText = "Agregar Nuevo Material";
            document.getElementById("btnGuardar").innerText = "Agregar";
            document.getElementById("btnCancelar").style.display = "none";

            /* Ruta de agregar enviada desde el controlador */
            document.getElementById("formMaterial").action = "<?= $actionAgregar ?>";
        }

        /* -------------------------------------------------------
           Confirmación antes de eliminar un material
        -------------------------------------------------------- */
        function confirmarEliminacion(id) {
            if (confirm("ADVERTENCIA:\nEsta acción eliminará el material permanentemente.\n¿Deseas continuar?")) {
                window.location.href = "<?= $actionEliminar ?>&id=" + id;
            }
        }

    </script>

</body>
</html>
