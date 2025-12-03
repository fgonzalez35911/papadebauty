<?php
session_start(); require '../includes/db_connect.php';
if(!isset($_SESSION['usuario_id'])) header("Location: ../index.php");

$id_editar = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
$datos_editar = null;
if($id_editar) {
    $datos_editar = $conn->query("SELECT * FROM galeria WHERE id=$id_editar")->fetch_assoc();
}

if(isset($_POST['guardar'])) {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $archivo_final = $_POST['imagen_actual'] ?? '';

    if(isset($_FILES['imagen']) && $_FILES['imagen']['name'] != '') {
        $dir = "../assets/uploads/galeria/";
        if(!file_exists($dir)) mkdir($dir, 0777, true);
        $name = time() . "_" . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $name);
        $archivo_final = "assets/uploads/galeria/$name";
    }

    if($id_editar) {
        $conn->query("UPDATE galeria SET titulo='$titulo', imagen='$archivo_final' WHERE id=$id_editar");
        header("Location: gestionar_galeria.php");
    } else {
        $conn->query("INSERT INTO galeria (titulo, imagen) VALUES ('$titulo', '$archivo_final')");
        header("Location: gestionar_galeria.php");
    }
}
if(isset($_GET['del'])) { $conn->query("DELETE FROM galeria WHERE id=".intval($_GET['del'])); header("Location: gestionar_galeria.php"); }
?>
<!DOCTYPE html><html><head><title>Admin Galería</title></head><body>
<?php include '../includes/header.php'; ?>
<div style="max-width:900px; margin:20px auto; padding:20px; background:white; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="color:var(--color-primario); margin:0;"><i class="fa-solid fa-images"></i> Galería</h2>
        <a href="panel.php" class="btn-grande" style="background: #999; font-size: 0.9rem; padding: 10px 20px;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="POST" enctype="multipart/form-data" style="background:#f8f9fa; padding:20px; border-radius:10px; margin-bottom:30px; border:1px solid #eee;">
        <h4 style="margin-top:0;"><?php echo $id_editar ? 'Editar Foto' : 'Subir Foto'; ?></h4>
        <input type="hidden" name="imagen_actual" value="<?php echo $datos_editar['imagen'] ?? ''; ?>">
        
        <input type="text" name="titulo" placeholder="Título o Descripción" style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ddd; border-radius:5px;" value="<?php echo $datos_editar['titulo'] ?? ''; ?>">
        
        <?php if($id_editar): ?><img src="../<?php echo $datos_editar['imagen']; ?>" width="100" style="margin-bottom:10px; border-radius:5px; display:block;"><small>Imagen actual</small><br><?php endif; ?>
        
        <input type="file" name="imagen" <?php echo $id_editar ? '' : 'required'; ?> style="margin-bottom:10px; width:100%;">
        
        <div style="display:flex; gap:10px;">
            <button type="submit" name="guardar" class="btn-grande" style="flex:1;"><?php echo $id_editar ? 'Guardar' : 'Subir'; ?></button>
            <?php if($id_editar): ?><a href="gestionar_galeria.php" class="btn-grande" style="background:#ccc; color:#555;">Cancelar</a><?php endif; ?>
        </div>
    </form>

    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap:15px;">
    <?php $res = $conn->query("SELECT * FROM galeria ORDER BY id DESC"); while($r = $res->fetch_assoc()): ?>
        <div style="position:relative; border:1px solid #eee; border-radius:10px; overflow:hidden;">
            <img src="../<?php echo $r['imagen']; ?>" style="width:100%; height:150px; object-fit:cover;">
            <div style="padding:10px; text-align:center; background:#fff;">
                <small style="display:block; margin-bottom:5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo $r['titulo']; ?></small>
                <a href="?editar=<?php echo $r['id']; ?>" style="color:#FFB347; margin-right:10px; font-size:1.2rem;"><i class="fa-solid fa-pencil"></i></a>
                <a href="?del=<?php echo $r['id']; ?>" style="color:#FF6B6B; font-size:1.2rem;" onclick="return confirm('¿X?');"><i class="fa-solid fa-trash"></i></a>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div></body></html>