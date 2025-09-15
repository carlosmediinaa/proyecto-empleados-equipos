// JavaScript principal para el sistema de gestión de empleados y equipos

// Función para mostrar alertas de confirmación
function confirmarAccion(titulo, texto, tipo = "warning") {
  return Swal.fire({
    title: titulo,
    text: texto,
    icon: tipo,
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#dc3545",
    confirmButtonText: "Sí, continuar",
    cancelButtonText: "Cancelar",
  });
}

// Función para mostrar alertas de éxito
function mostrarExito(mensaje) {
  Swal.fire({
    title: "¡Éxito!",
    text: mensaje,
    icon: "success",
    confirmButtonColor: "#28a745",
  });
}

// Función para mostrar alertas de error
function mostrarError(mensaje) {
  Swal.fire({
    title: "Error",
    text: mensaje,
    icon: "error",
    confirmButtonColor: "#dc3545",
  });
}

// Función para validar formularios
function validarFormulario(formId) {
  const form = document.getElementById(formId);
  const inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );
  let valido = true;

  inputs.forEach((input) => {
    if (!input.value.trim()) {
      input.classList.add("is-invalid");
      valido = false;
    } else {
      input.classList.remove("is-invalid");
    }
  });

  return valido;
}

// Función para validar email
function validarEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Función para confirmar eliminación
function confirmarEliminacion(url, nombre) {
  confirmarAccion(
    "¿Estás seguro?",
    `¿Realmente quieres eliminar a ${nombre}? Esta acción no se puede deshacer.`,
    "warning"
  ).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}

// Función para búsqueda en tiempo real
function buscarEnTabla(inputId, tablaId) {
  const input = document.getElementById(inputId);
  const tabla = document.getElementById(tablaId);
  const filas = tabla.getElementsByTagName("tr");

  input.addEventListener("keyup", function () {
    const filtro = this.value.toLowerCase();

    for (let i = 1; i < filas.length; i++) {
      const fila = filas[i];
      const texto = fila.textContent.toLowerCase();

      if (texto.indexOf(filtro) > -1) {
        fila.style.display = "";
      } else {
        fila.style.display = "none";
      }
    }
  });
}

// Función para inicializar tooltips
function inicializarTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// Función para mostrar loading
function mostrarLoading() {
  Swal.fire({
    title: "Procesando...",
    text: "Por favor espera",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });
}

// Función para cerrar loading
function cerrarLoading() {
  Swal.close();
}

// Inicialización cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
  // Inicializar tooltips
  inicializarTooltips();

  // Agregar animación de entrada a las tarjetas
  const cards = document.querySelectorAll(".card");
  cards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
    card.classList.add("fade-in");
  });

  // Validación de formularios
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validarFormulario(this.id)) {
        e.preventDefault();
        mostrarError("Por favor completa todos los campos requeridos.");
      }
    });
  });

  // Validación de email en tiempo real
  const emailInputs = document.querySelectorAll('input[type="email"]');
  emailInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value && !validarEmail(this.value)) {
        this.classList.add("is-invalid");
        mostrarError("Por favor ingresa un email válido.");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  });
});
