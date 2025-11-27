<?php
// Se incluye el archivo de conexión a la base de datos
require_once __DIR__ . "/../../config/database.php";
// Se incluye el modelo que maneja la lógica de respaldo y restauración
require_once __DIR__ . "/../models/Backup.php";

class BackupController {

    private $conexion;
    private $model;

    public function __construct() {
        // Inicia la sesión para validar permisos y usar variables de sesión
        session_start();

        // Verifica si el usuario está autenticado y si es administrador
        // Si no lo es, se redirige al login
        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        // Obtiene la conexión a la base de datos
        $this->conexion = Database::getConnection();
        // Crea una instancia del modelo Backup con la conexión activa
        $this->model = new Backup($this->conexion);
    }

    public function generar() {
        try {
            // Genera una marca de tiempo para el nombre del archivo de respaldo
            $fecha   = date("Y-m-d_H-i-s");
            // Crea el nombre del archivo utilizando la fecha
            $archivo = "db-backup-$fecha.sql";
            // Define la carpeta donde se almacenarán los respaldos
            $carpeta = "config/backups/";
            // Genera la ruta completa del archivo
            $ruta    = $carpeta . $archivo;

            // Si la carpeta no existe, la crea con permisos 0777
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            // Llama al método del modelo para generar el archivo de respaldo
            $this->model->generarBackup($ruta);
            // Almacena un mensaje de éxito en la sesión
            $_SESSION['mensaje'] = "Respaldo generado correctamente.";
        } 
        catch (Exception $e) {
            // Si ocurre un error, se captura el mensaje y se guarda en la sesión
            $_SESSION['mensaje'] = "Error al generar respaldo: " . $e->getMessage();
        }

        // Redirige de vuelta al panel principal del sistema
        header("Location: index.php?controller=dashboard&action=panel");
        exit();
    }

    public function restaurarBD() {
        try {
            // Carpeta donde se almacenan los respaldos
            $carpeta  = "config/backups/";
            // Obtiene todos los archivos de respaldo que coincidan con el patrón
            $archivos = glob($carpeta . "db-backup-*.sql");

            // Si no existe ningún archivo de respaldo, lanza una excepción
            if (!$archivos) {
                throw new Exception("No se encontró ningún archivo de respaldo.");
            }

            // Ordena los archivos del más reciente al más antiguo
            rsort($archivos);
            // Selecciona el archivo más reciente
            $ruta = $archivos[0];

            // Llama al modelo para restaurar la base de datos desde el archivo seleccionado
            $this->model->restaurarBD($ruta);
            // Guarda mensaje de éxito en la sesión
            $_SESSION['mensaje'] = "Restauración completada correctamente.";
        } 
        catch (Exception $e) {
            // Guarda mensaje de error si ocurre un problema
            $_SESSION['mensaje'] = "Error al restaurar: " . $e->getMessage();
        }

        // Redirige nuevamente al panel del dashboard
        header("Location: index.php?controller=dashboard&action=panel");
        exit();
    }
}
?>
