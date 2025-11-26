<?php
class Coordinador {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT id_coordinadores, id_carrera 
                FROM coordinadores 
                WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
