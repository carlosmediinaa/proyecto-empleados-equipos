<?php
// ============================
// Helpers generales
// ============================

// Obtener la sección actual para el navbar
function obtenerSeccionActual() {
    $uri_actual = $_SERVER['REQUEST_URI'];
    $seccion = 'home'; // Valor por defecto

    if (strpos($uri_actual, '/views/empleados/') !== false) {
        $seccion = 'empleados';
    } elseif (strpos($uri_actual, '/views/equipos/') !== false) {
        $seccion = 'equipos';
    }

    return $seccion;
}

// Obtener la página actual (para paginación)
function obtenerPaginaActual() {
    return isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
}

// Obtener el término de búsqueda
function obtenerBusqueda($nombre_param = 'busqueda') {
    return isset($_GET[$nombre_param]) ? trim($_GET[$nombre_param]) : '';
}

// ============================
// Helpers para listados
// ============================

// Obtener datos de un listado desde un controller
function obtenerDatosListado($controller, $tipo = 'items') {
    $pagina = obtenerPaginaActual();
    $busqueda = obtenerBusqueda();

    $data = $controller->listar($pagina, $busqueda);

    return [
        'items' => $data[$tipo] ?? [],
        'pagina_actual' => $data['pagina_actual'],
        'total_paginas' => $data['total_paginas'],
        'total_registros' => $data['total_registros'],
        'busqueda' => $busqueda
    ];
}

// Obtener mensajes de éxito o error del controller
function obtenerMensaje($controller) {
    $mensaje_data = $controller->manejarMensajes();
    return [
        'mensaje' => $mensaje_data['mensaje'],
        'tipo' => $mensaje_data['tipo']
    ];
}

// ============================
// Helpers para formularios
// ============================

// Escapar texto para mostrar en formularios
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Mantener valor de input después de POST
function old($campo, $default = '') {
    return $_POST[$campo] ?? $default;
}

// ============================
// Procesamiento de formularios
// ============================

// Procesar creación de registros
function procesarFormulario($controller, $postData, $accion = 'crear') {
    $resultado = [];
    $valores = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resultado = $controller->$accion($postData);

        if ($resultado['tipo'] === 'success') {
            // Reiniciar valores
            foreach ($postData as $key => $val) {
                $valores[$key] = '';
            }
        } else {
            // Mantener valores ingresados
            foreach ($postData as $key => $val) {
                $valores[$key] = $val;
            }
        }
    }

    return [
        'resultado' => $resultado,
        'valores' => $valores
    ];
}

// Procesar edición de registros
function procesarEdicion($controller, $id, $getData, $postData, $listarUrl, $accion = 'actualizar', $claveRegistro = 'registro') {
    $resultado = ['mensaje' => '', 'tipo' => ''];
    $registro = [];

    // Validar ID
    $id = (int)($getData['id'] ?? 0);
    if ($id <= 0) {
        header("Location: $listarUrl");
        exit;
    }

    // Procesar POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($postData)) {
        $resultado = $controller->$accion($id, $postData);
        $registro = $resultado[$claveRegistro] ?? [];
    } else {
        // Obtener datos del registro
        $registro = $controller->obtener($id);
        if (!$registro) {
            header("Location: $listarUrl");
            exit;
        }
    }

    return [
        'resultado' => $resultado,
        'registro' => $registro,
        'id' => $id
    ];
}
?>
