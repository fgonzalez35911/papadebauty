<?php 
// Activamos errores por seguridad
ini_set('display_errors', 1); error_reporting(E_ALL);

require 'includes/db_connect.php'; 
include 'includes/header.php'; 

// LÓGICA DE FILTRADO
$whereClauses = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $whereClauses[] = "(titulo LIKE '%$q%' OR descripcion LIKE '%$q%')";
}
if (isset($_GET['dest']) && !empty($_GET['dest'])) {
    $dest = $conn->real_escape_string($_GET['dest']);
    $whereClauses[] = "destinatario = '$dest'";
}
if (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
    $tipo = $conn->real_escape_string($_GET['tipo']);
    $whereClauses[] = "tipo = '$tipo'";
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(' AND ', $whereClauses);
}

$sql = "SELECT * FROM recursos $whereSql ORDER BY RAND()";
$result = $conn->query($sql);
?>

<style>
    /* HEADER TRANSPARENTE (CORREGIDO) */
    .rec-header {
        background: transparent; /* Ahora deja ver los iconos del fondo */
        padding: 60px 20px; 
        text-align: center;
        position: relative;
        /* Quitamos el borde para que sea más fluido */
        border: none; 
    }
    .rec-header h1 { font-size: 3.5rem; color: var(--color-primario); margin-bottom: 10px; font-weight: 900; }
    .rec-header p { 
        font-size: 1.3rem; color: #666; max-width: 700px; margin: 0 auto;
        /* Pequeño fondo vidrio para que el texto se lea bien sobre los iconos */
        background: rgba(255,255,255,0.6); backdrop-filter: blur(5px);
        padding: 10px 20px; border-radius: 20px; display: inline-block;
    }

    /* LAYOUT PRINCIPAL */
    .main-layout {
        max-width: 1200px; margin: 20px auto 60px; padding: 0 20px;
        display: grid; grid-template-columns: 3fr 1fr; gap: 40px;
    }

    /* GRILLA DE RECURSOS */
    .resources-grid {
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
        gap: 25px;
    }

    /* TARJETA DE RECURSO */
    .resource-card {
        background: white; border-radius: 15px; overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #fff;
        transition: transform 0.3s; display: flex; flex-direction: column;
        position: relative;
    }
    .resource-card:hover { transform: translateY(-5px); border-color: var(--color-primario); box-shadow: 0 15px 30px rgba(146, 168, 209, 0.2); }

    .res-visual {
        height: 160px; background: #f4f8fb; display: flex; align-items: center; justify-content: center;
        font-size: 3rem; color: #92A8D1; position: relative;
    }
    .res-visual img { width: 100%; height: 100%; object-fit: cover; }
    
    .badge-tipo {
        position: absolute; top: 10px; right: 10px;
        background: rgba(255,255,255,0.9); padding: 4px 10px; border-radius: 10px;
        font-size: 0.7rem; font-weight: bold; color: #555; text-transform: uppercase;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .res-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .res-title { font-size: 1.1rem; font-weight: 800; color: #444; margin-bottom: 8px; line-height: 1.3; }
    .res-desc { font-size: 0.9rem; color: #777; margin-bottom: 15px; flex-grow: 1; line-height: 1.5; }
    
    .res-btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 12px; background: var(--color-primario); 
        color: white; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 0.9rem;
        transition: 0.2s; box-sizing: border-box;
    }
    .res-btn:hover { background: #7fa1c3; }

    /* SIDEBAR */
    .sidebar-widget {
        background: white; padding: 25px; border-radius: 15px; 
        border: 1px solid #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        margin-bottom: 25px;
    }
    .widget-title { margin-top: 0; font-size: 1.1rem; color: #444; border-bottom: 2px solid #f5f5f5; padding-bottom: 10px; margin-bottom: 15px; font-weight: 800; }
    
    .search-form { display: flex; }
    .search-form input { width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px 0 0 10px; outline: none; background: #f9f9f9; }
    .search-form button { background: var(--color-primario); color: white; border: none; padding: 0 15px; border-radius: 0 10px 10px 0; cursor: pointer; }

    .filter-list { list-style: none; padding: 0; margin: 0; }
    .filter-list li { margin-bottom: 8px; }
    .filter-link { 
        display: flex; justify-content: space-between; align-items: center;
        text-decoration: none; color: #666; font-size: 0.95rem; padding: 10px 15px; 
        border-radius: 10px; transition: 0.2s; background: #fdfdfd; border: 1px solid #f0f0f0;
    }
    .filter-link:hover, .filter-link.active { background: var(--color-primario); color: white; border-color: var(--color-primario); }

    @media (max-width: 900px) {
        .main-layout { grid-template-columns: 1fr; }
        .rec-header h1 { font-size: 2.5rem; }
    }
</style>

<div class="rec-header">
    <h1>Recursos Gratuitos</h1>
    <p>Materiales listos para descargar, imprimir y usar.</p>
</div>

<div class="main-layout">
    
    <div>
        <div class="resources-grid">
            <?php if ($result && $result->num_rows > 0): 
                while($r = $result->fetch_assoc()): 
                    $icono = 'fa-file';
                    if($r['tipo']=='pdf') $icono = 'fa-file-pdf';
                    if($r['tipo']=='word') $icono = 'fa-file-word';
                    if($r['tipo']=='imagen') $icono = 'fa-image';
                    
                    $rutaArchivo = $r['archivo'];
                    if (strpos($rutaArchivo, 'assets') === false) $rutaArchivo = 'assets/uploads/recursos/' . $rutaArchivo;

                    $visualHTML = "";
                    if(!empty($r['imagen_destacada'])) {
                        $rutaImg = $r['imagen_destacada'];
                        if (strpos($rutaImg, 'assets') === false) $rutaImg = 'assets/uploads/recursos/portadas/' . $rutaImg;
                        $visualHTML = "<img src='$rutaImg' alt='Portada'>";
                    } else {
                        $visualHTML = "<i class='fa-solid $icono'></i>";
                    }
            ?>
                <div class="resource-card">
                    <div class="res-visual">
                        <span class="badge-tipo"><?php echo strtoupper($r['tipo']); ?></span>
                        <?php echo $visualHTML; ?>
                    </div>
                    <div class="res-body">
                        <div class="res-title"><?php echo $r['titulo']; ?></div>
                        <div class="res-desc"><?php echo strip_tags($r['descripcion']); ?></div>
                        <a href="<?php echo $rutaArchivo; ?>" class="res-btn" download target="_blank">
                            <i class="fa-solid fa-download"></i> Descargar
                        </a>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #888;">
                    <i class="fa-regular fa-folder-open" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3>No encontramos recursos.</h3>
                    <a href="recursos.php" style="color: var(--color-primario); font-weight: bold;">Ver todos</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <aside>
        <div class="sidebar-widget">
            <h4 class="widget-title">Buscar</h4>
            <form action="recursos.php" method="GET" class="search-form">
                <input type="text" name="q" placeholder="Palabra clave..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="sidebar-widget">
            <h4 class="widget-title">¿Para quién es?</h4>
            <ul class="filter-list">
                <li><a href="recursos.php" class="filter-link">Todos</a></li>
                <li><a href="recursos.php?dest=Familias" class="filter-link <?php echo (isset($_GET['dest']) && $_GET['dest']=='Familias')?'active':''; ?>">Familias <i class="fa-solid fa-house"></i></a></li>
                <li><a href="recursos.php?dest=Terapeutas" class="filter-link <?php echo (isset($_GET['dest']) && $_GET['dest']=='Terapeutas')?'active':''; ?>">Terapeutas <i class="fa-solid fa-user-doctor"></i></a></li>
                <li><a href="recursos.php?dest=Niños" class="filter-link <?php echo (isset($_GET['dest']) && $_GET['dest']=='Niños')?'active':''; ?>">Niños <i class="fa-solid fa-child"></i></a></li>
            </ul>
        </div>

        <div class="sidebar-widget">
            <h4 class="widget-title">Formato</h4>
            <ul class="filter-list">
                <li><a href="recursos.php?tipo=pdf" class="filter-link <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='pdf')?'active':''; ?>">PDF <i class="fa-regular fa-file-pdf"></i></a></li>
                <li><a href="recursos.php?tipo=word" class="filter-link <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='word')?'active':''; ?>">Word <i class="fa-regular fa-file-word"></i></a></li>
                <li><a href="recursos.php?tipo=imagen" class="filter-link <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='imagen')?'active':''; ?>">Imagen <i class="fa-regular fa-image"></i></a></li>
            </ul>
        </div>
        
        <?php if(isset($_GET['q']) || isset($_GET['dest']) || isset($_GET['tipo'])): ?>
            <a href="recursos.php" class="btn-grande" style="display:block; text-align:center; background:#eee; color:#555;">Limpiar Filtros</a>
        <?php endif; ?>
    </aside>

</div>

<?php include 'includes/footer.php'; ?>