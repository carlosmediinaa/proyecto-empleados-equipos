USE empleados_equipos;

-- ==========================================
-- EMPLEADOS
-- ==========================================

-- ------------------------------------------
-- Obtener empleado por ID
-- ------------------------------------------

DROP PROCEDURE IF EXISTS sp_obtener_empleado;
DELIMITER $$
CREATE PROCEDURE sp_obtener_empleado(IN p_id INT)
BEGIN
    SELECT *
    FROM empleados
    WHERE id = p_id;
END$$
DELIMITER ;

-- ------------------------------------------
-- Total de empleados
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_total_empleados;
DELIMITER $$
CREATE PROCEDURE sp_total_empleados()
BEGIN
    SELECT COUNT(*) AS total FROM empleados;
END$$
DELIMITER ;

-- ------------------------------------------
-- Total de departamentos
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_total_departamentos;
DELIMITER $$
CREATE PROCEDURE sp_total_departamentos()
BEGIN
    SELECT COUNT(DISTINCT departamento) AS total FROM empleados;
END$$
DELIMITER ;

-- ------------------------------------------
-- Contar empleados según búsqueda
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_contar_empleados;
DELIMITER $$
CREATE PROCEDURE sp_contar_empleados(IN p_busqueda VARCHAR(255))
BEGIN
    SET @sql = CONCAT(
        'SELECT COUNT(*) AS total FROM empleados ',
        IF(p_busqueda IS NOT NULL AND p_busqueda != '', 
           CONCAT('WHERE nombre LIKE "%', p_busqueda, '%" OR correo LIKE "%', p_busqueda, '%" OR departamento LIKE "%', p_busqueda, '%"'), 
           ''
        )
    );
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

-- ------------------------------------------
-- Listar empleados con búsqueda y paginación
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_listar_empleados;
DELIMITER $$
CREATE PROCEDURE sp_listar_empleados(
    IN p_busqueda VARCHAR(255),
    IN p_offset INT,
    IN p_limit INT
)
BEGIN
    SET @sql = CONCAT(
        'SELECT * FROM empleados ',
        IF(p_busqueda IS NOT NULL AND p_busqueda != '', 
           CONCAT('WHERE nombre LIKE "%', p_busqueda, '%" OR correo LIKE "%', p_busqueda, '%" OR departamento LIKE "%', p_busqueda, '%" '), 
           ''
        ),
        'ORDER BY id DESC LIMIT ', p_offset, ', ', p_limit
    );
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

-- ------------------------------------------
-- Verificar si un correo ya existe
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_verificar_correo;
DELIMITER $$
CREATE PROCEDURE sp_verificar_correo(IN p_correo VARCHAR(255))
BEGIN
    SELECT id
    FROM empleados
    WHERE correo = p_correo;
END$$
DELIMITER ;

-- ------------------------------------------
-- Verificar correo para edición
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_verificar_correo_edicion;
DELIMITER $$
CREATE PROCEDURE sp_verificar_correo_edicion(
    IN p_id INT,
    IN p_correo VARCHAR(255)
)
BEGIN
    SELECT id
    FROM empleados
    WHERE correo = p_correo AND id != p_id;
END$$
DELIMITER ;

-- ------------------------------------------
-- Crear nuevo empleado
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_crear_empleado;
DELIMITER $$
CREATE PROCEDURE sp_crear_empleado(
    IN p_nombre VARCHAR(255),
    IN p_correo VARCHAR(255),
    IN p_departamento VARCHAR(100)
)
BEGIN
    INSERT INTO empleados (nombre, correo, departamento)
    VALUES (p_nombre, p_correo, p_departamento);
END$$
DELIMITER ;

-- ------------------------------------------
-- Actualizar empleado
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_actualizar_empleado;
DELIMITER $$
CREATE PROCEDURE sp_actualizar_empleado(
    IN p_id INT,
    IN p_nombre VARCHAR(255),
    IN p_correo VARCHAR(255),
    IN p_departamento VARCHAR(100)
)
BEGIN
    UPDATE empleados
    SET nombre = p_nombre,
        correo = p_correo,
        departamento = p_departamento
    WHERE id = p_id;
END$$
DELIMITER ;

-- ------------------------------------------
-- Contar equipos asignados a un empleado
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_equipos_asignados_empleado;
DELIMITER $$
CREATE PROCEDURE sp_equipos_asignados_empleado(IN p_empleado_id INT)
BEGIN
    SELECT COUNT(*) AS total
    FROM equipos
    WHERE empleado_id = p_empleado_id;
END$$
DELIMITER ;

-- ------------------------------------------
-- Desasignar equipos de un empleado
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_desasignar_equipos;
DELIMITER $$
CREATE PROCEDURE sp_desasignar_equipos(IN p_empleado_id INT)
BEGIN
    UPDATE equipos
    SET empleado_id = NULL
    WHERE empleado_id = p_empleado_id;
END$$
DELIMITER ;

