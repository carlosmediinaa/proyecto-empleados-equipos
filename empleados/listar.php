<?php
require_once '../db/conexion.php';

// Manejar mensajes de resultado
$mensaje = '';
$tipo_mensaje = '';

if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'eliminado':
            $mensaje = 'Empleado "' . htmlspecialchars($_GET['nombre']) . '" eliminado exitosamente';
            $tipo_mensaje = 'success';
            break;
        case 'error':
            $mensaje = 'Error al eliminar empleado: ' . htmlspecialchars($_GET['error']);
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
    $where = "WHERE nombre LIKE :busqueda OR correo LIKE :busqueda OR departamento LIKE :busqueda";
    $params[':busqueda'] = "%$busqueda%";
}

// Contar total de registros
$sql_count = "SELECT COUNT(*) as total FROM empleados $where";
$stmt_count = $pdo->prepare($sql_count);
foreach ($params as $key => $value) {
    $stmt_count->bindValue($key, $value);
}
$stmt_count->execute();
$total_registros = $stmt_count->fetch()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Obtener empleados
$sql = "SELECT * FROM empleados $where ORDER BY id DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$empleados = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empleados - Impulsora</title>
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
      <!-- Nav -->
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
            <h1 class="h2 text-success d-block d-sm-inline">Gestión de Empleados</h1>
            <a href="crear.php" class="btn btn-primary d-block d-sm-inline text-sm-center">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Empleado
            </a>
          </div>
        </div>
      </section>
      <!-- Búsqueda -->
      <search class="row mb-4">
        <div class="col-md-6 mb-1">
          <form method="GET" class="d-flex">
            <input type="text" class="form-control me-2 mb-1" name="busqueda" 
              placeholder="Buscar por nombre, correo o departamento..." 
              value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit" class="btn btn-outline-success">Buscar</button>
            <?php if (!empty($busqueda)): ?>
            <a href="listar.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
            <?php endif; ?>
          </form>
        </div>
        <div class="col-md-6 text-md-end">
          <span class="text-muted">
          Mostrando <?php echo count($empleados); ?> de <?php echo $total_registros; ?> empleados
          </span>
        </div>
      </search>
      <!-- Tabla de empleados -->
      <section class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <?php if (empty($empleados)): ?>
              <div class="text-center py-5">
                <i class="bi bi-people-fill fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron empleados</h5>
                <p class="text-muted">
                  <?php if (!empty($busqueda)): ?>
                  No hay empleados que coincidan con tu búsqueda.
                  <?php else: ?>
                  Aún no hay empleados registrados.
                  <?php endif; ?>
                </p>
                <a href="crear.php" class="btn btn-success">Crear Empleado</a>
              </div>
              <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover" id="tablaEmpleados">
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
                      <td><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                      <td><?php echo htmlspecialchars($empleado['correo']); ?></td>
                      <td>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($empleado['departamento']); ?></span>
                      </td>
                      <td><?php echo date('d/m/Y', strtotime($empleado['fecha_creacion'])); ?></td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="editar.php?id=<?php echo $empleado['id']; ?>" 
                            class="btn btn-warning btn-sm" 
                            data-bs-toggle="tooltip" 
                            title="Editar">
                          <i class="bi bi-pencil-fill"></i>
                          </a>
                          <button type="button" 
                            class="btn btn-danger btn-sm" 
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
