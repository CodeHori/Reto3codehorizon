<?php
// BLOQUE 1: INCLUIR AUTENTICACIÓN PARA PROTEGER LA PÁGINA
// require_once __DIR__ . '/auth.php': Incluye el archivo que verifica si el usuario está logueado
// Si no lo está, redirige automáticamente al login
require_once __DIR__ . '/auth.php';

// BLOQUE 2: VERIFICAR SI EL USUARIO TIENE ROL DE ADMINISTRADOR
// $_SESSION['rol']: Rol del usuario guardado en la sesión (ej. 'admin', 'usuario')
// ?? '': Si no existe, usa string vacío
// !== 'admin': Si no es admin, guarda mensaje de error y redirige
// $_SESSION['mensaje']: Mensaje de error para mostrar en home.php
// $_SESSION['tipo_mensaje']: Tipo del mensaje ('error')
// header('Location: home.php'): Redirige al panel principal
// exit(): Detiene la ejecución
if (($_SESSION['rol'] ?? '') !== 'admin') {
    $_SESSION['mensaje'] = 'No tienes permiso para acceder al panel de administracion.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de administracion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/interfaz/nav.php'; ?>

    <main>
        <div class="contenedor">
            <div class="encabezado-centrado">
                <h1>Panel de Administracion</h1>
                <p class="texto-sutil ancho-max-800 m-8-auto-0">Accesos rapidos a usuarios, ausencias y calendario.</p>
            </div>
            <br>
            <div class="panel-admin">
                <div class="tarjeta-admin">
                    <h3>Gestionar usuarios</h3>
                    <p>Crear, editar y eliminar usuarios.</p>
                    <div class="acciones">
                        <a href="usuarios.php">Abrir</a>
                    </div>
                </div>

                <div class="tarjeta-admin">
                    <h3>Gestionar ausencias</h3>
                    <p>Revisar, aprobar o rechazar solicitudes.</p>
                    <div class="acciones">
                        <a href="gestionar.php">Abrir</a>
                    </div>
                </div>

                <div class="tarjeta-admin">
                    <h3>Calendario y guardias</h3>
                    <p>Ver ausencias aprobadas y tareas.</p>
                    <div class="acciones">
                        <a href="pruebacalendario.php">Abrir</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/interfaz/footer.php'; ?>
</body>
</html>
