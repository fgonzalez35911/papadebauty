<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../index.php"); exit; }

$id_juego = isset($_GET['id_juego']) ? intval($_GET['id_juego']) : 0;
$juego = null;
$contenido = null;

if($id_juego) {
    $juego = $conn->query("SELECT * FROM juegos WHERE id = $id_juego")->fetch_assoc();
    $contenido = $conn->query("SELECT * FROM juegos_contenido WHERE id_juego = $id_juego ORDER BY id ASC");
}

// --- PROCESAR SUBIDA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    
    // Recogemos los datos básicos
    $texto = $conn->real_escape_string($_POST['texto'] ?? ''); 
    $d1 = $conn->real_escape_string($_POST['d1'] ?? '');
    $d2 = $conn->real_escape_string($_POST['d2'] ?? '');
    $d3 = $conn->real_escape_string($_POST['d3'] ?? '');
    
    // Directorios para imágenes/audio (si hicieran falta)
    $dir_img = "../assets/uploads/juegos/";
    $dir_aud = "../assets/uploads/audios/";
    if (!file_exists($dir_img)) mkdir($dir_img, 0777, true);
    if (!file_exists($dir_aud)) mkdir($dir_aud, 0777, true);

    // 1. Campo IMAGEN (Puede ser un archivo O una palabra pregunta)
    $ruta_img = "";
    
    // CASO ESPECIAL: Si es juego de texto, la "imagen" es la PALABRA PREGUNTA que viene del input
    if (isset($_POST['palabra_pregunta']) && !empty($_POST['palabra_pregunta'])) {
        $ruta_img = $conn->real_escape_string($_POST['palabra_pregunta']);
    } 
    // CASO NORMAL: Subida de archivo
    elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ext = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
        $name = $id_juego . "_img_" . time() . "." . $ext;
        if(move_uploaded_file($_FILES["imagen"]["tmp_name"], $dir_img . $name)) {
            $ruta_img = "assets/uploads/juegos/" . $name;
        }
    }

    // 2. Imagen Extra (Solo para Asociación de Imágenes)
    $ruta_img_extra = "";
    if (isset($_FILES['imagen_extra']) && $_FILES['imagen_extra']['error'] == 0) {
        $ext = pathinfo($_FILES["imagen_extra"]["name"], PATHINFO_EXTENSION);
        $name = $id_juego . "_imgB_" . time() . "." . $ext;
        if(move_uploaded_file($_FILES["imagen_extra"]["tmp_name"], $dir_img . $name)) {
            $ruta_img_extra = "assets/uploads/juegos/" . $name;
        }
    }

    // 3. Audio
    $ruta_aud = "";
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] == 0) {
        $ext = pathinfo($_FILES["audio"]["name"], PATHINFO_EXTENSION);
        $name = $id_juego . "_aud_" . time() . "." . $ext;
        if(move_uploaded_file($_FILES["audio"]["tmp_name"], $dir_aud . $name)) {
            $ruta_aud = "assets/uploads/audios/" . $name;
        }
    }

    $sql = "INSERT INTO juegos_contenido (id_juego, imagen, imagen_extra, audio, palabra_correcta, distractor1, distractor2, distractor3) 
            VALUES ($id_juego, '$ruta_img', '$ruta_img_extra', '$ruta_aud', '$texto', '$d1', '$d2', '$d3')";
    $conn->query($sql);
    header("Location: gestionar_contenido.php?id_juego=$id_juego"); exit;
}

// Borrar
if (isset($_GET['del'])) {
    $id_del = intval($_GET['del']);
    $conn->query("DELETE FROM juegos_contenido WHERE id = $id_del");
    header("Location: gestionar_contenido.php?id_juego=$id_juego"); exit;
}

