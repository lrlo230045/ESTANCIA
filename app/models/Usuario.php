<?php
class Usuario {
    
    private $conexion;

    public function __construct($conexion) {
        // Guarda la conexión para usarla en todas las operaciones del modelo
        $this->conexion = $conexion;
    }

    /* =====================================================
       LOGIN: OBTENER USUARIO POR MATRÍCULA
       Busca un usuario activo coincidiendo con la matrícula
    ===================================================== */
    public function obtenerPorMatricula($matricula) {
        // Consulta para buscar usuario activo por matrícula
        $sql = "SELECT * FROM usuarios WHERE matricula = ? AND estatus = 'activo' LIMIT 1";

        // Prepara la sentencia
        $stmt = $this->conexion->prepare($sql);

        // Asocia el parámetro tipo string
        $stmt->bind_param("s", $matricula);

        // Ejecuta la sentencia
        $stmt->execute();

        // Obtiene el resultado
        $res = $stmt->get_result();

        // Devuelve la fila o null si no existe
        return $res->fetch_assoc() ?: null;
    }

    /* =====================================================
       VERIFICAR DUPLICADOS (MATRÍCULA O CORREO)
       Revisa si ya existe un usuario con esos datos
    ===================================================== */
    public function existeUsuario($matricula, $correo) {
        // Consulta que valida si matrícula O correo ya están registrados
        $sql = "SELECT id_usuario FROM usuarios WHERE matricula = ? OR correo = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $matricula, $correo);
        $stmt->execute();

        // Si hay al menos un resultado, el usuario ya existe
        return $stmt->get_result()->num_rows > 0;
    }

