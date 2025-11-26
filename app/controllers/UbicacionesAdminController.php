<?php
require_once "config/Database.php";
require_once "app/models/Ubicacion.php";

class UbicacionesAdminController {

    private $conexion;
    private $ubicacionModel;

    public function __construct() {
        session_start();

        // Validación de rol
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        $this->conexion = Database::getConnection();
        $this->ubicacionModel = new Ubicacion($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* =====================================================
       GESTIONAR UBICACIONES
    ===================================================== */
    public function gestionar() {

        $ubicaciones = $this->ubicacionModel->obtenerTodas();

        // Recuperar mensaje y limpiarlo
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $this->render("gestionar_ubicaciones", [
            "ubicaciones"    => $ubicaciones,
            "mensaje"        => $mensaje,

            // rutas para la vista
            "actionAgregar"  => "index.php?controller=ubicacionesAdmin&action=agregar",
            "actionEditar"   => "index.php?controller=ubicacionesAdmin&action=editar",
            "actionEliminar" => "index.php?controller=ubicacionesAdmin&action=eliminar",
            "volver"         => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       AGREGAR UBICACIÓN
    ===================================================== */
    public function agregar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->ubicacionModel->agregar(
                $_POST["nombre_ubicacion"],
                $_POST["ubicacion_fisica"],
                $_POST["capacidad"],
                $_POST["descripcion"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Ubicación agregada correctamente"
                : "Error al agregar ubicación";
        }

        header("Location: index.php?controller=ubicacionesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       EDITAR UBICACIÓN
    ===================================================== */
    public function editar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->ubicacionModel->editar(
                $_POST["id_ubicacion"],
                $_POST["nombre_ubicacion"],
                $_POST["ubicacion_fisica"],
                $_POST["capacidad"],
                $_POST["descripcion"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Ubicación actualizada correctamente"
                : "Error al actualizar ubicación";
        }

        header("Location: index.php?controller=ubicacionesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR UBICACIÓN
    ===================================================== */
    public function eliminar() {

        $id = $_GET["id"] ?? null;

        if ($id) {

            $ok = $this->ubicacionModel->eliminar($id);

            $_SESSION["mensaje"] = $ok
                ? "Ubicación eliminada correctamente"
                : "Error: la ubicación tiene dependencias";
        }

        header("Location: index.php?controller=ubicacionesAdmin&action=gestionar");
        exit();
    }
}
?>
