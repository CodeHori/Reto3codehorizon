<?php
    require __DIR__ . '/../config/conexionbd.php';
    require __DIR__ . '/../config/auth.php';

    if (!$conexion) {
    die("Conexion fallida: " . mysqli_connect_error());
}

$dni = $_SESSION['dni'];
if(!$dni) {
    die('no se ha podido obtener el dni del usuario');
}

if($conexion->connect_error) {
    echo "No hay conexión : (" . $conexion->connect_error . ")";
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
// LUNES
$ml1 = $_POST['modulol1'];
$al1 = $_POST['aulal1'];

$ml2 = $_POST['modulol2'];
$al2 = $_POST['aulal2'];

$ml3 = $_POST['modulol3'];
$al3 = $_POST['aulal3'];

$ml4 = $_POST['modulol4'];
$al4 = $_POST['aulal4'];

$ml5 = $_POST['modulol5'];
$al5 = $_POST['aulal5'];

$ml6 = $_POST['modulol6'];
$al6 = $_POST['aulal6'];
// MARTES
$mm1 = $_POST['modulom1'];
$am1 = $_POST['aulam1'];

$mm2 = $_POST['modulom2'];
$am2 = $_POST['aulam2'];

$mm3 = $_POST['modulom3'];
$am3 = $_POST['aulam3'];

$mm4 = $_POST['modulom4'];
$am4 = $_POST['aulam4'];

$mm5 = $_POST['modulom5'];
$am5 = $_POST['aulam5'];

$mm6 = $_POST['modulom6'];
$am6 = $_POST['aulam6'];
// MIERCOLES 
$mx1 = $_POST['modulox1'];
$ax1 = $_POST['aulax1'];

$mx2 = $_POST['modulox2'];
$ax2 = $_POST['aulax2'];

$mx3 = $_POST['modulox3'];
$ax3 = $_POST['aulax3'];

$mx4 = $_POST['modulox4'];
$ax4 = $_POST['aulax4'];

$mx5 = $_POST['modulox5'];
$ax5 = $_POST['aulax5'];

$mx6 = $_POST['modulox6'];
$ax6 = $_POST['aulax6'];
// JUEVES
$mj1 = $_POST['moduloj1'];
$aj1 = $_POST['aulaj1'];

$mj2 = $_POST['moduloj2'];
$aj2 = $_POST['aulaj2'];

$mj3 = $_POST['moduloj3'];
$aj3 = $_POST['aulaj3'];

$mj4 = $_POST['moduloj4'];
$aj4 = $_POST['aulaj4'];

$mj5 = $_POST['moduloj5'];
$aj5 = $_POST['aulaj5'];

$mj6 = $_POST['moduloj6'];
$aj6 = $_POST['aulaj6'];
// VIERNES
$mv1 = $_POST['modulov1'];
$av1 = $_POST['aulav1'];

$mv2 = $_POST['modulov2'];
$av2 = $_POST['aulav2'];

$mv3 = $_POST['modulov3'];
$av3 = $_POST['aulav3'];

$mv4 = $_POST['modulov4'];
$av4 = $_POST['aulav4'];

$mv5 = $_POST['modulov5'];
$av5 = $_POST['aulav5'];

$mv6 = $_POST['modulov6'];
$av6 = $_POST['aulav6'];

    $queryregistrar = "INSERT INTO horario 
    (modulo, aula, id_hora, dni_usuario)
    VALUES 
   
    ('$ml1', '$al1', 'L1', '$dni'),
     ('$ml2', '$al2', 'L2', '$dni'),
    ('$ml3', '$al3', 'L3', '$dni'),
    ('$ml4', '$al4', 'L4', '$dni'),
    ('$ml5', '$al5', 'L5', '$dni'),
    ('$ml6', '$al6', 'L6', '$dni'),
    
    ('$mm1', '$am1', 'M1', '$dni'),
    ('$mm2', '$am2', 'M2', '$dni'),
    ('$mm3', '$am3', 'M3', '$dni'),
    ('$mm4', '$am4', 'M4', '$dni'),
    ('$mm5', '$am5', 'M5', '$dni'),
    ('$mm6', '$am6', 'M6', '$dni'),
    
    ('$mx1', '$ax1', 'X1', '$dni'),
    ('$mx2', '$ax2', 'X2', '$dni'),
    ('$mx3', '$ax3', 'X3', '$dni'),
    ('$mx4', '$ax4', 'X4', '$dni'),
    ('$mx5', '$ax5', 'X5', '$dni'),
    ('$mx6', '$ax6', 'X6', '$dni'),
   
    ('$mj1', '$aj1', 'J1', '$dni'),
    ('$mj2', '$aj2', 'J2', '$dni'),
    ('$mj3', '$aj3', 'J3', '$dni'),
    ('$mj4', '$aj4', 'J4', '$dni'),
    ('$mj5', '$aj5', 'J5', '$dni'),
    ('$mj6', '$aj6', 'J6', '$dni'),
    
    ('$mv1', '$av1', 'V1', '$dni'),
    ('$mv2', '$av2', 'V2', '$dni'),
    ('$mv3', '$av3', 'V3', '$dni'),
    ('$mv4', '$av4', 'V4', '$dni'),
    ('$mv5', '$av5', 'V5', '$dni'),
    ('$mv6', '$av6', 'V6', '$dni')
   ";


    if(mysqli_query($conexion, $queryregistrar)) {
       echo "<script>alert('Horario implementado correctamente');window.location='/../php/home.php'</script>";
    } else {
        echo "Error en SQL: " . mysqli_error($conexion);
    }
}
?>