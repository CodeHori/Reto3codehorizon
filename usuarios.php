<?php
// BLOQUE 1: INCLUIR ARCHIVOS NECESARIOS PARA QUE LA PÁGINA FUNCIONE
// auth.php verifica que el usuario esté logueado correctamente (sin login, no se puede acceder)
// conexionbd.php establece la conexión con la base de datos de ausencias (necesaria para todas las operaciones)
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexionbd.php';

// BLOQUE 2: VERIFICAR QUE SOLO ADMINISTRADORES PUEDAN ACCEDER A ESTA PÁGINA
// Se obtiene el rol guardado en la sesión del usuario. Si el usuario no es admin, se redirige con error
// Esto previene que usuarios normales modifiquen, creen o eliminen otros usuarios
$rol_usuario = '';
if (isset($_SESSION['rol'])) {
    $rol_usuario = $_SESSION['rol'];
}
if ($rol_usuario !== 'admin') {
    $_SESSION['mensaje'] = 'Solo administradores pueden gestionar usuarios.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: home.php');
    exit();
}

// BLOQUE 3: DETECTAR EL NOMBRE CORRECTO DE LA COLUMNA DE CONTRASEÑA EN LA BASE DE DATOS
// La base de datos puede tener la columna nombrada 'contraseña' o 'contraseÃ±a' (problema de codificación)
// Se consulta la estructura de la tabla usuarios y se verifica qué columnas existen
// Luego se asigna el nombre correcto a la variable $columna_contraseña para usarla en las consultas SQL
$columna_contraseña = '`contraseña`';
$resultado_columnas = $conexion->query('SHOW COLUMNS FROM usuarios');
if ($resultado_columnas) {
    $columnas = [];
    while ($fila_col = $resultado_columnas->fetch_assoc()) {
        $columnas[] = $fila_col['Field'];
    }
    $resultado_columnas->free();

    // BLOQUE 3.1: VERIFICAR CUÁL ES EL NOMBRE CORRECTO DE LA COLUMNA Y ASIGNARLO
    // Se recorren todas las columnas y se verifica si existe 'contraseña' o 'contraseÃ±a'
    // Se asigna el nombre correcto con backticks (`) para evitar problemas si el nombre tiene caracteres especiales
    if (in_array('contraseña', $columnas, true)) {
        $columna_contraseña = '`contraseña`';
    } elseif (in_array('contraseÃ±a', $columnas, true)) {
        $columna_contraseña = '`contraseÃ±a`';
    }
}

