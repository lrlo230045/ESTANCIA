<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>    
</head>

<body class="vista-registro">

    <div class="registro-box">

        <h2>Registro de Usuario</h2>

        <!-- MENSAJE DE CONTROLADOR -->
        <?php if (!empty($mensaje)): ?>
            <div class="alerta exito">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <!-- ERROR DEL CONTROLADOR -->
        <?php if (!empty($error)): ?>
            <div class="alerta error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO -->
        <form action="<?= htmlspecialchars($action) ?>" method="POST">

            <label>Nombre:</label>
            <input type="text" name="nombre" required>

            <label>Apellido Paterno:</label>
            <input type="text" name="apellido_pa" required>

            <label>Apellido Materno:</label>
            <input type="text" name="apellido_ma">

            <label>Correo:</label>
            <input type="email" name="correo" required>

            <label>Teléfono:</label>
            <input type="tel" 
                   name="telefono" 
                   required 
                   pattern="\d{3} \d{3} \d{4}" 
                   placeholder="Formato: 777 123 4567">

            <label>Matrícula:</label>
            <input type="text" name="matricula" required>

            <label>Contraseña:</label>
            <input type="password" name="contrasena" required>

            <label>Género:</label>
            <select name="genero" required>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="otro">Otro</option>
            </select>

            <label>Tipo de usuario:</label>
            <select name="tipo_usuario" id="tipo_usuario" required onchange="toggleCarrera()">
                <option value="">Selecciona un tipo</option>
                <option value="alumno">Alumno</option>
                <option value="coordinador">Coordinador</option>
                <option value="administrador">Administrador</option>
            </select>

            <!-- CAMPO CARRERA DINÁMICO -->
            <div id="campo_carrera" style="display:none;">
                <label>Carrera:</label>
                <select name="id_carrera">
                    <option value="">Selecciona una carrera</option>

                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>">
                            <?= htmlspecialchars($c['nombre_carrera']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <button type="submit">Registrarse</button>
        </form>

        <p>
            <a href="<?= htmlspecialchars($volver) ?>">Volver al Panel</a>
        </p>

    </div>

    <script>
        function toggleCarrera() {
            const tipo = document.getElementById('tipo_usuario').value;
            document.getElementById('campo_carrera').style.display =
                (tipo === 'alumno' || tipo === 'coordinador') ? 'block' : 'none';
        }
    </script>

</body>
</html>
