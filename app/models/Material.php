<?php
class Material {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /* ============================
       CONSULTAS PÚBLICAS (Alumno)
    ============================ */
    public function obtenerActivos() {
        $sql = "SELECT m.id_material, m.nombre_material, m.descripcion, m.cantidad_disponible,
                       m.unidad_medida, u.nombre_ubicacion
                FROM materiales m
                INNER JOIN ubicaciones u ON m.id_ubicacion = u.id_ubicacion
                WHERE m.estado = 'activo'";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /* ============================
       CRUD ADMINISTRADOR
    ============================ */

    public function obtenerTodos() {
        $sql = "SELECT m.id_material, m.nombre_material, m.descripcion, m.cantidad_disponible,
                       m.unidad_medida, u.nombre_ubicacion, m.estado, m.id_ubicacion
                FROM materiales m
                INNER JOIN ubicaciones u ON m.id_ubicacion = u.id_ubicacion
                ORDER BY m.nombre_material ASC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function agregar($nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado) {
        $sql = "INSERT INTO materiales (nombre_material, descripcion, cantidad_disponible, unidad_medida, id_ubicacion, estado)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssisis", $nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado);
        return $stmt->execute();
    }

    public function editar($id, $nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado) {
        $sql = "UPDATE materiales 
                SET nombre_material=?, descripcion=?, cantidad_disponible=?, unidad_medida=?, id_ubicacion=?, estado=? 
                WHERE id_material=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssisisi", $nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql = "DELETE FROM materiales WHERE id_material=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

}
?>
