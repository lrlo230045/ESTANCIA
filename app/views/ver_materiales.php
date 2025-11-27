<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Materiales</title>

    <!-- ============================================================
         HOJAS DE ESTILO Y SCRIPT DE TEMA (modo claro/oscuro)
    ============================================================ -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Materiales Disponibles</h2>

    <!-- ============================================================
         MENSAJE DE ÉXITO (se envía desde el controlador)
    ============================================================ -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- ============================================================
         MENSAJE DE ERROR (si algo sale mal)
    ============================================================ -->
    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- ============================================================
         TABLA DE MATERIALES
         - $materiales se envía desde el controlador
    ============================================================ -->
    <table border="1" cellpadding="10"
           style="margin:auto; margin-top:30px; 
                  color:var(--text-main); 
                  border-color: var(--outline-main);">

        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Cantidad Disponible</th>
                <th>Unidad de Medida</th>
                <th>Ubicación</th>
            </tr>
        </thead>

        <tbody>
            <!-- Recorrer materiales y mostrarlos -->
            <?php foreach ($materiales as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['nombre_material']); ?></td>
                    <td><?= htmlspecialchars($m['descripcion']); ?></td>
                    <td><?= htmlspecialchars($m['cantidad_disponible']); ?></td>
                    <td><?= htmlspecialchars($m['unidad_medida']); ?></td>
                    <td><?= htmlspecialchars($m['nombre_ubicacion']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    <!-- ============================================================
         BOTÓN PARA VOLVER AL PANEL PRINCIPAL
         - $volver viene desde el controlador
    ============================================================ -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>

</body>
</html>
