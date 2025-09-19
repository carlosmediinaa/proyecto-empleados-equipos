<?php
require_once __DIR__ . '/../../controllers/EquipoController.php';
require_once __DIR__ . '/../../includes/helpers.php';

global $TIPOS_EQUIPOS;

$controller = new EquipoController($pdo);

// Obtener lista de empleados para el select
$empleados = $controller->listarEmpleados();

// Procesar formulario usando helper
$formData = procesarFormulario($controller, $_POST ?? [], 'crear');

$mensaje = $formData['resultado']['mensaje'] ?? '';
$tipo_mensaje = $formData['resultado']['tipo'] ?? '';

$nombre = $formData['valores']['nombre'] ?? '';
$tipo = $formData['valores']['tipo'] ?? '';
$numero_serie = $formData['valores']['numero_serie'] ?? '';
$empleado_id = $formData['valores']['empleado_id'] ?? null;
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-block d-sm-flex justify-content-between align-items-center">
      <h1 class="h2 text-primary">Crear Nuevo Equipo</h1>
      <a href="listar.php" class="btn btn-outline-secondary d-block d-sm-flex">
        <i class="bi bi-arrow-left me-2"></i>Volver
      </a>
    </div>
  </section>

  <section class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="bi-laptop me-2"></i>Información del Equipo</h5>
        </div>
        <div class="card-body">
          <form id="formularioEquipo" method="POST" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre del Equipo *</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                  value="<?php echo htmlspecialchars($nombre); ?>" 
                  placeholder="Ej: Laptop Dell XPS 13" required>
                <div class="invalid-feedback">Por favor ingresa el nombre del equipo.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="tipo" class="form-label">Tipo de Equipo *</label>
                <select class="form-select" id="tipo" name="tipo" required>
                  <option value="">Selecciona un tipo</option>
                  <?php foreach ($TIPOS_EQUIPOS as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo ($tipo === $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Por favor selecciona un tipo de equipo.</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="numero_serie" class="form-label">Número de Serie *</label>
                <input type="text" class="form-control" id="numero_serie" name="numero_serie"
                  value="<?php echo htmlspecialchars($numero_serie); ?>" 
                  placeholder="Ej: DELL001, HP123456" required>
                <div class="invalid-feedback">Por favor ingresa el número de serie.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="empleado_id" class="form-label">Asignar a Empleado</label>
                <select class="form-select" id="empleado_id" name="empleado_id">
                  <option value="">Sin asignar</option>
                  <?php foreach ($empleados as $empleado): ?>
                    <option value="<?php echo $empleado['id']; ?>" <?php echo ($empleado_id == $empleado['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($empleado['nombre'] . ' (' . $empleado['departamento'] . ')'); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Opcional: Puedes asignar el equipo ahora o después.</div>
              </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
              <button type="submit" class="btn btn-primary">Crear Equipo</button>
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
        document.querySelector('#formularioEquipo button[type="submit"]').disabled = true;
        setTimeout(() => { window.location.href = 'listar.php'; }, 1500);
    <?php else: ?>
        mostrarError(<?php echo json_encode($mensaje); ?>);
    <?php endif; ?>
  <?php endif; ?>
</script>
