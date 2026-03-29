<?php
session_start();
require '../includes/db_connect.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: listar_juegos.php"); exit; }

$id = ''; $titulo = ''; $descripcion = ''; $instrucciones = ''; $tipo_juego = 'seleccion';
$instruccion_jugador = ''; $activo = 1; $imagen_actual = 'default.jpg';
$id_categoria = 1;
// Valores por defecto para los tags manuales
$tag_peques = 0; $tag_escolar = 0; $tag_teen = 0;
$is_editing = false;

if (isset($_GET['id'])) {
    $is_editing = true;
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM juegos WHERE id = $id";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $titulo = $row['titulo'];
        $descripcion = $row['descripcion'];
        $instrucciones = $row['instrucciones_admin']; 
        $instruccion_jugador = $row['instruccion_jugador'];
        $activo = $row['activo'];
        $imagen_actual = $row['imagen_portada'];
        $id_categoria = $row['id_categoria'];
        $tipo_juego = $row['tipo_juego'];
        // Cargar estado de los checkboxes
        $tag_peques = $row['tag_peques'];
        $tag_escolar = $row['tag_escolar'];
        $tag_teen = $row['tag_teen'];
    }
}
$cats = $conn->query("SELECT * FROM categorias_blog");
?>
<?php include '../includes/header.php'; ?>
<style>
    body { background: #f4f4f4; padding: 120px 20px 40px !important; }
    .editor-box { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .form-group { margin-bottom: 20px; }
    label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
    input[type="text"], textarea, select { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; }
    .checkbox-group label { display: inline-block; margin-right: 20px; font-weight: normal; cursor: pointer; }
    .checkbox-group input { margin-right: 8px; transform: scale(1.2); }
</style>

<div class="editor-box">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <h2 style="margin:0; color: var(--color-primario);"><?php echo $is_editing ? '✏️ Editar Juego' : '✨ Nuevo Juego'; ?></h2>
        <a href="listar_juegos.php" class="btn btn-secondary">Cancelar</a>
    </div>

    <form action="guardar_juego.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="imagen_actual" value="<?php echo $imagen_actual; ?>">

        <div class="row">
            <div class="col-md-8 form-group">
                <label>Título</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>
            </div>
            <div class="col-md-4 form-group">
                <label>Mecánica del Juego</label>
                <select name="tipo_juego">
                    <option value="seleccion" <?php echo ($tipo_juego=='seleccion')?'selected':''; ?>>Selección Simple</option>
                    <option value="memoria" <?php echo ($tipo_juego=='memoria')?'selected':''; ?>>Memoria</option>
                    <option value="secuencia" <?php echo ($tipo_juego=='secuencia')?'selected':''; ?>>Secuencia</option>
                    <option value="verbos" <?php echo ($tipo_juego=='verbos')?'selected':''; ?>>Verbos</option>
                    <option value="pronombres" <?php echo ($tipo_juego=='pronombres')?'selected':''; ?>>Pronombres</option>
                    <option value="texto_drag" <?php echo ($tipo_juego=='texto_drag')?'selected':''; ?>>Arrastrar Texto</option>
                    <option value="puzzle" <?php echo ($tipo_juego=='puzzle')?'selected':''; ?>>Rompecabezas (Puzzle)</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="background:#f0f8ff; padding:15px; border-radius:10px; border:1px solid #d0e8ff;">
            <label style="color:#0056b3;">Etiquetas de Edad (Marcá las que correspondan)</label>
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="tag_peques" value="1" <?php echo ($tag_peques==1)?'checked':''; ?>> 
                    🧸 Peques (2-5)
                </label>
                <label>
                    <input type="checkbox" name="tag_escolar" value="1" <?php echo ($tag_escolar==1)?'checked':''; ?>> 
                    🎒 Chicos (6-9)
                </label>
                <label>
                    <input type="checkbox" name="tag_teen" value="1" <?php echo ($tag_teen==1)?'checked':''; ?>> 
                    🎓 +10 Años
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Frase para el Niño (Título en el juego)</label>
            <input type="text" name="instruccion_jugador" value="<?php echo htmlspecialchars($instruccion_jugador); ?>">
        </div>

        <div class="form-group">
            <label>Descripción Pública</label>
            <textarea name="descripcion" rows="2"><?php echo htmlspecialchars($descripcion); ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-4 form-group">
                <label>Mecánica del Juego</label>
                <select name="tipo_juego">
                    <?php $tipo_actual = isset($row['tipo_juego']) ? $row['tipo_juego'] : 'seleccion'; ?>
                    <option value="seleccion" <?php echo ($tipo_actual=='seleccion')?'selected':''; ?>>Selección Simple</option>
                    <option value="texto_drag" <?php echo ($tipo_actual=='texto_drag')?'selected':''; ?>>Arrastrar Texto</option>
                    <option value="verbos" <?php echo ($tipo_actual=='verbos')?'selected':''; ?>>🌟 Conjugación de Verbos</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>Estado</label>
                <select name="activo">
                    <option value="1" <?php echo ($activo==1)?'selected':''; ?>>Activo</option>
                    <option value="0" <?php echo ($activo==0)?'selected':''; ?>>Oculto</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>Portada</label>
                <?php if($imagen_actual) echo "<img src='../$imagen_actual' height='40' class='me-2'>"; ?>
                <input type="file" name="nueva_imagen" accept="image/*">
            </div>
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <button type="submit" class="btn btn-primary btn-lg px-5">Guardar Cambios</button>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>