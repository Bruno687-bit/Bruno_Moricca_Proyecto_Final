-- =========================================================
-- Base de datos: tienda_huracan
-- Proyecto: Sistema de usuarios - Tienda Huracán
-- Importar este archivo desde phpMyAdmin (pestaña "Importar")
-- =========================================================

CREATE DATABASE IF NOT EXISTS tienda_huracan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tienda_huracan;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre          VARCHAR(60)  NOT NULL,
  apellido        VARCHAR(60)  NOT NULL,
  dni             VARCHAR(10)  NOT NULL,
  email           VARCHAR(120) NOT NULL,
  password        VARCHAR(255) NOT NULL,              -- guarda el HASH, nunca la contraseña real
  rol             ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',
  fecha_creacion  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_archivado DATETIME     NULL DEFAULT NULL,     -- NULL = activo / con fecha = archivado
  PRIMARY KEY (id),
  UNIQUE KEY uq_dni (dni),                            -- no se repite el DNI
  UNIQUE KEY uq_email (email)                         -- no se repite el correo
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario administrador inicial
-- Correo: admin@huracan.com
-- Contraseña: Admin1234   (cambiarla después desde el panel)
INSERT INTO usuarios (nombre, apellido, dni, email, password, rol)
VALUES ('Admin', 'Quemero', '10000000', 'admin@huracan.com',
        '$2y$10$COIKQac87iFfKi7C4M6VBuZwdRSZchyr/erepsuVm8Dja3NjTMM7e', 'admin');

