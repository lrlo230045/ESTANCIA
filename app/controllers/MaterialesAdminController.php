<?php
// Incluye la clase de conexión a la base de datos
require_once "config/Database.php";
// Incluye el modelo Material para manejar operaciones CRUD sobre materiales
require_once "app/models/Material.php";
// Incluye el modelo Ubicacion para poder asignar ubicaciones a los materiales
require_once "app/models/Ubicacion.php";

class MaterialesAdminController {

    private $conexion;
    private $materialModel;
    private $ubicacionModel;

    public function __construct() {
        // Inicia sesión para validar usuario y manejar mensajes
        session_start();

        // Verificación de permisos: únicamente administradores pueden entrar
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Obtiene la conexión a la base de datos
        $this->conexion       = Database::getConnection();
        // Crea instancia del modelo Material
        $this->materialModel  = new Material($this->conexion);
        // Crea instancia del modelo Ubicacion
        $this->ubicacionModel = new Ubicacion($this->conexion);
    }

    // Función que carga una vista pasando sus datos
    private function render($vista, $data = []) {
        extract($data);                         // Convierte claves del array en variables
        require "app/views/$vista.php";         // Carga la vista solicitada
    }

    /* =====================================================
       GESTIONAR MATERIALES
       Muestra la lista de materiales y ubicaciones disponibles
    ===================================================== */
    public function gestionar() {

        // Obtiene todos los materiales desde el modelo
        $materiales  = $this->materialModel->obtenerTodos();
        // Obtiene todas las ubicaciones disponibles
        $ubicaciones = $this->ubicacionModel->obtenerTodas();

        // Recupera mensaje flash almacenado en sesión
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]); // Limpia el mensaje después de mostrarlo

        // Renderiza la vista enviando todos los datos necesarios
        $this->render("gestionar_materiales", [
            "materiales"     => $materiales,
            "ubicaciones"    => $ubicaciones,
            "mensaje"        => $mensaje,

            // Rutas para acciones CRUD enviadas a la vista
            "actionAgregar"  => "index.php?controller=materialesAdmin&action=agregar",
            "actionEditar"   => "index.php?controller=materialesAdmin&action=editar",
            "actionEliminar" => "index.php?controller=materialesAdmin&action=eliminar",
            "volver"         => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       AGREGAR MATERIAL
       Procesa envío del formulario para insertar un nuevo material
    ===================================================== */
    public function agregar() {

        // Solo procesa si la petición es POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Llama al modelo para insertar el material con los datos recibidos
            $ok = $this->materialModel->agregar(
                $_POST["nombre_material"],
                $_POST["descripcion"],
                $_POST["cantidad_disponible"],
                $_POST["unidad_medida"],
                $_POST["id_ubicacion"],
                $_POST["estado"]
            );

            // Mensaje según el resultado de la operación
            $_SESSION["mensaje"] = $ok
                ? "Material agregado correctamente"
                : "Error al agregar material";
        }

        // Redirige de vuelta a la pantalla de gestión
        header("Location: index.php?controller=materialesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       EDITAR MATERIAL
       Actualiza un material existente tras recibir los nuevos datos por POST
    ===================================================== */
    public function editar() {

        // Solo se ejecuta si la solicitud viene por POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Se llama al modelo para actualizar el material indicado
            $ok = $this->materialModel->editar(
                $_POST["id_material"],
                $_POST["nombre_material"],
                $_POST["descripcion"],
                $_POST["cantidad_disponible"],
                $_POST["unidad_medida"],
                $_POST["id_ubicacion"],
                $_POST["estado"]
            );

            // Guarda mensaje en sesión dependiendo del resultado
            $_SESSION["mensaje"] = $ok
                ? "Material actualizado correctamente"
                : "Error al actualizar material";
        }

        // Redirige a la vista de gestión
        header("Location: index.php?controller=materialesAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR MATERIAL
       Elimina un material por ID recibido mediante GET
    ===================================================== */
    public function eliminar() {

        // Obtiene el ID del material desde la URL
        $id = $_GET["id"] ?? null;

        // Si el ID existe, intenta eliminar el material
        if ($id) {

            // Llama al modelo para eliminar el material
            $ok = $this->materialModel->eliminar($id);

            // Mensaje según el resultado
            $_SESSION["mensaje"] = $ok
                ? "Material eliminado correctamente"
                : "Error: material en uso";
        }

        // Redirige nuevamente a la pantalla de administración
        header("Location: index.php?controller=materialesAdmin&action=gestionar");
        exit();
    }
}
?>
