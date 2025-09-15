<?php
require_once 'db/conexion.php';

// Obtener estadísticas generales
$stmt_empleados = $pdo->query("SELECT COUNT(*) as total FROM empleados");
$total_empleados = $stmt_empleados->fetch()['total'];

$stmt_equipos = $pdo->query("SELECT COUNT(*) as total FROM equipos");
$total_equipos = $stmt_equipos->fetch()['total'];

$stmt_asignados = $pdo->query("SELECT COUNT(*) as total FROM equipos WHERE empleado_id IS NOT NULL");
$equipos_asignados = $stmt_asignados->fetch()['total'];

$stmt_departamentos = $pdo->query("SELECT COUNT(DISTINCT departamento) as total FROM empleados");
$total_departamentos = $stmt_departamentos->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Empleados y Equipos - Impulsora</title>
    <!-- Bootstrap CSS -->
    <link href="./assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="./assets/libs/bootstrap/icons/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link href="./assets/libs/sweetalert/css/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="./assets/css/style.css" rel="stylesheet">
  </head>
  <body>
    <!-- Header -->
    <header>
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
          <a class="navbar-brand" href="index.php">
          <img src="https://grupoimpulsora.com/wp-content/uploads/2024/03/Logotipo-Barra-de-navegacion.png" height="32" alt="Logo de Impulsora">
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                <a class="nav-link active" href="index.php">Inicio</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="empleados/listar.php">Empleados</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="equipos/listar.php">Equipos</a>
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
          <h1 class="display-4 text-center text-success mb-3">Sistema de Gestión</h1>
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
              <h3 class="card-title text-primary"><?php echo $total_empleados; ?></h3>
              <p class="card-text">Empleados Registrados</p>
              <a href="empleados/listar.php" class="btn btn-outline-primary btn-sm">Ver Empleados</a>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card text-center h-100">
            <div class="card-body">
              <div class="text-success mb-2">
                <i class="bi bi-laptop fs-2"></i>
              </div>
              <h3 class="card-title text-success"><?php echo $total_equipos; ?></h3>
              <p class="card-text">Equipos Registrados</p>
              <a href="equipos/listar.php" class="btn btn-outline-success btn-sm">Ver Equipos</a>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card text-center h-100">
            <div class="card-body">
              <div class="text-warning mb-2">
                <i class="bi bi-link-45deg fs-2"></i>
              </div>
              <h3 class="card-title text-warning"><?php echo $equipos_asignados; ?></h3>
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
              <h3 class="card-title text-info"><?php echo $total_departamentos; ?></h3>
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
                <a href="empleados/crear.php" class="btn btn-success">Crear Empleado</a>
                <a href="empleados/listar.php" class="btn btn-outline-success">Ver Lista</a>
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
                <a href="equipos/crear.php" class="btn btn-primary">Crear Equipo</a>
                <a href="equipos/listar.php" class="btn btn-outline-primary">Ver Lista</a>
              </div>
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
    <script src="./assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="./assets/libs/sweetalert/js/sweetalert2.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
  </body>
</html>
