<?php
  require_once __DIR__ . '/../../config.php';
  require_once __DIR__ . '/../../db/conexion.php';
  
  global $TIPOS_EQUIPOS;
  
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) {
      header("Location: " . BASE_URL . "views/equipos/listar.php");
      exit;
  }
  
  // Obtener equipo
  $stmt = $pdo->prepare("SELECT * FROM equipos WHERE id = ?");
  $stmt->execute([$id]);
  $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$equipo) {
      header("Location: " . BASE_URL . "views/equipos/listar.php");
      exit;
  }
  
  // Obtener lista de empleados para el select
  $stmt_empleados = $pdo->query("SELECT id, nombre, departamento FROM empleados ORDER BY nombre");
  $empleados = $stmt_empleados->fetchAll();
  
  $mensaje = '';
  $tipo_mensaje = '';
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nombre = trim($_POST['nombre']);
      $tipo = trim($_POST['tipo']);
      $numero_serie = trim($_POST['numero_serie']);
      $empleado_id = !empty($_POST['empleado_id']) ? (int)$_POST['empleado_id'] : null;
  
      $errores = [];
  
      if (empty($nombre)) $errores[] = "El nombre del equipo es requerido.";
      if (empty($tipo)) $errores[] = "El tipo de equipo es requerido.";
      if (empty($numero_serie)) $errores[] = "El número de serie es requerido.";
  
      if (!empty($numero_serie)) {
          $stmt_check = $pdo->prepare("SELECT id FROM equipos WHERE numero_serie = ? AND id != ?");
          $stmt_check->execute([$numero_serie, $id]);
          if ($stmt_check->fetch()) $errores[] = "El número de serie ya está registrado por otro equipo.";
      }
  
      if ($empleado_id && $empleado_id > 0) {
          $stmt_emp = $pdo->prepare("SELECT id FROM empleados WHERE id = ?");
          $stmt_emp->execute([$empleado_id]);
          if (!$stmt_emp->fetch()) $errores[] = "El empleado seleccionado no existe.";
      }
  
      if (empty($errores)) {
          try {
              $stmt = $pdo->prepare("UPDATE equipos SET nombre = ?, tipo = ?, numero_serie = ?, empleado_id = ? WHERE id = ?");
              $stmt->execute([$nombre, $tipo, $numero_serie, $empleado_id, $id]);
  
              $mensaje = "Equipo actualizado correctamente.";
              $tipo_mensaje = "success";
  
              // Actualizar datos locales
              $equipo['nombre'] = $nombre;
              $equipo['tipo'] = $tipo;
              $equipo['numero_serie'] = $numero_serie;
              $equipo['empleado_id'] = $empleado_id;
          } catch (PDOException $e) {
              $mensaje = "Error al actualizar el equipo: " . $e->getMessage();
              $tipo_mensaje = "error";
          }
      } else {
          $mensaje = implode("<br>", $errores);
          $tipo_mensaje = "warning";
      }
  }
  ?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h1 class="h2 text-primary">Editar Equipo</h1>
      <a href="listar.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Volver
      </a>
    </div>
  </section>
  <section class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bi bi-laptop me-2"></i>Información del Equipo
          </h5>
        </div>
        <div class="card-body">
          <form id="formularioEquipo" method="POST" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre del Equipo *</label>
                <input type="text" 
                  class="form-control" 
                  id="nombre" 
                  name="nombre" 
                  value="<?php echo htmlspecialchars($equipo['nombre']); ?>" 
                  placeholder="Ej: Laptop Dell XPS 13"
                  required>
                <div class="invalid-feedback">Por favor ingresa el nombre del equipo.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="tipo" class="form-label">Tipo de Equipo *</label>
                <select class="form-select" id="tipo" name="tipo" required>
                  <option value="">Selecciona un tipo</option>
                  <?php foreach ($TIPOS_EQUIPOS as $t): ?>
                  <option value="<?php echo $t; ?>" <?php echo ($equipo['tipo'] === $t) ? 'selected' : ''; ?>>
                    <?php echo $t; ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Por favor selecciona un tipo de equipo.</div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="numero_serie" class="form-label">Número de Serie *</label>
                <input type="text" 
                  class="form-control" 
                  id="numero_serie" 
                  name="numero_serie" 
                  value="<?php echo htmlspecialchars($equipo['numero_serie']); ?>" 
                  placeholder="Ej: DELL001, HP123456"
                  required>
                <div class="invalid-feedback">Por favor ingresa el número de serie.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="empleado_id" class="form-label">Asignar a Empleado</label>
                <select class="form-select" id="empleado_id" name="empleado_id">
                  <option value="">Sin asignar</option>
                  <?php foreach ($empleados as $empleado): ?>
                  <option value="<?php echo $empleado['id']; ?>" <?php echo ($equipo['empleado_id'] == $empleado['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($empleado['nombre'] . ' (' . $empleado['departamento'] . ')'); ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Opcional: Puedes cambiar la asignación o dejarlo sin asignar.</div>
              </div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
              <button type="submit" class="btn btn-primary">Actualizar Equipo</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
      <?php if (!empty($mensaje)): ?>
          <?php if ($tipo_mensaje === 'success'): ?>
              mostrarExito(<?php echo json_encode($mensaje); ?>);
              const botonActualizar = document.querySelector('#formularioEquipo button[type="submit"]');
              if (botonActualizar) botonActualizar.disabled = true;
              setTimeout(() => { window.location.href = 'listar.php'; }, 1500);
          <?php else: ?>
              mostrarError(<?php echo json_encode($mensaje); ?>);
          <?php endif; ?>
      <?php endif; ?>
  });
</script>
