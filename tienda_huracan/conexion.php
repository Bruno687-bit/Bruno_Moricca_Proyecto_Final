<?php
// =========================================================
// conexion.php - Conexión a MySQL usando PDO
// =========================================================

$host     = "localhost";
$base     = "tienda_huracan";
$usuario  = "root";   // usuario por defecto de XAMPP
$clave    = "";       // en XAMPP la clave de root viene vacía

try {
    $pdo = new PDO("mysql:host=$host;dbname=$base;charset=utf8mb4", $usuario, $clave);

    // Si hay un error en una consulta, PDO lanza una excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Los resultados vuelven como arreglos asociativos: $fila['nombre']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Usar consultas preparadas reales del motor MySQL
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // No mostramos el detalle del error al usuario (podría revelar datos del servidor)
    die("Error al conectar con la base de datos. Verificá que MySQL esté encendido en XAMPP.");
}
