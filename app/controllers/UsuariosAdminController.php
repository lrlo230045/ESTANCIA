<?php
// Carga la conexión a la base de datos
require_once "config/Database.php";
// Carga el modelo de Usuario y carrera para realizar operaciones CRUD
require_once "app/models/Usuario.php";
require_once "app/models/Carrera.php";


class UsuariosAdminController {

    private $conexion;
    private $usuarioModel;

    public function __construct() {
        // Inicia la sesión para validar permisos y manejar mensajes
        session_start();

        // Verifica que el usuario logueado sea administrador
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Establece conexión con la BD
        $this->conexion      = Database::getConnection();
        // Modelo principal para operaciones sobre usuarios
        $this->usuarioModel  = new Usuario($this->conexion);
        // Modelo de carreras (necesario para asignar carrera a alumnos y coordinadores)
        $this->carreraModel  = new Carrera($this->conexion);
    }

    // Función auxiliar para cargar vistas enviando datos
    private function render($vista, $data = []) {
        extract($data);                         // Convierte claves del array en variables
        require "app/views/$vista.php";         // Carga la vista solicitada
    }

    /* =====================================================
       LISTAR USUARIOS
       Muestra a todos los usuarios registrados con su carrera
    ===================================================== */
    public function gestionar() {

        // Obtiene todos los usuarios junto con su carrera asociada
        $usuarios  = $this->usuarioModel->obtenerTodosConCarrera();
        // Consulta todas las carreras disponibles
        $carreras = $this->carreraModel->obtenerTodas();
        
        // Mensaje flash (si existe)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]); // Limpia mensaje tras usarlo

        // Renderiza la vista de gestión de usuarios
        $this->render("gestionar_usuarios", [
            "usuarios"        => $usuarios,
            "carreras"        => $carreras,
            "mensaje"         => $mensaje,
            "actionEditar"    => "index.php?controller=usuariosAdmin&action=editar",
            "actionInactivar" => "index.php?controller=usuariosAdmin&action=inactivar",
            "actionEliminar"  => "index.php?controller=usuariosAdmin&action=eliminar",
            "volver"          => "index.php?controller=dashboard&action=panel",
            "idUsuarioActual" => $_SESSION["id_usuario"] // Para evitar que un admin se elimine a sí mismo
        ]);
    }

    /* =====================================================
       REGISTRO DE USUARIO
       Alta de nuevos usuarios desde el panel administrador
    ===================================================== */
    public function registro() {

        // Obtiene todas las carreras para llenar el formulario
        $carreras = $this->carreraModel->obtenerTodas();

        // Recupera mensaje flash
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        // Si el formulario fue enviado
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Recibe y limpia la información del formulario
            $nombre       = trim($_POST["nombre"]);
            $ap_pa        = trim($_POST["apellido_pa"]);
            $ap_ma        = trim($_POST["apellido_ma"]);
            $correo       = trim($_POST["correo"]);
            $matricula    = trim($_POST["matricula"]);
            $telefono     = trim($_POST["telefono"]);
            $contrasena   = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
            $genero       = $_POST["genero"];
            $tipo_usuario = $_POST["tipo_usuario"];
            $id_carrera   = $_POST["id_carrera"] ?? null;

            // Verifica si ya existe matrícula o correo registrado
            if ($this->usuarioModel->existeUsuario($matricula, $correo)) {

                // Se regresa a la vista con mensaje de error sin perder datos
                $this->render("registro", [
                    "carreras" => $carreras,
                    "error"    => "Matrícula o correo ya registrado",
                    "mensaje"  => $mensaje,
                    "action"   => "index.php?controller=usuariosAdmin&action=registro",
                    "volver"   => "index.php?controller=dashboard&action=panel"
                ]);
                return;
            }

            // Inserta al usuario principal (tabla usuarios)
            $id_usuario = $this->usuarioModel->registrar(
                $nombre, $ap_pa, $ap_ma,
                $correo, $matricula,
                $contrasena, $tipo_usuario,
                $genero, $telefono
            );

            // Si ocurrió error al registrar
            if (!$id_usuario) {
                $_SESSION["mensaje"] = "Error al registrar usuario.";
                header("Location: index.php?controller=usuariosAdmin&action=gestionar");
                exit();
            }

            // Inserta en la tabla correspondiente según el rol
            if ($tipo_usuario === "alumno" && $id_carrera) {
                $this->usuarioModel->registrarAlumno($id_usuario, $id_carrera);

            } elseif ($tipo_usuario === "coordinador" && $id_carrera) {
                $this->usuarioModel->registrarCoordinador($id_usuario, $id_carrera);

            } elseif ($tipo_usuario === "administrador") {
                $this->usuarioModel->registrarAdministrador($id_usuario);
            }

            // Mensaje de éxito
            $_SESSION["mensaje"] = "Usuario registrado correctamente.";
            header("Location: index.php?controller=usuariosAdmin&action=gestionar");
            exit();
        }

        // Si entra por GET, muestra el formulario vacío
        $this->render("registro", [
            "carreras" => $carreras,
            "mensaje"  => $mensaje,
            "action"   => "index.php?controller=usuariosAdmin&action=registro",
            "volver"   => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       EDITAR USUARIO
       Actualiza los datos generales de un usuario
    ===================================================== */
    public function editar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Ejecuta la modificación de datos en el modelo
            $ok = $this->usuarioModel->editar(
                $_POST["id_usuario"],
                $_POST["nombre"],
                $_POST["apellido_pa"],
                $_POST["apellido_ma"],
                $_POST["correo"],
                $_POST["telefono"],
                $_POST["genero"],
                $_POST["tipo_usuario"],
                $_POST["estatus"]
            );

            // Mensaje flash
            $_SESSION["mensaje"] = $ok
                ? "Usuario actualizado correctamente"
                : "Error al actualizar usuario";
        }

        header("Location: index.php?controller=usuariosAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       INACTIVAR
       Cambia el estatus del usuario a inactivo
    ===================================================== */
    public function inactivar() {
        $id = $_GET["id"] ?? null;

        if ($id) {
            $this->usuarioModel->inactivar($id);
            $_SESSION["mensaje"] = "Usuario inactivado correctamente";
        }

        header("Location: index.php?controller=usuariosAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       ELIMINAR
       Elimina completamente un usuario (si no tiene dependencias)
    ===================================================== */
    public function eliminar() {
        $id = $_GET["id"] ?? null;

        // Intenta eliminar y responde con mensaje según resultado
        if ($id && $this->usuarioModel->eliminar($id)) {
            $_SESSION["mensaje"] = "Usuario eliminado correctamente";
        } else {
            $_SESSION["mensaje"] = "No se puede eliminar el usuario (dependencias activas)";
        }

        header("Location: index.php?controller=usuariosAdmin&action=gestionar");
        exit();
    }
}
?>
