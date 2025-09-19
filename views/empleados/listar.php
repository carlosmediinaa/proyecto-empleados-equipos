<?php
require_once __DIR__ . '/../../controllers/EmpleadoController.php';
require_once __DIR__ . '/../../includes/helpers.php';

$controller = new EmpleadoController($pdo);

$pagina   = obtenerPaginaActual();
$busqueda = obtenerBusqueda();

$data = $controller->listar($pagina, $busqueda);
$msg  = $controller->manejarMensajes();
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-block d-sm-flex justify-content-between align-items-center">
      <h1 class="h2 text-success">Gestión de Empleados</h1>
      <a href="crear.php" class="btn btn-primary d-block d-sm-flex">
        <i class="bi bi-plus-lg me-2"></i>Nuevo Empleado
      </a>
    </div>
  </section>

  <!-- Búsqueda -->
  <form method="GET" class="row mb-4">
    <div class="col-md-6 d-flex">
      <input type="text" class="form-control me-2" name="busqueda"
        placeholder="Buscar por nombre, correo o departamento..."
        value="<?php echo htmlspecialchars($data['busqueda']); ?>">
      <button type="submit" class="btn btn-outline-success">Buscar</button>
      <?php if (!empty($data['busqueda'])): ?>
        <a href="listar.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
      <?php endif; ?>
    </div>
    <div class="col-md-6 text-end">
      <span class="text-muted">
        Mostrando <?php echo count($data['empleados']); ?> de <?php echo $data['total_registros']; ?> empleados
      </span>
    </div>
  </form>

  <!-- Tabla de empleados -->
  <div class="card">
    <div class="card-body">
      <?php if (empty($data['empleados'])): ?>
        <div class="text-center py-5">
          <i class="bi bi-people-fill fs-1 text-muted mb-3"></i>
          <h5 class="text-muted">No se encontraron empleados</h5>
          <p class="text-muted">
            <?php echo !empty($data['busqueda']) ? 'No hay empleados que coincidan con tu búsqueda.' : 'Aún no hay empleados registrados.'; ?>
          </p>
          <a href="crear.php" class="btn btn-success">Crear Empleado</a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Departamento</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data['empleados'] as $empleado): ?>
              <tr>
                <td><?php echo $empleado['id']; ?></td>
                <td><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                <td><?php echo htmlspecialchars($empleado['correo']); ?></td>
                <td><span class="badge bg-primary"><?php echo htmlspecialchars($empleado['departamento']); ?></span></td>
                <td><?php echo date('d/m/Y', strtotime($empleado['fecha_creacion'])); ?></td>
                <td>
                  <div class="btn-group">
                    <a href="editar.php?id=<?php echo $empleado['id']; ?>" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Editar">
                      <i class="bi bi-pencil-fill"></i>
                    </a>
                    <button type="button" class="btn btn-danger btn-sm"
                      onclick="confirmarEliminacion('eliminar.php?id=<?php echo $empleado['id']; ?>', '<?php echo htmlspecialchars($empleado['nombre']); ?>')"
                      data-bs-toggle="tooltip"
                      title="Eliminar">
                      <i class="bi bi-trash3-fill"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <?php if ($data['total_paginas'] > 1): ?>
        <nav aria-label="Paginación de empleados" class="mt-3">
          <div class="d-flex justify-content-start justify-content-sm-center" style="overflow-x:auto; padding: 0 10px;">
            <ul class="pagination flex-nowrap mb-0">
              <?php if ($data['pagina_actual'] > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?pagina=<?php echo $data['pagina_actual'] - 1; ?>&busqueda=<?php echo urlencode($data['busqueda']); ?>">Anterior</a>
                </li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $data['total_paginas']; $i++): ?>
                <li class="page-item <?php echo $i == $data['pagina_actual'] ? 'active' : ''; ?>">
                  <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($data['busqueda']); ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($data['pagina_actual'] < $data['total_paginas']): ?>
                <li class="page-item">
                  <a class="page-link" href="?pagina=<?php echo $data['pagina_actual'] + 1; ?>&busqueda=<?php echo urlencode($data['busqueda']); ?>">Siguiente</a>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php if (!empty($msg['mensaje'])): ?>
<script>
  <?php if ($msg['tipo'] === 'success'): ?>
      mostrarExito(<?php echo json_encode($msg['mensaje']); ?>);
  <?php else: ?>
      mostrarError(<?php echo json_encode($msg['mensaje']); ?>);
  <?php endif; ?>
</script>
<?php endif; ?>
