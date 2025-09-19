<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'views/empleados/listar.php');
    exit;
}

try {
    // Obtener empleado
    $stmt = $pdo->prepare("SELECT * FROM empleados WHERE id = ?");
    $stmt->execute([$id]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if (!$empleado) {
        header('Location: ' . BASE_URL . 'views/empleados/listar.php');
        exit;
    }

    // Verificar equipos asignados usando procedimiento
    $stmt_equipos = $pdo->prepare("CALL sp_equipos_asignados_empleado(?)");
    $stmt_equipos->execute([$id]);
    $equipos_asignados = $stmt_equipos->fetch(PDO::FETCH_ASSOC)['total'];
    $stmt_equipos->closeCursor();

    if ($equipos_asignados > 0) {
        // Desasignar equipos usando procedimiento
        $stmt_desasignar = $pdo->prepare("CALL sp_desasignar_equipos(?)");
        $stmt_desasignar->execute([$id]);
        $stmt_desasignar->closeCursor();
    }

    // Eliminar empleado usando procedimiento
    $stmt_eliminar = $pdo->prepare("CALL sp_eliminar_empleado(?)");
    $stmt_eliminar->execute([$id]);
    $stmt_eliminar->closeCursor();

    // Redirigir con mensaje de éxito
    header('Location: ' . BASE_URL . 'views/empleados/listar.php?mensaje=eliminado&nombre=' . urlencode($empleado['nombre']));
    exit;

} catch (PDOException $e) {
    // Redirigir con mensaje de error
    header('Location: ' . BASE_URL . 'views/empleados/listar.php?mensaje=error&error=' . urlencode($e->getMessage()));
    exit;
}
?>
