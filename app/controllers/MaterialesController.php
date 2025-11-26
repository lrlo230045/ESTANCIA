<?php
require_once "config/Database.php";
require_once "app/models/Material.php";

class MaterialesController {

    private $conexion;
    private $materialModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->conexion = Database::getConnection();
        $this->materialModel = new Material($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    public function verMateriales() {

        // Obtener mensaje desde sesión
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        // Error opcional (por si algo falla en el futuro)
        $error = null;

        // Obtener materiales activos
        $materiales = $this->materialModel->obtenerActivos();

        // Ruta dinámica para regresar al panel del alumno
        $volver = "index.php?controller=dashboard&action=panel";

        $this->render("ver_materiales", [
            "materiales" => $materiales,
            "mensaje"    => $mensaje,
            "error"      => $error,
            "volver"     => $volver
        ]);
    }
}
?>
