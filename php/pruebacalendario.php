<?php
require_once '../config/auth.php';


require_once '../config/conexionbd.php';




$SQL = "SELECT
    ausencia.id_a,
  horario.id_horario,
  horario.aula,
  horario.modulo,
  horario.id_hora,
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
  LEFT JOIN horas ON horario.id_hora = horas.id_hora
  WHERE LEFT(horario.id_hora, 1) =
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
        $file = __DIR__ . '/../guardias.json';

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

        
        header("Location: /html/pruebacalendario_html.php");//Redirige al usuario a la página pruebacalendario.php.
        exit();
    }
}

$file = '../guardias.json'; // Ruta al archivo JSON que almacena las guardias por usuario
$guardias = []; //Inicializa un array vacío para guardar las guardias del usuario.
$usuario = $_SESSION['dni']; //Obtiene el DNI del usuario que está logueado.

if (file_exists($file)) { //Verifica si el archivo guardias.json existe antes de intentar leerlo.
    $json = json_decode(file_get_contents($file), true); //Convierte el contenido del archivo JSON en un array asociativo de PHP.
    if (is_array($json) && isset($json[$usuario])) {
        $guardias = $json[$usuario];
    }
}
?>
