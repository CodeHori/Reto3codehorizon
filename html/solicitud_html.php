<?php 
session_start();
    require __DIR__ . '/../config/conexionbd.php';
    require __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../php/solicitud.php';

    if (!$conexion) {
    die("Conexion fallida: " . mysqli_connect_error());
    }
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
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- ===== BLOQUE 10: INCLUIR NAVEGACIÓN =====-->
    <!-- include: Incluye el contenido del archivo nav.php (menú de navegación) -->
    <?php include __DIR__ . '/../interfaz/nav.php'; ?>

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
            <form method="POST" action="/php/solicitud.php" enctype="multipart/form-data" class="formulario-solicitud tarjeta tarjeta-formulario">
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
    <?php include __DIR__ . '/../interfaz/footer.php'; ?>
</body>
</html>