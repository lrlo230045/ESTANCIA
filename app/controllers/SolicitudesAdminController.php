<?php
// Carga la clase de conexión a la base de datos
require_once "config/Database.php";
// Carga el modelo de Solicitud, encargado del CRUD sobre solicitudes
require_once "app/models/Solicitud.php";

class SolicitudesAdminController {

    private $conexion;
    private $solicitudModel;

    public function __construct() {
        // Inicia la sesión para validar roles y manejar mensajes
        session_start();

        // Valida que el usuario actual sea un administrador
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Obtiene conexión a la base de datos
        $this->conexion       = Database::getConnection();
        // Instancia el modelo de solicitudes
        $this->solicitudModel = new Solicitud($this->conexion);
    }

    // Función encargada de cargar vistas y pasarles datos
    private function render($vista, $data = []) {
        extract($data);                     // Convierte las claves del arreglo en variables
        require "app/views/$vista.php";     // Carga la vista correspondiente
    }

    /* =====================================================
       GESTIONAR SOLICITUDES (LISTADO + FORMULARIO)
       Muestra todas las solicitudes y sus acciones
    ===================================================== */
    public function gestionar() {

        // Obtiene todas las solicitudes desde el modelo
        $solicitudes = $this->solicitudModel->obtenerTodas();

        // Recupera mensaje de sesión si existe
        $mensaje = $_SESSION["mensaje"] ?? null;
        // Elimina el mensaje para que no se repita
        unset($_SESSION["mensaje"]);

        // Rutas para las acciones del CRUD (con capitalización correcta)
        $actionEditar   = "index.php?controller=SolicitudesAdmin&action=editar";
        $actionEliminar = "index.php?controller=SolicitudesAdmin&action=eliminar";
        $volver         = "index.php?controller=Dashboard&action=panel";

        // Renderiza la vista con los datos requeridos
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
       Actualiza el estado y observaciones de una solicitud
    ===================================================== */
    public function editar() {

        // Solo procesa si la solicitud viene por POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Ejecuta la actualización del estado mediante el modelo
            $ok = $this->solicitudModel->editarEstado(
                intval($_POST["id_solicitud"]), // ID de solicitud a editar
                $_POST["estado"],               // Nuevo estado
                $_POST["observaciones"]         // Observaciones adicionales
            );

            // Guarda el mensaje correspondiente al resultado
            $_SESSION["mensaje"] = $ok
                ? "Solicitud actualizada correctamente"
                : "Error al actualizar la solicitud";
        }

        // Redirige nuevamente a la gestión con la capitalización correcta
        header("Location: index.php?controller=SolicitudesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR SOLICITUD
       Elimina una solicitud utilizando su ID
    ===================================================== */
    public function eliminar() {

        // Obtiene el ID desde GET, asegurando que sea entero
        $id = isset($_GET["id"]) ? intval($_GET["id"]) : null;

        // Si se obtuvo un ID válido, intenta eliminar
        if ($id) {
            $ok = $this->solicitudModel->eliminar($id);

            // Guarda un mensaje según el resultado
            $_SESSION["mensaje"] = $ok
                ? "Solicitud eliminada correctamente"
                : "Error: no se pudo eliminar la solicitud";
        }

        // Redirige de vuelta a la gestión (con capitalización correcta)
        header("Location: index.php?controller=SolicitudesAdmin&action=gestionar");
        exit();
    }
}
?>
