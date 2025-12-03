<?php
session_start(); require '../includes/db_connect.php';
if(!isset($_SESSION['usuario_id'])) header("Location: ../index.php");

// LÓGICA DE FILTRADO
$where = "WHERE 1=1";
$cat_filter = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
$type_filter = isset($_GET['tipo']) ? $conn->real_escape_string($_GET['tipo']) : '';
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if($cat_filter > 0) $where .= " AND id_categoria = $cat_filter";
if(!empty($type_filter)) $where .= " AND tipo_juego = '$type_filter'";
if(!empty($search)) $where .= " AND (titulo LIKE '%$search%' OR descripcion LIKE '%$search%')";

// CONSULTA
$sql = "SELECT j.*, c.nombre as cat_nombre 
        FROM juegos j 
        LEFT JOIN categorias_blog c ON j.id_categoria = c.id 
        $where 
        ORDER BY j.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html><html><head><title>Listar Juegos</title></head><body>
<?php include '../includes/header.php'; ?>

<div style="max-width:1100px; margin:30px auto; padding:20px; background:white; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h2 style="color:var(--color-primario); margin:0;"><i class="fa-solid fa-gamepad"></i> Mis Juegos (<?php echo $result->num_rows; ?>)</h2>
        <a href="editar_juego.php" class="btn-grande" style="padding:10px 20px; font-size:0.9rem;">+ Nuevo Juego</a>
    </div>

    <form action="" method="GET" style="background:#f4f8fb; padding:20px; border-radius:15px; margin-bottom:30px; border:1px solid #e0e0e0;">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
            
            <input type="text" name="q" placeholder="Buscar por título..." value="<?php echo $search; ?>" style="padding:12px; border:1px solid #ccc; border-radius:8px;">
            
            <select name="cat" style="padding:12px; border:1px solid #ccc; border-radius:8px;">
                <option value="0">Todas las Categorías</option>
                <?php 
                $cats = $conn->query("SELECT * FROM categorias_blog");
                while($c = $cats->fetch_assoc()) {
                    $sel = ($c['id'] == $cat_filter) ? 'selected' : '';
                    echo "<option value='".$c['id']."' $sel>".$c['nombre']."</option>";
                }
                ?>
            </select>

            <select name="tipo" style="padding:12px; border:1px solid #ccc; border-radius:8px;">
                <option value="">Todos los Tipos</option>
                <option value="seleccion" <?php echo ($type_filter=='seleccion')?'selected':''; ?>>Selección (Preguntas)</option>
                <option value="memoria" <?php echo ($type_filter=='memoria')?'selected':''; ?>>Memoria</option>
                <option value="secuencia" <?php echo ($type_filter=='secuencia')?'selected':''; ?>>Secuencia</option>
                <option value="pintura" <?php echo ($type_filter=='pintura')?'selected':''; ?>>Pintura</option>
            </select>

            <button type="submit" class="btn-grande" style="width:100%; border-radius:8px;">Filtrar</button>
        </div>
        <?php if($search || $cat_filter || $type_filter): ?>
            <div style="margin-top:10px; text-align:right;">
                <a href="listar_juegos.php" style="color:#FF6B6B; font-weight:bold; font-size:0.9rem;">Borrar Filtros</a>
            </div>
        <?php endif; ?>
    </form>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <tr style="background:var(--color-primario); color:white; text-align:left;">
                <th style="padding:15px; border-radius:10px 0 0 10px;">Juego</th>
                <th style="padding:15px;">Categoría</th>
                <th style="padding:15px;">Tipo</th>
                <th style="padding:15px;">Estado</th>
                <th style="padding:15px; text-align:right; border-radius:0 10px 10px 0;">Acciones</th>
            </tr>
            <?php if($result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:15px;">
                    <strong><?php echo $row['titulo']; ?></strong>
                    <br><small style="color:#888;"><?php echo substr($row['descripcion'],0,50); ?>...</small>
                </td>
                <td style="padding:15px;"><span style="background:#eee; padding:3px 8px; border-radius:10px; font-size:0.8rem;"><?php echo $row['cat_nombre']; ?></span></td>
                <td style="padding:15px; text-transform:capitalize;"><?php echo $row['tipo_juego']; ?></td>
                <td style="padding:15px;">
                    <?php echo ($row['activo']) ? '<span style="color:green; font-weight:bold;">Activo</span>' : '<span style="color:red;">Oculto</span>'; ?>
                </td>
                <td style="padding:15px; text-align:right;">
                    <a href="gestionar_contenido.php?id_juego=<?php echo $row['id']; ?>" class="btn-grande" style="background:#FFB347; padding:5px 10px; font-size:0.8rem; margin-right:5px;" title="Cargar Contenido"><i class="fa-solid fa-layer-group"></i></a>
                    <a href="editar_juego.php?id=<?php echo $row['id']; ?>" class="btn-grande" style="background:#92A8D1; padding:5px 10px; font-size:0.8rem;" title="Editar Config"><i class="fa-solid fa-pencil"></i></a>
                </td>
            </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5" style="padding:30px; text-align:center;">No se encontraron juegos.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>
</body></html>