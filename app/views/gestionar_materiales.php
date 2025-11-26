<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Materiales</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>
<body>
    <h2>Gestionar Materiales</h2>

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

    <!-- === FORMULARIO AGREGAR / EDITAR MATERIAL === -->
    <form id="formMaterial" action="<?= htmlspecialchars($actionAgregar); ?>" method="POST" style="max-width:700px; margin:auto;">
        <input type="hidden" name="id_material" value="">
        <h3 id="tituloForm">Agregar Nuevo Material</h3>

        <label>Nombre:</label>
        <input type="text" name="nombre_material" required>

        <label>Descripción:</label>
        <input type="text" name="descripcion">

        <label>Cantidad Disponible:</label>
        <input type="number" name="cantidad_disponible" min="0" required>

        <label>Unidad de Medida:</label>
        <input type="text" name="unidad_medida" required>

        <label>Ubicación:</label>
        <select name="id_ubicacion" required>
            <option value="">Selecciona una ubicación</option>
            <?php foreach ($ubicaciones as $u): ?>
                <option value="<?= $u['id_ubicacion']; ?>"><?= htmlspecialchars($u['nombre_ubicacion']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Estado:</label>
        <select name="estado">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select>

        <br><br>
        <button type="submit" id="btnGuardar">Agregar</button>
        <button type="button" onclick="cancelarEdicion()" style="display:none;" id="btnCancelar">Cancelar</button>
    </form>

    <hr style="margin:40px 0;">

    <!-- === TABLA DE MATERIALES === -->
    <table border="1" cellpadding="10" style="margin:auto; color:var(--text-main); border-color:var(--outline-main);">
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
                        <button onclick="editarMaterial(
                            '<?= $m['id_material']; ?>',
                            '<?= htmlspecialchars($m['nombre_material']); ?>',
                            '<?= htmlspecialchars($m['descripcion']); ?>',
                            '<?= $m['cantidad_disponible']; ?>',
                            '<?= $m['unidad_medida']; ?>',
                            '<?= $m['estado']; ?>'
                        )">Editar</button>

                        <button onclick="confirmarEliminacion(<?= $m['id_material']; ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>

    <script>
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

            // Ruta dinámica enviada por el controlador
            document.getElementById("formMaterial").action = "<?= $actionEditar ?>";
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function cancelarEdicion() {
            document.getElementById("formMaterial").reset();
            document.getElementById("tituloForm").innerText = "Agregar Nuevo Material";
            document.getElementById("btnGuardar").innerText = "Agregar";
            document.getElementById("btnCancelar").style.display = "none";

            // Ruta dinámica enviada por el controlador
            document.getElementById("formMaterial").action = "<?= $actionAgregar ?>";
        }

        function confirmarEliminacion(id) {
            if (confirm("ADVERTENCIA:\nEsta acción eliminará el material permanentemente.\n¿Deseas continuar?")) {
                window.location.href = "<?= $actionEliminar ?>&id=" + id;
            }
        }
    </script>

</body>
</html>
