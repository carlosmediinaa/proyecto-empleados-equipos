<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db/conexion.php';

class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function obtenerTotal($procedimiento) {
        $stmt = $this->pdo->query("CALL empleados_equipos.$procedimiento()");
        $total = $stmt->fetch()['total'];
        $stmt->closeCursor();
        return $total;
    }

    public function index() {
        return [
            'total_empleados'     => $this->obtenerTotal('sp_total_empleados'),
            'total_equipos'       => $this->obtenerTotal('sp_total_equipos'),
            'equipos_asignados'   => $this->obtenerTotal('sp_equipos_asignados'),
            'total_departamentos' => $this->obtenerTotal('sp_total_departamentos'),
        ];
    }
}
