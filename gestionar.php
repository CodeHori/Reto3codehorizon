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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Solicitudes de Ausencia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/interfaz/nav.php'; ?>

    <main>
        <div class="contenedor">
            <div class="encabezado-centrado margen-abajo-30">
                <h1>Gestionar Solicitudes de Ausencia</h1>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="<?php echo $clase_mensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <?php if (!empty($ausencias)): ?>
                <div class="table-wrapper">
                    <table class="tarjeta tarjeta-tabla">
                        <thead>
                            <tr class="table-header">
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Dia</th>
                                <th>Hora</th>
                                <th>Justificante</th>
                                <th>Tarea</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ausencias as $ausencia): ?>
                                <?php
                                $texto_tarea = trim((string)($ausencia['tarea_text'] ?? ''));
                                $adjunto_tarea = trim((string)($ausencia['tarea_file'] ?? ''));

                                if ($texto_tarea !== '' && preg_match('/\[Adjunto tarea:\s*([^\]]+)\]/', $texto_tarea, $match_adjunto)) {
                                    if ($adjunto_tarea === '') {
                                        $adjunto_tarea = trim($match_adjunto[1]);
                                    }
                                    $texto_tarea = trim(str_replace($match_adjunto[0], '', $texto_tarea));
                                }
                                ?>
                                <tr class="table-row-hover">
                                    <td class="celda-id"><?php echo (int)$ausencia['id_a']; ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($ausencia['nombre'] . ' ' . $ausencia['apellido']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($ausencia['tipo_ausencia']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($ausencia['dia']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($ausencia['hora'] . ':00'); ?></td>
                                    <td class="celda-accion">
                                        <?php if (!empty($ausencia['justificante']) && file_exists($ausencia['justificante'])): ?>
                                            <a href="<?php echo htmlspecialchars($ausencia['justificante']); ?>" target="_blank" class="boton boton-primario boton-pequeno">Abrir</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="celda-texto">
                                        <?php if ($texto_tarea !== ''): ?>
                                            <div style="margin-bottom: 6px;"><?php echo nl2br(htmlspecialchars($texto_tarea)); ?></div>
                                        <?php endif; ?>

                                        <?php if ($adjunto_tarea !== '' && file_exists($adjunto_tarea)): ?>
                                            <a href="<?php echo htmlspecialchars($adjunto_tarea); ?>" target="_blank" class="boton boton-secundario boton-pequeno">Abrir archivo</a>
                                        <?php elseif ($texto_tarea === ''): ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($ausencia['estado']); ?></td>
                                    <td>
                                        <?php if ($ausencia['estado'] === 'Pendiente'): ?>
                                            <form method="POST" style="margin-bottom: 6px;">
                                                <input type="hidden" name="accion" value="cambiar_estado">
                                                <input type="hidden" name="id_ausencia" value="<?php echo (int)$ausencia['id_a']; ?>">
                                                <input type="hidden" name="nuevo_estado" value="Aprobado">
                                                <button type="submit" class="boton boton-pequeno boton-aprobar">Aprobar</button>
                                            </form>

                                            <form method="POST">
                                                <input type="hidden" name="accion" value="cambiar_estado">
                                                <input type="hidden" name="id_ausencia" value="<?php echo (int)$ausencia['id_a']; ?>">
                                                <input type="hidden" name="nuevo_estado" value="Rechazado">
                                                <button type="submit" class="boton boton-pequeno boton-rechazar">Rechazar</button>
                                            </form>
                                        <?php else: ?>
                                            Sin acciones
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('¿Eliminar solicitud?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id_ausencia" value="<?php echo (int)$ausencia['id_a']; ?>">
                                            <button type="submit" class="boton boton-pequeno boton-rechazar">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="caja-centrada">
                    <p class="texto-nohaysoli">No hay solicitudes de ausencia registradas.</p>
                    <a class="gestionaralformulario" href="solicitud.php">Crear nueva solicitud</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/interfaz/footer.php'; ?>
</body>
</html>
