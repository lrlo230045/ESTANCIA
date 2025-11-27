<?php
class Pdf {
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión para ejecutar las consultas SQL
        $this->conexion = $conexion;
    }

    /* ============================================================
       CONSULTAR UNA SOLICITUD INDIVIDUAL PARA GENERAR pdfSolicitud()
       Retorna todos los datos necesarios para llenar el PDF
    ============================================================= */
    public function consultarSolicitudIndividual($id) {

        // Consulta completa que obtiene:
        // - datos del solicitante (alumno o coordinador)
        // - datos del material solicitado
        // - carrera, ubicación, observaciones, etc.
        $sql = "
            SELECT 
                s.id_solicitud,

                -- Nombre completo del solicitante
                CONCAT(u.nombre, ' ', u.apellido_pa, ' ', u.apellido_ma) AS solicitante,

                u.tipo_usuario AS tipo,
                u.correo,
                u.telefono,

                -- Carrera del usuario (alumno o coordinador)
                c.nombre_carrera AS carrera,

                -- Material solicitado
                m.nombre_material AS material,

                s.cantidad_solicitada AS cantidad,
                s.fecha_solicitud,
                s.observaciones,

                -- Ubicación física donde está el material
                ub.nombre_ubicacion AS ubicacion

            FROM solicitudes s

            -- Si la solicitud pertenece a alumno
            LEFT JOIN alumnos a 
                ON s.id_alumno = a.id_alumno

            -- Si la solicitud fue hecha por coordinador
            LEFT JOIN coordinadores co 
                ON s.id_coordinadores = co.id_coordinadores

            -- Obtiene al usuario según corresponda (alumno o coordinador)
            LEFT JOIN usuarios u 
                ON u.id_usuario = COALESCE(a.id_usuario, co.id_usuario)

            -- Obtiene carrera correspondiente
            LEFT JOIN carreras c 
                ON c.id_carrera = COALESCE(a.id_carrera, co.id_carrera)

            -- Información del material
            INNER JOIN materiales m 
                ON m.id_material = s.id_material

            -- Ubicación del material
            INNER JOIN ubicaciones ub 
                ON ub.id_ubicacion = m.id_ubicacion

            WHERE s.id_solicitud = ?
            LIMIT 1
        ";

        // Preparación segura de la consulta
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve un único registro
        return $stmt->get_result()->fetch_assoc();
    }

    /* ============================================================
       TOP DE MATERIALES MÁS SOLICITADOS
       Llama al procedimiento almacenado correspondiente
    ============================================================= */
    public function topMateriales() {
        // Limpia cualquier resultado previo para evitar errores al usar CALL
        $this->conexion->next_result(); 
        
        // Llama al procedimiento almacenado que genera estadísticas
        $resultado = $this->conexion->query("CALL sp_top_materiales(0)");

        // Devuelve resultados en arreglo asociativo
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /* ============================================================
       TOP DE CARRERAS CON MÁS SOLICITUDES
    ============================================================= */
    public function topCarreras() {
        // Limpia residuos del buffer de MySQL
        $this->conexion->next_result();

        // Llama al SP que trae estadísticas de carreras
        $resultado = $this->conexion->query("CALL sp_top_carreras(0)");

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /* ============================================================
       ESTADÍSTICAS DE SOLICITUDES POR GÉNERO
    ============================================================= */
    public function solicitudesGenero() {
        // Limpia resultados almacenados automáticamente por MySQL
        $this->conexion->next_result();

        // Ejecuta SP estadístico de género
        $resultado = $this->conexion->query("CALL sp_solicitudes_genero(0)");

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

}
?>
