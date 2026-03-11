<?php
// BLOQUE 1: INICIAR LA SESIÓN PARA PODER DESTRUIRLA
// session_start(): Inicia o reanuda la sesión actual
session_start();

// BLOQUE 2: LIMPIAR TODOS LOS DATOS DE LA SESIÓN
// $_SESSION = []: Vacía el array de sesión, eliminando todos los datos guardados
$_SESSION = [];

// BLOQUE 3: ELIMINAR LA COOKIE DE SESIÓN SI EXISTE
// ini_get('session.use_cookies'): Verifica si PHP usa cookies para sesiones
// if (...): Si usa cookies, obtenemos los parámetros y eliminamos la cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    // setcookie(...): Crea una cookie expirada (tiempo pasado) para borrarla
    // session_name(): Nombre de la cookie de sesión
    // time() - 42000: Tiempo en el pasado para expirar la cookie
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// BLOQUE 4: DESTRUIR LA SESIÓN COMPLETAMENTE
// session_destroy(): Destruye la sesión en el servidor
session_destroy();

// BLOQUE 5: REDIRIGIR AL FORMULARIO DE LOGIN
// header('Location: index.php'): Redirige al usuario al login
// exit(): Detiene la ejecución del script
header('Location: /../index.php');
exit();
