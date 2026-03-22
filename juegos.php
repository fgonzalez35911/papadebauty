<?php 
require 'includes/db_connect.php'; 
include 'includes/header.php'; 

// --- LÓGICA DE FILTRADO ---
$where = "WHERE activo = 1";
// AHORA ORDENA POR FECHA DE MODIFICACIÓN (updated_at)
$order = "ORDER BY updated_at DESC"; 
$titulo_seccion = "Todos los Juegos";

// 1. Búsqueda
if(isset($_GET['q']) && !empty($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $where .= " AND (titulo LIKE '%$q%' OR descripcion LIKE '%$q%')";
    $titulo_seccion = "Resultados para '$q'";
}

// 2. Categoría
if(isset($_GET['cat']) && !empty($_GET['cat'])) {
    $cat = intval($_GET['cat']);
    $where .= " AND id_categoria = $cat";
}

// 3. Edad
$edad_sel = isset($_GET['edad']) ? $_GET['edad'] : '';
if($edad_sel == 'peques') { $where .= " AND tag_peques = 1"; }
elseif($edad_sel == 'escolar') { $where .= " AND tag_escolar = 1"; }
elseif($edad_sel == 'teen') { $where .= " AND tag_teen = 1"; }

// 4. Orden Manual (Sobrescribe el default)
$orden_sel = isset($_GET['orden']) ? $_GET['orden'] : 'reciente';
switch($orden_sel) {
    case 'az': $order = "ORDER BY titulo ASC"; break;
    case 'za': $order = "ORDER BY titulo DESC"; break;
    case 'antiguo': $order = "ORDER BY id ASC"; break;
    default: $order = "ORDER BY updated_at DESC"; break; // Por defecto: Último modificado
}

$sql = "SELECT j.*, c.nombre as cat_nombre FROM juegos j LEFT JOIN categorias_blog c ON j.id_categoria = c.id $where $order";
$result = $conn->query($sql);
$cats = $conn->query("SELECT * FROM categorias_blog ORDER BY nombre ASC");

$es_admin = isset($_SESSION['usuario_id']);
?>

<style>
    .games-container { padding: 40px 20px; max-width: 1200px; margin: 0 auto; }
    .games-header { text-align: center; margin-bottom: 30px; }
    .games-header h1 { color: var(--color-primario); font-size: 2.5rem; font-weight: 800; margin-bottom: 10px; }
    
    /* BARRA DE FILTROS */
    .filter-bar {
        background: white; padding: 20px; border-radius: 20px; 
        border: 1px solid #f0f0f0; box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        margin-bottom: 40px; transition: all 0.3s;
    }
    
    /* BOTÓN TOGGLE MOVIL */
    .mobile-filter-btn {
        display: none; width: 100%; padding: 15px; background: white; 
        border: 2px solid var(--color-primario); color: var(--color-primario);
        border-radius: 15px; font-weight: 800; font-size: 1.1rem; cursor: pointer;
        margin-bottom: 20px; text-align: center;
    }
    
    .filter-row { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 15px; }
    .filter-row:last-child { margin-bottom: 0; }

    .tag-btn { 
        padding: 8px 20px; border-radius: 20px; background: white; border: 1px solid #ddd; 
        color: #666; text-decoration: none; font-size: 0.9rem; transition: 0.2s; 
    }
    .tag-btn:hover, .tag-btn.active { background: var(--color-primario); color: white; border-color: var(--color-primario); }

    .search-input { padding: 10px 20px; border: 2px solid #eee; border-radius: 50px; outline: none; width: 100%; max-width: 300px; }
    
    .games-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 30px; }

    .game-card {
        background: white; border-radius: 15px; overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s;
        border: 1px solid #f0f0f0; display: flex; flex-direction: column; height: 100%;
        position: relative;
    }
    .game-wrapper { position: relative; height: 100%; }
    .game-wrapper:hover .game-card { transform: translateY(-5px); border-color: var(--color-primario); }
    
    .game-thumb { width: 100%; aspect-ratio: 300 / 250; display: flex; align-items: center; justify-content: center; background: #f8fafd; color: var(--color-primario); overflow: hidden; }
    .game-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    
    .cat-label { position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 4px 10px; border-radius: 10px; font-size: 0.65rem; font-weight: 800; color: #555; text-transform: uppercase; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .game-info { padding: 20px; flex-grow: 1; }
    .game-tag { font-size: 0.7rem; font-weight: bold; color: #999; text-transform: uppercase; }
    .game-title { font-size: 1.2rem; font-weight: 700; color: #444; margin: 5px 0 10px; }
    
    /* BOTONES ADMIN */
    .admin-actions { position: absolute; top: 10px; left: 10px; z-index: 20; display: flex; gap: 5px; opacity: 0; transform: scale(0.9); transition: all 0.2s ease; }
    .game-wrapper:hover .admin-actions { opacity: 1; transform: scale(1); }
    .btn-action-float { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; color: white; font-size: 0.9rem; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transition: 0.2s; }
    .btn-config { background: #FFB347; } 
    .btn-content { background: #92A8D1; }
    .btn-action-float:hover { transform: scale(1.15); filter: brightness(1.1); }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .games-grid { grid-template-columns: 1fr; padding: 0 10px; }
        .filter-row { justify-content: flex-start; overflow-x: auto; padding-bottom: 5px; }
        .admin-actions { opacity: 1; transform: scale(1); } /* Admin siempre visible en móvil */
        
        /* OCULTAR FILTROS POR DEFECTO */
        .mobile-filter-btn { display: block; }
        .filter-bar { display: none; } /* Oculto */
        .filter-bar.open { display: block; animation: slideDown 0.3s; }
    }
    @keyframes slideDown { from {opacity:0; transform: translateY(-10px);} to {opacity:1; transform: translateY(0);} }
</style>

<div class="games-container">
    <div class="games-header">
        <h1><?php echo $titulo_seccion; ?></h1>
        
        <button class="mobile-filter-btn" onclick="toggleFiltros()">
            <i class="fa-solid fa-filter"></i> Filtrar Juegos 
        </button>

        <div class="filter-bar" id="filterBar">
            <div class="filter-row">
                <form action="juegos.php" method="GET" style="width:100%; text-align:center;">
                    <input type="text" name="q" class="search-input" placeholder="🔍 Buscar juego..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <?php if($edad_sel): ?><input type="hidden" name="edad" value="<?php echo $edad_sel; ?>"><?php endif; ?>
                    <?php if(isset($_GET['cat'])): ?><input type="hidden" name="cat" value="<?php echo $_GET['cat']; ?>"><?php endif; ?>
                </form>
            </div>

            <div class="filter-row">
                <a href="juegos.php?cat=<?php echo $_GET['cat']??''; ?>" class="tag-btn <?php echo $edad_sel==''?'active':''; ?>">Todos</a>
                <a href="juegos.php?edad=peques&cat=<?php echo $_GET['cat']??''; ?>" class="tag-btn <?php echo $edad_sel=='peques'?'active':''; ?>">🧸 Peques (2-5)</a>
                <a href="juegos.php?edad=escolar&cat=<?php echo $_GET['cat']??''; ?>" class="tag-btn <?php echo $edad_sel=='escolar'?'active':''; ?>">🎒 Chicos (6-9)</a>
                <a href="juegos.php?edad=teen&cat=<?php echo $_GET['cat']??''; ?>" class="tag-btn <?php echo $edad_sel=='teen'?'active':''; ?>">🎓 +10 Años</a>
            </div>

            <div class="filter-row" style="border-top: 1px solid #f5f5f5; padding-top: 15px;">
                <a href="juegos.php?edad=<?php echo $edad_sel; ?>" class="tag-btn <?php echo !isset($_GET['cat'])?'active':''; ?>">Todas</a>
                <?php while($c = $cats->fetch_assoc()): ?>
                    <a href="juegos.php?cat=<?php echo $c['id']; ?>&edad=<?php echo $edad_sel; ?>" class="tag-btn <?php echo (isset($_GET['cat']) && $_GET['cat']==$c['id'])?'active':''; ?>">
                        <?php echo $c['nombre']; ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="games-grid">
        <?php if ($result->num_rows > 0): while($juego = $result->fetch_assoc()): 
            $img = $juego['imagen_portada'];
            if (empty($img) || $img == 'default.jpg') $img = "assets/img/logo_placeholder.png";
            elseif (strpos($img, 'http') === false && strpos($img, 'assets') === false) $img = "assets/img/" . $img;
        ?>
            <div class="game-wrapper">
                <?php if($es_admin): ?>
                    <div class="admin-actions">
                        <a href="admin/gestionar_contenido.php?id_juego=<?php echo $juego['id']; ?>" class="btn-action-float btn-content"><i class="fa-solid fa-layer-group"></i></a>
                        <a href="admin/editar_juego.php?id=<?php echo $juego['id']; ?>" class="btn-action-float btn-config"><i class="fa-solid fa-pencil"></i></a>
                    </div>
                <?php endif; ?>

                <a href="jugar.php?id=<?php echo $juego['id']; ?>" style="text-decoration: none; height: 100%; display: block;">
                    <div class="game-card">
                        <div class="game-thumb">
                            <span class="cat-label"><?php echo $juego['cat_nombre']; ?></span>
                            <img src="<?php echo $img; ?>" alt="<?php echo $juego['titulo']; ?>">
                        </div>
                        <div class="game-info">
                            <span class="game-tag"><?php echo strtoupper($juego['tipo_juego']); ?></span>
                            <h3 class="game-title"><?php echo $juego['titulo']; ?></h3>
                            <p style="font-size:0.9rem; color:#666; margin:0; line-height: 1.4;">
                                <?php echo substr($juego['descripcion'],0,60); ?>...
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endwhile; else: ?>
            <p style="text-align:center; grid-column:1/-1; color:#888; padding: 40px;">No se encontraron juegos.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleFiltros() {
        var bar = document.getElementById('filterBar');
        bar.classList.toggle('open');
    }
</script>

<?php include 'includes/footer.php'; ?>