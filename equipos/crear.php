<?php
require_once '../db/conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Obtener lista de empleados para el select
$stmt_empleados = $pdo->query("SELECT id, nombre, departamento FROM empleados ORDER BY nombre");
$empleados = $stmt_empleados->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $tipo = trim($_POST['tipo']);
    $numero_serie = trim($_POST['numero_serie']);
    $empleado_id = !empty($_POST['empleado_id']) ? (int)$_POST['empleado_id'] : null;
    
    // Validaciones del servidor
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = 'El nombre del equipo es requerido';
    }
    
    if (empty($tipo)) {
        $errores[] = 'El tipo de equipo es requerido';
    }
    
    if (empty($numero_serie)) {
        $errores[] = 'El número de serie es requerido';
    } else {
        // Verificar si el número de serie ya existe
        $stmt_check = $pdo->prepare("SELECT id FROM equipos WHERE numero_serie = ?");
        $stmt_check->execute([$numero_serie]);
        if ($stmt_check->fetch()) {
            $errores[] = 'El número de serie ya está registrado';
        }
    }
    
    if ($empleado_id && $empleado_id > 0) {
        // Verificar que el empleado existe
        $stmt_emp = $pdo->prepare("SELECT id FROM empleados WHERE id = ?");
        $stmt_emp->execute([$empleado_id]);
        if (!$stmt_emp->fetch()) {
            $errores[] = 'El empleado seleccionado no existe';
        }
    }
    
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO equipos (nombre, tipo, numero_serie, empleado_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $tipo, $numero_serie, $empleado_id]);
            
            $mensaje = 'Equipo creado exitosamente';
            $tipo_mensaje = 'success';
            
            // Limpiar formulario
            $nombre = $tipo = $numero_serie = '';
            $empleado_id = null;
        } catch (PDOException $e) {
            $mensaje = 'Error al crear el equipo: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipo_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Equipo - Impulsora</title>
    <!-- Bootstrap CSS -->
    <link href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../assets/libs/bootstrap/icons/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link href="../assets/libs/sweetalert/css/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
  </head>
  <body>
    <!-- Header -->
    <header>
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
          <a class="navbar-brand" href="../index.php">
          <img src="https://grupoimpulsora.com/wp-content/uploads/2024/03/Logotipo-Barra-de-navegacion.png" height="32" alt="Logo de Impulsora">
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                <a class="nav-link" href="../index.php">Inicio</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../empleados/listar.php">Empleados</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="listar.php">Equipos</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <!-- Main -->
    <main class="container mt-4">
      <section class="row mb-4">
        <div class="col-12">
          <div class="d-sm-flex justify-content-between align-items-center">
            <h1 class="h2 text-primary d-block d-sm-inline">Crear Nuevo Equipo</h1>
            <a href="listar.php" class="btn btn-outline-secondary d-block d-sm-inline text-sm-center">
            <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
          </div>
        </div>
      </section>
      <!-- Formulario -->
      <section class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="bi-laptop me-2"></i>Información del Equipo
              </h5>
            </div>
            <div class="card-body">
              <form id="formularioEquipo" method="POST" novalidate>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre del Equipo *</label>
                    <input type="text" 
                      class="form-control" 
                      id="nombre" 
                      name="nombre" 
                      value="<?php echo htmlspecialchars($nombre ?? ''); ?>" 
                      placeholder="Ej: Laptop Dell XPS 13"
                      required>
                    <div class="invalid-feedback">
                      Por favor ingresa el nombre del equipo.
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="tipo" class="form-label">Tipo de Equipo *</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                      <option value="">Selecciona un tipo</option>
                      <option value="Laptop" <?php echo (isset($tipo) && $tipo === 'Laptop') ? 'selected' : ''; ?>>Laptop</option>
                      <option value="Desktop" <?php echo (isset($tipo) && $tipo === 'Desktop') ? 'selected' : ''; ?>>Desktop</option>
                      <option value="Monitor" <?php echo (isset($tipo) && $tipo === 'Monitor') ? 'selected' : ''; ?>>Monitor</option>
                      <option value="Impresora" <?php echo (isset($tipo) && $tipo === 'Impresora') ? 'selected' : ''; ?>>Impresora</option>
                      <option value="Periférico" <?php echo (isset($tipo) && $tipo === 'Periférico') ? 'selected' : ''; ?>>Periférico</option>
                      <option value="Móvil" <?php echo (isset($tipo) && $tipo === 'Móvil') ? 'selected' : ''; ?>>Móvil</option>
                      <option value="Tablet" <?php echo (isset($tipo) && $tipo === 'Tablet') ? 'selected' : ''; ?>>Tablet</option>
                      <option value="Otro" <?php echo (isset($tipo) && $tipo === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                    <div class="invalid-feedback">
                      Por favor selecciona un tipo de equipo.
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="numero_serie" class="form-label">Número de Serie *</label>
                    <input type="text" 
                      class="form-control" 
                      id="numero_serie" 
                      name="numero_serie" 
                      value="<?php echo htmlspecialchars($numero_serie ?? ''); ?>" 
                      placeholder="Ej: DELL001, HP123456"
                      required>
                    <div class="invalid-feedback">
                      Por favor ingresa el número de serie.
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="empleado_id" class="form-label">Asignar a Empleado</label>
                    <select class="form-select" id="empleado_id" name="empleado_id">
                      <option value="">Sin asignar</option>
                      <?php foreach ($empleados as $empleado): ?>
                      <option value="<?php echo $empleado['id']; ?>" 
                        <?php echo (isset($empleado_id) && $empleado_id == $empleado['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($empleado['nombre'] . ' (' . $empleado['departamento'] . ')'); ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                    <div class="form-text">
                      Opcional: Puedes asignar el equipo a un empleado ahora o hacerlo después.
                    </div>
                  </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
                  <button type="submit" class="btn btn-primary">
                  Crear Equipo
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>
    <!-- Footer -->
    <footer class="container mt-5 pt-3 border-top">
      <div class="row">
        <div class="col">
          <h6 class="text-success fs-small text-center">Sistema de Gestión de Empleados y Equipos</h6>
          <p class="text-muted fs-small text-center">© 2025 Carlos Medina</p>
        </div>
      </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="../assets/libs/sweetalert/js/sweetalert2.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/main.js"></script>
    <script>
      // Mostrar mensaje de resultado
      <?php if (!empty($mensaje)): ?>
          <?php if ($tipo_mensaje === 'success'): ?>
              mostrarExito('<?php echo addslashes($mensaje); ?>');
              // Deshabilitar botón de crear
              const botonCrear = document.querySelector('#formularioEquipo button[type="submit"]');
              if(botonCrear) botonCrear.disabled = true;
              // Redirigir al listado
              setTimeout(() => {
                  window.location.href = 'listar.php';
              }, 1500);
          <?php else: ?>
              mostrarError('<?php echo addslashes($mensaje); ?>');
          <?php endif; ?>
      <?php endif; ?>
    </script>
  </body>
</html>