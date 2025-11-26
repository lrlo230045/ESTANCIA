<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Gestionar Usuarios</h2>

    <!-- MENSAJE DEL CONTROLADOR -->
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


    <!-- === FORMULARIO EDITAR === -->
    <form id="formUsuario" action="<?= htmlspecialchars($actionEditar); ?>" method="POST" style="max-width:700px; margin:auto;">
        <input type="hidden" name="id_usuario" value="">
        <h3 id="tituloForm">Editar Usuario</h3>

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Apellido Paterno:</label>
        <input type="text" name="apellido_pa" required>

        <label>Apellido Materno:</label>
        <input type="text" name="apellido_ma">

        <label>Correo:</label>
        <input type="email" name="correo" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono">

        <label>Género:</label>
        <select name="genero" required>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
            <option value="otro">Otro</option>
        </select>

        <label>Tipo de Usuario:</label>
        <select name="tipo_usuario" id="tipo_usuario" required onchange="toggleCarrera()">
            <option value="alumno">Alumno</option>
            <option value="coordinador">Coordinador</option>
            <option value="administrador">Administrador</option>
        </select>

        <!-- CAMPO CARRERA -->
        <div id="campo_carrera" style="display:none;">
            <label>Carrera:</label>
            <select name="id_carrera">
                <option value="">-- Selecciona una carrera --</option>
                <?php foreach ($carreras as $c): ?>
                    <option value="<?= $c['id_carrera']; ?>">
                        <?= htmlspecialchars($c['nombre_carrera']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <label>Estatus:</label>
        <select name="estatus" required>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select>

        <br><br>
        <button type="submit" id="btnGuardar">Guardar Cambios</button>
        <button type="button" onclick="cancelarEdicion()" id="btnCancelar" style="display:none;">Cancelar</button>
    </form>

    <hr style="margin:40px 0;">


    <!-- === TABLA DE USUARIOS === -->

    <table border="2" cellpadding="5" style="margin:auto; color:var(--text-main); border-color:var(--outline-main); width:95%; max-width:1500px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Género</th>
                <th>Tipo</th>
                <th>Carrera</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario']; ?></td>
                    <td><?= htmlspecialchars($u['matricula']); ?></td>
                    <td><?= htmlspecialchars($u['nombre'].' '.$u['apellido_pa'].' '.$u['apellido_ma']); ?></td>
                    <td><?= htmlspecialchars($u['correo']); ?></td>
                    <td><?= htmlspecialchars($u['telefono']); ?></td>
                    <td><?= ucfirst($u['genero']); ?></td>
                    <td><?= ucfirst($u['tipo_usuario']); ?></td>
                    <td><?= htmlspecialchars($u['nombre_carrera'] ?? '-'); ?></td>
                    <td><?= ucfirst($u['estatus']); ?></td>

                    <td class="acciones">

                        <?php if ($u['id_usuario'] != $idUsuarioActual): ?>
                            
                            <button onclick="editarUsuario(
                                '<?= $u['id_usuario']; ?>',
                                '<?= htmlspecialchars($u['nombre'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($u['apellido_pa'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($u['apellido_ma'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($u['correo'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($u['telefono'] ?? '', ENT_QUOTES); ?>',
                                '<?= $u['genero']; ?>',
                                '<?= $u['tipo_usuario']; ?>',
                                '<?= $u['estatus']; ?>',
                                '<?= htmlspecialchars($u['nombre_carrera'] ?? '', ENT_QUOTES); ?>'
                            )">Editar</button>

                            <button onclick="inactivarUsuario(<?= $u['id_usuario']; ?>)">Inactivar</button>

                            <button onclick="eliminarUsuario(<?= $u['id_usuario']; ?>)">Eliminar</button>

                        <?php else: ?>
                            <em style="opacity:0.6;">No editable</em>
                        <?php endif; ?>

                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


<script>
    // Mostrar campo carrera dependiendo del tipo
    function toggleCarrera() {
        const tipo = document.getElementById('tipo_usuario').value;
        document.getElementById('campo_carrera').style.display =
            (tipo === 'alumno' || tipo === 'coordinador') ? 'block' : 'none';
    }

    // Cargar valores del usuario
    function editarUsuario(id, nombre, ap_pa, ap_ma, correo, telefono, genero, tipo, estatus, carrera) {

        document.querySelector("[name='id_usuario']").value = id;
        document.querySelector("[name='nombre']").value = nombre;
        document.querySelector("[name='apellido_pa']").value = ap_pa;
        document.querySelector("[name='apellido_ma']").value = ap_ma;
        document.querySelector("[name='correo']").value = correo;
        document.querySelector("[name='telefono']").value = telefono;
        document.querySelector("[name='genero']").value = genero;
        document.querySelector("[name='tipo_usuario']").value = tipo;
        document.querySelector("[name='estatus']").value = estatus;

        toggleCarrera();

        if (carrera) {
            const select = document.querySelector("[name='id_carrera']");
            [...select.options].forEach(o => { o.selected = (o.text === carrera); });
        }

        document.getElementById("btnCancelar").style.display = "inline-block";
        document.getElementById("formUsuario").action = "<?= $actionEditar; ?>";

        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function cancelarEdicion() {
        document.getElementById("formUsuario").reset();
        document.getElementById("btnCancelar").style.display = "none";
        toggleCarrera();
    }

    function inactivarUsuario(id) {
        if (confirm("¿Deseas marcar este usuario como INACTIVO?")) {
            window.location.href = "<?= $actionInactivar; ?>&id=" + id;
        }
    }

    function eliminarUsuario(id) {
        if (confirm("Esta acción eliminará permanentemente al usuario.\n¿Deseas continuar?")) {
            window.location.href = "<?= $actionEliminar; ?>&id=" + id;
        }
    }
</script>

</body>
</html>
