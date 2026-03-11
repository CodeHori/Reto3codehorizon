<?php
// Nav include usado por las páginas principales
?>
<nav class="nav-principal" role="navigation">
    <div class="contenedor-nav">
        <div class="nav-left">
            <ul>
                <li><a href="/php/home.php">Inicio</a></li>
                <li><a href="/html/pruebacalendario_html.php">Calendario</a></li>
                <li><a href="/html/solicitud_html.php">Solicitud de ausencias</a></li>
                <li><a href="/html/tablacontador_html.php">Contador</a></li>
                <li><a href="/html/historialguardias_html.php">Historial de guardias</a></li>
                <li><a href="/html/añadirhorario_html.php">Añadir horario</a></li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li><a href="/php/paneladmin.php">Panel de administración</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="nav-right">
            <ul>
                <li><a href="/config/logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>