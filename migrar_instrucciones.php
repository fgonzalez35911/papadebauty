<?php
// admin/migrar_instrucciones.php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

echo "<h1>⚙️ Migración Masiva de Instrucciones (85 Juegos)</h1>";

// 1. CREAR COLUMNA SI NO EXISTE
$check = $conn->query("SHOW COLUMNS FROM juegos LIKE 'instrucciones_admin'");
if ($check->num_rows == 0) {
    $conn->query("ALTER TABLE juegos ADD COLUMN instrucciones_admin TEXT AFTER descripcion");
    echo "<p>✅ Columna 'instrucciones_admin' creada.</p>";
}

// 2. DICCIONARIO COMPLETO (ID 1 al 85)
$datos = [
    // --- LENGUAJE Y VOCABULARIO ---
    1 => "Sube foto del animal (ej: Vaca). Correcta: 'VACA'. Distractores: 'PERRO', 'GATO'.",
    2 => "Sube foto del objeto de la casa (ej: Mesa). Correcta: 'MESA'. Distractores: 'SILLA', 'CAMA'.",
    3 => "Sube foto de la parte del cuerpo. Correcta: 'MANO'. Distractores: 'PIE', 'NARIZ'.",
    4 => "Sube foto de la profesión (ej: Bombero). Correcta: 'BOMBERO'.",
    5 => "Sube foto de una fruta o verdura. Correcta: 'FRUTA' o 'VERDURA'.",
    6 => "Sube foto comparando tamaños. Correcta: 'GRANDE' o 'PEQUEÑO'.",
    7 => "Sube foto (ej: Avión). Correcta: Letra inicial 'A'. Incorrectas: 'E', 'O'.",
    8 => "Sube imagen (ej: Gato). Correcta: Palabra que rime (ej: 'PATO').",
    9 => "Sube foto de una acción. Correcta: Verbo (ej: 'CORRER').",
    10 => "Sube foto (ej: Pan). Correcta: Palabra derivada (ej: 'PANADERO').",
    11 => "Sube imagen o texto. Correcta: Sinónimo (ej: Auto -> Coche).",
    12 => "Sube imagen o texto. Correcta: Antónimo (ej: Día -> Noche).",
    13 => "Sube imagen literal (ej: Gatos lloviendo). Correcta: Significado real ('LLUEVE MUCHO').",
    14 => "Sube imagen con doble sentido o chiste. Correcta: Explicación del chiste.",
    15 => "Sube imagen absurda (ej: Pez volando). Correcta: Qué está mal en la foto.",

    // --- MATEMÁTICA ---
    16 => "Sube foto con 1 a 5 objetos. Correcta: El número (ej: '3').",
    17 => "Sube foto con 1 a 10 objetos. Correcta: El número (ej: '7').",
    18 => "SECUENCIA: Sube los números en orden creciente. Paso 1: '1', Paso 2: '2', etc.",
    19 => "Sube imagen de suma visual (ej: 2 manzanas + 1 manzana). Correcta: Resultado '3'.",
    20 => "Sube imagen de resta visual (ej: 5 dedos y bajan 2). Correcta: Resultado '3'.",
    21 => "Sube imagen de la cuenta (ej: 2x3). Correcta: Resultado '6'.",
    22 => "Sube imagen de la cuenta (ej: 4x5). Correcta: Resultado '20'.",
    23 => "Sube imagen de reparto (ej: 6 caramelos en 2 bolsas). Correcta: Cuántos en cada una ('3').",
    24 => "SECUENCIA: Pasos para dividir. 1. Tomar cifra, 2. Buscar en tabla, 3. Restar.",
    25 => "Sube imagen de fracción (ej: media pizza). Correcta: '1/2'.",
    26 => "Sube foto del billete. Correcta: Su valor (ej: '$100').",
    27 => "Sube situación de compra: 'Gasto 50, Pago con 100'. Correcta: El vuelto ('50').",
    28 => "Sube imagen de figura geométrica. Correcta: Nombre (ej: 'TRIÁNGULO').",
    29 => "Sube cuerpo geométrico 3D. Correcta: Nombre (ej: 'CUBO').",
    30 => "Sube foto de reloj analógico. Correcta: La hora que marca (ej: '3:00').",

    // --- HISTORIA Y GEOGRAFÍA ---
    31 => "Sube foto del símbolo patrio. Correcta: Nombre (ej: 'ESCARAPELA').",
    32 => "Sube foto o pregunta sobre Belgrano. Correcta: Respuesta histórica.",
    33 => "Sube foto o pregunta sobre San Martín. Correcta: Respuesta histórica.",
    34 => "SECUENCIA 1810: 1. Cabildo Abierto, 2. Gente en la plaza, 3. Primera Junta.",
    35 => "SECUENCIA 1816: 1. Viaje en carreta a Tucumán, 2. Casita de Tucumán, 3. Firma del Acta.",
    36 => "SECUENCIA CRUCE: 1. Preparación del ejército, 2. Cruce de montañas, 3. Abrazo de Maipú.",
    37 => "Sube mapa pintado (Norte). Correcta: Nombre de la provincia (ej: 'JUJUY').",
    38 => "Sube mapa pintado (Sur). Correcta: Nombre de la provincia (ej: 'CHUBUT').",
    39 => "Sube nombre de provincia. Correcta: Su capital.",
    40 => "Sube foto de paisaje argentino. Correcta: Nombre del lugar (ej: 'CATARATAS').",

    // --- VIDA DIARIA (SECUENCIAS) ---
    41 => "ORDEN DIENTES: 1. Poner pasta, 2. Cepillar dientes, 3. Enjuagar boca, 4. Secar.",
    42 => "ORDEN MANOS: 1. Abrir canilla, 2. Poner jabón, 3. Frotar manos, 4. Enjuagar, 5. Secar.",
    43 => "ORDEN BAÑO: 1. Bajar ropa, 2. Sentarse en inodoro, 3. Limpiarse, 4. Subir ropa, 5. Tirar cadena.",
    44 => "ORDEN DUCHA: 1. Desvestirse, 2. Entrar al agua, 3. Jabón/Champú, 4. Enjuagar, 5. Secar cuerpo.",
    45 => "ORDEN VESTIRSE: 1. Ropa interior, 2. Remera/Camisa, 3. Pantalón, 4. Medias y Zapatillas.",
    46 => "ORDEN CORDONES: 1. Cruzar cordones, 2. Hacer orejita, 3. Dar vuelta, 4. Pasar y tirar.",
    47 => "ORDEN MESA: 1. Poner individual, 2. Plato en el centro, 3. Cubiertos a los lados, 4. Vaso.",
    48 => "ORDEN CAMA: 1. Sábana de abajo, 2. Sábana de arriba, 3. Frazada/Colcha, 4. Poner almohada.",
    49 => "ORDEN PLATOS: 1. Poner detergente, 2. Pasar esponja, 3. Enjuagar con agua, 4. Poner en secaplatos.",
    50 => "ORDEN SANDWICH: 1. Poner pan, 2. Untar aderezo, 3. Poner fiambre/queso, 4. Cerrar con otro pan.",
    51 => "ORDEN CHOCOLATADA: 1. Poner leche en vaso, 2. Agregar cacao, 3. Agregar azúcar (opcional), 4. Revolver.",
    52 => "ORDEN AFEITARSE: 1. Poner espuma, 2. Pasar máquina suave, 3. Enjuagar cara, 4. Secar con toalla.",
    53 => "ORDEN MENSTRUACIÓN: 1. Buscar toallita, 2. Sacar papel adhesivo, 3. Pegar en bombacha, 4. Tirar envoltorio.",
    54 => "ORDEN COLECTIVO: 1. Esperar en parada, 2. Subir y saludar, 3. Apoyar tarjeta SUBE, 4. Sentarse o agarrarse.",
    55 => "ORDEN CALLE: 1. Parar en el cordón, 2. Mirar a ambos lados, 3. Esperar si vienen autos, 4. Cruzar caminando.",

    // --- EMOCIONES Y SOCIAL ---
    56 => "Sube foto de expresión facial. Correcta: Emoción (ej: 'FELIZ', 'TRISTE', 'ENOJADO').",
    57 => "Sube foto de situación (ej: Se cae el helado). Correcta: Emoción que genera ('TRISTE').",
    58 => "Sube situación. Correcta: Nivel del termómetro ('VERDE', 'AMARILLO', 'ROJO').",
    59 => "Sube foto de estrés. Correcta: Estrategia de calma (ej: 'RESPIRAR', 'CONTAR HASTA 10').",
    60 => "Sube situación social. Correcta: Saludo adecuado (ej: 'HOLA', 'BUEN DÍA').",
    61 => "Sube situación de juego. Correcta: Acción social ('COMPARTIR', 'ESPERAR TURNO').",
    62 => "SECUENCIA CHARLA: 1. Mirar a los ojos, 2. Decir Hola, 3. Hacer una pregunta.",
    63 => "Sube pregunta de otra persona. Correcta: Respuesta adecuada para mantener la charla.",
    64 => "Sube foto de alguien llorando. Correcta: Acción empática ('AYUDAR', 'CONSOLAR').",
    65 => "Sube foto de lugar o acción. Correcta: Clasificación ('PÚBLICO' o 'PRIVADO').",
    66 => "Sube foto de mensaje feo en pantalla. Correcta: Acción segura ('BLOQUEAR', 'AVISAR A MAMÁ').",
    67 => "Sube foto de perder un juego. Correcta: Reacción positiva ('FELICITAR AL GANADOR', 'RESPIRAR').",

    // --- SONIDOS ---
    68 => "Sube AUDIO de animal. Correcta: Nombre (ej: 'VACA'). Imagen: Foto del animal.",
    69 => "Sube AUDIO de casa (ej: timbre, licuadora). Correcta: Nombre. Imagen: Foto del objeto.",
    70 => "Sube AUDIO de instrumento. Correcta: Nombre (ej: 'GUITARRA'). Imagen: Foto instrumento.",
    71 => "Sube AUDIO de ritmo musical. Correcta: Velocidad ('RÁPIDO' o 'LENTO').",

    // --- ARTE (PINTURA) ---
    72 => "Sube un dibujo lineal en BLANCO Y NEGRO para colorear. (No requiere respuesta correcta).",
    73 => "Sube un Mandala en BLANCO Y NEGRO. (No requiere respuesta correcta).",
    74 => "Sube una grilla pixelada o patrón simple como guía. (No requiere respuesta correcta).",

    // --- MEMORIA (MEMOTEST) ---
    75 => "Sube SOLO la imagen del animal. El sistema duplica la ficha automáticamente para armar el par.",
    76 => "Sube SOLO la imagen de la emoción. El sistema arma el par.",
    77 => "Sube SOLO la imagen del color. El sistema arma el par.",
    78 => "Sube SOLO la imagen del número. El sistema arma el par.",
    79 => "Sube SOLO la imagen histórica (ej: Bandera). El sistema arma el par.",
    80 => "Sube SOLO la imagen del instrumento. El sistema arma el par.",

    // --- ASOCIACIÓN Y TEXTO ---
    83 => "ASOCIACIÓN: Sube Imagen 1 (ej: Sol) y en Imagen Pareja sube su opuesto (ej: Luna).",
    84 => "ASOCIACIÓN: Sube Imagen 1 (ej: Auto) y en Imagen Pareja sube su sinónimo (ej: Coche).",
    85 => "TEXTO: En 'Palabra Pregunta' poné la palabra fija (ej: DÍA). En 'Respuesta' poné la que se arrastra (ej: NOCHE). No subas imagen."
];

echo "<div style='font-family:sans-serif; max-width:800px; margin:0 auto;'>";
echo "<ul style='background:#f9f9f9; padding:20px; border:1px solid #ccc;'>";

$count = 0;
foreach($datos as $id => $instruccion) {
    // Escapar texto para SQL
    $txt = $conn->real_escape_string($instruccion);
    
    // Actualizar solo si existe el juego
    $check = $conn->query("SELECT id FROM juegos WHERE id = $id");
    if($check->num_rows > 0) {
        $conn->query("UPDATE juegos SET instrucciones_admin = '$txt' WHERE id = $id");
        echo "<li style='color:green; margin-bottom:5px;'>✅ Juego ID <strong>$id</strong> actualizado.</li>";
        $count++;
    } else {
        echo "<li style='color:red; margin-bottom:5px;'>⚠️ Juego ID <strong>$id</strong> no encontrado en la BD (Saltado).</li>";
    }
}

echo "</ul>";
echo "<h3>Resultado Final:</h3>";
echo "<p>Se actualizaron instrucciones para <strong>$count</strong> juegos.</p>";
echo "<a href='listar_juegos.php' style='display:inline-block; padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>Ir al Listado de Juegos</a>";
echo "</div>";
?>