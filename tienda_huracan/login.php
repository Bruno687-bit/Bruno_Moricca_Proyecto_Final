<?php
// =========================================================
// login.php - Inicio de sesión
// =========================================================
require_once "includes/funciones.php";

// Si ya inició sesión, lo mandamos a su panel
if (!empty($_SESSION["usuario_id"])) {
    header("Location: panel.php");
    exit;
}

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verificar_csrf();

    $email    = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Completá el correo y la contraseña.";
    } else {
        // Buscamos al usuario por su correo (consulta preparada)
        $sql = $pdo->prepare("SELECT id, nombre, password, rol, fecha_archivado FROM usuarios WHERE email = ?");
        $sql->execute([$email]);
        $usuario = $sql->fetch();

        // password_verify compara la contraseña escrita con el hash guardado
        if (!$usuario || !password_verify($password, $usuario["password"])) {
            // Mismo mensaje en los dos casos: así no revelamos qué correos existen
            $error = "Correo o contraseña incorrectos.";
        } elseif ($usuario["fecha_archivado"] !== null) {
            $error = "Tu cuenta está archivada. Comunicate con un administrador.";
        } else {
            // Login correcto: generamos un id de sesión nuevo (evita robo de sesión)
            session_regenerate_id(true);
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nombre"]     = $usuario["nombre"];
            $_SESSION["rol"]        = $usuario["rol"];

            header("Location: " . ($usuario["rol"] === "admin" ? "admin_usuarios.php" : "panel.php"));
            exit;
        }
    }
}

$titulo = "Ingresar";
require "includes/header.php";
?>
  <section class="contenedor-form chico">
    <h1>Iniciar sesión</h1>
    <?php mostrar_mensajes(); ?>
    <?php if (isset($_GET["salida"])): ?>
      <p class="alerta ok">Cerraste sesión correctamente.</p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p class="alerta error"><?= e($error) ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <?= campo_csrf() ?>
      <fieldset>
        <legend>Tus datos</legend>
        <div>
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus>
        </div>
        <div>
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" required>
        </div>
      </fieldset>
      <div class="botones">
        <button type="submit">Ingresar</button>
      </div>
      <p class="nota">¿No tenés cuenta? <a href="registro.php">Registrate</a></p>
    </form>
  </section>
<?php require "includes/footer.php"; ?>
