<style>
    .contenedor-mate {
        display: flex; flex-direction: column; align-items: center;
        width: 100%; padding: 10px; user-select: none;
    }

    /* Zona donde aparecen los objetos (Manzanas, etc) */
    .pizarra-visual {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        background: white; padding: 20px; border-radius: 20px;
        border: 4px solid #88B04B; box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px; min-height: 150px; flex-wrap: wrap;
    }

    .objeto-icon {
        font-size: 3.5rem; color: #FF6B6B; /* Rojo manzana por defecto */
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .signo { font-size: 3rem; color: #ccc; margin: 0 15px; font-weight: bold; }

    /* Opciones de respuesta (Números grandes) */
    .opciones-mate { display: flex; gap: 20px; justify-content: center; }

    .btn-numero {
        width: 80px; height: 80px; border-radius: 50%;
        background: white; border: 3px solid #92A8D1;
        font-size: 2.5rem; font-weight: bold; color: #555;
        cursor: pointer; box-shadow: 0 5px 0 #7a8fb8;
        transition: transform 0.1s;
    }
    .btn-numero:active { transform: translateY(4px); box-shadow: none; }
    
    .btn-numero.correcto { background: #88B04B; color: white; border-color: #6a8f3d; box-shadow: 0 5px 0 #4e6b2c; }
    .btn-numero.incorrecto { opacity: 0.5; transform: scale(0.9); }

    /* Animación de entrada */
    @keyframes popIn { from { transform: scale(0); } to { transform: scale(1); } }

    /* Mensaje de refuerzo positivo */
    #feedback-mate {
        height: 40px; margin-top: 20px; font-size: 1.5rem;
        color: var(--color-primario); font-weight: bold; opacity: 0; transition: opacity 0.3s;
    }
</style>

<div class="contenedor-mate">
    
    <h2 style="color: #999; font-size: 1rem; margin-bottom: 10px;">
        <i class="fa-solid fa-apple-whole"></i> APRENDEMOS A CONTAR Y SUMAR
    </h2>

    <div class="pizarra-visual" id="pizarra">
        </div>

    <div class="opciones-mate" id="opciones">
        </div>

    <div id="feedback-mate">¡Muy bien! 🌟</div>

</div>

<script>
(function() {
    // CONFIGURACIÓN TERAPÉUTICA
    // Empezamos con sumas simples visuales (hasta 10)
    const config = <?php echo isset($juego['configuracion']) ? $juego['configuracion'] : '{}'; ?>;
    const nivelMaximo = config.maximo || 5; // Por defecto sumas hasta 5 (Nivel inicial)
    
    const pizarra = document.getElementById('pizarra');
    const contenedorOpciones = document.getElementById('opciones');
    const feedback = document.getElementById('feedback-mate');
    
    // Iconos divertidos para variar (Pictogramas)
    const iconos = [
        { icon: 'fa-apple-whole', color: '#FF6B6B' }, // Manzana
        { icon: 'fa-car-side', color: '#4ECDC4' },    // Auto
        { icon: 'fa-star', color: '#FFE66D' },        // Estrella
        { icon: 'fa-fish', color: '#FFB347' },        // Pez
        { icon: 'fa-cat', color: '#92A8D1' }          // Gato
    ];

    let respuestaCorrecta = 0;
    let bloqueado = false;

    function generarNivel() {
        bloqueado = false;
        feedback.style.opacity = '0';
        pizarra.innerHTML = '';
        contenedorOpciones.innerHTML = '';

        // 1. ELEGIR TEMA (Manzanas, Autos...)
        const tema = iconos[Math.floor(Math.random() * iconos.length)];
        
        // 2. GENERAR PROBLEMA (Ej: 2 + 1)
        // Aseguramos que la suma no supere el nivel máximo (para no frustrar)
        const num1 = Math.floor(Math.random() * (nivelMaximo - 1)) + 1;
        const num2 = Math.floor(Math.random() * (nivelMaximo - num1)) + 1;
        respuestaCorrecta = num1 + num2;

        // 3. DIBUJAR PICTOGRAMAS (Apoyo Visual)
        // Grupo 1
        dibujarGrupo(num1, tema);
        
        // Signo Más
        const mas = document.createElement('div');
        mas.className = 'signo';
        mas.innerText = '+';
        pizarra.appendChild(mas);
        
        // Grupo 2
        dibujarGrupo(num2, tema);

        // 4. GENERAR OPCIONES (Sin trampa, números cercanos)
        let opciones = [respuestaCorrecta];
        while(opciones.length < 3) {
            let n = Math.floor(Math.random() * nivelMaximo) + 1;
            if(!opciones.includes(n) && n !== respuestaCorrecta) opciones.push(n);
        }
        opciones.sort(() => Math.random() - 0.5); // Mezclar

        opciones.forEach(num => {
            const btn = document.createElement('button');
            btn.className = 'btn-numero';
            btn.innerText = num;
            btn.onclick = () => verificar(btn, num);
            contenedorOpciones.appendChild(btn);
        });
    }

    function dibujarGrupo(cantidad, tema) {
        for(let i=0; i<cantidad; i++) {
            const el = document.createElement('i');
            el.className = `fa-solid ${tema.icon} objeto-icon`;
            el.style.color = tema.color;
            // Pequeño retraso en animación para que aparezcan uno por uno (atención)
            el.style.animationDelay = (i * 0.1) + 's';
            pizarra.appendChild(el);
        }
    }

    function verificar(btn, valor) {
        if(bloqueado) return;
        
        if(valor === respuestaCorrecta) {
            // CORRECTO
            bloqueado = true;
            btn.classList.add('correcto');
            feedback.innerText = "¡Excelente! 🎉";
            feedback.style.opacity = '1';
            reproducirSonido(true);
            
            setTimeout(generarNivel, 2000); // Tiempo para celebrar
        } else {
            // ERROR (Sin castigo visual fuerte, solo se apaga)
            btn.classList.add('incorrecto');
            reproducirSonido(false);
        }
    }

    // Sonidos suaves
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function reproducirSonido(bien) {
        if (audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        
        if(bien) {
            osc.frequency.setValueAtTime(500, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1000, audioCtx.currentTime + 0.2);
        } else {
            osc.frequency.setValueAtTime(200, audioCtx.currentTime);
            osc.frequency.linearRampToValueAtTime(150, audioCtx.currentTime + 0.2);
        }
        
        osc.start();
        osc.stop(audioCtx.currentTime + 0.3);
    }

    // Arrancar
    generarNivel();

})();
</script>