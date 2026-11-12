<?php
// =========================================================
// admin_usuarios.php - Listado y búsqueda de usuarios ACTIVOS
// =========================================================
require_once "includes/funciones.php";
requerir_admin();

$buscar = trim($_GET["buscar"] ?? "");

if ($buscar !== "") {
    // Búsqueda por nombre, apellido, DNI o correo (con LIKE y parámetros)
    $like = "%" . $buscar . "%";
    $sql = $pdo->prepare("SELECT id, nombre, apellido, dni, email, rol, fecha_creacion
                          FROM usuarios
                          WHERE fecha_archivado IS NULL
                            AND (nombre LIKE ? OR apellido LIKE ? OR dni LIKE ? OR email LIKE ?
                                 OR CONCAT(nombre, ' ', apellido) LIKE ?)
                          ORDER BY apellido, nombre");
    $sql->execute([$like, $like, $like, $like, $like]);
} else {
    $sql = $pdo->query("SELECT id, nombre, apellido, dni, email, rol, fecha_creacion
                        FROM usuarios WHERE fecha_archivado IS NULL ORDER BY apellido, nombre");
}
$usuarios = $sql->fetchAll();

$titulo = "Administrar usuarios";
require "includes/header.php";
?>
  <section class="admin">
    <h1>Panel administrativo</h1>
    <?php mostrar_mensajes(); ?>

    <div class="admin-menu">
      <a href="admin_usuarios.php" class="boton">Usuarios activos</a>
      <a href="admin_nuevo_usuario.php" class="boton">+ Registrar usuario</a>
      <a href="usuarios_archivados.php" class="boton secundario">Usuarios archivados</a>
    </div>

    <form action="admin_usuarios.php" method="GET" class="buscador">
      <input type="text" name="buscar" placeholder="Buscar por nombre, apellido, DNI o correo..." value="<?= e($buscar) ?>">
      <button type="submit">Buscar</button>
      <?php if ($buscar !== ""): ?>
        <a href="admin_usuarios.php" class="boton secundario">Ver todos</a>
      <?php endif; ?>
    </form>

    <p class="nota"><?= count($usuarios) ?> usuario(s) encontrado(s)</p>

    <div class="tabla-scroll">
      <table class="tabla">
        <thead>
          <tr>
            <th>Nombre</th><th>Apellido</th><th>DNI</th><th>Correo</th><th>Rol</th><th>Alta</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
          <tr>
            <td><?= e($u["nombre"]) ?></td>
            <td><?= e($u["apellido"]) ?></td>
            <td><?= e($u["dni"]) ?></td>
            <td><?= e($u["email"]) ?></td>
            <td><span class="etiqueta <?= $u["rol"] === "admin" ? "admin" : "" ?>"><?= e($u["rol"]) ?></span></td>
            <td><?= e(date("d/m/Y", strtotime($u["fecha_creacion"]))) ?></td>
            <td class="acciones">
              <a href="editar_usuario.php?id=<?= (int)$u["id"] ?>" class="boton chico">Editar</a>
              <?php if ((int)$u["id"] !== (int)$_SESSION["usuario_id"]): ?>
                <!-- Archivar se hace por POST con token, nunca con un link GET -->
                <form action="archivar_usuario.php" method="POST" onsubmit="return confirm('¿Archivar a este usuario?');">
                  <?= campo_csrf() ?>
                  <input type="hidden" name="id" value="<?= (int)$u["id"] ?>">
                  <button type="submit" class="chico peligro">Archivar</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($usuarios)): ?>
          <tr><td colspan="7" class="vacio">No se encontraron usuarios.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php require "includes/footer.php"; ?>
