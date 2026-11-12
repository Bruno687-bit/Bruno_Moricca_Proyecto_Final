<?php
// =========================================================
// usuarios_archivados.php - Listado de cuentas archivadas
// =========================================================
require_once "includes/funciones.php";
requerir_admin();

$buscar = trim($_GET["buscar"] ?? "");

if ($buscar !== "") {
    $like = "%" . $buscar . "%";
    $sql = $pdo->prepare("SELECT id, nombre, apellido, dni, email, rol, fecha_archivado
                          FROM usuarios
                          WHERE fecha_archivado IS NOT NULL
                            AND (nombre LIKE ? OR apellido LIKE ? OR dni LIKE ? OR email LIKE ?)
                          ORDER BY fecha_archivado DESC");
    $sql->execute([$like, $like, $like, $like]);
} else {
    $sql = $pdo->query("SELECT id, nombre, apellido, dni, email, rol, fecha_archivado
                        FROM usuarios WHERE fecha_archivado IS NOT NULL ORDER BY fecha_archivado DESC");
}
$archivados = $sql->fetchAll();

$titulo = "Usuarios archivados";
require "includes/header.php";
?>
  <section class="admin">
    <h1>Usuarios archivados</h1>
    <?php mostrar_mensajes(); ?>

    <div class="admin-menu">
      <a href="admin_usuarios.php" class="boton secundario">&larr; Usuarios activos</a>
    </div>

    <form action="usuarios_archivados.php" method="GET" class="buscador">
      <input type="text" name="buscar" placeholder="Buscar entre archivados..." value="<?= e($buscar) ?>">
      <button type="submit">Buscar</button>
    </form>

    <div class="tabla-scroll">
      <table class="tabla">
        <thead>
          <tr><th>Nombre</th><th>Apellido</th><th>DNI</th><th>Correo</th><th>Rol</th><th>Archivado el</th><th>Acción</th></tr>
        </thead>
        <tbody>
        <?php foreach ($archivados as $u): ?>
          <tr>
            <td><?= e($u["nombre"]) ?></td>
            <td><?= e($u["apellido"]) ?></td>
            <td><?= e($u["dni"]) ?></td>
            <td><?= e($u["email"]) ?></td>
            <td><?= e($u["rol"]) ?></td>
            <td><?= e(date("d/m/Y H:i", strtotime($u["fecha_archivado"]))) ?></td>
            <td>
              <form action="reactivar_usuario.php" method="POST">
                <?= campo_csrf() ?>
                <input type="hidden" name="id" value="<?= (int)$u["id"] ?>">
                <button type="submit" class="chico">Reactivar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($archivados)): ?>
          <tr><td colspan="7" class="vacio">No hay usuarios archivados.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php require "includes/footer.php"; ?>
