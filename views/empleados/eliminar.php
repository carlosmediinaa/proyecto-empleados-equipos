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

    if (!$empleado) {
        header('Location: ' . BASE_URL . 'views/empleados/listar.php');
        exit;
    }

    // Verificar si el empleado tiene equipos asignados
    $stmt_equipos = $pdo->prepare("SELECT COUNT(*) as total FROM equipos WHERE empleado_id = ?");
    $stmt_equipos->execute([$id]);
    $equipos_asignados = $stmt_equipos->fetch()['total'];

    if ($equipos_asignados > 0) {
        // Si tiene equipos asignados, desasignarlos primero
        $stmt_desasignar = $pdo->prepare("UPDATE equipos SET empleado_id = NULL WHERE empleado_id = ?");
        $stmt_desasignar->execute([$id]);
    }

    // Eliminar empleado
    $stmt_eliminar = $pdo->prepare("DELETE FROM empleados WHERE id = ?");
    $stmt_eliminar->execute([$id]);

    // Redirigir con mensaje de éxito
    header('Location: ' . BASE_URL . 'views/empleados/listar.php?mensaje=eliminado&nombre=' . urlencode($empleado['nombre']));
    exit;

} catch (PDOException $e) {
    // Redirigir con mensaje de error
    header('Location: ' . BASE_URL . 'views/empleados/listar.php?mensaje=error&error=' . urlencode($e->getMessage()));
    exit;
}
?>
