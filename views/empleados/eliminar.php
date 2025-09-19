<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db/conexion.php';
require_once __DIR__ . '/../../controllers/EmpleadoController.php';

$controller = new EmpleadoController($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$resultado = $controller->eliminar($id);

// Redirigir a listar.php con mensaje y tipo
header('Location: ' . BASE_URL . 'views/empleados/listar.php?mensaje=' . urlencode($resultado['mensaje']) . '&tipo=' . $resultado['tipo']);
exit;