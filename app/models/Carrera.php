<?php
class Carrera {
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión a la base de datos para ser usada en todos los métodos
        $this->conexion = $conexion;
    }

    /* =====================================================
       OBTENER TODAS LAS CARRERAS
       Devuelve todas las carreras ordenadas alfabéticamente
    ===================================================== */
    public function obtenerTodas() {
        // Consulta simple para traer todas las carreras
        $sql = "SELECT * FROM carreras ORDER BY nombre_carrera ASC";

        // Ejecuta la consulta directamente (sin parámetros)
        $resultado = $this->conexion->query($sql);

        // Retorna todas las filas como arreglo asociativo
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /* =====================================================
       AGREGAR NUEVA CARRERA
       Inserta una carrera con nombre y descripción
    ===================================================== */
    public function agregar($nombre, $descripcion) {
        // Query con parámetros para evitar inyección SQL
        $sql = "INSERT INTO carreras (nombre_carrera, descripcion) VALUES (?, ?)";

        // Prepara la consulta
        $stmt = $this->conexion->prepare($sql);

        // Enlaza parámetros: ambos son cadenas (ss)
        $stmt->bind_param("ss", $nombre, $descripcion);

        // Ejecuta y retorna true/false según el resultado
        return $stmt->execute();
    }

    /* =====================================================
       EDITAR UNA CARRERA EXISTENTE
       Actualiza nombre y descripción por ID
    ===================================================== */
    public function editar($id, $nombre, $descripcion) {
        // Query parametrizada para modificar la carrera correspondiente
        $sql = "UPDATE carreras SET nombre_carrera=?, descripcion=? WHERE id_carrera=?";

        // Prepara la sentencia
        $stmt = $this->conexion->prepare($sql);

        // Enlaza parámetros: nombre (string), descripción (string), id (entero)
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);

        // Ejecuta la actualización
        return $stmt->execute();
    }

    /* =====================================================
       ELIMINAR UNA CARRERA
       Elimina una carrera por su ID
    ===================================================== */
    public function eliminar($id) {
        // Query de eliminación por ID
        $sql = "DELETE FROM carreras WHERE id_carrera=?";

        // Prepara la consulta
        $stmt = $this->conexion->prepare($sql);

        // Enlaza el ID (entero)
        $stmt->bind_param("i", $id);

        // Ejecuta la eliminación
        return $stmt->execute();
    }
}
?>
