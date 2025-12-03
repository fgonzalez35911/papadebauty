<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Borramos el juego (La base de datos borrará el contenido automáticamente por la configuración en cascada)
    $conn->query("DELETE FROM juegos WHERE id = $id");
}

header("Location: listar_juegos.php");
exit;
?>