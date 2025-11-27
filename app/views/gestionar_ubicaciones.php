<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Ubicaciones</title>

    <!-- Estilos globales del sistema (tema, colores) -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">

    <!-- Script para alternar entre tema claro/oscuro -->
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <!-- Título principal de la vista -->
    <h2>Gestionar Ubicaciones</h2>

    <!-- Mensaje que envía el controlador (éxito o aviso) -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>


    <!-- =====================================================
         FORMULARIO PARA AGREGAR / EDITAR UBICACIONES
    ====================================================== -->
    <form id="formUbicacion" action="<?= htmlspecialchars($actionAgregar); ?>" method="POST"
          style="max-width:700px; margin:auto;">

        <!-- Campo oculto: se llena al presionar "Editar" -->
        <input type="hidden" name="id_ubicacion" value="">

        <!-- Título dinámico del formulario -->
        <h3 id="tituloForm">Agregar Nueva Ubicación</h3>

        <!-- Nombre de la ubicación -->
        <label>Nombre:</label>
        <input type="text" name="nombre_ubicacion" required>

        <!-- Ubicación física -->
        <label>Ubicación Física:</label>
        <input type="text" name="ubicacion_fisica" required>

        <!-- Capacidad máxima -->
        <label>Capacidad:</label>
        <input type="number" name="capacidad" min="1" required>

        <!-- Descripción general de la ubicación -->
        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" style="width:100%; border-radius:10px;"></textarea>

        <br><br>

        <!-- Botón principal del formulario -->
        <button type="submit" id="btnGuardar">Agregar</button>

        <!-- Botón que solo aparece al editar -->
        <button type="button" onclick="cancelarEdicion()" id="btnCancelar"
                style="display:none;">Cancelar</button>
    </form>


    <!-- Separador visual -->
    <hr style="margin:40px 0;">


    <!-- =====================================================
         TABLA DE TODAS LAS UBICACIONES
         Muestra datos + botones dinámicos de editar/eliminar
    ====================================================== -->
    <table border="1" cellpadding="10"
           style="margin:auto; color:var(--text-main); border-color:var(--outline-main);">
        
        <!-- Encabezados -->
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
            <!-- Se recorre el arreglo que envía el controlador -->
            <?php foreach ($ubicaciones as $u): ?>
                <tr>
                    <!-- ID de la ubicación -->
                    <td><?= $u['id_ubicacion']; ?></td>

                    <!-- Campos con htmlspecialchars para seguridad -->
                    <td><?= htmlspecialchars($u['nombre_ubicacion']); ?></td>
                    <td><?= htmlspecialchars($u['ubicacion_fisica']); ?></td>
                    <td><?= $u['capacidad']; ?></td>
                    <td><?= htmlspecialchars($u['descripcion']); ?></td>

                    <td>
                        <!-- Botón EDITAR: envía valores al formulario -->
                        <button onclick="editarUbicacion(
                            '<?= $u['id_ubicacion']; ?>',
                            '<?= htmlspecialchars($u['nombre_ubicacion']); ?>',
                            '<?= htmlspecialchars($u['ubicacion_fisica']); ?>',
                            '<?= $u['capacidad']; ?>',
                            '<?= htmlspecialchars($u['descripcion']); ?>'
                        )">Editar</button>

                        <!-- Botón ELIMINAR: abre confirmación -->
                        <button onclick="confirmarEliminacion(<?= $u['id_ubicacion']; ?>)">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <br>

    <!-- Regresar al panel principal -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


    <!-- =====================================================
         SCRIPTS PARA MANIPULAR EL FORMULARIO Y ACCIONES
    ====================================================== -->
    <script>

    /* ---------------------------------------------------
       RUTAS ENVIADAS POR EL CONTROLADOR
    ---------------------------------------------------- */
    const urlEditar   = "<?= $actionEditar; ?>";
    const urlEliminar = "<?= $actionEliminar; ?>";
    const urlAgregar  = "<?= $actionAgregar; ?>";


    /* ---------------------------------------------------
       FUNCIÓN: Cargar datos en el formulario para edición
    ---------------------------------------------------- */
    function editarUbicacion(id, nombre, fisica, capacidad, desc) {

        // Insertar valores en el formulario
        document.querySelector("[name='id_ubicacion']").value = id;
        document.querySelector("[name='nombre_ubicacion']").value = nombre;
        document.querySelector("[name='ubicacion_fisica']").value = fisica;
        document.querySelector("[name='capacidad']").value = capacidad;
        document.querySelector("[name='descripcion']").value = desc;

        // Ajustar textos y botones
        document.getElementById("tituloForm").innerText = "Editar Ubicación";
        document.getElementById("btnGuardar").innerText = "Guardar Cambios";
        document.getElementById("btnCancelar").style.display = "inline-block";

        // Cambiar acción del formulario a "editar"
        document.getElementById("formUbicacion").action = urlEditar;

        // Subir al inicio del formulario para mejor UX
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }


    /* ---------------------------------------------------
       FUNCIÓN: Restaurar formulario al modo AGREGAR
    ---------------------------------------------------- */
    function cancelarEdicion() {

        // Limpiar campos
        document.getElementById("formUbicacion").reset();

        // Restaurar textos y botones
        document.getElementById("tituloForm").innerText = "Agregar Nueva Ubicación";
        document.getElementById("btnGuardar").innerText = "Agregar";
        document.getElementById("btnCancelar").style.display = "none";

        // Restaurar acción del formulario
        document.getElementById("formUbicacion").action = urlAgregar;
    }


    /* ---------------------------------------------------
       FUNCIÓN: Confirmar y ejecutar eliminación
    ---------------------------------------------------- */
    function confirmarEliminacion(id) {

        const confirmar = confirm(
            "ADVERTENCIA:\nEsta acción eliminará la ubicación permanentemente.\n¿Deseas continuar?"
        );

        if (confirmar) {
            // Concatenar ID y redirigir
            window.location.href = urlEliminar + "&id=" + id;
        }
    }

    </script>

</body>
</html>
