<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Ubicaciones</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Gestionar Ubicaciones</h2>

    <!-- Mensaje enviado por el controlador -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- === FORMULARIO AGREGAR / EDITAR === -->
    <form id="formUbicacion" action="<?= htmlspecialchars($actionAgregar); ?>" method="POST"
          style="max-width:700px; margin:auto;">

        <input type="hidden" name="id_ubicacion" value="">
        <h3 id="tituloForm">Agregar Nueva Ubicación</h3>

        <label>Nombre:</label>
        <input type="text" name="nombre_ubicacion" required>

        <label>Ubicación Física:</label>
        <input type="text" name="ubicacion_fisica" required>

        <label>Capacidad:</label>
        <input type="number" name="capacidad" min="1" required>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" style="width:100%; border-radius:10px;"></textarea>

        <br><br>

        <button type="submit" id="btnGuardar">Agregar</button>

        <button type="button" onclick="cancelarEdicion()" id="btnCancelar"
                style="display:none;">Cancelar</button>
    </form>

    <hr style="margin:40px 0;">

    <!-- === TABLA DE UBICACIONES === -->
    <table border="1" cellpadding="10"
           style="margin:auto; color:var(--text-main); border-color:var(--outline-main);">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación Física</th>
                <th>Capacidad</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($ubicaciones as $u): ?>
                <tr>
                    <td><?= $u['id_ubicacion']; ?></td>

                    <td><?= htmlspecialchars($u['nombre_ubicacion']); ?></td>
                    <td><?= htmlspecialchars($u['ubicacion_fisica']); ?></td>
                    <td><?= $u['capacidad']; ?></td>
                    <td><?= htmlspecialchars($u['descripcion']); ?></td>

                    <td>
                        <button onclick="editarUbicacion(
                            '<?= $u['id_ubicacion']; ?>',
                            '<?= htmlspecialchars($u['nombre_ubicacion']); ?>',
                            '<?= htmlspecialchars($u['ubicacion_fisica']); ?>',
                            '<?= $u['capacidad']; ?>',
                            '<?= htmlspecialchars($u['descripcion']); ?>'
                        )">Editar</button>

                        <button onclick="confirmarEliminacion(<?= $u['id_ubicacion']; ?>)">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <br>

    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>

    <script>
    // ❌ Antes (creaba &amp;action)
    // const urlEditar   = "<?= htmlspecialchars($actionEditar); ?>";
    // const urlEliminar = "<?= htmlspecialchars($actionEliminar); ?>";
    // const urlAgregar  = "<?= htmlspecialchars($actionAgregar); ?>";

    // ✔ Ahora — URLs limpias y correctas
    const urlEditar   = "<?= $actionEditar; ?>";
    const urlEliminar = "<?= $actionEliminar; ?>";
    const urlAgregar  = "<?= $actionAgregar; ?>";

    // Cambiar a modo editar
    function editarUbicacion(id, nombre, fisica, capacidad, desc) {

        document.querySelector("[name='id_ubicacion']").value = id;
        document.querySelector("[name='nombre_ubicacion']").value = nombre;
        document.querySelector("[name='ubicacion_fisica']").value = fisica;
        document.querySelector("[name='capacidad']").value = capacidad;
        document.querySelector("[name='descripcion']").value = desc;

        document.getElementById("tituloForm").innerText = "Editar Ubicación";
        document.getElementById("btnGuardar").innerText = "Guardar Cambios";
        document.getElementById("btnCancelar").style.display = "inline-block";

        // Correcto
        document.getElementById("formUbicacion").action = urlEditar;

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Cancelar edición
    function cancelarEdicion() {

        document.getElementById("formUbicacion").reset();

        document.getElementById("tituloForm").innerText = "Agregar Nueva Ubicación";
        document.getElementById("btnGuardar").innerText = "Agregar";
        document.getElementById("btnCancelar").style.display = "none";

        // Correcto
        document.getElementById("formUbicacion").action = urlAgregar;
    }

    // Confirmar eliminación
    function confirmarEliminacion(id) {
        const confirmar = confirm(
            "ADVERTENCIA:\nEsta acción eliminará la ubicación permanentemente.\n¿Deseas continuar?"
        );

        if (confirmar) {
            // Correcto
            window.location.href = urlEliminar + "&id=" + id;
        }
    }

</script>
</body>
</html>
