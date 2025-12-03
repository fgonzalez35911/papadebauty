<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

echo "<h1>🔧 Corrección de Desfase (+2)</h1>";
echo "<p>Buscando imágenes en <code>assets/img/juegos/</code>...</p>";
echo "<p>Regla: Si la imagen termina en <strong>_22</strong>, se asigna al Juego <strong>20</strong>.</p><hr>";

// CARPETA DONDE ESTÁN ESAS FOTOS RARAS
// (Dijiste 'assets/img/juegos', ajustá si es otra)
$dir_objetivo = '../assets/img/juegos/';
$ruta_bd_base = 'assets/img/juegos/'; 

if (!is_dir($dir_objetivo)) {
    die("<h3 style='color:red'>Error: No encuentro la carpeta $dir_objetivo</h3>");
}

$archivos = scandir($dir_objetivo);
$actualizados = 0;

echo "<ul style='font-family:monospace; font-size:13px;'>";

foreach ($archivos as $archivo) {
    if ($archivo == '.' || $archivo == '..') continue;
    
    // BUSCAMOS EL NÚMERO AL FINAL DEL NOMBRE
    // Regex: busca un guion bajo, seguido de números, seguido del punto y extensión
    if (preg_match('/_(\d+)\.(jpg|jpeg|png|webp)$/i', $archivo, $coincidencias)) {
        
        $id_imagen = intval($coincidencias[1]); // El número de la foto (ej: 22)
        
        // APLICAMOS LA LÓGICA DEL DESFASE (-2)
        $id_juego_destino = $id_imagen - 2; // (ej: 22 - 2 = 20)
        
        // SOLO TOCAMOS DEL 20 AL 84 (SEGURIDAD)
        if ($id_juego_destino >= 20 && $id_juego_destino <= 84) {
            
            $ruta_final = $ruta_bd_base . $archivo;
            
            // Actualizamos Portada
            $sql = "UPDATE juegos SET imagen_portada = '$ruta_final' WHERE id = $id_juego_destino";
            $conn->query($sql);
            
            // Actualizamos Contenido interno (para que se vea al jugar)
            // Nota: Esto asume que esa foto es la principal del juego.
            $sql2 = "UPDATE juegos_contenido SET imagen = '$ruta_final' WHERE id_juego = $id_juego_destino";
            $conn->query($sql2);

            echo "<li style='color:green;'>✅ Foto <strong>..._$id_imagen</strong> asignada a Juego ID <strong>$id_juego_destino</strong></li>";
            $actualizados++;
        } else {
            // Si da menos de 20 o más de 84, lo ignoramos para no romper lo que ya hiciste
            // echo "<li style='color:#ccc;'>Ignorado: Foto $id_imagen (Juego calculado $id_juego_destino fuera de rango)</li>";
        }
    }
}

echo "</ul>";
echo "<hr><h3>¡Listo! Se actualizaron $actualizados juegos.</h3>";
echo "<a href='listar_juegos.php' class='btn-grande' style='background:blue; color:white; padding:10px; text-decoration:none;'>Ver Listado</a>";
?>