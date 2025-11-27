<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>

    <!-- Hoja de estilos general (usa variables de color y tema) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script para alternar tema claro/oscuro -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal -->
    <h2>Gestionar Usuarios</h2>

    <!-- Mensaje enviado desde el controlador -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Error enviado por el controlador -->
    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>


    <!-- ==========================================================
         FORMULARIO PARA EDITAR USUARIOS
         * Se llena mediante JavaScript al presionar "Editar"
    =========================================================== -->
    <form id="formUsuario" action="<?= htmlspecialchars($actionEditar); ?>" 
          method="POST" style="max-width:700px; margin:auto;">

        <!-- ID oculto que se llena al editar -->
        <input type="hidden" name="id_usuario" value="">

        <!-- Título que no cambia (siempre se edita aquí) -->
        <h3 id="tituloForm">Editar Usuario</h3>

        <!-- Campos del usuario -->
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

        <!-- Tipo determina si se muestra el campo carrera -->
        <label>Tipo de Usuario:</label>
        <select name="tipo_usuario" id="tipo_usuario" required onchange="toggleCarrera()">
            <option value="alumno">Alumno</option>
            <option value="coordinador">Coordinador</option>
            <option value="administrador">Administrador</option>
        </select>

        <!-- Campo carrera (solo visible si es alumno o coordinador) -->
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

        <!-- Botón para guardar cambios -->
        <button type="submit" id="btnGuardar">Guardar Cambios</button>

        <!-- Botón visible solo cuando se está editando -->
        <button type="button" onclick="cancelarEdicion()" id="btnCancelar" style="display:none;">
            Cancelar
        </button>
    </form>

    <hr style="margin:40px 0;">


    <!-- ==========================================================
         TABLA DE LISTADO DE USUARIOS
    =========================================================== -->
    <table border="2" cellpadding="5"
           style="margin:auto; color:var(--text-main); border-color:var(--outline-main);
                  width:95%; max-width:1500px;">

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

            <!-- Ciclo principal de usuarios enviados por el controlador -->
            <?php foreach ($usuarios as $u): ?>
                <tr>

                    <!-- Datos básicos -->
                    <td><?= $u['id_usuario']; ?></td>
                    <td><?= htmlspecialchars($u['matricula']); ?></td>
                    <td><?= htmlspecialchars($u['nombre'].' '.$u['apellido_pa'].' '.$u['apellido_ma']); ?></td>
                    <td><?= htmlspecialchars($u['correo']); ?></td>
                    <td><?= htmlspecialchars($u['telefono']); ?></td>
                    <td><?= ucfirst($u['genero']); ?></td>
                    <td><?= ucfirst($u['tipo_usuario']); ?></td>

                    <!-- Carrera si aplica -->
                    <td><?= htmlspecialchars($u['nombre_carrera'] ?? '-'); ?></td>

                    <!-- Estatus -->
                    <td><?= ucfirst($u['estatus']); ?></td>

                    <!-- Acciones disponibles -->
                    <td class="acciones">

                        <!-- No permitir editar al usuario que está actualmente logeado -->
                        <?php if ($u['id_usuario'] != $idUsuarioActual): ?>

                            <!-- Botón EDITAR -->
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

                            <!-- Botón INACTIVAR -->
                            <button onclick="inactivarUsuario(<?= $u['id_usuario']; ?>)">
                                Inactivar
                            </button>

                            <!-- Botón ELIMINAR -->
                            <button onclick="eliminarUsuario(<?= $u['id_usuario']; ?>)">
                                Eliminar
                            </button>

                        <?php else: ?>
                            <!-- Texto en lugar de acciones -->
                            <em style="opacity:0.6;">No editable</em>
                        <?php endif; ?>

                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    <!-- Volver al panel principal -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>




<!-- ==========================================================
     SCRIPTS DE LA VISTA
=========================================================== -->
<script>

    /* -------------------------------------------------------
       Mostrar/ocultar select de carrera según el tipo
    ------------------------------------------------------- */
    function toggleCarrera() {
        const tipo = document.getElementById('tipo_usuario').value;

        document.getElementById('campo_carrera').style.display =
            (tipo === 'alumno' || tipo === 'coordinador') ? 'block' : 'none';
    }


    /* -------------------------------------------------------
       Cargar datos al formulario al hacer clic en EDITAR
    ------------------------------------------------------- */
    function editarUsuario(id, nombre, ap_pa, ap_ma, correo, telefono, genero,
                           tipo, estatus, carrera) {

        // Cargar valores base
        document.querySelector("[name='id_usuario']").value = id;
        document.querySelector("[name='nombre']").value = nombre;
        document.querySelector("[name='apellido_pa']").value = ap_pa;
        document.querySelector("[name='apellido_ma']").value = ap_ma;
        document.querySelector("[name='correo']").value = correo;
        document.querySelector("[name='telefono']").value = telefono;
        document.querySelector("[name='genero']").value = genero;
        document.querySelector("[name='tipo_usuario']").value = tipo;
        document.querySelector("[name='estatus']").value = estatus;

        // Mostrar o esconder el campo carrera
        toggleCarrera();

        // Seleccionar carrera si aplica
        if (carrera) {
            const select = document.querySelector("[name='id_carrera']");
            [...select.options].forEach(o => { o.selected = (o.text === carrera); });
        }

        // Mostrar botón cancelar
        document.getElementById("btnCancelar").style.display = "inline-block";

        // Establecer acción de POST hacia editar
        document.getElementById("formUsuario").action = "<?= $actionEditar; ?>";

        // Subir al formulario para mejor UX
        window.scrollTo({ top: 0, behavior: "smooth" });
    }


    /* -------------------------------------------------------
       Cancelar edición → limpiar formulario
    ------------------------------------------------------- */
    function cancelarEdicion() {
        document.getElementById("formUsuario").reset();
        document.getElementById("btnCancelar").style.display = "none";
        toggleCarrera();
    }


    /* -------------------------------------------------------
       Inactivar usuario
    ------------------------------------------------------- */
    function inactivarUsuario(id) {
        if (confirm("¿Deseas marcar este usuario como INACTIVO?")) {
            window.location.href = "<?= $actionInactivar; ?>&id=" + id;
        }
    }


    /* -------------------------------------------------------
       Eliminar usuario
    ------------------------------------------------------- */
    function eliminarUsuario(id) {
        if (confirm("Esta acción eliminará permanentemente al usuario.\n¿Deseas continuar?")) {
            window.location.href = "<?= $actionEliminar; ?>&id=" + id;
        }
    }

</script>

</body>
</html>
