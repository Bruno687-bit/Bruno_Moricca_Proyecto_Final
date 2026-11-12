<?php
// =========================================================
// admin_nuevo_usuario.php - El admin registra un usuario nuevo
// (puede elegir el rol: usuario o admin)
// =========================================================
require_once "includes/funciones.php";
requerir_admin();

$errores = [];
$nombre = $apellido = $dni = $email = "";
$rol = "usuario";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verificar_csrf();

    $nombre   = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $dni      = trim($_POST["dni"] ?? "");
    $email    = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $rol      = $_POST["rol"] ?? "usuario";

    $errores = validar_usuario($nombre, $apellido, $dni, $email);
    if (strlen($password) < 8) $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    if (!in_array($rol, ["usuario", "admin"], true)) $errores[] = "Rol no válido.";

    if (empty($errores)) {
        $errores = buscar_duplicados($pdo, $dni, $email);
    }

    if (empty($errores)) {
        $sql = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, password, rol) VALUES (?, ?, ?, ?, ?, ?)");
        try {
            $sql->execute([$nombre, $apellido, $dni, $email, password_hash($password, PASSWORD_DEFAULT), $rol]);
            $_SESSION["mensaje_ok"] = "Usuario $nombre $apellido registrado correctamente.";
            header("Location: admin_usuarios.php");
            exit;
        } catch (PDOException $ex) {
            $errores[] = "Ese DNI o correo ya está registrado.";
        }
    }
}

$titulo = "Registrar usuario";
require "includes/header.php";
?>
  <section class="contenedor-form">
    <h1>Registrar nuevo usuario</h1>
    <p><a href="admin_usuarios.php">&larr; Volver al listado</a></p>

    <?php foreach ($errores as $err): ?>
      <p class="alerta error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <form action="admin_nuevo_usuario.php" method="POST" novalidate>
      <?= campo_csrf() ?>
      <fieldset>
        <legend>Datos Personales</legend>
        <div>
          <label for="nombre">Nombre *</label>
          <input type="text" id="nombre" name="nombre" maxlength="60" value="<?= e($nombre) ?>" required>
        </div>
        <div>
          <label for="apellido">Apellido *</label>
          <input type="text" id="apellido" name="apellido" maxlength="60" value="<?= e($apellido) ?>" required>
        </div>
        <div>
          <label for="dni">DNI *</label>
          <input type="text" id="dni" name="dni" inputmode="numeric" maxlength="8" value="<?= e($dni) ?>" required>
        </div>
      </fieldset>

      <fieldset>
        <legend>Cuenta</legend>
        <div>
          <label for="email">Correo electrónico *</label>
          <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>
        <div>
          <label for="password">Contraseña inicial * (mínimo 8 caracteres)</label>
          <input type="password" id="password" name="password" minlength="8" required>
        </div>
        <div>
          <label for="rol">Rol *</label>
          <select id="rol" name="rol">
            <option value="usuario" <?= $rol === "usuario" ? "selected" : "" ?>>Usuario</option>
            <option value="admin"   <?= $rol === "admin"   ? "selected" : "" ?>>Administrador</option>
          </select>
        </div>
      </fieldset>

      <div class="botones">
        <button type="submit">Registrar</button>
        <button type="reset" class="secundario">Limpiar formulario</button>
      </div>
    </form>
  </section>
<?php require "includes/footer.php"; ?>
