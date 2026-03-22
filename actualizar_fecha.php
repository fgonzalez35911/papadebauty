<?php
// admin/actualizar_fecha.php
require '../includes/db_connect.php';
echo "<h1>⚙️ Actualizando Base de Datos para Ordenamiento</h1>";

// 1. Agregar columna updated_at si no existe
$check = $conn->query("SHOW COLUMNS FROM juegos LIKE 'updated_at'");
if ($check->num_rows == 0) {
    // TIMESTAMP que se actualiza solo cuando modificas algo
    $conn->query("ALTER TABLE juegos ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER activo");
    echo "<p>✅ Columna 'updated_at' creada. Ahora los juegos subirán al editarse.</p>";
} else {
    echo "<p>ℹ️ La columna de fecha ya existía.</p>";
}

echo "<a href='panel.php'>Volver</a>";
?>