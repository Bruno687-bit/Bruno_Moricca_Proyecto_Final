<?php
// =========================================================
// registro.php - Registro público de usuarios
// =========================================================
require_once "includes/funciones.php";

$errores = [];
// Valores para volver a mostrar en el formulario si hay error
$nombre = $apellido = $dni = $email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verificar_csrf();

    // 1) Leemos y limpiamos los datos del formulario
    $nombre     = trim($_POST["nombre"] ?? "");
    $apellido   = trim($_POST["apellido"] ?? "");
    $dni        = trim($_POST["dni"] ?? "");
    $email      = strtolower(trim($_POST["email"] ?? ""));
    $password   = $_POST["password"] ?? "";
    $password2  = $_POST["password2"] ?? "";

    // 2) Validamos
    $errores = validar_usuario($nombre, $apellido, $dni, $email);
    if (strlen($password) < 8)      $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    if ($password !== $password2)   $errores[] = "Las contraseñas no coinciden.";

    // 3) Revisamos que el DNI y el correo no existan
    if (empty($errores)) {
        $errores = buscar_duplicados($pdo, $dni, $email);
    }

    // 4) Si todo está bien, guardamos con la contraseña cifrada
    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, password) VALUES (?, ?, ?, ?, ?)");
        try {
            $sql->execute([$nombre, $apellido, $dni, $email, $hash]);
            $_SESSION["mensaje_ok"] = "¡Cuenta creada! Ya podés iniciar sesión.";
            header("Location: login.php");
            exit;
        } catch (PDOException $ex) {
            // 23000 = violación de UNIQUE (por si dos personas se registran al mismo tiempo)
            $errores[] = "Ese DNI o correo ya está registrado.";
        }
    }
}

$titulo = "Crear cuenta";
require "includes/header.php";
?>
  <section class="contenedor-form">
    <h1>Crear cuenta</h1>
    <p>Por favor complete todos los campos</p>

    <?php foreach ($errores as $err): ?>
      <p class="alerta error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <form action="registro.php" method="POST" novalidate>
      <?= campo_csrf() ?>
      <fieldset>
        <legend>Datos Personales</legend>

        <div>
          <label for="nombre">Nombre *</label>
          <input type="text" id="nombre" name="nombre" maxlength="60" placeholder="Ej: Bruno" value="<?= e($nombre) ?>" required>
        </div>
        <div>
          <label for="apellido">Apellido *</label>
          <input type="text" id="apellido" name="apellido" maxlength="60" placeholder="Ej: Moricca" value="<?= e($apellido) ?>" required>
        </div>
        <div>
          <label for="dni">DNI *</label>
          <input type="text" id="dni" name="dni" inputmode="numeric" maxlength="8" placeholder="Ej: 49960034" value="<?= e($dni) ?>" required>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos de acceso</legend>
        <div>
          <label for="email">Correo electrónico *</label>
          <input type="email" id="email" name="email" placeholder="Ej: brunito@correo.com" value="<?= e($email) ?>" required>
        </div>
        <div>
          <label for="password">Contraseña * (mínimo 8 caracteres)</label>
          <input type="password" id="password" name="password" minlength="8" required>
        </div>
        <div>
          <label for="password2">Repetir contraseña *</label>
          <input type="password" id="password2" name="password2" minlength="8" required>
        </div>
      </fieldset>

      <div class="botones">
        <button type="submit">Registrarse</button>
        <button type="reset" class="secundario">Limpiar formulario</button>
      </div>
      <p class="nota">¿Ya tenés cuenta? <a href="login.php">Iniciá sesión</a></p>
    </form>
  </section>
<?php require "includes/footer.php"; ?>
