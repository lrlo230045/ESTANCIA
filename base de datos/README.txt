

HOLA para poder usar la base de datos debera crear una base dentro de Mysql de XAMPP 
llamada "gestion_materiales" una ves creada dentro de la base debera importar 
el archivo .sql 

 --- IMPORTANTE ----

posterior a la instalacion de la base de datos busca en tu dispositivo 
el siguiente archivo: 

C:\xampp\php\php.ini

dentro del archivo deberas buscar la extencion "gd" y habilitarla esto se hace
eliminado el punto y coma que se encuentran antes y guardando el archivo debe 
verse tal que:

extension: gd

------ADVERTENCIA -------

si por algun motivo tu lista de usuarios de XAMPP esta corrupta 
no reconocera "DEFINER=`root`@`localhost` PROCEDURE"  y no se podran ejecutar 
algunas funciones para prevenir dichos fallos elimina manualmente los procedimientos
de la base de datos e insertalos con el siguiente comando:

DELIMITER $$
CREATE PROCEDURE sp_top_materiales (IN p_days INT)
BEGIN
    WITH datos AS (
        SELECT m.nombre_material AS etiqueta,
               COUNT(*) AS total
        FROM solicitudes s
        INNER JOIN materiales m ON m.id_material = s.id_material
        WHERE (s.id_alumno IS NOT NULL OR s.id_coordinadores IS NOT NULL)
          AND (p_days = 0 OR s.fecha_solicitud >= NOW() - INTERVAL p_days DAY)
        GROUP BY m.id_material
        ORDER BY total DESC
        LIMIT 10
    ),
    total_sum AS (
        SELECT SUM(total) AS total_general FROM datos
    )
    SELECT etiqueta,
           ROUND((total / total_general) * 100, 2) AS porcentaje
    FROM datos, total_sum;
END$$
DELIMITER ;
DELIMITER $$
CREATE PROCEDURE sp_top_carreras (IN p_days INT)
BEGIN
    WITH datos AS (
        SELECT c.nombre_carrera AS etiqueta,
               COUNT(*) AS total
        FROM solicitudes s
        LEFT JOIN alumnos a ON s.id_alumno = a.id_alumno
        LEFT JOIN coordinadores co ON s.id_coordinadores = co.id_coordinadores
        LEFT JOIN carreras c ON c.id_carrera = COALESCE(a.id_carrera, co.id_carrera)
        WHERE c.id_carrera IS NOT NULL
          AND (p_days = 0 OR s.fecha_solicitud >= NOW() - INTERVAL p_days DAY)
        GROUP BY c.id_carrera
        ORDER BY total DESC
        LIMIT 10
    ),
    total_sum AS (
        SELECT SUM(total) AS total_general FROM datos
    )
    SELECT etiqueta,
           ROUND((total / total_general) * 100, 2) AS porcentaje
    FROM datos, total_sum;
END$$
DELIMITER ;
DELIMITER $$
CREATE PROCEDURE sp_solicitudes_genero (IN p_days INT)
BEGIN
    WITH datos AS (
        SELECT u.genero AS etiqueta,
               COUNT(*) AS total
        FROM solicitudes s
        LEFT JOIN alumnos a ON s.id_alumno = a.id_alumno
        LEFT JOIN coordinadores co ON s.id_coordinadores = co.id_coordinadores
        LEFT JOIN usuarios u ON u.id_usuario = COALESCE(a.id_usuario, co.id_usuario)
        WHERE u.genero IS NOT NULL
          AND (p_days = 0 OR s.fecha_solicitud >= NOW() - INTERVAL p_days DAY)
        GROUP BY u.genero
    ),
    total_sum AS (
        SELECT SUM(total) AS total_general FROM datos
    )
    SELECT etiqueta,
           ROUND((total / total_general) * 100, 2) AS porcentaje
    FROM datos, total_sum;
END$$
DELIMITER ;



