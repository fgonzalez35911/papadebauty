<?php
session_start();
// Si ya está logueado, lo mandamos directo al panel
if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Admin - Papá de Bauti</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        body {
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; background-color: #eee;
            padding: 20px; /* Espacio para que no toque bordes en movil */
        }
        .login-box {
            background: white; padding: 40px; border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 400px;
            text-align: center;
        }
        input {
            width: 100%; padding: 15px; margin: 10px 0;
            border: 2px solid #ddd; border-radius: 10px; font-size: 1rem;
        }
        button {
            width: 100%; cursor: pointer; border: none; margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2 style="color: var(--color-secundario); margin-bottom: 20px;">
        <i class="fa-solid fa-lock"></i> Zona Privada
    </h2>
    
    <?php if(isset($_GET['error'])): ?>
        <p style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px;">
            Datos incorrectos. Intenta de nuevo.
        </p>
    <?php endif; ?>

    <form action="auth.php" method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" class="btn-grande btn-jugar">Ingresar</button>
    </form>
    
    <p style="margin-top: 20px;">
        <a href="../index.php" style="color: #999;">Volver al sitio</a>
    </p>
</div>

</body>
</html>