-- =========================================================
-- Tabla de productos (catálogo de la tienda)
-- La columna "imagen" guarda el nombre de la foto dentro de la carpeta img/
-- =========================================================
CREATE TABLE IF NOT EXISTS productos (
  id               INT UNSIGNED NOT NULL,
  nombre           VARCHAR(120) NOT NULL,
  categoria        ENUM('juego','entrenamiento','salida') NOT NULL,
  precio           INT UNSIGNED NOT NULL,           -- precio actual
  precio_anterior  INT UNSIGNED NULL DEFAULT NULL,  -- si tiene descuento, el precio de antes
  imagen           VARCHAR(120) NOT NULL,          -- ej: camiseta-titular-2026-regular.jpg
  PRIMARY KEY (id),
  KEY idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO productos (id, nombre, categoria, precio, precio_anterior, imagen) VALUES
(2032, 'Short Rojo Kids 2026', 'juego', 60000, NULL, 'short-rojo-kids-2026.jpg'),
(2028, 'Camiseta Suplente Kids 2026', 'juego', 88000, NULL, 'camiseta-suplente-kids-2026.jpg'),
(2024, 'Camiseta Violeta de Arquero 2025 Sin Sponsor', 'juego', 55000, NULL, 'camiseta-violeta-de-arquero-2025-sin-sponsor.jpg'),
(2020, 'Camiseta Blanca Regular 2025 Sin Sponsor', 'juego', 55000, NULL, 'camiseta-blanca-regular-2025-sin-sponsor.jpg'),
(2016, 'Camiseta Roja Regular 2025 Sin sponsor', 'juego', 55000, NULL, 'camiseta-roja-regular-2025-sin-sponsor.jpg'),
(2012, 'Camiseta Verde de Arquero 2025 Sin Sponsor', 'juego', 55000, NULL, 'camiseta-verde-de-arquero-2025-sin-sponsor.jpg'),
(2004, 'Short de Entrenamiento 2026', 'entrenamiento', 64000, 80000, 'short-de-entrenamiento-2026.jpg'),
(1998, 'Camiseta Dama Suplente 2026', 'juego', 135000, NULL, 'camiseta-dama-suplente-2026.jpg'),
(1990, 'Short Negro Alternativo 2026', 'juego', 70000, NULL, 'short-negro-alternativo-2026.jpg'),
(1982, 'Camiseta Negra Alternativa Regular 2026', 'juego', 135000, NULL, 'camiseta-negra-alternativa-regular-2026.jpg'),
(1976, 'Short Titular Blanco Niño 2026', 'juego', 60000, NULL, 'short-titular-blanco-nino-2026.jpg'),
(1968, 'Short Fucsia de Arquero 2026', 'juego', 70000, NULL, 'short-fucsia-de-arquero-2026.jpg'),
(1960, 'Short Celeste Arquero 2026', 'juego', 70000, NULL, 'short-celeste-arquero-2026.jpg'),
(1952, 'Short Naranja Arquero 2026', 'juego', 70000, NULL, 'short-naranja-arquero-2026.jpg'),
(1944, 'Camiseta Naranja de Arquero 2026', 'juego', 135000, NULL, 'camiseta-naranja-de-arquero-2026.jpg'),
(1936, 'Camiseta Fucsia de Arquero 2026', 'juego', 135000, NULL, 'camiseta-fucsia-de-arquero-2026.jpg'),
(1928, 'Camiseta Celeste Arquero 2026', 'juego', 135000, NULL, 'camiseta-celeste-arquero-2026.jpg'),
(1922, 'Short Arquero Naranja Niño', 'juego', 60000, NULL, 'short-arquero-naranja-nino.jpg'),
(1912, 'Chomba Retro 2026', 'salida', 100000, 125000, 'chomba-retro-2026.jpg'),
(1904, 'Short de Juego Suplente 2026', 'juego', 70000, NULL, 'short-de-juego-suplente-2026.jpg'),
(1896, 'Short de Juego Titular 2026', 'juego', 70000, NULL, 'short-de-juego-titular-2026.jpg'),
(1888, 'Camiseta Suplente 2026 Regular', 'juego', 135000, NULL, 'camiseta-suplente-2026-regular.jpg'),
(1880, 'Campera de Invierno Negra 2026', 'salida', 166000, NULL, 'campera-de-invierno-negra-2026.jpg'),
(1872, 'Camiseta Negra Pre Match 2026', 'entrenamiento', 88000, NULL, 'camiseta-negra-pre-match-2026.jpg'),
(1864, 'Rompeviento Negro 2026', 'entrenamiento', 145000, NULL, 'rompeviento-negro-2026.jpg'),
(1856, 'Camperon Huracan 2026', 'salida', 176000, NULL, 'camperon-huracan-2026.jpg'),
(1848, 'Camiseta Titular 2026 Regular', 'juego', 135000, NULL, 'camiseta-titular-2026-regular.jpg'),
(1840, 'Campera Retro Prematch 2026', 'entrenamiento', 130000, NULL, 'campera-retro-prematch-2026.jpg'),
(1831, 'Musculosa Entrenamiento Coral 2026', 'entrenamiento', 68000, NULL, 'musculosa-entrenamiento-coral-2026.jpg'),
(1822, 'Musculosa Entrenamiento V 2026', 'entrenamiento', 68000, NULL, 'musculosa-entrenamiento-v-2026.jpg'),
(1814, 'Remera Entrenamiento Coral 2026', 'entrenamiento', 73000, NULL, 'remera-entrenamiento-coral-2026.jpg'),
(1806, 'Remera Entrenamiento V 2026', 'entrenamiento', 73000, NULL, 'remera-entrenamiento-v-2026.jpg'),
(1797, 'Buzo Entrenamiento Medio Cierre Coral 2026', 'entrenamiento', 135000, NULL, 'buzo-entrenamiento-medio-cierre-coral-2026.jpg'),
(1789, 'Buzo Entrenamiento Medio Cierre V 2026', 'entrenamiento', 135000, NULL, 'buzo-entrenamiento-medio-cierre-v-2026.jpg'),
(1780, 'Pantalon de Entrenamiento 2026', 'entrenamiento', 104000, NULL, 'pantalon-de-entrenamiento-2026.jpg'),
(1771, 'Bermuda de Salida 2026', 'salida', 70000, NULL, 'bermuda-de-salida-2026.jpg'),
(1763, 'Pantalon Negro Salida 2026', 'salida', 114000, NULL, 'pantalon-negro-salida-2026.jpg'),
(1755, 'Campera Salida 2026', 'salida', 125000, NULL, 'campera-salida-2026.jpg'),
(1747, 'Polo Salida Negra 2026', 'salida', 66000, NULL, 'polo-salida-negra-2026.jpg'),
(1739, 'Polo Salida Blanca 2026', 'salida', 66000, NULL, 'polo-salida-blanca-2026.jpg'),
(1731, 'Remera Salida Negra 2026', 'salida', 63000, NULL, 'remera-salida-negra-2026.jpg'),
(1722, 'Remera Salida Blanca 2026', 'salida', 63000, NULL, 'remera-salida-blanca-2026.jpg'),
(1717, 'Camiseta Alternativa 2025 Slim', 'juego', 55000, 100000, 'camiseta-alternativa-2025-slim.jpg'),
(1710, 'Edición Limitada Huracan Kombat XXV', 'juego', 104400, 180000, 'edicion-limitada-huracan-kombat-xxv.jpg'),
(1704, 'Camiseta Lady Roja Regular 2025', 'juego', 46400, 80000, 'camiseta-lady-roja-regular-2025.jpg'),
(1698, 'Camiseta Lady Blanca Regular 2025', 'juego', 46400, 80000, 'camiseta-lady-blanca-regular-2025.jpg'),
(1690, 'Chomba de Salida Roja 2024', 'salida', 40000, NULL, 'chomba-de-salida-roja-2024.jpg'),
(1682, 'Chomba de Salida Blanca 2024', 'salida', 40000, NULL, 'chomba-de-salida-blanca-2024.jpg'),
(1676, 'Short Kids Arquero 2025', 'juego', 32850, 45000, 'short-kids-arquero-2025.jpg'),
(1670, 'Short Kids Rojo 2025', 'juego', 32850, 45000, 'short-kids-rojo-2025.jpg'),
(1664, 'Short Kids Blanco 2025', 'juego', 32850, 45000, 'short-kids-blanco-2025.jpg'),
(1652, 'Camiseta Titular Kids 2025', 'juego', 35000, 70000, 'camiseta-titular-kids-2025.jpg'),
(1638, 'Camiseta Roja Kids 2025', 'juego', 35000, 70000, 'camiseta-roja-kids-2025.jpg'),
(1632, 'Camiseta Alternativa Kids 2025', 'juego', 35000, 70000, 'camiseta-alternativa-kids-2025.jpg'),
(1624, 'Camperon Rojo 2025', 'entrenamiento', 127500, 170000, 'camperon-rojo-2025.jpg'),
(1616, 'Campera Abrigo Salida 2025', 'salida', 112500, 150000, 'campera-abrigo-salida-2025.jpg'),
(1608, 'Campera Rompevientos Beige 2025', 'entrenamiento', 105000, 140000, 'campera-rompevientos-beige-2025.jpg'),
(1600, 'Short Verde de Arquero 2025', 'juego', 36500, 50000, 'short-verde-de-arquero-2025.jpg'),
(1592, 'Short de Arquero Violeta 2025', 'juego', 36500, 50000, 'short-de-arquero-violeta-2025.jpg'),
(1584, 'Short Amarillo de Arquero 2025', 'juego', 36500, 50000, 'short-amarillo-de-arquero-2025.jpg'),
(1576, 'Campera de Entrenamiento Pre Match 2025', 'entrenamiento', 91000, 130000, 'campera-de-entrenamiento-pre-match-2025.jpg'),
(1568, 'Camiseta Violeta de Arquero 2025', 'juego', 55000, 100000, 'camiseta-violeta-de-arquero-2025.jpg'),
(1560, 'Camiseta Amarilla de Arquero 2025', 'juego', 55000, 100000, 'camiseta-amarilla-de-arquero-2025.jpg'),
(1551, 'Camiseta Verde de Arquero 2025', 'juego', 55000, 100000, 'camiseta-verde-de-arquero-2025.jpg'),
(1540, 'Camiseta Pre Match 2025', 'entrenamiento', 44660, 77000, 'camiseta-pre-match-2025.jpg'),
(1539, 'Medias de Juego Alternativa 2025', 'juego', 6600, 11000, 'medias-de-juego-alternativa-2025.jpg'),
(1531, 'Short de Juego Alternativo 2025', 'juego', 36500, 50000, 'short-de-juego-alternativo-2025.jpg'),
(1523, 'Camiseta Alternativa 2025 Regular', 'juego', 55000, 100000, 'camiseta-alternativa-2025-regular.jpg'),
(1510, 'Remera Salida Verde 2025', 'salida', 37700, 65000, 'remera-salida-verde-2025.jpg'),
(1502, 'Remera Salida Roja 2025', 'salida', 37700, 65000, 'remera-salida-roja-2025.jpg'),
(1494, 'Polo Salida Roja 2025', 'salida', 43500, 75000, 'polo-salida-roja-2025.jpg'),
(1486, 'Polo Salida Verde 2025', 'salida', 43500, 75000, 'polo-salida-verde-2025.jpg'),
(1478, 'Bermuda de Salida 2025', 'salida', 63750, 75000, 'bermuda-de-salida-2025.jpg'),
(1470, 'Pantalon Verde Salida 2025', 'salida', 75000, 100000, 'pantalon-verde-salida-2025.jpg'),
(1462, 'Campera de Salida Cuello Mao 2025', 'salida', 82500, 110000, 'campera-de-salida-cuello-mao-2025.jpg'),
(1452, 'Short de Juego Rojo 2025', 'juego', 36500, 50000, 'short-de-juego-rojo-2025.jpg'),
(1444, 'Short de Juego Blanco 2025', 'juego', 36500, 50000, 'short-de-juego-blanco-2025.jpg'),
(1435, 'Camiseta Roja Regular 2025', 'juego', 55000, 100000, 'camiseta-roja-regular-2025.jpg'),
(1419, 'Camiseta Blanca Regular 2025', 'juego', 55000, 100000, 'camiseta-blanca-regular-2025.jpg'),
(1411, 'Pantalon Rojo de Entrenamiento 2025', 'entrenamiento', 58000, 100000, 'pantalon-rojo-de-entrenamiento-2025.jpg'),
(1403, 'Short Entrenamiento 2025', 'entrenamiento', 36500, 50000, 'short-entrenamiento-2025.jpg'),
(1395, 'Buzo de Entrenamiento Medio Cierre Rojo 2025', 'entrenamiento', 75000, 100000, 'buzo-de-entrenamiento-medio-cierre-rojo-2025.jpg'),
(1387, 'Buzo de Entrenamiento Medio Cierre Beige 2025', 'entrenamiento', 75000, 100000, 'buzo-de-entrenamiento-medio-cierre-beige-2025.jpg'),
(1379, 'Musculosa Roja 2025', 'entrenamiento', 31900, 55000, 'musculosa-roja-2025.jpg'),
(1369, 'Musculosa Beige 2025', 'entrenamiento', 31900, 55000, 'musculosa-beige-2025.jpg'),
(1361, 'Remera de Entrenamiento Roja 2025', 'entrenamiento', 34800, 60000, 'remera-de-entrenamiento-roja-2025.jpg'),
(1353, 'Remera de Entrenamiento 2025 Beige', 'entrenamiento', 34800, 60000, 'remera-de-entrenamiento-2025-beige.jpg'),
(1344, 'Musculosa Gris de Entrenamiento Huracan 2024', 'entrenamiento', 31000, NULL, 'musculosa-gris-de-entrenamiento-huracan-2024.jpg'),
(1310, 'Camiseta Blanca Kids 2024', 'juego', 27500, 50000, 'camiseta-blanca-kids-2024.jpg'),
(1301, 'Remera blanca de salida 2024', 'salida', 36000, NULL, 'remera-blanca-de-salida-2024.jpg'),
(1293, 'Remera roja de salida 2024', 'salida', 36000, NULL, 'remera-roja-de-salida-2024.jpg'),
(1272, 'Pantalón de salida rojo 2024', 'salida', 45000, NULL, 'pantalon-de-salida-rojo-2024.jpg'),
(1264, 'Bermuda de salida 2024', 'salida', 45000, NULL, 'bermuda-de-salida-2024.jpg'),
(1223, 'Camiseta Roja Regular sin sponsor 2024', 'juego', 50050, 77000, 'camiseta-roja-regular-sin-sponsor-2024.jpg'),
(1175, 'Camiseta Blanca Regular 2024 Sin Sponsor', 'juego', 50050, 77000, 'camiseta-blanca-regular-2024-sin-sponsor.jpg'),
(1135, 'Camiseta arquero Lila 2024', 'juego', 50050, 77000, 'camiseta-arquero-lila-2024.jpg'),
(1086, 'Bolso Huracan', 'entrenamiento', 26500, NULL, 'bolso-huracan.jpg'),
(1085, 'Mochila Huracan', 'entrenamiento', 27000, NULL, 'mochila-huracan.jpg'),
(1074, 'Short rojo titular 2024', 'juego', 30000, NULL, 'short-rojo-titular-2024.jpg'),
(1058, 'Camiseta roja Slim 2024', 'juego', 50050, 77000, 'camiseta-roja-slim-2024.jpg'),
(1010, 'Remera negra conmemorativa 1973', 'salida', 14840, 21200, 'remera-negra-conmemorativa-1973.jpg'),
(938, 'Medias de Arquero Violetas 2023', 'juego', 3300, 5500, 'medias-de-arquero-violetas-2023.jpg'),
(937, 'Medias de Arquero Rosas 2023', 'juego', 3300, 5500, 'medias-de-arquero-rosas-2023.jpg'),
(936, 'Medias de Arquero Celestes 2023', 'juego', 3300, 5500, 'medias-de-arquero-celestes-2023.jpg');
