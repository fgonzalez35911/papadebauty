<?php
session_start();
require '../includes/db_connect.php'; // Conexión subiendo un nivel

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Buscamos al usuario en la base de datos
    $sql = "SELECT * FROM usuarios_admin WHERE usuario = ?";
    
    // Usamos sentencias preparadas por seguridad (evita hackeos)
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Verificamos la contraseña encriptada
        if (password_verify($password, $row['password'])) {
            // ¡ÉXITO! Guardamos la sesión
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];
            
            header("Location: panel.php");
            exit;
        }
    }

    // Si falla algo, volver al login con error
    header("Location: index.php?error=1");
    exit;
}
?>