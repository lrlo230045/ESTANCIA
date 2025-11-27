<?php
class Material {
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión para realizar todas las operaciones del modelo
        $this->conexion = $conexion;
    }

    /* ============================
       CONSULTAS PÚBLICAS (Alumno)
       Devuelve solo materiales activos
    ============================ */
    public function obtenerActivos() {
        // Consulta materiales visibles para alumnos (solo activos)
        $sql = "SELECT m.id_material, m.nombre_material, m.descripcion, m.cantidad_disponible,
                       m.unidad_medida, u.nombre_ubicacion
                FROM materiales m
                INNER JOIN ubicaciones u ON m.id_ubicacion = u.id_ubicacion
                WHERE m.estado = 'activo'";

        // Ejecuta la consulta
        $resultado = $this->conexion->query($sql);

        // Retorna todas las filas como arreglo asociativo
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /* ============================
       CRUD ADMINISTRADOR
       Mantenimiento total de materiales
    ============================ */

    // Obtener todos los materiales (sin importar estado)
    public function obtenerTodos() {
        // Consulta completa con JOIN para mostrar la ubicación
        $sql = "SELECT m.id_material, m.nombre_material, m.descripcion, m.cantidad_disponible,
                       m.unidad_medida, u.nombre_ubicacion, m.estado, m.id_ubicacion
                FROM materiales m
                INNER JOIN ubicaciones u ON m.id_ubicacion = u.id_ubicacion
                ORDER BY m.nombre_material ASC";

        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Agregar un nuevo material
    public function agregar($nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado) {
        // Query parametrizada para evitar inyección SQL
        $sql = "INSERT INTO materiales (nombre_material, descripcion, cantidad_disponible, unidad_medida, id_ubicacion, estado)
                VALUES (?, ?, ?, ?, ?, ?)";

        // Prepara la consulta
        $stmt = $this->conexion->prepare($sql);

        // Enlaza parámetros en el orden definido
        // s = string, i = entero
        $stmt->bind_param("ssisis", $nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado);

        // Ejecuta y devuelve true/false
        return $stmt->execute();
    }

    // Editar un material existente
    public function editar($id, $nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado) {
        // Sentencia SQL para actualizar el material correspondiente
        $sql = "UPDATE materiales 
                SET nombre_material=?, descripcion=?, cantidad_disponible=?, unidad_medida=?, id_ubicacion=?, estado=? 
                WHERE id_material=?";

        // Preparar sentencia
        $stmt = $this->conexion->prepare($sql);

        // Enlazar parámetros con tipos correctos
        $stmt->bind_param("ssisisi", $nombre, $descripcion, $cantidad, $unidad, $id_ubicacion, $estado, $id);

        return $stmt->execute();
    }

    // Eliminar material de la base de datos
    public function eliminar($id) {
        // SQL para borrar por ID
        $sql = "DELETE FROM materiales WHERE id_material=?";

        // Preparar
        $stmt = $this->conexion->prepare($sql);

        // Enlazar ID de material
        $stmt->bind_param("i", $id);

        // Ejecutar eliminación
        return $stmt->execute();
    }

}
?>
