<?php 
    session_start();

    require_once __DIR__ . '/../php/añadirhorario.php'; 

    if (!$conexion) {
    die("Conexion fallida: " . mysqli_connect_error());
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Añadir horarios</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
     <?php include __DIR__ . '/../interfaz/nav.php';?>
    <main>
        <form action="/php/añadirhorario.php" method="POST">
            <label for="L1">Lunes 1ª</label><br>
                <input type="text" name="modulol1" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulal1" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="L2">Lunes 2ª</label><br>
                <input type="text" name="modulol2" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulal2" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="L3">Lunes 3ª</label><br>
                <input type="text" name="modulol3" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulal3" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="L4">Lunes 4ª</label><br>
                <input type="text" name="modulol4" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulal4" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="L5">Lunes 5ª</label><br>
                <input type="text" name="modulol5" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulal5" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="L6">Lunes 6ª</label><br>
                <input type="text" name="modulol6" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulal6" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="M1">Martes 1ª</label><br>
                <input type="text" name="modulom1" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>  
                <input type="text" name="aulam1" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="M2">Martes 2ª</label><br>
                <input type="text" name="modulom2" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulam2" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="M3">Martes 3ª</label><br>
                <input type="text" name="modulom3" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulam3" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="M4">Martes 4ª</label><br>
                <input type="text" name="modulom4" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulam4" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="M5">Martes 5ª</label><br>
                <input type="text" name="modulom5" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulam5" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="M6">Martes 6ª</label><br>
                <input type="text" name="modulom6" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulam6" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="X1">Miercoles 1ª</label><br>
                <input type="text" name="modulox1" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulax1" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="X2">Miercoles 2ª</label><br>
                <input type="text" name="modulox2" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulax2" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="X3">Miercoles 3ª</label><br>
                <input type="text" name="modulox3" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulax3" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="X4">Miercoles 4ª</label><br>
                <input type="text" name="modulox4" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulax4" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="X5">Miercoles 5ª</label><br>
                <input type="text" name="modulox5" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulax5" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="X6">Miercoles 6ª</label><br>
                <input type="text" name="modulox6" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulax6" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="J1">Jueves 1ª</label><br>
                <input type="text" name="moduloj1" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulaj1" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="J2">Jueves 2ª</label><br>
                <input type="text" name="moduloj2" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulaj2" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="J3">Jueves 3ª</label><br>
                <input type="text" name="moduloj3" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulaj3" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="J4">jueves 4ª</label><br>
                <input type="text" name="moduloj4" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulaj4" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="J5">Jueves 5ª</label><br>
                <input type="text" name="moduloj5" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulaj5" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="J6">Jueves 6ª</label><br>
                <input type="text" name="moduloj6" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulaj6" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="V1">Viernes 1ª</label><br>
                <input type="text" name="modulov1" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulav1" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="V2">Viernes 2ª</label><br>
                <input type="text" name="modulov2" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulav2" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="V3">Viernes 3ª</label><br>
                <input type="text" name="modulov3" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulav3" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="V4">Viernes 4ª</label><br>
                <input type="text" name="modulov4" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulav4" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="V5">Viernes 5ª</label><br>
                <input type="text" name="modulov5" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulav5" class="aula" placeholder="Escribe el aula aquí"><br>

            <label for="V6">Viernes 6ª</label><br>
                <input type="text" name="modulov6" class="modulo" placeholder="Escribe el modulo aquí, si tienes guardia escribelo aquí" required><br>
                <input type="text" name="aulav6" class="aula" placeholder="Escribe el aula aquí"><br>

            <input type="submit" value="Guardar horario">
        </form>
    </main>
    <?php include __DIR__ . '/../interfaz/footer.php';?>
</body>
</html>