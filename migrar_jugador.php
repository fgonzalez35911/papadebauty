<?php
// admin/migrar_jugador.php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

echo "<h1>🧸 Migración de Instrucciones para el Jugador</h1>";

// 1. CREAR COLUMNA SI NO EXISTE
$check = $conn->query("SHOW COLUMNS FROM juegos LIKE 'instruccion_jugador'");
if ($check->num_rows == 0) {
    $conn->query("ALTER TABLE juegos ADD COLUMN instruccion_jugador VARCHAR(255) AFTER titulo");
    echo "<p>✅ Columna 'instruccion_jugador' creada.</p>";
}

// 2. DICCIONARIO DE FRASES AMIGABLES (ID 1 al 85)
$frases = [
    // LENGUAJE
    1 => "¿Qué animal es? ¡Tócalo!",
    2 => "¿Qué objeto es? Señálalo.",
    3 => "¿Qué parte del cuerpo es?",
    4 => "¿Quién usa esto? Toca la profesión.",
    5 => "¿Es Fruta o Verdura?",
    6 => "¿Cuál es el tamaño correcto?",
    7 => "¿Con qué letra empieza?",
    8 => "¿Qué palabra rima con la imagen?",
    9 => "¿Qué acción está haciendo?",
    10 => "¿Quién hace este producto?",
    11 => "Busca la palabra que significa lo mismo (Sinónimo)",
    12 => "Busca la palabra contraria (Opuesto)",
    13 => "¿Qué significa realmente esta frase?",
    14 => "¿Cuál es el chiste o doble sentido?",
    15 => "¿Qué está mal en esta imagen?",

    // MATEMÁTICA
    16 => "Cuenta: ¿Cuántos hay?",
    17 => "Cuenta los objetos: ¿Cuántos son?",
    18 => "Ordena los números de menor a mayor",
    19 => "Suma los objetos: ¿Cuánto es?",
    20 => "Resta visual: ¿Cuántos quedan?",
    21 => "Resuelve la multiplicación",
    22 => "Resuelve la cuenta",
    23 => "Reparte en partes iguales",
    24 => "Ordena los pasos para dividir",
    25 => "¿Qué fracción representa el dibujo?",
    26 => "¿Cuánto vale este billete?",
    27 => "Calcula el vuelto correcto",
    28 => "¿Qué forma geométrica es?",
    29 => "¿Cómo se llama este cuerpo?",
    30 => "¿Qué hora marca el reloj?",

    // HISTORIA
    31 => "¿Cómo se llama este símbolo patrio?",
    32 => "Pregunta sobre Manuel Belgrano",
    33 => "Pregunta sobre San Martín",
    34 => "Ordena la historia: 25 de Mayo de 1810",
    35 => "Ordena la historia: Independencia 1816",
    36 => "Ordena la historia: Cruce de los Andes",
    37 => "¿Qué provincia del Norte es?",
    38 => "¿Qué provincia del Sur es?",
    39 => "¿Cuál es la capital de esta provincia?",
    40 => "¿Qué paisaje de Argentina es?",

    // VIDA DIARIA (SECUENCIAS)
    41 => "Ordena los pasos para lavarte los dientes",
    42 => "Ordena los pasos para lavarte las manos",
    43 => "Ordena la secuencia de ir al baño",
    44 => "Ordena los pasos para bañarse",
    45 => "Ordena la ropa para vestirse",
    46 => "Ordena los pasos para atar los cordones",
    47 => "Ordena los pasos para poner la mesa",
    48 => "Ordena los pasos para hacer la cama",
    49 => "Ordena los pasos para lavar los platos",
    50 => "Ordena los pasos para hacer un sándwich",
    51 => "Ordena los pasos para hacer la chocolatada",
    52 => "Ordena los pasos para afeitarse",
    53 => "Ordena los pasos de higiene femenina",
    54 => "Ordena los pasos para viajar en colectivo",
    55 => "Ordena los pasos para cruzar la calle",

    // EMOCIONES Y SOCIAL
    56 => "¿Cómo se siente? Toca la emoción correcta",
    57 => "¿Cómo se siente el niño en esta situación?",
    58 => "¿Cuánto enojo siente? Marca el termómetro",
    59 => "¿Qué puedes hacer para calmarte?",
    60 => "¿Cuál es el saludo correcto?",
    61 => "¿Qué harías para jugar con amigos?",
    62 => "Ordena los pasos para iniciar una charla",
    63 => "¿Qué responderías en esta situación?",
    64 => "Si ves a alguien llorando, ¿qué haces?",
    65 => "¿Esto es Público o Privado?",
    66 => "Si recibes un mensaje feo, ¿qué haces?",
    67 => "Si pierdes el juego, ¿qué haces?",

    // SONIDOS Y ARTE
    68 => "Escucha el sonido: ¿Qué animal es?",
    69 => "Escucha el sonido: ¿Qué objeto es?",
    70 => "Escucha el sonido: ¿Qué instrumento es?",
    71 => "Escucha el ritmo: ¿Es Rápido o Lento?",
    72 => "¡A pintar! Elige colores y diviértete",
    73 => "Relájate pintando el Mandala",
    74 => "Copia el dibujo pixel por pixel",

    // MEMORIA
    75 => "Encuentra los pares de Animales",
    76 => "Encuentra los pares de Emociones",
    77 => "Encuentra los pares de Colores",
    78 => "Encuentra los pares de Números",
    79 => "Encuentra los pares de Historia",
    80 => "Encuentra los pares de Instrumentos",

    // ASOCIACIÓN Y TEXTO
    83 => "Arrastra cada imagen con su opuesto",
    84 => "Junta las palabras que significan lo mismo",
    85 => "Lee la palabra y arrastra su opuesto aquí"
];

$count = 0;
foreach($frases as $id => $texto) {
    $txt = $conn->real_escape_string($texto);
    // Solo actualizamos si el juego existe
    $check = $conn->query("SELECT id FROM juegos WHERE id = $id");
    if($check->num_rows > 0) {
        $conn->query("UPDATE juegos SET instruccion_jugador = '$txt' WHERE id = $id");
        $count++;
    }
}

echo "<p>✅ Se cargaron instrucciones para <strong>$count</strong> juegos.</p>";
echo "<hr><a href='panel.php' class='btn-grande'>Volver al Panel</a>";
?>