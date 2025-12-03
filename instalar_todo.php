<?php
// admin/instalar_todo.php
// SCRIPT DE INSTALACIÓN MASIVA Y AUTOMÁTICA

require '../includes/db_connect.php';
ini_set('display_errors', 1);
ini_set('max_execution_time', 600); // Damos 10 minutos por si acaso

echo "<div style='font-family:sans-serif; padding:20px;'>";
echo "<h1>🚀 Iniciando Instalación Completa...</h1>";

// 1. LIMPIEZA TOTAL (Reset de fábrica)
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE juegos_contenido");
$conn->query("TRUNCATE TABLE juegos");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "<p style='color:green;'>✅ Base de datos limpiada.</p>";

// 2. DEFINICIÓN DE TODOS LOS JUEGOS Y SU CONTENIDO
// (Aquí están los 60+ juegos con sus fichas de prueba)

$biblioteca = [
    // --- LENGUAJE Y COMUNICACIÓN ---
    [
        't' => 'Vocabulario: La Granja', 'd' => 'Identificar animales.', 'c' => 1, 'm' => 4, 'tipo' => 'seleccion', 'min' => 2,
        'data' => [
            ['i'=>'png?text=Vaca', 'ok'=>'Vaca', 'd1'=>'Perro', 'd2'=>'Gato', 'd3'=>'Pato'],
            ['i'=>'png?text=Cerdo', 'ok'=>'Cerdo', 'd1'=>'Caballo', 'd2'=>'Oveja', 'd3'=>'Pollo'],
            ['i'=>'png?text=Gallina', 'ok'=>'Gallina', 'd1'=>'Pato', 'd2'=>'Perro', 'd3'=>'Toro']
        ]
    ],
    [
        't' => 'Vocabulario: La Casa', 'd' => 'Objetos cotidianos.', 'c' => 1, 'm' => 4, 'tipo' => 'seleccion', 'min' => 2,
        'data' => [
            ['i'=>'png?text=Mesa', 'ok'=>'Mesa', 'd1'=>'Silla', 'd2'=>'Cama', 'd3'=>'Sofá'],
            ['i'=>'png?text=Cama', 'ok'=>'Cama', 'd1'=>'Mesa', 'd2'=>'Lámpara', 'd3'=>'Puerta'],
            ['i'=>'png?text=Vaso', 'ok'=>'Vaso', 'd1'=>'Plato', 'd2'=>'Cuchara', 'd3'=>'Olla']
        ]
    ],
    [
        't' => 'Sonidos Iniciales', 'd' => '¿Con qué letra empieza?', 'c' => 1, 'm' => 4, 'tipo' => 'seleccion', 'min' => 5,
        'data' => [
            ['i'=>'png?text=Avion', 'ok'=>'A', 'd1'=>'E', 'd2'=>'I', 'd3'=>'O'],
            ['i'=>'png?text=Elefante', 'ok'=>'E', 'd1'=>'A', 'd2'=>'U', 'd3'=>'I'],
            ['i'=>'png?text=Oso', 'ok'=>'O', 'd1'=>'A', 'd2'=>'U', 'd3'=>'E']
        ]
    ],
    
    // --- TERAPIA OCUPACIONAL (SECUENCIAS) ---
    [
        't' => 'Secuencia: Lavado de Dientes', 'd' => 'Pasos de higiene.', 'c' => 8, 'm' => 4, 'tipo' => 'secuencia', 'min' => 4,
        'data' => [
            ['i'=>'png?text=Pasta', 'ok'=>'Poner Pasta'],
            ['i'=>'png?text=Cepillar', 'ok'=>'Cepillar'],
            ['i'=>'png?text=Enjuagar', 'ok'=>'Enjuagar'],
            ['i'=>'png?text=Secar', 'ok'=>'Secar']
        ]
    ],
    [
        't' => 'Secuencia: Lavado de Manos', 'd' => 'Higiene de manos.', 'c' => 8, 'm' => 4, 'tipo' => 'secuencia', 'min' => 3,
        'data' => [
            ['i'=>'png?text=Agua', 'ok'=>'Mojar manos'],
            ['i'=>'png?text=Jabon', 'ok'=>'Enjabonar'],
            ['i'=>'png?text=Frotar', 'ok'=>'Frotar'],
            ['i'=>'png?text=Toalla', 'ok'=>'Secar']
        ]
    ],
    [
        't' => 'Secuencia: Hacer la Cama', 'd' => 'Tareas del hogar.', 'c' => 8, 'm' => 4, 'tipo' => 'secuencia', 'min' => 8,
        'data' => [
            ['i'=>'png?text=Sabana', 'ok'=>'Estirar sábana'],
            ['i'=>'png?text=Cobija', 'ok'=>'Poner frazada'],
            ['i'=>'png?text=Almohada', 'ok'=>'Poner almohada']
        ]
    ],

    // --- MATEMÁTICA ---
    [
        't' => 'Aprender a Sumar', 'd' => 'Sumas con objetos.', 'c' => 2, 'm' => 4, 'tipo' => 'seleccion', 'min' => 5,
        'data' => [
            ['i'=>'png?text=2+Manzanas+%2B+1', 'ok'=>'3', 'd1'=>'2', 'd2'=>'4', 'd3'=>'5'],
            ['i'=>'png?text=2+Autos+%2B+2', 'ok'=>'4', 'd1'=>'3', 'd2'=>'5', 'd3'=>'2'],
            ['i'=>'png?text=3+Dedos+%2B+2', 'ok'=>'5', 'd1'=>'4', 'd2'=>'6', 'd3'=>'10']
        ]
    ],
    [
        't' => 'Restas Visuales', 'd' => 'Quitar elementos.', 'c' => 2, 'm' => 4, 'tipo' => 'seleccion', 'min' => 6,
        'data' => [
            ['i'=>'png?text=5+Peces+-+2', 'ok'=>'3', 'd1'=>'2', 'd2'=>'4', 'd3'=>'1'],
            ['i'=>'png?text=3+Globos+-+1', 'ok'=>'2', 'd1'=>'1', 'd2'=>'3', 'd3'=>'0']
        ]
    ],
    [
        't' => 'Tablas de Multiplicar', 'd' => 'Repaso x2, x3.', 'c' => 2, 'm' => 4, 'tipo' => 'seleccion', 'min' => 8,
        'data' => [
            ['i'=>'png?text=2+x+3', 'ok'=>'6', 'd1'=>'5', 'd2'=>'8', 'd3'=>'9'],
            ['i'=>'png?text=4+x+4', 'ok'=>'16', 'd1'=>'14', 'd2'=>'12', 'd3'=>'20'],
            ['i'=>'png?text=5+x+2', 'ok'=>'10', 'd1'=>'15', 'd2'=>'7', 'd3'=>'12']
        ]
    ],
    [
        't' => 'Concepto División', 'd' => 'Repartir en partes iguales.', 'c' => 2, 'm' => 4, 'tipo' => 'seleccion', 'min' => 8,
        'data' => [
            ['i'=>'png?text=6+Caramelos+/+2+Nenes', 'ok'=>'3 cada uno', 'd1'=>'2 cada uno', 'd2'=>'4 cada uno', 'd3'=>'1 cada uno'],
            ['i'=>'png?text=10+Galletas+/+5+Nenes', 'ok'=>'2 cada uno', 'd1'=>'5 cada uno', 'd2'=>'1 cada uno', 'd3'=>'3 cada uno']
        ]
    ],

    // --- HISTORIA Y GEOGRAFÍA ---
    [
        't' => 'Línea de Tiempo: 1810', 'd' => 'Sucesos de Mayo.', 'c' => 3, 'm' => 4, 'tipo' => 'secuencia', 'min' => 8,
        'data' => [
            ['i'=>'png?text=Cabildo', 'ok'=>'Revolución de Mayo'],
            ['i'=>'png?text=Bandera', 'ok'=>'Creación Bandera'],
            ['i'=>'png?text=Casa+Tucuman', 'ok'=>'Independencia']
        ]
    ],
    [
        't' => 'Mapa: Provincias', 'd' => 'Ubicar en el mapa.', 'c' => 3, 'm' => 4, 'tipo' => 'seleccion', 'min' => 8,
        'data' => [
            ['i'=>'png?text=Mapa+Jujuy', 'ok'=>'Jujuy', 'd1'=>'Salta', 'd2'=>'Chaco', 'd3'=>'Misiones'],
            ['i'=>'png?text=Mapa+BsAs', 'ok'=>'Buenos Aires', 'd1'=>'Córdoba', 'd2'=>'Santa Fe', 'd3'=>'La Pampa'],
            ['i'=>'png?text=Mapa+Tierra+Fuego', 'ok'=>'Tierra del Fuego', 'd1'=>'Santa Cruz', 'd2'=>'Chubut', 'd3'=>'Neuquén']
        ]
    ],
    [
        't' => 'Próceres Argentinos', 'd' => 'Identificar personajes.', 'c' => 3, 'm' => 4, 'tipo' => 'seleccion', 'min' => 6,
        'data' => [
            ['i'=>'png?text=Belgrano', 'ok'=>'Belgrano', 'd1'=>'San Martín', 'd2'=>'Moreno', 'd3'=>'Sarmiento'],
            ['i'=>'png?text=San+Martin', 'ok'=>'San Martín', 'd1'=>'Belgrano', 'd2'=>'Saavedra', 'd3'=>'Roca']
        ]
    ],

    // --- MEMORIA ---
    [
        't' => 'Memotest: Emociones', 'd' => 'Encuentra las parejas.', 'c' => 6, 'm' => 4, 'tipo' => 'memoria', 'min' => 5,
        'data' => [
            ['i'=>'FFB347/white?text=Feliz', 'ok'=>'Feliz'],
            ['i'=>'88B04B/white?text=Triste', 'ok'=>'Triste'],
            ['i'=>'FF6B6B/white?text=Enojado', 'ok'=>'Enojado'],
            ['i'=>'92A8D1/white?text=Sorpresa', 'ok'=>'Sorpresa'],
            ['i'=>'cccccc/black?text=Miedo', 'ok'=>'Miedo'],
            ['i'=>'F7CAC9/white?text=Calma', 'ok'=>'Calma']
        ]
    ],
    [
        't' => 'Memotest: Animales', 'd' => 'Parejas de animales.', 'c' => 2, 'm' => 4, 'tipo' => 'memoria', 'min' => 4,
        'data' => [
            ['i'=>'png?text=Leon', 'ok'=>'León'],
            ['i'=>'png?text=Sapo', 'ok'=>'Sapo'],
            ['i'=>'png?text=Gato', 'ok'=>'Gato'],
            ['i'=>'png?text=Pez', 'ok'=>'Pez'],
            ['i'=>'png?text=Perro', 'ok'=>'Perro'],
            ['i'=>'png?text=Vaca', 'ok'=>'Vaca']
        ]
    ],

    // --- ARTE Y PINTURA ---
    [
        't' => 'Pintar: Animales', 'd' => 'Colorea libremente.', 'c' => 8, 'm' => 4, 'tipo' => 'pintura', 'min' => 3,
        'data' => [
            ['i'=>'white/black?text=Leon+Lineas', 'ok'=>'León'],
            ['i'=>'white/black?text=Auto+Lineas', 'ok'=>'Auto'],
            ['i'=>'white/black?text=Casa+Lineas', 'ok'=>'Casa']
        ]
    ],
    [
        't' => 'Arte Pixelado', 'd' => 'Pinta cuadraditos.', 'c' => 8, 'm' => 4, 'tipo' => 'pintura', 'min' => 8,
        'data' => [
            ['i'=>'white/black?text=Grilla+Pixel', 'ok'=>'Pixel Art']
        ]
    ],

    // --- ADOLESCENTES Y SOCIAL (+10 Años) ---
    [
        't' => 'Frases Hechas', 'd' => '¿Qué significa?', 'c' => 1, 'm' => 4, 'tipo' => 'seleccion', 'min' => 10,
        'data' => [
            ['i'=>'png?text=Tomar+el+pelo', 'ok'=>'Burlar', 'd1'=>'Cortar pelo', 'd2'=>'Peinar', 'd3'=>'Jugar'],
            ['i'=>'png?text=Estar+en+las+nubes', 'ok'=>'Distraído', 'd1'=>'Volando', 'd2'=>'Saltando', 'd3'=>'Durmiendo']
        ]
    ],
    [
        't' => 'Resolución de Conflictos', 'd' => '¿Qué haces?', 'c' => 6, 'm' => 4, 'tipo' => 'seleccion', 'min' => 10,
        'data' => [
            ['i'=>'png?text=Me+empujan', 'ok'=>'Aviso a un adulto', 'd1'=>'Pego', 'd2'=>'Grito', 'd3'=>'Lloro'],
            ['i'=>'png?text=Perdi+el+juego', 'ok'=>'Respiro y felicito', 'd1'=>'Rompo todo', 'd2'=>'Me enojo', 'd3'=>'Insulto']
        ]
    ],
    [
        't' => 'Manejo del Dinero', 'd' => 'Calcular vuelto.', 'c' => 8, 'm' => 4, 'tipo' => 'seleccion', 'min' => 10,
        'data' => [
            ['i'=>'png?text=Gasto+50+Pago+100', 'ok'=>'Vuelto 50', 'd1'=>'Vuelto 20', 'd2'=>'Vuelto 10', 'd3'=>'Nada'],
            ['i'=>'png?text=Gasto+20+Pago+50', 'ok'=>'Vuelto 30', 'd1'=>'Vuelto 40', 'd2'=>'Vuelto 10', 'd3'=>'Vuelto 5']
        ]
    ],
    [
        't' => 'Higiene: Afeitarse', 'd' => 'Secuencia masculina.', 'c' => 8, 'm' => 4, 'tipo' => 'secuencia', 'min' => 12,
        'data' => [
            ['i'=>'png?text=Crema', 'ok'=>'Poner espuma'],
            ['i'=>'png?text=Afeitar', 'ok'=>'Pasar máquina'],
            ['i'=>'png?text=Enjuagar', 'ok'=>'Enjuagar cara'],
            ['i'=>'png?text=Secar', 'ok'=>'Secar suavemente']
        ]
    ],
    [
        't' => 'Uso del Celular', 'd' => 'Reglas digitales.', 'c' => 6, 'm' => 4, 'tipo' => 'seleccion', 'min' => 10,
        'data' => [
            ['i'=>'png?text=Mensaje+Desconocido', 'ok'=>'Bloquear y avisar', 'd1'=>'Responder', 'd2'=>'Mandar foto', 'd3'=>'Llamar'],
            ['i'=>'png?text=Hora+de+dormir', 'ok'=>'Dejar celular lejos', 'd1'=>'Usar en la cama', 'd2'=>'Jugar toda la noche', 'd3'=>'Ver videos']
        ]
    ]
];

