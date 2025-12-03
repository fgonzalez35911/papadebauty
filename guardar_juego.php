<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: listar_juegos.php"); exit;
}

$id = $_POST['id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$activo = $_POST['activo'];
$imagen_final = $_POST['imagen_actual'];

// --- SUBIR IMAGEN ---
if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['name'] != '') {
    $directorio_destino = "../assets/img/juegos/";
    
    // Crear carpeta si no existe
    if (!is_dir($directorio_destino)) { mkdir($directorio_destino, 0777, true); }

    $nombre_archivo = time() . "_" . basename($_FILES["nueva_imagen"]["name"]);
    $ruta_fisica = $directorio_destino . $nombre_archivo;
    
    if (move_uploaded_file($_FILES["nueva_imagen"]["tmp_name"], $ruta_fisica)) {
        // Guardamos la ruta relativa (assets/img/juegos/...)
        $imagen_final = "assets/img/juegos/" . $nombre_archivo;
    }
}

// --- ACTUALIZAR BASE DE DATOS ---
$stmt = $conn->prepare("UPDATE juegos SET titulo=?, descripcion=?, activo=?, imagen_portada=? WHERE id=?");
$stmt->bind_param("ssisi", $titulo, $descripcion, $activo, $imagen_final, $id);

if ($stmt->execute()) {
    header("Location: listar_juegos.php?exito=1");
} else {
    echo "Error: " . $conn->error;
}
?>