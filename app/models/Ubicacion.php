<?php
class Ubicacion {
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión a la base de datos para utilizarla en las consultas
        $this->conexion = $conexion;
    }

    /* =====================================================
       OBTENER TODAS LAS UBICACIONES
       Devuelve todas las ubicaciones en orden alfabético
    ===================================================== */
    public function obtenerTodas() {
        // Consulta simple sin parámetros
        $sql = "SELECT * FROM ubicaciones ORDER BY nombre_ubicacion ASC";

        // Ejecuta la consulta directamente
        $resultado = $this->conexion->query($sql);

        // Devuelve todas las filas como arreglo asociativo
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /* =====================================================
       AGREGAR NUEVA UBICACIÓN
       Inserta una nueva fila en la tabla ubicaciones
    ===================================================== */
    public function agregar($nombre, $ubicacion_fisica, $capacidad, $descripcion) {
        // Preparación de la sentencia con parámetros
        $sql = "INSERT INTO ubicaciones (nombre_ubicacion, ubicacion_fisica, capacidad, descripcion)
                VALUES (?, ?, ?, ?)";

        // Prepara la sentencia
        $stmt = $this->conexion->prepare($sql);

        // Enlaza los parámetros en el orden definido
        // s = string, i = entero
        $stmt->bind_param("ssis", $nombre, $ubicacion_fisica, $capacidad, $descripcion);

        // Ejecuta y retorna true/false según éxito
        return $stmt->execute();
    }

    /* =====================================================
       EDITAR UBICACIÓN EXISTENTE
       Actualiza datos de una ubicación mediante ID
    ===================================================== */
    public function editar($id, $nombre, $ubicacion_fisica, $capacidad, $descripcion) {
        // Sentencia SQL para actualizar una ubicación existente
        $sql = "UPDATE ubicaciones 
                SET nombre_ubicacion=?, ubicacion_fisica=?, capacidad=?, descripcion=?
                WHERE id_ubicacion=?";

        // Prepara la sentencia
        $stmt = $this->conexion->prepare($sql);

        // Enlaza los valores (string, string, int, string, int)
        $stmt->bind_param("ssisi", $nombre, $ubicacion_fisica, $capacidad, $descripcion, $id);

        // Ejecuta la consulta
        return $stmt->execute();
    }

    /* =====================================================
       ELIMINAR UBICACIÓN
       Elimina una ubicación si no tiene dependencias
    ===================================================== */
    public function eliminar($id) {
        // Query de eliminación
        $sql = "DELETE FROM ubicaciones WHERE id_ubicacion=?";

        // Prepara la consulta
        $stmt = $this->conexion->prepare($sql);

        // Enlaza el ID a eliminar
        $stmt->bind_param("i", $id);

        // Ejecuta y retorna true/false
        return $stmt->execute();
    }
}
?>
