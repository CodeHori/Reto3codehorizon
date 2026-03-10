<?php
// BLOQUE 1: INCLUIR ARCHIVOS DE AUTENTICACIÓN Y CONEXIÓN A BD
// require_once __DIR__ . '/auth.php': Incluye autenticación para verificar login
// require_once __DIR__ . '/conexionbd.php': Incluye conexión a la base de datos
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexionbd.php';

// BLOQUE 2: VERIFICAR QUE EL USUARIO SEA ADMINISTRADOR
// $_SESSION['rol']: Rol del usuario ('admin' o 'usuario')
// ?? '': Si no existe, usa string vacío
// !== 'admin': Si no es admin, guarda mensaje de error y redirige
// $_SESSION['mensaje']: Mensaje para mostrar en home.php
// $_SESSION['tipo_mensaje']: Tipo del mensaje ('error')
// header('Location: home.php'): Redirige al panel principal
// exit(): Detiene la ejecución
if (($_SESSION['rol'] ?? '') !== 'admin') {
    $_SESSION['mensaje'] = 'No tienes permisos para gestionar ausencias.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: home.php');
    exit();
}

// BLOQUE 3: FUNCIÓN PARA MOSTRAR MENSAJES Y REDIRIGIR
// function mensaje_y_redirigir(string $mensaje, string $tipo = 'info'): Define la función
// $_SESSION['mensaje']: Guarda el mensaje en la sesión
// $_SESSION['tipo_mensaje']: Guarda el tipo del mensaje
// header('Location: gestionar.php'): Redirige de vuelta a esta página
// exit(): Detiene la ejecución
function mensaje_y_redirigir(string $mensaje, string $tipo = 'info'): void
{
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
    header('Location: gestionar.php');
    exit();
}

