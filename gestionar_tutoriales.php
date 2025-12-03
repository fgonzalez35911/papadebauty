<?php
session_start(); require '../includes/db_connect.php';
if(!isset($_SESSION['usuario_id'])) header("Location: ../index.php");

$id_editar = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
$datos_editar = null;
if($id_editar) {
    $datos_editar = $conn->query("SELECT * FROM tutoriales WHERE id=$id_editar")->fetch_assoc();
}

if(isset($_POST['guardar'])) {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $url = $conn->real_escape_string($_POST['url']);
    parse_str( parse_url( $url, PHP_URL_QUERY ), $vars );
    $vid_id = $vars['v'] ?? $url;

    if($id_editar) {
        $conn->query("UPDATE tutoriales SET titulo='$titulo', video_url='$vid_id' WHERE id=$id_editar");
        header("Location: gestionar_tutoriales.php");
    } else {
        $conn->query("INSERT INTO tutoriales (titulo, video_url) VALUES ('$titulo', '$vid_id')");
        header("Location: gestionar_tutoriales.php");
    }
}
if(isset($_GET['del'])) { $conn->query("DELETE FROM tutoriales WHERE id=".intval($_GET['del'])); header("Location: gestionar_tutoriales.php"); }

$where = "WHERE 1=1";
if(isset($_GET['q'])) $where .= " AND titulo LIKE '%".$conn->real_escape_string($_GET['q'])."%'";
?>
<!DOCTYPE html><html><head><title>Admin Tutoriales</title></head><body>
<?php include '../includes/header.php'; ?>
<div style="max-width:900px; margin:20px auto; padding:20px; background:white; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="color:var(--color-primario); margin:0;"><i class="fa-brands fa-youtube"></i> Tutoriales</h2>
        <a href="panel.php" class="btn-grande" style="background: #999; font-size: 0.9rem; padding: 10px 20px;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="POST" style="background:#f8f9fa; padding:20px; border-radius:10px; margin-bottom:30px; border:1px solid #eee;">
        <h4 style="margin-top:0;"><?php echo $id_editar ? 'Editar Video' : 'Nuevo Video'; ?></h4>
        <input type="text" name="titulo" placeholder="Título del Video" style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ddd; border-radius:5px;" value="<?php echo $datos_editar['titulo'] ?? ''; ?>" required>
        <input type="text" name="url" placeholder="Link de YouTube (ej: https://www.youtube.com/watch?v=...)" style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ddd; border-radius:5px;" value="<?php echo $id_editar ? 'https://www.youtube.com/watch?v='.$datos_editar['video_url'] : ''; ?>" required>
        
        <div style="display:flex; gap:10px;">
            <button type="submit" name="guardar" class="btn-grande" style="flex:1;"><?php echo $id_editar ? 'Guardar Cambios' : 'Agregar Video'; ?></button>
            <?php if($id_editar): ?><a href="gestionar_tutoriales.php" class="btn-grande" style="background:#ccc; color:#555;">Cancelar</a><?php endif; ?>
        </div>
    </form>

    <form action="" method="GET" style="display:flex; margin-bottom:20px;">
        <input type="text" name="q" placeholder="Buscar video..." style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px 0 0 5px;">
        <button type="submit" class="btn-grande" style="border-radius:0 5px 5px 0; padding:10px 20px;"><i class="fa-solid fa-search"></i></button>
    </form>

    <div style="display:grid; gap:15px;">
        <?php $res = $conn->query("SELECT * FROM tutoriales $where ORDER BY id DESC"); while($r = $res->fetch_assoc()): ?>
        <div style="display:flex; justify-content:space-between; padding:15px; border:1px solid #eee; border-radius:10px; align-items:center;">
            <div style="display:flex; align-items:center; gap:15px;">
                <img src="https://img.youtube.com/vi/<?php echo $r['video_url']; ?>/default.jpg" style="width:80px; border-radius:5px;">
                <strong><?php echo $r['titulo']; ?></strong>
            </div>
            <div style="min-width:80px; text-align:right;">
                <a href="?editar=<?php echo $r['id']; ?>" class="btn-grande" style="background:#FFB347; padding:5px 10px; font-size:0.8rem;"><i class="fa-solid fa-pencil"></i></a>
                <a href="?del=<?php echo $r['id']; ?>" class="btn-grande" style="background:#FF6B6B; padding:5px 10px; font-size:0.8rem;" onclick="return confirm('¿Borrar?');"><i class="fa-solid fa-trash"></i></a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div></body></html>