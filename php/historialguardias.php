<?php
// BLOQUE 1: INCLUIR ARCHIVOS DE AUTENTICACIÓN Y CONEXIÓN
// require_once __DIR__ . '/auth.php': Verifica que el usuario esté logueado
// require_once __DIR__ . '/conexionbd.php': Conecta a la base de datos
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexionbd.php';

// BLOQUE 2: VERIFICAR ROL DE ADMINISTRADOR
// $_SESSION['rol']: Rol del usuario
// !== 'admin': Si no es admin, error y redirige
if (($_SESSION['rol'] ?? '') !== 'admin') {
    $_SESSION['mensaje'] = 'Solo administradores pueden ver el historial.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: /../php/home.php');
    exit();
}

// BLOQUE 3: CALCULAR EL RANGO DEL CURSO ESCOLAR ACTUAL
// new DateTimeImmutable('today'): Obtiene la fecha actual
// $anio y $mes: Año y mes actuales
// if ($mes >= 9): Si es septiembre o después, curso de este año a siguiente
// else: Curso del año anterior a este
// sprintf(...): Formatea las fechas como 'YYYY-MM-DD'
$hoy = new DateTimeImmutable('today');
$anio = (int)$hoy->format('Y');
$mes = (int)$hoy->format('n');

if ($mes >= 9) {
    $inicio_curso = sprintf('%04d-09-01', $anio);
    $fin_curso = sprintf('%04d-08-31', $anio + 1);
} else {
    $inicio_curso = sprintf('%04d-09-01', $anio - 1);
    $fin_curso = sprintf('%04d-08-31', $anio);
}

// BLOQUE 4: CONSULTAR AUSENCIAS DEL CURSO ACTUAL
// $sql: Consulta para obtener ausencias en el rango de fechas
// LEFT JOIN usuarios: para obtener nombre y apellido del usuariio
// prepare($sql): Prepara la consulta
// bind_param('ss', ...): Vincula las fechas (strings)
// execute(): Ejecuta la consulta
// get_result(): Obtiene el resultado
$sql = "SELECT 
        a.dni_usuario, 
        a.id_horario, 
        a.justificante, 
        a.tipo_ausencia, 
        a.tarea_file, 
        a.tarea_text, 
        a.estado, 
        a.dia_a, 
        u.nombre, 
        u.apellido
        FROM ausencia a
        LEFT JOIN usuarios u ON a.dni_usuario = u.dni
        WHERE dia_a BETWEEN ? AND ?
        ORDER BY dia_a DESC, id_a DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ss', $inicio_curso, $fin_curso);
$stmt->execute();
$resultado = $stmt->get_result();
?>