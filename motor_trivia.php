<?php
// Este archivo se incluye DENTRO de jugar.php, así que ya tiene acceso a $conn y $game_id

// 1. OBTENER PREGUNTAS DE LA BASE DE DATOS
$sql_preguntas = "SELECT * FROM preguntas WHERE id_juego = $game_id ORDER BY RAND()"; // Orden aleatorio
$res_preguntas = $conn->query($sql_preguntas);

$array_preguntas = [];
if ($res_preguntas->num_rows > 0) {
    while($p = $res_preguntas->fetch_assoc()) {
        $array_preguntas[] = $p;
    }
}
// Convertimos a JSON para usarlo en Javascript
$json_preguntas = json_encode($array_preguntas);
?>

<style>
    .trivia-container {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        width: 100%; height: 100%; padding: 20px; text-align: center;
    }

    .pregunta-box {
        font-size: 1.5rem; color: var(--color-texto); margin-bottom: 30px;
        font-weight: bold;
    }

    .opciones-grid {
        display: grid; gap: 15px; width: 100%; max-width: 600px;
    }

    .opcion-btn {
        background: white; border: 2px solid #ddd; padding: 20px;
        border-radius: 15px; font-size: 1.2rem; cursor: pointer;
        transition: transform 0.2s, background 0.2s;
        color: #555; font-weight: bold;
        box-shadow: 0 4px 0 #ccc; /* Efecto 3D */
    }
    
    .opcion-btn:active { transform: translateY(4px); box-shadow: none; }
    
    /* Estados de respuesta */
    .opcion-btn.correcto { background-color: #88B04B; color: white; border-color: #88B04B; box-shadow: 0 4px 0 #6a8f3d; }
    .opcion-btn.incorrecto { background-color: #FF6B6B; color: white; border-color: #FF6B6B; box-shadow: 0 4px 0 #d9534f; }

    .barra-progreso {
        width: 100%; max-width: 600px; height: 10px; background: #eee;
        border-radius: 5px; margin-top: 30px; overflow: hidden;
    }
    .barra-relleno {
        height: 100%; background: var(--color-secundario); width: 0%; transition: width 0.5s;
    }

    /* Pantalla final */
    #pantalla-final { display: none; flex-direction: column; align-items: center; }
    .puntaje-grande { font-size: 4rem; color: var(--color-primario); margin: 20px 0; font-weight: 800; }
</style>

<div id="juego-trivia" class="trivia-container">
    
    <div id="zona-pregunta">
        <h3 style="color: #999; font-size: 0.9rem; margin-bottom: 10px;">PREGUNTA <span id="num-actual">1</span> DE <span id="total-preguntas">0</span></h3>
        
        <div class="pregunta-box" id="texto-pregunta">Cargando...</div>

        <div class="opciones-grid">
            <button class="opcion-btn" onclick="elegir('a')" id="btn-a">Opción A</button>
            <button class="opcion-btn" onclick="elegir('b')" id="btn-b">Opción B</button>
            <button class="opcion-btn" onclick="elegir('c')" id="btn-c">Opción C</button>
        </div>

        <div class="barra-progreso">
            <div class="barra-relleno" id="progreso"></div>
        </div>
    </div>

    <div id="pantalla-final">
        <h2>¡Juego Terminado!</h2>
        <p>Tu puntaje final es:</p>
        <div class="puntaje-grande"><span id="score-final">0</span>/100</div>
        <p id="mensaje-final" style="font-size: 1.2rem; margin-bottom: 20px;"></p>
        
        <button onclick="location.reload()" class="btn-grande btn-jugar">
            <i class="fa-solid fa-rotate-right"></i> Jugar Otra Vez
        </button>
        <a href="juegos.php" class="btn-grande btn-padres" style="margin-top:10px;">Salir</a>
    </div>

</div>

<script>
(function() {
    // Recibimos las preguntas de PHP
    const preguntas = <?php echo $json_preguntas; ?>;
    
    if (preguntas.length === 0) {
        document.getElementById('texto-pregunta').innerText = "No hay preguntas cargadas para este juego.";
        return;
    }

    let indiceActual = 0;
    let puntaje = 0;
    let bloqueado = false; // Para que no clickeen mil veces

    const ui = {
        texto: document.getElementById('texto-pregunta'),
        btnA: document.getElementById('btn-a'),
        btnB: document.getElementById('btn-b'),
        btnC: document.getElementById('btn-c'),
        numActual: document.getElementById('num-actual'),
        total: document.getElementById('total-preguntas'),
        barra: document.getElementById('progreso'),
        zonaJuego: document.getElementById('zona-pregunta'),
        zonaFinal: document.getElementById('pantalla-final'),
        scoreFinal: document.getElementById('score-final'),
        msgFinal: document.getElementById('mensaje-final')
    };

    ui.total.innerText = preguntas.length;

    function cargarPregunta() {
        bloqueado = false;
        const p = preguntas[indiceActual];
        
        ui.texto.innerText = p.pregunta;
        ui.btnA.innerText = p.opcion_a;
        ui.btnB.innerText = p.opcion_b;
        ui.btnC.innerText = p.opcion_c;
        
        ui.numActual.innerText = indiceActual + 1;
        
        // Actualizar barra
        const porcentaje = (indiceActual / preguntas.length) * 100;
        ui.barra.style.width = porcentaje + "%";

        // Limpiar colores
        resetBotones();
    }

    function resetBotones() {
        ['btn-a', 'btn-b', 'btn-c'].forEach(id => {
            document.getElementById(id).className = 'opcion-btn';
        });
    }

    window.elegir = function(opcion) {
        if (bloqueado) return;
        bloqueado = true;

        const p = preguntas[indiceActual];
        const btnElegido = document.getElementById('btn-' + opcion);
        const btnCorrecto = document.getElementById('btn-' + p.correcta);

        if (opcion === p.correcta) {
            // CORRECTO
            btnElegido.classList.add('correcto');
            puntaje++;
            reproducirSonido(true);
        } else {
            // INCORRECTO
            btnElegido.classList.add('incorrecto');
            btnCorrecto.classList.add('correcto'); // Le mostramos cuál era la correcta
            reproducirSonido(false);
        }

        // Esperar 1.5 segundos y pasar a la siguiente
        setTimeout(() => {
            indiceActual++;
            if (indiceActual < preguntas.length) {
                cargarPregunta();
            } else {
                terminarJuego();
            }
        }, 1500);
    };

    function terminarJuego() {
        ui.zonaJuego.style.display = 'none';
        ui.zonaFinal.style.display = 'flex';
        
        // Calcular puntaje en base 100
        const nota = Math.round((puntaje / preguntas.length) * 100);
        ui.scoreFinal.innerText = nota;

        if (nota === 100) ui.msgFinal.innerText = "¡Perfecto! Sos un genio 🧠";
        else if (nota >= 60) ui.msgFinal.innerText = "¡Muy bien! Sigue así 👏";
        else ui.msgFinal.innerText = "¡Buen intento! A practicar 💪";
    }

    // Sonidos simples con AudioContext (sin archivos)
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function reproducirSonido(esCorrecto) {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        
        if (esCorrecto) {
            // Sonido "Ding" (agudo)
            osc.type = 'sine';
            osc.frequency.setValueAtTime(600, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1000, audioCtx.currentTime + 0.1);
        } else {
            // Sonido "Boonk" (grave)
            osc.type = 'square';
            osc.frequency.setValueAtTime(150, audioCtx.currentTime);
            osc.frequency.linearRampToValueAtTime(100, audioCtx.currentTime + 0.2);
        }
        
        osc.start();
        gain.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 0.3);
        osc.stop(audioCtx.currentTime + 0.3);
    }

    // Iniciar
    cargarPregunta();

})();
</script>