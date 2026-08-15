<main class="auth container">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas (Éxito, Error, Info, etc.) -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Mi Biblioteca</h1>
            <p class="auth__descripcion">Crea y administra tu biblioteca virtual</p>
        </header>

        <section class="auth__contenido" aria-labelledby="login-heading">
            <h2 id="login-heading" class="auth__subtitulo">Iniciar Sesión</h2>

            <form action="/login" method="POST" class="auth__formulario" novalidate>
                
                <div class="campo">
                    <label for="email">
                        Email <span class="requerido" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Tu Email"
                        autocomplete="email"
                        required
                        aria-required="true"
                    >
                </div>

                <div class="campo">
                    <label for="password">
                        Password <span class="requerido" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Tu Password"
                        autocomplete="current-password"
                        required
                        aria-required="true"
                    >
                </div>

                <input type="submit" class="btn-guardar auth__submit" value="Iniciar Sesión">
            </form>

            <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
                <a href="/crear" class="auth__enlace">¿Aún no tienes una cuenta? Obtén una</a>
                <a href="/olvide" class="auth__enlace">¿Olvidaste tu Password?</a>
            </nav>
        </section>

    </div>
</main>