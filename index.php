<?php
require '../config/controlador.php';
// BLOQUE 1: INICIAR SESIÓN PARA ACCEDER A VARIABLES DE SESIÓN
// session_start(): Inicia la sesión para leer/escribir en $_SESSION
session_start();

// BLOQUE 2: VERIFICAR SI EL USUARIO YA ESTÁ LOGUEADO
// isset($_SESSION['dni']): Comprueba si existe el DNI en la sesión (usuario logueado)
// if (...): Si está logueado, redirige al panel principal para evitar mostrar el login
// header('Location: home.php'): Redirige a la página de inicio
// exit(): Detiene la ejecución del script
if (isset($_SESSION['dni'])) {
    header('Location: /../php/home.php');
    exit();
}

// BLOQUE 3: OBTENER MENSAJE DE ERROR DE LA SESIÓN
// $_SESSION['error']: Mensaje de error guardado en controlador.php (ej. campos vacíos)
// ?? '': Si no existe, usa string vacío
// unset($_SESSION['error']): Elimina el mensaje de la sesión después de obtenerlo
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesion</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main id="mainses" class="mains">
        <div class="contenedor">
            <div class="encabezado-centrado">
                <h1 id="h1ini">CPIFP Bajo Aragon</h1>
                <h2 id="h1form">Inicio de sesion</h2>
            </div>

            <?php if ($error !== ''): ?>
                <div class="mensaje mensaje-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <section>
                <form action="/config/controlador.php" method="post" id="iniform" class="tarjeta tarjeta-formulario">
                    <label for="uname">Correo</label>
                    <input type="text" name="uname" id="uname" placeholder="Correo" required class="control-formulario">

                    <label for="psw">Contraseña</label>
                    <input type="password" name="psw" id="psw" placeholder="Contraseña" required class="control-formulario">

                    <div class="acciones-formulario">
                        <button type="submit" class="boton boton-primario">Acceder</button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <?php require '../interfaz/footer.php'; ?>
</body>
</html>
