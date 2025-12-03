<style>
    .tablero-simon {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        max-width: 400px;
        margin: 0 auto;
    }
    .boton-simon {
        aspect-ratio: 1/1;
        border-radius: 20px;
        cursor: pointer;
        opacity: 0.6;
        transition: all 0.1s;
        border: 4px solid rgba(0,0,0,0.1);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .boton-simon.activo {
        opacity: 1;
        transform: scale(0.95);
        box-shadow: 0 0 20px currentColor;
        border-color: white;
    }
    #mensaje-estado {
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: var(--color-texto);
        height: 30px; /* Para que no salte si está vacío */
    }
    .btn-iniciar {
        margin-top: 20px;
        background: var(--color-texto);
    }
</style>

<div id="mensaje-estado">Toca "Iniciar" para jugar</div>

<div class="tablero-simon">
    <div class="boton-simon" id="btn-0" style="background-color: #FF6B6B; color: #FF6B6B;" data-id="0"></div>
    <div class="boton-simon" id="btn-1" style="background-color: #4ECDC4; color: #4ECDC4;" data-id="1"></div>
    <div class="boton-simon" id="btn-2" style="background-color: #FFE66D; color: #FFE66D;" data-id="2"></div>
    <div class="boton-simon" id="btn-3" style="background-color: #1A535C; color: #1A535C;" data-id="3"></div>
</div>

<div style="text-align: center;">
    <button class="btn-grande btn-iniciar" onclick="iniciarJuego()">
        <i class="fa-solid fa-play"></i> Iniciar
    </button>
</div>

<script>
(function() {
    // Configuración
    const botones = document.querySelectorAll('.boton-simon');
    const mensaje = document.getElementById('mensaje-estado');
    let secuencia = [];
    let pasoUsuario = 0;
    let jugando = false;
    let turnoUsuario = false;

    // Sonidos simples con oscilador (No requiere archivos)
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const frecuencias = [261.63, 329.63, 392.00, 523.25]; // Do, Mi, Sol, Do (Acorde Mayor)

    function tocarSonido(index) {
        if (audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.value = frecuencias[index];
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        gain.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 0.5);
        osc.stop(audioCtx.currentTime + 0.5);
    }

    function iluminar(index) {
        const btn = document.getElementById('btn-' + index);
        btn.classList.add('activo');
        tocarSonido(index);
        setTimeout(() => btn.classList.remove('activo'), 400);
    }

    window.iniciarJuego = function() {
        secuencia = [];
        jugando = true;
        document.querySelector('.btn-iniciar').style.display = 'none';
        mensaje.innerText = "¡Mira bien!";
        siguienteNivel();
    };

    function siguienteNivel() {
        pasoUsuario = 0;
        turnoUsuario = false;
        secuencia.push(Math.floor(Math.random() * 4));
        
        // Reproducir secuencia
        let i = 0;
        const intervalo = setInterval(() => {
            iluminar(secuencia[i]);
            i++;
            if (i >= secuencia.length) {
                clearInterval(intervalo);
                turnoUsuario = true;
                mensaje.innerText = "¡Tu turno!";
            }
        }, 800); // Velocidad entre luces
    }

    // Interacción del usuario
    botones.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!turnoUsuario) return;

            const id = parseInt(e.target.getAttribute('data-id'));
            iluminar(id);

            if (id === secuencia[pasoUsuario]) {
                // Correcto
                pasoUsuario++;
                if (pasoUsuario === secuencia.length) {
                    turnoUsuario = false;
                    mensaje.innerText = "¡Bien hecho! 👍";
                    setTimeout(siguienteNivel, 1000);
                }
            } else {
                // Error
                mensaje.innerText = "¡Oh no! Intenta de nuevo.";
                jugando = false;
                turnoUsuario = false;
                document.querySelector('.btn-iniciar').style.display = 'inline-block';
                document.querySelector('.btn-iniciar').innerText = "Reintentar";
            }
        });
    });

})();
</script>