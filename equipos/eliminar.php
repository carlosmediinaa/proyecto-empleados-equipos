<?php
require_once '../db/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Obtener equipo
$stmt = $pdo->prepare("SELECT * FROM equipos WHERE id = ?");
$stmt->execute([$id]);
$equipo = $stmt->fetch();

if (!$equipo) {
    header('Location: listar.php');
    exit;
}

try {
    // Eliminar equipo
    $stmt_eliminar = $pdo->prepare("DELETE FROM equipos WHERE id = ?");
    $stmt_eliminar->execute([$id]);
    
    // Redirigir con mensaje de éxito
    header('Location: listar.php?mensaje=eliminado&nombre=' . urlencode($equipo['nombre']));
    exit;
} catch (PDOException $e) {
    // Redirigir con mensaje de error
    header('Location: listar.php?mensaje=error&error=' . urlencode($e->getMessage()));
    exit;
}
?>
