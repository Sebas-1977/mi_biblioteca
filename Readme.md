# 📚 Mi Biblioteca - Sistema de Gestión (CRUD)

Un sistema web completo para la administración y gestión de **Libros, Autores y Géneros Literarios**, desarrollado aplicando buenas prácticas de programación, arquitectura limpia y dinamismo en la interfaz.

---

## 🛠️ Tecnologías y Arquitectura

El proyecto está construido utilizando las siguientes tecnologías y patrones:

* **PHP 8+**: Programación Orientada a Objetos (**POO**).
* **Patrón MVC**: Separación clara de responsabilidades (**Modelo - Vista - Controlador**).
* **MySQL**: Base de datos relacional para el almacenamiento persistente.
* **JavaScript (Vanilla JS)**: Manejo del DOM, eventos y lógica cliente.
* **AJAX / Fetch API**: Búsquedas en tiempo real, filtrado por estados y paginación sin recargar la página.
* **HTML5 & CSS3**: Diseño responsivo, modularización de componentes (`.form-card`, `.campo`, `.alerta`) y maquetación limpia.

---

## ✨ Características Principales

- 🔄 **CRUD Completo**: Creación, lectura, edición y cambio de estado para Libros, Autores y Géneros.
- ⚡ **Búsqueda Dinámica con Debounce**: Búsqueda en tiempo real mediante AJAX con retardo optimizado para no sobrecargar el servidor.
- 🗂️ **Filtrado por Estados**: Control de registros *Activos*, *Inactivos* y *Todos* mediante pestañas interactivas.
- 📄 **Paginación Asíncrona**: Navegación fluida entre páginas de registros sin refresco de pantalla.
- 🖼️ **Carga de Archivos**: Subida y gestión de portadas para los libros.
- 🔔 **Notificaciones UX**: Sistema de alertas emergentes con auto-desvanecimiento temporizado.

---

## 🚀 Instalación Local

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/Sebas-1977/mi_biblioteca.git](https://github.com/Sebas-1977/mi_biblioteca.git)