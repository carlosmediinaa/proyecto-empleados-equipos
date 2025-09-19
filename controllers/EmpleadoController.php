<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db/conexion.php';

class EmpleadoController {
    private $pdo;
    private $registrosPorPagina = 5;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // =======================
    // Listar empleados
    // =======================
    public function listar($pagina = 1, $busqueda = '') {
        $offset = ($pagina - 1) * $this->registrosPorPagina;

        $stmt_count = $this->pdo->prepare("CALL empleados_equipos.sp_contar_empleados(:busqueda)");
        $stmt_count->bindValue(':busqueda', $busqueda);
        $stmt_count->execute();
        $total_registros = $stmt_count->fetch()['total'] ?? 0;
        $stmt_count->closeCursor();

        $total_paginas = ceil($total_registros / $this->registrosPorPagina);

        $stmt = $this->pdo->prepare("CALL empleados_equipos.sp_listar_empleados(:busqueda, :offset, :limit)");
        $stmt->bindValue(':busqueda', $busqueda);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $this->registrosPorPagina, PDO::PARAM_INT);
        $stmt->execute();
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return [
            'empleados'       => $empleados,
            'pagina_actual'   => $pagina,
            'total_paginas'   => $total_paginas,
            'total_registros' => $total_registros,
            'busqueda'        => $busqueda
        ];
    }

    // =======================
    // Crear empleado
    // =======================
    public function crear($datos) {
        $nombre = trim($datos['nombre'] ?? '');
        $correo = trim($datos['correo'] ?? '');
        $departamento = trim($datos['departamento'] ?? '');
        $errores = [];

        if (empty($nombre)) $errores[] = 'El nombre es requerido';
        if (empty($correo)) $errores[] = 'El correo es requerido';
        elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo inválido';
        else {
            $stmt_check = $this->pdo->prepare("CALL sp_verificar_correo(?)");
            $stmt_check->execute([$correo]);
            if ($stmt_check->fetch()) $errores[] = 'El correo ya está registrado';
            $stmt_check->closeCursor();
        }
        if (empty($departamento)) $errores[] = 'El departamento es requerido';

        if (!empty($errores)) {
            return ['mensaje' => implode('<br>', $errores), 'tipo' => 'error'];
        }

        try {
            $stmt = $this->pdo->prepare("CALL sp_crear_empleado(?, ?, ?)");
            $stmt->execute([$nombre, $correo, $departamento]);
            $stmt->closeCursor();
            return ['mensaje' => 'Empleado creado exitosamente', 'tipo' => 'success'];
        } catch (PDOException $e) {
            return ['mensaje' => 'Error al crear el empleado: ' . $e->getMessage(), 'tipo' => 'error'];
        }
    }

    // =======================
    // Obtener un empleado por ID
    // =======================
    public function obtener($id) {
        $stmt = $this->pdo->prepare("CALL sp_obtener_empleado(?)");
        $stmt->execute([$id]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $empleado ?: null;
    }

    // =======================
    // Actualizar empleado
    // =======================
    public function actualizar($id, $datos) {
        $empleado = $this->obtener($id);
        if (!$empleado) {
            return ['mensaje' => 'Empleado no encontrado', 'tipo' => 'error', 'empleado' => null];
        }

        $nombre = trim($datos['nombre'] ?? $empleado['nombre']);
        $correo = trim($datos['correo'] ?? $empleado['correo']);
        $departamento = trim($datos['departamento'] ?? $empleado['departamento']);
        $errores = [];

        if (empty($nombre)) $errores[] = 'El nombre es requerido';
        if (empty($correo)) $errores[] = 'El correo es requerido';
        elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo inválido';
        if (empty($departamento)) $errores[] = 'Selecciona un departamento';

        if (!empty($errores)) {
            return ['mensaje' => implode('<br>', $errores), 'tipo' => 'warning', 'empleado' => $empleado];
        }

        try {
            $stmt_verificar = $this->pdo->prepare("CALL sp_verificar_correo_edicion(?, ?)");
            $stmt_verificar->execute([$id, $correo]);
            $correo_existente = $stmt_verificar->fetch(PDO::FETCH_ASSOC);
            $stmt_verificar->closeCursor();

            if ($correo_existente) {
                return ['mensaje' => 'El correo ya está registrado en otro empleado', 'tipo' => 'warning', 'empleado' => $empleado];
            }

            $stmt_actualizar = $this->pdo->prepare("CALL sp_actualizar_empleado(?, ?, ?, ?)");
            $stmt_actualizar->execute([$id, $nombre, $correo, $departamento]);
            $stmt_actualizar->closeCursor();

            $empleado['nombre'] = $nombre;
            $empleado['correo'] = $correo;
            $empleado['departamento'] = $departamento;

            return ['mensaje' => 'Empleado actualizado correctamente', 'tipo' => 'success', 'empleado' => $empleado];
        } catch (PDOException $e) {
            return ['mensaje' => 'Error al actualizar el empleado: ' . $e->getMessage(), 'tipo' => 'error', 'empleado' => $empleado];
        }
    }

    // =======================
    // Eliminar empleado
    // =======================
    public function eliminar($id) {
    $id = (int)$id;
    if ($id <= 0) {
        return ['mensaje' => 'ID de empleado inválido', 'tipo' => 'error'];
    }

    try {
        // Obtener empleado usando SP
        $stmt = $this->pdo->prepare("CALL sp_obtener_empleado(?)");
        $stmt->execute([$id]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$empleado) {
            return ['mensaje' => 'Empleado no encontrado', 'tipo' => 'error'];
        }

        // Verificar equipos asignados usando SP
        $stmt_equipos = $this->pdo->prepare("CALL sp_equipos_asignados_empleado(?)");
        $stmt_equipos->execute([$id]);
        $equipos_asignados = $stmt_equipos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt_equipos->closeCursor();

        // Desasignar equipos si hay alguno
        if ($equipos_asignados > 0) {
            $stmt_desasignar = $this->pdo->prepare("CALL sp_desasignar_equipos(?)");
            $stmt_desasignar->execute([$id]);
            $stmt_desasignar->closeCursor();
        }

        // Eliminar empleado usando SP
        $stmt_eliminar = $this->pdo->prepare("CALL sp_eliminar_empleado(?)");
        $stmt_eliminar->execute([$id]);
        $stmt_eliminar->closeCursor();

        return [
            'mensaje' => 'Empleado "' . $this->e($empleado['nombre']) . '" eliminado exitosamente',
            'tipo' => 'success'
        ];

    } catch (PDOException $e) {
        return ['mensaje' => 'Error al eliminar empleado: ' . $e->getMessage(), 'tipo' => 'error'];
    }
    }

   // =======================
    // Manejar mensajes de empleados
    // =======================
    public function manejarMensajes() {
        return [
            'mensaje' => $_GET['mensaje'] ?? '',
            'tipo' => $_GET['tipo'] ?? ''
        ];
    }
}
