<?php
// =========================================================
// editar_usuario.php - Edición de datos de un usuario
// =========================================================
require_once "includes/funciones.php";
requerir_admin();

// El id puede venir por GET (al abrir) o por POST (al guardar)
$id = (int)($_POST["id"] ?? $_GET["id"] ?? 0);

$sql = $pdo->prepare("SELECT id, nombre, apellido, dni, email, rol FROM usuarios WHERE id = ? AND fecha_archivado IS NULL");
$sql->execute([$id]);
$usuario = $sql->fetch();

if (!$usuario) {
    $_SESSION["mensaje_error"] = "El usuario no existe o está archivado.";
    header("Location: admin_usuarios.php");
    exit;
}

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verificar_csrf();

    $usuario["nombre"]   = trim($_POST["nombre"] ?? "");
    $usuario["apellido"] = trim($_POST["apellido"] ?? "");
    $usuario["dni"]      = trim($_POST["dni"] ?? "");
    $usuario["email"]    = strtolower(trim($_POST["email"] ?? ""));
    $nuevo_rol           = $_POST["rol"] ?? "usuario";
    $nueva_clave         = $_POST["password"] ?? "";

    $errores = validar_usuario($usuario["nombre"], $usuario["apellido"], $usuario["dni"], $usuario["email"]);
    if (!in_array($nuevo_rol, ["usuario", "admin"], true)) $errores[] = "Rol no válido.";
    if ($nueva_clave !== "" && strlen($nueva_clave) < 8) $errores[] = "La nueva contraseña debe tener al menos 8 caracteres.";
    // Un admin no puede quitarse a sí mismo el rol (se quedaría sin acceso)
    if ($id === (int)$_SESSION["usuario_id"] && $nuevo_rol !== "admin") {
        $errores[] = "No podés quitarte tu propio rol de administrador.";
    }
    $usuario["rol"] = $nuevo_rol;

    if (empty($errores)) {
        // Revisamos duplicados excluyendo al propio usuario
        $errores = buscar_duplicados($pdo, $usuario["dni"], $usuario["email"], $id);
    }

    if (empty($errores)) {
        try {
            if ($nueva_clave !== "") {
                $upd = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=?, rol=?, password=? WHERE id=?");
                $upd->execute([$usuario["nombre"], $usuario["apellido"], $usuario["dni"], $usuario["email"], $usuario["rol"],
                               password_hash($nueva_clave, PASSWORD_DEFAULT), $id]);
            } else {
                $upd = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=?, rol=? WHERE id=?");
                $upd->execute([$usuario["nombre"], $usuario["apellido"], $usuario["dni"], $usuario["email"], $usuario["rol"], $id]);
            }
            $_SESSION["mensaje_ok"] = "Datos actualizados correctamente.";
            header("Location: admin_usuarios.php");
            exit;
        } catch (PDOException $ex) {
            $errores[] = "Ese DNI o correo ya pertenece a otra cuenta.";
        }
    }
}

$titulo = "Editar usuario";
require "includes/header.php";
?>
  <section class="contenedor-form">
    <h1>Editar usuario</h1>
    <p><a href="admin_usuarios.php">&larr; Volver al listado</a></p>

    <?php foreach ($errores as $err): ?>
      <p class="alerta error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <form action="editar_usuario.php" method="POST" novalidate>
      <?= campo_csrf() ?>
      <input type="hidden" name="id" value="<?= (int)$usuario["id"] ?>">
      <fieldset>
        <legend>Datos Personales</legend>
        <div>
          <label for="nombre">Nombre *</label>
          <input type="text" id="nombre" name="nombre" maxlength="60" value="<?= e($usuario["nombre"]) ?>" required>
        </div>
        <div>
          <label for="apellido">Apellido *</label>
          <input type="text" id="apellido" name="apellido" maxlength="60" value="<?= e($usuario["apellido"]) ?>" required>
        </div>
        <div>
          <label for="dni">DNI *</label>
          <input type="text" id="dni" name="dni" inputmode="numeric" maxlength="8" value="<?= e($usuario["dni"]) ?>" required>
        </div>
      </fieldset>

      <fieldset>
        <legend>Cuenta</legend>
        <div>
          <label for="email">Correo electrónico *</label>
          <input type="email" id="email" name="email" value="<?= e($usuario["email"]) ?>" required>
        </div>
        <div>
          <label for="rol">Rol *</label>
          <select id="rol" name="rol">
            <option value="usuario" <?= $usuario["rol"] === "usuario" ? "selected" : "" ?>>Usuario</option>
            <option value="admin"   <?= $usuario["rol"] === "admin"   ? "selected" : "" ?>>Administrador</option>
          </select>
        </div>
        <div>
          <label for="password">Nueva contraseña (dejar vacío para no cambiarla)</label>
          <input type="password" id="password" name="password" minlength="8">
        </div>
      </fieldset>

      <div class="botones">
        <button type="submit">Guardar cambios</button>
        <a href="admin_usuarios.php" class="boton secundario">Cancelar</a>
      </div>
    </form>
  </section>
<?php require "includes/footer.php"; ?>