// Instrucciones dinámicas
function getInstrucciones($tipo) {
    switch($tipo) {
        case 'seleccion': return ['t'=>'Preguntas / Selección', 'd'=>'Sube una imagen y la respuesta correcta.'];
        case 'memoria': return ['t'=>'Memotest', 'd'=>'Sube solo la imagen. El sistema crea la pareja automáticamente.'];
        case 'secuencia': return ['t'=>'Ordenar Secuencia', 'd'=>'Sube los pasos EN ORDEN (1, 2, 3...).'];
        case 'pintura': return ['t'=>'Arte / Pintura', 'd'=>'Sube dibujo en blanco y negro.'];
        case 'asociacion': return ['t'=>'Asociación Imágenes', 'd'=>'Sube 2 imágenes para unir (Ej: Sol y Luna).'];
        
        // NUEVO TIPO AGREGADO AL PANEL
        case 'texto_drag': return ['t'=>'Arrastrar Palabras', 'd'=>'Aquí NO subas fotos. Escribe la Palabra Pregunta (Fija) y la Respuesta (Móvil).', 'ejemplo'=>'<b>Ejemplo:</b><br>Palabra Fija: DÍA<br>Respuesta Correcta: NOCHE<br>Distractores: SOL, LUZ, TARDE'];
        
        default: return ['t'=>'', 'd'=>''];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Contenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: #f0f2f5;">

<?php include '../includes/header.php'; ?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:#555; margin:0;"><i class="fa-solid fa-layer-group"></i> Constructor</h2>
        <a href="panel.php" class="btn btn-secondary">Volver</a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white p-3">
                    <input type="text" id="searchGames" class="form-control form-control-sm" placeholder="🔍 Buscar juego..." style="border:none; border-radius:5px;">
                </div>
                <div class="list-group list-group-flush" id="gamesList" style="max-height:600px; overflow-y:auto;">
                    <?php 
                    $js = $conn->query("SELECT * FROM juegos ORDER BY titulo ASC");
                    while($j = $js->fetch_assoc()): 
                        $act = ($j['id']==$id_juego)?'active':'';
                        $tipo = substr($j['tipo_juego'],0,4);
                        $bg = ($j['tipo_juego']=='texto_drag') ? 'warning' : 'secondary';
                    ?>
                        <a href="?id_juego=<?php echo $j['id']; ?>" class="list-group-item list-group-item-action <?php echo $act; ?> game-item">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <span class="fw-bold game-title" style="font-size:0.95rem;"><?php echo $j['titulo']; ?></span>
                                <span class="badge bg-<?php echo $bg; ?>" style="font-size:0.65rem; text-transform:uppercase;"><?php echo $tipo; ?></span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <?php if($juego): $info = getInstrucciones($juego['tipo_juego']); ?>
                
                <div class="alert alert-info shadow-sm border-0" style="border-left: 5px solid #0dcaf0;">
                    <h5 class="alert-heading fw-bold"><i class="fa-solid fa-lightbulb"></i> <?php echo $info['t']; ?></h5>
                    <p class="mb-0"><?php echo $info['d']; ?></p>
                    <?php if(isset($info['ejemplo'])): ?><hr><small><?php echo $info['ejemplo']; ?></small><?php endif; ?>
                </div>

                <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4 shadow-sm border-0">
                    <h4 class="mb-3 text-secondary">Cargar Ficha</h4>
                    
                    <?php if($juego['tipo_juego'] == 'texto_drag'): ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary" style="font-size:1.1rem;">1. Palabra FIJA (Pregunta)</label>
                                <input type="text" name="palabra_pregunta" class="form-control form-control-lg border-primary" placeholder="Ej: GRANDE" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success" style="font-size:1.1rem;">2. Palabra CORRECTA (Móvil)</label>
                                <input type="text" name="texto" class="form-control form-control-lg border-success" placeholder="Ej: PEQUEÑO" required>
                            </div>
                            
                            <div class="col-12"><hr> <label class="form-label fw-bold text-muted">Opciones Incorrectas (Distractores)</label></div>
                            
                            <div class="col-md-4"><input type="text" name="d1" class="form-control" placeholder="Incorrecta 1" required></div>
                            <div class="col-md-4"><input type="text" name="d2" class="form-control" placeholder="Incorrecta 2" required></div>
                            <div class="col-md-4"><input type="text" name="d3" class="form-control" placeholder="Incorrecta 3" required></div>
                        </div>

                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Imagen Principal</label>
                                <input type="file" name="imagen" class="form-control" accept="image/*">
                            </div>
                            
                            <?php if($juego['tipo_juego'] == 'asociacion'): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Imagen Pareja</label>
                                <input type="file" name="imagen_extra" class="form-control" accept="image/*">
                            </div>
                            <?php endif; ?>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Audio (MP3) <small class="text-muted">- Opcional</small></label>
                                <input type="file" name="audio" class="form-control" accept="audio/*">
                            </div>
                        </div>

                        <?php if($juego['tipo_juego'] == 'seleccion'): ?>
                            <div class="mb-3"><input type="text" name="texto" class="form-control border-success" placeholder="Respuesta CORRECTA" required></div>
                            <div class="row g-2">
                                <div class="col"><input type="text" name="d1" class="form-control" placeholder="Incorrecta 1" required></div>
                                <div class="col"><input type="text" name="d2" class="form-control" placeholder="Incorrecta 2" required></div>
                                <div class="col"><input type="text" name="d3" class="form-control" placeholder="Incorrecta 3" required></div>
                            </div>
                        <?php elseif($juego['tipo_juego'] == 'asociacion'): ?>
                             <div class="row g-2">
                                <div class="col-md-6"><label>Texto Imagen 1</label><input type="text" name="texto" class="form-control" required></div>
                                <div class="col-md-6"><label>Texto Imagen 2</label><input type="text" name="d1" class="form-control" required></div>
                            </div>
                        <?php else: ?>
                            <div class="mb-3"><label class="form-label fw-bold">Texto / Descripción</label><input type="text" name="texto" class="form-control"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <button type="submit" name="guardar" class="btn btn-success mt-4 fw-bold py-2" style="width:100%;">GUARDAR FICHA</button>
                </form>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light fw-bold">Contenido Cargado</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Contenido</th><th>Respuesta</th><th width="50"></th></tr></thead>
                            <tbody>
                                <?php if($contenido->num_rows > 0): while($row = $contenido->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php if($juego['tipo_juego']=='texto_drag'): ?>
                                                <span class="badge bg-primary fs-6"><?php echo $row['imagen']; ?></span>
                                            <?php elseif($row['imagen']): ?>
                                                <img src="../<?php echo $row['imagen']; ?>" width="40">
                                            <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo $row['palabra_correcta']; ?></strong>
                                            <?php if($juego['tipo_juego']=='texto_drag' || $juego['tipo_juego']=='seleccion'): ?>
                                                <br><small class="text-muted">Distractores: <?php echo $row['distractor1'].', '.$row['distractor2'].', '.$row['distractor3']; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="?id_juego=<?php echo $id_juego; ?>&del=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">X</a></td>
                                    </tr>
                                <?php endwhile; else: ?><tr><td colspan="3" class="text-center py-3">Vacío.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
document.getElementById('searchGames').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.game-item').forEach(item => {
        let text = item.querySelector('.game-title').textContent.toLowerCase();
        item.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>