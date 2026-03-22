<?php
session_start(); require '../includes/db_connect.php';
if(!isset($_SESSION['usuario_id'])) header("Location: ../index.php");

// --- PROCESAR ACCIONES MASIVAS ---
if(isset($_POST['accion_lote']) && isset($_POST['ids'])) {
    $ids = implode(',', array_map('intval', $_POST['ids']));
    if(!empty($ids)) {
        if($_POST['accion_lote'] == 'activar') {
            $conn->query("UPDATE juegos SET activo = 1 WHERE id IN ($ids)");
        } elseif($_POST['accion_lote'] == 'desactivar') {
            $conn->query("UPDATE juegos SET activo = 0 WHERE id IN ($ids)");
        } elseif($_POST['accion_lote'] == 'borrar') {
            $conn->query("DELETE FROM juegos WHERE id IN ($ids)");
        }
    }
    header("Location: listar_juegos.php"); exit;
}

// --- PROCESAR SWITCH RÁPIDO ---
if(isset($_GET['toggle_id'])) {
    $tid = intval($_GET['toggle_id']);
    $conn->query("UPDATE juegos SET activo = NOT activo WHERE id = $tid");
    header("Location: listar_juegos.php"); exit;
}

// FILTROS Y ORDEN (Mantenemos tu lógica)
$where = "WHERE 1=1";
$cat_filter = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'updated_at'; // Default Updated
$order_dir = isset($_GET['dir']) && $_GET['dir'] == 'asc' ? 'ASC' : 'DESC';
$new_dir = ($order_dir == 'ASC') ? 'desc' : 'asc';

$orderBySql = "ORDER BY j.updated_at DESC";
if($sort_by == 'titulo') $orderBySql = "ORDER BY j.titulo $order_dir";
if($sort_by == 'id') $orderBySql = "ORDER BY j.id $order_dir";

if($cat_filter > 0) $where .= " AND id_categoria = $cat_filter";
if(!empty($search)) $where .= " AND (titulo LIKE '%$search%' OR descripcion LIKE '%$search%')";

$sql = "SELECT j.*, c.nombre as cat_nombre FROM juegos j LEFT JOIN categorias_blog c ON j.id_categoria = c.id $where $orderBySql";
$result = $conn->query($sql);
?>
<!DOCTYPE html><html><head><title>Listar Juegos</title></head><body>
<?php include '../includes/header.php'; ?>

<style>
    /* ESTILO SWITCH */
    .switch { position: relative; display: inline-block; width: 40px; height: 20px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 20px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #88B04B; }
    input:checked + .slider:before { transform: translateX(20px); }
</style>

<div style="max-width:1100px; margin:30px auto; padding:20px; background:white; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="color:var(--color-primario); margin:0;"><i class="fa-solid fa-gamepad"></i> Mis Juegos (<?php echo $result->num_rows; ?>)</h2>
        <a href="editar_juego.php" class="btn-grande" style="padding:10px 20px; font-size:0.9rem;">+ Nuevo Juego</a>
    </div>

    <form method="POST" id="formLote">
        
        <div style="background:#f4f8fb; padding:15px; border-radius:10px; margin-bottom:20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="text" name="q" placeholder="Buscar..." value="<?php echo $search; ?>" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); window.location='?q='+this.value; }" style="padding:8px; border:1px solid #ccc; border-radius:5px;">
            
            <div style="flex-grow:1;"></div>

            <select name="accion_lote" class="form-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                <option value="">Acciones en Lote...</option>
                <option value="activar">🟢 Activar Marcados</option>
                <option value="desactivar">🔴 Ocultar Marcados</option>
                <option value="borrar">🗑️ Borrar Marcados</option>
            </select>
            <button type="button" onclick="if(confirm('¿Aplicar acción a los seleccionados?')) document.getElementById('formLote').submit();" class="btn-grande" style="background:#666; font-size:0.8rem; padding:8px 15px;">Aplicar</button>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <tr style="background:var(--color-primario); color:white; text-align:left;">
                    <th style="padding:10px; width:30px;"><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th style="padding:15px;"><a href="?sort=id&dir=<?php echo $new_dir; ?>" style="color:white; text-decoration:none;">ID <i class="fa-solid fa-sort"></i></a></th>
                    <th style="padding:15px;"><a href="?sort=titulo&dir=<?php echo $new_dir; ?>" style="color:white; text-decoration:none;">Juego / Título <i class="fa-solid fa-sort"></i></a></th>
                    <th style="padding:15px;">Categoría</th>
                    <th style="padding:15px;">Estado</th>
                    <th style="padding:15px; text-align:right;">Acciones</th>
                </tr>
                <?php if($result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>"></td>
                    <td style="padding:15px; color:#999;">#<?php echo $row['id']; ?></td>
                    <td style="padding:15px;">
                        <strong><?php echo $row['titulo']; ?></strong>
                        <br><small style="color:#aaa; font-size:0.75rem;">Modificado: <?php echo date("d/m H:i", strtotime($row['updated_at'])); ?></small>
                    </td>
                    <td style="padding:15px;"><span style="background:#eee; padding:3px 8px; border-radius:10px; font-size:0.8rem;"><?php echo $row['cat_nombre']; ?></span></td>
                    <td style="padding:15px;">
                        <label class="switch">
                            <input type="checkbox" <?php echo ($row['activo']) ? 'checked' : ''; ?> onchange="window.location='?toggle_id=<?php echo $row['id']; ?>'">
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td style="padding:15px; text-align:right; white-space:nowrap;">
                        <a href="gestionar_contenido.php?id_juego=<?php echo $row['id']; ?>" class="btn-grande" style="background:#FFB347; padding:5px 10px; font-size:0.8rem; margin-right:5px;"><i class="fa-solid fa-layer-group"></i></a>
                        <a href="editar_juego.php?id=<?php echo $row['id']; ?>" class="btn-grande" style="background:#92A8D1; padding:5px 10px; font-size:0.8rem;"><i class="fa-solid fa-pencil"></i></a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="6" style="padding:30px; text-align:center;">No se encontraron juegos.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </form>
</div>

<script>
function toggleAll(source) {
    checkboxes = document.getElementsByName('ids[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>
</body></html>