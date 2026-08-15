<main class="auth container">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas (Éxito, Error, Info, etc.) -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Mi Biblioteca</h1>
            <p class="auth__descripcion">Crea y administra tu biblioteca virtual</p>
        </header>

        <section class="auth__contenido" aria-labelledby="confirmar-heading">
            <h2 id="confirmar-heading" class="auth__subtitulo">Confirmación de Cuenta</h2>

            <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
                <a href="/login" class="auth__enlace">Inicia Sesión</a>
            </nav>
        </section>

    </div>
</main>