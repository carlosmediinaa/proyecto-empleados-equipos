<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'views/equipos/listar.php');
    exit;
}

try {
    // Obtener equipo antes de eliminar
    $stmt = $pdo->prepare("SELECT * FROM equipos WHERE id = ?");
    $stmt->execute([$id]);
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipo) {
        header('Location: ' . BASE_URL . 'views/equipos/listar.php');
        exit;
    }

    // Eliminar equipo usando procedimiento almacenado
    $stmt_eliminar = $pdo->prepare("CALL sp_eliminar_equipo(?)");
    $stmt_eliminar->execute([$id]);

    header('Location: ' . BASE_URL . 'views/equipos/listar.php?mensaje=eliminado&nombre=' . urlencode($equipo['nombre']));
    exit;

} catch (PDOException $e) {
    header('Location: ' . BASE_URL . 'views/equipos/listar.php?mensaje=error&error=' . urlencode($e->getMessage()));
    exit;
}
?>
