<?php 
// ACTIVAR ERRORES PARA DEBUG
ini_set('display_errors', 1); error_reporting(E_ALL);

require 'includes/db_connect.php'; 
require 'includes/header.php'; 

if (!isset($_GET['id'])) {
    echo "<div style='padding:2rem; text-align:center;'>Falta ID. <a href='juegos.php'>Volver</a></div>";
    require 'includes/footer.php'; exit;
}
$game_id = intval($_GET['id']);

// 1. Obtener datos básicos del juego
$sql = "SELECT * FROM juegos WHERE id = $game_id";
$juego = $conn->query($sql)->fetch_assoc();

if (!$juego) {
    echo "Juego no encontrado."; require 'includes/footer.php'; exit;
}

// 2. BUSCAR CONTENIDO (CORREGIDO: Usamos SELECT * para traer las nuevas columnas de fotos y preguntas)
$sql_contenido = "SELECT * FROM juegos_contenido WHERE id_juego = $game_id ORDER BY orden ASC, id ASC";
$res_contenido = $conn->query($sql_contenido);

$contenido_manual = [];
if ($res_contenido) {
    while($row = $res_contenido->fetch_assoc()){
        $contenido_manual[] = $row;
    }
}

$config_json = !empty($juego['configuracion']) ? $juego['configuracion'] : '{}';
?>

<script>
    const contenidoJuego = <?php echo json_encode($contenido_manual); ?>;
    const gameConfig = <?php echo $config_json; ?>; 
    const tipoJuego = "<?php echo $juego['tipo_juego']; ?>";
    console.log("Juego:", "<?php echo $juego['titulo']; ?>");
</script>

<style>
    .game-wrapper { 
        padding: 20px; min-height: 80vh; background: #f4f7f6; 
        display: flex; justify-content: center; align-items: flex-start;
    }
    
    .game-card { 
        width: 100%; max-width: 900px; background: white; 
        border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
        overflow: hidden; display: flex; flex-direction: column;
        transition: all 0.3s ease;
    }
    
    .game-header { 
        padding: 15px 30px; background: #f8f9fa; border-bottom: 2px solid #eee; 
        display: flex; justify-content: space-between; align-items: center; 
    }
    
    .game-area { 
        padding: 20px; min-height: 500px; 
        display: flex; justify-content: center; flex-direction: column;
        flex: 1; 
    }

    /* MODO ZEN (FULLSCREEN) */
    .game-card:fullscreen {
        width: 100vw; height: 100vh; max-width: none; border-radius: 0;
        display: flex; flex-direction: column;
        overflow-y: auto; padding: 0; background: white;
    }

    .game-card:fullscreen .game-header { padding: 10px 20px; background: #fff; border-bottom: 1px solid #eee; }
    .game-card:fullscreen .game-area { justify-content: center; min-height: auto; }

    /* CONTROLES */
    .header-actions { display: flex; gap: 15px; position: relative; align-items: center; }
    
    .btn-icon { 
        width: 45px; height: 45px; border-radius: 50%; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem;
        transition: transform 0.2s; position: relative; z-index: 2;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .btn-icon:hover { transform: scale(1.1); }

    /* --- FLECHA ANIMADA --- */
    .btn-hint {
        position: absolute;
        right: 100%; 
        margin-right: 10px; 
        background: #FFB347; 
        color: white;
        padding: 8px 15px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 900;
        box-shadow: 0 5px 15px rgba(255, 179, 71, 0.5);
        animation: senalarFuerte 0.8s infinite ease-in-out;
        pointer-events: none; 
        white-space: nowrap;
        display: flex; align-items: center; gap: 8px;
        z-index: 10;
    }
    
    .btn-hint::after {
        content: ''; position: absolute; right: -8px; top: 50%; margin-top: -8px;
        border-width: 8px; border-style: solid;
        border-color: transparent transparent transparent #FFB347;
    }

    @keyframes senalarFuerte {
        0% { transform: translateX(0); }
        50% { transform: translateX(-15px); } 
        100% { transform: translateX(0); }
    }

    .game-card:fullscreen .btn-hint { display: none; }

    /* --- FIX MÓVIL: Evitar scroll y arreglar header --- */
    @media (max-width: 768px) {
        .game-wrapper { padding: 5px; min-height: auto; }
        .game-card { border-radius: 15px; margin-bottom: 5px; }
        .game-header { padding: 10px; }
        .game-header h2 { font-size: 1.1rem !important; }
        .btn-hint { display: none !important; } /* Ocultar el cartel molesto */
        .btn-icon { width: 35px; height: 35px; font-size: 1rem; }
        .header-actions { gap: 8px; }
        .header-actions .btn-grande { padding: 6px 15px !important; font-size: 0.9rem !important; }
        .game-area { padding: 10px; min-height: auto; }
    }
</style>

<div class="game-wrapper">
    <div class="game-card" id="juego-principal">
        <div class="game-header">
            <h2 style="margin:0; color:var(--color-secundario); font-size:1.5rem;">
                <i class="fa-solid fa-gamepad"></i> <?php echo $juego['titulo']; ?>
            </h2>
            
            <div class="header-actions">
                
                <div class="btn-hint" id="hint-full">
                    Grande <i class="fa-solid fa-arrow-right-long"></i>
                </div>

                <button class="btn-icon" style="background:var(--color-primario);" onclick="toggleFullScreen()" title="Pantalla Completa">
                    <i class="fa-solid fa-expand" id="icon-screen"></i>
                </button>
                
                <a href="juegos.php" class="btn-grande" style="padding:10px 25px; background:#eee; color:#555!important; font-size:0.9rem; border:1px solid #ddd;">
                    Salir
                </a>
            </div>
        </div>

        <div class="game-area" id="contenedor-juego">
    <?php 
        $tipo = $juego['tipo_juego'];

        if ($tipo == 'texto_drag') {
            include 'motores/motor_texto_drag.php';
        } 
        else if ($tipo == 'verbos') {
            include 'motores/motor_verbos.php'; 
        } 
        else if ($tipo == 'pronombres') {
            include 'motores/motor_pronombres.php';
        }
        else {
            include 'motores/motor_universal.php';
        }
    ?>
</div>
    </div>
</div>

<script>
    function toggleFullScreen() {
        const elem = document.getElementById("juego-principal");
        const icon = document.getElementById("icon-screen");
        const hint = document.getElementById("hint-full");

        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) { 
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { 
                elem.msRequestFullscreen();
            }
            icon.classList.replace('fa-expand', 'fa-compress');
            if(hint) hint.style.display = 'none';
            
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) { 
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { 
                document.msExitFullscreen();
            }
            icon.classList.replace('fa-compress', 'fa-expand');
        }
    }

    document.addEventListener('fullscreenchange', (event) => {
        const icon = document.getElementById("icon-screen");
        if (!document.fullscreenElement) {
            icon.classList.replace('fa-compress', 'fa-expand');
        }
    });
</script>

<?php require 'includes/footer.php'; ?>