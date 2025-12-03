<?php
require 'includes/db_connect.php';
require 'includes/header.php';

// --- CONFIGURACIÓN PAGINACIÓN ---
$articulos_por_pagina = 15;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $articulos_por_pagina;

// --- FILTROS ---
$where = "WHERE 1=1";
$parametros_url = ""; // Para mantener los filtros al cambiar de página

if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = $conn->real_escape_string($_GET['busqueda']);
    $where .= " AND (titulo LIKE '%$busqueda%' OR contenido LIKE '%$busqueda%')";
    $parametros_url .= "&busqueda=" . urlencode($_GET['busqueda']);
}
if (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $cat_id = intval($_GET['cat']);
    $where .= " AND id_categoria = $cat_id";
    $parametros_url .= "&cat=" . $cat_id;
}

// 1. CONTAR TOTAL DE ARTÍCULOS (Para saber cuántas páginas son)
$sql_count = "SELECT COUNT(*) as total FROM articulos a $where";
$total_articulos = $conn->query($sql_count)->fetch_assoc()['total'];
$total_paginas = ceil($total_articulos / $articulos_por_pagina);

// 2. CONSULTA CON LÍMITE (Para mostrar solo 20)
$sql = "SELECT a.*, c.nombre as categoria_nombre 
        FROM articulos a 
        LEFT JOIN categorias_blog c ON a.id_categoria = c.id 
        $where 
        ORDER BY fecha_publicacion DESC 
        LIMIT $offset, $articulos_por_pagina";
$result = $conn->query($sql);
?>

