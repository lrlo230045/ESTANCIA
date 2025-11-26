<?php
require_once "config/Database.php";
require_once "app/models/Estadisticas.php";

class DashboardController {

    public function __construct() {
        session_start();

        if (!isset($_SESSION["tipo_usuario"])) {
            header("Location: index.php?controller=login&action=login");
            exit();
        }
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* ======================================================
       PANEL GENERAL (SEGÚN TIPO DE USUARIO)
    ====================================================== */
    public function panel() {

        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $nombre = $_SESSION["nombre"] ?? "Usuario";

        switch ($_SESSION["tipo_usuario"]) {

            /* === PANEL ADMIN === */
            case "administrador":
                $this->render("panel_admin", [
                    "nombre"  => $nombre,
                    "mensaje" => $mensaje,
                    "error"   => null
                ]);
                break;

            /* === PANEL ALUMNO === */
            case "alumno":

                $noticias = glob("assets/noticias/*.png");
                $noticiasReverso = array_reverse($noticias);

                $this->render("panel_alumno", [
                    "nombre"          => $nombre,
                    "mensaje"         => $mensaje,
                    "error"           => null,
                    "noticias"        => $noticias,
                    "noticiasReverso" => $noticiasReverso
                ]);
                break;

            /* === PANEL COORDINADOR === */
            case "coordinador":
                $this->panelCoordinador();
                break;

            default:
                header("Location: index.php");
                exit();
        }
    }

    /* ======================================================
       PANEL DEL COORDINADOR
    ====================================================== */
    public function panelCoordinador() {

        if ($_SESSION["tipo_usuario"] !== "coordinador") {
            header("Location: index.php");
            exit();
        }

        $conexion     = Database::getConnection();
        $estadisticas = new Estadisticas($conexion);

        $topMateriales = $estadisticas->getTopMateriales();
        $topCarreras   = $estadisticas->getTopCarreras();
        $generoStats   = $estadisticas->getStatsGenero();

        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $this->render("panel_coordinador", [
            "nombre"        => $_SESSION["nombre"],
            "mensaje"       => $mensaje,
            "error"         => null,
            "topMateriales" => $topMateriales,
            "topCarreras"   => $topCarreras,
            "generoStats"   => $generoStats
        ]);
    }
}
?>
