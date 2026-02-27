<?php
// BLOQUE 1: INCLUIR ARCHIVOS DE AUTENTICACIÓN Y CONEXIÓN
// require_once __DIR__ . '/auth.php': Verifica que el usuario esté logueado
// require_once __DIR__ . '/conexionbd.php': Conecta a la base de datos
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexionbd.php';

// BLOQUE 2: VERIFICAR ROL DE ADMINISTRADOR
// $_SESSION['rol']: Rol del usuario
// !== 'admin': Si no es admin, error y redirige
if (($_SESSION['rol'] ?? '') !== 'admin') {
    $_SESSION['mensaje'] = 'Solo administradores pueden ver el historial.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: home.php');
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Guardias</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/interfaz/nav.php'; ?>

    <main>
        <div class="contenedor">
            <div class="encabezado-centrado">
                <h1>Historial de Guardias</h1>
                <p class="texto-sutil">Curso actual: <?php echo htmlspecialchars($inicio_curso . ' a ' . $fin_curso); ?></p>
            </div>

            <div class="buscador-simple">
                <input id="searchInput" type="text" placeholder="Buscar por DNI, tipo, estado, fecha...">
            </div>

            <div class="table-wrapper">
                <table class="historial-ausencias">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>ID Horario</th>
                            <th>Nombre</th>
                            <th>Justificante</th>
                            <th>Tipo</th>
                            <th>Tarea (archivo)</th>
                            <th>Tarea (texto)</th>
                            <th>Estado</th>
                            <th>Dia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows === 0): ?>
                            <tr>
                                <td colspan="8">No hay registros en el rango seleccionado.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($fila = $resultado->fetch_assoc()):/*bucle que recorre todas las columnas de la tabla colocando 
                                la inofmracion de la consulta*/?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fila['dni_usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['id_horario']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido']);?></td>
                                    <td>
                                        <?php if (!empty($fila['justificante']) && file_exists($fila['justificante'])): ?>
                                            <a href="<?php echo htmlspecialchars($fila['justificante']); ?>" target="_blank" class="boton boton-pequeno boton-primario">Abrir</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($fila['tipo_ausencia']); ?></td>
                                    <td>
                                        <?php if (!empty($fila['tarea_file']) && file_exists($fila['tarea_file'])): ?>
                                            <!-- Si el archivo existe, muestra un enlace para abrirlo 
                                            target="_blank" es para abrir en una nueva pestaña-->
                                            <a href="<?php echo htmlspecialchars($fila['tarea_file']); ?>" target="_blank" class="boton boton-pequeno boton-secundario">Abrir</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($fila['tarea_text'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['dia_a'] ?? ''); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('searchInput');
        if (!input) return;

        input.addEventListener('input', function () {
            var filtro = input.value.toLowerCase();
            var filas = document.querySelectorAll('.historial-ausencias tbody tr');

            filas.forEach(function (fila) {
                var texto = fila.textContent.toLowerCase();
                fila.style.display = texto.indexOf(filtro) !== -1 ? '' : 'none';
            });
        });
    });
    </script>

    <?php include __DIR__ . '/interfaz/footer.php'; ?>
</body>
</html>