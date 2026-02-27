<?php
// BLOQUE 1: INCLUIR AUTENTICACIÓN PARA PROTEGER LA PÁGINA
// require_once __DIR__ . '/auth.php': Incluye el archivo que verifica si el usuario está logueado
// Si no lo está, redirige automáticamente al login
require_once __DIR__ . '/auth.php';

// BLOQUE 2: OBTENER MENSAJES DE LA SESIÓN PARA MOSTRAR EN LA PÁGINA
// $_SESSION['mensaje_home']: Mensaje de bienvenida guardado en el login
// ?? '': Si no existe, usa string vacío
// unset(...): Elimina la variable de la sesión después de usarla
$mensaje_home = $_SESSION['mensaje_home'] ?? '';
unset($_SESSION['mensaje_home']);

// BLOQUE 3: OBTENER MENSAJES GENERALES (ÉXITO O ERROR) DE LA SESIÓN
// $_SESSION['mensaje']: Mensaje general (ej. de otras páginas)
// $_SESSION['tipo_mensaje']: Tipo del mensaje ('exito', 'error', 'info')
// unset(...): Elimina ambas variables después de obtenerlas
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// BLOQUE 4: DETERMINAR LA CLASE CSS PARA EL MENSAJE
// === 'exito': Si el tipo es 'exito', usa clase de éxito; sino, de error
// $clase: Variable que se usará en el HTML para aplicar estilos
$clase = $tipo_mensaje === 'exito' ? 'mensaje mensaje-exito' : 'mensaje mensaje-error';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - CPIFP Bajo Aragon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/interfaz/nav.php'; ?>

    <main>
        <div class="contenedor">
            <div class="tarjeta ancho-max-1000 texto-izquierda">
                <?php if ($mensaje_home !== ''): ?>
                    <div class="mensaje mensaje-exito"><?php echo htmlspecialchars($mensaje_home); ?></div>
                <?php endif; ?>

                <?php if ($mensaje !== ''): ?>
                    <div class="<?php echo $clase; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
                <?php endif; ?>

                <h1 class="titulo-tarjeta">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></h1>
                <h2 class="h2bienvenida">Panel de gestion: Ausencias y Guardias</h2>
                <p class="texto-webdescrip1">Aplicacion para gestionar ausencias y guardias del CPIFP.</p>
                <p class="texto-webdescrip2">Accede a calendario, solicitudes y panel de administracion segun tu rol.<p> 
                <p br>https://prod.liveshare.vsengsaas.visualstudio.com/join?CB54294030FCF6CA1C6A283CE7D71008D3BF</p>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/interfaz/footer.php'; ?>
</body>
</html>
