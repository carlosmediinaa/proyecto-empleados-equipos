<?php
require_once '../db/conexion.php';

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

// Construir consulta
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

// Obtener equipos con información del empleado asignado
$sql = "SELECT e.*, emp.nombre as empleado_nombre, emp.departamento as empleado_departamento 
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

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Equipos - Impulsora</title>
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
            <h1 class="h2 text-primary d-block d-sm-inline">Gestión de Equipos</h1>
            <a href="crear.php" class="btn btn-primary d-block d-sm-inline text-sm-center">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Equipo
            </a>
          </div>
        </div>
      </section>
      <!-- Búsqueda -->
      <search class="row mb-4">
        <div class="col-md-6">
          <form method="GET" class="d-flex">
            <input type="text" class="form-control me-2 mb-1" name="busqueda" 
              placeholder="Buscar por nombre, tipo, serie o empleado..." 
              value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
            <?php if (!empty($busqueda)): ?>
            <a href="listar.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
            <?php endif; ?>
          </form>
        </div>
        <div class="col-md-6 text-md-end">
          <span class="text-muted">
          Mostrando <?php echo count($equipos); ?> de <?php echo $total_registros; ?> equipos
          </span>
        </div>
      </search>
      <!-- Tabla de equipos -->
      <section class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <?php if (empty($equipos)): ?>
              <div class="text-center py-5">
                <i class="bi bi-laptop display-5 text-muted mb-3"> </i>
                <h5 class="text-muted">No se encontraron equipos</h5>
                <p class="text-muted">
                  <?php if (!empty($busqueda)): ?>
                  No hay equipos que coincidan con tu búsqueda.
                  <?php else: ?>
                  Aún no hay equipos registrados.
                  <?php endif; ?>
                </p>
                <a href="crear.php" class="btn btn-primary">Crear Equipo</a>
              </div>
              <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover" id="tablaEquipos">
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
                      <td>
                        <span class="badge bg-info"><?php echo htmlspecialchars($equipo['tipo']); ?></span>
                      </td>
                      <td>
                        <code><?php echo htmlspecialchars($equipo['numero_serie']); ?></code>
                      </td>
                      <td>
                        <?php if ($equipo['empleado_nombre']): ?>
                        <span class="text-success">
                        <i class="bi bi-person-fill me-1"></i>
                        <?php echo htmlspecialchars($equipo['empleado_nombre']); ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">
                        <i class="bi bi-x-lg me-1"></i>
                        Sin asignar
                        </span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($equipo['empleado_departamento']): ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($equipo['empleado_departamento']); ?></span>
                        <?php else: ?>
                        <span class="text-muted">
                        <i class="bi bi-dash-lg me-1"></i>
                        </span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo date('d/m/Y', strtotime($equipo['fecha_creacion'])); ?></td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="editar.php?id=<?php echo $equipo['id']; ?>" 
                            class="btn btn-warning btn-sm" 
                            data-bs-toggle="tooltip" 
                            title="Editar">
                          <i class="bi bi-pencil-fill"></i>
                          </a>
                          <button type="button" 
                            class="btn btn-danger btn-sm" 
                            onclick="confirmarEliminacion('eliminar.php?id=<?php echo $equipo['id']; ?>', '<?php echo htmlspecialchars($equipo['nombre']); ?>')"
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
                <div class="d-flex justify-content-start justify-content-sm-center overflow-auto">
                  <ul class="pagination d-inline-flex mb-0">
                    <?php if ($pagina_actual > 1): ?>
                      <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Anterior</a>
                      </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                      <li class="page-item <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    <?php if ($pagina_actual < $total_paginas): ?>
                      <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Siguiente</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </nav>
              <?php endif; ?>
              <?php endif; ?>
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
          <?php else: ?>
              mostrarError('<?php echo addslashes($mensaje); ?>');
          <?php endif; ?>
      <?php endif; ?>
    </script>
  </body>
</html>