// BLOQUE 4: FUNCIÓN PARA GUARDAR MENSAJES Y REDIRIGIR
// Esta función reutilizable guarda un mensaje en la sesión, lo que permite mostrar información al usuario
// después de redirigir. Si hay mensaje, se guarda junto con su tipo (éxito, error, etc.)
// Luego redirige a usuarios.php para que la página se recargue y muestre el mensaje
function volver_usuarios(string $mensaje = '', string $tipo = 'info'): void
{
    if ($mensaje !== '') {
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = $tipo;
    }
    header('Location: usuarios.php');
    exit();
}
// BLOQUE 5: PROCESAR FORMULARIO CUANDO SE CREA O EDITA UN USUARIO 
// Se obtienen todos los datos enviados desde el formulario (DNI, nombre, apellido, etc.)
// Cada dato se obtiene con if-else: si existe en $_POST, se usa; si no, se asigna un valor por defecto
// Los datos se validan: si faltan campos obligatorios, se detiene el proceso y se muestra error
// BLOQUE 5: PROCESAR FORMULARIO CUANDO EL USUARIO GUARDA UN USUARIO (CREAR O EDITAR)
// Se obtienen todos los datos enviados desde el formulario (DNI, nombre, apellido, etc.)
// Cada dato se obtiene con if-else: si existe en $_POST, se usa; si no, se asigna un valor por defecto
// Los datos se validan: si faltan campos obligatorios, se detiene el proceso y se muestra error
if (isset($_POST['guardar'])) {
    // BLOQUE 5.1: OBTENER CADA DATO DEL FORMULARIO
    // Se obtiene el valor de cada campo enviado desde el formulario
    // Si el campo no existe en $_POST, se asigna un valor por defecto (string vacío o 'usuario' para rol)
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
    } else {
        $accion = 'crear';
    }
    
    if (isset($_POST['dni'])) {
        $dni = trim($_POST['dni']);
    } else {
        $dni = '';
    }
    
    if (isset($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);
    } else {
        $nombre = '';
    }
    
    if (isset($_POST['apellido'])) {
        $apellido = trim($_POST['apellido']);
    } else {
        $apellido = '';
    }
    
    if (isset($_POST['correo_electronico'])) {
        $correo = trim($_POST['correo_electronico']);
    } else {
        $correo = '';
    }
    
    if (isset($_POST['familia'])) {
        $familia = trim($_POST['familia']);
    } else {
        $familia = '';
    }
    
    if (isset($_POST['rol'])) {
        $rol = trim($_POST['rol']);
    } else {
        $rol = 'usuario';
    }
    
    if (isset($_POST['contraseña'])) {
        $contraseña_texto = $_POST['contraseña'];
    } else {
        $contraseña_texto = '';
    }
    // Validación DNI: debe tener 9 caracteres
    if (strlen($dni) !== 9) {
        volver_usuarios('El DNI debe tener exactamente 9 caracteres', 'error');
    }

    // Validación: el DNI debe tener 8 números más una letra en mayúscula
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
        volver_usuarios('El DNI debe tener 8 números y una letra en mayúscula', 'error');
    }

    // Validación: familia solo debe tener letras en minúsculas
    if ($familia !== '' && !preg_match('/^[a-záéíóú]+$/', $familia)) {
        volver_usuarios('El campo familia solo puede contener letras en minúsculas', 'error');
    }

    // Validación: nombre solo letras y mínimo 3 caracteres
    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]{3,}$/', $nombre)) {
        volver_usuarios('El nombre solo puede contener letras y debe tener al menos 3 caracteres', 'error');
    }

    // Validación: apellido solo letras y mínimo 3 caracteres
    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]{3,}$/', $apellido)) {
        volver_usuarios('El apellido solo puede contener letras y debe tener al menos 3 caracteres', 'error');
    }

    // BLOQUE 5.2: VALIDAR QUE TODOS LOS CAMPOS OBLIGATORIOS ESTÉN LLENOS
    // Si alguno de los campos principales está vacío después de obtener los datos, se muestra error
    if ($dni === '' || $nombre === '' || $apellido === '' || $correo === '') {
        volver_usuarios('Faltan campos obligatorios.', 'error');
    }

    // BLOQUE 5.3: MODO EDICIÓN - ACTUALIZAR USUARIO EXISTENTE
    // Si la acción es 'editar', se ejecuta una consulta UPDATE para modificar los datos del usuario
    // Se verifica si se envió una nueva contraseña: si sí, se actualiza también; si no, se mantiene la anterior
    if ($accion === 'editar') {
        if ($contraseña_texto !== '') {
            // Si se proporciona nueva contraseña, se hashea y se incluye en la actualización
            // password_hash() convierte la contraseña en un hash seguro que se guarda en BD
            $hash = password_hash($contraseña_texto, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios
                    SET nombre = ?, apellido = ?, correo_electronico = ?, $columna_contraseña = ?, familia = ?, rol = ?
                    WHERE dni = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sssssss', $nombre, $apellido, $correo, $hash, $familia, $rol, $dni);
        } else {
            // Si no hay nueva contraseña, se actualiza solo los otros datos sin modificar la contraseña actual
            $sql = "UPDATE usuarios
                    SET nombre = ?, apellido = ?, correo_electronico = ?, familia = ?, rol = ?
                    WHERE dni = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('ssssss', $nombre, $apellido, $correo, $familia, $rol, $dni);
        }

        if (!$stmt || !$stmt->execute()) {
            volver_usuarios('No se pudo actualizar el usuario.', 'error');
        }

        volver_usuarios('Usuario actualizado correctamente.', 'exito');
    }

    // BLOQUE 5.4: MODO CREACIÓN - CREAR NUEVO USUARIO
    // Si la contraseña está vacía en modo creación, se muestra error (es obligatoria)
    // Si los datos son válidos, se inserta el nuevo usuario en la base de datos
    if ($contraseña_texto === '') {
        volver_usuarios('La contraseña es obligatoria para crear.', 'error');
    }

    $hash = password_hash($contraseña_texto, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (dni, nombre, apellido, correo_electronico, $columna_contraseña, familia, rol)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        volver_usuarios('No se pudo preparar la insercion.', 'error');
    }

    $stmt->bind_param('sssssss', $dni, $nombre, $apellido, $correo, $hash, $familia, $rol);
    if (!$stmt->execute()) {
        volver_usuarios('No se pudo crear el usuario. Revisa si el DNI ya existe.', 'error');
    }

    volver_usuarios('Usuario creado correctamente.', 'exito');
}

