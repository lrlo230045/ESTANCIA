<?php
require_once "config/Database.php";
require_once "app/models/Carrera.php";

class CarrerasAdminController {

    private $conexion;
    private $model;

    public function __construct() {
        session_start();

        // Validación de sesión
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        $this->conexion = Database::getConnection();
        $this->model = new Carrera($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* =====================================================
       MOSTRAR GESTIÓN
    ===================================================== */
    public function gestionar() {

        $carreras = $this->model->obtenerTodas();

        // Recuperar mensaje de sesión
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $this->render("gestionar_carreras", [
            "carreras"       => $carreras,
            "mensaje"        => $mensaje,
            "actionAgregar"  => "index.php?controller=carrerasAdmin&action=agregar",
            "actionEditar"   => "index.php?controller=carrerasAdmin&action=editar",
            "actionEliminar" => "index.php?controller=carrerasAdmin&action=eliminar",
            "volver"         => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       AGREGAR CARRERA
    ===================================================== */
    public function agregar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->model->agregar(
                $_POST["nombre_carrera"],
                $_POST["descripcion"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Carrera agregada correctamente"
                : "Error al agregar carrera";
        }

        header("Location: index.php?controller=carrerasAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       EDITAR CARRERA
    ===================================================== */
    public function editar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->model->editar(
                $_POST["id_carrera"],
                $_POST["nombre_carrera"],
                $_POST["descripcion"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Carrera actualizada correctamente"
                : "Error al actualizar carrera";
        }

        header("Location: index.php?controller=carrerasAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR CARRERA
    ===================================================== */
    public function eliminar() {

        $id = $_GET["id"] ?? null;

        if ($id) {

            $ok = $this->model->eliminar($id);

            $_SESSION["mensaje"] = $ok
                ? "Carrera eliminada correctamente"
                : "Error: la carrera tiene dependencias";
        }

        header("Location: index.php?controller=carrerasAdmin&action=gestionar");
        exit();
    }
}
?>
