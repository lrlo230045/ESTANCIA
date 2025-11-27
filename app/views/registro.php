<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>

    <!-- ============================================================
         HOJAS DE ESTILO Y TEMA (modo claro/oscuro)
    ============================================================ -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>    
</head>
<body class="vista-registro">

    <div class="registro-box">

        <h2>Registro de Usuario</h2>

        <!-- ============================================================
             MENSAJE DE ÉXITO (si el controlador envía $mensaje)
        ============================================================ -->
        <?php if (!empty($mensaje)): ?>
            <div class="alerta exito">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <!-- ============================================================
             MENSAJE DE ERROR (si el controlador envía $error)
        ============================================================ -->
        <?php if (!empty($error)): ?>
            <div class="alerta error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>


        <!-- ============================================================
             FORMULARIO DE REGISTRO
             - $action viene del controlador (registroAdmin / registro general)
        ============================================================ -->
        <form action="<?= htmlspecialchars($action) ?>" method="POST">

            <!-- Nombre -->
            <label>Nombre:</label>
            <input type="text" name="nombre" required>

            <!-- Apellido paterno -->
            <label>Apellido Paterno:</label>
            <input type="text" name="apellido_pa" required>

            <!-- Apellido materno -->
            <label>Apellido Materno:</label>
            <input type="text" name="apellido_ma">

            <!-- Correo -->
            <label>Correo:</label>
            <input type="email" name="correo" required>

            <!-- Teléfono con validación HTML5 -->
            <label>Teléfono:</label>
            <input type="tel" 
                   name="telefono" 
                   required 
                   pattern="\d{3} \d{3} \d{4}" 
                   placeholder="Formato: 777 123 4567">

            <!-- Matrícula -->
            <label>Matrícula:</label>
            <input type="text" name="matricula" required>

            <!-- Contraseña -->
            <label>Contraseña:</label>
            <input type="password" name="contrasena" required>

            <!-- Género -->
            <label>Género:</label>
            <select name="genero" required>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="otro">Otro</option>
            </select>

            <!-- Tipo de usuario -->
            <label>Tipo de usuario:</label>
            <select name="tipo_usuario" id="tipo_usuario" required onchange="toggleCarrera()">
                <option value="">Selecciona un tipo</option>
                <option value="alumno">Alumno</option>
                <option value="coordinador">Coordinador</option>
                <option value="administrador">Administrador</option>
            </select>

            <!-- ============================================================
                 CAMPO CARRERA (solo ALUMNO y COORDINADOR lo usan)
            ============================================================ -->
            <div id="campo_carrera" style="display:none;">
                <label>Carrera:</label>

                <select name="id_carrera">
                    <option value="">Selecciona una carrera</option>

                    <!-- Lista de carreras enviada por el controlador -->
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>">
                            <?= htmlspecialchars($c['nombre_carrera']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <!-- Botón principal -->
            <button type="submit">Registrarse</button>

        </form>

        <!-- Enlace para volver -->
        <p>
            <a href="<?= htmlspecialchars($volver) ?>">Volver al Panel</a>
        </p>

    </div>

    <!-- ============================================================
         SCRIPT PARA MOSTRAR U OCULTAR "CARRERA"
         según el tipo seleccionado
    ============================================================ -->
    <script>
        function toggleCarrera() {
            const tipo = document.getElementById('tipo_usuario').value;
            document.getElementById('campo_carrera').style.display =
                (tipo === 'alumno' || tipo === 'coordinador') ? 'block' : 'none';
        }
    </script>

</body>
</html>
