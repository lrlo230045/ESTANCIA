<?php
class Coordinador {
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión a la base de datos para usarla en consultas
        $this->conexion = $conexion;
    }
    
    // OBTENER INFORMACIÓN DEL COORDINADOR POR ID DE USUARIO
    public function obtenerPorUsuario($id_usuario) {

        // Consulta que obtiene el id del coordinador y su carrera
        // La tabla coordinadores vincula usuarios con carreras
        $sql = "SELECT id_coordinadores, id_carrera 
                FROM coordinadores 
                WHERE id_usuario = ?";

        // Prepara la consulta para evitar inyecciones SQL
        $stmt = $this->conexion->prepare($sql);

        // Enlaza el parámetro (entero)
        $stmt->bind_param("i", $id_usuario);

        // Ejecuta la consulta
        $stmt->execute();

        // Retorna el resultado en arreglo asociativo
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
