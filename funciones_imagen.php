<?php
function subir_y_procesar($file_input, $ruta_destino) {
    // CONFIGURACIÓN
    $ancho_maximo = 1200; // Máximo ancho de la imagen final
    $calidad_jpg = 85;
    
    // Ruta de tu marca de agua
    $ruta_watermark = '../assets/img/watermark.png'; 
    
    // Porcentaje del ancho que ocupará la marca (40% es ideal)
    $porcentaje_marca = 0.4; 
    
    // Opacidad (0 a 100). 20 es muy sutil, 50 es visible, 100 es sólido.
    $opacidad = 20; 

    // 1. CARGAR IMAGEN ORIGINAL
    $tipo = $file_input['type'];
    $tmp = $file_input['tmp_name'];
    $imagen = null;

    if ($tipo == 'image/jpeg' || $tipo == 'image/jpg') $imagen = imagecreatefromjpeg($tmp);
    elseif ($tipo == 'image/png') $imagen = imagecreatefrompng($tmp);
    elseif ($tipo == 'image/gif') $imagen = imagecreatefromgif($tmp);
    elseif ($tipo == 'image/webp') $imagen = imagecreatefromwebp($tmp);
    
    if (!$imagen) return false;

    // 2. REDIMENSIONAR IMAGEN PRINCIPAL (Si es muy grande)
    $ancho = imagesx($imagen);
    $alto = imagesy($imagen);
    
    if ($ancho > $ancho_maximo) {
        $nuevo_alto = floor($alto * ($ancho_maximo / $ancho));
        $nueva = imagecreatetruecolor($ancho_maximo, $nuevo_alto);
        
        // Preservar transparencia
        imagealphablending($nueva, false);
        imagesavealpha($nueva, true);
        
        imagecopyresampled($nueva, $imagen, 0, 0, 0, 0, $ancho_maximo, $nuevo_alto, $ancho, $alto);
        $imagen = $nueva;
        $ancho = $ancho_maximo;
        $alto = $nuevo_alto;
    }

    // 3. APLICAR MARCA DE AGUA (Inteligente)
    if (file_exists($ruta_watermark)) {
        $watermark = imagecreatefrompng($ruta_watermark);
        $w_ancho_orig = imagesx($watermark);
        $w_alto_orig = imagesy($watermark);

        // CALCULAR TAMAÑO PROPORCIONAL (La magia está aquí)
        // Queremos que la marca sea el 40% del ancho de la foto
        $nuevo_w_ancho = $ancho * $porcentaje_marca;
        $ratio = $nuevo_w_ancho / $w_ancho_orig;
        $nuevo_w_alto = $w_alto_orig * $ratio;

        // Crear marca redimensionada
        $watermark_redim = imagecreatetruecolor($nuevo_w_ancho, $nuevo_w_alto);
        
        // Fondo transparente para la marca
        imagealphablending($watermark_redim, false);
        imagesavealpha($watermark_redim, true);
        $trans_color = imagecolorallocatealpha($watermark_redim, 0, 0, 0, 127);
        imagefill($watermark_redim, 0, 0, $trans_color);

        // Redimensionar marca
        imagecopyresampled($watermark_redim, $watermark, 0, 0, 0, 0, $nuevo_w_ancho, $nuevo_w_alto, $w_ancho_orig, $w_alto_orig);

        // Calcular posición centrada
        $dest_x = ($ancho - $nuevo_w_ancho) / 2;
        $dest_y = ($alto - $nuevo_w_alto) / 2;

        // FUSIÓN CON OPACIDAD (Transparencia real)
        // Esta función mezcla la imagen con la marca usando el porcentaje de opacidad
        imagecopymerge_alpha($imagen, $watermark_redim, $dest_x, $dest_y, 0, 0, $nuevo_w_ancho, $nuevo_w_alto, $opacidad);

        imagedestroy($watermark);
        imagedestroy($watermark_redim);
    }

    // 4. GUARDAR
    $resultado = false;
    $ext = strtolower(pathinfo($ruta_destino, PATHINFO_EXTENSION));
    
    if ($ext == 'jpg' || $ext == 'jpeg') $resultado = imagejpeg($imagen, $ruta_destino, $calidad_jpg);
    elseif ($ext == 'png') {
        imagesavealpha($imagen, true);
        $resultado = imagepng($imagen, $ruta_destino, 8);
    }
    elseif ($ext == 'webp') {
        imagesavealpha($imagen, true);
        $resultado = imagewebp($imagen, $ruta_destino, 85);
    }

    imagedestroy($imagen);
    return $resultado;
}

// FUNCIÓN AUXILIAR PARA FUSIONAR CON TRANSPARENCIA EN PNG
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
    // Crear un recorte
    $cut = imagecreatetruecolor($src_w, $src_h);
    // Copiar la zona de destino al recorte
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
    // Copiar la marca sobre el recorte
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
    // Fusionar el recorte de vuelta con opacidad
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    imagedestroy($cut);
}
?>