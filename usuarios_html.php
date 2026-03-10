<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de usuarios</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/interfaz/nav.php'; ?>

    <main>
        <div class="contenedor">
            <div class="encabezado-centrado">
                <h1>Gestion de usuarios</h1>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="<?php echo $clase_mensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <div class="tarjeta tarjeta-formulario ancho-grande">
                <h2 style="margin-bottom: 15px;"><?php echo $editar ? 'Editar usuario' : 'Nuevo usuario'; ?></h2>
                <form method="post" action="usuarios.php">
                    <input type="hidden" name="accion" value="<?php echo $editar ? 'editar' : 'crear'; ?>">
                    <div class="form-inline-row">
                        <div class="form-inline-item">
                            <label class="form-label">DNI</label>
                            <input class="control-formulario" type="text" name="dni" placeholder="DNI" pattern="[A-Z0-9]+" minlength="9" maxlength="9" required value="<?php echo htmlspecialchars($usuario_edicion['dni']); ?>" <?php echo $editar ? 'readonly' : ''; ?>>
                        </div>

                        <div class="form-inline-item">
                            <label class="form-label">Nombre</label>
                            <input class="control-formulario" type="text" name="nombre" placeholder="Nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ]+" minlength="3" required value="<?php echo htmlspecialchars($usuario_edicion['nombre']); ?>">
                        </div>

                        <div class="form-inline-item">
                            <label class="form-label">Apellido</label>
                            <input class="control-formulario" type="text" name="apellido" placeholder="Apellido" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ]+" minlength="3" required value="<?php echo htmlspecialchars($usuario_edicion['apellido']); ?>">
                        </div>

                        <div class="form-inline-item">
                            <label class="form-label">Correo</label>
                            <input class="control-formulario" type="email" name="correo_electronico" placeholder="Correo" required value="<?php echo htmlspecialchars($usuario_edicion['correo_electronico']); ?>">
                        </div>

                        <div class="form-inline-item">
                            <label class="form-label"><?php echo $editar ? 'Nueva contraseña (opcional)' : 'Contraseña'; ?></label>
                            <input class="control-formulario" type="password" name="contraseña" placeholder="Contraseña" minlength="8">
                        </div>

                        <div class="form-inline-item">
                            <label class="form-label">Familia</label>
                            <input class="control-formulario" type="text" name="familia" placeholder="Familia"  pattern="[a-záéíóú]+" value="<?php echo htmlspecialchars($usuario_edicion['familia']); ?>">
                        </div>

                        <div class="form-inline-item">
                            <label class="form-label">Rol</label>
                            <select class="control-formulario" name="rol">
                                <option value="usuario" <?php echo $usuario_edicion['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                <option value="admin" <?php echo $usuario_edicion['rol'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div><br>

                    <div class="acciones-formulario">
                        <button class="boton boton-primario" type="submit" name="guardar">Guardar</button>
                        <?php if ($editar): ?>
                            <a class="boton boton-enlace" href="usuarios.php">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="margen-abajo-30">
                <h2 class="texto-centrado">Listado de usuarios</h2><br>
                <div class="table-wrapper">
                    <table class="tarjeta tarjeta-tabla">
                        <thead>
                            <tr class="table-header">
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Correo</th>
                                <th>Familia</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($listado && $listado->num_rows > 0): ?>
                            <?php while ($fila = $listado->fetch_assoc()): ?>
                                <tr class="table-row-hover">
                                    <td class="celda-id"><?php echo htmlspecialchars($fila['dni']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($fila['apellido']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($fila['correo_electronico']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($fila['familia']); ?></td>
                                    <td class="celda-texto"><?php echo htmlspecialchars($fila['rol']); ?></td>
                                    <td>
                                        <a class="boton boton-pequeno boton-primario" href="usuarios.php?editar=<?php echo urlencode($fila['dni']); ?>">Editar</a>
                                        <a class="boton boton-pequeno boton-rechazar" href="usuarios.php?eliminar=<?php echo urlencode($fila['dni']); ?>" onclick="return confirm('¿Eliminar usuario?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No hay usuarios registrados.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/interfaz/footer.php'; ?>
</body>
</html>