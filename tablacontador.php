<?php
session_start();

require_once __DIR__ . '/auth.php';
require_once "conexionbd.php";

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
    $file = __DIR__ . '/guardias.json';
    $guardias = [];
    //La función sirve: si la variable $file existe y contiene un JSON decodificado, entonces guarda los datos en $guardias.
    if (file_exists($file)) {
        $guardias = json_decode(file_get_contents($file), true);
        if (!is_array($guardias)) {
            $guardias = [];
        }
    }

    // Consulta usuarios y filta mediante el where
    $consulta = "SELECT correo_electronico, nombre, apellido, dni FROM usuarios";
    if ($dni !== null) {
        $consulta .= " WHERE dni = '$dni'";
    }
//pepara la conexion
    $resultado = $conexion->query($consulta);
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
    return $usuarios_array; // Devuelve el array
}

/* llama la funcion*/
$usuarios = funcionContadorJSON(null);

if (!$usuarios) {
    die("Error en la consulta: " . $conexion->error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Tabla Contador de Guardias</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include __DIR__ . '/interfaz/nav.php'; ?>

<main>
    <div class="contenedor">

        <div class="encabezado-centrado margen-abajo-30">
            <h1>CPIFP Bajo Aragón</h1>
            <h2>Tabla Contador de Guardias</h2>
            <p class="texto-sutil">Cantidad de guardias reclamadas por cada usuario</p>
        </div>

        <div class="table-wrapper">
            <table class="contador-guardias">
                <thead>
                    <tr>
                        <th>Correo</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Total de Guardias</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    if (empty($usuarios)) { 
                        echo "<tr><td colspan='4'>No hay datos disponibles</td></tr>";
                    } else {
                        foreach ($usuarios as $fila) { 
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($fila['correo_electronico']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['apellido']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['total_guardias']) . "</td>";
                            echo "</tr>";
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>

        <?php $conexion->close(); ?>

    </div>
</main>

<?php include __DIR__ . '/interfaz/footer.php'; ?>

</body>
</html>