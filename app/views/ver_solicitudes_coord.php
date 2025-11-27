<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes de la Carrera</title>

    <!-- ===============================
         ESTILOS Y SCRIPT DEL TEMA
    ================================ -->
    <link id="theme-link" rel="stylesheet" href="assets/css/colores.css">
    <script src="assets/js/tema.js"></script>
</head>

<body>

    <h2>Solicitudes de la Carrera</h2>

    <!-- ==========================================
         MENSAJE DE ÉXITO (si viene desde controlador)
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
         TABLA PRINCIPAL DE SOLICITUDES
    =========================================== -->
    <table border="1" cellpadding="10"
           style="margin:auto; margin-top:30px; 
                  color:var(--text-main); border-color: var(--outline-main);">

        <thead>
            <tr>
                <th>ID Solicitud</th>
                <th>Solicitante</th>
                <th>Tipo</th>
                <th>Carrera</th>
                <th>Material</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <!-- Si existen solicitudes -->
            <?php if (!empty($solicitudes)): ?>

                <?php foreach ($solicitudes as $s): ?>
                    <tr>

                        <!-- ID -->
                        <td><?= $s['id_solicitud']; ?></td>

                        <!-- Nombre completo -->
                        <td>
                            <?= htmlspecialchars($s['nombre_usuario']
                                . " " . $s['apellido_pa']
                                . " " . $s['apellido_ma']); ?>
                        </td>

                        <!-- Tipo usuario: alumno / coordinador -->
                        <td><?= ucfirst($s['tipo_usuario']); ?></td>

                        <!-- Carrera -->
                        <td><?= htmlspecialchars($s['nombre_carrera']); ?></td>

                        <!-- Material -->
                        <td><?= htmlspecialchars($s['nombre_material']); ?></td>

                        <!-- Cantidad -->
                        <td><?= $s['cantidad_solicitada']; ?></td>

                        <!-- Estado -->
                        <td><?= ucfirst($s['estado']); ?></td>

                        <!-- Fecha formateada -->
                        <td><?= date("d/m/Y H:i", strtotime($s['fecha_solicitud'])); ?></td>

                        <!-- Observaciones -->
                        <td><?= htmlspecialchars($s['observaciones'] ?? ''); ?></td>

                        <!-- ================================
                             ACCIONES (solo si está pendiente)
                        ================================= -->
                        <td style="text-align:center;">

                            <?php if ($s['estado'] === 'pendiente'): ?>

                                <!-- EDITAR -->
                                <button onclick="
                                    window.location.href='<?= $actionEditar ?>&id=<?= $s['id_solicitud'] ?>'
                                ">
                                    Editar
                                </button>

                                <!-- CANCELAR -->
                                <button onclick="
                                    if(confirm('¿Deseas cancelar esta solicitud?'))
                                        window.location.href='<?= $actionCancelar ?>&id=<?= $s['id_solicitud'] ?>'
                                ">
                                    Cancelar
                                </button>

                            <?php else: ?>

                                <!-- Botones deshabilitados -->
                                <button disabled style="opacity:0.5;">Editar</button>
                                <button disabled style="opacity:0.5;">Cancelar</button>

                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>

            <!-- Si NO hay solicitudes -->
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align:center;">
                        No hay solicitudes registradas.
                    </td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

    <br>

    <!-- Enlace regresar -->
    <a href="<?= htmlspecialchars($volver); ?>">Volver al Panel</a>


    <!-- ==========================================
         ESTILOS INTERNOS (solo para esta vista)
    =========================================== -->
    <style>

        button {
            margin: 3px;
            padding: 6px 15px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid var(--outline-main);
            background-color: var(--bg-btn);
            color: var(--text-main);
            cursor: pointer;
            transition: 0.2s ease-in-out;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 8px var(--outline-main);
        }

        .alerta {
            margin: 15px auto;
            width: 60%;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
        }

        .alerta.exito {
            background-color: var(--success-bg);
            color: var(--success-text);
        }

        .alerta.error {
            background-color: var(--error-bg);
            color: var(--error-text);
        }

    </style>

</body>
</html>
