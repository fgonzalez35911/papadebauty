<?php
$base_path = "./";
$script_name = $_SERVER['SCRIPT_NAME'];
if (strpos($script_name, '/admin/') !== false) { $base_path = "../"; }
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Papá de Bauti</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/estilos.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --p-azul: #92A8D1;
            --p-rosa: #F7CAC9;
            --p-verde: #88B04B;
            --p-naranja: #FFB347;
            --p-teal: #4ECDC4;
            --color-fondo: #fdfbf7;
        }
        
        body { 
            font-family: 'Nunito', sans-serif; 
            background-color: var(--color-fondo); 
            color: #666;
            padding-top: 80px; 
            margin: 0; 
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .btn-grande, .nav-link { font-weight: 800 !important; }

        /* HEADER */
        .main-header {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            height: 80px; width: 100%; position: fixed; top: 0; left: 0; z-index: 9000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            display: grid; grid-template-columns: 200px 1fr 200px; 
            align-items: center; padding: 0 30px; box-sizing: border-box;
        }
        
        .logo a { font-size: 1.5rem; font-weight: 900; color: var(--p-azul); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        
        /* MENÚ PC */
        .nav-container { display: flex; justify-content: center; }
        .nav-menu { display: flex; gap: 25px; list-style: none; margin: 0; padding: 0; }
        .nav-link { text-decoration: none; color: #8898aa; font-size: 1rem; transition: 0.3s; position: relative; }
        .nav-link:hover { color: var(--p-azul); transform: translateY(-2px); }
        
        .user-area { display: flex; justify-content: flex-end; }
        .btn-acceso {
            background: var(--p-azul); color: white !important; padding: 10px 25px; border-radius: 50px;
            font-size: 0.95rem; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 15px rgba(146, 168, 209, 0.4); transition: 0.3s;
        }
        .btn-acceso:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(146, 168, 209, 0.5); }

        /* BOTÓN FLOTANTE */
        .menu-toggle-fixed { 
            display: none; 
            position: fixed; top: 15px; right: 20px; 
            font-size: 1.8rem; color: var(--p-azul); 
            cursor: pointer; z-index: 11000;
            background: rgba(255,255,255,0.9);
            width: 50px; height: 50px; border-radius: 50%;
            align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(146, 168, 209, 0.2);
            transition: transform 0.3s, color 0.3s, background 0.3s;
        }
        .menu-toggle-fixed:active { transform: scale(0.9); }

        /* MENÚ MÓVIL */
        .mobile-menu-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            z-index: 10000;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            clip-path: circle(0% at 90% 5%); 
            transition: clip-path 0.5s cubic-bezier(0.75, 0, 0.175, 1);
        }

        .mobile-menu-overlay.active { clip-path: circle(150% at 90% 5%); }

        .mobile-links { list-style: none; padding: 0; text-align: center; width: 100%; }
        .mobile-links li { margin: 15px 0; opacity: 0; transform: translateY(20px); transition: 0.4s; }
        .mobile-link { font-size: 1.8rem; text-decoration: none; font-weight: 900; display: block; width: 100%; transition: 0.2s; }
        
        .mobile-links li:nth-child(1) .mobile-link { color: var(--p-azul); }
        .mobile-links li:nth-child(2) .mobile-link { color: var(--p-rosa); }
        .mobile-links li:nth-child(3) .mobile-link { color: var(--p-verde); }
        .mobile-links li:nth-child(4) .mobile-link { color: var(--p-naranja); }
        .mobile-links li:nth-child(5) .mobile-link { color: var(--p-teal); }
        .mobile-links li:nth-child(6) .mobile-link { color: #9575CD; }

        .mobile-menu-overlay.active li:nth-child(1) { transition-delay: 0.1s; opacity: 1; transform: translateY(0); }
        .mobile-menu-overlay.active li:nth-child(2) { transition-delay: 0.15s; opacity: 1; transform: translateY(0); }
        .mobile-menu-overlay.active li:nth-child(3) { transition-delay: 0.2s; opacity: 1; transform: translateY(0); }
        .mobile-menu-overlay.active li:nth-child(4) { transition-delay: 0.25s; opacity: 1; transform: translateY(0); }
        .mobile-menu-overlay.active li:nth-child(5) { transition-delay: 0.3s; opacity: 1; transform: translateY(0); }
        .mobile-menu-overlay.active li:nth-child(6) { transition-delay: 0.35s; opacity: 1; transform: translateY(0); }
        .mobile-menu-overlay.active .btn-mobile-container { transition-delay: 0.4s; opacity: 1; transform: translateY(0); }

        .btn-mobile-container { margin-top: 30px; opacity: 0; transform: translateY(20px); transition: 0.4s; }
        .btn-mobile { 
            background: var(--p-azul); color: white !important; padding: 15px 40px; 
            border-radius: 50px; font-size: 1.2rem; font-weight: 800; text-decoration: none;
            box-shadow: 0 10px 25px rgba(146, 168, 209, 0.4); display: inline-block;
        }

        .menu-blob { position: absolute; border-radius: 50%; filter: blur(40px); z-index: -1; animation: floatMenu 8s infinite; }
        .mb1 { top: 10%; left: 10%; width: 150px; height: 150px; background: var(--p-rosa); opacity: 0.3; }
        .mb2 { bottom: 20%; right: 10%; width: 200px; height: 200px; background: var(--p-azul); opacity: 0.2; animation-delay: 1s; }
        
        @keyframes floatMenu { 0%,100%{transform:translate(0,0);} 50%{transform:translate(10px, -20px);} }

        /* === NUEVAS ANIMACIONES DE ENTRADA (BURBUJA Y SCROLL) === */
        
        /* Estado inicial oculto */
        .animar-scroll, .animar-pop { opacity: 0; }

        /* Efecto 1: Aparecer subiendo (Para textos y secciones) */
        .fade-up-active {
            animation: fadeUp 0.8s cubic-bezier(0.5, 0, 0, 1) forwards;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Efecto 2: Pop Burbuja (Para tarjetas y fotos) */
        .pop-active {
            animation: popBurbuja 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        @keyframes popBurbuja {
            0% { opacity: 0; transform: scale(0.8) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        @media (max-width: 1100px) {
            .main-header { display: flex; justify-content: space-between; padding: 0 20px; }
            .nav-container, .user-area { display: none; } 
            .menu-toggle-fixed { display: flex; } 
        }
    </style>
</head>
<body>

<div class="menu-toggle-fixed" id="toggleBtn" onclick="toggleMenu()">
    <i class="fa-solid fa-bars"></i>
</div>

<header class="main-header">
    <div class="logo">
        <a href="<?php echo $base_path; ?>index.php"><i class="fa-solid fa-puzzle-piece"></i> Papá de Bauti</a>
    </div>

    <div class="nav-container">
        <ul class="nav-menu">
            <li><a href="<?php echo $base_path; ?>index.php" class="nav-link">Inicio</a></li>
            <li><a href="<?php echo $base_path; ?>padres.php" class="nav-link">Padres</a></li>
            <li><a href="<?php echo $base_path; ?>juegos.php" class="nav-link">Juegos</a></li>
            <li><a href="<?php echo $base_path; ?>recursos.php" class="nav-link">Recursos</a></li>
            <li><a href="<?php echo $base_path; ?>tutoriales.php" class="nav-link">Tutoriales</a></li>            
            <li><a href="<?php echo $base_path; ?>galeria.php" class="nav-link">Galería</a></li>
            <?php if(isset($_SESSION['usuario_id'])): ?>
                <li><a href="<?php echo $base_path; ?>admin/gestionar_contenido.php" class="nav-link" style="color: var(--p-verde);"><i class="fa-solid fa-pen-to-square"></i> Gestionar</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="user-area">
        <?php if(isset($_SESSION['usuario_id'])): ?>
            <a href="<?php echo $base_path; ?>admin/panel.php" class="btn-acceso">Mi Panel</a>
        <?php else: ?>
            <a href="<?php echo $base_path; ?>admin/index.php" class="btn-acceso">Acceso</a>
        <?php endif; ?>
    </div>
</header>

<div class="mobile-menu-overlay" id="mobileMenu">
    <div class="menu-blob mb1"></div>
    <div class="menu-blob mb2"></div>
    <ul class="mobile-links">
        <li><a href="<?php echo $base_path; ?>index.php" class="mobile-link" onclick="toggleMenu()">Inicio</a></li>
        <li><a href="<?php echo $base_path; ?>padres.php" class="mobile-link" onclick="toggleMenu()">Padres</a></li>
        <li><a href="<?php echo $base_path; ?>juegos.php" class="mobile-link" onclick="toggleMenu()">Juegos</a></li>
        <li><a href="<?php echo $base_path; ?>recursos.php" class="mobile-link" onclick="toggleMenu()">Recursos</a></li>
        <li><a href="<?php echo $base_path; ?>tutoriales.php" class="mobile-link" onclick="toggleMenu()">Tutoriales</a></li>
        <li><a href="<?php echo $base_path; ?>galeria.php" class="mobile-link" onclick="toggleMenu()">Galería</a></li>
    </ul>
    <div class="btn-mobile-container">
        <?php if(isset($_SESSION['usuario_id'])): ?>
            <a href="<?php echo $base_path; ?>admin/panel.php" class="btn-mobile" onclick="toggleMenu()">Mi Panel</a>
        <?php else: ?>
            <a href="<?php echo $base_path; ?>admin/index.php" class="btn-mobile" onclick="toggleMenu()">Acceso Admin</a>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleMenu() {
        const menu = document.getElementById('mobileMenu');
        const btnContainer = document.getElementById('toggleBtn');
        const icon = btnContainer.querySelector('i');
        menu.classList.toggle('active');
        if(menu.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
            btnContainer.style.color = "#FF6B6B"; 
            btnContainer.style.borderColor = "#FF6B6B";
            document.body.style.overflow = 'hidden';
        } else {
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
            btnContainer.style.color = "#92A8D1";
            btnContainer.style.borderColor = "#92A8D1";
            document.body.style.overflow = 'auto';
        }
    }
</script>

<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: -1;">
    <i class="fa-solid fa-puzzle-piece" style="position: absolute; top: 5%; left: 2%; font-size: 7rem; color: #92A8D1; opacity: 0.06; animation: float 12s infinite;"></i>
    <i class="fa-solid fa-rocket" style="position: absolute; bottom: 10%; right: 3%; font-size: 9rem; color: #92A8D1; opacity: 0.04; animation: float 15s infinite 1s;"></i>
    <i class="fa-solid fa-cloud" style="position: absolute; top: 15%; right: 15%; font-size: 10rem; color: #E0F7FA; opacity: 0.3; animation: float 20s infinite;"></i>
    <i class="fa-solid fa-shapes" style="position: absolute; top: 40%; left: 10%; font-size: 5rem; color: #F7CAC9; opacity: 0.08; animation: float 10s infinite 2s;"></i>
    <i class="fa-solid fa-music" style="position: absolute; bottom: 20%; left: 30%; font-size: 6rem; color: #4ECDC4; opacity: 0.06; animation: float 14s infinite 3s;"></i>
    <i class="fa-solid fa-star" style="position: absolute; top: 25%; left: 20%; font-size: 3rem; color: #FFB347; opacity: 0.15; animation: float 7s infinite;"></i>
    <i class="fa-solid fa-lightbulb" style="position: absolute; bottom: 30%; left: 5%; font-size: 3.5rem; color: #FFD54F; opacity: 0.1; animation: float 8s infinite 1s;"></i>
    <i class="fa-solid fa-heart" style="position: absolute; top: 60%; right: 10%; font-size: 2.5rem; color: #FF6B6B; opacity: 0.12; animation: float 6s infinite 2s;"></i>
    <i class="fa-solid fa-cube" style="position: absolute; top: 10%; right: 35%; font-size: 3rem; color: #FFE0B2; opacity: 0.15; animation: float 9s infinite;"></i>
    <i class="fa-solid fa-ghost" style="position: absolute; bottom: 5%; right: 40%; font-size: 3rem; color: #B0BEC5; opacity: 0.1; animation: float 11s infinite;"></i>
    <i class="fa-solid fa-gamepad" style="position: absolute; top: 50%; left: 50%; font-size: 2rem; color: #CE93D8; opacity: 0.15; animation: float 5s infinite;"></i>
    <i class="fa-solid fa-hand-peace" style="position: absolute; top: 80%; left: 15%; font-size: 2rem; color: #AED581; opacity: 0.15; animation: float 6s infinite 1.5s;"></i>
    <i class="fa-solid fa-dice" style="position: absolute; top: 35%; right: 5%; font-size: 2rem; color: #FFAB91; opacity: 0.15; animation: float 7s infinite 0.5s;"></i>
    <i class="fa-solid fa-check" style="position: absolute; bottom: 50%; left: 8%; font-size: 2rem; color: #88B04B; opacity: 0.1; animation: float 6s infinite 2.5s;"></i>
    <i class="fa-solid fa-bolt" style="position: absolute; top: 5%; left: 40%; font-size: 2rem; color: #FFD700; opacity: 0.15; animation: float 5s infinite 1s;"></i>
    <i class="fa-solid fa-palette" style="position: absolute; bottom: 15%; right: 25%; font-size: 2.5rem; color: #92A8D1; opacity: 0.15; animation: float 8s infinite;"></i>
    <i class="fa-solid fa-music" style="position: absolute; top: 70%; left: 5%; font-size: 1.5rem; color: #4ECDC4; opacity: 0.2; animation: float 4s infinite;"></i>
    <i class="fa-solid fa-star" style="position: absolute; bottom: 5%; right: 5%; font-size: 1.5rem; color: #FFB347; opacity: 0.2; animation: float 5s infinite 3s;"></i>

    <style>
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }
    </style>
</div>

</body>
</html>