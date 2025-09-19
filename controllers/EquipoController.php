<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db/conexion.php';

class EquipoController {
    private $pdo;
    private $registrosPorPagina = 5;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // =======================
    // Listar equipos
    // =======================
    public function listar($pagina = 1, $busqueda = '') {
        $offset = ($pagina - 1) * $this->registrosPorPagina;

        $stmt_count = $this->pdo->prepare("CALL sp_contar_equipos(:busqueda)");
        $stmt_count->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt_count->execute();
        $total_registros = $stmt_count->fetch()['total'] ?? 0;
        $stmt_count->closeCursor();

        $total_paginas = ceil($total_registros / $this->registrosPorPagina);

        $stmt = $this->pdo->prepare("CALL sp_listar_equipos(:busqueda, :offset, :limit)");
        $stmt->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $this->registrosPorPagina, PDO::PARAM_INT);
        $stmt->execute();
        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return [
            'equipos'        => $equipos,
            'pagina_actual'  => $pagina,
            'total_paginas'  => $total_paginas,
            'total_registros'=> $total_registros,
            'busqueda'       => $busqueda
        ];
    }

    // =======================
    // Listar empleados para select
    // =======================
    public function listarEmpleados() {
        $stmt = $this->pdo->query("CALL sp_listar_empleados('', 0, 1000)");
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $empleados;
    }

    // =======================
    // Crear equipo
    // =======================
    public function crear($datos) {
        $nombre = trim($datos['nombre'] ?? '');
        $tipo = trim($datos['tipo'] ?? '');
        $numero_serie = trim($datos['numero_serie'] ?? '');
        $empleado_id = !empty($datos['empleado_id']) ? (int)$datos['empleado_id'] : null;

        $errores = [];

        if (empty($nombre)) $errores[] = 'El nombre del equipo es requerido';
        if (empty($tipo)) $errores[] = 'El tipo del equipo es requerido';
        if (empty($numero_serie)) $errores[] = 'El número de serie es requerido';
        else {
            $stmt_check = $this->pdo->prepare("CALL sp_verificar_numero_serie(?)");
            $stmt_check->execute([$numero_serie]);
            if ($stmt_check->fetch()) $errores[] = 'El número de serie ya está registrado';
            $stmt_check->closeCursor();
        }

        if ($empleado_id && $empleado_id > 0) {
            $stmt_emp = $this->pdo->prepare("SELECT id FROM empleados WHERE id = ?");
            $stmt_emp->execute([$empleado_id]);
            if (!$stmt_emp->fetch()) $errores[] = 'El empleado seleccionado no existe';
        }

        if (!empty($errores)) {
            return ['mensaje' => implode('<br>', $errores), 'tipo' => 'error'];
        }

        try {
            $stmt = $this->pdo->prepare("CALL sp_crear_equipo(?, ?, ?, ?)");
            $stmt->execute([$nombre, $tipo, $numero_serie, $empleado_id]);
            $stmt->closeCursor();
            return ['mensaje' => 'Equipo creado exitosamente', 'tipo' => 'success'];
        } catch (PDOException $e) {
            return ['mensaje' => 'Error al crear el equipo: ' . $e->getMessage(), 'tipo' => 'error'];
        }
    }

    // =======================
    // Obtener un equipo por ID
    // =======================
    public function obtener($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM equipos WHERE id = ?");
        $stmt->execute([$id]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $equipo ?: null;
    }

    // =======================
    // Actualizar equipo
    // =======================
    public function actualizar($id, $datos) {
        $nombre = trim($datos['nombre'] ?? '');
        $tipo = trim($datos['tipo'] ?? '');
        $numero_serie = trim($datos['numero_serie'] ?? '');
        $empleado_id = !empty($datos['empleado_id']) ? (int)$datos['empleado_id'] : null;

        $errores = [];

        if (empty($nombre)) $errores[] = 'El nombre del equipo es requerido';
        if (empty($tipo)) $errores[] = 'El tipo de equipo es requerido';
        if (empty($numero_serie)) $errores[] = 'El número de serie es requerido';

        if (!empty($numero_serie)) {
            $stmt_check = $this->pdo->prepare("CALL sp_verificar_numero_serie_editar(?, ?)");
            $stmt_check->execute([$numero_serie, $id]);
            if ($stmt_check->fetch()) $errores[] = 'El número de serie ya está registrado por otro equipo';
            $stmt_check->closeCursor();
        }

        if ($empleado_id && $empleado_id > 0) {
            $stmt_emp = $this->pdo->prepare("SELECT id FROM empleados WHERE id = ?");
            $stmt_emp->execute([$empleado_id]);
            if (!$stmt_emp->fetch()) $errores[] = 'El empleado seleccionado no existe';
        }

        if (!empty($errores)) {
            return ['mensaje' => implode('<br>', $errores), 'tipo' => 'error'];
        }

        try {
            $stmt_update = $this->pdo->prepare("CALL sp_actualizar_equipo(?, ?, ?, ?, ?)");
            $stmt_update->execute([$id, $nombre, $tipo, $numero_serie, $empleado_id]);
            $stmt_update->closeCursor();
            return ['mensaje' => 'Equipo actualizado correctamente', 'tipo' => 'success'];
        } catch (PDOException $e) {
            return ['mensaje' => 'Error al actualizar el equipo: ' . $e->getMessage(), 'tipo' => 'error'];
        }
    }

    // =======================
    // Eliminar equipo
    // =======================
    public function eliminar($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return ['mensaje' => 'ID de equipo inválido', 'tipo' => 'error'];
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM equipos WHERE id = ?");
            $stmt->execute([$id]);
            $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$equipo) {
                return ['mensaje' => 'Equipo no encontrado.', 'tipo' => 'error'];
            }

            $stmt_del = $this->pdo->prepare("CALL sp_eliminar_equipo(?)");
            $stmt_del->execute([$id]);
            $stmt_del->closeCursor();

            return [
                'mensaje' => 'Equipo "' . $this->e($equipo['nombre']) . '" eliminado exitosamente',
                'tipo' => 'success'
            ];

        } catch (PDOException $e) {
            return ['mensaje' => 'Error al eliminar el equipo: ' . $e->getMessage(), 'tipo' => 'error'];
        }
    }

    // =======================
    // Manejar mensajes de equipos
    // =======================
    public function manejarMensajes() {
        $mensaje = '';
        $tipo_mensaje = '';

        if (isset($_GET['mensaje'])) {
            if ($_GET['mensaje'] === 'success' && isset($_GET['nombre'])) {
                $mensaje = urldecode($_GET['nombre']);
                $tipo_mensaje = 'success';
            } elseif ($_GET['mensaje'] === 'error' && isset($_GET['nombre'])) {
                $mensaje = urldecode($_GET['nombre']);
                $tipo_mensaje = 'error';
            }
        }

        return [
            'mensaje' => $mensaje,
            'tipo' => $tipo_mensaje
        ];
    }
}
