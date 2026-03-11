<?php

include __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../config/auth.php';


if (!isset($_SESSION['dni'])) {
    header('location: index.php');
    exit();
}

$dni = $_SESSION['dni'];

//  consulta sql perada
$sql = "SELECT 
            horario.aula,
            horario.modulo,
            usuarios.familia,
            horas.hora,
            horas.dia
        FROM horario
        LEFT JOIN horas ON horario.id_hora = horas.id_hora
        LEFT JOIN usuarios ON horario.dni_usuario = usuarios.dni

        WHERE dni_usuario = ?
        ORDER BY horario.id_hora";

$stmt = $conexion->prepare($sql);

//  Enlazar los parámetros
$stmt->bind_param("s", $dni);

//  Ejecutar la consulta
$stmt->execute();

// se obtiene los resultados
$resultado = $stmt->get_result();
 
$dias = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes"]; // se crea un array con los dias de clases
$horariodia = []; // se crea un array vacia para guarda las clase de los dias 
foreach ($dias as $d) {  // en cada vuelta del bucle, $d tiene un día diferente (Lunes, Martes, etc.)
    $horariodia[$d] = []; //se crea un array vacío para ese día
}

while ($fila = $resultado->fetch_assoc()) { // se obtiene cada fila de la consulta SQL 
    $horariodia[$fila['dia']][] = $fila; // se añade la $fila al  día correspondiente
}

?>

<!DOCTYPE html>
    <html>
        <head>
        <link rel="stylesheet" href="/css/style.css">

        <title>Mi Horario</title>

        </head>
        <body>
            <nav>
                <?php include __DIR__ . '/../interfaz/nav.php'; ?> 
            </nav>

            <main>
                <h2>Mi Horario</h2>

                <?php foreach ($dias as $dia): ?> <!-- sirve para que salga el nombre del día -->
                    <h3><?php echo $dia; ?></h3>

                    <table border="1">
                        <tr>
                            <th>Hora</th>
                            <th>Familia</th>
                            <th>Móduolo</th>
                            <th>Aula</th>
                            
                        </tr>

                        <?php
                        if (empty($horariodia[$dia])) { ?>
                            <tr>
                                <td colspan="4">No hay datos esta semana</td>
                            </tr>
                        <?php 
                        } else {
                            foreach ($horariodia[$dia] as $fila) {  
                                echo "<tr>";
                                echo "<td>" . $fila['hora'] . "</td>"; 
                                echo "<td>" . $fila['familia'] . "</td>";
                                echo "<td>" . $fila['modulo'] . "</td>";
                                echo "<td>" . $fila['aula'] . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>   
                    </table>

                <?php endforeach; ?>

            </main>   
            <footer>
              <?php include __DIR__ . '/../interfaz/footer.php'; ?>  
            </footer>
        </body>  
    </html>

<?php
$stmt->close();  
$conexion->close(); 
?>