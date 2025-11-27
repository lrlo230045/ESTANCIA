<?php
// Incluye el archivo de conexión a la base de datos
require_once "config/Database.php";
// Incluye el modelo Carrera que maneja las operaciones SQL
require_once "app/models/Carrera.php";

class CarrerasAdminController {

    private $conexion;
    private $model;

    public function __construct() {
        // Inicia sesión para validar permisos y manejar mensajes
        session_start();

        // Verifica que el usuario sea administrador
        // Si no, lo redirige al login
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Obtiene la conexión a la base de datos
        $this->conexion = Database::getConnection();
        // Instancia el modelo Carrera para interactuar con la tabla carreras
        $this->model = new Carrera($this->conexion);
    }

    // Función para cargar vistas y pasarles datos
    private function render($vista, $data = []) {
        extract($data); // Convierte claves del array en variables
        require "app/views/$vista.php"; // carga la vista correspondiente
    }

    /* =====================================================
       MOSTRAR GESTIÓN
       Muestra listado de carreras y rutas para CRUD
    ===================================================== */
    public function gestionar() {

        // Obtiene todas las carreras desde el modelo
        $carreras = $this->model->obtenerTodas();

        // Recupera mensaje almacenado en sesión (si existe)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]); // elimina el mensaje después de leerlo

        // Renderiza la vista de gestión con los datos necesarios
        $this->render("gestionar_carreras", [
            "carreras"       => $carreras,
            "mensaje"        => $mensaje,
            "actionAgregar"  => "index.php?controller=carrerasAdmin&action=agregar",
            "actionEditar"   => "index.php?controller=carrerasAdmin&action=editar",
            "actionEliminar" => "index.php?controller=carrerasAdmin&action=eliminar",
            "volver"         => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       AGREGAR CARRERA
       Procesa formulario POST para agregar nueva carrera
    ===================================================== */
    public function agregar() {

        // Solo procesa si el método es POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Llama al modelo para insertar la nueva carrera
            $ok = $this->model->agregar(
                $_POST["nombre_carrera"],
                $_POST["descripcion"]
            );

            // Guarda mensaje de éxito o error en sesión
            $_SESSION["mensaje"] = $ok
                ? "Carrera agregada correctamente"
                : "Error al agregar carrera";
        }

        // Redirige de vuelta a la gestión
        header("Location: index.php?controller=carrerasAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       EDITAR CARRERA
       Procesa el formulario para actualizar una carrera existente
    ===================================================== */
    public function editar() {

        // Solo procesa si el método es POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Llama al modelo para actualizar la carrera indicada
            $ok = $this->model->editar(
                $_POST["id_carrera"],
                $_POST["nombre_carrera"],
                $_POST["descripcion"]
            );

            // Almacena mensaje según el resultado
            $_SESSION["mensaje"] = $ok
                ? "Carrera actualizada correctamente"
                : "Error al actualizar carrera";
        }

        // Redirige nuevamente al listado
        header("Location: index.php?controller=carrerasAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR CARRERA
       Elimina una carrera por ID recibido por GET
    ===================================================== */
    public function eliminar() {

        // Obtiene el ID enviado por la URL
        $id = $_GET["id"] ?? null;

        // Si existe un ID, procede a eliminar
        if ($id) {

            // Llama al modelo para eliminar la carrera
            $ok = $this->model->eliminar($id);

            // Guarda mensaje dependiendo del resultado
            $_SESSION["mensaje"] = $ok
                ? "Carrera eliminada correctamente"
                : "Error: la carrera tiene dependencias";
        }

        // Redirige de regreso al listado
        header("Location: index.php?controller=carrerasAdmin&action=gestionar");
        exit();
    }
}
?>
