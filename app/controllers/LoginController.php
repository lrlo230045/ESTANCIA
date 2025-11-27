<?php
// Incluye el archivo de conexión a la base de datos
require_once "config/Database.php";
// Incluye el modelo Usuario para manejar las consultas del login
require_once "app/models/Usuario.php";

class LoginController {

    // Función para renderizar vistas y pasar datos
    private function render($vista, $data = []) {
        extract($data);                     // Convierte claves del array en variables
        require "app/views/$vista.php";    // Carga la vista correspondiente
    }

    public function login() {

        // Inicia sesión para gestionar autenticación y mensajes
        session_start();

        // Obtiene la conexión a la base de datos
        $conexion = Database::getConnection();
        // Instancia el modelo Usuario con la conexión
        $usuarioModel = new Usuario($conexion);

        // Variables para errores y mensajes
        $error   = "";
        // Mensaje opcional guardado previamente en sesión (por ejemplo, logout o bloqueo)
        $mensaje = $_SESSION["mensaje"] ?? null;
        unset($_SESSION["mensaje"]); // Se elimina para evitar que se repita

        // Verifica si el formulario fue enviado vía POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Obtiene la matrícula enviada y elimina espacios laterales
            $matricula = trim($_POST["matricula"] ?? "");
            // Obtiene la contraseña enviada
            $pass      = $_POST["contrasena"] ?? "";

            // Busca al usuario por su matrícula
            $usuario = $usuarioModel->obtenerPorMatricula($matricula);

            // Verifica si el usuario existe y si la contraseña coincide
            if ($usuario && password_verify($pass, $usuario["contrasena"])) {

                // Guarda datos del usuario en la sesión
                $_SESSION["id_usuario"]   = $usuario["id_usuario"];
                $_SESSION["nombre"]       = $usuario["nombre"];
                $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];

                // Redirige al panel principal dependiendo del tipo de usuario
                header("Location: index.php?controller=dashboard&action=panel");
                exit();

            } else {
                // Mensaje cuando el acceso falla
                $error = "Matrícula o contraseña incorrecta";
            }
        }

        // Llama a la vista de login con variables ya preparadas
        // La vista no debe leer directamente las variables de sesión
        $this->render("login", [
            "error"   => $error,
            "mensaje" => $mensaje,
            "action"  => "index.php?controller=login&action=login"
        ]);
    }

    public function logout() {
        // Inicia sesión para poder destruirla correctamente
        session_start();
        // Borra todos los datos de la sesión
        session_destroy();
        // Redirige al formulario de login
        header("Location: index.php?controller=login&action=login");
        exit();
    }
}
?>
