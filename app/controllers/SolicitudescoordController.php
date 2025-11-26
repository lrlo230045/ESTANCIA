<?php
require_once "config/Database.php";
require_once "app/models/Solicitud.php";
require_once "app/models/Material.php";
require_once "app/models/Coordinador.php";

class SolicitudescoordController {

    private $conexion;
    private $solicitudModel;
    private $materialModel;
    private $coordinadorModel;

    public function __construct() {
        session_start();

        // Validación de acceso
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "coordinador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Conexión y modelos
        $this->conexion         = Database::getConnection();
        $this->solicitudModel   = new Solicitud($this->conexion);
        $this->materialModel    = new Material($this->conexion);
        $this->coordinadorModel = new Coordinador($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* =====================================================
       VER SOLICITUDES DEL COORDINADOR POR CARRERA
    ===================================================== */
    public function verSolicitudescoord() {

        // Recuperar mensaje y error desde sesión (si existen)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $error = $_SESSION["error"] ?? null;
        unset($_SESSION["error"]);

        $infoCoord = $this->coordinadorModel->obtenerPorUsuario($_SESSION["id_usuario"]);

        if (!$infoCoord) {
            $_SESSION["mensaje"] = "No se encontró la información del coordinador.";
            header("Location: index.php?controller=dashboard&action=panel");
            exit();
        }

        $solicitudes = $this->solicitudModel->obtenerPorCarrera($infoCoord["id_carrera"]);

        $this->render("ver_solicitudes_coord", [
            "solicitudes"    => $solicitudes,
            "mensaje"        => $mensaje,
            "error"          => $error,
            "actionEditar"   => "index.php?controller=solicitudescoord&action=editarSolicitudcoord",
            "actionCancelar" => "index.php?controller=solicitudescoord&action=cancelarSolicitudcoord",
            "volver"         => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       CREAR SOLICITUD (COORDINADOR)
    ===================================================== */
    public function crearSolicitudcoord() {

        $materiales = $this->materialModel->obtenerActivos();
        $infoCoord  = $this->coordinadorModel->obtenerPorUsuario($_SESSION["id_usuario"]);

        if (!$infoCoord) {
            $_SESSION["mensaje"] = "No se encontró la información del coordinador.";
            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->solicitudModel->crearPorCoordinador(
                $infoCoord["id_coordinadores"],
                $_POST["id_material"],
                $_POST["cantidad_solicitada"],
                $_POST["observaciones"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Solicitud creada correctamente"
                : "Error al crear la solicitud";

            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        // Mostrar formulario vacío
        $this->render("crear_solicitud_coord", [
            "materiales" => $materiales,
            "action"     => "index.php?controller=solicitudescoord&action=crearSolicitudcoord",
            "volver"     => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       EDITAR SOLICITUD
    ===================================================== */
    public function editarSolicitudcoord() {

        $id = $_GET["id"] ?? null;

        if (!$id) {
            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        $solicitud  = $this->solicitudModel->obtenerPorId($id);
        $materiales = $this->materialModel->obtenerActivos();

        if (!$solicitud) {
            $_SESSION["mensaje"] = "Solicitud no encontrada.";
            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ok = $this->solicitudModel->editarPorCoordinador(
                $id,
                $_POST["id_material"],
                $_POST["cantidad_solicitada"],
                $_POST["observaciones"]
            );

            $_SESSION["mensaje"] = $ok
                ? "Solicitud actualizada correctamente"
                : "Error al actualizar solicitud";

            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        $this->render("editar_solicitud_coord", [
            "solicitud"  => $solicitud,
            "materiales" => $materiales,
            "action"     => "index.php?controller=solicitudescoord&action=editarSolicitudcoord&id=$id",
            "volver"     => "index.php?controller=solicitudescoord&action=verSolicitudescoord"
        ]);
    }

    /* =====================================================
       CANCELAR SOLICITUD
    ===================================================== */
    public function cancelarSolicitudcoord() {

        $id = $_GET["id"] ?? null;

        if ($id) {
            $this->solicitudModel->cancelar($id);
            $_SESSION["mensaje"] = "Solicitud cancelada correctamente.";
        }

        header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
        exit();
    }
}
?>