<style>
    /* HEADERS */
    .blog-header {
        text-align: center; padding: 60px 20px; background: #fff;
        margin-bottom: 40px; border-bottom: 1px solid #eee;
    }
    .blog-header h1 { font-size: 3rem; color: var(--color-primario); margin-bottom: 10px; font-weight: 800; }
    .blog-header p { color: #888; font-size: 1.2rem; }

    /* LAYOUT */
    .blog-layout {
        max-width: 1300px; margin: 0 auto; padding: 0 20px;
        display: grid; grid-template-columns: 3fr 1fr; gap: 40px;
    }

    /* CARDS (Forzando 3 columnas en espacio disponible) */
    .posts-grid { 
        display: grid; 
        /* Ajuste clave: 220px permite que entren 3 en la columna ancha */
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
        gap: 25px; 
    }
    
    .post-card {
        background: white; border-radius: 20px; overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s;
        display: flex; flex-direction: column; border: 1px solid #f0f0f0; height: 100%;
    }
    .post-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(146, 168, 209, 0.2); border-color: var(--color-primario); }
    
    .post-img { height: 180px; object-fit: cover; width: 100%; transition: 0.3s; }
    .post-card:hover .post-img { opacity: 0.9; }

    .post-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .post-cat { color: var(--color-primario); font-weight: bold; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 8px; }
    
    .post-title { font-size: 1.1rem; margin: 0 0 15px 0; line-height: 1.4; font-weight: 800; }
    .post-title a { text-decoration: none; color: #444; transition: 0.2s; }
    .post-title a:hover { color: var(--color-primario); }

    .post-btn { 
        margin-top: auto; background-color: var(--color-primario); color: white; 
        font-weight: 800; text-decoration: none; padding: 8px 20px; border-radius: 50px; 
        display: inline-block; text-align: center; transition: 0.3s; font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(146, 168, 209, 0.3);
    }
    .post-btn:hover { background-color: #7fa1c3; transform: translateY(-2px); }

    /* PAGINACIÓN BURBUJA */
    .pagination-container {
        margin-top: 50px; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;
    }
    .page-bubble {
        width: 45px; height: 45px; border-radius: 50%; 
        background: white; border: 2px solid #eee;
        display: flex; align-items: center; justify-content: center;
        color: #666; font-weight: bold; text-decoration: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Efecto rebote */
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .page-bubble:hover {
        transform: scale(1.15); border-color: var(--color-primario); color: var(--color-primario);
    }
    .page-bubble.active {
        background: var(--color-primario); color: white; border-color: var(--color-primario);
        transform: scale(1.1); box-shadow: 0 5px 15px rgba(146, 168, 209, 0.4);
    }
    /* Flechas rectangulares redondeadas */
    .page-nav {
        padding: 0 20px; border-radius: 25px; width: auto;
    }

    /* SIDEBAR */
    .sidebar-box { background: white; padding: 25px; border-radius: 15px; margin-bottom: 30px; border: 1px solid #eee; }
    .sidebar-title { font-size: 1.2rem; margin-bottom: 15px; color: #444; border-bottom: 2px solid #f5f5f5; padding-bottom: 10px; font-weight: 800; }
    
    .tag-cloud { display: flex; flex-wrap: wrap; gap: 8px; }
    .tag { background: #f8f9fa; padding: 5px 12px; border-radius: 20px; color: #666; font-size: 0.85rem; text-decoration: none; transition: 0.2s; }
    .tag:hover { background: var(--color-primario); color: white; }

    @media (max-width: 900px) {
        .blog-layout { grid-template-columns: 1fr; }
        .posts-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
    }
</style>

<div class="blog-header">
    <h1>Blog para Padres</h1>
    <p>Recursos, historias y herramientas para el día a día.</p>
</div>

<div class="blog-layout">
    
    <div>
        <div class="posts-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="post-card">
                        <a href="articulo.php?id=<?php echo $row['id']; ?>" style="display:block;">
                            <img src="<?php echo !empty($row['imagen_destacada']) ? $row['imagen_destacada'] : 'assets/img/papaybau.jpg'; ?>" class="post-img" alt="Imagen">
                        </a>
                        <div class="post-body">
                            <span class="post-cat"><?php echo $row['categoria_nombre']; ?></span>
                            <h3 class="post-title">
                                <a href="articulo.php?id=<?php echo $row['id']; ?>"><?php echo $row['titulo']; ?></a>
                            </h3>
                            <a href="articulo.php?id=<?php echo $row['id']; ?>" class="post-btn">
                                Leer artículo <i class="fa-solid fa-arrow-right" style="margin-left:5px;"></i>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                    <h3>No encontramos artículos.</h3>
                    <a href="padres.php" class="btn-grande">Ver todos</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_paginas > 1): ?>
        <div class="pagination-container">
            
            <?php if ($pagina_actual > 1): ?>
                <a href="?pagina=<?php echo $pagina_actual - 1; ?><?php echo $parametros_url; ?>" class="page-bubble page-nav">
                    <i class="fa-solid fa-chevron-left"></i> &nbsp; Anterior
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $parametros_url; ?>" 
                   class="page-bubble <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina_actual + 1; ?><?php echo $parametros_url; ?>" class="page-bubble page-nav">
                    Siguiente &nbsp; <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    </div>

    <aside>
        <div class="sidebar-box">
            <h4 class="sidebar-title">Buscar</h4>
            <form action="padres.php" method="GET" style="display: flex;">
                <input type="text" name="busqueda" placeholder="Tema..." style="width: 100%; border: 1px solid #ddd; border-right: none; padding: 10px; border-radius: 5px 0 0 5px; outline:none;">
                <button type="submit" style="background: var(--color-primario); color: white; border: none; padding: 0 15px; border-radius: 0 5px 5px 0; cursor: pointer;"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="sidebar-box">
            <h4 class="sidebar-title">Temas</h4>
            <div class="tag-cloud">
                <a href="padres.php" class="tag">Todos</a>
                <?php
                $cats = $conn->query("SELECT * FROM categorias_blog");
                while($c = $cats->fetch_assoc()) {
                    $activa = (isset($_GET['cat']) && $_GET['cat'] == $c['id']) ? 'background: #FFDAC1; color:#fff;' : '';
                    echo "<a href='padres.php?cat=".$c['id']."' class='tag' style='$activa'>".$c['nombre']."</a>";
                }
                ?>
            </div>
        </div>
        
        <div class="sidebar-box">
            <h4 class="sidebar-title">Recomendados</h4>
            <ul style="list-style:none; padding:0; margin:0;">
                <?php 
                $rec = $conn->query("SELECT id, titulo FROM articulos ORDER BY RAND() LIMIT 3");
                while($r = $rec->fetch_assoc()): ?>
                    <li style="margin-bottom:10px; border-bottom:1px solid #f5f5f5; padding-bottom:5px;">
                        <a href="articulo.php?id=<?php echo $r['id']; ?>" style="text-decoration:none; color:#555; font-size:0.9rem; font-weight:600;">
                            <?php echo $r['titulo']; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </aside>

</div>

<div style="height: 60px;"></div>

<?php require 'includes/footer.php'; ?>