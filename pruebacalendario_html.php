<!DOCTYPE html>
  <html>
        <head>
            <title>Calendario</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
            <nav>
              <?php include __DIR__ . '/interfaz/nav.php'; ?> 

            </nav>
            <main>
                    <h1>Calendario</h1>
                    <table class="tabla-calendario">
                        <thead>
                            <th>Correo</th>
                            <th>Nombre</th>
                            <th>Apelido</th>
                            <th>Hora</th>
                            <th>Día</th>
                            <th>Familia</th>
                            <th>Aula</th>
                            <th>Modulo</th>
                            <th>Tarea</th>
                            <th>Tarea achivo</th>
                            <th>Firma</th>

                        </thead>
                        <?php if ($resultado->num_rows === 0) { ?>
                            <tr>
                                <td colspan="11">No hay datos esta semana</td><!-- no hay datos disponibles en la tabla, se muestra un mensaje en la tabla html -->

                            </tr>
                        <?php } else {?>
                        <?php while ($columna = mysqli_fetch_array($resultado)) { ?> 
                          
                            <tr> <!--sale el rsultado que es la variable $columna['nombre'] y se muestra en la tabla html-->
                                <td><?= htmlspecialchars($columna['correo_electronico']) ?></td>
                                <td><?= htmlspecialchars($columna['nombre']) ?></td> 
                                <td><?= htmlspecialchars($columna['apellido']) ?></td>
                                <td><?= htmlspecialchars($columna['id_hora'][1]) ?></td>
                                <td><?= htmlspecialchars($columna['dia']) ?></td> 
                                <td><?= htmlspecialchars($columna['familia']) ?></td>
                                <td><?= htmlspecialchars($columna['aula']) ?></td>
                                <td><?= htmlspecialchars($columna['modulo']) ?></td>

                                <td>
                                <?php
                                // Mostrar solo el texto de la tarea, sin el nombre del archivo adjunto
                                $texto_tarea = $columna['tarea_text'] ?? '';
                                if (!empty($texto_tarea)) {
                                    // Elimina el patrón [Adjunto tarea: ...]
                                    $solo_texto = preg_replace('/\[Adjunto tarea:[^\]]*\]/', '', $texto_tarea);
                                    echo nl2br(htmlspecialchars(trim($solo_texto)));
                                } else {
                                    echo '';
                                }
                                ?>
                                </td>
                                <td>
                                <?php
                                // Buscar adjunto en tarea_text (patrón: [Adjunto tarea: ruta])
                                // Si existe y el archivo está presente, muestra un enlace para abrirlo; de lo contrario, indica que no hay archivo
                                $adjunto_tarea = null;
                                if (!empty($columna['tarea_text']) && preg_match('/\[Adjunto tarea:\s*([^\]]+)\]/', $columna['tarea_text'], $m)) {
                                    $adjunto_tarea = $m[1];
                                }
                                if ($adjunto_tarea && file_exists($adjunto_tarea)) {
                                    echo '<a href="' . htmlspecialchars($adjunto_tarea) . '" target="_blank" class="boton boton-secundario boton-pequeno">Abrir archivo de tarea</a>';
                                } else {
                                    echo 'No hay archivo';
                                }
                                ?>
                                </td>
                                
                                <td>
                                    
                                    <form method="post" action="pruebacalendario.php">
                                          <?php
                                            if (!empty($guardias[$columna['id_a']])) { // !empty significa que el valor no está vacío. $guardias[$columna['id_a']] busca esa guardia por su ID usando el array $guardias.//
 
                                                
                                              echo '<p>Guardia guardada</p>';
                                                                                            
                        
                                         }else {
                                                echo '<input type="submit" name="guardia' . $columna['id_a'] . '" value="Aceptar Guardia">' ;
                                         }      
                                          ?>

                                    </form>                                    
                                </tr>
                        <?php } ?>
                        
    
                        
                <?php } ?>
                <?php
                
                $resultado->free();  //se libera la actual cosulta para limpiar la memoria.//
                $conexion->close(); //se cierra la conexion a la base de datos.//
                ?>
                </table>
                <button><a href="horarioprofesor.php">Tu horario</a></button>

            </main>
            <footer>
              <?php include __DIR__ . '/interfaz/footer.php'; ?>  
            </footer>  
        </body>
</html>