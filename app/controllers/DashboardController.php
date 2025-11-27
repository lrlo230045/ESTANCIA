<?php
// Incluye la clase de conexión a la base de datos
require_once "config/Database.php";
// Incluye el modelo que contiene consultas de estadísticas
require_once "app/models/Estadisticas.php";

class DashboardController {

    public function __construct() {
        // Inicia sesión para validar usuario y mensajes
        session_start();

        // Si no hay un tipo de usuario registrado, se redirige al login
        if (!isset($_SESSION["tipo_usuario"])) {
            header("Location: index.php?controller=login&action=login");
            exit();
        }
    }

    // Carga una vista y le pasa datos mediante variables extraídas del array
    private function render($vista, $data = []) {
        extract($data); 
        require "app/views/$vista.php";
    }

    /* ======================================================
       PANEL GENERAL (SEGÚN TIPO DE USUARIO)
       Redirige al panel correspondiente según el rol
    ====================================================== */
    public function panel() {

        // Recupera mensaje en sesión (si existe)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        // Obtiene nombre del usuario o valor por defecto
        $nombre = $_SESSION["nombre"] ?? "Usuario";

        // Selecciona el panel dependiendo del tipo de usuario
        switch ($_SESSION["tipo_usuario"]) {

            /* === PANEL ADMIN === */
            case "administrador":
                // Renderiza la vista del panel administrador
                $this->render("panel_admin", [
                    "nombre"  => $nombre,
                    "mensaje" => $mensaje,
                    "error"   => null
                ]);
                break;

            /* === PANEL ALUMNO === */
            case "alumno":

                // Obtiene todas las imágenes de noticias desde la carpeta
                $noticias = glob("assets/noticias/*.png");
                // Invierte el orden para mostrar primero las más recientes
                $noticiasReverso = array_reverse($noticias);

                // Renderiza la vista del panel alumno
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
                // Envía al método especializado para el panel de coordinador
                $this->panelCoordinador();
                break;

            // Si no coincide ningún tipo de usuario válido
            default:
                header("Location: index.php");
                exit();
        }
    }

    /* ======================================================
       PANEL DEL COORDINADOR
       Muestra estadísticas generales a coordinadores
    ====================================================== */
    public function panelCoordinador() {

        // Seguridad: evita que alguien sin rol de coordinador ingrese a este panel
        if ($_SESSION["tipo_usuario"] !== "coordinador") {
            header("Location: index.php");
            exit();
        }

        // Obtiene conexión a la base de datos
        $conexion     = Database::getConnection();
        // Instancia el modelo encargado de las estadísticas
        $estadisticas = new Estadisticas($conexion);

        // Consulta los materiales más usados
        $topMateriales = $estadisticas->getTopMateriales();
        // Consulta las carreras más solicitadas
        $topCarreras   = $estadisticas->getTopCarreras();
        // Consulta estadísticas por género
        $generoStats   = $estadisticas->getStatsGenero();

        // Recupera mensaje en sesión (si existe)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        // Renderiza la vista del panel coordinador con las estadísticas
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
