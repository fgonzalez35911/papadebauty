<?php
require 'includes/db_connect.php';
require 'includes/header.php';

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: padres.php"); exit;
}
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT a.*, c.nombre as categoria_nombre FROM articulos a LEFT JOIN categorias_blog c ON a.id_categoria = c.id WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div style='padding:50px; text-align:center;'><h2>Artículo no encontrado</h2><a href='padres.php' class='btn-grande'>Volver</a></div>";
    require 'includes/footer.php'; exit;
}
$articulo = $result->fetch_assoc();
$img_bg = $articulo['imagen_destacada'];
?>

<style>
    /* HERO: Imagen oscura de fondo */
    .hero-articulo {
        height: 50vh; /* Ocupa mitad de pantalla */
        min-height: 400px;
        background: url('<?php echo $img_bg; ?>') no-repeat center center/cover;
        position: relative;
        display: flex;
        align-items: flex-end;
    }
    .hero-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.9));
    }
    .hero-content {
        position: relative; z-index: 2; width: 100%; max-width: 900px;
        margin: 0 auto; padding: 0 20px 60px; /* 60px padding bottom para dar aire */
        color: white;
    }
    
    /* CAJA DEL CONTENIDO */
    .articulo-container {
        max-width: 900px;
        margin: 0 auto 60px; /* Margen abajo */
        background: white;
        padding: 50px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        position: relative;
        z-index: 3;
        margin-top: -40px; /* Sube un poco, pero no tanto para tapar el texto */
    }

    .meta-tags {
        display: flex; gap: 10px; margin-bottom: 15px;
    }
    .tag-cat {
        background: var(--color-primario); color: white; padding: 5px 12px;
        border-radius: 20px; font-weight: bold; font-size: 0.8rem; text-transform: uppercase;
    }
    
    /* Tipografía del cuerpo */
    .contenido-body {
        font-size: 1.2rem; line-height: 1.8; color: #333;
    }
    .contenido-body p { margin-bottom: 20px; }
    
    /* Imagen final (la que estaba repetida arriba) */
    .img-final-referencia {
        width: 100%; border-radius: 15px; margin-top: 30px;
        border: 1px solid #eee;
    }

    @media (max-width: 700px) {
        .articulo-container { padding: 25px; margin-top: -20px; }
        .hero-content h1 { font-size: 1.8rem; }
    }
</style>

<div class="hero-articulo">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="meta-tags">
            <span class="tag-cat"><?php echo $articulo['categoria_nombre']; ?></span>
        </div>
        <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 10px 0; line-height: 1.2;">
            <?php echo $articulo['titulo']; ?>
        </h1>
        <div style="font-size: 0.95rem; opacity: 0.9;">
            <i class="fa-regular fa-calendar"></i> <?php echo date("d/m/Y", strtotime($articulo['fecha_publicacion'])); ?> 
            &nbsp;|&nbsp; 
            <i class="fa-regular fa-user"></i> Por <?php echo $articulo['autor']; ?>
        </div>
    </div>
</div>

<div class="articulo-container">
    <a href="padres.php" style="display:inline-block; margin-bottom: 30px; color: #888; font-weight: bold;">
        <i class="fa-solid fa-arrow-left"></i> Volver al Blog
    </a>

    <div class="contenido-body">
        <?php echo $articulo['contenido']; ?>

        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="font-size: 0.9rem; color: #999; text-align: center;">Imagen de referencia del artículo:</p>
            <img src="<?php echo $img_bg; ?>" alt="Referencia" class="img-final-referencia">
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>