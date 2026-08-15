<main class="auth container">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas (Éxito, Error, Info, etc.) -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Mi Biblioteca</h1>
            <p class="auth__descripcion">Crea y administra tu biblioteca virtual</p>
        </header>

        <section class="auth__contenido" aria-labelledby="crear-heading">
            <h2 id="crear-heading" class="auth__subtitulo">Crea tu Cuenta en Mi Biblioteca</h2>

            <form action="/crear" method="POST" class="auth__formulario" novalidate>

                <div class="campo">
                    <label for="nombre">
                        Nombre <span class="requerido" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Tu Nombre"
                        autocomplete="given-name"
                        value="<?php echo htmlspecialchars($usuario->nombre ?? ''); ?>"
                        required
                        aria-required="true"
                    >
                </div>

                <div class="campo">
                    <label for="apellido">
                        Apellido <span class="requerido" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="apellido" 
                        name="apellido" 
                        placeholder="Tu Apellido"
                        autocomplete="family-name"
                        value="<?php echo htmlspecialchars($usuario->apellido ?? ''); ?>"
                        required
                        aria-required="true"
                    >
                </div>
                
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
                        value="<?php echo htmlspecialchars($usuario->email ?? ''); ?>"
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
                        placeholder="Tu Password (mínimo 6 caracteres)"
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
                        placeholder="Repite tu Password"
                        autocomplete="new-password"
                        required
                        aria-required="true"
                    >
                </div>

                <input type="submit" class="btn-guardar auth__submit" value="Crear Cuenta">
            </form>

            <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
                <a href="/login" class="auth__enlace">¿Ya tienes una cuenta? Inicia Sesión</a>
                <a href="/olvide" class="auth__enlace">¿Olvidaste tu Password?</a>
            </nav>
        </section>

    </div>
</main>