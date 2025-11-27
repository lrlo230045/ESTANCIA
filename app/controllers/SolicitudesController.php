<?php
// Incluye la conexión a la base de datos
require_once "config/Database.php";
// Incluye el modelo de Solicitud para manejar operaciones relacionadas
require_once "app/models/Solicitud.php";
// Incluye el modelo de Material para listar materiales disponibles
require_once "app/models/Material.php";

class SolicitudesController {

    private $conexion;
    private $solicitudModel;
    private $materialModel;

    public function __construct() {
        // Inicia sesión para validar al usuario
        session_start();

        // Validación del rol: solo alumnos pueden usar este controlador
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "alumno") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Inicializa la conexión y modelos
        $this->conexion       = Database::getConnection();
        $this->solicitudModel = new Solicitud($this->conexion);
        $this->materialModel  = new Material($this->conexion);
    }

    // Carga una vista con los datos proporcionados
    private function render($vista, $data = []) {
        extract($data);                        // Convierte claves en variables
        require "app/views/$vista.php";        // Incluye la vista
    }

    /* =====================================================
       VER SOLICITUDES DEL ALUMNO
       Lista todas las solicitudes creadas por el alumno actual
    ===================================================== */
    public function verSolicitudes() {

        // Obtiene el ID del usuario desde sesión
        $idUsuario   = $_SESSION["id_usuario"];
        // Consulta las solicitudes del alumno
        $solicitudes = $this->solicitudModel->obtenerPorAlumno($idUsuario);

        // Mensaje almacenado en sesión (flash)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        // Rutas para botones/acciones en la vista
        $actionPDF      = "index.php?controller=pdf&action=pdfSolicitud";
        $actionEditar   = "index.php?controller=solicitudes&action=editarSolicitud";
        $actionCancelar = "index.php?controller=solicitudes&action=cancelarSolicitud";
        $volver         = "index.php?controller=dashboard&action=panel";

        // Renderiza la vista principal
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
       Permite al alumno generar una nueva solicitud
    ===================================================== */
    public function crearSolicitud() {

        // Obtiene todos los materiales activos para el formulario
        $materiales = $this->materialModel->obtenerActivos();

        // Si la petición es POST, se procesa el formulario
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Crea la solicitud a través del modelo
            $ok = $this->solicitudModel->crear(
                $_SESSION["id_usuario"],          // ID alumno
                $_POST["id_material"],            // Material solicitado
                $_POST["cantidad_solicitada"],    // Cantidad
                $_POST["observaciones"]           // Observaciones
            );

            // Mensaje flash según resultado
            $_SESSION["mensaje"] = $ok
                ? "Solicitud creada correctamente"
                : "Error al crear la solicitud";

            // Redirige a la lista de solicitudes
            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        // Renderiza el formulario de creación
        $this->render("crear_solicitud", [
            "materiales" => $materiales,
            "action"     => "index.php?controller=solicitudes&action=crearSolicitud",
            "volver"     => "index.php?controller=dashboard&action=panel",
            "mensaje"    => $_SESSION["mensaje"] ?? null
        ]);
    }

    /* =====================================================
       EDITAR SOLICITUD
       Permite modificar una solicitud mientras esté pendiente
    ===================================================== */
    public function editarSolicitud() {

        // Obtiene ID desde GET
        $id = $_GET["id"] ?? null;

        // Si no hay ID, redirige
        if (!$id) {
            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        // Valida que la solicitud pertenece al alumno logueado
        $solicitud = $this->solicitudModel->obtenerPorId($id, $_SESSION["id_usuario"]);

        // Si no existe o no pertenece al alumno, muestra error
        if (!$solicitud) {
            $_SESSION["mensaje"] = "No tienes permiso para editar esta solicitud.";
            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        // Materiales activos para llenar el formulario
        $materiales = $this->materialModel->obtenerActivos();

        // Si envió el formulario (POST)
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Ejecuta la actualización
            $ok = $this->solicitudModel->editar(
                $id,
                $_POST["id_material"],
                $_POST["cantidad_solicitada"],
                $_POST["observaciones"]
            );

            // Mensaje según el resultado
            $_SESSION["mensaje"] = $ok
                ? "Solicitud actualizada correctamente"
                : "Error al actualizar solicitud";

            header("Location: index.php?controller=solicitudes&action=verSolicitudes");
            exit();
        }

        // Si no se envió el formulario, muestra la vista de edición
        $this->render("editar_solicitud", [
            "solicitud"  => $solicitud,
            "materiales" => $materiales,
            "action"     => "index.php?controller=solicitudes&action=editarSolicitud&id=$id",
            "volver"     => "index.php?controller=solicitudes&action=verSolicitudes"
        ]);
    }

    /* =====================================================
       CANCELAR SOLICITUD
       El alumno cancela su propia solicitud
    ===================================================== */
    public function cancelarSolicitud() {

        // Obtiene el ID desde GET
        $id = $_GET["id"] ?? null;

        if ($id) {

            // Verifica que la solicitud pertenece al alumno
            $solicitud = $this->solicitudModel->obtenerPorId($id, $_SESSION["id_usuario"]);

            // Si no pertenece al usuario, no puede cancelarla
            if (!$solicitud) {
                $_SESSION["mensaje"] = "No puedes cancelar esta solicitud.";
                header("Location: index.php?controller=solicitudes&action=verSolicitudes");
                exit();
            }

            // Cancela la solicitud
            $this->solicitudModel->cancelar($id);

            // Mensaje de éxito
            $_SESSION["mensaje"] = "Solicitud cancelada correctamente";
        }

        // Redirige a la vista principal
        header("Location: index.php?controller=solicitudes&action=verSolicitudes");
        exit();
    }
}
?>
