<?php
class Backup {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // ==================================================
    // GENERAR RESPALDO
    // ==================================================
    public function generarBackup($ruta) {
        try {
            $db = $this->conexion->query("SELECT DATABASE()")->fetch_row()[0];

            // Encabezado
            $sqlBackup  = "-- ==========================================\n";
            $sqlBackup .= "--  RESPALDO COMPLETO DE BASE DE DATOS: $db\n";
            $sqlBackup .= "--  Fecha: " . date("Y-m-d H:i:s") . "\n";
            $sqlBackup .= "-- ==========================================\n\n";
            $sqlBackup .= "CREATE DATABASE IF NOT EXISTS `$db`;\nUSE `$db`;\n\n";

            // Obtener tablas
            $tablas = [];
            $resultado = $this->conexion->query("SHOW TABLES");
            while ($fila = $resultado->fetch_row()) {
                $tablas[] = $fila[0];
            }

            // Estructura + datos
            foreach ($tablas as $tabla) {
                $create = $this->conexion->query("SHOW CREATE TABLE `$tabla`")->fetch_row();

                $sqlBackup .= "\n-- ESTRUCTURA DE TABLA `$tabla`\n";
                $sqlBackup .= "DROP TABLE IF EXISTS `$tabla`;\n";
                $sqlBackup .= $create[1] . ";\n\n";

                // Exportar datos
                $datos = $this->conexion->query("SELECT * FROM `$tabla`");
                $numCampos = $datos->field_count;

                if ($datos->num_rows > 0) {
                    $sqlBackup .= "-- DATOS DE LA TABLA `$tabla`\n";

                    while ($fila = $datos->fetch_row()) {
                        $valores = [];
                        for ($i = 0; $i < $numCampos; $i++) {
                            $valores[] = isset($fila[$i]) 
                                ? '"' . addslashes($fila[$i]) . '"' 
                                : "NULL";
                        }
                        $sqlBackup .= "INSERT INTO `$tabla` VALUES(" . implode(",", $valores) . ");\n";
                    }
                }

                $sqlBackup .= "\n";
            }

            // PROCEDIMIENTOS
            $sqlBackup .= "\n-- PROCEDIMIENTOS\n\n";

            $procedures = $this->conexion->query("
                SELECT ROUTINE_NAME, ROUTINE_DEFINITION, ROUTINE_TYPE
                FROM INFORMATION_SCHEMA.ROUTINES
                WHERE ROUTINE_SCHEMA = '$db'
            ");

            while ($proc = $procedures->fetch_assoc()) {
                $nombre = $proc['ROUTINE_NAME'];
                $tipo   = strtoupper($proc['ROUTINE_TYPE']);
                $def    = $proc['ROUTINE_DEFINITION'];

                $sqlBackup .= "DROP $tipo IF EXISTS `$nombre`;\n";
                $sqlBackup .= "CREATE $tipo `$nombre`() $def;\n\n";
            }

            // Guardar archivo
            if (file_put_contents($ruta, $sqlBackup) === false) {
                throw new Exception("No se pudo escribir el archivo de respaldo.");
            }

            return true;

        } catch (Exception $e) {
            throw new Exception("Error al generar el backup: " . $e->getMessage());
        }
    }

    // ==================================================
    // RESTAURAR BACKUP
    // ==================================================
    public function restaurarBD($ruta) {
        if (!file_exists($ruta)) {
            throw new Exception("Archivo no encontrado: $ruta");
        }

        $contenido = file_get_contents($ruta);

        if (!$contenido || trim($contenido) === "") {
            throw new Exception("El archivo de respaldo está vacío.");
        }

        // Quitar delimitadores
        $contenido = preg_replace('/DELIMITER\s+.+/i', '', $contenido);
        $contenido = str_replace('//', ';', $contenido);

        $consultas = [];
        $bloque = '';
        $dentro_procedimiento = false;

        $lineas = explode("\n", $contenido);

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if ($linea === '' || strpos($linea, '--') === 0) continue;

            if (stripos($linea, 'CREATE PROCEDURE') === 0) {
                $dentro_procedimiento = true;
            }

            $bloque .= $linea . "\n";

            if ($dentro_procedimiento && stripos($linea, 'END;') === 0) {
                $consultas[] = $bloque;
                $bloque = '';
                $dentro_procedimiento = false;
            } elseif (!$dentro_procedimiento && substr($linea, -1) === ';') {
                $consultas[] = $bloque;
                $bloque = '';
            }
        }

        // Ejecutar consultas
        $this->conexion->query("SET FOREIGN_KEY_CHECKS=0;");

        foreach ($consultas as $sql) {
            $sql = trim($sql);
            if ($sql === '') continue;

            if (!$this->conexion->multi_query($sql)) {
                throw new Exception(
                    "Error ejecutando SQL: " . $this->conexion->error . 
                    "\nConsulta: $sql"
                );
            }

            while ($this->conexion->more_results() && $this->conexion->next_result()) {}
        }

        $this->conexion->query("SET FOREIGN_KEY_CHECKS=1;");

        return true;
    }
}
?>
