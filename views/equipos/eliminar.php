<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db/conexion.php';
require_once __DIR__ . '/../../controllers/EquipoController.php';

$controller = new EquipoController($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$resultado = $controller->eliminar($id);

$mensaje = urlencode($resultado['mensaje']);
$tipo = $resultado['tipo'];

header("Location: " . BASE_URL . "views/equipos/listar.php?mensaje=$tipo&nombre=$mensaje");
exit;
