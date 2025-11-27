<?php
// Carga la clase para la conexión a la base de datos
require_once "config/Database.php";
// Carga el modelo Ubicacion para manejar CRUD en la tabla ubicaciones
require_once "app/models/Ubicacion.php";

class UbicacionesAdminController {

    private $conexion;
    private $ubicacionModel;

    public function __construct() {
        // Inicia la sesión para validar permisos y manejar mensajes
        session_start();

        // Verifica que el usuario sea administrador
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Crea conexión a la base de datos
        $this->conexion = Database::getConnection();
        // Instancia el modelo Ubicacion
        $this->ubicacionModel = new Ubicacion($this->conexion);
    }

    // Función auxiliar que carga una vista y pasa datos a la misma
    private function render($vista, $data = []) {
        extract($data);                        // Convierte las claves del arreglo en variables
        require "app/views/$vista.php";        // Incluye la vista correspondiente
    }

    /* =====================================================
       GESTIONAR UBICACIONES
       Muestra listado de ubicaciones y controla acciones
    ===================================================== */
    public function gestionar() {

        // Obtiene todas las ubicaciones desde el modelo
        $ubicaciones = $this->ubicacionModel->obtenerTodas();

        // Recupera mensaje flash de sesión (si existe)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]); // Limpia el mensaje para evitar que se repita

        // Renderiza la vista de gestión enviando datos necesarios
        $this->render("gestionar_ubicaciones", [
            "ubicaciones"    => $ubicaciones,
            "mensaje"        => $mensaje,

            // Rutas que usará la vista para formularios y botones
            "actionAgregar"  => "index.php?controller=ubicacionesAdmin&action=agregar",
            "actionEditar"   => "index.php?controller=ubicacionesAdmin&action=editar",
            "actionEliminar" => "index.php?controller=ubicacionesAdmin&action=eliminar",
            "volver"         => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       AGREGAR UBICACIÓN
       Procesa formulario de creación
    ===================================================== */
    public function agregar() {

        // Solo se ejecuta si el método es POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Invoca al modelo para agregar una nueva ubicación
            $ok = $this->ubicacionModel->agregar(
                $_POST["nombre_ubicacion"],
                $_POST["ubicacion_fisica"],
                $_POST["capacidad"],
                $_POST["descripcion"]
            );

            // Genera mensaje flash según resultado
            $_SESSION["mensaje"] = $ok
                ? "Ubicación agregada correctamente"
                : "Error al agregar ubicación";
        }

        // Redirige de vuelta a la vista principal
        header("Location: index.php?controller=ubicacionesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       EDITAR UBICACIÓN
       Actualiza valores de una ubicación existente
    ===================================================== */
    public function editar() {

        // Valida que sea POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Llama al modelo para actualizar el registro
            $ok = $this->ubicacionModel->editar(
                $_POST["id_ubicacion"],
                $_POST["nombre_ubicacion"],
                $_POST["ubicacion_fisica"],
                $_POST["capacidad"],
                $_POST["descripcion"]
            );

            // Guarda mensaje flash
            $_SESSION["mensaje"] = $ok
                ? "Ubicación actualizada correctamente"
                : "Error al actualizar ubicación";
        }

        // Redirige nuevamente a la vista principal
        header("Location: index.php?controller=ubicacionesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR UBICACIÓN
       Elimina una ubicación según ID proporcionado
    ===================================================== */
    public function eliminar() {

        // Obtiene el ID enviado por GET
        $id = $_GET["id"] ?? null;

        if ($id) {
            // Solicita al modelo eliminar la ubicación
            $ok = $this->ubicacionModel->eliminar($id);

            // Mensaje correspondiente
            $_SESSION["mensaje"] = $ok
                ? "Ubicación eliminada correctamente"
                : "Error: la ubicación tiene dependencias"; // normal en FK
        }

        // Regresa a la vista de gestión
        header("Location: index.php?controller=ubicacionesAdmin&action=gestionar");
        exit();
    }
}
?>
