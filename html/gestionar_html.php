<?php 
session_start();
    require_once '../php/gestionar.php';

    if (!$conexion) {
    die("Conexion fallida: " . mysqli_connect_error());
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Solicitudes de Ausencia</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../interfaz/nav.php'; ?>

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
                                        <?php
                                        $ruta_just_fs = '';
                                        if (!empty($ausencia['justificante'])) {
                                            $ruta_just_fs = __DIR__ . '/../' . ltrim($ausencia['justificante'], '/');
                                        }
                                        if (!empty($ausencia['justificante']) && file_exists($ruta_just_fs)):
                                        ?>
                                            <a href="/<?php echo htmlspecialchars(ltrim($ausencia['justificante'], '/')); ?>" target="_blank" class="boton boton-primario boton-pequeno">Abrir</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="celda-texto">
                                        <?php if ($texto_tarea !== ''): ?>
                                            <div style="margin-bottom: 6px;"><?php echo nl2br(htmlspecialchars($texto_tarea)); ?></div>
                                        <?php endif; ?>

                                        <?php
                                        $ruta_tarea_fs = '';
                                        if ($adjunto_tarea !== '') {
                                            $ruta_tarea_fs = __DIR__ . '/../' . ltrim($adjunto_tarea, '/');
                                        }
                                        if ($adjunto_tarea !== '' && file_exists($ruta_tarea_fs)): ?>
                                            <a href="/<?php echo htmlspecialchars(ltrim($adjunto_tarea, '/')); ?>" target="_blank" class="boton boton-secundario boton-pequeno">Abrir archivo</a>
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
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../interfaz/footer.php'; ?>
</body>
</html>
