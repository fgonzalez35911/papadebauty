<?php
// PROYECTO: Papá de Bauti
// ARCHIVO: includes/db_connect.php

// 1. DATOS DE CONEXIÓN (AQUÍ DEBES PONER TUS DATOS REALES)
$servername = "localhost"; // En Hostinger suele ser "localhost"
$username = "u415354546_papadebauty"; // <--- CAMBIAR ESTO (Usuario de la BD)
$password = "Fmg35911@"; // <--- CAMBIAR ESTO (Contraseña que creaste para la BD)
$dbname = "u415354546_papadebauty"; // <--- CAMBIAR ESTO (Nombre de la BD)

// 2. CREAR CONEXIÓN
$conn = new mysqli($servername, $username, $password, $dbname);

// 3. VERIFICAR SI HUBO ERROR
if ($conn->connect_error) {
    // Si falla, mostramos un mensaje y detenemos todo para no mostrar errores feos
    die("Error de conexión: Lo sentimos, hubo un problema técnico. (Error: " . $conn->connect_error . ")");
}

// 4. CONFIGURAR CARACTERES (Importante para ñ y tildes)
$conn->set_charset("utf8mb4");

// Si llegamos acá, la conexión es exitosa.
// No mostramos mensaje de "éxito" para no ensuciar el código de la web después.
?>