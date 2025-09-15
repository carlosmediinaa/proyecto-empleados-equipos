<?php
require_once '../db/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Obtener empleado
$stmt = $pdo->prepare("SELECT * FROM empleados WHERE id = ?");
$stmt->execute([$id]);
$empleado = $stmt->fetch();

if (!$empleado) {
    header('Location: listar.php');
    exit;
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $departamento = trim($_POST['departamento']);
    
    // Validaciones del servidor
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = 'El nombre es requerido';
    }
    
    if (empty($correo)) {
        $errores[] = 'El correo es requerido';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no tiene un formato válido';
    } else {
        // Verificar si el correo ya existe (excluyendo el empleado actual)
        $stmt_check = $pdo->prepare("SELECT id FROM empleados WHERE correo = ? AND id != ?");
        $stmt_check->execute([$correo, $id]);
        if ($stmt_check->fetch()) {
            $errores[] = 'El correo ya está registrado por otro empleado';
        }
    }
    
    if (empty($departamento)) {
        $errores[] = 'El departamento es requerido';
    }
    
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("UPDATE empleados SET nombre = ?, correo = ?, departamento = ? WHERE id = ?");
            $stmt->execute([$nombre, $correo, $departamento, $id]);
            
            $mensaje = 'Empleado actualizado exitosamente';
            $tipo_mensaje = 'success';
            
            // Actualizar datos del empleado
            $empleado['nombre'] = $nombre;
            $empleado['correo'] = $correo;
            $empleado['departamento'] = $departamento;
        } catch (PDOException $e) {
            $mensaje = 'Error al actualizar el empleado: ' . $e->getMessage();
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
    <title>Editar Empleado - Impulsora</title>
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
                <a class="nav-link active" href="listar.php">Empleados</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../equipos/listar.php">Equipos</a>
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
            <h1 class="h2 text-success d-block d-sm-inline">Editar Empleado</h1>
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
                <i class="bi bi-pencil me-2"></i>Información del Empleado
              </h5>
            </div>
            <div class="card-body">
              <form id="formularioEmpleado" method="POST" novalidate>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre Completo *</label>
                    <input type="text" 
                      class="form-control" 
                      id="nombre" 
                      name="nombre" 
                      value="<?php echo htmlspecialchars($empleado['nombre']); ?>" 
                      required>
                    <div class="invalid-feedback">
                      Por favor ingresa el nombre completo.
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="correo" class="form-label">Correo Electrónico *</label>
                    <input type="email" 
                      class="form-control" 
                      id="correo" 
                      name="correo" 
                      value="<?php echo htmlspecialchars($empleado['correo']); ?>" 
                      required>
                    <div class="invalid-feedback">
                      Por favor ingresa un correo válido.
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="departamento" class="form-label">Departamento *</label>
                  <select class="form-select" id="departamento" name="departamento" required>
                    <option value="">Selecciona un departamento</option>
                    <option value="IT" <?php echo $empleado['departamento'] === 'IT' ? 'selected' : ''; ?>>IT</option>
                    <option value="Recursos Humanos" <?php echo $empleado['departamento'] === 'Recursos Humanos' ? 'selected' : ''; ?>>Recursos Humanos</option>
                    <option value="Ventas" <?php echo $empleado['departamento'] === 'Ventas' ? 'selected' : ''; ?>>Ventas</option>
                    <option value="Marketing" <?php echo $empleado['departamento'] === 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                    <option value="Finanzas" <?php echo $empleado['departamento'] === 'Finanzas' ? 'selected' : ''; ?>>Finanzas</option>
                    <option value="Operaciones" <?php echo $empleado['departamento'] === 'Operaciones' ? 'selected' : ''; ?>>Operaciones</option>
                    <option value="Otro" <?php echo $empleado['departamento'] === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                  </select>
                  <div class="invalid-feedback">
                    Por favor selecciona un departamento.
                  </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
                  <button type="submit" class="btn btn-success">
                  Actualizar Empleado
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
              const botonCrear = document.querySelector('#formularioEmpleado button[type="submit"]');
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
    </script>
  </body>
</html>
