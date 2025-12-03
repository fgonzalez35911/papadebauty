<?php
session_start();
require '../includes/db_connect.php';

// Habilitar reporte de errores para ver qué pasa
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: index.php"); exit;
}

$id = $_POST['id'];
$titulo = $_POST['titulo'];
$contenido = $_POST['contenido'];
$imagen_final = $_POST['imagen_actual'];
$autor = $_SESSION['nombre'];

// --- DIAGNÓSTICO DE SUBIDA ---
if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['name'] != '') {
    
    // 1. Chequear si hubo error en la subida inicial
    if ($_FILES['nueva_imagen']['error'] != 0) {
        die("<h1 style='color:red'>Error al subir: Código " . $_FILES['nueva_imagen']['error'] . "</h1><p>Probablemente el archivo es muy pesado.</p>");
    }

    $directorio_destino = "../assets/img/"; 
    
    // 2. VERIFICAR SI LA CARPETA EXISTE
    if (!is_dir($directorio_destino)) {
        // Intentar crearla si no existe
        mkdir($directorio_destino, 0777, true);
    }

    // 3. VERIFICAR SI TIENE PERMISOS DE ESCRITURA
    if (!is_writable($directorio_destino)) {
        die("<div style='background:pink; padding:20px; border:2px solid red;'>
                <h1>⛔ ERROR DE PERMISOS</h1>
                <p>El servidor no deja guardar archivos en la carpeta <strong>assets/img</strong>.</p>
                <p><strong>SOLUCIÓN:</strong> Entrá a Hostinger -> Administrador de Archivos -> Click derecho en la carpeta 'assets' -> Permisos -> Poné <strong>777</strong> (marcar todas las casillas).</p>
             </div>");
    }

    $nombre_archivo = time() . "_" . basename($_FILES["nueva_imagen"]["name"]);
    $ruta_fisica = $directorio_destino . $nombre_archivo;
    $ruta_web = "assets/img/" . $nombre_archivo; // Ruta para la BD

    // 4. INTENTAR MOVER EL ARCHIVO
    if (move_uploaded_file($_FILES["nueva_imagen"]["tmp_name"], $ruta_fisica)) {
        $imagen_final = $ruta_web; // ¡Éxito!
    } else {
        die("<h1 style='color:red'>Falló move_uploaded_file</h1><p>No se pudo mover el archivo a: $ruta_fisica</p>");
    }
}

// --- GUARDAR EN BD ---
if ($id != '') {
    $stmt = $conn->prepare("UPDATE articulos SET titulo=?, contenido=?, imagen_destacada=? WHERE id=?");
    $stmt->bind_param("sssi", $titulo, $contenido, $imagen_final, $id);
} else {
    $stmt = $conn->prepare("INSERT INTO articulos (titulo, contenido, imagen_destacada, autor) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $titulo, $contenido, $imagen_final, $autor);
}

if ($stmt->execute()) {
    // Si todo salió bien, volvemos a la lista
    header("Location: listar_articulos.php?exito=1");
} else {
    echo "Error SQL: " . $conn->error;
}
?>