<main class="auth container">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas (Éxito, Error, Info, etc.) -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Mi Biblioteca</h1>
            <p class="auth__descripcion">Crea y administra tu biblioteca virtual</p>
        </header>

        <section class="auth__contenido" aria-labelledby="reestablecer-heading">
            <h2 id="reestablecer-heading" class="auth__subtitulo">Ingresa tu Nuevo Password</h2>

            <!-- El action puede incluir el query string o token si procesas la URL así: action="/reestablecer?token=..." -->
            <form method="POST" class="auth__formulario" novalidate>

                <div class="campo">
                    <label for="password">
                        Nuevo Password <span class="requerido" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Tu Nuevo Password (mínimo 6 caracteres)"
                        autocomplete="new-password"
                        required
                        aria-required="true"
                    >
                </div>

                <div class="campo">
                    <label for="password2">
                        Repetir Password <span class="requerido" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password2" 
                        name="password2" 
                        placeholder="Repite tu Nuevo Password"
                        autocomplete="new-password"
                        required
                        aria-required="true"
                    >
                </div>

                <input type="submit" class="btn-guardar auth__submit" value="Guardar Password">
            </form>

            <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
                <a href="/login" class="auth__enlace">¿Recordaste tu clave? Inicia Sesión</a>
                <a href="/crear" class="auth__enlace">¿Aún no tienes una cuenta? Obtén una</a>
            </nav>
        </section>

    </div>
</main>