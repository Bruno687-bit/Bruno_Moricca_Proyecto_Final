# =========================================================
# descargar_fotos.ps1 - Crea la carpeta "img" y descarga las fotos
# de las 104 prendas desde la tienda oficial.
# Se ejecuta con doble clic en "descargar_fotos.bat".
# =========================================================
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$ProgressPreference = 'SilentlyContinue'

$carpeta = Join-Path $PSScriptRoot 'img'
if (-not (Test-Path $carpeta)) { New-Item -ItemType Directory -Path $carpeta | Out-Null }

# nombre del archivo en img\  ,  ruta de la foto en la tienda
$fotos = @(
    @('short-rojo-kids-2026.jpg', 'archivos/productos/17932067420715_K235284SW-KA24C-1 (1).jpg'),
    @('camiseta-suplente-kids-2026.jpg', 'archivos/productos/17932048679985_WhatsApp Image 2026-10-28 at 13.27.26.jpeg'),
    @('camiseta-violeta-de-arquero-2025-sin-sponsor.jpg', 'archivos/productos/17915633696324_WhatsApp Image 2026-10-09 at 13.04.08.jpeg'),
    @('camiseta-blanca-regular-2025-sin-sponsor.jpg', 'archivos/productos/17915631861086_WhatsApp Image 2026-10-09 at 13.04.07 (1).jpeg'),
    @('camiseta-roja-regular-2025-sin-sponsor.jpg', 'archivos/productos/17915628696416_WhatsApp Image 2026-10-09 at 13.04.07 (2).jpeg'),
    @('camiseta-verde-de-arquero-2025-sin-sponsor.jpg', 'archivos/productos/17915626451902_WhatsApp Image 2026-10-09 at 13.04.07.jpeg'),
    @('short-de-entrenamiento-2026.jpg', 'archivos/productos/17901895120493_196652_FullHD.jpg'),
    @('camiseta-dama-suplente-2026.jpg', 'archivos/productos/17901766136203_K2382D6WWZ-KA24C-1.jpg'),
    @('short-negro-alternativo-2026.jpg', 'archivos/productos/17897636289488_K235256BWZ-KA73-1.jpg'),
    @('camiseta-negra-alternativa-regular-2026.jpg', 'archivos/productos/17897628976871_K2382C57WZ-KA73-1.jpg'),
    @('short-titular-blanco-nino-2026.jpg', 'archivos/productos/17888815091085_K235256BWZ-KA00B-1.jpg'),
    @('short-fucsia-de-arquero-2026.jpg', 'archivos/productos/17875994022169_K235256BWZ-KV27-1.jpg'),
    @('short-celeste-arquero-2026.jpg', 'archivos/productos/17875990271773_K235256BWZ-KA03D-1.jpg'),
    @('short-naranja-arquero-2026.jpg', 'archivos/productos/17875988102031_K235256BWZ-KALV-1.jpg'),
    @('camiseta-naranja-de-arquero-2026.jpg', 'archivos/productos/17875835553057_K2382C58WZ-KALV-1.jpg'),
    @('camiseta-fucsia-de-arquero-2026.jpg', 'archivos/productos/17875833475166_K2382C58WZ-KV27-1.jpg'),
    @('camiseta-celeste-arquero-2026.jpg', 'archivos/productos/17875824735745_K2382C58WZ-KA03D-1.jpg'),
    @('short-arquero-naranja-nino.jpg', 'archivos/productos/17866322549950_Gemini_Generated_Image_9fao9k9fao9k9fao.png'),
    @('chomba-retro-2026.jpg', 'archivos/productos/17864731032387_K2382M82W-KA24C-1_JPG.jpg'),
    @('short-de-juego-suplente-2026.jpg', 'archivos/productos/17818908540065_K235284SW-KA24C-1 (1).jpg'),
    @('short-de-juego-titular-2026.jpg', 'archivos/productos/17810269930532_K235256BWZ-KA00B-1.jpg'),
    @('camiseta-suplente-2026-regular.jpg', 'archivos/productos/17810266661393_K2382D6WWZ-KA24C-1.jpg'),
    @('campera-de-invierno-negra-2026.jpg', 'archivos/productos/17786942836793_Campera abrigo negra.jpeg'),
    @('camiseta-negra-pre-match-2026.jpg', 'archivos/productos/17786938971710_pre 1.jpeg'),
    @('rompeviento-negro-2026.jpg', 'archivos/productos/17782509005994_Gemini_Generated_Image_m5ycksm5ycksm5yc (1).png'),
    @('camperon-huracan-2026.jpg', 'archivos/productos/17779339771790_camperon fondo blanco.png'),
    @('camiseta-titular-2026-regular.jpg', 'archivos/productos/17775828209699_K2372H1GW-KA00B-1.jpg'),
    @('campera-retro-prematch-2026.jpg', 'archivos/productos/17798180444768_Pre 1 hd.png'),
    @('musculosa-entrenamiento-coral-2026.jpg', 'archivos/productos/17768942602099_HELIUM MASCO PRO HURACAN_2_Colorway 1_Front.png'),
    @('musculosa-entrenamiento-v-2026.jpg', 'archivos/productos/17768935887584_HELIUM MASCO PRO HURACAN_2_A01 CYBER BLUE-DARK MOSS_Front_1.png'),
    @('remera-entrenamiento-coral-2026.jpg', 'archivos/productos/17768929711483_HELIUM MOKIBA PRO HURACAN_2_Colorway 7_Front.png'),
    @('remera-entrenamiento-v-2026.jpg', 'archivos/productos/17768924434122_HELIUM MOKIBA PRO HURACAN_2_Colorway 8_Front.png'),
    @('buzo-entrenamiento-medio-cierre-coral-2026.jpg', 'archivos/productos/17768920753565_HELIUM MUZIRA PRO HURACAN_2_Colorway 4_Front.png'),
    @('buzo-entrenamiento-medio-cierre-v-2026.jpg', 'archivos/productos/17768916558202_HELIUM MUZIRA PRO HURACAN_2_Colorway 8_Front.png'),
    @('pantalon-de-entrenamiento-2026.jpg', 'archivos/productos/17768912727752_HELIUM MIRAGI PRO HURACAN 2_Front.png'),
    @('bermuda-de-salida-2026.jpg', 'archivos/productos/17755726901707_K2352569W-KA73-1.jpg'),
    @('pantalon-negro-salida-2026.jpg', 'archivos/productos/17779353295237_lonpa hd.png'),
    @('campera-salida-2026.jpg', 'archivos/productos/17755717490291_K2382C59W-KA73-1.jpg'),
    @('polo-salida-negra-2026.jpg', 'archivos/productos/17755711788999_K2382M2UW-KA73-1.jpg'),
    @('polo-salida-blanca-2026.jpg', 'archivos/productos/17755709397631_K2382M2UW-KA00B-1.jpg'),
    @('remera-salida-negra-2026.jpg', 'archivos/productos/17755705660753_K235256CW-KA73-1.jpg'),
    @('remera-salida-blanca-2026.jpg', 'archivos/productos/17755698794841_K235256CW-KA00B-1.jpg'),
    @('camiseta-alternativa-2025-slim.jpg', 'archivos/productos/17734169583336_ALTERNATIVA CON JELUZ.jpg'),
    @('edicion-limitada-huracan-kombat-xxv.jpg', 'archivos/productos/17625285906250_1.jpg'),
    @('camiseta-lady-roja-regular-2025.jpg', 'archivos/productos/17564866758838_roja jeluz.jpg'),
    @('camiseta-lady-blanca-regular-2025.jpg', 'archivos/productos/17564868392268_WhatsApp Image 2025-08-29 at 13.55.17.jpeg'),
    @('chomba-de-salida-roja-2024.jpg', 'archivos/productos/17546802254070_WhatsApp Image 2025-08-08 at 16.01.02.jpeg'),
    @('chomba-de-salida-blanca-2024.jpg', 'archivos/productos/17546800975403_WhatsApp Image 2025-08-08 at 16.03.01.jpeg'),
    @('short-kids-arquero-2025.jpg', 'archivos/productos/17581283310492_ef8eb197-d2dc-4944-bb55-71fdd9bd8550.jpg'),
    @('short-kids-rojo-2025.jpg', 'archivos/productos/17544915663844_17395625664600_WhatsApp Image 2025-02-14 at 16.23.06.jpeg'),
    @('short-kids-blanco-2025.jpg', 'archivos/productos/17546803313145_WhatsApp Image 2025-08-08 at 16.01.03.jpeg'),
    @('camiseta-titular-kids-2025.jpg', 'archivos/productos/17497348658987_titular.jpeg'),
    @('camiseta-roja-kids-2025.jpg', 'archivos/productos/17495858241103_ROJA CON JELUZ.jpg'),
    @('camiseta-alternativa-kids-2025.jpg', 'archivos/productos/17492402269258_ALTERNATIVA CON JELUZ.jpg'),
    @('camperon-rojo-2025.jpg', 'archivos/productos/17466269555569_K2322349W-KS03-1.JPG'),
    @('campera-abrigo-salida-2025.jpg', 'archivos/productos/17465478390805_K232234BW-KF67-3.JPG'),
    @('campera-rompevientos-beige-2025.jpg', 'archivos/productos/17465475029752_K2322348W-KA58-3.JPG'),
    @('short-verde-de-arquero-2025.jpg', 'archivos/productos/17448091823562_WhatsApp Image 2025-04-15 at 17.19.50 (2).jpeg'),
    @('short-de-arquero-violeta-2025.jpg', 'archivos/productos/17447242581014_K2372718W-KC74-1.JPG'),
    @('short-amarillo-de-arquero-2025.jpg', 'archivos/productos/17447238842229_k2372718W-KB83A-1.JPG'),
    @('campera-de-entrenamiento-pre-match-2025.jpg', 'archivos/productos/17442881402048_IMG-20250409-WA0087.jpg'),
    @('camiseta-violeta-de-arquero-2025.jpg', 'archivos/productos/17436896265538_K2361y2LW-KC74-1.JPG'),
    @('camiseta-amarilla-de-arquero-2025.jpg', 'archivos/productos/17436886465782_K2361y2LW-KB83A-1.JPG'),
    @('camiseta-verde-de-arquero-2025.jpg', 'archivos/productos/17435398926200_K2361y2LW-KA20-1.JPG'),
    @('camiseta-pre-match-2025.jpg', 'archivos/productos/17418694344277_IMG-20250312-WA0089.jpg'),
    @('medias-de-juego-alternativa-2025.jpg', 'archivos/productos/17407757070536_WhatsApp Image 2025-02-28 at 17.45.40 (2).jpeg'),
    @('short-de-juego-alternativo-2025.jpg', 'archivos/productos/17407757864772_WhatsApp Image 2025-02-28 at 17.45.41.jpeg'),
    @('camiseta-alternativa-2025-regular.jpg', 'archivos/productos/17434513713560_ALTERNATIVA CON JELUZ.jpg'),
    @('remera-salida-verde-2025.jpg', 'archivos/productos/17405829889663_K2322B8SW-KY10-1.jpg'),
    @('remera-salida-roja-2025.jpg', 'archivos/productos/17405827363131_K2322B8SW-KS03-1.jpg'),
    @('polo-salida-roja-2025.jpg', 'archivos/productos/17405817470270_K237232BW-KS03-1.jpg'),
    @('polo-salida-verde-2025.jpg', 'archivos/productos/17405813002733_K237232BW-KY10-1.jpg'),
    @('bermuda-de-salida-2025.jpg', 'archivos/productos/17405802777222_K2372719W-KY10-1.jpg'),
    @('pantalon-verde-salida-2025.jpg', 'archivos/productos/17405788248719_K2322E13W-KY10-1.jpg'),
    @('campera-de-salida-cuello-mao-2025.jpg', 'archivos/productos/17405782336130_K2382F1SW-KY10-1.jpg'),
    @('short-de-juego-rojo-2025.jpg', 'archivos/productos/17395625664574_WhatsApp Image 2025-02-14 at 16.23.06 (1).jpeg'),
    @('short-de-juego-blanco-2025.jpg', 'archivos/productos/17395619540982_WhatsApp Image 2025-02-14 at 16.23.07.jpeg'),
    @('camiseta-roja-regular-2025.jpg', 'archivos/productos/17424845692463_ROJA CON JELUZ (1).jpg'),
    @('camiseta-blanca-regular-2025.jpg', 'archivos/productos/17433586064870_WhatsApp Image 2025-03-30 at 14.39.33.jpeg'),
    @('pantalon-rojo-de-entrenamiento-2025.jpg', 'archivos/productos/17392871401911_K2322E12W-KF67-1.jpg'),
    @('short-entrenamiento-2025.jpg', 'archivos/productos/17392869453567_K2372717W-KF67-1.jpg'),
    @('buzo-de-entrenamiento-medio-cierre-rojo-2025.jpg', 'archivos/productos/17392866608540_K2382F1RW-KF67-1.jpg'),
    @('buzo-de-entrenamiento-medio-cierre-beige-2025.jpg', 'archivos/productos/17392855546861_K2382F1RW-KA58-1.jpg'),
    @('musculosa-roja-2025.jpg', 'archivos/productos/17392839272776_K2361Y2IW-KF67-1.jpg'),
    @('musculosa-beige-2025.jpg', 'archivos/productos/17392213262092_Frontal.jpg'),
    @('remera-de-entrenamiento-roja-2025.jpg', 'archivos/productos/17392210141854_Frontal.jpg'),
    @('remera-de-entrenamiento-2025-beige.jpg', 'archivos/productos/17392162201381_Frontal.jpg'),
    @('musculosa-gris-de-entrenamiento-huracan-2024.jpg', 'archivos/productos/17388571426085_1.3'),
    @('camiseta-blanca-kids-2024.jpg', 'archivos/productos/17325526582372_WhatsApp Image 2024-11-25 at 13.32.33 (1).jpeg'),
    @('remera-blanca-de-salida-2024.jpg', 'archivos/productos/17291109121267_1.jpg'),
    @('remera-roja-de-salida-2024.jpg', 'archivos/productos/17291108033350_1.jpg'),
    @('pantalon-de-salida-rojo-2024.jpg', 'archivos/productos/17257468586641_1.jpg'),
    @('bermuda-de-salida-2024.jpg', 'archivos/productos/17260169042901_1.jpg'),
    @('camiseta-roja-regular-sin-sponsor-2024.jpg', 'archivos/productos/17212242287154_Camiseta roja.jpg'),
    @('camiseta-blanca-regular-2024-sin-sponsor.jpg', 'archivos/productos/17182268923435_blanca sin sponsor.jpg'),
    @('camiseta-arquero-lila-2024.jpg', 'archivos/productos/17128724357639_1.jpg'),
    @('bolso-huracan.jpg', 'archivos/productos/17091597802663_bolso 1.jpg'),
    @('mochila-huracan.jpg', 'archivos/productos/17091596840755_1.jpg'),
    @('short-rojo-titular-2024.jpg', 'archivos/productos/17073196733956_4.jpg'),
    @('camiseta-roja-slim-2024.jpg', 'archivos/productos/17634092469265_17212242287154_Camiseta roja.jpg'),
    @('remera-negra-conmemorativa-1973.jpg', 'archivos/productos/16950691182584_1.jpg'),
    @('medias-de-arquero-violetas-2023.jpg', 'archivos/productos/16835448081009_Violeta 1.jpg'),
    @('medias-de-arquero-rosas-2023.jpg', 'archivos/productos/16835447646266_Rosa 1.jpg'),
    @('medias-de-arquero-celestes-2023.jpg', 'archivos/productos/16835447234966_Celeste 1.jpg')
)

