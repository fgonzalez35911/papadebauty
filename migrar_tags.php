<?php
// admin/migrar_tags.php
require '../includes/db_connect.php';
echo "<h1>⚙️ Agregando control manual de edades...</h1>";

// Agregamos 3 columnas booleanas (Si/No)
$cols = ['tag_peques', 'tag_escolar', 'tag_teen'];
foreach($cols as $col) {
    $check = $conn->query("SHOW COLUMNS FROM juegos LIKE '$col'");
    if ($check->num_rows == 0) {
        // TINYINT(1) funciona como booleano (0 o 1)
        $conn->query("ALTER TABLE juegos ADD COLUMN $col TINYINT(1) DEFAULT 0 AFTER id_categoria");
        echo "<p>✅ Columna '$col' agregada para control manual.</p>";
    }
}
echo "<hr><p>Listo. Ahora puedes editar los juegos y marcar manualmente la edad.</p>";
?>