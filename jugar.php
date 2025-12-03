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

// 2. BUSCAR CONTENIDO MANUAL
$sql_contenido = "SELECT imagen, palabra_correcta, distractor1, distractor2, distractor3, audio 
                  FROM juegos_contenido WHERE id_juego = $game_id ORDER BY id ASC";
$res_contenido = $conn->query($sql_contenido);
$contenido_manual = [];
while($row = $res_contenido->fetch_assoc()){
    $contenido_manual[] = $row;
}

$config_json = $juego['configuracion'] ?: '{}';
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

    /* --- FLECHA ANIMADA EXAGERADA --- */
    .btn-hint {
        position: absolute;
        right: 100%; /* A la izquierda del botón */
        margin-right: 10px; /* Espacio */
        
        background: #FFB347; 
        color: white;
        padding: 8px 15px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 900;
        box-shadow: 0 5px 15px rgba(255, 179, 71, 0.5);
        
        /* Animación de rebote lateral fuerte */
        animation: senalarFuerte 0.8s infinite ease-in-out;
        
        pointer-events: none; 
        white-space: nowrap;
        display: flex; align-items: center; gap: 8px;
        z-index: 10;
    }
    
    /* Triangulito del globo */
    .btn-hint::after {
        content: ''; position: absolute; right: -8px; top: 50%; margin-top: -8px;
        border-width: 8px; border-style: solid;
        border-color: transparent transparent transparent #FFB347;
    }

    @keyframes senalarFuerte {
        0% { transform: translateX(0); }
        50% { transform: translateX(-15px); } /* Se mueve 15px a la izquierda */
        100% { transform: translateX(0); }
    }

    /* Ocultar flecha en fullscreen */
    .game-card:fullscreen .btn-hint { display: none; }

</style>

<div class="game-wrapper">
    <div class="game-card" id="juego-principal">
        <div class="game-header">
            <h2 style="margin:0; color:var(--color-secundario); font-size:1.5rem;">
                <i class="fa-solid fa-gamepad"></i> <?php echo $juego['titulo']; ?>
            </h2>
            
            <div class="header-actions">
                
                <div class="btn-hint" id="hint-full">
                    ¡Pantalla Completa! <i class="fa-solid fa-arrow-right-long"></i>
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
                if ($juego['tipo_juego'] == 'texto_drag') {
                    include 'motores/motor_texto_drag.php';
                } else if ($juego['tipo_juego'] == 'asociacion') {
                     include 'motores/motor_universal.php'; 
                } else {
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
            // Entrar
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) { 
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { 
                elem.msRequestFullscreen();
            }
            icon.classList.replace('fa-expand', 'fa-compress');
            
            // Ocultar la flecha inmediatamente al entrar
            if(hint) hint.style.display = 'none';
            
        } else {
            // Salir
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