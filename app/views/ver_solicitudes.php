<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Solicitudes</title>

    <!-- ===============================
         ESTILOS Y TEMA DINÁMICO
    ================================ -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Mis Solicitudes</h2>

    <!-- ==========================================
         MENSAJE DE ÉXITO
    =========================================== -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta exito">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- ==========================================
         MENSAJE DE ERROR
    =========================================== -->
    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>


    <!-- ==========================================
         TABLA DE SOLICITUDES DEL ALUMNO
    =========================================== -->
    <table border="1" cellpadding="10"
           style="margin:auto; margin-top:30px; 
                  color:var(--text-main); border-color: var(--outline-main);">

        <thead>
            <tr>
                <th>ID Solicitud</th>
                <th>Material</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <!-- Si hay solicitudes -->
            <?php if (!empty($solicitudes)): ?>

                <?php foreach ($solicitudes as $s): ?>
                    <tr>

                        <!-- Datos principales -->
                        <td><?= htmlspecialchars($s['id_solicitud']); ?></td>
                        <td><?= htmlspecialchars($s['nombre_material']); ?></td>
                        <td><?= htmlspecialchars($s['cantidad_solicitada']); ?></td>
                        <td><?= ucfirst(htmlspecialchars($s['estado'])); ?></td>

                        <!-- Fecha formato día/mes/año hora:minuto -->
                        <td><?= date("d/m/Y H:i", strtotime($s['fecha_solicitud'])); ?></td>

                        <td><?= htmlspecialchars($s['observaciones']); ?></td>

                        <!-- ============================
                             ACCIONES DISPONIBLES
                             (Solo si está PENDIENTE)
                        ============================== -->
                        <td style="text-align:center;">

                            <?php if ($s['estado'] === 'pendiente'): ?>

                                <!-- Generar PDF -->
                                <button onclick="
                                    window.location.href='<?= $actionPDF ?>&id=<?= $s['id_solicitud']; ?>'
                                ">
                                    PDF
                                </button>

                                <!-- Editar solicitud -->
                                <button onclick="
                                    window.location.href='<?= $actionEditar ?>&id=<?= $s['id_solicitud']; ?>'
                                ">
                                    Editar
                                </button>

                                <!-- Cancelar solicitud -->
                                <button onclick="
                                    if(confirm('¿Deseas cancelar esta solicitud?')) 
                                        window.location.href='<?= $actionCancelar ?>&id=<?= $s['id_solicitud']; ?>'
                                ">
                                    Cancelar
                                </button>

                            <?php else: ?>

                                <!-- Estado ya no permite acciones -->
                                <button disabled style="opacity:0.5;">PDF</button>
                                <button disabled style="opacity:0.5;">Editar</button>
                                <button disabled style="opacity:0.5;">Cancelar</button>

                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>

            <!-- Si no hay solicitudes -->
            <?php else: ?>

                <tr>
                    <td colspan="7" style="text-align:center;">
                        No tienes solicitudes registradas.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>

    <br>

    <!-- Enlace para volver -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


    <!-- ==========================================
         ESTILOS INTERNOS
    =========================================== -->
    <style>
        button {
            margin: 3px;
            padding: 5px 15px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid var(--outline-main);
            background-color: var(--bg-btn);
            color: var(--text-main);
            cursor: pointer;
        }

        button:hover:not([disabled]) {
            transform: scale(1.05);
            box-shadow: 0 0 8px var(--outline-main);
        }
    </style>

</body>
</html>