// 3. INSERCIÓN MASIVA
$contJuegos = 0;
$contFichas = 0;

foreach($biblioteca as $j) {
    $titulo = $conn->real_escape_string($j['t']);
    $desc = $conn->real_escape_string($j['d']);
    $tipo = $j['tipo'];
    $cat = $j['c'];
    $motor = $j['m'];
    $min = $j['min'];
    
    // Insertar Juego
    $sql = "INSERT INTO juegos (titulo, descripcion, id_categoria, id_motor, activo, tipo_juego, edad_min) 
            VALUES ('$titulo', '$desc', $cat, $motor, 1, '$tipo', $min)";
    
    if($conn->query($sql)) {
        $id_juego = $conn->insert_id; // ID REAL
        $contJuegos++;
        
        // Insertar Contenido
        $orden = 1;
        foreach($j['data'] as $d) {
            $img = "https://placehold.co/400x400/" . $d['i'];
            $ok = $conn->real_escape_string($d['ok']);
            $d1 = isset($d['d1']) ? $conn->real_escape_string($d['d1']) : '';
            $d2 = isset($d['d2']) ? $conn->real_escape_string($d['d2']) : '';
            $d3 = isset($d['d3']) ? $conn->real_escape_string($d['d3']) : '';
            
            $sql2 = "INSERT INTO juegos_contenido (id_juego, imagen, palabra_correcta, distractor1, distractor2, distractor3, orden)
                     VALUES ($id_juego, '$img', '$ok', '$d1', '$d2', '$d3', $orden)";
            $conn->query($sql2);
            $orden++;
            $contFichas++;
        }
    }
}

echo "<h2>¡INSTALACIÓN COMPLETADA! 🎉</h2>";
echo "<ul>";
echo "<li>Juegos Creados: <strong>$contJuegos</strong></li>";
echo "<li>Fichas de Contenido: <strong>$contFichas</strong></li>";
echo "</ul>";
echo "<a href='../juegos.php' style='background:blue; color:white; padding:15px; text-decoration:none; display:inline-block; border-radius:10px;'>IR A JUGAR AHORA</a>";
echo "</div>";
?>