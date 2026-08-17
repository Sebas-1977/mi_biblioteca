# 📚 Mi Biblioteca - Sistema de Gestión (CRUD)

Un sistema web completo para la administración y gestión de **Libros, Autores y Géneros Literarios**, desarrollado aplicando buenas prácticas de programación, arquitectura limpia, patrón MVC y dinamismo en la interfaz.

🌐 **Demo en vivo:** [https://mi-biblioteca-l822.onrender.com](https://mi-biblioteca-l822.onrender.com)

---

## 🛠️ Tecnologías y Arquitectura

* **PHP 8+**: Programación Orientada a Objetos (**POO**).
* **Patrón MVC**: Separación de responsabilidades (**Modelo - Vista - Controlador**).
* **MySQL / MariaDB**: Base de datos relacional para el almacenamiento persistente.
* **JavaScript (Vanilla JS)**: Manejo del DOM, eventos e interacción dinámica.
* **AJAX / Fetch API**: Búsquedas en tiempo real, cambio de estados y paginación asíncrona sin recargar pantalla.
* **Cloudinary SDK**: Almacenamiento e integración en la nube para gestión persistente de imágenes en producción.
* **SASS / CSS3**: Hojas de estilo modulares y diseño responsivo.
* **PHPMailer**: Envío transaccional de correos para confirmación de cuenta y recuperación de contraseña.
* **Dotenv (`vlucas/phpdotenv`)**: Gestión segura de variables de entorno.

---

## ✨ Características Principales

- 🔄 **CRUD Completo**: Creación, lectura, edición y cambio de estado para Libros, Autores y Géneros.
- 🖼️ **Gestión Híbrida de Portadas**: Soporte automático para almacenamiento local en desarrollo y Cloudinary en producción.
- ⚡ **Búsqueda Dinámica con Debounce**: Búsqueda en tiempo real optimizada para reducir peticiones al servidor.
- 🗂️ **Filtrado por Estados**: Control de registros activos e inactivos mediante pestañas interactivas.
- 📧 **Autenticación y Correos**: Registro con validación, activación por token y recuperación de contraseña vía SMTP.
- 🔔 **Alertas UX**: Notificaciones emergentes dinámicas para retroalimentación al usuario.

---

## 🚀 Instalación Local

### 1. Clonar el repositorio
```bash
git clone [https://github.com/Sebas-1977/mi_biblioteca.git](https://github.com/Sebas-1977/mi_biblioteca.git)
cd mi_biblioteca
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Configurar variables de entorno
Copiá el archivo de ejemplo y completá tus credenciales:
```bash
cp .env.example .env
```

Configuración base para `.env`:
```ini
APP_URL=http://localhost:3000

DB_HOST=localhost
DB_NAME=biblioteca
DB_USER=root
DB_PASS=
DB_PORT=3306

# Sistema de archivos (local / cloudinary)
FS_DRIVER=local

# Cloudinary (Opcional en local)
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret

MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=tu_usuario_mailtrap
MAIL_PASS=tu_password_mailtrap
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=cuentas@mibiblioteca.com
MAIL_FROM_NAME="Mi Biblioteca (Local)"
```

### 4. Importar la Base de Datos
Importá el script SQL ubicado en la carpeta del proyecto:
```bash
/database/biblioteca3.sql
```

### 5. Crear la carpeta para imágenes locales
```bash
mkdir -p public/img/portadas
```

### 6. Levantar el servidor local
```bash
php -S localhost:3000 -t public
```