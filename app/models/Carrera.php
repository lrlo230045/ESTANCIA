<?php
class Carrera {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Obtener todas las carreras
    public function obtenerTodas() {
        $sql = "SELECT * FROM carreras ORDER BY nombre_carrera ASC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Agregar nueva carrera
    public function agregar($nombre, $descripcion) {
        $sql = "INSERT INTO carreras (nombre_carrera, descripcion) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $nombre, $descripcion);
        return $stmt->execute();
    }

    // Editar carrera existente
    public function editar($id, $nombre, $descripcion) {
        $sql = "UPDATE carreras SET nombre_carrera=?, descripcion=? WHERE id_carrera=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
        return $stmt->execute();
    }

    // Eliminar carrera
    public function eliminar($id) {
        $sql = "DELETE FROM carreras WHERE id_carrera=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
