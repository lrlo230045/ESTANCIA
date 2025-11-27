<?php
// Incluye la clase de conexión a la base de datos
require_once "config/Database.php";
// Incluye el modelo Material, encargado de las operaciones sobre la tabla de materiales
require_once "app/models/Material.php";

class MaterialesController {

    private $conexion;
    private $materialModel;

    public function __construct() {
        // Inicia sesión solo si no ha sido iniciada anteriormente
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Obtiene la conexión a la base de datos
        $this->conexion = Database::getConnection();
        // Crea una instancia del modelo Material con la conexión activa
        $this->materialModel = new Material($this->conexion);
    }

    // Función para cargar una vista y pasarle datos
    private function render($vista, $data = []) {
        extract($data);                     // Convierte las claves del array en variables
        require "app/views/$vista.php";    // Carga la vista correspondiente
    }

    /* =====================================================
       VER MATERIALES (USADO POR ALUMNOS)
    ===================================================== */
    public function verMateriales() {

        // Obtiene un mensaje almacenado en sesión (si existe)
        $mensaje = $_SESSION["mensaje"] ?? null;
        // Una vez leído, se borra para evitar repeticiones
        unset($_SESSION["mensaje"]);

        // Variable opcional para manejar errores futuros si se requieren
        $error = null;

        // Obtiene únicamente los materiales activos desde el modelo
        $materiales = $this->materialModel->obtenerActivos();

        // Ruta para botón "volver" hacia el panel del alumno
        $volver = "index.php?controller=dashboard&action=panel";

        // Renderiza la vista enviando los materiales y demás variables
        $this->render("ver_materiales", [
            "materiales" => $materiales,
            "mensaje"    => $mensaje,
            "error"      => $error,
            "volver"     => $volver
        ]);
    }
}
?>
