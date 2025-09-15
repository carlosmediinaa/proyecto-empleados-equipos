-- Borrar la base de datos si ya existe
DROP DATABASE IF EXISTS empleados_equipos;

-- Crear la base de datos
CREATE DATABASE empleados_equipos;
USE empleados_equipos;

-- Tabla de empleados
CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    departamento VARCHAR(50) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de equipos
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    numero_serie VARCHAR(50) NOT NULL UNIQUE,
    empleado_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE SET NULL
);

-- Insertar datos de ejemplo en empleados
INSERT INTO empleados (nombre, correo, departamento) VALUES
('Juan Pérez', 'juan.perez@empresa.com', 'IT'),
('María García', 'maria.garcia@empresa.com', 'Recursos Humanos'),
('Carlos López', 'carlos.lopez@empresa.com', 'Ventas'),
('Ana Martínez', 'ana.martinez@empresa.com', 'Marketing'),
('Luis Ramírez', 'luis.ramirez@empresa.com', 'IT'),
('Fernanda Torres', 'fernanda.torres@empresa.com', 'Finanzas'),
('Roberto Sánchez', 'roberto.sanchez@empresa.com', 'Ventas'),
('Paola Díaz', 'paola.diaz@empresa.com', 'Marketing'),
('Héctor Fernández', 'hector.fernandez@empresa.com', 'IT'),
('Daniela Ortega', 'daniela.ortega@empresa.com', 'Recursos Humanos'),
('Miguel Castro', 'miguel.castro@empresa.com', 'Soporte'),
('Lucía Herrera', 'lucia.herrera@empresa.com', 'Finanzas'),
('Sergio Ríos', 'sergio.rios@empresa.com', 'IT'),
('Patricia Morales', 'patricia.morales@empresa.com', 'Ventas'),
('Adrián Mendoza', 'adrian.mendoza@empresa.com', 'Marketing'),
('Gabriela Cruz', 'gabriela.cruz@empresa.com', 'Recursos Humanos'),
('Ricardo Vázquez', 'ricardo.vazquez@empresa.com', 'Finanzas'),
('Andrea Navarro', 'andrea.navarro@empresa.com', 'IT'),
('Jorge Soto', 'jorge.soto@empresa.com', 'Soporte'),
('Mónica Reyes', 'monica.reyes@empresa.com', 'Marketing'),
('David Peña', 'david.pena@empresa.com', 'Ventas'),
('Claudia Romero', 'claudia.romero@empresa.com', 'IT'),
('Francisco Chávez', 'francisco.chavez@empresa.com', 'Soporte'),
('Alejandra Flores', 'alejandra.flores@empresa.com', 'Recursos Humanos'),
('Guillermo Jiménez', 'guillermo.jimenez@empresa.com', 'Finanzas'),
('Isabel Vargas', 'isabel.vargas@empresa.com', 'Marketing'),
('Tomás Herrera', 'tomas.herrera@empresa.com', 'IT'),
('Verónica Silva', 'veronica.silva@empresa.com', 'Ventas'),
('Hugo Delgado', 'hugo.delgado@empresa.com', 'Soporte'),
('Natalia Méndez', 'natalia.mendez@empresa.com', 'Recursos Humanos');

-- Insertar datos de ejemplo en equipos
INSERT INTO equipos (nombre, tipo, numero_serie, empleado_id) VALUES
('Laptop Dell XPS 13', 'Laptop', 'DELL001', 1),
('Monitor Samsung 24"', 'Monitor', 'SAM001', 1),
('Teclado Mecánico Logitech', 'Periférico', 'KEY001', 2),
('Mouse Inalámbrico Logitech', 'Periférico', 'MOU001', 2),
('Impresora HP LaserJet Pro', 'Impresora', 'HP001', 3),
('Laptop HP EliteBook', 'Laptop', 'HP002', 4),
('Monitor LG UltraWide', 'Monitor', 'LG001', 5),
('Tablet iPad Air', 'Tablet', 'IPD001', 6),
('Smartphone Samsung Galaxy', 'Móvil', 'SMG001', 7),
('Laptop Lenovo ThinkPad', 'Laptop', 'LEN001', 8),
('Auriculares Jabra Evolve', 'Periférico', 'AUR001', 9),
('Proyector Epson', 'Otro', 'EPS001', 10),
('Impresora Epson EcoTank', 'Impresora', 'EPS002', 11),
('Laptop MacBook Pro 14"', 'Laptop', 'MAC001', 12),
('Monitor Dell UltraSharp', 'Monitor', 'DEL002', 13),
('Mouse Gamer Razer', 'Periférico', 'RAZ001', 14),
('Tablet Samsung Galaxy Tab', 'Tablet', 'TAB001', 15),
('Laptop Acer Aspire', 'Laptop', 'ACE001', 16),
('Smartphone iPhone 13', 'Móvil', 'IPH001', 17),
('Router Cisco', 'Otro', 'CIS001', 18),
('Laptop Asus ZenBook', 'Laptop', 'ASU001', 19),
('Impresora Brother HL-L2395DW', 'Impresora', 'BRO001', 20),
('Teclado Microsoft Ergonomic', 'Periférico', 'MIC001', 21),
('Laptop Microsoft Surface', 'Laptop', 'SUR001', 22),
('Monitor BenQ 27"', 'Monitor', 'BEN001', 23),
('Smartphone Motorola G100', 'Móvil', 'MOT001', 24),
('Tablet Huawei MediaPad', 'Tablet', 'HUA001', 25),
('Laptop MSI Prestige', 'Laptop', 'MSI001', 26),
('Impresora Canon Pixma', 'Impresora', 'CAN001', 27),
('Laptop Alienware M15', 'Laptop', 'ALI001', 28);
