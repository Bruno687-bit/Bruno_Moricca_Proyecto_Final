<?php
// =========================================================
// panel.php - Panel personal (solo con sesión iniciada)
// =========================================================
require_once "includes/funciones.php";
requerir_login();

// Traemos los datos actualizados desde la base (nunca la contraseña)
$sql = $pdo->prepare("SELECT nombre, apellido, dni, email, rol, fecha_creacion, fecha_archivado FROM usuarios WHERE id = ?");
$sql->execute([$_SESSION["usuario_id"]]);
$yo = $sql->fetch();

// Si el usuario fue archivado o borrado mientras estaba conectado, cerramos la sesión
if (!$yo || $yo["fecha_archivado"] !== null) {
    header("Location: cerrar_sesion.php");
    exit;
}

$titulo = "Mi cuenta";
require "includes/header.php";
?>
  <section class="contenedor-form">
    <h1>¡Hola, <?= e($yo["nombre"]) ?>!</h1>
    <p>Bienvenido/a a tu panel personal.</p>
    <?php mostrar_mensajes(); ?>

    <fieldset>
      <legend>Mis datos</legend>
      <table class="tabla datos">
        <tr><th>Nombre</th><td><?= e($yo["nombre"]) ?></td></tr>
        <tr><th>Apellido</th><td><?= e($yo["apellido"]) ?></td></tr>
        <tr><th>DNI</th><td><?= e($yo["dni"]) ?></td></tr>
        <tr><th>Correo</th><td><?= e($yo["email"]) ?></td></tr>
        <tr><th>Rol</th><td><?= e($yo["rol"]) ?></td></tr>
        <tr><th>Socio/a desde</th><td><?= e(date("d/m/Y", strtotime($yo["fecha_creacion"]))) ?></td></tr>
      </table>
    </fieldset>

    <div class="botones">
      <?php if ($yo["rol"] === "admin"): ?>
        <a href="admin_usuarios.php" class="boton">Ir al panel de administración</a>
      <?php endif; ?>
      <a href="cerrar_sesion.php" class="boton secundario">Cerrar sesión</a>
    </div>
  </section>
<?php require "includes/footer.php"; ?>
