<?php
require_once "config/Database.php";
require_once "app/models/Usuario.php";

class LoginController {

    private function render($vista, $data = []) {
        extract($data);
        require "app/views/$vista.php";
    }

    public function login() {

        session_start();

        $conexion = Database::getConnection();
        $usuarioModel = new Usuario($conexion);

        $error   = "";
        $mensaje = $_SESSION["mensaje"] ?? null;   // mensaje general (opcional)
        unset($_SESSION["mensaje"]);               // limpiar una vez leído

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $matricula = trim($_POST["matricula"] ?? "");
            $pass      = $_POST["contrasena"] ?? "";

            // Buscar usuario por matrícula
            $usuario = $usuarioModel->obtenerPorMatricula($matricula);

            if ($usuario && password_verify($pass, $usuario["contrasena"])) {

                // Guardar datos de sesión
                $_SESSION["id_usuario"]   = $usuario["id_usuario"];
                $_SESSION["nombre"]       = $usuario["nombre"];
                $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];

                // Redirigir al panel principal
                header("Location: index.php?controller=dashboard&action=panel");
                exit();

            } else {
                $error = "Matrícula o contraseña incorrecta";
            }
        }

        // Render con variables independientes (sin usar $_SESSION directo en la vista)
        $this->render("login", [
            "error"   => $error,
            "mensaje" => $mensaje,
            "action"  => "index.php?controller=login&action=login"
        ]);
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?controller=login&action=login");
        exit();
    }
}
?>
