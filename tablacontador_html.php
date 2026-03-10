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