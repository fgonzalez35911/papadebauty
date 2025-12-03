<?php
// includes/juegos_data.php

$biblioteca_juegos = [
    // --- LENGUAJE Y VOCABULARIO (Motor Lenguaje) ---
    1 => ['titulo' => 'Animales de Granja', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['vaca', 'cerdo', 'caballo', 'gallina', 'pato']],
    2 => ['titulo' => 'Frutas Ricas', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['manzana', 'banana', 'naranja', 'uva', 'pera']],
    3 => ['titulo' => 'Medios de Transporte', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['auto', 'avion', 'barco', 'tren', 'bici']],
    4 => ['titulo' => 'Colores Básicos', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['rojo', 'azul', 'amarillo', 'verde']],
    5 => ['titulo' => 'Partes del Cuerpo', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['mano', 'pie', 'ojos', 'boca', 'nariz']],
    6 => ['titulo' => 'Ropa de Invierno', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['gorro', 'bufanda', 'guantes', 'campera']],
    7 => ['titulo' => 'Ropa de Verano', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['short', 'remera', 'ojotas', 'gorra']],
    8 => ['titulo' => 'Cosas de la Casa', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['mesa', 'silla', 'cama', 'puerta']],
    9 => ['titulo' => 'Instrumentos Musicales', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['guitarra', 'piano', 'tambor', 'flauta']],
    10 => ['titulo' => 'La Familia', 'tipo' => 'lenguaje', 'categoria' => 'vocabulario', 'data' => ['mama', 'papa', 'abuelo', 'abuela', 'hermano']],

    // --- MATEMÁTICA Y NÚMEROS (Motor Mate Visual) ---
    11 => ['titulo' => 'Contar Manzanas (1-5)', 'tipo' => 'mate_visual', 'config' => ['maximo' => 5, 'icono' => 'fa-apple-whole', 'color' => '#FF6B6B']],
    12 => ['titulo' => 'Contar Estrellas (1-10)', 'tipo' => 'mate_visual', 'config' => ['maximo' => 10, 'icono' => 'fa-star', 'color' => '#FFD700']],
    13 => ['titulo' => 'Sumas Simples (Autos)', 'tipo' => 'mate_visual', 'config' => ['maximo' => 5, 'operacion' => 'suma', 'icono' => 'fa-car', 'color' => '#4ECDC4']],
    14 => ['titulo' => 'Restas con Peces', 'tipo' => 'mate_visual', 'config' => ['maximo' => 5, 'operacion' => 'resta', 'icono' => 'fa-fish', 'color' => '#FFB347']],
    15 => ['titulo' => 'Contar Dedos', 'tipo' => 'mate_visual', 'config' => ['maximo' => 10, 'icono' => 'fa-hand', 'color' => '#F7CAC9']],
    16 => ['titulo' => 'Sumas de Gatitos', 'tipo' => 'mate_visual', 'config' => ['maximo' => 8, 'icono' => 'fa-cat', 'color' => '#92A8D1']],
    17 => ['titulo' => 'Matemática con Bloques', 'tipo' => 'matematica', 'config' => ['operacion' => 'contar']], // Usa motor_matematica.php
    18 => ['titulo' => 'La Casita de Sumar', 'tipo' => 'matematica', 'config' => ['operacion' => 'suma_vertical']],
    19 => ['titulo' => 'Restar Tachando', 'tipo' => 'matematica', 'config' => ['operacion' => 'resta_visual']],
    20 => ['titulo' => 'Multiplicación Visual', 'tipo' => 'matematica', 'config' => ['operacion' => 'multiplicacion']],

    // --- MEMORIA Y ATENCIÓN (Motor Memoria) ---
    21 => ['titulo' => 'Memotest: Granja', 'tipo' => 'memoria', 'config' => ['tema' => 'granja', 'cartas' => 6]],
    22 => ['titulo' => 'Memotest: Granja Difícil', 'tipo' => 'memoria', 'config' => ['tema' => 'granja', 'cartas' => 12]],
    23 => ['titulo' => 'Memotest: Emociones', 'tipo' => 'memoria', 'config' => ['tema' => 'emociones', 'cartas' => 8]],
    24 => ['titulo' => 'Memotest: Formas', 'tipo' => 'memoria', 'config' => ['tema' => 'default', 'cartas' => 8]],
    25 => ['titulo' => 'Simón Dice (Colores)', 'tipo' => 'secuencia', 'config' => ['velocidad' => 'lento']],
    26 => ['titulo' => 'Simón Dice (Rápido)', 'tipo' => 'secuencia', 'config' => ['velocidad' => 'rapido']],
    
    // --- NUEVO MOTOR: CLASIFICACIÓN (LÓGICA) ---
    // (Explicado en el Paso 3)
    27 => ['titulo' => 'Clasificar: Fruta vs Verdura', 'tipo' => 'clasificacion', 'data' => ['grupos' => ['Frutas', 'Verduras'], 'items' => [['manzana','Frutas'], ['lechuga','Verduras'], ['banana','Frutas'], ['zanahoria','Verduras']]]],
    28 => ['titulo' => 'Clasificar: Caliente vs Frío', 'tipo' => 'clasificacion', 'data' => ['grupos' => ['Caliente', 'Frío'], 'items' => [['fuego','Caliente'], ['hielo','Frío'], ['sol','Caliente'], ['nieve','Frío']]]],
    29 => ['titulo' => 'Clasificar: Colores (Rojo vs Azul)', 'tipo' => 'clasificacion', 'data' => ['grupos' => ['Rojo', 'Azul'], 'items' => [['corazon','Rojo'], ['cielo','Azul'], ['frutilla','Rojo'], ['ballena','Azul']]]],
    30 => ['titulo' => 'Clasificar: Animales (Agua vs Tierra)', 'tipo' => 'clasificacion', 'data' => ['grupos' => ['Agua', 'Tierra'], 'items' => [['pez','Agua'], ['perro','Tierra'], ['pulpo','Agua'], ['gato','Tierra']]]],
    
    // --- ARTE Y SENSORIAL ---
    31 => ['titulo' => 'Pintura Libre', 'tipo' => 'pintura', 'config' => []],
    32 => ['titulo' => 'Burbujas Relajantes', 'tipo' => 'sensorial', 'config' => []],
    33 => ['titulo' => 'Puzzle: El León', 'tipo' => 'puzzle', 'imagen_portada' => 'assets/img/leon.jpg'],
    34 => ['titulo' => 'Puzzle: El Auto', 'tipo' => 'puzzle', 'imagen_portada' => 'assets/img/auto.jpg'],
    35 => ['titulo' => 'Puzzle: La Casa', 'tipo' => 'puzzle', 'imagen_portada' => 'assets/img/casa.jpg'],
    
    // ... Puedes seguir agregando hasta 50 copiando la estructura
];
?>
