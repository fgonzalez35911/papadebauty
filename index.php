<?php 
require 'includes/db_connect.php'; // Necesario para cargar configuración
include 'includes/header.php'; 

// --- CARGAR CONFIGURACIÓN DE IMÁGENES ---
$config_home = [];
$sql_conf = "SELECT * FROM configuracion";
$res_conf = $conn->query($sql_conf);
if ($res_conf) {
    while($row = $res_conf->fetch_assoc()) {
        $config_home[$row['clave']] = $row['valor'];
    }
}

// Función helper para no repetir código
function get_img_home($clave, $default) {
    global $config_home;
    return !empty($config_home[$clave]) ? $config_home[$clave] : $default;
}

// Definimos las rutas finales usando la BD o el default
$img_bio = get_img_home('img_bio', 'assets/img/papaybau.jpg');
$img_gal_1 = get_img_home('img_galeria_1', 'assets/img/papaybau2.jpg');
$img_gal_2 = get_img_home('img_galeria_2', 'assets/img/papaybau3.jpg');
$img_gal_3 = get_img_home('img_galeria_3', 'assets/img/papaybau1.jpeg');
?>

<style>
    /* --- ESTILOS GLOBALES DEL INDEX --- */
    section { position: relative; z-index: 2; }

    /* HERO 100% PANTALLA Y TRANSPARENTE */
    .hero {
        min-height: 100vh; 
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.7) 0%, rgba(253, 251, 247, 0) 70%);
        padding: 0 20px; 
        text-align: center;
        position: relative;
        margin-top: -80px; /* Compensa el header fijo */
        padding-top: 120px; 
        box-sizing: border-box;
    }

    .hero-content { max-width: 900px; width: 100%; position: relative; z-index: 2; }

    .hero h1 {
        font-size: 4.5rem; font-weight: 900; color: var(--color-primario);
        margin-bottom: 15px; line-height: 1.1; text-shadow: 3px 3px 0px white;
    }

    .hero p {
        font-size: 1.5rem; color: #666; 
        margin-bottom: 30px; 
        line-height: 1.6; max-width: 700px; margin-left: auto; margin-right: auto;
        background: rgba(255,255,255,0.6); backdrop-filter: blur(5px);
        padding: 15px; border-radius: 15px;
    }

    /* BOTONES */
    .btn-hero {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 18px 45px; border-radius: 50px; font-size: 1.2rem; font-weight: 800; text-decoration: none;
        transition: transform 0.3s, box-shadow 0.3s; margin: 10px;
    }
    .btn-play { background: var(--color-primario); color: white; box-shadow: 0 10px 30px rgba(146, 168, 209, 0.4); }
    .btn-padres { background: white; color: var(--color-primario); border: 2px solid var(--color-primario); }
    .btn-hero:hover { transform: translateY(-5px); }

    /* SECCIÓN BIO */
    .section-bio { padding: 100px 20px; max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
    .bio-img { width: 100%; border-radius: 30px; box-shadow: 20px 20px 0 rgba(146, 168, 209, 0.2); transform: rotate(-2deg); }
    .bio-text { background: rgba(255,255,255,0.85); padding: 40px; border-radius: 20px; backdrop-filter: blur(5px); border: 1px solid white; }
    .tag-bio { background: #E8F5E9; color: #2E7D32; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; text-transform: uppercase; display: inline-block; margin-bottom: 15px; }

    /* SECCIÓN SEGURA */
    .section-safe { padding: 80px 20px; text-align: center; background: linear-gradient(to bottom, #fff, #fcfcfc); margin: 40px 0; }
    .safe-box { max-width: 900px; margin: 0 auto; background: white; border-radius: 25px; padding: 50px; box-shadow: 0 20px 60px rgba(0,0,0,0.08); position: relative; overflow: hidden; }
    .safe-box::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 6px; background: #92A8D1; }
    .safe-icon { font-size: 4rem; color: #92A8D1; margin-bottom: 25px; display: block; }
    .safe-title { font-size: 2.2rem; color: #444; margin-bottom: 15px; font-weight: 800; }
    .safe-text { font-size: 1.2rem; color: #666; line-height: 1.8; }

    /* PARA QUIÉN ES */
    .section-target { padding: 80px 20px; max-width: 1200px; margin: 0 auto; }
    .target-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 40px; }
    .target-card { background: rgba(255,255,255,0.9); padding: 35px 25px; border-radius: 20px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-top: 5px solid #ccc; border: 1px solid #f9f9f9; }
    .target-card h3 { font-size: 1.4rem; margin: 15px 0 10px; color: #444; }
    
    /* METODOLOGÍA */
    .metodo-section { padding: 100px 20px; text-align: center; }
    .metodo-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto; }
    .card-metodo { background: white; padding: 40px 25px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); transition: 0.3s; }

    /* GALERÍA */
    .galeria-section { padding: 100px 20px; text-align: center; }
    .polaroid-grid { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin-top: 60px; }
    .polaroid { background: white; padding: 15px 15px 50px 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transform: rotate(-3deg); width: 280px; border: 1px solid #eee; }
    .polaroid:nth-child(2) { transform: rotate(2deg); margin-top: 30px; }
    .polaroid img { width: 100%; height: 260px; object-fit: cover; filter: grayscale(20%); }
    .caption { margin-top: 20px; font-weight: bold; font-size: 1.1rem; color: #555; }

    /* FAQ */
    .section-faq { padding: 80px 20px; max-width: 800px; margin: 0 auto; }
    .faq-item { background: white; border-radius: 15px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid var(--color-primario); }
    .faq-q { font-weight: 800; font-size: 1.1rem; color: #444; margin-bottom: 10px; display: block; }
    .faq-a { color: #666; line-height: 1.6; }

    /* CONTACTO */
    .contact-section { padding: 100px 20px; text-align: center; }
    .contact-card { background: white; padding: 50px; border-radius: 30px; box-shadow: 0 20px 60px rgba(146, 168, 209, 0.25); margin-top: 40px; max-width: 700px; margin-left: auto; margin-right: auto; }
    .form-input { width: 100%; padding: 18px; margin-bottom: 20px; border: 2px solid #eee; border-radius: 15px; outline: none; font-family: inherit; font-size: 1rem; }
    .btn-send { background: var(--color-primario); color: white; border: none; width: 100%; padding: 18px; font-weight: 800; font-size: 1.2rem; border-radius: 15px; cursor: pointer; }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .hero { padding-top: 110px; padding-bottom: 40px; min-height: 85vh; justify-content: flex-start; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .hero p { font-size: 1.1rem; margin-bottom: 25px; padding: 10px; }
        .hero-buttons { display: flex; flex-direction: column; gap: 10px; width: 100%; max-width: 300px; }
        .btn-hero { width: 100%; justify-content: center; margin: 0; box-sizing: border-box; padding: 15px 20px; font-size: 1rem; }
        .section-bio { grid-template-columns: 1fr; padding: 60px 20px; }
        .target-grid, .metodo-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="hero">
    <div class="hero-content">
        <h1>Aprender es una<br>Gran Aventura</h1>
        <p>Una plataforma neurodiversa, segura y gratuita. Juegos terapéuticos, recursos visuales y herramientas diseñadas para potenciar habilidades a su propio ritmo.</p>
        <div class="hero-buttons">
            <a href="juegos.php" class="btn-hero btn-play"><i class="fa-solid fa-gamepad"></i> EMPEZAR A JUGAR</a>
            <a href="padres.php" class="btn-hero btn-padres"><i class="fa-solid fa-book-open"></i> BLOG PARA PADRES</a>
        </div>
    </div>
</div>

<section class="section-bio">
    <div class="bio-img-container">
        <img src="<?php echo $img_bio; ?>" class="bio-img" alt="Papá y Bauti">
    </div>
    <div class="bio-text">
        <span class="tag-bio">Nuestra Historia</span>
        <h2 style="font-size: 2.5rem; margin-bottom: 20px; color:#444;">Hola, soy Federico</h2>
        <p style="font-size: 1.1rem; line-height: 1.8; color: #555;">
            Como papá de Bauti, entiendo los desafíos y las alegrías de criar a un niño dentro del espectro autista. Este sitio nace de la necesidad de encontrar herramientas digitales amables.
        </p>
        <p style="font-size: 1.1rem; line-height: 1.8; color: #555;">
            Aquí no buscamos "curar" nada, sino conectar y potenciar. Cada juego está pensado para trabajar una habilidad específica desde la calma.
        </p>
    </div>
</section>

<section class="section-safe">
    <div class="safe-box">
        <i class="fa-solid fa-shield-heart safe-icon"></i>
        <h2 class="safe-title">Un Espacio 100% Seguro</h2>
        <p class="safe-text">
            Creamos <strong>Papá de Bauti</strong> con una única misión: ayudar. <br>
            Aquí no encontrarás anuncios, ni pop-ups molestos, ni ventas ocultas. 
            Todo el contenido es gratuito y está diseñado para que los chicos naveguen con total tranquilidad.
        </p>
    </div>
</section>

<section class="section-target">
    <div style="text-align: center;">
        <h2 style="font-size: 2.5rem; color: #444; font-weight: 800;">Pensado para todos</h2>
        <p style="color: #888; font-size: 1.1rem;">Neurodiversidad en todas sus formas.</p>
    </div>
    <div class="target-grid">
        <div class="target-card" style="border-top-color: #92A8D1;">
            <i class="fa-solid fa-puzzle-piece" style="font-size: 3rem; color: #92A8D1; margin-bottom: 15px;"></i>
            <h3>Autismo (TEA)</h3>
            <p>Estructura clara, anticipación y apoyos visuales para reducir la ansiedad.</p>
        </div>
        <div class="target-card" style="border-top-color: #FFB347;">
            <i class="fa-solid fa-bolt" style="font-size: 3rem; color: #FFB347; margin-bottom: 15px;"></i>
            <h3>TDAH</h3>
            <p>Actividades dinámicas y cortas para mantener la atención sostenida.</p>
        </div>
        <div class="target-card" style="border-top-color: #F7CAC9;">
            <i class="fa-solid fa-comments" style="font-size: 3rem; color: #F7CAC9; margin-bottom: 15px;"></i>
            <h3>Desafíos del Lenguaje</h3>
            <p>Juegos de fonética, vocabulario y construcción de frases.</p>
        </div>
        <div class="target-card" style="border-top-color: #4ECDC4;">
            <i class="fa-solid fa-baby" style="font-size: 3rem; color: #4ECDC4; margin-bottom: 15px;"></i>
            <h3>Estimulación Temprana</h3>
            <p>Ideal para preescolares que están descubriendo el mundo.</p>
        </div>
        <div class="target-card" style="border-top-color: #88B04B;">
            <i class="fa-solid fa-users" style="font-size: 3rem; color: #88B04B; margin-bottom: 15px;"></i>
            <h3>Síndrome de Down</h3>
            <p>Aprendizaje visual paso a paso para facilitar la comprensión de conceptos.</p>
        </div>
        <div class="target-card" style="border-top-color: #FF6B6B;">
            <i class="fa-solid fa-font" style="font-size: 3rem; color: #FF6B6B; margin-bottom: 15px;"></i>
            <h3>Dislexia</h3>
            <p>Fuentes claras y apoyo de audio para facilitar la lectoescritura.</p>
        </div>
    </div>
</section>

<section class="metodo-section">
    <h2 style="font-size: 2.5rem; color: #444; margin-bottom: 60px;">Nuestra Metodología</h2>
    <div class="metodo-grid">
        <div class="card-metodo">
            <i class="fa-solid fa-eye" style="font-size: 3rem; color: #F7CAC9; margin-bottom: 20px;"></i>
            <h3>Apoyos Visuales</h3>
            <p style="color: #666;">Pictogramas claros y sin distracciones.</p>
        </div>
        <div class="card-metodo">
            <i class="fa-solid fa-hands-holding-circle" style="font-size: 3rem; color: #92A8D1; margin-bottom: 20px;"></i>
            <h3>Sin Errores</h3>
            <p style="color: #666;">Aprendizaje positivo sin sonidos frustrantes.</p>
        </div>
        <div class="card-metodo">
            <i class="fa-solid fa-music" style="font-size: 3rem; color: #FFB347; margin-bottom: 20px;"></i>
            <h3>Calma Sensorial</h3>
            <p style="color: #666;">Colores pasteles y ambiente regulado.</p>
        </div>
        <div class="card-metodo">
            <i class="fa-solid fa-user-check" style="font-size: 3rem; color: #4ECDC4; margin-bottom: 20px;"></i>
            <h3>Autonomía</h3>
            <p style="color: #666;">Diseño intuitivo para que puedan navegar solos.</p>
        </div>
    </div>
</section>

<section id="galeria" class="galeria-section">
    <h2 style="font-size: 3rem; color: var(--color-primario);">Galería de Momentos</h2>
    <div class="polaroid-grid">
        <div class="polaroid">
            <img src="<?php echo $img_gal_1; ?>" alt="Jugando">
            <div class="caption">Descubriendo 🎨</div>
        </div>
        <div class="polaroid">
            <img src="<?php echo $img_gal_2; ?>" alt="Bauti">
            <div class="caption">¡Juntos! ❤️</div>
        </div>
        <div class="polaroid">
            <img src="<?php echo $img_gal_3; ?>" alt="Aprendiendo">
            <div class="caption">Aprendiendo 🚀</div>
        </div>
    </div>
    <div style="margin-top: 60px;">
        <a href="galeria.php" class="btn-hero btn-padres" style="border-color:#ccc; color:#666;">VER ÁLBUM COMPLETO</a>
    </div>
</section>

<section class="section-faq">
    <h2 style="font-size: 2.5rem; text-align: center; color: #444; margin-bottom: 40px;">Preguntas Frecuentes</h2>
    <div class="faq-item">
        <span class="faq-q">¿Tiene algún costo usar la plataforma?</span>
        <div class="faq-a">No, es 100% gratuita. Todo el material está disponible sin suscripciones ni pagos ocultos.</div>
    </div>
    <div class="faq-item">
        <span class="faq-q">¿Necesito instalar una aplicación?</span>
        <div class="faq-a">No, funciona directamente desde el navegador (Chrome, Safari) en tu computadora, tablet o celular.</div>
    </div>
    <div class="faq-item">
        <span class="faq-q">¿Para qué edades son los juegos?</span>
        <div class="faq-a">Tenemos actividades desde estimulación temprana (2-3 años) hasta adolescencia (higiene, uso de dinero, habilidades sociales).</div>
    </div>
    <div class="faq-item">
        <span class="faq-q">¿Sirve para otros diagnósticos además de TEA?</span>
        <div class="faq-a">¡Sí! Los recursos visuales y la estructura clara son excelentes para TDAH, Síndrome de Down y dificultades de aprendizaje.</div>
    </div>
    <div class="faq-item">
        <span class="faq-q">¿Puedo descargar los materiales?</span>
        <div class="faq-a">Sí. En la sección "Recursos" encontrarás fichas en PDF y Word listas para imprimir y usar en casa o el consultorio.</div>
    </div>
    <div class="faq-item">
        <span class="faq-q">¿Puedo sugerir un juego nuevo?</span>
        <div class="faq-a">¡Por supuesto! Escribinos desde el formulario de abajo. Creamos contenido basado en lo que las familias necesitan.</div>
    </div>
</section>

<section id="contacto" class="contact-section">
    <h2 style="font-size: 3rem; margin-bottom: 10px; color: var(--color-primario);">Hablemos</h2>
    <p style="font-size: 1.3rem; color: #666;">¿Sugerencias? ¡Escribime!</p>
    <div class="contact-card">
        <form action="#" method="POST" onsubmit="event.preventDefault(); alert('¡Gracias! Mensaje enviado.');">
            <input type="text" placeholder="Tu Nombre" class="form-input" required>
            <input type="email" placeholder="Tu Correo Electrónico" class="form-input" required>
            <textarea rows="5" placeholder="Tu Mensaje..." class="form-input" required></textarea>
            <button type="submit" class="btn-send">ENVIAR MENSAJE</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>