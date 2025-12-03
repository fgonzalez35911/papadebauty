<?php
session_start();
require '../includes/db_connect.php';
require_once '../includes/funciones_imagen.php'; // Motor de imagen

if (!isset($_SESSION['usuario_id'])) { header("Location: ../index.php"); exit; }

// PROCESAR SUBIDA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dir = "../assets/img/";
    if (!file_exists($dir)) mkdir($dir, 0777, true);

    // Función helper para procesar cada input
    function actualizar_foto($input_name, $clave_db, $conn, $dir) {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
            $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
            $name = "home_" . $input_name . "_" . time() . "." . $ext;
            $destino = $dir . $name;
            
            // Usamos tu motor de imagen (marca de agua + resize)
            if (subir_y_procesar($_FILES[$input_name], $destino)) {
                $ruta_db = "assets/img/" . $name;
                
                // Guardar en BD (Insert o Update)
                $sql = "INSERT INTO configuracion (clave, valor) VALUES ('$clave_db', '$ruta_db') 
                        ON DUPLICATE KEY UPDATE valor = '$ruta_db'";
                $conn->query($sql);
                return true;
            }
        }
        return false;
    }

    $cambios = 0;
    if (actualizar_foto('img_bio', 'img_bio', $conn, $dir)) $cambios++;
    if (actualizar_foto('img_galeria_1', 'img_galeria_1', $conn, $dir)) $cambios++;
    if (actualizar_foto('img_galeria_2', 'img_galeria_2', $conn, $dir)) $cambios++;
    if (actualizar_foto('img_galeria_3', 'img_galeria_3', $conn, $dir)) $cambios++;

    if ($cambios > 0) {
        $mensaje = "<div class='alert alert-success'>✅ Se actualizaron $cambios imágenes correctamente.</div>";
    }
}

// OBTENER CONFIGURACIÓN ACTUAL
$config = [];
$res = $conn->query("SELECT * FROM configuracion");
while($row = $res->fetch_assoc()) {
    $config[$row['clave']] = $row['valor'];
}

// Helper para mostrar imagen o placeholder
function mostrar_img($clave, $config) {
    $src = isset($config[$clave]) ? "../" . $config[$clave] : "https://placehold.co/400x300?text=Sin+Imagen";
    return "<img src='$src' style='width:100%; height:200px; object-fit:cover; border-radius:10px; border:1px solid #ddd; margin-bottom:10px;'>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: #f0f2f5;">

<?php include '../includes/header.php'; ?>

<div class="container" style="max-width:900px; margin:40px auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🏠 Editar Imágenes del Inicio</h2>
        <a href="panel.php" class="btn btn-secondary">Volver al Panel</a>
    </div>

    <?php if(isset($mensaje)) echo $mensaje; ?>

    <form method="POST" enctype="multipart/form-data">
        
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white fw-bold text-primary">1. Sección "Hola, soy Fede"</div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <?php echo mostrar_img('img_bio', $config); ?>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Imagen Principal de Bienvenida</label>
                        <input type="file" name="img_bio" class="form-control" accept="image/*">
                        <div class="form-text">Se recomienda formato vertical o cuadrado.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white fw-bold text-primary">2. Galería de Momentos (Polaroids)</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Foto Izquierda</label>
                        <?php echo mostrar_img('img_galeria_1', $config); ?>
                        <input type="file" name="img_galeria_1" class="form-control form-control-sm" accept="image/*">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Foto Central</label>
                        <?php echo mostrar_img('img_galeria_2', $config); ?>
                        <input type="file" name="img_galeria_2" class="form-control form-control-sm" accept="image/*">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Foto Derecha</label>
                        <?php echo mostrar_img('img_galeria_3', $config); ?>
                        <input type="file" name="img_galeria_3" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success btn-lg fw-bold px-5"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
        </div>
    </form>
</div>

</body>
</html>