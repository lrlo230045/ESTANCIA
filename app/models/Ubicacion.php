<?php
class Ubicacion {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerTodas() {
        $sql = "SELECT * FROM ubicaciones ORDER BY nombre_ubicacion ASC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function agregar($nombre, $ubicacion_fisica, $capacidad, $descripcion) {
        $sql = "INSERT INTO ubicaciones (nombre_ubicacion, ubicacion_fisica, capacidad, descripcion)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssis", $nombre, $ubicacion_fisica, $capacidad, $descripcion);
        return $stmt->execute();
    }

    public function editar($id, $nombre, $ubicacion_fisica, $capacidad, $descripcion) {
        $sql = "UPDATE ubicaciones 
                SET nombre_ubicacion=?, ubicacion_fisica=?, capacidad=?, descripcion=?
                WHERE id_ubicacion=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssisi", $nombre, $ubicacion_fisica, $capacidad, $descripcion, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql = "DELETE FROM ubicaciones WHERE id_ubicacion=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
