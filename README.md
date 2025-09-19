# Sistema de Gestión de Empleados y Equipos

## 📋 Descripción del Proyecto

Sistema web completo tipo CRUD para la gestión de empleados y equipos de la empresa Impulsora. Desarrollado utilizando únicamente PHP (sin frameworks), JavaScript, Bootstrap para el diseño visual y SweetAlert2 para las alertas.

## 🎯 Características Principales

### Módulo de Empleados

- ✅ **Crear empleado**: Registro con nombre, correo y departamento
- ✅ **Listar empleados**: Tabla con búsqueda y paginación
- ✅ **Editar empleado**: Modificación de información existente
- ✅ **Eliminar empleado**: Eliminación con confirmación SweetAlert2

### Módulo de Equipos

- ✅ **Crear equipo**: Registro con nombre, tipo y número de serie
- ✅ **Asignar equipo**: Asignación a empleados mediante formulario con select
- ✅ **Listar equipos**: Vista con información del empleado asignado
- ✅ **Editar y eliminar equipos**: Gestión completa del inventario

### Validaciones

- ✅ **Validación del servidor**: Verificación de campos vacíos y formatos
- ✅ **Validación del cliente**: Validación de campos obligatorios y formato de email
- ✅ **Confirmaciones**: SweetAlert2 para todas las acciones críticas

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+ (sin frameworks)
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Base de Datos**: MySQL 5.7+
- **Diseño**: Bootstrap 5.3.8
- **Alertas**: SweetAlert2 11.23.0
- **Iconos**: Bootstrap Icons 1.13.1

## 📁 Estructura del Proyecto

```
proyecto-empleados-equipos/
├── index.php                      # Página principal con estadísticas
├── views/                         # Carpeta para vistas
│   ├── empleados/                 # Módulo de empleados
│   │   ├── listar.php             # Lista con búsqueda y paginación
│   │   ├── crear.php              # Formulario de creación usando procesarFormulario()
│   │   ├── editar.php             # Formulario de edición usando procesarEdicion()
│   │   └── eliminar.php           # Proceso de eliminación
│   ├── equipos/                   # Módulo de equipos
│   │   ├── listar.php             # Lista con asignaciones, búsqueda y scrollable pagination
│   │   ├── crear.php              # Formulario de creación usando procesarFormulario()
│   │   ├── editar.php             # Formulario de edición usando procesarEdicion()
│   │   └── eliminar.php           # Proceso de eliminación
│   └── layouts/                   # Layouts compartidos
│       ├── header.php             # Cabecera común
│       └── footer.php             # Pie de página común
├── controllers/                   # Controladores
│   ├── DashboardController.php
│   ├── EmpleadoController.php
│   └── EquipoController.php
├── db/                            # Base de datos
│   ├── conexion.php               # Configuración de conexión PDO
│   ├── init.sql                   # Script de inicialización con tablas y procedimientos
│   └── procedimientos.sql         # Script con los procedimientos almacenados para empleados y equipos
├── includes/                      # Helpers y funciones compartidas
│   └── helpers.php                # Helpers para formularios, listados y edición
└── assets/                        # Recursos estáticos
    ├── css/
    │   └── style.css              # Estilos personalizados
    ├── js/
    │   └── main.js                # JS principal (SweetAlert, validaciones)
    └── libs/
        ├── bootstrap/
        │   ├── css/
        │   │   └── bootstrap.min.css
        │   ├── js/
        │   │   └── bootstrap.bundle.min.js
        │   └── icons/
        │       └── bootstrap-icons.css
        └── sweetalert/
            ├── css/
            │   └── sweetalert2.min.css
            └── js/
                └── sweetalert2.min.js
```

## 🚀 Instalación y Configuración

### Instalación

#### Requisitos Previos

- Servidor web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno

#### Pasos de Instalación

1. **Clonar o descargar el proyecto**

   ```bash
   git clone https://github.com/carlosmediinaa/proyecto-empleados-equipos.git
   cd proyecto-empleados-equipos
   ```

2. **Configurar la base de datos**

   - Crear una base de datos MySQL.
   - Importar el archivo `db/init.sql` para crear tablas y datos iniciales.
   - Importar el archivo `db/procedimientos.sql` para crear los procedimientos almacenados.
   - Configurar las credenciales en `db/conexion.php`.

3. **Configurar el servidor web**

   - Colocar el proyecto en el directorio del servidor web
   - Asegurar que PHP tenga permisos de lectura/escritura

4. **Acceder al sistema**
   - Abrir el navegador y navegar a la URL del proyecto
   - El sistema estará listo para usar