    /* =====================================================
       OBTENER TODOS LOS USUARIOS (ADMIN)
       Incluye carrera tanto para alumnos como coordinadores
    ===================================================== */
    public function obtenerTodosConCarrera() {
        // Consulta que obtiene usuarios y, mediante COALESCE,
        // obtiene el nombre de carrera dependiendo del rol
        $sql = "
            SELECT 
                u.*,
                COALESCE(cA.nombre_carrera, cC.nombre_carrera) AS nombre_carrera
            FROM usuarios u
            LEFT JOIN alumnos a ON u.id_usuario = a.id_usuario
            LEFT JOIN carreras cA ON a.id_carrera = cA.id_carrera
            LEFT JOIN coordinadores co ON u.id_usuario = co.id_usuario
            LEFT JOIN carreras cC ON co.id_carrera = cC.id_carrera
            ORDER BY u.tipo_usuario, u.nombre
        ";

        // Ejecuta consulta directa
        $res = $this->conexion->query($sql);

        // Devuelve arreglo asociativo
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /* =====================================================
       REGISTRAR USUARIO GENERAL
       Inserta un usuario base en la tabla usuarios
    ===================================================== */
    public function registrar(
        $nombre, $ap_pa, $ap_ma,
        $correo, $matricula,
        $contrasena, $tipo, $genero, $telefono
    ) {

        // Inserta registro general del usuario
        $sql = "INSERT INTO usuarios 
                (nombre, apellido_pa, apellido_ma, correo, matricula, contrasena, tipo_usuario, genero, telefono)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        // Se enlazan los valores del usuario
        $stmt->bind_param(
            "sssssssss",
            $nombre, $ap_pa, $ap_ma,
            $correo, $matricula,
            $contrasena, $tipo,
            $genero, $telefono
        );

        // Si falla, retorna false
        if (!$stmt->execute()) return false;

        // Devuelve el id del usuario recién insertado
        return $stmt->insert_id;
    }

    /* =====================================================
       REGISTRO DE ROLES
       Se asigna el rol correspondiente
    ===================================================== */

    public function registrarAlumno($id_usuario, $id_carrera) {
        $sql = "INSERT INTO alumnos (id_usuario, id_carrera) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $id_usuario, $id_carrera);
        return $stmt->execute();
    }

    public function registrarCoordinador($id_usuario, $id_carrera) {
        $sql = "INSERT INTO coordinadores (id_usuario, id_carrera) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $id_usuario, $id_carrera);
        return $stmt->execute();
    }

    public function registrarAdministrador($id_usuario) {
        $sql = "INSERT INTO administradores (id_usuario) VALUES (?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }

    /* =====================================================
       EDITAR USUARIO
       Actualiza datos base + maneja cambios de rol
    ===================================================== */
    public function editar(
        $id, $nombre, $ap_pa, $ap_ma,
        $correo, $telefono, $genero,
        $tipoNuevo, $estatus, $id_carrera = null
    ) {

        // Recupera el tipo de usuario actual
        $stmt = $this->conexion->prepare("SELECT tipo_usuario FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $res = $stmt->get_result();
        if (!$res->num_rows) return false;

        $tipoActual = $res->fetch_assoc()["tipo_usuario"];

        // Inicia transacción para evitar datos inconsistentes al cambiar roles
        $this->conexion->begin_transaction();

        try {

            /* === Actualización principal del usuario === */
            $sql = "UPDATE usuarios
                    SET nombre=?, apellido_pa=?, apellido_ma=?, correo=?, telefono=?, genero=?, tipo_usuario=?, estatus=?
                    WHERE id_usuario=?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param(
                "ssssssssi",
                $nombre, $ap_pa, $ap_ma, $correo,
                $telefono, $genero, $tipoNuevo,
                $estatus, $id
            );
            $stmt->execute();

            /* === Si el tipo NO cambia === */
            if ($tipoActual === $tipoNuevo) {

                // Permite actualizar carrera si es alumno o coordinador
                if (($tipoNuevo === "alumno" || $tipoNuevo === "coordinador") && $id_carrera) {

                    if ($tipoNuevo === "alumno") {
                        $upd = $this->conexion->prepare("UPDATE alumnos SET id_carrera=? WHERE id_usuario=?");
                    } else {
                        $upd = $this->conexion->prepare("UPDATE coordinadores SET id_carrera=? WHERE id_usuario=?");
                    }

                    $upd->bind_param("ii", $id_carrera, $id);
                    $upd->execute();
                }

                $this->conexion->commit();
                return true;
            }

            /* === Si cambia de tipo: se elimina el rol anterior === */
            if ($tipoActual === "alumno") {
                $del = $this->conexion->prepare("DELETE FROM alumnos WHERE id_usuario=?");
            } elseif ($tipoActual === "coordinador") {
                $del = $this->conexion->prepare("DELETE FROM coordinadores WHERE id_usuario=?");
            } else {
                $del = $this->conexion->prepare("DELETE FROM administradores WHERE id_usuario=?");
            }
            $del->bind_param("i", $id);
            $del->execute();

            /* === Crear registro para el nuevo tipo === */
            if ($tipoNuevo === "alumno") {
                // Si no especifica carrera, asigna una por defecto
                if (!$id_carrera) $id_carrera = 1;

                $stmt = $this->conexion->prepare("INSERT INTO alumnos (id_usuario, id_carrera) VALUES (?, ?)");
                $stmt->bind_param("ii", $id, $id_carrera);
                $stmt->execute();

            } elseif ($tipoNuevo === "coordinador") {

                if (!$id_carrera) $id_carrera = 1;

                $stmt = $this->conexion->prepare("INSERT INTO coordinadores (id_usuario, id_carrera) VALUES (?, ?)");
                $stmt->bind_param("ii", $id, $id_carrera);
                $stmt->execute();

            } else {

                $stmt = $this->conexion->prepare("INSERT INTO administradores (id_usuario) VALUES (?)");
                $stmt->bind_param("i", $id);
                $stmt->execute();
            }

            // Finaliza cambios
            $this->conexion->commit();
            return true;

        } catch (Exception $e) {

            // Revierte cambios si ocurrió un error
            $this->conexion->rollback();
            return false;
        }
    }

    /* =====================================================
       INACTIVAR USUARIO
       Solo cambia el estatus a inactivo
    ===================================================== */
    public function inactivar($id) {
        $stmt = $this->conexion->prepare("UPDATE usuarios SET estatus='inactivo' WHERE id_usuario=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =====================================================
       ELIMINAR USUARIO
       Elimina completamente el registro
    ===================================================== */
    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id_usuario=?");
        $stmt->bind_param("i", $id);

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Si existe una restricción de llave foránea devuelve false
            return false;
        }
    }
}
?>
