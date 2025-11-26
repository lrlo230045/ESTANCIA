<?php
class Estadisticas {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function getTopMateriales() {
        $res = $this->conexion->query("CALL sp_top_materiales(0)");
        $datos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $this->conexion->next_result();
        return $datos;
    }

    public function getTopCarreras() {
        $res = $this->conexion->query("CALL sp_top_carreras(0)");
        $datos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $this->conexion->next_result();
        return $datos;
    }

    public function getStatsGenero() {
        $res = $this->conexion->query("CALL sp_solicitudes_genero(0)");
        $datos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $this->conexion->next_result();
        return $datos;
    }
}
?>
