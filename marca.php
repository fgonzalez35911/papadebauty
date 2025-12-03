<?php
session_start();
// Configuración para proceso pesado
ini_set('memory_limit', '1024M');
set_time_limit(0);

require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

echo "<h1>🔄 Procesamiento Final (Marca de Agua + Conexión)</h1>";
echo "<p>Buscando archivos numéricos (ej: <code>1.png</code>) en <code>assets/img/juegos/</code>...</p><hr>";

// 1. CONFIGURACIÓN
$dir_objetivo = '../assets/img/juegos/'; // Donde subiste las fotos
$ruta_watermark = '../assets/img/watermark.png'; // Tu marca de agua
$ruta_bd_base = 'assets/img/juegos/'; // Ruta para guardar en la BD

if (!file_exists($ruta_watermark)) {
    die("<h2 style='color:red'>ERROR: No encuentro 'assets/img/watermark.png'. Subilo primero.</h2>");
}

if (!is_dir($dir_objetivo)) {
    die("<h2 style='color:red'>ERROR: No encuentro la carpeta '$dir_objetivo'.</h2>");
}

// 2. PROCESAMIENTO
$contador = 0;
$errores = 0;

// Obtenemos la lista de archivos
$archivos = scandir($dir_objetivo);

foreach ($archivos as $archivo) {
    if ($archivo == '.' || $archivo == '..') continue;

    // Obtenemos el nombre sin extensión (ej: "1")
    $nombre_sin_ext = pathinfo($archivo, PATHINFO_FILENAME);
    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

    // Solo procesamos si el nombre es un NÚMERO PURO (1, 2, 84...) y es imagen
    if (is_numeric($nombre_sin_ext) && in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        
        $id_juego = intval($nombre_sin_ext);
        $ruta_completa = $dir_objetivo . $archivo;

        // APLICAR MARCA DE AGUA
        if (aplicar_marca_final($ruta_completa, $ruta_watermark)) {
            
            // ACTUALIZAR BASE DE DATOS (Conectar ID Juego con Archivo)
            $ruta_final_bd = $ruta_bd_base . $archivo;
            
            // Actualizar Portada
            $conn->query("UPDATE juegos SET imagen_portada = '$ruta_final_bd' WHERE id = $id_juego");
            
            // Actualizar Contenido Interno
            $conn->query("UPDATE juegos_contenido SET imagen = '$ruta_final_bd' WHERE id_juego = $id_juego");

            echo "<div style='color:green; font-family:monospace;'>✅ ID $id_juego: Procesada y conectada (<code>$archivo</code>)</div>";
            $contador++;
        } else {
            echo "<div style='color:red;'>❌ Error al procesar imagen: $archivo</div>";
            $errores++;
        }

        // Forzar salida visual
        if(ob_get_level() > 0) { ob_flush(); flush(); }
    }
}

echo "<hr><h2>¡Listo!</h2>";
echo "<p>Imágenes procesadas: <strong>$contador</strong></p>";
echo "<a href='panel.php' class='btn-grande' style='background:blue; color:white; padding:10px; text-decoration:none;'>Volver al Panel</a>";


// --- FUNCIÓN DE MARCA DE AGUA (40% Ancho, 20% Opacidad) ---
function aplicar_marca_final($ruta_img, $ruta_wm) {
    $ext = strtolower(pathinfo($ruta_img, PATHINFO_EXTENSION));
    $img = null;

    if ($ext == 'jpg' || $ext == 'jpeg') $img = @imagecreatefromjpeg($ruta_img);
    elseif ($ext == 'png') $img = @imagecreatefrompng($ruta_img);
    elseif ($ext == 'webp') $img = @imagecreatefromwebp($ruta_img);

    if (!$img) return false;

    $watermark = imagecreatefrompng($ruta_wm);
    if (!$watermark) return false;

    // Dimensiones
    $img_w = imagesx($img);
    $img_h = imagesy($img);
    $wt_w = imagesx($watermark);
    $wt_h = imagesy($watermark);

    // --- CÁLCULO DE TAMAÑO PROPORCIONAL (40% del ancho) ---
    $porcentaje = 0.4; 
    $nuevo_wt_w = $img_w * $porcentaje;
    $ratio = $nuevo_wt_w / $wt_w;
    $nuevo_wt_h = $wt_h * $ratio;

    // Crear marca redimensionada transparente
    $marca_final = imagecreatetruecolor($nuevo_wt_w, $nuevo_wt_h);
    imagealphablending($marca_final, false);
    imagesavealpha($marca_final, true);
    $trans = imagecolorallocatealpha($marca_final, 0, 0, 0, 127);
    imagefill($marca_final, 0, 0, $trans);
    
    imagecopyresampled($marca_final, $watermark, 0, 0, 0, 0, $nuevo_wt_w, $nuevo_wt_h, $wt_w, $wt_h);

    // Posición Centrada
    $dest_x = ($img_w - $nuevo_wt_w) / 2;
    $dest_y = ($img_h - $nuevo_wt_h) / 2;

    // FUSIÓN CON OPACIDAD (20%)
    // El último parámetro es la opacidad (0-100)
    imagecopymerge($img, $marca_final, $dest_x, $dest_y, 0, 0, $nuevo_wt_w, $nuevo_wt_h, 20);

    // Guardar
    $res = false;
    if ($ext == 'jpg' || $ext == 'jpeg') $res = imagejpeg($img, $ruta_img, 90);
    elseif ($ext == 'png') {
        imagesavealpha($img, true);
        $res = imagepng($img, $ruta_img, 9);
    } elseif ($ext == 'webp') $res = imagewebp($img, $ruta_img, 90);

    imagedestroy($img);
    imagedestroy($watermark);
    imagedestroy($marca_final);

    return $res;
}
?>