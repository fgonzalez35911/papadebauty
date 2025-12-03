<?php
session_start();
require '../includes/db_connect.php';
require_once '../includes/funciones_imagen.php'; // Motor de imagen

// Configuración
ini_set('max_execution_time', 600);
ini_set('memory_limit', '1024M');

if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

echo "<h1>🚀 Importador Secuencial (Orden Visual)</h1>";
echo "<p>Asignando fotos en orden de lista (Foto 1 -> Juego #1 de la lista), ignorando saltos de ID.</p><hr>";

$dir_origen = '../assets/restaurar/';
$dir_destino = '../assets/uploads/juegos/';

if (!is_dir($dir_origen)) {
    die("<h3 style='color:red'>Error: Subí tus fotos a 'assets/restaurar'.</h3>");
}

// 1. OBTENER JUEGOS EN ORDEN DE ID (1, 2, 4, 8...)
// Esto respeta el orden en que aparecen en tu lista
$sql = "SELECT id, titulo FROM juegos ORDER BY id ASC";
$res = $conn->query($sql);
$juegos = [];
while($row = $res->fetch_assoc()) {
    $juegos[] = $row;
}

echo "<p>Juegos en base de datos: <strong>" . count($juegos) . "</strong></p>";

// 2. OBTENER FOTOS ORDENADAS POR NÚMERO
$archivos = scandir($dir_origen);
$fotos_ordenadas = [];

foreach ($archivos as $f) {
    if ($f == '.' || $f == '..') continue;
    // Extraer número del nombre (1.png -> 1)
    $num = intval(pathinfo($f, PATHINFO_FILENAME));
    if ($num > 0) {
        $fotos_ordenadas[$num] = $f;
    }
}
// Ordenar por clave (1, 2, 3...)
ksort($fotos_ordenadas);

// Reindexar para que sea un array simple [0]=>1.png, [1]=>2.png...
$lista_fotos = array_values($fotos_ordenadas);

echo "<p>Fotos detectadas: <strong>" . count($lista_fotos) . "</strong></p>";

// 3. CRUZAR LISTAS (SECUENCIAL)
$limit = min(count($juegos), count($lista_fotos));
$procesados = 0;

echo "<ul style='font-family:monospace; font-size:12px;'>";

for ($i = 0; $i < $limit; $i++) {
    $juego_actual = $juegos[$i];
    $foto_actual_nombre = $lista_fotos[$i];
    
    $id_juego = $juego_actual['id'];
    $titulo = $juego_actual['titulo'];
    
    // Procesar
    $ruta_origen = $dir_origen . $foto_actual_nombre;
    $ext = pathinfo($foto_actual_nombre, PATHINFO_EXTENSION);
    $nombre_final = $id_juego . "_game_" . time() . "." . $ext;
    $ruta_destino_fisica = $dir_destino . $nombre_final;
    $ruta_bd = "assets/uploads/juegos/" . $nombre_final;

    $fake_file = [
        'type' => mime_content_type($ruta_origen),
        'tmp_name' => $ruta_origen,
        'name' => $foto_actual_nombre
    ];

    if (subir_y_procesar($fake_file, $ruta_destino_fisica)) {
        // Actualizar BD
        $conn->query("UPDATE juegos SET imagen_portada = '$ruta_bd' WHERE id = $id_juego");
        $conn->query("UPDATE juegos_contenido SET imagen = '$ruta_bd' WHERE id_juego = $id_juego");

        echo "<li>✅ Foto <strong>$foto_actual_nombre</strong> asignada a Juego ID <strong>$id_juego</strong> ($titulo)</li>";
        $procesados++;
    } else {
        echo "<li style='color:red'>❌ Error procesando $foto_actual_nombre</li>";
    }
    
    if(ob_get_level() > 0) { ob_flush(); flush(); }
}

echo "</ul>";
echo "<hr><h3>¡Terminado! $procesados juegos actualizados.</h3>";
?>