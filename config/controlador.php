<?php
// BLOQUE 1: INICIAR SESIÓN Y CONECTAR A LA BASE DE DATOS
// session_start(): Inicia la sesión para guardar datos del usuario logueado
// require_once __DIR__ . '/conexionbd.php': Incluye el archivo de conexión a la BD
session_start();
require '../config/conexionbd.php';

// BLOQUE 2: VERIFICAR QUE EL FORMULARIO SE ENVÍO POR POST
// $_SERVER['REQUEST_METHOD']: Método HTTP usado (GET, POST, etc.)
// !== 'POST': Si no es POST, redirige al login (evita accesos directos)
// header('Location: index.php'): Redirige al formulario de login
// exit(): Detiene la ejecución
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// BLOQUE 3: OBTENER Y VALIDAR LOS DATOS DEL FORMULARIO
// trim(...): Elimina espacios en blanco al inicio y fin
// $_POST['uname']: Campo de correo del formulario
// $_POST['psw']: Campo de contraseña
// ?? '': Si no existe, usa string vacío
$correo = trim($_POST['uname'] ?? '');
$clave = $_POST['psw'] ?? '';

// BLOQUE 4: COMPROBAR SI LOS CAMPOS ESTÁN VACÍOS
// === '': Verifica si son strings vacíos
// || : Operador OR (si alguno está vacío)
// $_SESSION['error']: Guarda el mensaje de error en la sesión
// header('Location: index.php'): Redirige de vuelta al login para mostrar el error
if ($correo === '' || $clave === '') {
    $_SESSION['error'] = 'LOS CAMPOS ESTAN VACIOS';
    header('Location: index.php');
    exit();
}

// BLOQUE 5: PREPARAR LA CONSULTA PARA BUSCAR EL USUARIO
// $sql: Consulta SQL con placeholder (?) para evitar inyección SQL
// prepare($sql): Prepara la consulta en el objeto de conexión
$sql = "SELECT * FROM usuarios WHERE correo_electronico = ?";
$stmt = $conexion->prepare($sql);

// BLOQUE 6: VERIFICAR SI LA PREPARACIÓN FALLÓ
// !$stmt: Si prepare() falló, $stmt es false
// $_SESSION['error']: Guarda mensaje de error interno
if (!$stmt) {
    $_SESSION['error'] = 'Error interno al iniciar sesion';
    header('Location: index.php');
    exit();
}

// BLOQUE 7: VINCULAR EL PARÁMETRO Y EJECUTAR LA CONSULTA
// bind_param('s', $correo): Vincula el correo (string) al placeholder
// execute(): Ejecuta la consulta preparada
// get_result(): Obtiene el resultado de la consulta
$stmt->bind_param('s', $correo);
$stmt->execute();
$resultado = $stmt->get_result();

// BLOQUE 8: VERIFICAR SI SE ENCONTRÓ EXACTAMENTE UN USUARIO
// num_rows: Número de filas devueltas
// !== 1: Si no es exactamente 1, el usuario no existe o hay duplicados
// $_SESSION['error']: Mensaje de usuario no encontrado
if ($resultado->num_rows !== 1) {
    $_SESSION['error'] = 'USUARIO NO ENCONTRADO';
    header('Location: index.php');
    exit();
}

// BLOQUE 9: OBTENER LOS DATOS DEL USUARIO
// fetch_assoc(): Convierte la fila en un array asociativo
// $hash_contrasena: Obtiene el hash de la contraseña (maneja nombres de columna variables)
$usuario = $resultado->fetch_assoc();
$hash_contrasena = $usuario['contraseÃ±a'] ?? ($usuario['contraseña'] ?? '');

// BLOQUE 10: VERIFICAR LA CONTRASEÑA
// password_verify($clave, $hash_contrasena): Compara la clave con el hash
// || : Si el hash está vacío o no coincide, error
// $_SESSION['error']: Mensaje de contraseña incorrecta
if ($hash_contrasena === '' || !password_verify($clave, $hash_contrasena)) {
    $_SESSION['error'] = 'CONTRASENA INCORRECTA';
    header('Location: index.php');
    exit();
}

// NOTE: antiguas comprobaciones individuales eliminadas si no son necesarias.

// BLOQUE 11: GUARDAR DATOS DEL USUARIO EN LA SESIÓN
// $_SESSION[...]: Almacena datos para usar en otras páginas
// Incluye DNI, correo, nombre, apellido, rol y mensaje de bienvenida
$_SESSION['dni'] = $usuario['dni'];
$_SESSION['correo_electronico'] = $usuario['correo_electronico'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['apellido'] = $usuario['apellido'];
$_SESSION['rol'] = $usuario['rol'];
$_SESSION['mensaje_home'] = 'Bienvenido ' . $usuario['nombre'] . ' ' . $usuario['apellido'] . ' (' . $usuario['rol'] . ')';

// BLOQUE 12: REDIRIGIR AL PANEL PRINCIPAL
// header('Location: home.php'): Redirige a la página de inicio después del login exitoso
// exit(): Detiene la ejecución
header('Location: /php/home.php');
exit();
