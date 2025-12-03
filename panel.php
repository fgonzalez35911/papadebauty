<?php
session_start();
require '../includes/db_connect.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: ../index.php"); exit; }

// Conteos rápidos
$c_art = $conn->query("SELECT COUNT(*) as c FROM articulos")->fetch_assoc()['c'];
$c_jue = $conn->query("SELECT COUNT(*) as c FROM juegos")->fetch_assoc()['c'];
$c_rec = $conn->query("SELECT COUNT(*) as c FROM recursos")->fetch_assoc()['c'];
$c_tut = $conn->query("SELECT COUNT(*) as c FROM tutoriales")->fetch_assoc()['c'];
$c_gal = $conn->query("SELECT COUNT(*) as c FROM galeria")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
</head>
<body>
<?php include '../includes/header.php'; ?>

<style>
    body { background-color: #f0f2f5; padding-top: 100px; padding-bottom: 40px; }
    .panel-container { max-width: 1100px; margin: 0 auto; padding: 0 20px; }
    .welcome-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; border-left: 5px solid var(--color-primario); }
    .grid-panel { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
    .card-panel { background: white; padding: 30px 20px; border-radius: 20px; text-align: center; text-decoration: none; color: #555; box-shadow: 0 5px 20px rgba(0,0,0,0.03); transition: 0.3s; border: 2px solid transparent; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: center; }
    .card-panel:hover { transform: translateY(-5px); border-color: var(--color-primario); box-shadow: 0 10px 30px rgba(146, 168, 209, 0.2); }
    .icon-big { font-size: 2.5rem; margin-bottom: 15px; color: var(--color-primario); }
    .stat-num { font-size: 1.5rem; font-weight: 800; color: #333; margin-top: 10px; }
    .card-destacada { background: #fffbf0; border: 2px dashed #FFB347; }
</style>

<div class="panel-container">
    <div class="welcome-card">
        <div>
            <h1 style="margin:0; color:#333;">Panel de Control</h1>
            <p style="margin:5px 0 0; color:#777;">Bienvenido, <strong><?php echo $_SESSION['nombre']; ?></strong></p>
        </div>
        <a href="logout.php" class="btn-grande" style="background:#ff6b6b; font-size:0.9rem; padding:8px 20px;">Salir</a>
    </div>

    <h3 style="color:#555; margin-bottom:20px;">Contenido Principal</h3>
    <div class="grid-panel">
        <a href="gestionar_contenido.php" class="card-panel card-destacada">
            <i class="fa-solid fa-layer-group icon-big" style="color:#FFB347;"></i>
            <h3>Contenido Juegos</h3>
            <span>Subir fotos y preguntas</span>
        </a>
        <a href="listar_juegos.php" class="card-panel">
            <i class="fa-solid fa-gamepad icon-big"></i>
            <h3>Mis Juegos</h3>
            <span class="stat-num"><?php echo $c_jue; ?></span>
        </a>
        <a href="listar_articulos.php" class="card-panel">
            <i class="fa-regular fa-newspaper icon-big"></i>
            <h3>Blog</h3>
            <span class="stat-num"><?php echo $c_art; ?></span>
        </a>
    </div>

    <h3 style="color:#555; margin:40px 0 20px;">Recursos y Medios</h3>
    <div class="grid-panel">
        <a href="gestionar_recursos.php" class="card-panel">
            <i class="fa-solid fa-file-arrow-down icon-big" style="color:#4ECDC4;"></i>
            <h3>Recursos</h3>
            <span class="stat-num"><?php echo $c_rec; ?></span>
        </a>
        <a href="gestionar_tutoriales.php" class="card-panel">
            <i class="fa-brands fa-youtube icon-big" style="color:#FF6B6B;"></i>
            <h3>Tutoriales</h3>
            <span class="stat-num"><?php echo $c_tut; ?></span>
        </a>
        <a href="gestionar_galeria.php" class="card-panel">
            <i class="fa-solid fa-images icon-big" style="color:#92A8D1;"></i>
            <h3>Galería</h3>
            <span class="stat-num"><?php echo $c_gal; ?></span>
        </a>
    </div>
</div>
</body>
</html>