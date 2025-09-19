<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db/conexion.php';

// Función helper para escapar texto
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Manejar mensajes de resultado
$mensaje = '';
$tipo_mensaje = '';

if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'eliminado':
            $mensaje = 'Empleado "' . e($_GET['nombre']) . '" eliminado exitosamente';
            $tipo_mensaje = 'success';
            break;
        case 'error':
            $mensaje = 'Error al eliminar empleado: ' . e($_GET['error']);
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

// Contar total de registros con procedimiento
$stmt_count = $pdo->prepare("CALL empleados_equipos.sp_contar_empleados(:busqueda)");
$stmt_count->bindValue(':busqueda', $busqueda);
$stmt_count->execute();
$total_registros = $stmt_count->fetch()['total'];
$stmt_count->closeCursor();

$total_paginas = ceil($total_registros / $registros_por_pagina);

// Obtener empleados con procedimiento
$stmt = $pdo->prepare("CALL empleados_equipos.sp_listar_empleados(:busqueda, :offset, :limit)");
$stmt->bindValue(':busqueda', $busqueda);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$empleados = $stmt->fetchAll();
$stmt->closeCursor();
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h1 class="h2 text-success">Gestión de Empleados</h1>
      <a href="crear.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Nuevo Empleado
      </a>
    </div>
  </section>

  <!-- Búsqueda -->
  <form method="GET" class="row mb-4">
    <div class="col-md-6 d-flex">
      <input type="text" class="form-control me-2" name="busqueda"
        placeholder="Buscar por nombre, correo o departamento..."
        value="<?php echo e($busqueda); ?>">
      <button type="submit" class="btn btn-outline-success">Buscar</button>
      <?php if (!empty($busqueda)): ?>
      <a href="listar.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
      <?php endif; ?>
    </div>
    <div class="col-md-6 text-end">
      <span class="text-muted">
        Mostrando <?php echo count($empleados); ?> de <?php echo $total_registros; ?> empleados
      </span>
    </div>
  </form>

  <!-- Tabla de empleados -->
  <div class="card">
    <div class="card-body">
      <?php if (empty($empleados)): ?>
      <div class="text-center py-5">
        <i class="bi bi-people-fill fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">No se encontraron empleados</h5>
        <p class="text-muted">
          <?php echo !empty($busqueda) ? 'No hay empleados que coincidan con tu búsqueda.' : 'Aún no hay empleados registrados.'; ?>
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
            <?php foreach ($empleados as $empleado): ?>
            <tr>
              <td><?php echo $empleado['id']; ?></td>
              <td><?php echo e($empleado['nombre']); ?></td>
              <td><?php echo e($empleado['correo']); ?></td>
              <td><span class="badge bg-primary"><?php echo e($empleado['departamento']); ?></span></td>
              <td><?php echo date('d/m/Y', strtotime($empleado['fecha_creacion'])); ?></td>
              <td>
                <div class="btn-group">
                  <a href="editar.php?id=<?php echo $empleado['id']; ?>" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Editar">
                    <i class="bi bi-pencil-fill"></i>
                  </a>
                  <button type="button" class="btn btn-danger btn-sm"
                    onclick="confirmarEliminacion('eliminar.php?id=<?php echo $empleado['id']; ?>', '<?php echo e($empleado['nombre']); ?>')"
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
      <?php if ($total_paginas > 1): ?>
      <nav aria-label="Paginación de empleados" class="mt-3">
        <ul class="pagination justify-content-center">
          <?php if ($pagina_actual > 1): ?>
          <li class="page-item"><a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Anterior</a></li>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <li class="page-item <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
            <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>"><?php echo $i; ?></a>
          </li>
          <?php endfor; ?>
          <?php if ($pagina_actual < $total_paginas): ?>
          <li class="page-item"><a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Siguiente</a></li>
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
