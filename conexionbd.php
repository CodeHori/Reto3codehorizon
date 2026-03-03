<?php
// BLOQUE 1: CONFIGURACIÓN DE LA CONEXIÓN A LA BASE DE DATOS
// Definimos las variables con los datos de conexión al servidor MySQL
// $host: Dirección del servidor de BD (localhost o IP)
// $usuario: Usuario de MySQL (por defecto 'root' en desarrollo)
// $contrasena: Contraseña del usuario (vacía por defecto en XAMPP)
// $base_datos: Nombre de la base de datos a usar
$host = '127.0.0.1';
$usuario = 'Codehorizon';
$contrasena = 'Codehorizon1234/_';
$base_datos = 'ausencias_cpifp';

// BLOQUE 2: CREAR LA CONEXIÓN A LA BASE DE DATOS
// new mysqli(...): Crea un objeto de conexión MySQLi con los parámetros anteriores
// Este objeto se usará para ejecutar consultas en otros archivos
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

// BLOQUE 3: VERIFICAR SI LA CONEXIÓN FALLÓ
// $conexion->connect_error: Propiedad que contiene el mensaje de error si falla la conexión
// if (...): Si hay error, mostramos el mensaje y detenemos el script
// die(...): Muestra el error y termina la ejecución (útil en desarrollo)
if ($conexion->connect_error) {
    die('Conexion fallida: ' . $conexion->connect_error);
}

