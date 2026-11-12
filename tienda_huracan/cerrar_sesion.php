<?php
// =========================================================
// cerrar_sesion.php - Cierra la sesión y vuelve al login
// =========================================================
session_start();

$_SESSION = [];                 // vaciamos las variables de sesión
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), "", time() - 3600, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}
session_destroy();              // destruimos la sesión en el servidor

// Avisamos al login por la URL para que muestre el mensaje
header("Location: login.php?salida=1");
exit;
