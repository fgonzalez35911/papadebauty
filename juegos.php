<?php 
require 'includes/db_connect.php'; 
include 'includes/header.php'; 

$where = "WHERE activo = 1";
$titulo = "Todos los Juegos";

if(isset($_GET['cat'])) {
    $cat = intval($_GET['cat']);
    $where .= " AND id_categoria = $cat";
}
if(isset($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $where .= " AND (titulo LIKE '%$q%' OR descripcion LIKE '%$q%')";
    $titulo = "Resultados";
}

$sql = "SELECT * FROM juegos $where ORDER BY id_categoria ASC, id ASC";
$result = $conn->query($sql);
?>

<style>
    .games-container { padding: 40px 20px; max-width: 1200px; margin: 0 auto; }
    
    /* HEADER SECCIÓN */
    .games-header { text-align: center; margin-bottom: 40px; }
    .games-header h1 { color: var(--color-primario); font-size: 2.5rem; font-weight: 800; }
    
    /* BUSCADOR Y FILTROS */
    .search-form { max-width: 500px; margin: 20px auto; display: flex; }
    .search-input { flex: 1; padding: 12px 20px; border: 2px solid #eee; border-radius: 50px 0 0 50px; outline: none; }
    .search-btn { background: var(--color-primario); color: white; border: none; padding: 0 25px; border-radius: 0 50px 50px 0; cursor: pointer; }
    
    .filter-tags { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
    .tag-btn { padding: 8px 20px; border-radius: 20px; background: white; border: 1px solid #ddd; color: #666; text-decoration: none; font-size: 0.9rem; transition: 0.2s; }
    .tag-btn:hover { background: var(--color-primario); color: white; border-color: var(--color-primario); }

    /* GRILLA RESPONSIVA */
    .games-grid {
        display: grid;
        /* Ajuste para que las tarjetas no sean muy anchas y respeten la proporción */
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); 
        gap: 30px;
    }

    /* TARJETA DE JUEGO */
    .game-card {
        background: white; border-radius: 15px; overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s;
        border: 1px solid #f0f0f0; display: flex; flex-direction: column; height: 100%;
    }
    .game-card:hover { transform: translateY(-5px); border-color: var(--color-primario); }
    
    /* --- CORRECCIÓN DE IMAGEN --- */
    .game-thumb {
        width: 100%;
        /* MAGIA AQUÍ: Forzamos la proporción exacta de tus fotos (300x250) */
        aspect-ratio: 300 / 250; 
        display: flex; align-items: center; justify-content: center;
        background: #f8fafd; color: var(--color-primario);
        overflow: hidden;
    }
    .game-thumb img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; /* Llena el cuadro perfectamente */
        display: block;
    }
    
    .game-info { padding: 20px; flex-grow: 1; }
    .game-tag { font-size: 0.7rem; font-weight: bold; color: #999; text-transform: uppercase; }
    .game-title { font-size: 1.2rem; font-weight: 700; color: #444; margin: 5px 0 10px; }
    
    /* RESPONSIVE MÓVIL */
    @media (max-width: 600px) {
        .games-grid {
            grid-template-columns: 1fr; /* 1 columna en celular */
            padding: 0 10px;
        }
        .filter-tags { overflow-x: auto; justify-content: flex-start; padding-bottom: 10px; white-space: nowrap; }
    }
</style>

<div class="games-container">
    <div class="games-header">
        <h1><?php echo $titulo; ?></h1>
        
        <form action="juegos.php" method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="Buscar juego...">
            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <div class="filter-tags">
            <a href="juegos.php" class="tag-btn">Todos</a>
            <a href="juegos.php?cat=1" class="tag-btn">Lenguaje</a>
            <a href="juegos.php?cat=2" class="tag-btn">Cognitivo</a>
            <a href="juegos.php?cat=6" class="tag-btn">Social</a>
            <a href="juegos.php?cat=8" class="tag-btn">Vida Diaria</a>
        </div>
    </div>

    <div class="games-grid">
        <?php if ($result->num_rows > 0): while($juego = $result->fetch_assoc()): 
            // Imagen
            $img = $juego['imagen_portada'];
            if (empty($img) || $img == 'default.jpg') {
                $thumb = "<i class='fa-solid fa-gamepad' style='font-size:3rem;'></i>";
            } else {
                if (strpos($img, 'http') === false && strpos($img, 'assets') === false) $img = "assets/img/" . $img;
                $thumb = "<img src='$img' alt='".$juego['titulo']."'>";
            }
        ?>
            <a href="jugar.php?id=<?php echo $juego['id']; ?>" style="text-decoration: none;">
                <div class="game-card">
                    <div class="game-thumb"><?php echo $thumb; ?></div>
                    <div class="game-info">
                        <span class="game-tag"><?php echo strtoupper($juego['tipo_juego']); ?></span>
                        <h3 class="game-title"><?php echo $juego['titulo']; ?></h3>
                        <p style="font-size:0.9rem; color:#666; margin:0;"><?php echo $juego['descripcion']; ?></p>
                    </div>
                </div>
            </a>
        <?php endwhile; else: ?>
            <p style="text-align:center; grid-column:1/-1; color:#888;">No se encontraron juegos.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>