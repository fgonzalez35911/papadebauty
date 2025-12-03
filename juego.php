<?php
// 1. ACTIVAR REPORTE DE ERRORES (Para ver qué pasa si algo falla)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. INCLUIR ARCHIVOS CORRECTAMENTE (Rutas corregidas a 'includes/')
require 'includes/db_connect.php'; 
require 'includes/header.php';     // <--- AQUÍ ESTABA EL ERROR

// 3. VALIDAR ID
if (!isset($_GET['id'])) {
    echo "<div style='padding:50px; text-align:center'>Falta el ID del juego. <a href='juegos.php'>Volver</a></div>";
    require 'includes/footer.php'; 
    exit;
}

$id_juego = intval($_GET['id']);

// 4. CONSULTA A LA BASE DE DATOS
// Unimos la tabla 'juegos' con 'motores_juego' para saber qué archivo cargar
$sql = "SELECT j.*, m.archivo_base 
        FROM juegos j 
        LEFT JOIN motores_juego m ON j.id_motor = m.id 
        WHERE j.id = $id_juego";

$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "<div style='padding:50px; text-align:center'>
            <h2>Juego no encontrado (ID: $id_juego)</h2>
            <p>Es posible que el juego no exista en la base de datos o esté desactivado.</p>
            <a href='juegos.php' class='btn-grande'>Volver al menú</a>
          </div>";
    require 'includes/footer.php';
    exit;
}

$juego = $result->fetch_assoc();

// Preparar configuración para JS (si existe)
$config_json = !empty($juego['configuracion']) ? $juego['configuracion'] : '{}';
?>

<style>
    .game-conteiner-pro {
        max-width: 900px; margin: 20px auto; background: white;
        border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden; min-height: 500px;
    }
    .game-header {
        padding: 15px 20px; background: #f8f9fa; border-bottom: 1px solid #eee;
        display: flex; justify-content: space-between; align-items: center;
    }
    .game-body { padding: 0; position: relative; }
</style>

<script>
    const gameConfig = <?php echo $config_json; ?>;
    const datosJuego = gameConfig; // Compatibilidad con motores viejos
    console.log("Cargando juego:", "<?php echo $juego['titulo']; ?>");
</script>

<div class="zona-juego">
    <div class="game-conteiner-pro">
        
        <div class="game-header">
            <h2 style="margin:0; color: var(--color-primario);"><?php echo $juego['titulo']; ?></h2>
            <a href="juegos.php" class="btn-grande" style="padding: 5px 15px; font-size: 0.9rem; background:#ccc;">Salir</a>
        </div>

        <div class="game-body">
            <?php
            // 6. CARGAR EL MOTOR DESDE LA CARPETA 'motores'
            $archivo_motor = 'motores/motor_universal.php';

            if (file_exists($archivo_motor)) {
                include $archivo_motor;
            } else {
                echo "<div style='padding:40px; text-align:center; color:red;'>
                        <h3>Error de Archivo</h3>
                        <p>El sistema busca: <strong>$archivo_motor</strong></p>
                        <p>Verificá que el archivo exista dentro de la carpeta <code>motores</code>.</p>
                      </div>";
            }
            ?>
        </div>
    </div>
    
    <div style="max-width:800px; margin: 0 auto; text-align:center; color:#666;">
        <p><?php echo $juego['descripcion']; ?></p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>