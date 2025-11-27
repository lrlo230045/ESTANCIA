<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Carreras</title>

    <!-- Hoja de estilos del tema del sistema -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script que controla el modo claro/oscuro -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Gestionar Carreras</h2>

    <!-- Mensaje general de éxito, si existe -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Mensaje de error, si existe -->
    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>


    <!-- =====================================================
         FORMULARIO AGREGAR / EDITAR CARRERA
         Este formulario sirve para ambas funciones.
         El controlador define las rutas dinámicas.
    ====================================================== -->
    <form id="formCarrera" method="POST" style="max-width:700px; margin:auto;">
        
        <!-- Campo oculto usado para editar -->
        <input type="hidden" name="id_carrera" value="">
        
        <!-- Texto dinámico: cambia entre "Agregar" y "Editar" -->
        <h3 id="tituloForm">Agregar Nueva Carrera</h3>

        <!-- Nombre de la carrera -->
        <label>Nombre de la carrera:</label>
        <input type="text" name="nombre_carrera" required>

        <!-- Descripción de la carrera -->
        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" style="width:100%; border-radius:10px;"></textarea>

        <br><br>

        <!-- Botón principal: cambia entre Agregar / Guardar Cambios -->
        <button type="submit" id="btnGuardar">Agregar</button>

        <!-- Botón para cancelar edición, oculto por defecto -->
        <button type="button" 
                onclick="cancelarEdicion()" 
                id="btnCancelar" 
                style="display:none;">
            Cancelar
        </button>

    </form>

    <!-- Separador visual -->
    <hr style="margin:40px 0;">


    <!-- =====================================================
         TABLA DE CARRERAS REGISTRADAS
         Cada fila permite editar o eliminar una carrera
    ====================================================== -->
    <table border="1" cellpadding="10" 
           style="margin:auto; color:var(--text-main); border-color:var(--outline-main);">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de Carrera</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <!-- Recorre todas las carreras enviadas desde el controlador -->
            <?php foreach ($carreras as $c): ?>
                <tr>
                    <td><?= $c['id_carrera']; ?></td>
                    <td><?= htmlspecialchars($c['nombre_carrera']); ?></td>
                    <td><?= htmlspecialchars($c['descripcion']); ?></td>

                    <td>
                        <!-- Botón para cargar datos dentro del formulario -->
                        <button 
                            onclick="editarCarrera(
                                '<?= $c['id_carrera']; ?>',
                                '<?= htmlspecialchars($c['nombre_carrera'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($c['descripcion'], ENT_QUOTES); ?>'
                            )">
                            Editar
                        </button>

                        <!-- Botón para ejecutar eliminación -->
                        <button 
                            onclick="confirmarEliminacion(<?= $c['id_carrera']; ?>)">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <br>

    <!-- Link para regresar al panel del administrador -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


    <!-- =====================================================
         JAVASCRIPT: Funciones dinámicas del formulario
    ====================================================== -->
    <script>
        // Rutas enviadas desde el controlador: agregar, editar, eliminar
        const URL_AGREGAR = "<?= $actionAgregar ?>";
        const URL_EDITAR  = "<?= $actionEditar ?>";
        const URL_ELIMINAR = "<?= $actionEliminar ?>";

        /* ----------------------------------------------------
           FUNCION: editarCarrera
           Carga los datos seleccionados dentro del formulario,
           cambia textos y activa modo edición.
        ---------------------------------------------------- */
        function editarCarrera(id, nombre, descripcion) {
            document.querySelector("[name='id_carrera']").value = id;
            document.querySelector("[name='nombre_carrera']").value = nombre;
            document.querySelector("[name='descripcion']").value = descripcion;

            // Cambia las etiquetas del formulario a modo edición
            document.getElementById("tituloForm").innerText = "Editar Carrera";
            document.getElementById("btnGuardar").innerText = "Guardar Cambios";
            document.getElementById("btnCancelar").style.display = "inline-block";

            // El formulario enviará a la URL de edición
            document.getElementById("formCarrera").action = URL_EDITAR;

            // Mover la página hacia arriba
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        /* ----------------------------------------------------
           FUNCION: cancelarEdicion
           Vuelve el formulario a modo agregar.
        ---------------------------------------------------- */
        function cancelarEdicion() {
            document.getElementById("formCarrera").reset();
            document.getElementById("tituloForm").innerText = "Agregar Nueva Carrera";
            document.getElementById("btnGuardar").innerText = "Agregar";
            document.getElementById("btnCancelar").style.display = "none";

            // Restablecer acción del formulario
            document.getElementById("formCarrera").action = URL_AGREGAR;
        }

        /* ----------------------------------------------------
           FUNCION: confirmarEliminacion
           Muestra confirmación antes de eliminar una carrera.
        ---------------------------------------------------- */
        function confirmarEliminacion(id) {
            if (confirm("ADVERTENCIA:\nEsta acción eliminará la carrera permanentemente.\n¿Deseas continuar?")) {
                window.location.href = URL_ELIMINAR + "&id=" + id;
            }
        }

        // Acción inicial cuando la vista carga: modo agregar
        document.getElementById("formCarrera").action = URL_AGREGAR;
    </script>

</body>
</html>
