<?php
require_once "config/Database.php";
require_once "app/models/Material.php";
require_once "app/models/Ubicacion.php";

class MaterialesAdminController {

    private $conexion;
    private $materialModel;
    private $ubicacionModel;

    public function __construct() {
        session_start();

        // Solo administradores pueden entrar
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        $this->conexion       = Database::getConnection();
        $this->materialModel  = new Material($this->conexion);
        $this->ubicacionModel = new Ubicacion($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* =====================================================
       GESTIONAR MATERIALES
    ===================================================== */
    public function gestionar() {

        $materiales  = $this->materialModel->obtenerTodos();
        $ubicaciones = $this->ubicacionModel->obtenerTodas();

        // Mensaje flash
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $this->render("gestionar_materiales", [
            "materiales"     => $materiales,
            "ubicaciones"    => $ubicaciones,
            "mensaje"        => $mensaje,

            // Enviamos rutas dinámicas a la vista
            "actionAgregar"  => "index.php?controller=materialesAdmin&action=agregar",
            "actionEditar"   => "index.php?controller=materialesAdmin&action=editar",
            "actionEliminar" => "index.php?controller=materialesAdmin&action=eliminar",
            "volver"         => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       AGREGAR MATERIAL
    ===================================================== */
    public function agregar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->materialModel->agregar(
                $_POST["nombre_material"],
                $_POST["descripcion"],
                $_POST["cantidad_disponible"],
                $_POST["unidad_medida"],
                $_POST["id_ubicacion"],
                $_POST["estado"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Material agregado correctamente"
                : "Error al agregar material";
        }

        header("Location: index.php?controller=materialesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       EDITAR MATERIAL
    ===================================================== */
    public function editar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->materialModel->editar(
                $_POST["id_material"],
                $_POST["nombre_material"],
                $_POST["descripcion"],
                $_POST["cantidad_disponible"],
                $_POST["unidad_medida"],
                $_POST["id_ubicacion"],
                $_POST["estado"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Material actualizado correctamente"
                : "Error al actualizar material";
        }

        header("Location: index.php?controller=materialesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR MATERIAL
    ===================================================== */
    public function eliminar() {

        $id = $_GET["id"] ?? null;

        if ($id) {

            $ok = $this->materialModel->eliminar($id);

            $_SESSION["mensaje"] = $ok
                ? "Material eliminado correctamente"
                : "Error: material en uso";
        }

        header("Location: index.php?controller=materialesAdmin&action=gestionar");
        exit();
    }
}
?>