// BLOQUE 4: FUNCIÓN PARA BORRAR ARCHIVOS SI EXISTEN
// function borrar_archivo_si_existe(?string $ruta): Define la función con parámetro opcional
// if ($ruta === null || $ruta === ''): Si la ruta es nula o vacía, no hace nada
// trim($ruta): Elimina espacios en blanco
// file_exists($ruta_limpia): Verifica si el archivo existe
// @unlink($ruta_limpia): Borra el archivo (el @ suprime errores)
function borrar_archivo_si_existe(?string $ruta): void
{
    if ($ruta === null || $ruta === '') {
        return;
    }

    $ruta_limpia = trim($ruta);
    if ($ruta_limpia !== '' && file_exists($ruta_limpia)) {
        @unlink($ruta_limpia);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // BLOQUE 5: OBTENER DATOS DEL FORMULARIO ENVIADO
    // $_POST['accion']: Acción a realizar ('eliminar' o 'cambiar_estado')
    // $_POST['id_ausencia']: ID de la ausencia a gestionar
    // (int)(...): Convierte a entero para validar
    $accion = $_POST['accion'] ?? '';
    $id_ausencia = (int)($_POST['id_ausencia'] ?? 0);

    // PASO INTERNO: VALIDAR EL ID DE AUSENCIA
    // if ($id_ausencia <= 0): Si no es válido, muestra error y redirige
    if ($id_ausencia <= 0) {
        mensaje_y_redirigir('Identificador de ausencia no valido.', 'error');
    }

    // PASO INTERNO: ACCIÓN ELIMINAR AUSENCIA
    // if ($accion === 'eliminar'): Si la acción es eliminar
    if ($accion === 'eliminar') {
        // PASO 1: OBTENER DATOS DE LA AUSENCIA (ARCHIVOS A BORRAR)
        // prepare(...): Prepara consulta para obtener justificante y archivos
        // bind_param('i', $id_ausencia): Vincula el ID (entero)
        // execute() y get_result(): Ejecuta y obtiene resultado
        // fetch_assoc(): Convierte a array
        $stmt = $conexion->prepare('SELECT justificante, tarea_file, tarea_text FROM ausencia WHERE id_a = ?');
        $stmt->bind_param('i', $id_ausencia);
        $stmt->execute();
        $ausencia = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // PASO 2: VERIFICAR SI LA AUSENCIA EXISTE
        // if (!$ausencia): Si no existe, error
        if (!$ausencia) {
            mensaje_y_redirigir('La solicitud no existe.', 'error');
        }

        // PASO 3: BORRAR ARCHIVOS ASOCIADOS
        // borrar_archivo_si_existe(...): Llama a la función para borrar archivos
        borrar_archivo_si_existe($ausencia['justificante'] ?? null);
        borrar_archivo_si_existe($ausencia['tarea_file'] ?? null);

        // PASO 4: BORRAR ARCHIVOS MENCIONADOS EN EL TEXTO DE TAREA
        // preg_match_all(...): Busca patrones de archivos en el texto
        // foreach (...): Itera sobre los archivos encontrados y los borra
        if (!empty($ausencia['tarea_text']) && preg_match_all('/\[Adjunto tarea:\s*([^\]]+)\]/', $ausencia['tarea_text'], $matches)) {
            foreach ($matches[1] as $archivo) {
                borrar_archivo_si_existe($archivo);
            }
        }

        // PASO 5: ELIMINAR LA AUSENCIA DE LA BASE DE DATOS
        // prepare('DELETE ...'): Prepara consulta de eliminación
        // bind_param('i', $id_ausencia): Vincula el ID
        // execute(): Ejecuta la eliminación
        // if (!$stmt->execute()): Si falla, error
        $stmt = $conexion->prepare('DELETE FROM ausencia WHERE id_a = ?');
        $stmt->bind_param('i', $id_ausencia);
        if (!$stmt->execute()) {
            mensaje_y_redirigir('No se pudo eliminar la solicitud.', 'error');
        }

        // PASO 6: MENSAJE DE ÉXITO Y REDIRIGIR
        mensaje_y_redirigir('Solicitud eliminada correctamente.', 'exito');
    }

    // PASO INTERNO: ACCIÓN CAMBIAR ESTADO
    // if ($accion === 'cambiar_estado'): Si la acción es cambiar estado
    if ($accion === 'cambiar_estado') {
        // PASO 1: OBTENER EL NUEVO ESTADO
        // $_POST['nuevo_estado']: Estado enviado ('Pendiente', 'Aprobado', 'Rechazado')
        $nuevo_estado = trim($_POST['nuevo_estado'] ?? '');

        // PASO 2: VALIDAR EL ESTADO
        // $estados_validos: Array con estados permitidos
        // !in_array(...): Si no está en la lista, error
        $estados_validos = ['Pendiente', 'Aprobado', 'Rechazado'];
        if (!in_array($nuevo_estado, $estados_validos, true)) {
            mensaje_y_redirigir('Estado no valido.', 'error');
        }

        // PASO 3: ACTUALIZAR EL ESTADO EN LA BD
        // prepare('UPDATE ...'): Prepara consulta de actualización
        // bind_param('si', $nuevo_estado, $id_ausencia): Vincula estado (string) e ID (int)
        // execute(): Ejecuta la actualización
        // if (!$stmt->execute()): Si falla, error
        $stmt = $conexion->prepare('UPDATE ausencia SET estado = ? WHERE id_a = ?');
        $stmt->bind_param('si', $nuevo_estado, $id_ausencia);
        if (!$stmt->execute()) {
            mensaje_y_redirigir('No se pudo actualizar el estado.', 'error');
        }

        // PASO 4: MENSAJE DE ÉXITO Y REDIRIGIR
        mensaje_y_redirigir('Estado actualizado a: ' . $nuevo_estado, 'exito');
    }
}

// BLOQUE 6: CONSULTAR TODAS LAS AUSENCIAS PARA MOSTRAR EN LA TABLA
// $sql: Consulta SQL con JOINs para obtener datos de ausencias, usuarios y horarios
// prepare($sql): Prepara la consulta
// execute(): Ejecuta la consulta
// get_result(): Obtiene el resultado
// fetch_all(MYSQLI_ASSOC): Convierte todas las filas en un array de arrays asociativos
// close(): Cierra el statement
$sql = "SELECT a.id_a, a.dni_usuario, a.justificante, a.tipo_ausencia, a.tarea_file, a.tarea_text, a.estado, a.dia_a,
               u.nombre, u.apellido, h.hora, h.dia
        FROM ausencia a
        JOIN usuarios u ON a.dni_usuario = u.dni
        JOIN horario ho ON a.id_horario = ho.id_horario
        JOIN horas h ON ho.id_hora = h.id_hora
        ORDER BY a.id_a DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$ausencias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// BLOQUE 7: OBTENER MENSAJES DE LA SESIÓN PARA MOSTRAR
// $_SESSION['mensaje']: Mensaje guardado en funciones anteriores
// $_SESSION['tipo_mensaje']: Tipo del mensaje ('exito' o 'error')
// unset(...): Elimina los mensajes de la sesión después de obtenerlos
// $clase_mensaje: Determina la clase CSS basada en el tipo
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
$clase_mensaje = $tipo_mensaje === 'exito' ? 'mensaje mensaje-exito' : 'mensaje mensaje-error';
?>