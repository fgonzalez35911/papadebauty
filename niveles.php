<?php 
require 'includes/db_connect.php'; 
include 'includes/header.php'; 

// Validar que nos pasaron un "Grupo" (ej: Matematica)
if (!isset($_GET['grupo'])) {
    echo "<script>window.location='juegos.php';</script>"; exit;
}

$grupo = $conn->real_escape_string($_GET['grupo']);
?>

<div style="padding: 2rem; text-align: center;">
    
    <a href="juegos.php" style="display: inline-block; margin-bottom: 20px; color: #666; text-decoration: none;">
        <i class="fa-solid fa-arrow-left"></i> Volver a Categorías
    </a>

    <h1 style="color: var(--color-primario); margin-bottom: 10px; text-transform: capitalize;">
        <?php echo $grupo; ?>
    </h1>
    <p style="color: #666; margin-bottom: 3rem;">Selecciona un nivel para empezar</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; max-width: 1000px; margin: 0 auto;">

        <?php
        // Buscamos juegos que contengan la palabra clave en el título
        // Ej: Si grupo es 'Matemática', busca 'Matemática Nivel 1', 'Matemática Inicial', etc.
        $sql = "SELECT * FROM juegos WHERE titulo LIKE '%$grupo%' AND activo = 1 ORDER BY id ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $contador = 1;
            while($juego = $result->fetch_assoc()) {
                echo '<a href="jugar.php?id=' . $juego['id'] . '" style="text-decoration: none; color: inherit;">';
                echo '  <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.2s; position: relative; overflow: hidden;">';
                
                // Etiqueta de Nivel
                echo '      <div style="position: absolute; top: 0; left: 0; background: var(--color-secundario); color: white; padding: 5px 15px; border-bottom-right-radius: 20px; font-weight: bold;">';
                echo '          Nivel ' . $contador;
                echo '      </div>';
                
                echo '      <i class="fa-solid fa-play" style="font-size: 3rem; color: var(--color-primario); margin-top: 20px; margin-bottom: 15px;"></i>';
                
                echo '      <h3 style="font-size: 1.2rem; color: var(--color-texto);">' . $juego['titulo'] . '</h3>';
                echo '      <p style="color: #999; font-size: 0.9rem;">' . $juego['descripcion'] . '</p>';
                
                echo '  </div>';
                echo '</a>';
                $contador++;
            }
        } else {
            echo "<p>No hay niveles disponibles para este grupo.</p>";
        }
        ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>