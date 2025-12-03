<?php
session_start();
if (!isset($_SESSION['usuario_id'])) die("Acceso denegado");

// CONFIGURACIÓN
$base_dir = realpath('../assets'); // Carpeta raíz de medios
$req_dir = isset($_GET['dir']) ? $_GET['dir'] : '';

// SEGURIDAD: Evitar salir de assets
$target_path = realpath($base_dir . '/' . $req_dir);
if (!$target_path || strpos($target_path, $base_dir) !== 0) {
    $target_path = $base_dir;
    $req_dir = '';
}

// ESCANEAR
$items = scandir($target_path);
$carpetas = [];
$archivos = [];

foreach ($items as $item) {
    if ($item == '.') continue;
    
    // Opción para volver atrás
    if ($item == '..') {
        if ($req_dir != '') { // Solo mostramos volver si no estamos en la raíz
            $padre = dirname($req_dir);
            if ($padre == '.') $padre = '';
            $carpetas[] = ['nombre' => '.. (Volver)', 'ruta' => $padre, 'tipo' => 'back', 'sort' => -1];
        }
        continue;
    }

    $ruta_completa = $target_path . '/' . $item;
    $ruta_relativa = ($req_dir ? $req_dir . '/' : '') . $item;

    if (is_dir($ruta_completa)) {
        $carpetas[] = ['nombre' => $item, 'ruta' => $ruta_relativa, 'tipo' => 'dir', 'sort' => 0];
    } else {
        $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            // INTENTAMOS EXTRAER EL NÚMERO FINAL PARA ORDENAR
            // Busca: guion bajo o medio o inicio, seguido de DIGITOS, seguido de punto
            $peso = 999999; // Peso alto por defecto (al final)
            if (preg_match('/(?:[-_]|^)(\d+)\.[a-zA-Z0-9]+$/', $item, $coincidencias)) {
                $peso = intval($coincidencias[1]);
            }
            
            $archivos[] = [
                'nombre' => $item, 
                'ruta' => 'assets/' . $ruta_relativa, 
                'thumb' => '../assets/' . $ruta_relativa,
                'sort' => $peso
            ];
        }
    }
}

// FUNCIÓN DE ORDENAMIENTO PERSONALIZADO
usort($archivos, function($a, $b) {
    // 1. Si ambos tienen número extraído, comparamos los números
    if ($a['sort'] !== 999999 && $b['sort'] !== 999999) {
        return $a['sort'] - $b['sort'];
    }
    // 2. Si solo A tiene número, A va antes
    if ($a['sort'] !== 999999) return -1;
    // 3. Si solo B tiene número, B va antes
    if ($b['sort'] !== 999999) return 1;
    
    // 4. Si ninguno tiene número, orden alfabético normal
    return strcasecmp($a['nombre'], $b['nombre']);
});

?>
<div style="padding:10px;">
    <div style="background:#f0f0f0; padding:10px; margin-bottom:15px; border-radius:5px; border:1px solid #ddd; font-size:0.9rem;">
        <i class="fa-regular fa-folder-open"></i> <strong>Ruta:</strong> /assets/<?php echo $req_dir; ?>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap:10px;">
        
        <?php foreach ($carpetas as $c): ?>
            <div onclick="cargarBrowser('<?php echo $c['ruta']; ?>')" 
                 style="cursor:pointer; background:#fff8e1; padding:15px 5px; border-radius:8px; text-align:center; border:1px solid #ffe0b2; transition:0.2s;">
                <i class="fa-solid <?php echo ($c['tipo']=='back')?'fa-arrow-turn-up':'fa-folder'; ?>" style="font-size:2.5rem; color:#ff9800; display:block; margin-bottom:5px;"></i>
                <span style="font-size:0.85rem; word-break:break-word; font-weight:bold; color:#5d4037;"><?php echo $c['nombre']; ?></span>
            </div>
        <?php endforeach; ?>

        <?php foreach ($archivos as $a): ?>
            <div onclick="seleccionarArchivo('<?php echo $a['ruta']; ?>')" 
                 style="cursor:pointer; background:white; padding:5px; border-radius:8px; text-align:center; border:1px solid #eee; transition:0.2s; position:relative;"
                 onmouseover="this.style.borderColor='#92A8D1'; this.style.backgroundColor='#f0f8ff';"
                 onmouseout="this.style.borderColor='#eee'; this.style.backgroundColor='white';">
                
                <div style="height:90px; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f9f9f9; border-radius:5px;">
                    <img src="<?php echo $a['thumb']; ?>" loading="lazy" style="max-width:100%; max-height:100%; object-fit:contain;">
                </div>
                
                <span style="font-size:0.75rem; word-break:break-all; display:block; margin-top:5px; line-height:1.2; color:#444;">
                    <?php 
                    // Si tiene número, lo destacamos en negrita para que lo encuentres rápido
                    if ($a['sort'] !== 999999) {
                        echo str_replace((string)$a['sort'], "<b>{$a['sort']}</b>", $a['nombre']);
                    } else {
                        echo $a['nombre'];
                    }
                    ?>
                </span>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($carpetas) && empty($archivos)): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:30px; color:#999;">
                <i class="fa-regular fa-folder-open" style="font-size:2rem;"></i><br>Carpeta vacía
            </div>
        <?php endif; ?>
    </div>
</div>