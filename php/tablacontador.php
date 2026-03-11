<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexionbd.php';

if (!isset($_SESSION['dni'])) {
    header('location: index.php');
    exit();
}

/* 
   FUNCIÓN: CONTAR GUARDIAS POR USUARIO DESDE JSON
    */
function funcionContadorJSON($dni = null) { 
    global $conexion;

    // Cargar JSON
    $file = __DIR__ . '/../guardias.json';
    $guardias = [];
    //La función sirve: si la variable $file existe y contiene un JSON decodificado, entonces guarda los datos en $guardias.
    if (file_exists($file)) {
        $guardias = json_decode(file_get_contents($file), true);
        if (!is_array($guardias)) {
            $guardias = [];
        }
    }

    // Consulta usuarios y filta mediante el where
    // consulta prepada  //
    if ($dni === null) {
        // Si NO se filtra por DNI
        $stmt = $conexion->prepare("SELECT correo_electronico, nombre, apellido, dni FROM usuarios");
    } else {
        // Si SÍ se filtra por DNI
        $stmt = $conexion->prepare("SELECT correo_electronico, nombre, apellido, dni FROM usuarios WHERE dni = ?");
        $stmt->bind_param("s", $dni); // "s" = string
    }

    //pepara la conexion
    $stmt->execute();
    $resultado = $stmt->get_result();

    //mensaje error
    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $usuarios_array = []; // Inicializa el array de usuarios

    while ($fila = $resultado->fetch_assoc()) { //
        $dni_usuario = $fila['dni'];

        // Contar guardias del usuario
        // Si hay guardias registradas para ese usuario, las cuántas; si no, asigna 0.
        if (isset($guardias[$dni_usuario])) {
            $fila['total_guardias'] = count($guardias[$dni_usuario]);
        } else {
            $fila['total_guardias'] = 0;
        }

        $usuarios_array[] = $fila; // Agrega la fila del usuario al array final
    }

    $resultado->free(); //libera la conexion
    $stmt->close(); // cerrar consulta preparada
    return $usuarios_array; // Devuelve el array
}

/* llama la funcion*/
$usuarios = funcionContadorJSON(null);

if (!$usuarios) {
    die("Error en la consulta: " . $conexion->error);
}

?>