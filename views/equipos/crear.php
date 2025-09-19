<?php
require_once __DIR__ . '/../../db/conexion.php';

global $TIPOS_EQUIPOS;

$mensaje = '';
$tipo_mensaje = '';

// Obtener lista de empleados para el select usando SP
$stmt_empleados = $pdo->query("CALL sp_listar_empleados('', 0, 1000)"); // Ajusta el límite si hay muchos empleados
$empleados = $stmt_empleados->fetchAll();
$stmt_empleados->closeCursor(); // Liberar cursor para futuras llamadas

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $tipo = trim($_POST['tipo']);
    $numero_serie = trim($_POST['numero_serie']);
    $empleado_id = !empty($_POST['empleado_id']) ? (int)$_POST['empleado_id'] : null;

    $errores = [];

    if (empty($nombre)) {
        $errores[] = 'El nombre del equipo es requerido';
    }

    if (empty($tipo)) {
        $errores[] = 'El tipo de equipo es requerido';
    }

    if (empty($numero_serie)) {
        $errores[] = 'El número de serie es requerido';
    } else {
        // Verificar número de serie usando SP
        $stmt_check = $pdo->prepare("CALL sp_verificar_numero_serie(?)");
        $stmt_check->execute([$numero_serie]);
        if ($stmt_check->fetch()) {
            $errores[] = 'El número de serie ya está registrado';
        }
        $stmt_check->closeCursor();
    }

    if ($empleado_id && $empleado_id > 0) {
        $stmt_emp = $pdo->prepare("SELECT id FROM empleados WHERE id = ?");
        $stmt_emp->execute([$empleado_id]);
        if (!$stmt_emp->fetch()) {
            $errores[] = 'El empleado seleccionado no existe';
        }
    }

    if (empty($errores)) {
        try {
            // Crear equipo usando SP
            $stmt = $pdo->prepare("CALL sp_crear_equipo(?, ?, ?, ?)");
            $stmt->execute([$nombre, $tipo, $numero_serie, $empleado_id]);
            $stmt->closeCursor();

            $mensaje = 'Equipo creado exitosamente';
            $tipo_mensaje = 'success';

            // Limpiar formulario
            $nombre = $tipo = $numero_serie = '';
            $empleado_id = null;
        } catch (PDOException $e) {
            $mensaje = 'Error al crear el equipo: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipo_mensaje = 'error';
    }
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>


<main class="container mt-4">
  <section class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h1 class="h2 text-primary">Crear Nuevo Equipo</h1>
      <a href="listar.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Volver
      </a>
    </div>
  </section>
  <section class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="bi-laptop me-2"></i>Información del Equipo</h5>
        </div>
        <div class="card-body">
          <form id="formularioEquipo" method="POST" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre del Equipo *</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                  value="<?php echo htmlspecialchars($nombre ?? ''); ?>" 
                  placeholder="Ej: Laptop Dell XPS 13"
                  required>
                <div class="invalid-feedback">Por favor ingresa el nombre del equipo.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="tipo" class="form-label">Tipo de Equipo *</label>
                <select class="form-select" id="tipo" name="tipo" required>
                  <option value="">Selecciona un tipo</option>
                  <?php
                    foreach ($TIPOS_EQUIPOS as $t) {
                        $selected = (isset($tipo) && $tipo === $t) ? 'selected' : '';
                        echo "<option value=\"$t\" $selected>$t</option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">Por favor selecciona un tipo de equipo.</div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="numero_serie" class="form-label">Número de Serie *</label>
                <input type="text" class="form-control" id="numero_serie" name="numero_serie"
                  value="<?php echo htmlspecialchars($numero_serie ?? ''); ?>" 
                  placeholder="Ej: DELL001, HP123456"
                  required>
                <div class="invalid-feedback">Por favor ingresa el número de serie.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="empleado_id" class="form-label">Asignar a Empleado</label>
                <select class="form-select" id="empleado_id" name="empleado_id">
                  <option value="">Sin asignar</option>
                  <?php foreach ($empleados as $empleado): ?>
                  <option value="<?php echo $empleado['id']; ?>"
                    <?php echo (isset($empleado_id) && $empleado_id == $empleado['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($empleado['nombre'] . ' (' . $empleado['departamento'] . ')'); ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Opcional: Puedes asignar el equipo ahora o después.</div>
              </div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
              <button type="submit" class="btn btn-primary">Crear Equipo</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
  <?php if (!empty($mensaje)): ?>
    <?php if ($tipo_mensaje === 'success'): ?>
        mostrarExito(<?php echo json_encode($mensaje); ?>);
        document.querySelector('#formularioEquipo button[type="submit"]').disabled = true;
        setTimeout(() => { window.location.href = 'listar.php'; }, 1500);
    <?php else: ?>
        mostrarError(<?php echo json_encode($mensaje); ?>);
    <?php endif; ?>
  <?php endif; ?>
</script>
