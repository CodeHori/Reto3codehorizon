<?php
//BLOQUE 1: INCLUIR ARCHIVOS DE CONEXION A LA BD Y AUTENTICACION DE USUARIO
//require_once: Incluye un archivo PHP solo una vez (evita duplicaciones)
//__DIR__ . '/auth.php': Incluye el archivo de autenticación que gestiona las sesiones de usuario
require_once __DIR__ . '/auth.php';
//Incluye el archivo de conexión a la base de datos para poder conectar y ejecutar consultas
require_once __DIR__ . '/conexionbd.php';

//BLOQUE 2: OBTENER DNI DEL USUARIO DE LA SESIÓN
//$_SESSION['dni']: Obtiene el DNI del usuario que está logueado (guardado en la sesión)
//Esta variable se usará frecuentemente para identificar de quién es la solicitud de ausencia
$dni_usuario = $_SESSION['dni'];

//BLOQUE 3: FUNCIÓN PARA REDIRIGIR CON MENSAJES 
//Esta función redirige al usuario a la misma página pero pasa mensajes (éxito o error)
//string $mensaje: El texto del mensaje a mostrar
//string $tipo: Tipo de mensaje ('error' o 'exito', por defecto 'error')
//void: Esta función no retorna nada (solo redirige)
function redirigir_con_mensaje(string $mensaje, string $tipo = 'error'): void
{
    //Redirige a solicitud.php con el mensaje y tipo en la URL mediante GET
    //urlencode(): Convierte caracteres especiales para que se puedan pasar por URL
    header('Location: solicitud.php?mensaje=' . urlencode($mensaje) . '&tipo=' . urlencode($tipo));
    //exit(): Detiene la ejecución del script después de la redirección
    exit();
}

//BLOQUE 4: FUNCIÓN PARA GUARDAR ARCHIVOS SUBIDOS
//array $archivo: Array del archivo ($_FILES) que contiene nombre, tamaño, tipo, ruta temporal, etc.
//array $extensiones_permitidas: Lista de extensiones que se permiten guardar (ej: ['pdf', 'jpg'])
//string $prefijo: Prefijo para el nombre del archivo guardado (ej: 'justificante' o 'tarea')
//?string: Retorna la ruta guardada o null si hay error
function guardar_archivo(array $archivo, array $extensiones_permitidas, string $prefijo): ?string
{
    //PASO 1: VERIFICAR SI EL ARCHIVO DA ERROR O NO SE SUBIÓ
    //?? UPLOAD_ERR_NO_FILE: Usa UPLOAD_ERR_NO_FILE si el índice 'error' no existe
    //!== UPLOAD_ERR_OK: Si el error NO es 0 (OK), retorna null (el upload falló)
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    //PASO 2: VALIDAR LA EXTENSIÓN DEL ARCHIVO 
    //$archivo['name'] ?? '': Obtiene el nombre original del archivo, o string vacío si no existe
    $nombre_original = $archivo['name'] ?? '';
    //strtolower(): Convierte a minúsculas
    //pathinfo(..., PATHINFO_EXTENSION): Extrae la extensión del nombre (ej: 'pdf' de 'documento.PDF')
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    //!in_array(..., ..., true): Si la extensión NO está en la lista permitida, retorna null
    //El true = búsqueda estricta (evita problemas de tipo de dato)
    if (!in_array($extension, $extensiones_permitidas, true)) {
        return null;
    }

    //PASO 3: CREAR LA CARPETA DE UPLOADS SI NO EXISTE 
    //__DIR__: Directorio actual del script
    $carpeta_fisica = __DIR__ . '/uploads';
    //!is_dir(...) && !mkdir(...): Si la carpeta no existe (is_dir = false) Y no se puede crear
    //mkdir(..., 0755, true): 0755 = permisos, true = crear carpetas necesarias recursivamente
    if (!is_dir($carpeta_fisica) && !mkdir($carpeta_fisica, 0755, true)) {
        return null;
    }

    //PASO 4: GENERAR UN NOMBRE ÚNICO PARA EL ARCHIVO
    //time(): Obtiene la marca de tiempo actual (segundos desde 1970)
    //random_bytes(4): Genera 4 bytes aleatorios
    //bin2hex(): Convierte bytes a hexadecimal (8 caracteres)
    //Resultado: nombre como "justificante_1708874532_a7f3c21e.pdf"
    $nombre_nuevo = $prefijo . '_' . time() . '_' . uniqid() . '.' . $extension;
    //Ruta completa donde se guardará el archivo en el servidor
    $ruta_fisica = $carpeta_fisica . '/' . $nombre_nuevo;
    // Ruta relativa que se guardará en la base de datos (para acceso web)
    $ruta_guardada = 'uploads/' . $nombre_nuevo;

    //PASO 5: MOVER EL ARCHIVO TEMPORAL A LA CARPETA FINAL =====
    //move_uploaded_file(): Mueve archivo de la carpeta temporal de PHP a la carpeta destino
    //$archivo['tmp_name']: Es la ruta temporal del archivo subido
    //Si falla, retorna null
    if (!move_uploaded_file($archivo['tmp_name'], $ruta_fisica)) {
        return null;
    }

    //PASO 6: RETORNAR LA RUTA RELATIVA 
    //Retorna la ruta que usaremos para guardar en la base de datos
    return $ruta_guardada;
}

