<?php
// admin/actualizar_bd_full.php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

echo "<h1>🛠️ Actualización de Base de Datos (Nivel Experto)</h1>";

// Columnas a agregar en juegos_contenido
$nuevas_columnas = [
    'texto_pregunta' => "TEXT AFTER audio", // La pregunta específica de esa ficha
    'img_correcta' => "VARCHAR(255) AFTER palabra_correcta",
    'img_distractor1' => "VARCHAR(255) AFTER distractor1",
    'img_distractor2' => "VARCHAR(255) AFTER distractor2",
    'img_distractor3' => "VARCHAR(255) AFTER distractor3"
];

echo "<ul>";
foreach ($nuevas_columnas as $col => $def) {
    $check = $conn->query("SHOW COLUMNS FROM juegos_contenido LIKE '$col'");
    if ($check->num_rows == 0) {
        if ($conn->query("ALTER TABLE juegos_contenido ADD COLUMN $col $def")) {
            echo "<li style='color:green'>✅ Columna <strong>$col</strong> creada.</li>";
        } else {
            echo "<li style='color:red'>❌ Error creando $col: " . $conn->error . "</li>";
        }
    } else {
        echo "<li style='color:gray'>ℹ️ Columna $col ya existe.</li>";
    }
}
echo "</ul>";

echo "<hr><p>Base de datos lista para soportar imágenes en respuestas y títulos personalizados.</p>";
echo "<a href='panel.php' class='btn-grande'>Volver al Panel</a>";
?>