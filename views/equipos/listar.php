<?php
  require_once __DIR__ . '/../../config.php';
  require_once __DIR__ . '/../../db/conexion.php';
  
  // Manejar mensajes de resultado
  $mensaje = '';
  $tipo_mensaje = '';
  if (isset($_GET['mensaje'])) {
      switch ($_GET['mensaje']) {
          case 'eliminado':
              $mensaje = 'Equipo "' . htmlspecialchars($_GET['nombre']) . '" eliminado exitosamente';
              $tipo_mensaje = 'success';
              break;
          case 'error':
              $mensaje = 'Error al eliminar equipo: ' . htmlspecialchars($_GET['error']);
              $tipo_mensaje = 'error';
              break;
      }
  }
  
  // Paginación
  $registros_por_pagina = 5;
  $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
  $offset = ($pagina_actual - 1) * $registros_por_pagina;
  
  // Búsqueda
  $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
  $where = '';
  $params = [];
  if (!empty($busqueda)) {
      $where = "WHERE e.nombre LIKE :busqueda OR e.tipo LIKE :busqueda OR e.numero_serie LIKE :busqueda OR emp.nombre LIKE :busqueda";
      $params[':busqueda'] = "%$busqueda%";
  }
  
  // Contar total de registros
  $sql_count = "SELECT COUNT(*) as total FROM equipos e LEFT JOIN empleados emp ON e.empleado_id = emp.id $where";
  $stmt_count = $pdo->prepare($sql_count);
  foreach ($params as $key => $value) {
      $stmt_count->bindValue($key, $value);
  }
  $stmt_count->execute();
  $total_registros = $stmt_count->fetch()['total'];
  $total_paginas = ceil($total_registros / $registros_por_pagina);
  
  // Obtener equipos
  $sql = "SELECT e.*, emp.nombre AS empleado_nombre, emp.departamento AS empleado_departamento 
          FROM equipos e 
          LEFT JOIN empleados emp ON e.empleado_id = emp.id
          $where
          ORDER BY e.fecha_actualizacion DESC
          LIMIT :offset, :limit";
  $stmt = $pdo->prepare($sql);
  foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
  }
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':limit', $registros_por_pagina, PDO::PARAM_INT);
  $stmt->execute();
  $equipos = $stmt->fetchAll();
  ?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2 text-primary">Gestión de Equipos</h1>
    <a href="crear.php" class="btn btn-primary">
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
  <!-- Tabla -->
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
      <nav aria-label="Paginación" class="mt-3">
        <ul class="pagination justify-content-center">
          <?php if ($pagina_actual > 1): ?>
          <li class="page-item"><a class="page-link" href="?pagina=<?php echo $pagina_actual-1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Anterior</a></li>
          <?php endif; ?>
          <?php for ($i=1; $i<=$total_paginas; $i++): ?>
          <li class="page-item <?php echo $i==$pagina_actual?'active':''; ?>">
            <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>"><?php echo $i; ?></a>
          </li>
          <?php endfor; ?>
          <?php if ($pagina_actual < $total_paginas): ?>
          <li class="page-item"><a class="page-link" href="?pagina=<?php echo $pagina_actual+1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Siguiente</a></li>
          <?php endif; ?>
        </ul>
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