//BLOQUE 5: PROCESAR EL FORMULARIO CUANDO SE ENVÍA POR POST
//$_SERVER['REQUEST_METHOD']: Método HTTP usado (GET, POST, etc.)
//Se ejecuta solo si el usuario envía el formulario (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //PASO 1: OBTENER LOS DATOS DEL FORMULARIO 
    //trim(): Elimina espacios en blanco al inicio y final
    //$_POST[...] ?? '': Obtiene el valor enviado o string vacío si no existe
    $tipo_ausencia = trim($_POST['tipo_ausencia'] ?? '');
    //(int)(...): Convierte a número entero (seguridad de tipo)
    $id_horario = (int)($_POST['id_horario'] ?? 0);
    $tarea_text = trim($_POST['tarea_text'] ?? '');

    //PASO 2: VALIDAR CAMPOS OBLIGATORIOS =====
    //Verifica que tipo_ausencia no esté vacío Y que id_horario sea mayor a 0
    if ($tipo_ausencia === '' || $id_horario <= 0) {
        //Si falta algo, redirige con mensaje de error
        redirigir_con_mensaje('Faltan campos obligatorios');
    }

    //PASO 3: VALIDAR QUE SE ADJUNTÓ EL JUSTIFICANTE 
    //isset(): Verifica si existe la variable
    //El justificante siempre es obligatorio
    if (!isset($_FILES['justificante_file'])) {
        redirigir_con_mensaje('Debes subir un justificante');
    }

    //PASO 4: DEFINIR EXTENSIONES PERMITIDAS =====
    //Array de extensiones válidas para el justificante
    $ext_justificante = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    //Array de extensiones válidas para la tarea (más variedad)
    $ext_tarea = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];

    //PASO 5: GUARDAR EL JUSTIFICANTE 
    //Llama la función guardar_archivo con:
    //- El archivo del formulario
    //- Extensiones permitidas para justificantes
    // - Prefijo 'justificante' para el nombre del archivo
    $ruta_justificante = guardar_archivo($_FILES['justificante_file'], $ext_justificante, 'justificante');
    //Si retorna null, significa que hubo error (archivo inválido o no se pudo guardar)
    if ($ruta_justificante === null) {
        redirigir_con_mensaje('No se pudo guardar el justificante o su formato no es valido');
    }

    //PASO 6: GUARDAR LA TAREA (OPCIONAL) 
    //Inicializa como null (en caso de que no se adjunte tarea)
    $ruta_tarea = null;
    //Verifica si se envió un archivo de tarea Y que no sea un error de "sin archivo"
    if (isset($_FILES['tarea_file']) && ($_FILES['tarea_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        // Intenta guardar el archivo de tarea
        $ruta_tarea = guardar_archivo($_FILES['tarea_file'], $ext_tarea, 'tarea');
        //Si hay error, redirige con mensaje
        if ($ruta_tarea === null) {
            redirigir_con_mensaje('El archivo de tarea no se pudo guardar o no tiene formato valido');
        }
    }

    //PASO 7: PREPARAR LA FECHA ACTUAL
    //date('Y-m-d'): Formatea la fecha actual como YYYY-MM-DD (2026-02-24)
    $fecha_actual = date('Y-m-d');
    
    //PASO 8: CONSTRUIR LA CONSULTA SQL
    //INSERT INTO: Inserta un nuevo registro
    //Tabla 'ausencia' con sus campos
    //VALUES (?, ?, ...): Los ? son placeholders para valores que se pasarán después (previene SQL injection)
    $sql = "INSERT INTO ausencia
            (dni_usuario, id_horario, justificante, tipo_ausencia, tarea_file, tarea_text, estado, dia_a)
            VALUES (?, ?, ?, ?, ?, ?, 'Pendiente', ?)";
    
    //PASO 9: PREPARAR LA CONSULTA =====
    //prepare(): Prepara la consulta SQL en la base de datos
    //Retorna un objeto statement o false si hay error
    $stmt = $conexion->prepare($sql);

    //Verifica que la preparación fue exitosa
    if (!$stmt) {
        redirigir_con_mensaje('Error interno al guardar la solicitud');
    }

    //PASO 10: VINCULAR PARÁMETROS A LA CONSULTA
    //bind_param('sisssss', ...): Vincula variables a los placeholders ?
    //'s' = string, 'i' = integer
    //Orden: s(dni_usuario) i(id_horario) s(justificante) s(tipo_ausencia) s(tarea_file) s(tarea_text) s(fecha)
    $stmt->bind_param(
        'sisssss',
        $dni_usuario,
        $id_horario,
        $ruta_justificante,
        $tipo_ausencia,
        $ruta_tarea,
        $tarea_text,
        $fecha_actual
    );

    //PASO 11: EJECUTAR LA CONSULTA
    //execute(): Ejecuta la consulta SQL con los parámetros vinculados
    if (!$stmt->execute()) {
        redirigir_con_mensaje('No se pudo guardar la solicitud');
    }

    //PASO 12: REDIRIGIR CON MENSAJE DE ÉXITO
    //Si todo fue exitoso, redirige con mensaje de confirmación
    redirigir_con_mensaje('Solicitud enviada correctamente', 'exito');
}

//BLOQUE 6: OBTENER DATOS DEL USUARIO DESDE LA BASE DE DATOS
//prepare(): Prepara una consulta SQL para obtener nombre y apellido
//? es un placeholder que se reemplazará con el DNI del usuario
$stmt = $conexion->prepare('SELECT nombre, apellido FROM usuarios WHERE dni = ?');
//bind_param('s', ...): Vincula la variable $dni_usuario (string) al placeholder
$stmt->bind_param('s', $dni_usuario);
//execute(): Ejecuta la consulta
$stmt->execute();
//get_result(): Obtiene el resultado de la consulta
//fetch_assoc(): Convierte la fila en un array asociativo (ej: ['nombre' => 'Juan', 'apellido' => 'Pérez'])
$usuario = $stmt->get_result()->fetch_assoc();
//close(): Cierra el statement para liberar recursos
$stmt->close();

//BLOQUE 7: OBTENER LOS HORARIOS DEL USUARIO
//Consulta SELECT que obtiene: id_horario, día, hora, aula y módulo del usuario
//JOIN: Relaciona dos tablas (horario y horas) por su clave de relación
//WHERE: Filtra solo los horarios del usuario actual
//ORDER BY FIELD: Ordena los días en orden específico (Lunes, Martes, Miércoles, etc.)
//ASC: Orden ascendente por hora
$sql_horas = "SELECT ho.id_horario, h.dia, h.hora, ho.aula, ho.modulo
              FROM horario ho
              JOIN horas h ON ho.id_hora= h.id_hora
              WHERE ho.dni_usuario = ?
              ORDER BY FIELD(h.dia, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'), h.hora ASC";
//Prepara la consulta
$stmt = $conexion->prepare($sql_horas);
//Vincula el DNI del usuario
$stmt->bind_param('s', $dni_usuario);
//Ejecuta
$stmt->execute();
// fetch_all(MYSQLI_ASSOC): Obtiene todos los resultados como un array de arrays asociativos
$horas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// Cierra el statement
$stmt->close();

// ===== BLOQUE 8: PREPARAR DATOS PARA MOSTRAR EN LA PÁGINA =====
// Array con los tipos de ausencia disponibles para seleccionar
$tipos_ausencia = ['Enfermedad', 'Permiso Personal', 'Asunto Urgente', 'Cita Medica', 'Justificado', 'Otros'];
// Obtiene el parámetro "mensaje" de la URL (?mensaje=...)
// urldecode(): Decodifica caracteres especiales (%20 = espacio, etc.)
// ?? '': Si no existe el parámetro, usa string vacío
$mensaje = urldecode($_GET['mensaje'] ?? '');
// Obtiene el parámetro "tipo" de la URL para saber si es error o éxito
$tipo = $_GET['tipo'] ?? 'info';
// Define la clase CSS según el tipo de mensaje
// Si tipo es 'exito', usa clase 'mensaje mensaje-exito'; si no, usa 'mensaje mensaje-error'
$clase_mensaje = $tipo === 'exito' ? 'mensaje mensaje-exito' : 'mensaje mensaje-error';
// ===== BLOQUE 9: INICIO DEL DOCUMENTO HTML =====
// <!DOCTYPE html>: Declara que es HTML5
// <html lang="es">: Elemento raíz del documento, lang="es" = idioma español
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Meta charset: Define la codificación de caracteres como UTF-8 (para acentos, ñ, etc.) -->
    <meta charset="UTF-8">
    <!-- Meta viewport: Hace la página responsive (se adapta a diferentes tamaños de pantalla) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título de la pestaña del navegador -->
    <title>Solicitud de Ausencia</title>
    <!-- Vincula el archivo CSS de estilos -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- ===== BLOQUE 10: INCLUIR NAVEGACIÓN =====-->
    <!-- include: Incluye el contenido del archivo nav.php (menú de navegación) -->
    <?php include __DIR__ . '/interfaz/nav.php'; ?>

    <!-- ===== BLOQUE 11: CONTENEDOR PRINCIPAL =====-->
    <!-- <main>: Elemento semántico para el contenido principal de la página -->
    <main>
        <!-- <div class="contenedor">: Contenedor general que limita el ancho de contenido -->
        <div class="contenedor">
            <!-- ===== BLOQUE 12: ENCABEZADO DE LA PÁGINA ===== -->
            <!-- div con clases de estilo: margen-abajo-30 (margen inferior), encabezado-centrado (centrado) -->
            <div class="margen-abajo-30 encabezado-centrado">
                <!-- Título principal de la página -->
                <h1>Solicitud de Ausencia</h1>
            </div>

            <!-- ===== BLOQUE 13: TARJETA CON DATOS DEL USUARIO ===== -->
            <!-- <div class="tarjeta">: Contenedor estilizado como tarjeta -->
            <div class="tarjeta">
                <!-- Muestra el nombre y apellido del usuario logueado -->
                <!-- htmlspecialchars(): Convierte caracteres especiales a entidades HTML (seguridad contra XSS) -->
                <!-- ?? '': Usa string vacío si no existe la clave -->
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? '')); ?></p>
                <!-- Muestra el DNI del usuario -->
                <p><strong>DNI:</strong> <?php echo htmlspecialchars($dni_usuario); ?></p>
            </div>

            <!-- ===== BLOQUE 14: MOSTRAR MENSAJES (ERROR O ÉXITO) ===== -->
            <!-- Condicional: Solo muestra si hay un mensaje en la URL -->
            <?php if ($mensaje !== ''): ?>
                <!-- div con clase dinámica: si es éxito, muestra en verde; si es error, en rojo -->
                <div class="<?php echo $clase_mensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <!-- ===== BLOQUE 15: FORMULARIO DE SOLICITUD ===== -->
            <!-- <form>: Elemento de formulario -->
            <!-- method="POST": Envía datos al servidor usando POST (más seguro que GET) -->
            <!-- action="solicitud.php": El formulario se procesa en el mismo archivo -->
            <!-- enctype="multipart/form-data": Permite enviar archivos (requerido para <input type="file">) -->
            <!-- class="formulario-solicitud tarjeta tarjeta-formulario": Clases de estilo CSS -->
            <form method="POST" action="solicitud.php" enctype="multipart/form-data" class="formulario-solicitud tarjeta tarjeta-formulario">
                <!-- <div class="form-grid two-cols">: Contenedor con grid de 2 columnas (responsivo) -->
                <div class="form-grid two-cols">
                    <!-- ===== CAMPO 1: TIPO DE AUSENCIA ===== -->
                    <div>
                        <!-- <label>: Etiqueta asociada al campo (mejora accesibilidad) -->
                        <label for="tipo_ausencia" class="etiqueta-campo">Tipo de ausencia</label><br>
                        <!-- <select>: Desplegable para seleccionar -->
                        <!-- name="tipo_ausencia": Nombre del campo que se envía en POST -->
                        <!-- id="tipo_ausencia": Identificador único del elemento -->
                        <!-- required: El campo es obligatorio (validación HTML5) -->
                        <select name="tipo_ausencia" id="tipo_ausencia" required class="entrada-campo">
                            <!-- <option value="">: Opción por defecto (placeholder) -->
                            <option value="">Selecciona un tipo</option>
                            <!-- foreach: Itera sobre cada tipo de ausencia en el array -->
                            <?php foreach ($tipos_ausencia as $tipo_item): ?>
                                <!-- Crea una opción para cada tipo de ausencia -->
                                <option value="<?php echo htmlspecialchars($tipo_item); ?>"><?php echo htmlspecialchars($tipo_item); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- ===== CAMPO 2: HORA DE AUSENCIA ===== -->
                    <div>
                        <label for="id_horario" class="etiqueta-campo">Hora de ausencia</label><br>
                        <!-- Desplegable con los horarios del usuario -->
                        <select name="id_horario" id="id_horario" required class="entrada-campo">
                            <option value="">Selecciona una hora</option>
                            <!-- foreach: Itera sobre cada horario disponible -->
                            <?php foreach ($horas as $hora): ?>
                                <!-- Crea opción con: id_horario como value, y texto mostrando día-hora-aula -->
                                <!-- Ejemplo: "Lunes - 08:00 (A101)" -->
                                <option value="<?php echo htmlspecialchars($hora['id_horario']); ?>">
                                    <?php echo htmlspecialchars($hora['dia'] . ' - ' . $hora['hora'] . ':00 (' . $hora['aula'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- ===== CAMPO 3: ARCHIVO JUSTIFICANTE (OBLIGATORIO) ===== -->
                    <div>
                        <label for="justificante_file" class="etiqueta-campo">Adjuntar justificante (obligatorio)</label><br>
                        <!-- <input type="file">: Selector de archivos -->
                        <!-- name="justificante_file": Nombre del campo -->
                        <!-- accept=".pdf,.doc,.docx,...": Limita los tipos de archivo que se pueden seleccionar -->
                        <!-- required: Campo obligatorio -->
                        <input type="file" name="justificante_file" id="justificante_file" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg" required class="entrada-archivo">
                        <!-- <small>: Texto pequeño con información adicional -->
                        <small class="texto-sutil-pequeno">Formatos: PDF, DOC, DOCX, JPG, PNG</small>
                    </div>

                    <!-- ===== CAMPO 4: TEXTO DE LA TAREA (OPCIONAL) ===== -->
                    <div>
                        <label for="tarea_text" class="etiqueta-campo">Texto de la tarea (opcional)</label><br>
                        <!-- <textarea>: Área de texto para escribir múltiples líneas -->
                        <!-- rows="3": Altura de 3 líneas por defecto -->
                        <!-- placeholder: Texto gris de ayuda que desaparece al escribir -->
                        <textarea name="tarea_text" id="tarea_text" rows="3" placeholder="Descripcion u observaciones" class="area-texto"></textarea>
                    </div>

                    <!-- ===== CAMPO 5: ARCHIVO DE TAREA (OPCIONAL) ===== -->
                    <div>
                        <label for="tarea_file" class="etiqueta-campo">Adjuntar tarea (opcional)</label><br>
                        <!-- Selector de archivo para la tarea (sin required, ya que es opcional) -->
                        <input type="file" name="tarea_file" id="tarea_file" accept=".pdf,.doc,.docx,.txt,.jpg,.png,.jpeg" class="entrada-archivo">
                        <small class="texto-sutil-pequeno">Formatos: PDF, DOC, DOCX, TXT, JPG, PNG</small>
                    </div>

                    <!-- ===== CAMPO 6: BOTONES DE ENVÍO Y LIMPIEZA ===== -->
                    <!-- div con clase full: Ocupa el ancho completo (2 columnas) -->
                    <div class="botones-solicitud full"><br>
                        <!-- <button type="submit">: Botón que envía el formulario -->
                        <button type="submit" class="boton-enviar">Enviar solicitud</button>
                        <!-- <button type="reset">: Botón que limpia todos los campos del formulario -->
                        <button type="reset" class="boton-limpiar">Limpiar formulario</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- ===== BLOQUE 16: INCLUIR PIE DE PÁGINA ===== -->
    <!-- include: Incluye el contenido del archivo footer.php (pie de página) -->
    <?php include __DIR__ . '/interfaz/footer.php'; ?>
</body>
</html>
