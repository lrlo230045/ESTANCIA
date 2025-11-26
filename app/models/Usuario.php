<?php
class Usuario {
    
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /* =====================================================
       LOGIN
    ===================================================== */
    public function obtenerPorMatricula($matricula) {
        $sql = "SELECT * FROM usuarios WHERE matricula = ? AND estatus = 'activo' LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    /* =====================================================
       VERIFICAR DUPLICADOS
    ===================================================== */
    public function existeUsuario($matricula, $correo) {
        $sql = "SELECT id_usuario FROM usuarios WHERE matricula = ? OR correo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $matricula, $correo);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /* =====================================================
       OBTENER TODOS (ADMIN)
    ===================================================== */
    public function obtenerTodosConCarrera() {
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
        $res = $this->conexion->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /* =====================================================
       REGISTRO GENERAL
    ===================================================== */
    public function registrar(
        $nombre, $ap_pa, $ap_ma,
        $correo, $matricula,
        $contrasena, $tipo, $genero, $telefono
    ) {
        $sql = "INSERT INTO usuarios 
                (nombre, apellido_pa, apellido_ma, correo, matricula, contrasena, tipo_usuario, genero, telefono)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "sssssssss",
            $nombre, $ap_pa, $ap_ma,
            $correo, $matricula,
            $contrasena, $tipo,
            $genero, $telefono
        );

        if (!$stmt->execute()) return false;
        return $stmt->insert_id;
    }

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
       EDITAR USUARIO (SIN USAR $_POST)
    ===================================================== */
    public function editar(
        $id, $nombre, $ap_pa, $ap_ma,
        $correo, $telefono, $genero,
        $tipoNuevo, $estatus, $id_carrera = null
    ) {

        // Obtener tipo actual
        $stmt = $this->conexion->prepare("SELECT tipo_usuario FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$res->num_rows) return false;

        $tipoActual = $res->fetch_assoc()["tipo_usuario"];

        $this->conexion->begin_transaction();

        try {
            /* === Actualizar datos base === */
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

            /* === Si NO cambia el tipo === */
            if ($tipoActual === $tipoNuevo) {

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

            /* === Eliminar rol anterior === */
            if ($tipoActual === "alumno") {
                $del = $this->conexion->prepare("DELETE FROM alumnos WHERE id_usuario=?");
            } elseif ($tipoActual === "coordinador") {
                $del = $this->conexion->prepare("DELETE FROM coordinadores WHERE id_usuario=?");
            } else {
                $del = $this->conexion->prepare("DELETE FROM administradores WHERE id_usuario=?");
            }
            $del->bind_param("i", $id);
            $del->execute();

            /* === Insertar nuevo rol === */
            if ($tipoNuevo === "alumno") {
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

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollback();
            return false;
        }
    }

    /* =====================================================
       INACTIVAR
    ===================================================== */
    public function inactivar($id) {
        $stmt = $this->conexion->prepare("UPDATE usuarios SET estatus='inactivo' WHERE id_usuario=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =====================================================
       ELIMINAR
    ===================================================== */
    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id_usuario=?");
        $stmt->bind_param("i", $id);

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
}
?>
