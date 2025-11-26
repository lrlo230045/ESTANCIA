<?php
class Pdf {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function consultarSolicitudIndividual($id) {

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

                -- Material
                m.nombre_material AS material,

                s.cantidad_solicitada AS cantidad,
                s.fecha_solicitud,
                s.observaciones,

                -- Ubicacion del material
                ub.nombre_ubicacion AS ubicacion

            FROM solicitudes s
            LEFT JOIN alumnos a 
                ON s.id_alumno = a.id_alumno

            LEFT JOIN coordinadores co 
                ON s.id_coordinadores = co.id_coordinadores

            LEFT JOIN usuarios u 
                ON u.id_usuario = COALESCE(a.id_usuario, co.id_usuario)

            LEFT JOIN carreras c 
                ON c.id_carrera = COALESCE(a.id_carrera, co.id_carrera)

            INNER JOIN materiales m 
                ON m.id_material = s.id_material

            INNER JOIN ubicaciones ub 
                ON ub.id_ubicacion = m.id_ubicacion

            WHERE s.id_solicitud = ?
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
public function topMateriales() {
    $this->conexion->next_result(); 
    $resultado = $this->conexion->query("CALL sp_top_materiales(0)");
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

public function topCarreras() {
    $this->conexion->next_result();
    $resultado = $this->conexion->query("CALL sp_top_carreras(0)");
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

public function solicitudesGenero() {
    $this->conexion->next_result();
    $resultado = $this->conexion->query("CALL sp_solicitudes_genero(0)");
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

}
