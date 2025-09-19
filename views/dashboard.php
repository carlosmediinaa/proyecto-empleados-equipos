<?php
require_once __DIR__ . '/../controllers/DashboardController.php';

// Inicializar el controlador
$controller = new DashboardController($pdo);

// Obtener datos
$data = $controller->index();
?>

<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12">
      <h1 class="display-4 text-center text-success mb-3"><?php echo APP_NAME; ?></h1>
      <p class="lead text-center text-muted">Gestiona empleados y equipos de manera eficiente</p>
    </div>
  </section>

  <!-- Statistics Cards -->
  <section class="row mb-5">
    <div class="col-md-3 mb-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="text-primary mb-2">
            <i class="bi bi-people-fill fs-2"></i>
          </div>
          <h3 class="card-title text-primary"><?php echo $data['total_empleados']; ?></h3>
          <p class="card-text">Empleados Registrados</p>
          <a href="<?php echo BASE_URL; ?>views/empleados/listar.php" class="btn btn-outline-primary btn-sm">Ver Empleados</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="text-success mb-2">
            <i class="bi bi-laptop fs-2"></i>
          </div>
          <h3 class="card-title text-success"><?php echo $data['total_equipos']; ?></h3>
          <p class="card-text">Equipos Registrados</p>
          <a href="<?php echo BASE_URL; ?>views/equipos/listar.php" class="btn btn-outline-success btn-sm">Ver Equipos</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="text-warning mb-2">
            <i class="bi bi-link-45deg fs-2"></i>
          </div>
          <h3 class="card-title text-warning"><?php echo $data['equipos_asignados']; ?></h3>
          <p class="card-text">Equipos Asignados</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="text-info mb-2">
            <i class="bi bi-building fs-2"></i>
          </div>
          <h3 class="card-title text-info"><?php echo $data['total_departamentos']; ?></h3>
          <p class="card-text">Departamentos</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Quick Actions -->
  <section class="row">
    <div class="col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-header bg-success text-white">
          <h5 class="card-title mb-0">
            <i class="bi bi-person-plus-fill me-2"></i>Gestión de Empleados
          </h5>
        </div>
        <div class="card-body">
          <p class="card-text">Administra la información de los empleados de la empresa.</p>
          <ul class="list-unstyled">
            <li><i class="bi bi-check-lg text-success me-2"></i>Crear nuevos empleados</li>
            <li><i class="bi bi-check-lg text-success me-2"></i>Listar y buscar empleados</li>
            <li><i class="bi bi-check-lg text-success me-2"></i>Editar información</li>
            <li><i class="bi bi-check-lg text-success me-2"></i>Eliminar empleados</li>
          </ul>
          <div class="d-grid gap-2">
            <a href="<?php echo BASE_URL; ?>views/empleados/crear.php" class="btn btn-success">Crear Empleado</a>
            <a href="<?php echo BASE_URL; ?>views/empleados/listar.php" class="btn btn-outline-success">Ver Lista</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0">
            <i class="bi bi-laptop me-2"></i>Gestión de Equipos
          </h5>
        </div>
        <div class="card-body">
          <p class="card-text">Administra el inventario de equipos y sus asignaciones.</p>
          <ul class="list-unstyled">
            <li><i class="bi bi-check-lg text-success me-2"></i>Registrar nuevos equipos</li>
            <li><i class="bi bi-check-lg text-success me-2"></i>Asignar equipos a empleados</li>
            <li><i class="bi bi-check-lg text-success me-2"></i>Gestionar inventario</li>
            <li><i class="bi bi-check-lg text-success me-2"></i>Control de asignaciones</li>
          </ul>
          <div class="d-grid gap-2">
            <a href="<?php echo BASE_URL; ?>views/equipos/crear.php" class="btn btn-primary">Crear Equipo</a>
            <a href="<?php echo BASE_URL; ?>views/equipos/listar.php" class="btn btn-outline-primary">Ver Lista</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
