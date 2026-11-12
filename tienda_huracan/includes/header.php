<?php
// header.php - Cabecera + barra roja de navegación (se incluye arriba de cada página)
// Antes de incluirlo, cada página define $titulo
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tienda Huracán | <?= e($titulo ?? "Inicio") ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <!-- Franja blanca superior con el logo -->
  <header class="cabecera">
    <a href="index.php" class="logo">
      <?php if (file_exists(__DIR__ . "/../img/logo.png")): ?>
        <img src="img/logo.png" alt="Tienda Huracán">
      <?php else: ?>
        TIENDA <span>HURACÁN</span>
      <?php endif; ?>
    </a>
    <span class="anio">1908</span>
  </header>

  <!-- Barra roja -->
  <nav class="menu">
    <div class="menu-izq">
      <div class="desplegable">
        <a href="productos.php" class="menu-boton">KAPPA</a>
        <!-- Se muestra al pasar el mouse (solo con CSS) -->
        <div class="desplegable-contenido">
          <div class="desplegable-foto"><?= dibujo_prenda("remera", "#d71920", "#fff") ?></div>
          <div class="desplegable-links">
            <a href="productos.php?cat=juego">Juego</a>
            <a href="productos.php?cat=entrenamiento">Entrenamiento</a>
            <a href="productos.php?cat=salida">Salida</a>
          </div>
        </div>
      </div>
    </div>

    <div class="menu-der">
      <?php if (!empty($_SESSION["usuario_id"])): ?>
        <?php if ($_SESSION["rol"] === "admin"): ?>
          <a href="admin_usuarios.php" class="menu-texto">Administrar</a>
        <?php endif; ?>
        <a href="panel.php" class="menu-texto">Hola, <?= e($_SESSION["nombre"]) ?></a>
      <?php endif; ?>

      <a href="index.php" title="Buscar" class="icono">
        <svg viewBox="0 0 24 24"><circle cx="10" cy="10" r="6.5"/><line x1="15" y1="15" x2="21" y2="21"/></svg>
      </a>
      <!-- Ícono de usuario: lleva al login o al panel según la sesión -->
      <a href="<?= empty($_SESSION["usuario_id"]) ? "login.php" : "panel.php" ?>" title="Mi cuenta" class="icono">
        <svg viewBox="0 0 24 24" class="relleno"><circle cx="12" cy="7" r="4.5"/><path d="M3 22c0-5 4-8 9-8s9 3 9 8z"/></svg>
      </a>
      <a href="index.php" title="Carrito" class="icono">
        <svg viewBox="0 0 24 24" class="relleno"><path d="M1 3h4l2.5 11h12L22 6H7" fill="none" stroke="#fff" stroke-width="2.2"/><circle cx="9" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg>
      </a>
      <?php if (!empty($_SESSION["usuario_id"])): ?>
        <a href="cerrar_sesion.php" class="menu-texto salir">Salir</a>
      <?php endif; ?>
    </div>
  </nav>
  <main>
