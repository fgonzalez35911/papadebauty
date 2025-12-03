<?php
session_start();
require '../includes/db_connect.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit; }

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM articulos WHERE id=$id");
}

header("Location: listar_articulos.php");
exit;
?>