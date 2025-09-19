<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db/conexion.php';

global $DEPARTAMENTOS;

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $departamento = trim($_POST['departamento']);

    $errores = [];

    // Validaciones
    if (empty($nombre)) {
        $errores[] = 'El nombre es requerido';
    }

    if (empty($correo)) {
        $errores[] = 'El correo es requerido';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no tiene un formato válido';
    } else {
        // Llamar al procedimiento para verificar correo
        $stmt_check = $pdo->prepare("CALL sp_verificar_correo(?)");
        $stmt_check->execute([$correo]);
        if ($stmt_check->fetch()) {
            $errores[] = 'El correo ya está registrado';
        }
        $stmt_check->closeCursor();
    }

    if (empty($departamento)) {
        $errores[] = 'El departamento es requerido';
    }

    // Si no hay errores, crear empleado usando procedimiento
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("CALL sp_crear_empleado(?, ?, ?)");
            $stmt->execute([$nombre, $correo, $departamento]);
            $stmt->closeCursor();

            $mensaje = 'Empleado creado exitosamente';
            $tipo_mensaje = 'success';

            // Limpiar formulario
            $nombre = $correo = $departamento = '';
        } catch (PDOException $e) {
            $mensaje = 'Error al crear el empleado: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipo_mensaje = 'error';
    }
}
?>


<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h1 class="h2 text-success">Editar Empleado</h1>
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
            <i class="bi bi-person-plus-fill me-2"></i>Información del Empleado
          </h5>
        </div>
        <div class="card-body">
          <form id="formularioEmpleado" method="POST" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre Completo *</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                  value="<?php echo htmlspecialchars($nombre ?? ''); ?>" 
                  placeholder="Ej: Juan Pérez"
                  required>
                <div class="invalid-feedback">Por favor ingresa el nombre completo.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="correo" class="form-label">Correo Electrónico *</label>
                <input type="email" class="form-control" id="correo" name="correo"
                  value="<?php echo htmlspecialchars($correo ?? ''); ?>" 
                  placeholder="Ej: juan.perez@empresa.com"
                  required>
                <div class="invalid-feedback">Por favor ingresa un correo válido.</div>
              </div>
            </div>
            <div class="mb-3">
              <label for="departamento" class="form-label">Departamento *</label>
              <select class="form-select" id="departamento" name="departamento" required>
                <option value="">Selecciona un departamento</option>
                <?php
                  foreach ($DEPARTAMENTOS as $dep) {
                      $selected = (isset($departamento) && $departamento === $dep) ? 'selected' : '';
                      echo "<option value=\"$dep\" $selected>$dep</option>";
                  }
                  ?>
              </select>
              <div class="invalid-feedback">Por favor selecciona un departamento.</div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
              <button type="submit" class="btn btn-primary">Crear Empleado</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
  <?php if (!empty($mensaje)): ?>
      <?php if ($tipo_mensaje === 'success'): ?>
          mostrarExito(<?php echo json_encode($mensaje); ?>);
          document.querySelector('#formularioEmpleado button[type="submit"]').disabled = true;
          setTimeout(() => { window.location.href = 'listar.php'; }, 1500);
      <?php else: ?>
          mostrarError(<?php echo json_encode($mensaje); ?>);
      <?php endif; ?>
  <?php endif; ?>
</script>