### Configuración de Base de Datos

Editar el archivo `db/conexion.php` con tus credenciales:

```php
$host = 'localhost';        // Servidor de base de datos
$dbname = 'empleados_equipos'; // Nombre de la base de datos
$username = 'root';         // Usuario de MySQL
$password = 'root';         // Contraseña de MySQL
```

## 📊 Base de Datos

### Tabla: empleados

| Campo               | Tipo               | Descripción                  |
| ------------------- | ------------------ | ---------------------------- |
| id                  | INT AUTO_INCREMENT | Clave primaria               |
| nombre              | VARCHAR(100)       | Nombre completo del empleado |
| correo              | VARCHAR(100)       | Correo electrónico (único)   |
| departamento        | VARCHAR(50)        | Departamento de trabajo      |
| fecha_creacion      | TIMESTAMP          | Fecha de registro            |
| fecha_actualizacion | TIMESTAMP          | Última modificación          |

### Tabla: equipos

| Campo               | Tipo               | Descripción                   |
| ------------------- | ------------------ | ----------------------------- |
| id                  | INT AUTO_INCREMENT | Clave primaria                |
| nombre              | VARCHAR(100)       | Nombre del equipo             |
| tipo                | VARCHAR(50)        | Tipo de equipo                |
| numero_serie        | VARCHAR(50)        | Número de serie (único)       |
| empleado_id         | INT                | ID del empleado asignado (FK) |
| fecha_creacion      | TIMESTAMP          | Fecha de registro             |
| fecha_actualizacion | TIMESTAMP          | Última modificación           |

## 🎨 Características de Diseño

- **Responsive**: Adaptable a dispositivos móviles y tablets
- **Moderno**: Interfaz limpia y profesional
- **Intuitivo**: Navegación fácil y clara
- **Accesible**: Cumple estándares de accesibilidad web

## 🔧 Funcionalidades Técnicas

### Validaciones Implementadas

- **Servidor**: Validación de campos requeridos, formatos de email, unicidad
- **Cliente**: Validación en tiempo real, feedback visual
- **Base de datos**: Restricciones de integridad referencial

### Características de Seguridad

- **Prepared Statements**: Prevención de inyección SQL
- **Sanitización**: Escape de caracteres especiales
- **Validación**: Verificación de datos en múltiples capas

### Experiencia de Usuario

- **Alertas**: SweetAlert2 para confirmaciones y notificaciones
- **Búsqueda**: Filtrado en tiempo real
- **Paginación**: Navegación eficiente de grandes listas
- **Feedback**: Mensajes claros de éxito y error

## 📱 Responsive Design

El sistema está optimizado para:

- **Desktop**: Experiencia completa con todas las funcionalidades
- **Tablet**: Interfaz adaptada para pantallas medianas
- **Mobile**: Navegación optimizada para dispositivos móviles

## 🚀 Uso del Sistema

### Gestión de Empleados

1. **Crear**: Acceder a "Empleados" → "Nuevo Empleado"
2. **Listar**: Ver todos los empleados con opciones de búsqueda
3. **Editar**: Hacer clic en el botón de edición en la lista
4. **Eliminar**: Confirmar eliminación con SweetAlert2

### Gestión de Equipos

1. **Crear**: Acceder a "Equipos" → "Nuevo Equipo"
2. **Asignar**: Seleccionar empleado durante la creación o edición
3. **Listar**: Ver equipos con información de asignación
4. **Gestionar**: Editar o eliminar equipos según necesidad

## 🐛 Solución de Problemas

### Error de Conexión a Base de Datos

- Verificar credenciales en `db/conexion.php`
- Asegurar que MySQL esté ejecutándose
- Confirmar que la base de datos existe

### Problemas de Permisos

- Verificar permisos de lectura/escritura en el directorio
- Asegurar que PHP tenga acceso a la base de datos

### Errores de JavaScript

- Verificar que las librerías CDN estén cargando correctamente
- Revisar la consola del navegador para errores

## 📝 Notas de Desarrollo

- **Sin Frameworks**: Desarrollado completamente en PHP vanilla
- **Bootstrap**: Solo para diseño, sin dependencias de JavaScript
- **SweetAlert2**: Solo para alertas, sin jQuery
- **Responsive**: Mobile-first approach
- **Accesible**: Cumple estándares WCAG 2.1

## 👥 Soporte

Para soporte técnico o consultas sobre el proyecto:

- Revisar la documentación
- Verificar la configuración del servidor
- Consultar los logs de error de PHP y MySQL

## 📄 Licencia

Este proyecto fue desarrollado como parte de una evaluación técnica para Impulsora.
