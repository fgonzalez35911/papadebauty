<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../index.php"); exit; }

$id_juego = isset($_GET['id_juego']) ? intval($_GET['id_juego']) : 0;
$juego = null;
$contenido = null;

// --- VARIABLES PARA EDICIÓN ---
$id_editar_item = isset($_GET['edit_item']) ? intval($_GET['edit_item']) : 0;
$datos_editar = null;

if($id_juego) {
    $juego = $conn->query("SELECT * FROM juegos WHERE id = $id_juego")->fetch_assoc();
    $contenido = $conn->query("SELECT * FROM juegos_contenido WHERE id_juego = $id_juego ORDER BY orden ASC, id ASC");
    
    if($id_editar_item > 0) {
        $q_edit = $conn->query("SELECT * FROM juegos_contenido WHERE id = $id_editar_item AND id_juego = $id_juego");
        $datos_editar = $q_edit->fetch_assoc();
    }
}

// --- PROCESAR SUBIDA (INSERTAR O EDITAR) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $id_contenido = isset($_POST['id_contenido']) ? intval($_POST['id_contenido']) : 0;
    
    $texto = $conn->real_escape_string($_POST['texto'] ?? ''); 
    $texto_pregunta = $conn->real_escape_string($_POST['texto_pregunta'] ?? ''); // NUEVO
    
    $d1 = $conn->real_escape_string($_POST['d1'] ?? '');
    $d2 = $conn->real_escape_string($_POST['d2'] ?? '');
    $d3 = $conn->real_escape_string($_POST['d3'] ?? '');
    
    $dir_img = "../assets/uploads/juegos/";
    $dir_aud = "../assets/uploads/audios/";
    if (!file_exists($dir_img)) mkdir($dir_img, 0777, true);
    if (!file_exists($dir_aud)) mkdir($dir_aud, 0777, true);

    // FUNCIÓN HELPER PARA SUBIR FOTOS
    function procesar_foto($input_name, $dir, $prefix, $id_juego, $existing = "") {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
            $ext = pathinfo($_FILES[$input_name]["name"], PATHINFO_EXTENSION);
            $name = $id_juego . "_" . $prefix . "_" . time() . "_" . uniqid() . "." . $ext;
            if(move_uploaded_file($_FILES[$input_name]["tmp_name"], $dir . $name)) {
                return "assets/uploads/juegos/" . $name;
            }
        }
        return $existing;
    }

    // 1. IMÁGENES PRINCIPALES
    $ruta_img = ($id_contenido > 0) ? $_POST['img_actual'] : ""; 
    if (isset($_POST['palabra_pregunta'])) {
        $ruta_img = $conn->real_escape_string($_POST['palabra_pregunta']);
    } else {
        $ruta_img = procesar_foto('imagen', $dir_img, 'main', $id_juego, $ruta_img);
    }

    $ruta_img_extra = ($id_contenido > 0) ? $_POST['img_extra_actual'] : "";
    $ruta_img_extra = procesar_foto('imagen_extra', $dir_img, 'extra', $id_juego, $ruta_img_extra);

    // 2. IMÁGENES DE OPCIONES (NUEVO)
    $img_correcta = procesar_foto('file_correcta', $dir_img, 'opt_ok', $id_juego, $_POST['img_correcta_actual']??'');
    $img_d1 = procesar_foto('file_d1', $dir_img, 'opt_d1', $id_juego, $_POST['img_d1_actual']??'');
    $img_d2 = procesar_foto('file_d2', $dir_img, 'opt_d2', $id_juego, $_POST['img_d2_actual']??'');
    $img_d3 = procesar_foto('file_d3', $dir_img, 'opt_d3', $id_juego, $_POST['img_d3_actual']??'');

    // 3. AUDIO
    $ruta_aud = ($id_contenido > 0) ? $_POST['audio_actual'] : "";
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] == 0) {
        $ext = pathinfo($_FILES["audio"]["name"], PATHINFO_EXTENSION);
        $name = $id_juego . "_aud_" . time() . "_" . uniqid() . "." . $ext;
        if(move_uploaded_file($_FILES["audio"]["tmp_name"], $dir_aud . $name)) {
            $ruta_aud = "assets/uploads/audios/" . $name;
        }
    }

    if ($id_contenido > 0) {
        // UPDATE
        $sql = "UPDATE juegos_contenido SET 
                imagen='$ruta_img', imagen_extra='$ruta_img_extra', audio='$ruta_aud', 
                texto_pregunta='$texto_pregunta',
                palabra_correcta='$texto', img_correcta='$img_correcta',
                distractor1='$d1', img_distractor1='$img_d1',
                distractor2='$d2', img_distractor2='$img_d2',
                distractor3='$d3', img_distractor3='$img_d3'
                WHERE id=$id_contenido";
        $conn->query($sql);
    } else {
        // INSERT
        $orden = 0;
        if($juego['tipo_juego'] == 'secuencia') {
            $r = $conn->query("SELECT MAX(orden) as max_o FROM juegos_contenido WHERE id_juego = $id_juego")->fetch_assoc();
            $orden = ($r['max_o'] ?? 0) + 1;
        }
        $sql = "INSERT INTO juegos_contenido (id_juego, imagen, imagen_extra, audio, texto_pregunta, palabra_correcta, img_correcta, distractor1, img_distractor1, distractor2, img_distractor2, distractor3, img_distractor3, orden) 
                VALUES ($id_juego, '$ruta_img', '$ruta_img_extra', '$ruta_aud', '$texto_pregunta', '$texto', '$img_correcta', '$d1', '$img_d1', '$d2', '$img_d2', '$d3', '$img_d3', $orden)";
        $conn->query($sql);
    }
    header("Location: gestionar_contenido.php?id_juego=$id_juego"); exit;
}

