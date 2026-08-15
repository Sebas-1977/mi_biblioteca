<main class="auth container">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas (Éxito, Error, Info, etc.) -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Mi Biblioteca</h1>
            <p class="auth__descripcion">Crea y administra tu biblioteca virtual</p>
        </header>

        <section class="auth__contenido" aria-labelledby="mensaje-heading">
            <h2 id="mensaje-heading" class="auth__subtitulo">Confirmación de Cuenta</h2>
            
            <p class="auth__descripcion" style="margin-bottom: 1.5rem; text-align: center;">
                Hemos enviado las instrucciones para confirmar tu cuenta a tu correo electrónico. Por favor, revisa tu bandeja de entrada o carpeta de spam.
            </p>

            <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
                <a href="/login" class="auth__enlace">¿Ya confirmaste tu cuenta? Inicia Sesión</a>
            </nav>
        </section>

    </div>
</main>