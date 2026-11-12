<?php
// =========================================================
// reactivar_usuario.php - Vuelve a activar una cuenta archivada
// Solo acepta POST + token CSRF + rol admin
// =========================================================
require_once "includes/funciones.php";
requerir_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: usuarios_archivados.php");
    exit;
}
verificar_csrf();

$id = (int)($_POST["id"] ?? 0);

// Para reactivar, volvemos la fecha de archivado a NULL
$sql = $pdo->prepare("UPDATE usuarios SET fecha_archivado = NULL WHERE id = ? AND fecha_archivado IS NOT NULL");
$sql->execute([$id]);

if ($sql->rowCount() === 1) {
    $_SESSION["mensaje_ok"] = "Usuario reactivado. Ya puede volver a iniciar sesión.";
} else {
    $_SESSION["mensaje_error"] = "No se pudo reactivar: el usuario no existe o ya está activo.";
}

header("Location: usuarios_archivados.php");
exit;
