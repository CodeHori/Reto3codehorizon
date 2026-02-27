<?php
// BLOQUE 1: INICIAR SESIÓN SI NO ESTÁ ACTIVA
// session_status(): Verifica el estado de la sesión (PHP_SESSION_NONE, PHP_SESSION_ACTIVE, etc.)
// !== PHP_SESSION_ACTIVE: Si la sesión no está activa, la iniciamos
// session_start(): Inicia o reanuda la sesión para acceder a $_SESSION
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// BLOQUE 2: VERIFICAR SI EL USUARIO ESTÁ LOGUEADO
// $_SESSION['dni']: Variable de sesión que guarda el DNI del usuario logueado
// !isset(...): Si no existe (usuario no logueado), redirigir al login
// header('Location: index.php'): Redirige al formulario de login
// exit(): Detiene la ejecución del script para evitar que se cargue la página protegida
if (!isset($_SESSION['dni'])) {
    header('Location: index.php');
    exit();
}

