<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// --- LÓGICA DE FILTROS Y BÚSQUEDA ---
$search = $_GET['s'] ?? '';
$category_id = $_GET['cat'] ?? 'all';
$where_clauses = [];

// 1. Cláusula de Búsqueda por Título/Contenido
if (!empty($search)) {
    // Usamos real_escape_string para sanitizar la búsqueda
    $search_escaped = $conn->real_escape_string("%$search%");
    $where_clauses[] = "(a.titulo LIKE '$search_escaped' OR a.contenido LIKE '$search_escaped')";
}

// 2. Cláusula de Filtro por Categoría
if ($category_id !== 'all' && is_numeric($category_id)) {
    $where_clauses[] = "a.id_categoria = " . intval($category_id);
}

// Construcción final de la cláusula WHERE
$where_sql = count($where_clauses) > 0 ? " WHERE " . implode(" AND ", $where_clauses) : "";

// --- Consulta principal (Incluye el nombre de la categoría para mostrarlo) ---
$sql = "SELECT a.*, c.nombre AS categoria_nombre FROM articulos a 
        LEFT JOIN categorias_blog c ON a.id_categoria = c.id
        $where_sql 
        ORDER BY a.id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Artículos</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include '../includes/header.php'; ?>
    <style>
        body { background-color: #f4f4f4; display: block; padding: 20px; }
        .container-admin { max-width: 1200px; margin: 0 auto; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }

        /* Estilos de tabla base */
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: var(--color-secundario); color: white; }
        
        /* Botones de acción en tabla de escritorio */
        .btn-accion {
            padding: 8px 12px; border-radius: 5px; color: white !important; margin-right: 5px;
            display: inline-block;
        }
        .btn-editar { background: #ffc107; color: #333 !important; }
        .btn-borrar { background: #dc3545; }

        /* --- ESTILOS CARD VIEW (MÓVIL) --- */
        .articulo-list-mobile {
            display: none; /* Oculto en desktop */
            padding: 10px 0;
        }
        .articulo-card-mobile {
            background: white;
            border-left: 5px solid var(--color-primario);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .card-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #666;
        }
        .card-actions {
            margin-top: 15px;
            text-align: right;
        }
        
        /* CORRECCIÓN DE COLORES PARA BOTONES EN VISTA MÓVIL */
        .articulo-card-mobile .btn-editar { background: #ffc107; color: #333 !important; }
        .articulo-card-mobile .btn-borrar { background: #dc3545; color: white !important; }

        /* MEDIA QUERY PARA HACER EL CAMBIO DE VISTA */
        @media (max-width: 768px) {
            /* Ocultar la tabla clásica en celular */
            .table-responsive { display: none; }
            /* Mostrar la lista de tarjetas en celular */
            .articulo-list-mobile { display: block; }
            /* Filtros en una columna */
            .search-filter-form { flex-direction: column; }
            .search-filter-form input, 
            .search-filter-form select, 
            .search-filter-form button { width: 100%; margin: 5px 0; }
        }
    </style>
</head>
<body>

<div class="container-admin">

    <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Mis Artículos</h2>

    <div class="top-bar">
        <a href="panel.php" class="btn-grande" style="background: #999; font-size: 0.9rem; padding: 10px 20px; margin-right: auto;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
        <a href="editar_articulo.php" class="btn-grande btn-jugar" style="font-size: 0.9rem; padding: 10px 20px;">
            <i class="fa-solid fa-plus"></i> Nueva Nota
        </a>
    </div>
    
    <form method="GET" class="search-filter-form" style="display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; background: white; padding: 20px; border-radius: 8px;">
        <input type="text" name="s" placeholder="Buscar por título o contenido..." 
               value="<?php echo htmlspecialchars($search); ?>" 
               style="flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-size: 1rem;">
        
        <select name="cat" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-size: 1rem;">
            <option value="all">Todas las Categorías</option>
            <?php 
            // Carga dinámica de categorías
            $cats_query = $conn->query("SELECT id, nombre FROM categorias_blog ORDER BY nombre ASC");
            while ($c = $cats_query->fetch_assoc()): 
                $selected = ($c['id'] == $category_id) ? 'selected' : '';
            ?>
                <option value="<?php echo $c['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($c['nombre']); ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit" class="btn-grande btn-jugar" style="font-size: 0.9rem; padding: 10px 20px; margin: 0; box-shadow: none;">
            <i class="fa-solid fa-magnifying-glass"></i> Buscar
        </button>
        
        <?php if (!empty($search) || $category_id !== 'all'): ?>
            <a href="listar_articulos.php" style="color: #dc3545; font-size: 0.9rem; margin-top: 10px; align-self: center;">Limpiar filtros</a>
        <?php endif; ?>
    </form>


    <div class="table-responsive">
        <h3 style="margin-bottom: 15px; color: #555;">Vista de Escritorio</h3>
        <table style="background: white; border-radius: 10px; overflow: hidden;">
            <thead>
                <tr>
                    <th width="80">Imagen</th>
                    <th>Título</th>
                    <th width="150">Categoría</th>
                    <th width="120">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    // Resetear el puntero para el bucle de la tabla
                    $result->data_seek(0);
                    while($row = $result->fetch_assoc()) {
                        // Manejo de imagen
                        $img_path = $row['imagen_destacada'];
                        if (!empty($img_path) && strpos($img_path, 'http') === false && strpos($img_path, '../') === false) {
                            $img_path = "../" . $img_path;
                        }

                        $img_html = empty($img_path) 
                            ? '<span style="color:#ccc; font-size:0.8rem;">N/A</span>' 
                            : '<img src="'.$img_path.'" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                        
                        echo "<tr>";
                        echo "<td>" . $img_html . "</td>";
                        echo "<td><strong>" . htmlspecialchars($row['titulo']) . "</strong></td>";
                        echo "<td>" . htmlspecialchars($row['categoria_nombre'] ?? 'Sin Cat.') . "</td>";
                        
                        echo "<td>
                                <a href='editar_articulo.php?id=" . $row['id'] . "' class='btn-accion btn-editar'><i class='fa-solid fa-pencil'></i></a>
                                <a href='borrar_articulo.php?id=" . $row['id'] . "' class='btn-accion btn-borrar' onclick='return confirm(\"¿Seguro que quieres borrar este artículo?\")'><i class='fa-solid fa-trash'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No se encontraron artículos con esos filtros.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="articulo-list-mobile">
        <h3 style="margin-bottom: 15px; color: #555;">Vista de Móvil</h3>
        <?php
        if ($result && $result->num_rows > 0) {
            // Resetear el puntero para el bucle de las tarjetas
            $result->data_seek(0);
            while($row = $result->fetch_assoc()) {
                $img_path = $row['imagen_destacada'];
                if (!empty($img_path) && strpos($img_path, 'http') === false && strpos($img_path, '../') === false) {
                    $img_path = "../" . $img_path;
                }

                $img_html = empty($img_path) 
                    ? '' 
                    : '<img src="'.$img_path.'" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; float: left; margin-right: 10px;">';

                echo "<div class='articulo-card-mobile'>";
                
                // Título y Categoría
                echo "<div class='card-title'>" . $img_html . htmlspecialchars($row['titulo']) . "</div>";
                echo "<div style='clear: both;'></div>"; // Limpia el float de la imagen

                // Metadatos
                echo "<div class='card-line'>
                        <span><i class='fa-solid fa-tag' style='color:var(--color-primario);'></i> Categoría:</span> 
                        <strong>" . htmlspecialchars($row['categoria_nombre'] ?? 'Sin Categoría') . "</strong>
                      </div>";

                // Acciones (botones grandes para tocar) - ¡COLORES CORREGIDOS!
                echo "<div class='card-actions'>
                        <a href='editar_articulo.php?id=" . $row['id'] . "' class='btn-grande btn-editar' style='font-size: 0.9rem; padding: 8px 15px;'>
                           <i class='fa-solid fa-pencil'></i> Editar
                        </a>
                        <a href='borrar_articulo.php?id=" . $row['id'] . "' class='btn-grande btn-borrar' style='font-size: 0.9rem; padding: 8px 15px;' onclick='return confirm(\"¿Borrar?\")'>
                           <i class='fa-solid fa-trash'></i> Borrar
                        </a>
                      </div>";

                echo "</div>"; // Cierra articulo-card-mobile
            }
        } else {
            echo "<p style='text-align:center; padding: 20px; background:white; border-radius:8px;'>No se encontraron artículos con esos filtros.</p>";
        }
        ?>
    </div>

</div>

</body>
</html>
