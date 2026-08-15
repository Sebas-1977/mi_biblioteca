# 📚 Mi Biblioteca - Sistema de Gestión (CRUD)

Un sistema web completo para la administración y gestión de **Libros, Autores y Géneros Literarios**, desarrollado aplicando buenas prácticas de programación, arquitectura limpia, patrón MVC y dinamismo en la interfaz.

---

## 🛠️ Tecnologías y Arquitectura

* **PHP 8+**: Programación Orientada a Objetos (**POO**).
* **Patrón MVC**: Separación de responsabilidades (**Modelo - Vista - Controlador**).
* **MySQL / MariaDB**: Base de datos relacional para el almacenamiento persistente.
* **JavaScript (Vanilla JS)**: Manejo del DOM, eventos e interacción dinámica.
* **AJAX / Fetch API**: Búsquedas en tiempo real, cambio de estados y paginación asíncrona sin recargar pantalla.
* **PHPMailer**: Envío transaccional de correos para confirmación de cuenta y recuperación de contraseña.
* **Dotenv (`vlucas/phpdotenv`)**: Gestión segura de variables de entorno.
* **HTML5 & CSS3**: Maquetación modular y diseño responsivo.

---

## ✨ Características Principales

- 🔄 **CRUD Completo**: Creación, lectura, edición y cambio de estado para Libros, Autores y Géneros.
- 🖼️ **Gestión de Portadas**: Subida y procesamiento seguro de imágenes para cada libro.
- ⚡ **Búsqueda Dinámica con Debounce**: Búsqueda en tiempo real optimizada para reducir peticiones al servidor.
- 🗂️ **Filtrado por Estados**: Control de registros activos e inactivos mediante pestañas interactivas.
- 📧 **Autenticación y Correos**: Registro con validación, activación por token y recuperación de password vía SMTP.
- 🔔 **Alertas UX**: Notificaciones emergentes dinámicas para retroalimentación al usuario.

---

## 🚀 Instalación Local

### 1. Clonar el repositorio
```bash
git clone [https://github.com/Sebas-1977/mi_biblioteca.git](https://github.com/Sebas-1977/mi_biblioteca.git)
cd mi_biblioteca

### 2. Instalar dependencias de PHP
```bash
composer install

cp .env.example .env

APP_URL=http://localhost:3000

DB_HOST=localhost
DB_NAME=biblioteca
DB_USER=root
DB_PASS=
DB_PORT=3306

MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=tu_usuario_mailtrap
MAIL_PASS=tu_password_mailtrap
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=cuentas@mibiblioteca.com
MAIL_FROM_NAME="Mi Biblioteca (Local)"

4. Importar la Base de Datos
/database/biblioteca3.sql

mkdir -p public/img/portadas

php -S localhost:3000 -t public
