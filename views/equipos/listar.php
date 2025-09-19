<?php
require_once __DIR__ . '/../../controllers/EquipoController.php';
require_once __DIR__ . '/../../includes/helpers.php';

$controller = new EquipoController($pdo);

// Obtener datos del listado usando el helper
$data = obtenerDatosListado($controller, 'equipos');
$equipos = $data['items'];
$pagina_actual = $data['pagina_actual'];
$total_paginas = $data['total_paginas'];
$total_registros = $data['total_registros'];
$busqueda = $data['busqueda'];

// Obtener mensajes usando el helper
$mensaje_data = obtenerMensaje($controller);
$mensaje = $mensaje_data['mensaje'];
$tipo_mensaje = $mensaje_data['tipo'];
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <div class="d-block d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h2 text-primary">Gestión de Equipos</h1>
    <a href="crear.php" class="btn btn-primary d-block d-sm-flex">
      <i class="bi bi-plus-lg me-2"></i>Nuevo Equipo
    </a>
  </div>

  <!-- Búsqueda -->
  <form method="GET" class="row mb-4">
    <div class="col-md-6 d-flex">
      <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar por nombre, tipo, serie o empleado..." value="<?php echo htmlspecialchars($busqueda); ?>">
      <button type="submit" class="btn btn-outline-primary">Buscar</button>
      <?php if (!empty($busqueda)): ?>
        <a href="listar.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
      <?php endif; ?>
    </div>
    <div class="col-md-6 text-end">
      <span class="text-muted">
        Mostrando <?php echo count($equipos); ?> de <?php echo $total_registros; ?> equipos
      </span>
    </div>
  </form>

  <!-- Tabla de equipos -->
  <div class="card">
    <div class="card-body">
      <?php if (empty($equipos)): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-laptop display-5 mb-3"></i>
          <h5>No se encontraron equipos</h5>
          <p><?php echo !empty($busqueda) ? 'No hay equipos que coincidan con tu búsqueda.' : 'Aún no hay equipos registrados.'; ?></p>
          <a href="crear.php" class="btn btn-primary">Crear Equipo</a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Número de Serie</th>
                <th>Empleado Asignado</th>
                <th>Departamento</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($equipos as $equipo): ?>
                <tr>
                  <td><?php echo $equipo['id']; ?></td>
                  <td><?php echo htmlspecialchars($equipo['nombre']); ?></td>
                  <td><span class="badge bg-info"><?php echo htmlspecialchars($equipo['tipo']); ?></span></td>
                  <td><code><?php echo htmlspecialchars($equipo['numero_serie']); ?></code></td>
                  <td>
                    <?php if ($equipo['empleado_nombre']): ?>
                      <span class="text-success"><i class="bi bi-person-fill me-1"></i><?php echo htmlspecialchars($equipo['empleado_nombre']); ?></span>
                    <?php else: ?>
                      <span class="text-muted"><i class="bi bi-x-lg me-1"></i>Sin asignar</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($equipo['empleado_departamento']): ?>
                      <span class="badge bg-secondary"><?php echo htmlspecialchars($equipo['empleado_departamento']); ?></span>
                    <?php else: ?>
                      <span class="text-muted"><i class="bi bi-dash-lg me-1"></i></span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo date('d/m/Y', strtotime($equipo['fecha_creacion'])); ?></td>
                  <td>
                    <div class="btn-group">
                      <a href="editar.php?id=<?php echo $equipo['id']; ?>" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Editar">
                        <i class="bi bi-pencil-fill"></i>
                      </a>
                      <button type="button" class="btn btn-danger btn-sm"
                        onclick="confirmarEliminacion('eliminar.php?id=<?php echo $equipo['id']; ?>','<?php echo htmlspecialchars($equipo['nombre']); ?>')"
                        data-bs-toggle="tooltip" title="Eliminar">
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
        <?php if ($total_paginas > 1): ?>
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

<?php if (!empty($mensaje)): ?>
<script>
  <?php if ($tipo_mensaje === 'success'): ?>
      mostrarExito(<?php echo json_encode($mensaje); ?>);
  <?php else: ?>
      mostrarError(<?php echo json_encode($mensaje); ?>);
  <?php endif; ?>
</script>
<?php endif; ?>
