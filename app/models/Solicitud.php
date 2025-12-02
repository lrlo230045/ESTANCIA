<?php
class Solicitud {
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión a la base de datos para uso en todos los métodos
        $this->conexion = $conexion;
    }

    /* ============================
       FUNCIONES INTERNAS
    ============================ */

    // Obtiene el id_alumno correspondiente a un id_usuario.
    // Se usa para que el alumno no envíe directamente su propio id_alumno.
    public function obtenerIdAlumnoPorUsuario($id_usuario) {
        $sql = "SELECT id_alumno FROM alumnos WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();

        // Usa fetch_assoc para obtener una fila
        $resultado = $stmt->get_result()->fetch_assoc();

        // Si existe el alumno, retorna id_alumno, si no, null
        return $resultado ? $resultado['id_alumno'] : null;
    }

    /* ============================
       FUNCIONES PARA ALUMNOS
    ============================ */

    // Obtiene todas las solicitudes hechas por un alumno (vía id_usuario)
    public function obtenerPorAlumno($id_usuario) {
        $sql = "SELECT s.id_solicitud,
                       m.nombre_material, 
                       s.cantidad_solicitada,
                       s.estado,
                       s.fecha_solicitud,
                       s.observaciones
                FROM solicitudes s
                INNER JOIN materiales m ON s.id_material = m.id_material
                INNER JOIN alumnos a ON s.id_alumno = a.id_alumno
                WHERE a.id_usuario = ?
                ORDER BY s.fecha_solicitud DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();

        // Devuelve todas las solicitudes como arreglo asociativo
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Inserta una nueva solicitud hecha por un alumno
    public function crear($id_usuario, $id_material, $cantidad, $observaciones) {
        // Obtiene id_alumno desde id_usuario
        $id_alumno = $this->obtenerIdAlumnoPorUsuario($id_usuario);

        // Si no existe el alumno, no puede crear solicitud
        if (!$id_alumno) return false;

        $sql = "INSERT INTO solicitudes (id_alumno, id_material, cantidad_solicitada, observaciones)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('iiis', $id_alumno, $id_material, $cantidad, $observaciones);

        return $stmt->execute();
    }

    /*
     * Obtiene una solicitud específica.
     */
    public function obtenerPorId($id, $id_usuario = null) {
        $sql = "SELECT s.*,
                       m.nombre_material
                FROM solicitudes s
                INNER JOIN materiales m ON s.id_material = m.id_material
                LEFT JOIN alumnos a ON s.id_alumno = a.id_alumno
                WHERE s.id_solicitud = ?";

        // Si es para un alumno, se valida que sea suya
        if ($id_usuario !== null) {
            $sql .= " AND a.id_usuario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('ii', $id, $id_usuario);
        } else {
            // Admin o coordinador
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('i', $id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Editar solicitud (solo si está pendiente)
    public function editar($id_solicitud, $id_material, $cantidad, $observaciones) {
        $sql = "UPDATE solicitudes 
                SET id_material = ?, cantidad_solicitada = ?, observaciones = ?
                WHERE id_solicitud = ? AND estado = 'pendiente'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('iisi', $id_material, $cantidad, $observaciones, $id_solicitud);

        return $stmt->execute();
    }

    // Cancela una solicitud si sigue pendiente
    public function cancelar($id) {
        $sql = "UPDATE solicitudes 
                SET estado = 'cancelada' 
                WHERE id_solicitud = ? AND estado = 'pendiente'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // affected_rows > 0 indica si la solicitud cambió de estado correctamente
        return $stmt->affected_rows > 0;
    }

    /* ============================
       FUNCIONES PARA ADMIN
    ============================ */

    // Obtiene todas las solicitudes con datos del usuario solicitante
    public function obtenerTodas() {
        $sql = "SELECT s.id_solicitud,
                       u.nombre,
                       u.apellido_pa,
                       u.apellido_ma,
                       m.nombre_material,
                       s.cantidad_solicitada,
                       s.estado,
                       s.fecha_solicitud,
                       s.observaciones
                FROM solicitudes s
                INNER JOIN materiales m ON s.id_material = m.id_material
                LEFT JOIN alumnos a ON s.id_alumno = a.id_alumno
                LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
                ORDER BY s.fecha_solicitud DESC";

        return $this->conexion->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Admin actualiza el estado de la solicitud
    public function editarEstado($id, $estado, $observaciones) {
        $sql = "UPDATE solicitudes 
                SET estado = ?, observaciones = ?
                WHERE id_solicitud = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ssi', $estado, $observaciones, $id);

        return $stmt->execute();
    }

    // Eliminar solicitud (solo admin)
    public function eliminar($id) {
        $sql = "DELETE FROM solicitudes WHERE id_solicitud = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    /* ======================================
       FUNCIONES PARA COORDINADORES
    ====================================== */

    // Obtiene solicitudes relacionadas a una carrera específica
    public function obtenerPorCarrera($id_carrera) {
        $sql = "
            SELECT 
                s.id_solicitud,
                s.estado,
                s.cantidad_solicitada,
                s.fecha_solicitud,
                s.observaciones,
                m.nombre_material,
                u.nombre AS nombre_usuario,
                u.apellido_pa,
                u.apellido_ma,
                u.tipo_usuario,
                c.nombre_carrera
            FROM solicitudes s
            INNER JOIN materiales m ON s.id_material = m.id_material
            LEFT JOIN alumnos a ON s.id_alumno = a.id_alumno
            LEFT JOIN coordinadores co ON s.id_coordinadores = co.id_coordinadores
            LEFT JOIN usuarios u ON u.id_usuario = COALESCE(a.id_usuario, co.id_usuario)
            LEFT JOIN carreras c ON c.id_carrera = COALESCE(a.id_carrera, co.id_carrera)
            WHERE c.id_carrera = ?
            ORDER BY s.fecha_solicitud DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Registrar solicitud hecha por un coordinador
    public function crearPorCoordinador($id_coordinadores, $id_material, $cantidad, $obs) {
        $sql = "INSERT INTO solicitudes (id_coordinadores, id_material, cantidad_solicitada, observaciones)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiis", $id_coordinadores, $id_material, $cantidad, $obs);

        return $stmt->execute();
    }

    // Editar solicitud creada por un coordinador (solo si está pendiente)
    public function editarPorCoordinador($id_solicitud, $id_material, $cantidad, $obs) {
        $sql = "UPDATE solicitudes 
                SET id_material = ?, cantidad_solicitada = ?, observaciones = ?
                WHERE id_solicitud = ? AND estado = 'pendiente'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iisi", $id_material, $cantidad, $obs, $id_solicitud);

        return $stmt->execute();
    }
}
?>
