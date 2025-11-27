<?php
class Database {

    // Método estático para obtener la conexión a la base de datos
    public static function getConnection() {

        // Datos de conexión a MySQL
        $host = "localhost";
        $usuario = "root";
        $password = "";
        $base_datos = "gestion_materiales";

        // Crea una nueva instancia de mysqli
        $conexion = new mysqli($host, $usuario, $password, $base_datos);

        // Verifica si hubo un error al conectar
        if ($conexion->connect_error) {
            // Detiene la ejecución y muestra el error
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Establece el charset para permitir caracteres especiales
        $conexion->set_charset("utf8mb4");

        // Devuelve la conexión lista para usarse en los modelos
        return $conexion;
    }
}
?>
