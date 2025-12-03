<?php
session_start();
require '../includes/db_connect.php';
require_once '../includes/funciones_imagen.php'; // TU MOTOR DE MARCA DE AGUA

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Acceso denegado']);
    exit;
}

if (isset($_FILES['foto']) && isset($_POST['id_articulo'])) {
    $id = intval($_POST['id_articulo']);
    
    if ($_FILES['foto']['error'] == 0) {
        $dir = "../assets/img/"; // Carpeta de imágenes del blog
        if (!file_exists($dir)) mkdir($dir, 0777, true);
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $name = "art_" . $id . "_" . time() . "." . $ext; // Nombre único
        $destino = $dir . $name;
        $ruta_db = "assets/img/" . $name;

        // USAMOS LA FUNCIÓN QUE APLICA MARCA DE AGUA
        if (subir_y_procesar($_FILES['foto'], $destino)) {
            
            // Actualizar base de datos
            $stmt = $conn->prepare("UPDATE articulos SET imagen_destacada = ? WHERE id = ?");
            $stmt->bind_param("si", $ruta_db, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'url' => '../' . $ruta_db]);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error SQL']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error al procesar imagen']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Error de subida: ' . $_FILES['foto']['error']]);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
}
?>