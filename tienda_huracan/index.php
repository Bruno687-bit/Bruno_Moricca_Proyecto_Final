<?php
require_once "includes/funciones.php";

// =========================================================
// Portada: para cada categoría traemos las 4 prendas más nuevas
// =========================================================
$filas = [];
$sql = $pdo->prepare("SELECT id, nombre, categoria, precio, precio_anterior, imagen
                      FROM productos WHERE categoria = ? ORDER BY id DESC LIMIT 4");
foreach ($CATEGORIAS as $clave => $nombre) {
    $sql->execute([$clave]);
    $filas[$clave] = $sql->fetchAll();
}

$hay_escudo = file_exists("img/escudo.png");
$hay_banner = file_exists("img/banner.jpg");

$titulo = "Inicio";
require "includes/header.php";
?>
  <!-- ===== BANNER ===== -->
  <?php if ($hay_banner): ?>
    <section class="banner" style="background-image:url(img/banner.jpg)"></section>
  <?php else: ?>
    <!-- Collage de 4 paneles hecho con CSS (se reemplaza guardando img/banner.jpg) -->
    <section class="banner collage">
      <div class="panel panel-1"></div>
      <div class="panel panel-2"></div>
      <div class="panel panel-3"></div>
      <div class="panel panel-4"></div>
      <div class="banner-texto">
        <h1>TEMPORADA 2026</h1>
        <p>La nueva indumentaria oficial del Globo</p>
      </div>
    </section>
  <?php endif; ?>

  <?php mostrar_mensajes(); ?>

  <!-- ===== FILAS DE CATEGORÍAS ===== -->
  <?php $primera = true; ?>
  <?php foreach ($CATEGORIAS as $clave => $nombre): ?>
    <section class="categoria <?= $primera ? "primera" : "" ?>" id="<?= e($clave) ?>">
    <?php $primera = false; ?>
      <div class="fila">

        <!-- Tarjeta de la izquierda: lleva a "VER TODO" de esa categoría -->
        <a href="productos.php?cat=<?= e($clave) ?>" class="tarjeta-categoria">
          <?php if ($hay_escudo): ?>
            <img src="img/escudo.png" alt="Escudo" class="escudo">
          <?php else: ?>
            <span class="escudo-texto">CAH</span>
          <?php endif; ?>
          <div class="etiqueta-categoria">
            <strong>KAPPA</strong>
            <span><?= e($nombre) ?></span>
          </div>
          <span class="ver-todo">VER TODO</span>
        </a>

        <?php foreach ($filas[$clave] as $p): ?>
          <?= tarjeta_producto($p) ?>
        <?php endforeach; ?>

      </div>
    </section>
  <?php endforeach; ?>

  <div class="ver-catalogo">
    <a href="productos.php" class="boton">Ver todas las prendas</a>
  </div>
<?php require "includes/footer.php"; ?>
