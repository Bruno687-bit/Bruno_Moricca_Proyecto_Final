# Tienda Huracán – Sistema de usuarios

Trabajo práctico: registro, inicio de sesión y administración de usuarios con **PHP + MySQL (PDO)**.

---

## 1. Instalación (XAMPP)

1. Copiar la carpeta `tienda_huracan` dentro de `C:\xampp\htdocs\`.
2. Abrir el panel de XAMPP y encender **Apache** y **MySQL**.
3. Entrar a `http://localhost/phpmyadmin` → pestaña **Importar** → elegir `tienda_huracan.sql` → **Continuar**.
   Esto crea la base `tienda_huracan`, la tabla `usuarios` y un administrador.
4. Abrir `http://localhost/tienda_huracan/`

**Administrador inicial**
- Correo: `admin@huracan.com`
- Contraseña: `Admin1234` (conviene cambiarla desde *Administrar → Editar*)

Si MySQL tiene otra contraseña o puerto, cambiar los datos en `conexion.php`.

### Fotos de los productos

El catálogo tiene **104 prendas** (tabla `productos`). Sus fotos están en la carpeta **`img/`**,
cada una con el nombre de la prenda (por ejemplo `img/camiseta-titular-2026-regular.jpg`).
En la base, la columna `imagen` guarda ese nombre de archivo.

**Si la carpeta `img/` no está o está vacía**: hacer doble clic en `descargar_fotos.bat` (Windows, con internet).
Crea la carpeta y descarga las 104 fotos. No necesita XAMPP. Después se pueden borrar
`descargar_fotos.bat` y `descargar_fotos.ps1`.

Opcional: guardando `img/logo.png`, `img/escudo.png` o `img/banner.jpg` se reemplazan el logo,
el círculo "CAH" y el banner de rayas.

---

## 2. Estructura de archivos

| Archivo | Para qué sirve |
|---|---|
| `conexion.php` | Conecta PHP con MySQL usando PDO |
| `includes/funciones.php` | Funciones comunes: sesión, escape HTML, token CSRF, control de acceso, validaciones |
| `includes/header.php` / `footer.php` | Barra de navegación y pie de página que comparten todas las páginas |
| `index.php` | Portada: 4 prendas de cada categoría (Juego, Entrenamiento, Salida) |
| `productos.php` | "VER TODO": catálogo con 18 prendas por página (6 páginas) y filtro por categoría |
| `registro.php` | Registro público de usuarios |
| `login.php` | Inicio de sesión |
| `panel.php` | Panel personal (muestra los datos del usuario) |
| `cerrar_sesion.php` | Cierra la sesión |
| `admin_usuarios.php` | Listado de usuarios activos + buscador |
| `admin_nuevo_usuario.php` | El admin registra usuarios (y elige el rol) |
| `editar_usuario.php` | Edición de datos de un usuario |
| `archivar_usuario.php` | Archiva una cuenta (no la borra) |
| `usuarios_archivados.php` | Listado de cuentas archivadas |
| `reactivar_usuario.php` | Reactiva una cuenta archivada |
| `img/` | Fotos de las 104 prendas |
| `css/estilos.css` | Estilos (colores del club: blanco y rojo) |
| `tienda_huracan.sql` | Script para crear la base de datos |

---

## 3. Cómo funciona (explicación general)

1. **Formulario HTML → PHP**: cada formulario envía los datos por `POST` a su propio archivo PHP. PHP los lee con `$_POST`, les saca los espacios con `trim()` y los valida.
2. **PHP → Base de datos**: si los datos son válidos, PHP usa **PDO** con **consultas preparadas** (`prepare()` + `execute()`) para guardar o leer información de la tabla `usuarios`.
3. **Sesiones**: al iniciar sesión correctamente se guardan en `$_SESSION` el id, el nombre y el rol. Cada página privada llama a `requerir_login()` o `requerir_admin()`, que revisan la sesión (y vuelven a consultar la base) antes de mostrar nada.
4. **Archivar en vez de borrar**: la columna `fecha_archivado` vale `NULL` si el usuario está activo. Al archivar se guarda la fecha (`NOW()`); al reactivar se vuelve a `NULL`. El listado de activos filtra con `WHERE fecha_archivado IS NULL`. Un usuario archivado no puede iniciar sesión y, si estaba conectado, se lo desconecta.
5. **Roles**: la columna `rol` puede ser `usuario` o `admin`. Solo los admin ven el menú *Administrar* y pueden entrar a esas páginas.

6. **Catálogo y paginación**: `productos.php` cuenta los productos (`COUNT(*)`), calcula cuántas páginas hay (`ceil(total / 18)`) y trae solo los de la página pedida con `LIMIT 18 OFFSET ...`. Por ejemplo, la página 3 saltea 36 productos (`(3 - 1) * 18`). La categoría que llega por la URL se valida contra una lista (`$CATEGORIAS`) antes de usarla.

### Tabla `productos`