$ok = 0; $ya = 0; $fallas = @()
$n = 0
foreach ($f in $fotos) {
    $n++
    $destino = Join-Path $carpeta $f[0]
    if ((Test-Path $destino) -and ((Get-Item $destino).Length -gt 0)) { $ya++; continue }

    $ruta = [uri]::EscapeDataString($f[1]).Replace('%20', '+')
    $url  = "https://tienda.cahuracan.com/gd.php?img=$ruta&width=500&height=560"
    try {
        Invoke-WebRequest -Uri $url -OutFile $destino -UseBasicParsing -TimeoutSec 60
        # Comprobamos que sea una imagen (JPG empieza con FF D8, PNG con 89 50)
        $b = [System.IO.File]::ReadAllBytes($destino)
        if ($b.Length -gt 4 -and (($b[0] -eq 0xFF -and $b[1] -eq 0xD8) -or ($b[0] -eq 0x89 -and $b[1] -eq 0x50))) {
            $ok++
            Write-Host ("[{0}/{1}] OK  {2}" -f $n, $fotos.Count, $f[0]) -ForegroundColor Green
        } else {
            Remove-Item $destino -ErrorAction SilentlyContinue
            $fallas += $f[0]
            Write-Host ("[{0}/{1}] ERROR  {2}" -f $n, $fotos.Count, $f[0]) -ForegroundColor Red
        }
    } catch {
        Remove-Item $destino -ErrorAction SilentlyContinue
        $fallas += $f[0]
        Write-Host ("[{0}/{1}] ERROR  {2}" -f $n, $fotos.Count, $f[0]) -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Descargadas: $ok   Ya estaban: $ya   Con error: $($fallas.Count)   (de $($fotos.Count))" -ForegroundColor Cyan
if ($fallas.Count -gt 0) {
    Write-Host "Revisa tener internet y volve a hacer doble clic en descargar_fotos.bat (solo baja las que faltan)." -ForegroundColor Yellow
} else {
    Write-Host "Listo! Las fotos estan en la carpeta img." -ForegroundColor Green
}
