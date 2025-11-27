<?php
class Backup {
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión a la base de datos para uso posterior
        $this->conexion = $conexion;
    }

    // ==================================================
    // GENERAR RESPALDO
    // ==================================================
    public function generarBackup($ruta) {
        try {
            // Obtiene el nombre de la base de datos actual
            $db = $this->conexion->query("SELECT DATABASE()")->fetch_row()[0];

            // Construye el encabezado del archivo SQL con información general
            $sqlBackup  = "-- ==========================================\n";
            $sqlBackup .= "--  RESPALDO COMPLETO DE BASE DE DATOS: $db\n";
            $sqlBackup .= "--  Fecha: " . date("Y-m-d H:i:s") . "\n";
            $sqlBackup .= "-- ==========================================\n\n";
            $sqlBackup .= "CREATE DATABASE IF NOT EXISTS `$db`;\nUSE `$db`;\n\n";

            // Obtiene la lista de tablas existentes en la base de datos
            $tablas = [];
            $resultado = $this->conexion->query("SHOW TABLES");
            while ($fila = $resultado->fetch_row()) {
                $tablas[] = $fila[0];
            }

            // Para cada tabla, respalda estructura y datos
            foreach ($tablas as $tabla) {

                // Exporta la estructura (DDL)
                $create = $this->conexion->query("SHOW CREATE TABLE `$tabla`")->fetch_row();

                $sqlBackup .= "\n-- ESTRUCTURA DE TABLA `$tabla`\n";
                $sqlBackup .= "DROP TABLE IF EXISTS `$tabla`;\n";
                $sqlBackup .= $create[1] . ";\n\n";

                // Exporta los datos (DML)
                $datos = $this->conexion->query("SELECT * FROM `$tabla`");
                $numCampos = $datos->field_count;

                if ($datos->num_rows > 0) {
                    $sqlBackup .= "-- DATOS DE LA TABLA `$tabla`\n";

                    // Genera INSERT para cada fila de datos
                    while ($fila = $datos->fetch_row()) {
                        $valores = [];
                        for ($i = 0; $i < $numCampos; $i++) {
                            // Maneja valores NULL y escapa caracteres
                            $valores[] = isset($fila[$i]) 
                                ? '"' . addslashes($fila[$i]) . '"' 
                                : "NULL";
                        }
                        // Agrega sentencia INSERT al respaldo
                        $sqlBackup .= "INSERT INTO `$tabla` VALUES(" . implode(",", $valores) . ");\n";
                    }
                }

                $sqlBackup .= "\n";
            }

            // Encabezado para procedimientos almacenados
            $sqlBackup .= "\n-- PROCEDIMIENTOS\n\n";

            // Consulta los procedimientos almacenados en la BD
            $procedures = $this->conexion->query("
                SELECT ROUTINE_NAME, ROUTINE_DEFINITION, ROUTINE_TYPE
                FROM INFORMATION_SCHEMA.ROUTINES
                WHERE ROUTINE_SCHEMA = '$db'
            ");

            // Exporta cada procedimiento almacenado
            while ($proc = $procedures->fetch_assoc()) {
                $nombre = $proc['ROUTINE_NAME'];
                $tipo   = strtoupper($proc['ROUTINE_TYPE']);
                $def    = $proc['ROUTINE_DEFINITION'];

                $sqlBackup .= "DROP $tipo IF EXISTS `$nombre`;\n";
                $sqlBackup .= "CREATE $tipo `$nombre`() $def;\n\n";
            }

            // Guarda el contenido en el archivo .sql
            if (file_put_contents($ruta, $sqlBackup) === false) {
                throw new Exception("No se pudo escribir el archivo de respaldo.");
            }

            return true;

        } catch (Exception $e) {
            // Error general si ocurre falla en el proceso
            throw new Exception("Error al generar el backup: " . $e->getMessage());
        }
    }

    // ==================================================
    // RESTAURAR BACKUP
    // ==================================================
    public function restaurarBD($ruta) {

        // Verifica que el archivo exista
        if (!file_exists($ruta)) {
            throw new Exception("Archivo no encontrado: $ruta");
        }

        // Lee el contenido del archivo SQL
        $contenido = file_get_contents($ruta);

        // Verifica que no esté vacío
        if (!$contenido || trim($contenido) === "") {
            throw new Exception("El archivo de respaldo está vacío.");
        }

        // Elimina directivas DELIMITER (no usadas aquí)
        $contenido = preg_replace('/DELIMITER\s+.+/i', '', $contenido);
        // Reemplaza // por ; en procedimientos
        $contenido = str_replace('//', ';', $contenido);

        // Arreglos auxiliares para separar consultas
        $consultas = [];
        $bloque = '';
        $dentro_procedimiento = false;

        // Procesa el archivo línea por línea
        $lineas = explode("\n", $contenido);

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            // Ignora líneas vacías o comentarios
            if ($linea === '' || strpos($linea, '--') === 0) continue;

            // Detecta inicio de procedimiento almacenado
            if (stripos($linea, 'CREATE PROCEDURE') === 0) {
                $dentro_procedimiento = true;
            }

            // Acumula la línea en el bloque actual
            $bloque .= $linea . "\n";

            // Detecta fin de procedimiento almacenado
            if ($dentro_procedimiento && stripos($linea, 'END;') === 0) {
                $consultas[] = $bloque;
                $bloque = '';
                $dentro_procedimiento = false;

            // Detecta fin de una consulta normal
            } elseif (!$dentro_procedimiento && substr($linea, -1) === ';') {
                $consultas[] = $bloque;
                $bloque = '';
            }
        }

        // Deshabilita validación de claves foráneas para evitar conflictos
        $this->conexion->query("SET FOREIGN_KEY_CHECKS=0;");

        // Ejecuta todas las consultas del archivo de respaldo
        foreach ($consultas as $sql) {
            $sql = trim($sql);
            if ($sql === '') continue;

            // Ejecuta consultas complejas o múltiples sentencias
            if (!$this->conexion->multi_query($sql)) {
                throw new Exception(
                    "Error ejecutando SQL: " . $this->conexion->error . 
                    "\nConsulta: $sql"
                );
            }

            // Limpia buffer de resultados múltiples
            while ($this->conexion->more_results() && $this->conexion->next_result()) {}
        }

        // Vuelve a activar verificación de claves foráneas
        $this->conexion->query("SET FOREIGN_KEY_CHECKS=1;");

        return true;
    }
}
?>
