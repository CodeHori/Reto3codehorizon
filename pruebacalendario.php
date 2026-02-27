<?php
session_start();

require_once "conexionbd.php"; 

if (!isset($_SESSION['dni'])) {
    header('location: index.php');
    exit();
}

$SQL = "SELECT
    ausencia.id_a,
  horario.id_horario,
  horario.aula,
  horario.modulo,
  horario.id_hora_,
  usuarios.dni,
  usuarios.correo_electronico,
  usuarios.familia,
  usuarios.nombre,
  usuarios.apellido,
  horas.id_hora,
  horas.hora,
  horas.dia
FROM
  ausencia
  LEFT JOIN horario ON ausencia.id_horario = horario.id_horario
  LEFT JOIN usuarios ON horario.dni_usuario = usuarios.dni
  LEFT JOIN horas ON horario.id_hora_ = horas.id_hora
  WHERE LEFT(horario.id_hora_, 1) =
       ELT(DAYOFWEEK(CURDATE()), 'D', 'L', 'M', 'X', 'J', 'V', 'S')
  AND DAYOFWEEK(CURDATE()) BETWEEN 2 AND 6

    AND ausencia.estado != 'pendiente'
LIMIT 50";

//Selecciona los datos de las tablas mediante LEFT JOIN y, mediante WHERE, se filtra para que se muestren
// solo los días a partir de hoy. ELT(DAYOFWEEK(CURDATE())) se utiliza para obtener el día de la semana 
// correspondiente a
//  la fecha actual. CURDATE() representa la fecha actual, y BETWEEN indica “entre lunes y viernes
//tambien se filta por estado perdiente y se limita el numero a 50 resultados

$resultado = $conexion->query($SQL); //pepara la conexion
//control de errores

if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}



foreach ($_POST as $key => $value) { //Recorre todos los datos enviados por el formulario ($_POST)
    // $key contiene el nombre del campo y $value su valor


    if (strpos($key, 'guardia') === 0) { // Verifica si el nombre del campo comienza con la palabra 'guardia'
        // Esto permite filtrar solo los campos relacionados con guardias

        $id_a = str_replace('guardia', '', $key); // Elimina la palabra 'guardia' del nombre del campo
        // Dejando solo el número que corresponde al ID de la ausencia

        $usuario = $_SESSION['dni']; // DNI del usuario con la seccion actual

        $update = "UPDATE ausencia SET estado = 'cubierta' WHERE id_a = $id_a"; // Construye la consulta SQL para actualizar el estado de 
        // la ausencia a 'cubierta'

        $conexion->query($update); // Ejecuta la consulta SQL utilizando la conexión a la base de datos

        //Guardar en JSON por usuario
        $file = __DIR__ . '/guardias.json';

        if (!file_exists($file)) {  // Si el archivo JSON no existe, se crea con un objeto vacío
            file_put_contents($file, '{}');
        }

        $data = json_decode(file_get_contents($file), true); //convierte en un array asociativo
        if (!is_array($data)) {
            $data = [];
        }

        if (!isset($data[$usuario])) { //Comprueba si en el array existe un registro para el DNI del usuario.
            $data[$usuario] = [];
        }

        $data[$usuario][$id_a] = true;//Marca si la guardia ha sido aceptada por ese usuario.

        file_put_contents($file, json_encode($data));//Guarda y actualiza la información en el archivo JSON.

        
        header("Location: pruebacalendario.php");//Redirige al usuario a la página pruebacalendario.php.
        exit();
    }
}



$file = __DIR__ . '/guardias.json';
$guardias = []; //Inicializa un array vacío para guardar las guardias del usuario.
$usuario = $_SESSION['dni']; //Obtiene el DNI del usuario que está logueado.

if (file_exists($file)) { //Verifica si el archivo guardias.json existe antes de intentar leerlo.
    $json = json_decode(file_get_contents($file), true); //Convierte el contenido del archivo JSON en un array asociativo de PHP.
    if (is_array($json) && isset($json[$usuario])) {
        $guardias = $json[$usuario];
    }
}
?>




?>
<!DOCTYPE html>
  <html>
        <head>
            <title>Calendario</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
            <?php include __DIR__ . '/interfaz/nav.php'; ?> 
            <nav>
             
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
            </main>
            <?php include __DIR__ . '/interfaz/footer.php'; ?>  
        </body>
    </html>                                                 