// BLOQUE 6: PROCESAR ELIMINACIÓN DE USUARIO
// Cuando el usuario hace clic en "Eliminar", se obtiene el DNI desde la URL (parámetro GET)
// Se verifica que no sea el usuario actual (protección para evitar auto-eliminarse)
// Se ejecuta DELETE de la base de datos para remover al usuario
if (isset($_GET['eliminar'])) {
    $dni_eliminar = trim($_GET['eliminar']);

    if ($dni_eliminar === ($_SESSION['dni'])) {
        volver_usuarios('No puedes eliminar tu propio usuario.', 'error');
    }

    $stmt = $conexion->prepare('DELETE FROM usuarios WHERE dni = ?');
    $stmt->bind_param('s', $dni_eliminar);
    if (!$stmt->execute()) {
        volver_usuarios('No se pudo eliminar el usuario.', 'error');
    }

    volver_usuarios('Usuario eliminado correctamente.', 'exito');
}

// BLOQUE 7: PREPARAR LOS DATOS DEL USUARIO A EDITAR
// Si el usuario hace clic en "Editar", se obtiene el DNI desde la URL
// Se consulta la base de datos para obtener todos los datos actuales del usuario
// Estos datos se cargan en el formulario para que el usuario vea qué está editando
$editar = false;
$usuario_edicion = [
    'dni' => '',
    'nombre' => '',
    'apellido' => '',
    'correo_electronico' => '',
    'familia' => '',
    'rol' => 'usuario',
];

if (isset($_GET['editar'])) {
    $editar = true;
    $dni_editar = trim($_GET['editar']);
    $stmt = $conexion->prepare('SELECT dni, nombre, apellido, correo_electronico, familia, rol FROM usuarios WHERE dni = ?');
    $stmt->bind_param('s', $dni_editar);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    if ($fila) {
        $usuario_edicion = $fila;
    } else {
        $editar = false;
    }
    $stmt->close();
}

// BLOQUE 8: OBTENER LA LISTA COMPLETA DE USUARIOS PARA MOSTRAR EN LA TABLA
// Se consulta la base de datos para obtener todos los usuarios ordenados por apellido y nombre
// Esta lista se mostrará en una tabla HTML para que el admin pueda ver, editar o eliminar usuarios
$listado = $conexion->query('SELECT dni, nombre, apellido, correo_electronico, familia, rol FROM usuarios ORDER BY apellido, nombre');

// BLOQUE 9: OBTENER MENSAJES DE LA SESIÓN PARA MOSTRAR AL USUARIO
// Se obtienen los mensajes guardados en la sesión (error, éxito, etc.) desde operaciones anteriores
// Se determina la clase CSS a usar según el tipo de mensaje (verde para éxito, rojo para error)
// Luego se eliminan los mensajes de la sesión para que no se repitan en la próxima carga
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
} else {
    $mensaje = '';
}

if (isset($_SESSION['tipo_mensaje'])) {
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
} else {
    $tipo_mensaje = 'info';
}

unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

if ($tipo_mensaje === 'exito') {
    $clase_mensaje = 'mensaje mensaje-exito';
} else {
    $clase_mensaje = 'mensaje mensaje-error';
}
?>