<?php
// Nav include usado por las páginas principales
?>
<nav class="nav-principal" role="navigation">
    <div class="contenedor-nav">
        <div class="nav-left">
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="pruebacalendario.php">Calendario</a></li>
                <li><a href="solicitud.php">Solicitud de ausencias</a></li>
                <li><a href="tablacontador.php">Contador</a></li>
                <li><a href="historialguardias.php">Historial de guardias</a></li>
                <li><a href="añadirhorariohtml.php">Añadir horario</a></li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li><a href="paneladmin.php">Panel de administración</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="nav-right">
            <ul>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>