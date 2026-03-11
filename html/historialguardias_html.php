<?php 
session_start();
    require_once __DIR__ . '/../php/historialguardias.php';

    if (!$conexion) {
    die("Conexion fallida: " . mysqli_connect_error());
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Guardias</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../interfaz/nav.php'; ?>
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
                                        <?php
                                        $ruta_just_fs = '';
                                        if (!empty($fila['justificante'])) {
                                            $ruta_just_fs = __DIR__ . '/../' . ltrim($fila['justificante'], '/');
                                        }
                                        if (!empty($fila['justificante']) && file_exists($ruta_just_fs)): ?>
                                            <a href="/<?php echo htmlspecialchars(ltrim($fila['justificante'], '/')); ?>" target="_blank" class="boton boton-pequeno boton-primario">Abrir</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($fila['tipo_ausencia']); ?></td>
                                    <td>
                                        <?php
                                        $ruta_tarea_fs = '';
                                        if (!empty($fila['tarea_file'])) {
                                            $ruta_tarea_fs = __DIR__ . '/../' . ltrim($fila['tarea_file'], '/');
                                        }
                                        if (!empty($fila['tarea_file']) && file_exists($ruta_tarea_fs)): ?>
                                            <!-- Si el archivo existe, muestra un enlace para abrirlo 
                                            target="_blank" es para abrir en una nueva pestaña-->
                                            <a href="/<?php echo htmlspecialchars(ltrim($fila['tarea_file'], '/')); ?>" target="_blank" class="boton boton-pequeno boton-secundario">Abrir</a>
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

    <?php include __DIR__ . '/../interfaz/footer.php'; ?>
</body>
</html>