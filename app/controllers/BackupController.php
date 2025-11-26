<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Backup.php";

class BackupController {

    private $conexion;
    private $model;

    public function __construct() {
        session_start();

        if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
            header("Location: index.php?controller=login&action=login");
            exit();
        }

        $this->conexion = Database::getConnection();
        $this->model = new Backup($this->conexion);
    }

    public function generar() {
        try {
            $fecha   = date("Y-m-d_H-i-s");
            $archivo = "db-backup-$fecha.sql";
            $carpeta = "config/backups/";
            $ruta    = $carpeta . $archivo;

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $this->model->generarBackup($ruta);
            $_SESSION['mensaje'] = "Respaldo generado correctamente.";
        } 
        catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al generar respaldo: " . $e->getMessage();
        }

        header("Location: index.php?controller=dashboard&action=panel");
        exit();
    }

    public function restaurarBD() {
        try {
            $carpeta  = "config/backups/";
            $archivos = glob($carpeta . "db-backup-*.sql");

            if (!$archivos) {
                throw new Exception("No se encontró ningún archivo de respaldo.");
            }

            rsort($archivos);
            $ruta = $archivos[0];

            $this->model->restaurarBD($ruta);
            $_SESSION['mensaje'] = "Restauración completada correctamente.";
        } 
        catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al restaurar: " . $e->getMessage();
        }

        header("Location: index.php?controller=dashboard&action=panel");
        exit();
    }
}
?>
