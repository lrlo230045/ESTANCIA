<?php
require_once "config/Database.php";
require_once "app/models/Solicitud.php";
require_once "app/models/Material.php";

class SolicitudesController {

    private $conexion;
    private $solicitudModel;
    private $materialModel;

    public function __construct() {
        session_start();

        // Validación de acceso
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "alumno") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Conexión y modelos
        $this->conexion       = Database::getConnection();
        $this->solicitudModel = new Solicitud($this->conexion);
        $this->materialModel  = new Material($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* =====================================================
       VER SOLICITUDES DEL ALUMNO
    ===================================================== */
    public function verSolicitudes() {

        $idUsuario   = $_SESSION["id_usuario"];
        $solicitudes = $this->solicitudModel->obtenerPorAlumno($idUsuario);

        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        // Rutas para evitar hardcode:
        $actionPDF      = "index.php?controller=pdf&action=pdfSolicitud";
        $actionEditar   = "index.php?controller=solicitudes&action=editarSolicitud";
        $actionCancelar = "index.php?controller=solicitudes&action=cancelarSolicitud";
        $volver         = "index.php?controller=dashboard&action=panel";

        $this->render("ver_solicitudes", [
            "solicitudes"    => $solicitudes,
            "mensaje"        => $mensaje,
            "error"          => null,
            "actionPDF"      => $actionPDF,
            "actionEditar"   => $actionEditar,
            "actionCancelar" => $actionCancelar,
            "volver"         => $volver
        ]);
    }

    /* =====================================================
       CREAR SOLICITUD
    ===================================================== */
    public function crearSolicitud() {

        $materiales = $this->materialModel->obtenerActivos();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->solicitudModel->crear(
                $_SESSION["id_usuario"],
                $_POST["id_material"],
                $_POST["cantidad_solicitada"],
                $_POST["observaciones"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Solicitud creada correctamente"
                : "Error al crear la solicitud";

            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        $this->render("crear_solicitud", [
            "materiales" => $materiales,
            "action"     => "index.php?controller=solicitudes&action=crearSolicitud",
            "volver"     => "index.php?controller=dashboard&action=panel",
            "mensaje"    => $_SESSION["mensaje"] ?? null
        ]);
    }

    /* =====================================================
       EDITAR SOLICITUD
    ===================================================== */
    public function editarSolicitud() {

        $id = $_GET["id"] ?? null;

        if (!$id) {
            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        // Validar que la solicitud pertenece al alumno
        $solicitud = $this->solicitudModel->obtenerPorId($id, $_SESSION["id_usuario"]);

        if (!$solicitud) {
            $_SESSION["mensaje"] = "No tienes permiso para editar esta solicitud.";
            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        $materiales = $this->materialModel->obtenerActivos();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->solicitudModel->editar(
                $id,
                $_POST["id_material"],
                $_POST["cantidad_solicitada"],
                $_POST["observaciones"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Solicitud actualizada correctamente"
                : "Error al actualizar solicitud";

            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        $this->render("editar_solicitud", [
            "solicitud"  => $solicitud,
            "materiales" => $materiales,
            "action"     => "index.php?controller=solicitudes&action=editarSolicitud&id=$id",
            "volver"     => "index.php?controller=solicitudes&action=verSolicitudes"
        ]);
    }

    /* =====================================================
       CANCELAR SOLICITUD
    ===================================================== */
    public function cancelarSolicitud() {

        $id = $_GET["id"] ?? null;

        if ($id) {

            // Asegurar pertenencia
            $solicitud = $this->solicitudModel->obtenerPorId($id, $_SESSION["id_usuario"]);

            if (!$solicitud) {
                $_SESSION["mensaje"] = "No puedes cancelar esta solicitud.";
                header("Location: index.php?controller=solicitudes&action=verSolicitudes");
                exit();
            }

            $this->solicitudModel->cancelar($id);

            $_SESSION["mensaje"] = "Solicitud cancelada correctamente";
        }

        header("Location: index.php?controller=solicitudes&action=verSolicitudes");
        exit();
    }
}
?>
