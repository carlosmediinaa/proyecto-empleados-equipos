<?php
require_once __DIR__ . '/../../controllers/EmpleadoController.php';
require_once __DIR__ . '/../../includes/helpers.php';

global $DEPARTAMENTOS;

$controller = new EmpleadoController($pdo);

// Procesar edición usando el helper
$form = procesarEdicion(
    $controller,
    $_GET['id'] ?? 0,
    $_GET,
    $_POST,
    BASE_URL . 'views/empleados/listar.php',
    'actualizar',
    'empleado' // clave correcta del registro
);

$mensaje = $form['resultado']['mensaje'] ?? '';
$tipo_mensaje = $form['resultado']['tipo'] ?? '';
$empleado = $form['registro'] ?? [];
$id = $form['id'] ?? 0;
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-block d-sm-flex justify-content-between align-items-center">
      <h1 class="h2 text-success">Editar Empleado</h1>
      <a href="listar.php" class="btn btn-outline-secondary d-block d-sm-flex">
        <i class="bi bi-arrow-left me-2"></i>Volver
      </a>
    </div>
  </section>

  <section class="row justify-content-center mt-4">
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
                <input type="text" class="form-control" id="nombre" name="nombre"
                  value="<?php echo e($empleado['nombre'] ?? ''); ?>" required>
                <div class="invalid-feedback">Por favor ingresa el nombre completo.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="correo" class="form-label">Correo Electrónico *</label>
                <input type="email" class="form-control" id="correo" name="correo"
                  value="<?php echo e($empleado['correo'] ?? ''); ?>" required>
                <div class="invalid-feedback">Por favor ingresa un correo válido.</div>
              </div>
            </div>
            <div class="mb-3">
              <label for="departamento" class="form-label">Departamento *</label>
              <select class="form-select" id="departamento" name="departamento" required>
                <option value="">Selecciona un departamento</option>
                <?php foreach ($DEPARTAMENTOS as $dep): 
                    $selected = (($empleado['departamento'] ?? '') === $dep) ? 'selected' : '';
                ?>
                  <option value="<?php echo e($dep); ?>" <?php echo $selected; ?>><?php echo e($dep); ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Por favor selecciona un departamento.</div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
              <button type="submit" class="btn btn-success">Actualizar Empleado</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

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
