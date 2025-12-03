<?php
session_start();
require '../includes/db_connect.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit; }

$id = ''; $titulo = ''; $contenido = ''; $imagen = ''; $cat_id = 1;
$is_editing = false;

// Si hay ID, cargamos los datos para editar
if (isset($_GET['id'])) {
    $is_editing = true;
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM articulos WHERE id = $id";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $titulo = $row['titulo'];
        $contenido = $row['contenido'];
        $imagen = $row['imagen_destacada'];
        $cat_id = $row['id_categoria'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Contenido</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <?php include '../includes/header.php'; ?>
    <script src="https://cdn.tiny.cloud/1/fsu1zolakhx1ihn2slwt050tc9rgv1jejro3mwbyixxr2coh/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
        <script src="https://cdn.tiny.cloud/1/fsu1zolakhx1ihn2slwt050tc9rgv1jejro3mwbyixxr2coh/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <script>
      tinymce.init({
        selector: '#editor-contenido',
        height: 500,
        // PLUGINS COMPLETOS (Escritorio y móvil usan esta lista)
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code fullscreen textcolor',
        
        // TOOLBAR ESCRITORIO (Completa y con todas las opciones pedidas)
        toolbar: 'cut copy paste selectall | undo redo | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | aligncenter alignjustify alignleft alignright | link image media table | numlist bullist indent outdent | emoticons charmap | removeformat | code fullscreen',
        
        // MENÚ CONTEXTUAL (Clic derecho o toque largo en móvil)
        contextmenu: "cut copy paste | selectall | link image table", 
        
        // CONFIGURACIÓN MÓVIL: USAMOS TEMA 'SILVER' (Escritorio) para asegurar que carga.
        mobile: {
            theme: 'silver', 
            plugins: 'anchor autolink charmap emoticons image link lists media table visualblocks wordcount paste textcolor',
            // Toolbar móvil simplificada pero con los comandos clave (incluyendo Cut/Copy/Paste/Select All)
            toolbar: 'cut copy paste selectall | undo redo | bold italic underline forecolor | link image table'
        },
        
        language: 'es', 
      });
    </script>


    
    <style>
        body { background: #f0f2f5; padding: 20px; display: block; }
        
        .editor-container { 
            width: 100%;
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); 
        }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input[type="text"], select { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem; }

        .header-editor {
            display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;
        }

        /* Responsive movil */
        @media (max-width: 600px) {
            body { padding: 10px; padding-top: 80px; } /* Ajuste por el header fijo */
            .editor-container { padding: 20px; }
            .header-editor { flex-direction: column; gap: 15px; align-items: flex-start; }
            .btn-grande { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

<div class="editor-container">
    <div class="header-editor">
        <h1 style="margin:0; color:var(--color-secundario); font-size: 1.5rem;">
            <?php echo $is_editing ? '✏️ Editar Artículo' : '✨ Nuevo Artículo'; ?>
        </h1>
        <a href="listar_articulos.php" class="btn-grande" style="background:#ccc; font-size:0.9rem; padding:10px 20px;">Cancelar</a>
    </div>

    <form action="guardar_articulo.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="imagen_actual" value="<?php echo $imagen; ?>">

        <div class="form-group">
            <label>Título Principal</label>
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required placeholder="Ej: Estrategias para la calma...">
        </div>

        <div class="form-group">
            <label>Categoría</label>
            <select name="id_categoria">
                <?php
                $cats = $conn->query("SELECT * FROM categorias_blog");
                while($c = $cats->fetch_assoc()) {
                    $selected = ($c['id'] == $cat_id) ? 'selected' : '';
                    echo "<option value='".$c['id']."' $selected>".$c['nombre']."</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Contenido del Artículo (Usa el botón "Seleccionar Todo" para borrar)</label>
            <textarea id="editor-contenido" name="contenido"><?php echo $contenido; ?></textarea>
        </div>

        <div class="form-group">
            <label>Imagen Destacada</label>
            <?php if($imagen): ?>
                <div style="margin-bottom:10px;">
                    <img src="../<?php echo $imagen; ?>" style="height:150px; border-radius:10px; object-fit:cover; max-width: 100%;">
                    <p style="font-size:0.8rem; color:#666;">(Imagen actual)</p>
                </div>
            <?php endif; ?>
            <input type="file" name="nueva_imagen" accept="image/*">
        </div>

        <div style="text-align: right; margin-top: 30px;">
            <button type="submit" class="btn-grande btn-jugar" style="font-size: 1.2rem; padding: 15px 50px; width: 100%;">
                <?php echo $is_editing ? 'Guardar Cambios' : 'Publicar Ahora'; ?>
            </button>
        </div>
    </form>
</div>

</body>
</html>
