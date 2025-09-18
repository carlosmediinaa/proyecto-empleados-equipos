<?php require_once __DIR__ . '/../../config.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo APP_NAME; ?></title>

  <!-- Bootstrap CSS -->
  <link href="<?php echo BASE_URL; ?>assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="<?php echo BASE_URL; ?>assets/libs/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 CSS -->
  <link href="<?php echo BASE_URL; ?>assets/libs/sweetalert/css/sweetalert2.min.css" rel="stylesheet">
  <!-- Estilos personalizados -->
  <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
        <img src="https://grupoimpulsora.com/wp-content/uploads/2024/03/Logotipo-Barra-de-navegacion.png" height="32" alt="Logo de Impulsora">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="<?php echo BASE_URL; ?>index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>views/empleados/listar.php">Empleados</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>views/equipos/listar.php">Equipos</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
