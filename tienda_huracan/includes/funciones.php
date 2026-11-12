<?php
// =========================================================
// funciones.php - Funciones que usan todas las páginas
// =========================================================

// Iniciamos la sesión una sola vez
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../conexion.php";

// Escapa texto antes de mostrarlo en HTML (evita inyección de código / XSS)
function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, "UTF-8");
}

// Muestra un precio como en la tienda: $135000
function precio($numero) {
    return $numero === null ? "Consultar" : "$" . (int)$numero;
}

// Dibujo simple (SVG) de una prenda, se usa cuando todavía no hay foto en la carpeta img/
// $tipo: "remera" | "short" | "pantalon"
function dibujo_prenda($tipo, $color, $detalle) {
    $formas = [
        "remera"   => "M30 15 L42 10 Q50 17 58 10 L70 15 L88 32 L78 43 L70 37 L70 90 L30 90 L30 37 L22 43 L12 32 Z",
        "short"    => "M22 22 L78 22 L85 78 L56 81 L50 47 L44 81 L15 78 Z",
        "pantalon" => "M31 8 L69 8 L72 93 L55 93 L50 33 L45 93 L28 93 Z",
    ];
    $d = $formas[$tipo] ?? $formas["remera"];
    return '<svg viewBox="0 0 100 100" class="prenda" aria-hidden="true">'
         . '<path d="' . $d . '" fill="' . e($color) . '" stroke="' . e($detalle) . '" stroke-width="3" stroke-linejoin="round"/>'
         . '</svg>';
}

// ---------- Productos ----------
// Categorías válidas (también sirve para validar lo que llega por la URL)
$CATEGORIAS = [
    "juego"         => "Juego",
    "entrenamiento" => "Entrenamiento",
    "salida"        => "Salida",
];

// Devuelve la dirección de la foto de un producto (las fotos están en la carpeta img/)
function foto_producto($p) {
    return "img/" . $p["imagen"];
}

// Dibuja la tarjeta de un producto (se usa en la portada y en productos.php)
function tarjeta_producto($p) {
    $html  = '<article class="producto">';
    if ($p["precio_anterior"]) {
        $off = round((1 - $p["precio"] / $p["precio_anterior"]) * 100);
        $html .= '<span class="oferta">' . $off . '% OFF</span>';
    }
    $html .= '<div class="producto-img"><img src="' . e(foto_producto($p)) . '" alt="' . e($p["nombre"]) . '" loading="lazy"></div>';
    $html .= '<h3>' . e($p["nombre"]) . '</h3>';
    $html .= '<p class="precio">';
    if ($p["precio_anterior"]) {
        $html .= '<span class="antes">' . e(precio($p["precio_anterior"])) . '</span> ';
    }
    $html .= e(precio($p["precio"])) . '</p>';
    $html .= '</article>';
    return $html;
}

// ---------- Protección CSRF ----------
// Un token secreto que se guarda en la sesión y viaja oculto en cada formulario.
// Si alguien intenta enviar un formulario desde otro sitio, no tiene el token.
function token_csrf() {
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf"];
}

function campo_csrf() {
    return '<input type="hidden" name="csrf" value="' . token_csrf() . '">';
}

function verificar_csrf() {
    $enviado = $_POST["csrf"] ?? "";
    if (!hash_equals(token_csrf(), $enviado)) {
        http_response_code(403);
        die("Solicitud no válida. Volvé a cargar la página e intentá de nuevo.");
    }
}

// ---------- Control de acceso ----------
function requerir_login() {
    global $pdo;
    if (empty($_SESSION["usuario_id"])) {
        $_SESSION["mensaje_error"] = "Tenés que iniciar sesión para entrar a esa sección.";
        header("Location: login.php");
        exit;
    }
    // Volvemos a consultar la base: si lo archivaron o le cambiaron el rol, se actualiza al instante
    $sql = $pdo->prepare("SELECT rol, fecha_archivado FROM usuarios WHERE id = ?");
    $sql->execute([$_SESSION["usuario_id"]]);
    $fila = $sql->fetch();
    if (!$fila || $fila["fecha_archivado"] !== null) {
        header("Location: cerrar_sesion.php");
        exit;
    }
    $_SESSION["rol"] = $fila["rol"];
}

function requerir_admin() {
    requerir_login();
    if ($_SESSION["rol"] !== "admin") {
        http_response_code(403);
        $_SESSION["mensaje_error"] = "No tenés permisos de administrador.";
        header("Location: panel.php");
        exit;
    }
}

// ---------- Mensajes "flash" (se muestran una vez) ----------
function mostrar_mensajes() {
    if (!empty($_SESSION["mensaje_ok"])) {
        echo '<p class="alerta ok">' . e($_SESSION["mensaje_ok"]) . '</p>';
        unset($_SESSION["mensaje_ok"]);
    }
    if (!empty($_SESSION["mensaje_error"])) {
        echo '<p class="alerta error">' . e($_SESSION["mensaje_error"]) . '</p>';
        unset($_SESSION["mensaje_error"]);
    }
}

// ---------- Validación de datos de usuario ----------
// Devuelve un arreglo con los errores encontrados (vacío si todo está bien)
function validar_usuario($nombre, $apellido, $dni, $email) {
    $errores = [];
    if ($nombre === "")   $errores[] = "El nombre es obligatorio.";
    if ($apellido === "") $errores[] = "El apellido es obligatorio.";
    if (mb_strlen($nombre) > 60 || mb_strlen($apellido) > 60) $errores[] = "Nombre y apellido: máximo 60 caracteres.";
    if (!preg_match('/^[0-9]{7,8}$/', $dni)) $errores[] = "El DNI debe tener 7 u 8 números, sin puntos.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido.";
    return $errores;
}

// Revisa si el DNI o el email ya existen (se puede excluir un id al editar)
function buscar_duplicados($pdo, $dni, $email, $excluir_id = 0) {
    $errores = [];
    $sql = $pdo->prepare("SELECT dni, email FROM usuarios WHERE (dni = ? OR email = ?) AND id <> ?");
    $sql->execute([$dni, $email, $excluir_id]);
    foreach ($sql->fetchAll() as $fila) {
        if ($fila["dni"] === $dni)     $errores[] = "Ya existe una cuenta con ese DNI.";
        if (strcasecmp($fila["email"], $email) === 0) $errores[] = "Ya existe una cuenta con ese correo.";
    }
    return array_unique($errores);
}