-- ------------------------------------------
-- Eliminar empleado
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_eliminar_empleado;
DELIMITER $$
CREATE PROCEDURE sp_eliminar_empleado(IN p_empleado_id INT)
BEGIN
    DELETE FROM empleados
    WHERE id = p_empleado_id;
END$$
DELIMITER ;

-- ==========================================
-- EQUIPOS
-- ==========================================

-- ------------------------------------------
-- Total de equipos
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_total_equipos;
DELIMITER $$
CREATE PROCEDURE sp_total_equipos()
BEGIN
    SELECT COUNT(*) AS total FROM equipos;
END$$
DELIMITER ;

-- ------------------------------------------
-- Total de equipos asignados
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_equipos_asignados;
DELIMITER $$
CREATE PROCEDURE sp_equipos_asignados()
BEGIN
    SELECT COUNT(*) AS total FROM equipos WHERE empleado_id IS NOT NULL;
END$$
DELIMITER ;

-- ------------------------------------------
-- Contar equipos según búsqueda
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_contar_equipos;
DELIMITER $$
CREATE PROCEDURE sp_contar_equipos(IN p_busqueda VARCHAR(255))
BEGIN
    SET @sql = CONCAT(
        'SELECT COUNT(*) AS total FROM equipos e LEFT JOIN empleados emp ON e.empleado_id = emp.id ',
        IF(p_busqueda IS NOT NULL AND p_busqueda != '',
           CONCAT('WHERE e.nombre LIKE "%', p_busqueda, '%" OR e.tipo LIKE "%', p_busqueda, '%" OR e.numero_serie LIKE "%', p_busqueda, '%" OR emp.nombre LIKE "%', p_busqueda, '%"'),
           ''
        )
    );
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

-- ------------------------------------------
-- Listar equipos con búsqueda y paginación
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_listar_equipos;
DELIMITER $$
CREATE PROCEDURE sp_listar_equipos(
    IN p_busqueda VARCHAR(255),
    IN p_offset INT,
    IN p_limit INT
)
BEGIN
    SET @sql = CONCAT(
        'SELECT e.*, emp.nombre AS empleado_nombre, emp.departamento AS empleado_departamento ',
        'FROM equipos e LEFT JOIN empleados emp ON e.empleado_id = emp.id ',
        IF(p_busqueda IS NOT NULL AND p_busqueda != '',
           CONCAT('WHERE e.nombre LIKE "%', p_busqueda, '%" OR e.tipo LIKE "%', p_busqueda, '%" OR e.numero_serie LIKE "%', p_busqueda, '%" OR emp.nombre LIKE "%', p_busqueda, '%" '),
           ''
        ),
        'ORDER BY e.fecha_actualizacion DESC LIMIT ', p_offset, ', ', p_limit
    );
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

-- ------------------------------------------
-- Crear nuevo equipo
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_crear_equipo;
DELIMITER $$
CREATE PROCEDURE sp_crear_equipo(
    IN p_nombre VARCHAR(255),
    IN p_tipo VARCHAR(100),
    IN p_numero_serie VARCHAR(100),
    IN p_empleado_id INT
)
BEGIN
    INSERT INTO equipos (nombre, tipo, numero_serie, empleado_id)
    VALUES (p_nombre, p_tipo, p_numero_serie, p_empleado_id);
END$$
DELIMITER ;

-- ------------------------------------------
-- Verificar número de serie (crear)
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_verificar_numero_serie;
DELIMITER $$
CREATE PROCEDURE sp_verificar_numero_serie(IN p_numero_serie VARCHAR(100))
BEGIN
    SELECT id
    FROM equipos
    WHERE numero_serie = p_numero_serie;
END$$
DELIMITER ;

-- ------------------------------------------
-- Actualizar equipo
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_actualizar_equipo;
DELIMITER $$
CREATE PROCEDURE sp_actualizar_equipo(
    IN p_id INT,
    IN p_nombre VARCHAR(255),
    IN p_tipo VARCHAR(100),
    IN p_numero_serie VARCHAR(100),
    IN p_empleado_id INT
)
BEGIN
    UPDATE equipos
    SET nombre = p_nombre,
        tipo = p_tipo,
        numero_serie = p_numero_serie,
        empleado_id = p_empleado_id
    WHERE id = p_id;
END$$
DELIMITER ;

-- ------------------------------------------
-- Verificar número de serie (editar)
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_verificar_numero_serie_editar;
DELIMITER $$
CREATE PROCEDURE sp_verificar_numero_serie_editar(
    IN p_numero_serie VARCHAR(100),
    IN p_excluir_id INT
)
BEGIN
    SELECT id
    FROM equipos
    WHERE numero_serie = p_numero_serie
      AND (p_excluir_id IS NULL OR id != p_excluir_id);
END$$
DELIMITER ;

-- ------------------------------------------
-- Eliminar equipo
-- ------------------------------------------
DROP PROCEDURE IF EXISTS sp_eliminar_equipo;
DELIMITER $$
CREATE PROCEDURE sp_eliminar_equipo(IN p_id INT)
BEGIN
    DELETE FROM equipos WHERE id = p_id;
END$$
DELIMITER ;
