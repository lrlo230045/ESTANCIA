<?php
class Estadisticas {

    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión para ejecutar procedimientos almacenados
        $this->conexion = $conexion;
    }

    /* =====================================================
       OBTENER MATERIALES MÁS SOLICITADOS
       Ejecuta el procedimiento almacenado sp_top_materiales
    ===================================================== */
    public function getTopMateriales() {

        // Ejecuta el procedimiento almacenado con parámetro 0
        $res = $this->conexion->query("CALL sp_top_materiales(0)");

        // Si existe resultado, obtiene todas las filas como arreglo asociativo
        $datos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Necesario cuando se usan procedimientos almacenados para limpiar buffer
        $this->conexion->next_result();

        // Retorna los datos al controlador
        return $datos;
    }

    /* =====================================================
       OBTENER CARRERAS CON MÁS SOLICITUDES
       Ejecuta el procedimiento almacenado sp_top_carreras
    ===================================================== */
    public function getTopCarreras() {

        // Llamada al procedimiento almacenado
        $res = $this->conexion->query("CALL sp_top_carreras(0)");

        // Obtiene datos o arreglo vacío si no hay resultados
        $datos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Limpia resultados pendientes del procedimiento
        $this->conexion->next_result();

        return $datos;
    }

    /* =====================================================
       OBTENER ESTADÍSTICAS DE SOLICITUDES POR GÉNERO
       Ejecuta el procedimiento almacenado sp_solicitudes_genero
    ===================================================== */
    public function getStatsGenero() {

        // Llamada al SP que devuelve estadísticas por género
        $res = $this->conexion->query("CALL sp_solicitudes_genero(0)");

        // Si hay datos, los convierte en arreglo asociativo
        $datos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Limpia el buffer tras usar un procedimiento almacenado
        $this->conexion->next_result();

        return $datos;
    }
}
?>
