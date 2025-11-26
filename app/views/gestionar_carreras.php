<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Carreras</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Gestionar Carreras</h2>

    <!-- Mensajes -->
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


    <!-- === FORMULARIO AGREGAR / EDITAR === -->
    <form id="formCarrera" method="POST" style="max-width:700px; margin:auto;">
        
        <input type="hidden" name="id_carrera" value="">
        
        <h3 id="tituloForm">Agregar Nueva Carrera</h3>

        <label>Nombre de la carrera:</label>
        <input type="text" name="nombre_carrera" required>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" style="width:100%; border-radius:10px;"></textarea>

        <br><br>
        <button type="submit" id="btnGuardar">Agregar</button>

        <button type="button" 
                onclick="cancelarEdicion()" 
                id="btnCancelar" 
                style="display:none;">
            Cancelar
        </button>

    </form>

    <hr style="margin:40px 0;">


    <!-- === TABLA DE CARRERAS === -->
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
            <?php foreach ($carreras as $c): ?>
                <tr>
                    <td><?= $c['id_carrera']; ?></td>
                    <td><?= htmlspecialchars($c['nombre_carrera']); ?></td>
                    <td><?= htmlspecialchars($c['descripcion']); ?></td>

                    <td>
                        <button 
                            onclick="editarCarrera(
                                '<?= $c['id_carrera']; ?>',
                                '<?= htmlspecialchars($c['nombre_carrera'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($c['descripcion'], ENT_QUOTES); ?>'
                            )">
                            Editar
                        </button>

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

    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


    <script>
        // Rutas provenientes del controlador
        const URL_AGREGAR = "<?= $actionAgregar ?>";
        const URL_EDITAR  = "<?= $actionEditar ?>";
        const URL_ELIMINAR = "<?= $actionEliminar ?>";

        // Cargar datos para editar
        function editarCarrera(id, nombre, descripcion) {
            document.querySelector("[name='id_carrera']").value = id;
            document.querySelector("[name='nombre_carrera']").value = nombre;
            document.querySelector("[name='descripcion']").value = descripcion;

            document.getElementById("tituloForm").innerText = "Editar Carrera";
            document.getElementById("btnGuardar").innerText = "Guardar Cambios";
            document.getElementById("btnCancelar").style.display = "inline-block";

            document.getElementById("formCarrera").action = URL_EDITAR;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Cancelar edición
        function cancelarEdicion() {
            document.getElementById("formCarrera").reset();
            document.getElementById("tituloForm").innerText = "Agregar Nueva Carrera";
            document.getElementById("btnGuardar").innerText = "Agregar";
            document.getElementById("btnCancelar").style.display = "none";

            document.getElementById("formCarrera").action = URL_AGREGAR;
        }

        // Confirmar eliminación
        function confirmarEliminacion(id) {
            if (confirm("ADVERTENCIA:\nEsta acción eliminará la carrera permanentemente.\n¿Deseas continuar?")) {
                window.location.href = URL_ELIMINAR + "&id=" + id;
            }
        }

        // Acción por defecto → agregar
        document.getElementById("formCarrera").action = URL_AGREGAR;
    </script>

</body>
</html>
