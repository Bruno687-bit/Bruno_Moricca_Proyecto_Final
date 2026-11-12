<?php
// =========================================================
// archivar_usuario.php - Archiva una cuenta (NO la borra)
// Solo acepta POST + token CSRF + rol admin
// =========================================================
require_once "includes/funciones.php";
requerir_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_usuarios.php");
    exit;
}
verificar_csrf();

$id = (int)($_POST["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["mensaje_error"] = "Usuario no válido.";
} elseif ($id === (int)$_SESSION["usuario_id"]) {
    $_SESSION["mensaje_error"] = "No podés archivar tu propia cuenta.";
} else {
    // Guardamos la fecha de archivado: el registro sigue existiendo en la base
    $sql = $pdo->prepare("UPDATE usuarios SET fecha_archivado = NOW() WHERE id = ? AND fecha_archivado IS NULL");
    $sql->execute([$id]);

    if ($sql->rowCount() === 1) {
        $_SESSION["mensaje_ok"] = "Usuario archivado. Podés reactivarlo desde 'Usuarios archivados'.";
    } else {
        $_SESSION["mensaje_error"] = "El usuario no existe o ya estaba archivado.";
    }
}

header("Location: admin_usuarios.php");
exit;
