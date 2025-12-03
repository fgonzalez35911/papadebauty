<?php
session_start();
set_time_limit(0); // Sin límite de tiempo

// Seguridad básica
if (!isset($_SESSION['usuario_id'])) { die("<h1>Acceso Denegado</h1>Debes iniciar sesión."); }

echo "<h1>🛡️ Renombrador Seguro (Coincidencia Exacta)</h1>";
echo "<p>Este script NO adivina el orden. Solo renombra si el número coincide exactamente.</p><hr>";

// RUTAS (En minúscula como dijiste)
$dir_referencia = '../assets/fede/';  // Nombres raros del servidor
$dir_objetivo   = '../assets/fede2/'; // Tus fotos limpias (1.png, 2.jpg)

// Validar carpetas
if (!is_dir($dir_referencia)) die("<h3 style='color:red'>Error: No existe la carpeta 'assets/fede'</h3>");
if (!is_dir($dir_objetivo)) die("<h3 style='color:red'>Error: No existe la carpeta 'assets/fede2'</h3>");

// 1. CREAR DICCIONARIO DE REFERENCIAS
// Guardamos:  ID [7]  =>  Nombre Real [12345_7.png]
$mapa_server = [];
$archivos_server = scandir($dir_referencia);

foreach ($archivos_server as $archivo) {
    if ($archivo == '.' || $archivo == '..') continue;
    
    // Buscamos el número al final del nombre (ej: ..._7.png)
    if (preg_match('/[_-](\d+)\./', $archivo, $matches)) {
        $id = intval($matches[1]); // El número "7"
        $mapa_server[$id] = $archivo;
    }
}

echo "<div style='background:#e3f2fd; padding:10px; border-radius:5px; margin-bottom:20px;'>";
echo "<strong>Referencias encontradas en 'fede':</strong> " . count($mapa_server);
echo "</div>";

// 2. PROCESAR TUS FOTOS EN FEDE2
$archivos_mios = scandir($dir_objetivo);
$contador_ok = 0;
$contador_skip = 0;

echo "<ul style='font-family:monospace; font-size:13px;'>";

foreach ($archivos_mios as $mi_foto) {
    if ($mi_foto == '.' || $mi_foto == '..') continue;

    // Obtenemos el número de tu foto (ej: "7.png" -> 7)
    $nombre_sin_ext = pathinfo($mi_foto, PATHINFO_FILENAME);
    
    // Solo procesamos si el nombre es un número puro
    if (is_numeric($nombre_sin_ext)) {
        $mi_id = intval($nombre_sin_ext);

        // ¿EXISTE ESTE ID EN EL MAPA DEL SERVIDOR?
        if (isset($mapa_server[$mi_id])) {
            // ¡SÍ! Coincidencia exacta encontrada
            $nombre_final = $mapa_server[$mi_id]; // El nombre largo que queremos
            
            $ruta_actual = $dir_objetivo . $mi_foto;
            $ruta_nueva  = $dir_objetivo . $nombre_final;
            
            // Renombrar (solo si no se llama ya así)
            if ($mi_foto !== $nombre_final) {
                if (rename($ruta_actual, $ruta_nueva)) {
                    echo "<li style='color:green;'>✅ ID <strong>$mi_id</strong>: <code>$mi_foto</code> ➔ <code>$nombre_final</code></li>";
                    $contador_ok++;
                } else {
                    echo "<li style='color:red;'>❌ ID <strong>$mi_id</strong>: Error de permisos al renombrar.</li>";
                }
            } else {
                echo "<li style='color:blue;'>ℹ️ ID <strong>$mi_id</strong>: Ya tenía el nombre correcto.</li>";
            }

        } else {
            // NO EXISTE EN EL SERVIDOR -> LO SALTAMOS (No rompemos nada)
            echo "<li style='color:orange;'>⚠️ ID <strong>$mi_id</strong> ($mi_foto): Saltado. No encontré un archivo <code>_<i>$mi_id</i>.ext</code> en la carpeta 'fede'.</li>";
            $contador_skip++;
        }
    }
}

echo "</ul>";
echo "<hr><h3>Resultado:</h3>";
echo "<p>Renombrados correctamente: <strong>$contador_ok</strong></p>";
echo "<p>Saltados (sin coincidencia): <strong>$contador_skip</strong></p>";

echo "<p style='margin-top:20px;'><strong>Próximo paso:</strong> Mover todo el contenido de <code>assets/fede2</code> a la carpeta real de juegos.</p>";
?>