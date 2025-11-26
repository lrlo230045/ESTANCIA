<?php
require_once "config/Database.php";
require_once "app/models/Usuario.php";

class UsuariosAdminController {

    private $conexion;
    private $usuarioModel;

    public function __construct() {
        session_start();

        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        $this->conexion      = Database::getConnection();
        $this->usuarioModel  = new Usuario($this->conexion);
    }

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    /* =====================================================
       LISTAR USUARIOS
    ===================================================== */
    public function gestionar() {

        $usuarios  = $this->usuarioModel->obtenerTodosConCarrera();
        $carreras  = $this->conexion->query("SELECT * FROM carreras")->fetch_all(MYSQLI_ASSOC);
        
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        $this->render("gestionar_usuarios", [
            "usuarios"        => $usuarios,
            "carreras"        => $carreras,
            "mensaje"         => $mensaje,
            "actionEditar"    => "index.php?controller=usuariosAdmin&action=editar",
            "actionInactivar" => "index.php?controller=usuariosAdmin&action=inactivar",
            "actionEliminar"  => "index.php?controller=usuariosAdmin&action=eliminar",
            "volver"          => "index.php?controller=dashboard&action=panel",
            "idUsuarioActual" => $_SESSION["id_usuario"]
        ]);
    }

    /* =====================================================
       REGISTRO DE USUARIO
    ===================================================== */
    public function registro() {

        $carreras = $this->conexion->query("SELECT * FROM carreras")->fetch_all(MYSQLI_ASSOC);

        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

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

            // Validación de duplicados
            if ($this->usuarioModel->existeUsuario($matricula, $correo)) {

                $this->render("registro", [
                    "carreras" => $carreras,
                    "error"    => "Matrícula o correo ya registrado",
                    "mensaje"  => $mensaje,
                    "action"   => "index.php?controller=usuariosAdmin&action=registro",
                    "volver"   => "index.php?controller=dashboard&action=panel"
                ]);
                return;
            }

            // Crear usuario base
            $id_usuario = $this->usuarioModel->registrar(
                $nombre, $ap_pa, $ap_ma,
                $correo, $matricula,
                $contrasena, $tipo_usuario,
                $genero, $telefono
            );

            if (!$id_usuario) {
                $_SESSION["mensaje"] = "Error al registrar usuario.";
                header("Location: index.php?controller=usuariosAdmin&action=gestionar");
                exit();
            }

            // Insertar rol correspondiente
            if ($tipo_usuario === "alumno" && $id_carrera) {
                $this->usuarioModel->registrarAlumno($id_usuario, $id_carrera);

            } elseif ($tipo_usuario === "coordinador" && $id_carrera) {
                $this->usuarioModel->registrarCoordinador($id_usuario, $id_carrera);

            } elseif ($tipo_usuario === "administrador") {
                $this->usuarioModel->registrarAdministrador($id_usuario);
            }

            $_SESSION["mensaje"] = "Usuario registrado correctamente.";
            header("Location: index.php?controller=usuariosAdmin&action=gestionar");
            exit();
        }

        // Mostrar formulario al entrar por GET
        $this->render("registro", [
            "carreras" => $carreras,
            "mensaje"  => $mensaje,
            "action"   => "index.php?controller=usuariosAdmin&action=registro",
            "volver"   => "index.php?controller=dashboard&action=panel"
        ]);
    }

    /* =====================================================
       EDITAR USUARIO
    ===================================================== */
    public function editar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

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

            $_SESSION["mensaje"] = $ok
                ? "Usuario actualizado correctamente"
                : "Error al actualizar usuario";
        }

        header("Location: index.php?controller=usuariosAdmin&action=gestionar");
        exit();
    }

    /* =====================================================
       INACTIVAR
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
    ===================================================== */
    public function eliminar() {
        $id = $_GET["id"] ?? null;

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
