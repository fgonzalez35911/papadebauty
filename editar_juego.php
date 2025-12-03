<?php
session_start();
require '../includes/db_connect.php';
if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) { header("Location: listar_juegos.php"); exit; }

$id = intval($_GET['id']);
$sql = "SELECT * FROM juegos WHERE id = $id";
$result = $conn->query($sql);
$juego = $result->fetch_assoc();

// Corrección de ruta para previsualizar
$img_prev = $juego['imagen_portada'];
if (strpos($img_prev, 'http') === false) $img_prev = "../" . $img_prev;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Juego</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <?php include '../includes/header.php'; ?>
    <style>
        body { background: #f4f4f4; display: block; padding: 20px; }
        .editor-box { 
            width: 100%; 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        }
        label { display: block; margin: 15px 0 5px; font-weight: bold; color: #555; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; }
        
        /* Ajuste para movil */
        @media (max-width: 500px) {
            .editor-box { padding: 15px; }
            h2 { font-size: 1.5rem; }
            .btn-grande { width: 100%; margin-bottom: 10px; display: block; }
        }
    </style>
</head>
<body>

<div class="editor-box">
    <h2 style="margin-bottom: 20px; color: var(--color-primario);">Editar: <?php echo $juego['titulo']; ?></h2>

    <form action="guardar_juego.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="imagen_actual" value="<?php echo $juego['imagen_portada']; ?>">

        <label>Título</label>
        <input type="text" name="titulo" value="<?php echo $juego['titulo']; ?>" required>

        <label>Descripción Corta</label>
        <textarea name="descripcion" rows="3"><?php echo $juego['descripcion']; ?></textarea>

        <label>Estado</label>
        <select name="activo">
            <option value="1" <?php if($juego['activo']==1) echo 'selected'; ?>>Activo (Visible)</option>
            <option value="0" <?php if($juego['activo']==0) echo 'selected'; ?>>Oculto (Borrador)</option>
        </select>

        <label>Imagen de Portada</label>
        <div style="margin-bottom: 10px;">
            <img src="<?php echo $img_prev; ?>" style="height: 80px; border-radius: 5px;">
        </div>
        <input type="file" name="nueva_imagen" accept="image/*">

        <div style="margin-top: 30px; text-align: right;">
            <a href="listar_juegos.php" class="btn-grande" style="background:#999; font-size: 1rem; padding: 10px 20px;">Cancelar</a>
            <button type="submit" class="btn-grande btn-jugar" style="font-size: 1rem; padding: 10px 20px;">Guardar Cambios</button>
        </div>
    </form>
</div>

</body>
</html>
