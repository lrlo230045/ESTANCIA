<?php
// Carga la conexión a la base de datos
require_once "config/Database.php";
// Carga el modelo de solicitudes (CRUD de solicitudes)
require_once "app/models/Solicitud.php";
// Carga el modelo de materiales (para listar materiales activos)
require_once "app/models/Material.php";
// Carga el modelo de coordinador (para obtener datos del coordinador logueado)
require_once "app/models/Coordinador.php";

class SolicitudescoordController {

    private $conexion;
    private $solicitudModel;
    private $materialModel;
    private $coordinadorModel;

    public function __construct() {
        // Inicia la sesión para validación y manejo de mensajes
        session_start();

        // Validación estricta: solo los coordinadores pueden acceder
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "coordinador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Inicialización de conexión y modelos
        $this->conexion         = Database::getConnection();
        $this->solicitudModel   = new Solicitud($this->conexion);
        $this->materialModel    = new Material($this->conexion);
        $this->coordinadorModel = new Coordinador($this->conexion);
    }

    // Función auxiliar para cargar vistas pasando variables
    private function render($vista, $data = []) {
        extract($data);                      // Convierte claves a variables
        require "app/views/$vista.php";      // Carga la vista especificada
    }

    /* =====================================================
       VER SOLICITUDES DEL COORDINADOR POR CARRERA
    ===================================================== */
    public function verSolicitudescoord() {

        // Recuperar mensajes flash almacenados en sesión
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $error = $_SESSION["error"] ?? null;
        unset($_SESSION["error"]);

        // Obtiene información del coordinador desde su ID de usuario
        $infoCoord = $this->coordinadorModel->obtenerPorUsuario($_SESSION["id_usuario"]);

        // Si no hay información del coordinador, no se puede continuar
        if (!$infoCoord) {
            $_SESSION["mensaje"] = "No se encontró la información del coordinador.";
            header("Location: index.php?controller=dashboard&action=panel");
            exit();
        }

        // Obtiene solicitudes que corresponden a la carrera del coordinador
        $solicitudes = $this->solicitudModel->obtenerPorCarrera($infoCoord["id_carrera"]);

        // Renderiza la vista con los datos necesarios
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
       CREAR SOLICITUD (POR COORDINADOR)
    ===================================================== */
    public function crearSolicitudcoord() {

        // Se cargan todos los materiales activos para mostrar en el formulario
        $materiales = $this->materialModel->obtenerActivos();

        // Obtiene datos del coordinador logueado
        $infoCoord  = $this->coordinadorModel->obtenerPorUsuario($_SESSION["id_usuario"]);

        if (!$infoCoord) {
            $_SESSION["mensaje"] = "No se encontró la información del coordinador.";
            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        // Si el método es POST, se procesa el formulario
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Crear solicitud a nombre del coordinador
            $ok = $this->solicitudModel->crearPorCoordinador(
                $infoCoord["id_coordinadores"],     // ID del coordinador
                $_POST["id_material"],              // Material solicitado
                $_POST["cantidad_solicitada"],      // Cantidad
                $_POST["observaciones"]             // Observaciones
            );

            // Mensaje flash
            $_SESSION["mensaje"] = $ok
                ? "Solicitud creada correctamente"
                : "Error al crear la solicitud";

            // Redirige a la lista de solicitudes del coordinador
            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        // Si el método no es POST, muestra el formulario vacío
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

        // Obtiene el ID de la solicitud desde GET
        $id = $_GET["id"] ?? null;

        // Si no hay ID, redirige
        if (!$id) {
            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        // Obtiene datos de la solicitud para editarla
        $solicitud  = $this->solicitudModel->obtenerPorId($id);
        // Lista de materiales activos para el formulario
        $materiales = $this->materialModel->obtenerActivos();

        // Si la solicitud no existe, notifica y redirige
        if (!$solicitud) {
            $_SESSION["mensaje"] = "Solicitud no encontrada.";
            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        // Procesar formulario si es POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Edita los valores de la solicitud
            $ok = $this->solicitudModel->editarPorCoordinador(
                $id,
                $_POST["id_material"],
                $_POST["cantidad_solicitada"],
                $_POST["observaciones"]
            );

            // Mensaje de éxito o fallo
            $_SESSION["mensaje"] = $ok
                ? "Solicitud actualizada correctamente"
                : "Error al actualizar solicitud";

            header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
            exit();
        }

        // Renderiza la vista de edición con la información actual
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

        // Obtiene el ID de la solicitud
        $id = $_GET["id"] ?? null;

        // Si existe un ID válido
        if ($id) {
            // Cancela la solicitud desde el modelo
            $this->solicitudModel->cancelar($id);

            // Mensaje de éxito
            $_SESSION["mensaje"] = "Solicitud cancelada correctamente.";
        }

        // Redirige a la lista de solicitudes del coordinador
        header("Location: index.php?controller=solicitudescoord&action=verSolicitudescoord");
        exit();
    }
}
?>
