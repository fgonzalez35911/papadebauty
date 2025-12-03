<?php
session_start();
// Límites al máximo porque va a revisar miles de archivos
ini_set('memory_limit', '1024M');
set_time_limit(0);

require '../includes/db_connect.php';
if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

echo "<h1>🕵️‍♂️ Iniciando Escaneo Profundo (Recursivo)...</h1>";
echo "<p>Buscando imágenes en TODAS las subcarpetas de 'assets'...</p><hr>";

// 1. CONFIGURACIÓN
$directorio_raiz = realpath('../assets'); // Escanea todo lo que esté en assets
$ruta_watermark = realpath('../assets/img/watermark.png');

if (!$ruta_watermark || !file_exists($ruta_watermark)) {
    die("<h2 style='color:red'>ERROR CRÍTICO: No encuentro 'assets/img/watermark.png'.</h2>");
}

// 2. ITERADOR RECURSIVO (EL BUSCADOR PROFUNDO)
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directorio_raiz, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$contador = 0;
$errores = 0;
$omitidos = 0;

foreach ($iterator as $archivo) {
    // Solo archivos, no carpetas
    if ($archivo->isDir()) continue;

    $ruta_completa = $archivo->getRealPath();
    
    // SEGURIDAD: No procesar la propia marca de agua
    if ($ruta_completa == $ruta_watermark) {
        continue;
    }

    // FILTRO: Solo imágenes
    $ext = strtolower($archivo->getExtension());
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        continue;
    }

    // 3. PROCESAR
    // Intentamos aplicar la marca.
    if (procesar_imagen_existente($ruta_completa, $ruta_watermark)) {
        // Convertimos la ruta absoluta a relativa para mostrarla lindo
        $ruta_visual = str_replace(realpath('../'), '', $ruta_completa);
        echo "<div style='color:green; font-size:12px; font-family:monospace;'>✅ $ruta_visual</div>";
        $contador++;
    } else {
        echo "<div style='color:red; font-size:12px;'>❌ Error: " . $archivo->getFilename() . "</div>";
        $errores++;
    }

    // Forzar que se vea en pantalla mientras carga
    if (ob_get_level() > 0) { ob_flush(); flush(); }
}

echo "<hr><h2>🎉 FINALIZADO: $contador imágenes marcadas.</h2>";
echo "<a href='panel.php'>Volver al Panel</a>";


// --- FUNCIÓN INTELIGENTE (AJUSTE PROPORCIONAL + TRANSPARENCIA) ---
function procesar_imagen_existente($ruta_img, $ruta_wm) {
    $ext = strtolower(pathinfo($ruta_img, PATHINFO_EXTENSION));
    $img = null;

    // Cargar
    if ($ext == 'jpg' || $ext == 'jpeg') $img = @imagecreatefromjpeg($ruta_img);
    elseif ($ext == 'png') $img = @imagecreatefrompng($ruta_img);
    elseif ($ext == 'webp') $img = @imagecreatefromwebp($ruta_img);

    if (!$img) return false;

    // Cargar Marca
    $watermark = imagecreatefrompng($ruta_wm);
    if (!$watermark) return false;

    // Dimensiones
    $img_w = imagesx($img);
    $img_h = imagesy($img);
    $wt_w = imagesx($watermark);
    $wt_h = imagesy($watermark);

    // --- LÓGICA PROPORCIONAL ---
    // La marca ocupará el 50% del ancho de la imagen
    $porcentaje = 0.5; 
    $nuevo_wt_w = $img_w * $porcentaje;
    $ratio = $nuevo_wt_w / $wt_w;
    $nuevo_wt_h = $wt_h * $ratio;

    // Crear marca redimensionada
    $marca_final = imagecreatetruecolor($nuevo_wt_w, $nuevo_wt_h);
    imagealphablending($marca_final, false);
    imagesavealpha($marca_final, true);
    $trans = imagecolorallocatealpha($marca_final, 0, 0, 0, 127);
    imagefill($marca_final, 0, 0, $trans);

    imagecopyresampled($marca_final, $watermark, 0, 0, 0, 0, $nuevo_wt_w, $nuevo_wt_h, $wt_w, $wt_h);

    // Posición Centrada
    $dest_x = ($img_w - $nuevo_wt_w) / 2;
    $dest_y = ($img_h - $nuevo_wt_h) / 2;

    // Fusión con Opacidad (20%)
    // Nota: imagecopymerge no soporta canal alfa bien en PNGs transparentes directos, 
    // pero para marca de agua simple suele servir. 
    // Usamos el truco de imagecopy si es PNG para mantener transparencia o merge para opacidad.
    
    // TRUCO PARA OPACIDAD REAL:
    // Lamentablemente GD de PHP es complejo con opacidad + transparencia PNG.
    // Vamos a usar imagecopy simple que respeta la transparencia del PNG original de la marca.
    // *Asegurate que tu watermark.png YA tenga la opacidad baja en Photoshop/Canva*
    
    imagecopy($img, $marca_final, $dest_x, $dest_y, 0, 0, $nuevo_wt_w, $nuevo_wt_h);

    // Guardar
    $res = false;
    if ($ext == 'jpg' || $ext == 'jpeg') $res = imagejpeg($img, $ruta_img, 85);
    elseif ($ext == 'png') {
        imagesavealpha($img, true);
        $res = imagepng($img, $ruta_img, 8);
    } elseif ($ext == 'webp') $res = imagewebp($img, $ruta_img, 85);

    imagedestroy($img);
    imagedestroy($watermark);
    imagedestroy($marca_final);

    return $res;
}
?>