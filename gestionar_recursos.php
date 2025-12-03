<?php
session_start(); require '../includes/db_connect.php';
if(!isset($_SESSION['usuario_id'])) header("Location: ../index.php");

// (LÓGICA DE GUARDADO IGUAL QUE ANTES... No la toco para no romper)
// ... [Mantené tu código de guardar/editar arriba de este punto] ...
// Solo reemplazá desde la línea de BÚSQUEDA hacia abajo

// --- LÓGICA DE BÚSQUEDA Y FILTROS ---
$where = "WHERE 1=1";
$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$f_tipo = isset($_GET['f_tipo']) ? $conn->real_escape_string($_GET['f_tipo']) : '';
$f_dest = isset($_GET['f_dest']) ? $conn->real_escape_string($_GET['f_dest']) : '';

if($q) $where .= " AND titulo LIKE '%$q%'";
if($f_tipo) $where .= " AND tipo = '$f_tipo'";
if($f_dest) $where .= " AND destinatario = '$f_dest'";

// --- DETECCIÓN DE EDICIÓN (Lo dejo aquí para que funcione el form) ---
$id_editar = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
$datos_editar = null;
if($id_editar) $datos_editar = $conn->query("SELECT * FROM recursos WHERE id=$id_editar")->fetch_assoc();

// PROCESAR POST (Tu código de guardado va aquí, resumido para el ejemplo)
if(isset($_POST['guardar'])) {
    // ... (Tu lógica de guardado que ya funciona) ...
    // Solo asegurate de redirigir a gestionar_recursos.php al final
}
if(isset($_GET['del'])) { $conn->query("DELETE FROM recursos WHERE id=".intval($_GET['del'])); header("Location: gestionar_recursos.php"); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Admin Recursos</title>
    <script src="https://cdn.tiny.cloud/1/fsu1zolakhx1ihn2slwt050tc9rgv1jejro3mwbyixxr2coh/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({selector:'#editor-contenido', height:200, plugins:'lists link', toolbar:'bold italic | bullist link', language:'es'});</script>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div style="max-width:1000px; margin:20px auto; padding:20px; background:white; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="color:var(--color-primario); margin:0;">Gestionar Recursos</h2>
        <a href="panel.php" class="btn-grande" style="background:#999; font-size:0.9rem;">Volver</a>
    </div>

    <details <?php echo $id_editar ? 'open' : ''; ?> style="background:#f8f9fa; padding:15px; border-radius:10px; margin-bottom:30px; border:1px solid #eee;">
        <summary style="cursor:pointer; font-weight:bold; color:var(--color-primario);">
            <?php echo $id_editar ? 'Editando Recurso (Clic para colapsar)' : '➕ Cargar Nuevo Recurso (Clic para abrir)'; ?>
        </summary>
        <form method="POST" enctype="multipart/form-data" style="margin-top:15px;">
            <input type="hidden" name="archivo_actual" value="<?php echo $datos_editar['archivo'] ?? ''; ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo $datos_editar['imagen_destacada'] ?? ''; ?>">
            
            <div style="display:grid; gap:15px;">
                <input type="text" name="titulo" placeholder="Título" style="width:100%; padding:10px; border:1px solid #ddd;" value="<?php echo $datos_editar['titulo'] ?? ''; ?>" required>
                <textarea id="editor-contenido" name="descripcion"><?php echo $datos_editar['descripcion'] ?? ''; ?></textarea>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <select name="tipo" style="padding:10px; border:1px solid #ddd;">
                        <option value="pdf" <?php echo ($datos_editar['tipo']??'')=='pdf'?'selected':''; ?>>PDF</option>
                        <option value="word" <?php echo ($datos_editar['tipo']??'')=='word'?'selected':''; ?>>Word</option>
                        <option value="imagen" <?php echo ($datos_editar['tipo']??'')=='imagen'?'selected':''; ?>>Imagen</option>
                    </select>
                    <select name="destinatario" style="padding:10px; border:1px solid #ddd;">
                        <option value="Familias" <?php echo ($datos_editar['destinatario']??'')=='Familias'?'selected':''; ?>>Familias</option>
                        <option value="Terapeutas" <?php echo ($datos_editar['destinatario']??'')=='Terapeutas'?'selected':''; ?>>Terapeutas</option>
                        <option value="Niños" <?php echo ($datos_editar['destinatario']??'')=='Niños'?'selected':''; ?>>Niños</option>
                    </select>
                </div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div><label>Archivo:</label><input type="file" name="archivo"></div>
                    <div><label>Portada:</label><input type="file" name="imagen_destacada"></div>
                </div>

                <button type="submit" name="guardar" class="btn-grande"><?php echo $id_editar ? 'Guardar Cambios' : 'Publicar'; ?></button>
            </div>
        </form>
    </details>

    <form method="GET" style="background:#eef4fa; padding:15px; border-radius:10px; margin-bottom:20px; display:grid; grid-template-columns: 2fr 1fr 1fr auto; gap:10px;">
        <input type="text" name="q" placeholder="Buscar..." value="<?php echo $q; ?>" style="padding:10px; border:1px solid #ccc; border-radius:5px;">
        
        <select name="f_dest" style="padding:10px; border:1px solid #ccc; border-radius:5px;">
            <option value="">Todos Destinatarios</option>
            <option value="Familias" <?php echo $f_dest=='Familias'?'selected':''; ?>>Familias</option>
            <option value="Terapeutas" <?php echo $f_dest=='Terapeutas'?'selected':''; ?>>Terapeutas</option>
        </select>

        <select name="f_tipo" style="padding:10px; border:1px solid #ccc; border-radius:5px;">
            <option value="">Todos Formatos</option>
            <option value="pdf" <?php echo $f_tipo=='pdf'?'selected':''; ?>>PDF</option>
            <option value="word" <?php echo $f_tipo=='word'?'selected':''; ?>>Word</option>
        </select>

        <button type="submit" class="btn-grande" style="padding:10px 20px;"><i class="fa-solid fa-filter"></i></button>
    </form>

    <table style="width:100%; border-collapse:collapse;">
        <tr style="background:#f0f2f5; color:#666; text-align:left;"><th style="padding:10px;">Recurso</th><th>Destino</th><th>Tipo</th><th style="text-align:right;">Acción</th></tr>
        <?php 
        $res = $conn->query("SELECT * FROM recursos $where ORDER BY id DESC"); 
        while($r = $res->fetch_assoc()): ?>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:15px;"><strong><?php echo $r['titulo']; ?></strong></td>
            <td><span style="background:#fff3e0; color:#e65100; padding:3px 8px; border-radius:10px; font-size:0.8rem;"><?php echo $r['destinatario']; ?></span></td>
            <td><?php echo strtoupper($r['tipo']); ?></td>
            <td style="text-align:right;">
                <a href="?editar=<?php echo $r['id']; ?>" class="btn-grande" style="background:#FFB347; padding:5px 10px; font-size:0.8rem;"><i class="fa-solid fa-pencil"></i></a>
                <a href="?del=<?php echo $r['id']; ?>" class="btn-grande" style="background:#FF6B6B; padding:5px 10px; font-size:0.8rem;" onclick="return confirm('¿X?');"><i class="fa-solid fa-trash"></i></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div></body></html>