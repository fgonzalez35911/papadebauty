<?php
session_start(); require '../includes/db_connect.php';
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') { header("Location: listar_juegos.php"); exit; }

$id = $_POST['id'];
$titulo = $_POST['titulo'];
$id_categoria = intval($_POST['id_categoria']); // Categoría manual
$descripcion = $_POST['descripcion'];
$instruccion_jugador = $_POST['instruccion_jugador'];
$activo = $_POST['activo'];
$imagen_final = $_POST['imagen_actual'];

// RECIBIR LOS TAGS MANUALES (Si no están marcados, valen 0)
$tag_peques = isset($_POST['tag_peques']) ? 1 : 0;
$tag_escolar = isset($_POST['tag_escolar']) ? 1 : 0;
$tag_teen = isset($_POST['tag_teen']) ? 1 : 0;

if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['name'] != '') {
    $dir = "../assets/img/juegos/"; if (!is_dir($dir)) mkdir($dir, 0777, true);
    $nombre = time() . "_" . basename($_FILES["nueva_imagen"]["name"]);
    if (move_uploaded_file($_FILES["nueva_imagen"]["tmp_name"], $dir . $nombre)) $imagen_final = "assets/img/juegos/" . $nombre;
}

if(empty($id)) {
    // INSERT con tags manuales
    $sql = "INSERT INTO juegos (titulo, id_categoria, descripcion, instruccion_jugador, activo, imagen_portada, tag_peques, tag_escolar, tag_teen, id_motor, tipo_juego) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 4, 'seleccion')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissisiii", $titulo, $id_categoria, $descripcion, $instruccion_jugador, $activo, $imagen_final, $tag_peques, $tag_escolar, $tag_teen);
} else {
    // UPDATE con tags manuales
    $sql = "UPDATE juegos SET titulo=?, id_categoria=?, descripcion=?, instruccion_jugador=?, activo=?, imagen_portada=?, tag_peques=?, tag_escolar=?, tag_teen=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissisiiii", $titulo, $id_categoria, $descripcion, $instruccion_jugador, $activo, $imagen_final, $tag_peques, $tag_escolar, $tag_teen, $id);
}

if ($stmt->execute()) { header("Location: listar_juegos.php?exito=1"); } else { echo "Error: " . $conn->error; }
?>