<?php
require_once "config/Database.php";
require_once "app/models/Solicitud.php";

class SolicitudesAdminController {

    private $conexion;
    private $solicitudModel;

    public function __construct() {
        session_start();

        // Validación de rol
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        $this->conexion       = Database::getConnection();
        $this->solicitudModel = new Solicitud($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* =====================================================
       GESTIONAR SOLICITUDES (LISTADO + FORMULARIO)
    ===================================================== */
    public function gestionar() {

        $solicitudes = $this->solicitudModel->obtenerTodas();

        // Mensaje de sesión (si existe)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        // PASAR RUTAS CON CAPITALIZACIÓN CORRECTA
        $actionEditar   = "index.php?controller=SolicitudesAdmin&action=editar";
        $actionEliminar = "index.php?controller=SolicitudesAdmin&action=eliminar";
        $volver         = "index.php?controller=Dashboard&action=panel";

        $this->render("gestionar_solicitudes", [
            "solicitudes"    => $solicitudes,
            "mensaje"        => $mensaje,
            "actionEditar"   => $actionEditar,
            "actionEliminar" => $actionEliminar,
            "volver"         => $volver
        ]);
    }

    /* =====================================================
       EDITAR ESTADO DE SOLICITUD
    ===================================================== */
    public function editar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->solicitudModel->editarEstado(
                intval($_POST["id_solicitud"]),
                $_POST["estado"],
                $_POST["observaciones"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Solicitud actualizada correctamente"
                : "Error al actualizar la solicitud";
        }

        // 🔥 CAPITALIZADO CORRECTO
        header("Location: index.php?controller=SolicitudesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR SOLICITUD
    ===================================================== */
    public function eliminar() {

        $id = isset($_GET["id"]) ? intval($_GET["id"]) : null;

        if ($id) {
            $ok = $this->solicitudModel->eliminar($id);

            $_SESSION["mensaje"] = $ok
                ? "Solicitud eliminada correctamente"
                : "Error: no se pudo eliminar la solicitud";
        }

        // 🔥 CAPITALIZADO CORRECTO
        header("Location: index.php?controller=SolicitudesAdmin&action=gestionar");
        exit();
    }
}
?>
