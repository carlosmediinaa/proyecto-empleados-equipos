<?php
  require_once __DIR__ . '/../../config.php';
  require_once __DIR__ . '/../../db/conexion.php';
  
  global $DEPARTAMENTOS;
  
  $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
  if ($id <= 0) {
      header("Location: " . BASE_URL . "views/empleados/listar.php");
      exit;
  }
  
  // Obtener datos del empleado
  $stmt = $pdo->prepare("SELECT * FROM empleados WHERE id = ?");
  $stmt->execute([$id]);
  $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$empleado) {
      header("Location: " . BASE_URL . "views/empleados/listar.php");
      exit;
  }
  
  $mensaje = '';
  $tipo_mensaje = '';
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nombre = trim($_POST['nombre']);
      $correo = trim($_POST['correo']);
      $departamento = trim($_POST['departamento']);
  
      $errores = [];
      if (empty($nombre)) $errores[] = "El nombre es requerido.";
      if (empty($correo)) $errores[] = "El correo es requerido.";
      if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo inválido.";
      if (empty($departamento)) $errores[] = "Selecciona un departamento.";
  
      if (empty($errores)) {
          $stmt = $pdo->prepare("UPDATE empleados SET nombre = ?, correo = ?, departamento = ? WHERE id = ?");
          if ($stmt->execute([$nombre, $correo, $departamento, $id])) {
              $mensaje = "Empleado actualizado correctamente.";
              $tipo_mensaje = "success";
              // Actualizar datos del empleado
              $empleado['nombre'] = $nombre;
              $empleado['correo'] = $correo;
              $empleado['departamento'] = $departamento;
          } else {
              $mensaje = "Error al actualizar el empleado.";
              $tipo_mensaje = "error";
          }
      } else {
          $mensaje = implode('<br>', $errores);
          $tipo_mensaje = "warning";
      }
  }
  ?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h1 class="h2 text-success">Crear Nuevo Empleado</h1>
      <a href="listar.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Volver
      </a>
    </div>
  </section>
  <section class="row justify-content-center mt-4">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bi bi-pencil me-2"></i>Editar Empleado
          </h5>
        </div>
        <div class="card-body">
          <form id="formularioEmpleado" method="POST" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre Completo *</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                  value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>
                <div class="invalid-feedback">Por favor ingresa el nombre completo.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="correo" class="form-label">Correo Electrónico *</label>
                <input type="email" class="form-control" id="correo" name="correo"
                  value="<?php echo htmlspecialchars($empleado['correo']); ?>" required>
                <div class="invalid-feedback">Por favor ingresa un correo válido.</div>
              </div>
            </div>
            <div class="mb-3">
              <label for="departamento" class="form-label">Departamento *</label>
              <select class="form-select" id="departamento" name="departamento" required>
                <option value="">Selecciona un departamento</option>
                <?php
                  foreach ($DEPARTAMENTOS as $dep) {
                      $selected = ($empleado['departamento'] === $dep) ? 'selected' : '';
                      echo "<option value=\"$dep\" $selected>$dep</option>";
                  }
                  ?>
              </select>
              <div class="invalid-feedback">Por favor selecciona un departamento.</div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="<?php echo BASE_URL; ?>views/empleados/listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
              <button type="submit" class="btn btn-success">Actualizar Empleado</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
      <?php if (!empty($mensaje)): ?>
          <?php if ($tipo_mensaje === 'success'): ?>
              mostrarExito(<?php echo json_encode($mensaje); ?>);
              const botonActualizar = document.querySelector('#formularioEmpleado button[type="submit"]');
              if (botonActualizar) botonActualizar.disabled = true;
              setTimeout(() => { window.location.href = 'listar.php'; }, 1500);
          <?php else: ?>
              mostrarError(<?php echo json_encode($mensaje); ?>);
          <?php endif; ?>
      <?php endif; ?>
  });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
