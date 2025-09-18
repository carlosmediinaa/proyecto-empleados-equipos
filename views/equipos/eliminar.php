<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'views/equipos/listar.php');
    exit;
}

try {
    // Obtener equipo
    $stmt = $pdo->prepare("SELECT * FROM equipos WHERE id = ?");
    $stmt->execute([$id]);
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipo) {
        header('Location: ' . BASE_URL . 'views/equipos/listar.php');
        exit;
    }

    // Eliminar equipo
    $stmt_eliminar = $pdo->prepare("DELETE FROM equipos WHERE id = ?");
    $stmt_eliminar->execute([$id]);

    // Redirigir con mensaje de éxito
    header('Location: ' . BASE_URL . 'views/equipos/listar.php?mensaje=eliminado&nombre=' . urlencode($equipo['nombre']));
    exit;

} catch (PDOException $e) {
    // Redirigir con mensaje de error
    header('Location: ' . BASE_URL . 'views/equipos/listar.php?mensaje=error&error=' . urlencode($e->getMessage()));
    exit;
}
?>