| Campo | Tipo | Detalle |
|---|---|---|
| id | INT UNSIGNED | Clave primaria |
| nombre | VARCHAR(120) | |
| categoria | ENUM('juego','entrenamiento','salida') | |
| precio | INT UNSIGNED | Precio actual |
| precio_anterior | INT UNSIGNED NULL | Si tiene descuento, el precio de antes (se muestra tachado con el % OFF) |
| imagen | VARCHAR(120) | Nombre del archivo de la foto dentro de `img/` |

### Tabla `usuarios`

| Campo | Tipo | Detalle |
|---|---|---|
| id | INT UNSIGNED AUTO_INCREMENT | Clave primaria |
| nombre, apellido | VARCHAR(60) NOT NULL | |
| dni | VARCHAR(10) NOT NULL **UNIQUE** | No se puede repetir |
| email | VARCHAR(120) NOT NULL **UNIQUE** | No se puede repetir |
| password | VARCHAR(255) NOT NULL | Guarda el **hash**, no la contraseña |
| rol | ENUM('usuario','admin') | Por defecto `usuario` |
| fecha_creacion | DATETIME | Se completa sola |
| fecha_archivado | DATETIME NULL | NULL = activo |

---

## 4. Medidas de seguridad implementadas

- **Contraseñas cifradas**: se guardan con `password_hash()` y se comprueban con `password_verify()`. Nunca se muestran ni la contraseña ni el hash.
- **Consultas preparadas con PDO**: los datos del usuario nunca se pegan dentro del SQL, se pasan como parámetros (`?`). Esto evita la **inyección SQL**.
- **Escape de HTML**: todo lo que se muestra en pantalla pasa por la función `e()` (usa `htmlspecialchars`). Si alguien se registra con un nombre como `<script>`, se muestra como texto y no se ejecuta (**XSS**).
- **Token CSRF**: cada formulario lleva un campo oculto con un código secreto guardado en la sesión. Si el código no coincide, la acción se rechaza. Así otra página no puede hacer que el admin archive o edite usuarios sin querer.
- **Archivar/reactivar solo por POST**: no hay links tipo `archivar.php?id=5`, porque un link se puede abrir por error o desde otro sitio.
- **Control de acceso**: las páginas privadas verifican la sesión; las administrativas verifican además el rol `admin` consultando la base (si le sacan el rol a alguien, pierde el acceso al instante).
- **Duplicados**: se controlan en PHP (mensaje claro) y también en la base con `UNIQUE` (por si dos personas se registran al mismo tiempo).
- **Login seguro**: el mismo mensaje para correo inexistente o contraseña mal (no revela qué correos existen) y `session_regenerate_id()` al ingresar, para evitar robo de sesión.
- **Protecciones del admin**: no puede archivarse a sí mismo ni quitarse su propio rol.
- **Errores de conexión**: no se muestran detalles técnicos al usuario.

---

## 5. Pruebas obligatorias (todas verificadas)

| # | Prueba | Cómo hacerla |
|---|---|---|
| 1 | Registrar usuario | *Crear cuenta* con datos válidos → redirige al login con mensaje verde |
| 2 | Rechazo de duplicados | Registrar otra vez el mismo DNI o correo → “Ya existe una cuenta con ese DNI/correo” |
| 3 | Iniciar y cerrar sesión | Ingresar → *Mi cuenta* → *Cerrar sesión* |
| 4 | Panel sin login | Sin sesión, abrir `localhost/tienda_huracan/panel.php` → manda al login |
| 5 | Usuario común sin admin | Con un usuario común, abrir `admin_usuarios.php` → vuelve al panel con “No tenés permisos” |
| 6 | Admin registra/busca/edita | *Administrar* → *+ Registrar usuario*, usar el buscador, *Editar* |
| 7 | Archivados no aparecen | *Archivar* un usuario → desaparece del listado de activos |
| 8 | Consultar y reactivar | *Usuarios archivados* → *Reactivar* |
| 9 | Mensajes claros | Dejar campos vacíos, DNI con letras, contraseñas distintas, etc. |

👉 Sacar una captura de pantalla de cada prueba para la entrega.

---

## 6. Dificultades encontradas y cómo se resolvieron

> ✏️ Esta parte tiene que ser **tuya**. Algunas ideas de cosas que suelen pasar (borrá las que no te pasaron y agregá las tuyas):

- *Error “Access denied” o “Unknown database”* al conectar → faltaba importar el `.sql` o la clave de MySQL era distinta; se corrigió en `conexion.php`.
- *Cómo borrar un usuario sin perder el registro* → se resolvió con la columna `fecha_archivado` en vez de usar `DELETE`.
- *Al editar, el sistema decía que mi propio correo estaba duplicado* → en la búsqueda de duplicados se excluye el id del usuario que se está editando (`AND id <> ?`).
- *Cómo saber si la contraseña es correcta si está cifrada* → no se “descifra”: `password_verify()` compara la escrita con el hash.
- *Los acentos se veían mal* → se usó `utf8mb4` en la base y en la conexión, y `<meta charset="UTF-8">`.
