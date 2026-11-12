<?php
// =========================================================
// productos.php - "VER TODO": catálogo con paginación
// Ejemplos:  productos.php            -> todas las prendas
//            productos.php?cat=juego  -> solo Juego
//            productos.php?pagina=3   -> página 3
// =========================================================
require_once "includes/funciones.php";

$POR_PAGINA = 18;   // 18 prendas por página

// 1) Leemos y validamos lo que viene por la URL
$cat = $_GET["cat"] ?? "";
if (!array_key_exists($cat, $CATEGORIAS)) {
    $cat = "";      // categoría inválida o vacía = mostrar todas
}
$pagina = max(1, (int)($_GET["pagina"] ?? 1));

// 2) Contamos cuántos productos hay para saber cuántas páginas son
if ($cat !== "") {
    $sql = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE categoria = ?");
    $sql->execute([$cat]);
} else {
    $sql = $pdo->query("SELECT COUNT(*) FROM productos");
}
$total   = (int)$sql->fetchColumn();
$paginas = max(1, (int)ceil($total / $POR_PAGINA));
$pagina  = min($pagina, $paginas);              // si piden la página 99, mostramos la última
$desde   = ($pagina - 1) * $POR_PAGINA;         // OFFSET: cuántos productos saltear

// 3) Traemos solo los productos de esta página (LIMIT y OFFSET)
$where = $cat !== "" ? "WHERE categoria = :cat" : "";
$sql = $pdo->prepare("SELECT id, nombre, categoria, precio, precio_anterior, imagen
                      FROM productos $where
                      ORDER BY id DESC
                      LIMIT :limite OFFSET :desde");
if ($cat !== "") $sql->bindValue(":cat", $cat);
$sql->bindValue(":limite", $POR_PAGINA, PDO::PARAM_INT);
$sql->bindValue(":desde", $desde, PDO::PARAM_INT);
$sql->execute();
$productos = $sql->fetchAll();

// Cantidad por categoría (para el menú de la izquierda)
$conteo = $pdo->query("SELECT categoria, COUNT(*) AS cant FROM productos GROUP BY categoria")->fetchAll(PDO::FETCH_KEY_PAIR);

// Arma el link a una página manteniendo la categoría elegida
function link_pagina($cat, $n) {
    $params = ["pagina" => $n];
    if ($cat !== "") $params = ["cat" => $cat] + $params;
    return "productos.php?" . http_build_query($params);
}

$titulo = $cat !== "" ? $CATEGORIAS[$cat] : "Kappa";
require "includes/header.php";
?>
  <div class="catalogo">
    <!-- Menú lateral de categorías -->
    <aside class="lateral">
      <h2>KAPPA</h2>
      <a href="productos.php" class="<?= $cat === "" ? "activo" : "" ?>">
        Ver todo <span><?= array_sum($conteo) ?></span>
      </a>
      <?php foreach ($CATEGORIAS as $clave => $nombre): ?>
        <a href="productos.php?cat=<?= e($clave) ?>" class="<?= $cat === $clave ? "activo" : "" ?>">
          <?= e($nombre) ?> <span><?= (int)($conteo[$clave] ?? 0) ?></span>
        </a>
      <?php endforeach; ?>
    </aside>

    <section class="catalogo-contenido">
      <div class="catalogo-titulo">
        <h1>KAPPA<?= $cat !== "" ? " · " . e($CATEGORIAS[$cat]) : "" ?></h1>
        <p><?= $total ?> productos</p>
      </div>

      <div class="grilla">
        <?php foreach ($productos as $p): ?>
          <?= tarjeta_producto($p) ?>
        <?php endforeach; ?>
        <?php if (empty($productos)): ?>
          <p class="vacio">No hay productos en esta categoría.</p>
        <?php endif; ?>
      </div>

      <!-- Paginación -->
      <?php if ($paginas > 1): ?>
        <nav class="paginacion">
          <span class="paginacion-info">Mostrando página <?= $pagina ?> de <?= $paginas ?></span>
          <ul>
            <?php if ($pagina > 1): ?>
              <li><a href="<?= e(link_pagina($cat, $pagina - 1)) ?>">&laquo;</a></li>
            <?php endif; ?>
            <?php for ($n = 1; $n <= $paginas; $n++): ?>
              <li>
                <?php if ($n === $pagina): ?>
                  <span class="actual"><?= $n ?></span>
                <?php else: ?>
                  <a href="<?= e(link_pagina($cat, $n)) ?>"><?= $n ?></a>
                <?php endif; ?>
              </li>
            <?php endfor; ?>
            <?php if ($pagina < $paginas): ?>
              <li><a href="<?= e(link_pagina($cat, $pagina + 1)) ?>">&raquo;</a></li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </section>
  </div>
<?php require "includes/footer.php"; ?>