if (isset($_GET['del'])) {
    $conn->query("DELETE FROM juegos_contenido WHERE id = ".intval($_GET['del']));
    header("Location: gestionar_contenido.php?id_juego=$id_juego"); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor Avanzado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .instruccion-box { background: #fff3cd; border-left: 5px solid #ffc107; padding: 20px; border-radius: 5px; margin-bottom: 25px; }
        .list-group-item.active { background-color: #0d6efd !important; border-color: #0d6efd !important; color: white !important; }
        .input-group-text { background: #f8f9fa; font-size: 0.8rem; }
        .mini-thumb { height: 30px; border-radius: 4px; margin-left: 5px; vertical-align: middle; }
    </style>
</head>
<body style="background-color: #f0f2f5;">

<?php include '../includes/header.php'; ?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:#555; margin:0;"><i class="fa-solid fa-layer-group"></i> Contenido: <?php echo $juego['titulo'] ?? 'Seleccionar'; ?></h2>
        <div>
            <?php if($juego): ?>
                <a href="editar_juego.php?id=<?php echo $juego['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pencil"></i> Config</a>
            <?php endif; ?>
            <a href="panel.php" class="btn btn-secondary btn-sm">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white p-3">
                    <input type="text" id="searchGames" class="form-control form-control-sm" placeholder="🔍 Buscar..." style="border:none; border-radius:5px;">
                </div>
                <div class="list-group list-group-flush" id="gamesList" style="max-height:600px; overflow-y:auto; position: relative;">
                    <?php 
                    $js = $conn->query("SELECT * FROM juegos ORDER BY titulo ASC");
                    while($j = $js->fetch_assoc()): 
                        $act = ($j['id']==$id_juego)?'active':'';
                    ?>
                        <a href="?id_juego=<?php echo $j['id']; ?>" class="list-group-item list-group-item-action <?php echo $act; ?> game-item">
                            <span class="fw-bold d-block" style="font-size:0.85rem;"><?php echo $j['titulo']; ?></span>
                            <small class="text-muted" style="font-size:0.7rem;">ID: <?php echo $j['id']; ?></small>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <?php if($juego): ?>
                
                <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4 shadow-sm border-0">
                    <input type="hidden" name="id_contenido" value="<?php echo $datos_editar['id'] ?? 0; ?>">
                    
                    <input type="hidden" name="img_actual" value="<?php echo $datos_editar['imagen'] ?? ''; ?>">
                    <input type="hidden" name="img_extra_actual" value="<?php echo $datos_editar['imagen_extra'] ?? ''; ?>">
                    <input type="hidden" name="audio_actual" value="<?php echo $datos_editar['audio'] ?? ''; ?>">
                    <input type="hidden" name="img_correcta_actual" value="<?php echo $datos_editar['img_correcta'] ?? ''; ?>">
                    <input type="hidden" name="img_d1_actual" value="<?php echo $datos_editar['img_distractor1'] ?? ''; ?>">
                    <input type="hidden" name="img_d2_actual" value="<?php echo $datos_editar['img_distractor2'] ?? ''; ?>">
                    <input type="hidden" name="img_d3_actual" value="<?php echo $datos_editar['img_distractor3'] ?? ''; ?>">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="m-0 text-secondary">
                            <?php echo $datos_editar ? '<i class="fa-solid fa-pencil"></i> Editando Elemento' : '<i class="fa-solid fa-plus"></i> Nuevo Elemento'; ?>
                        </h4>
                        <?php if($datos_editar): ?>
                            <a href="?id_juego=<?php echo $id_juego; ?>" class="btn btn-outline-danger btn-sm">Cancelar</a>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Pregunta Específica (Opcional)</label>
                        <input type="text" name="texto_pregunta" class="form-control" placeholder="Ej: ¿Quién creó la bandera?" value="<?php echo $datos_editar['texto_pregunta'] ?? ''; ?>">
                        <small class="text-muted">Si escribes aquí, reemplazará al título general del juego para esta ficha.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Imagen Central</label>
                            <?php if(isset($datos_editar['imagen']) && $datos_editar['imagen']): ?>
                                <img src="../<?php echo $datos_editar['imagen']; ?>" class="mini-thumb">
                            <?php endif; ?>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Audio (Opcional)</label>
                            <input type="file" name="audio" class="form-control" accept="audio/*">
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-success">Respuesta CORRECTA</label>
                        <div class="input-group mb-2">
                            <input type="text" name="texto" class="form-control border-success" placeholder="Texto (Ej: Belgrano)" value="<?php echo $datos_editar['palabra_correcta'] ?? ''; ?>">
                            <span class="input-group-text"><i class="fa-solid fa-image"></i></span>
                            <input type="file" name="file_correcta" class="form-control" accept="image/*">
                        </div>
                        <?php if(isset($datos_editar['img_correcta']) && $datos_editar['img_correcta']): ?>
                            <small class="text-success"><i class="fa-solid fa-check"></i> Imagen cargada: <img src="../<?php echo $datos_editar['img_correcta']; ?>" class="mini-thumb"></small>
                        <?php endif; ?>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label text-muted">Incorrecta 1</label>
                            <div class="input-group mb-1">
                                <input type="text" name="d1" class="form-control" placeholder="Texto" value="<?php echo $datos_editar['distractor1'] ?? ''; ?>">
                                <input type="file" name="file_d1" class="form-control" accept="image/*">
                            </div>
                            <?php if(isset($datos_editar['img_distractor1']) && $datos_editar['img_distractor1']): ?>
                                <img src="../<?php echo $datos_editar['img_distractor1']; ?>" class="mini-thumb">
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted">Incorrecta 2</label>
                            <div class="input-group mb-1">
                                <input type="text" name="d2" class="form-control" placeholder="Texto" value="<?php echo $datos_editar['distractor2'] ?? ''; ?>">
                                <input type="file" name="file_d2" class="form-control" accept="image/*">
                            </div>
                            <?php if(isset($datos_editar['img_distractor2']) && $datos_editar['img_distractor2']): ?>
                                <img src="../<?php echo $datos_editar['img_distractor2']; ?>" class="mini-thumb">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted">Incorrecta 3</label>
                            <div class="input-group mb-1">
                                <input type="text" name="d3" class="form-control" placeholder="Texto" value="<?php echo $datos_editar['distractor3'] ?? ''; ?>">
                                <input type="file" name="file_d3" class="form-control" accept="image/*">
                            </div>
                            <?php if(isset($datos_editar['img_distractor3']) && $datos_editar['img_distractor3']): ?>
                                <img src="../<?php echo $datos_editar['img_distractor3']; ?>" class="mini-thumb">
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" name="guardar" class="btn btn-success mt-4 fw-bold py-2 w-100">
                        <i class="fa-solid fa-save"></i> <?php echo $datos_editar ? 'ACTUALIZAR FICHA' : 'GUARDAR FICHA'; ?>
                    </button>
                </form>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light fw-bold">Contenido Cargado (<?php echo $contenido->num_rows; ?>)</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr>
                                <th>Imagen</th>
                                <th>Pregunta / Respuesta</th>
                                <th width="100" class="text-end">Acciones</th>
                            </tr></thead>
                            <tbody>
                                <?php if($contenido->num_rows > 0): while($row = $contenido->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php if($row['imagen']): ?>
                                                <img src="../<?php echo $row['imagen']; ?>" width="50" style="border-radius:5px;">
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($row['texto_pregunta']): ?>
                                                <div class="text-primary fw-bold mb-1"><?php echo $row['texto_pregunta']; ?></div>
                                            <?php endif; ?>
                                            <strong>✅ <?php echo $row['palabra_correcta']; ?></strong>
                                            <?php if($row['img_correcta']): ?><i class="fa-solid fa-image text-success"></i><?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="?id_juego=<?php echo $id_juego; ?>&edit_item=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm me-1"><i class="fa-solid fa-pencil"></i></a>
                                            <a href="?id_juego=<?php echo $id_juego; ?>&del=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Borrar?');"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="3" class="text-center py-3 text-muted">Vacío.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary text-center py-5">
                    <h3>👈 Seleccioná un juego.</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchGames').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('.game-item').forEach(item => {
            let text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? "" : "none";
        });
    });
    setTimeout(function() {
        const activeItem = document.querySelector('.list-group-item.active');
        if (activeItem) { activeItem.scrollIntoView({ block: 'center', behavior: 'auto' }); }
    }, 300);
</script>
</body>
</html>