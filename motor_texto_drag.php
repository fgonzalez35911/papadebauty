<style>
    /* RESET */
    .motor-texto * { box-sizing: border-box; }

    .motor-texto {
        width: 100%; max-width: 900px; margin: 0 auto; 
        display: flex; flex-direction: column; align-items: center; 
        user-select: none; padding: 20px; font-family: 'Nunito', sans-serif;
        padding-bottom: 100px; overflow-x: hidden; 
    }

    /* --- ESTILOS DE LA PANTALLA FINAL (FIESTA TOTAL) --- */
    .final-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); /* Degradado violeta/rosa */
        z-index: 10000;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        animation: fadeIn 0.5s ease-out;
    }

    .winner-card {
        background: white; padding: 40px 30px; border-radius: 40px;
        text-align: center; 
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        border: 6px solid #FFD700; /* Borde Dorado */
        width: 90%; max-width: 500px;
        position: relative; z-index: 10;
        animation: bounceIn 1s cubic-bezier(0.215, 0.610, 0.355, 1.000);
    }

    .winner-title {
        font-size: 3.5rem; color: #FF5722; margin: 0;
        text-shadow: 3px 3px 0px #FFC107; font-weight: 900;
        line-height: 1.2; letter-spacing: 1px;
    }

    .winner-text { font-size: 1.4rem; color: #555; margin: 15px 0 30px; font-weight: bold; }

    .btn-winner {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        width: 100%; padding: 18px; margin: 12px 0;
        border-radius: 50px; font-size: 1.2rem; font-weight: 900;
        text-decoration: none; color: white !important;
        transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: none;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15); text-transform: uppercase;
    }
    .btn-replay { background: linear-gradient(45deg, #88B04B, #6a8f3d); }
    .btn-menu { background: linear-gradient(45deg, #92A8D1, #7a8fb8); }
    
    .btn-winner:hover { transform: scale(1.05) translateY(-3px); box-shadow: 0 12px 25px rgba(0,0,0,0.25); }
    .btn-winner:active { transform: scale(0.95); }

    /* CONFETI */
    .confetti {
        position: absolute; width: 12px; height: 12px;
        animation: fall linear forwards; z-index: 1;
        border-radius: 3px;
    }
    @keyframes fall { to { transform: translateY(100vh) rotate(720deg); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes bounceIn { 
        0% { transform: scale(0.3); opacity: 0; } 
        50% { transform: scale(1.05); opacity: 1; } 
        70% { transform: scale(0.9); } 
        100% { transform: scale(1); } 
    }

    /* --- ESTILOS DEL JUEGO (ORIGINALES QUE FUNCIONABAN) --- */
    .zona-pregunta {
        display: flex; align-items: center; justify-content: center; 
        gap: 20px; margin-bottom: 40px;
        background: white; padding: 30px; border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee;
        width: 95%; margin-left: auto; margin-right: auto;
        box-sizing: border-box; flex-wrap: wrap; 
    }

    .ficha-fija {
        background: #92A8D1; color: white; 
        font-size: 1.8rem; font-weight: 800; text-transform: uppercase;
        padding: 15px 30px; border-radius: 12px;
        box-shadow: 0 5px 0 #7a8fb8;
        text-align: center; display: inline-block;
        max-width: 100%; word-break: break-word;
    }

    .signo-igual { font-size: 2.5rem; color: #ccc; display: flex; align-items: center; justify-content: center; }

    .slot-destino {
        width: 220px; height: 80px; border: 3px dashed #ccc; border-radius: 15px;
        background: #f9f9f9; display: flex; align-items: center; justify-content: center;
        transition: 0.3s; box-sizing: border-box;
        max-width: 100%;
    }
    .slot-destino.lleno { border-style: solid; border-color: #88B04B; background: #e8f5e9; box-shadow: 0 0 15px rgba(136, 176, 75, 0.2); }

    .zona-opciones {
        display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;
        width: 100%; padding: 10px 0;
    }

    .ficha-drag {
        background: white; color: #555; border: 2px solid #e0e0e0;
        font-size: 1.3rem; font-weight: 700; text-transform: uppercase;
        padding: 12px 25px; border-radius: 12px;
        cursor: grab; box-shadow: 0 5px 0 #ddd; 
        position: relative; min-width: 140px; text-align: center;
        
        touch-action: none; /* Bloquea scroll solo en la ficha */
    }
    
    /* ESTILO ARRASTRANDO (VOLVEMOS AL ORIGINAL QUE TE GUSTABA) */
    .ficha-drag.arrastrando { 
        cursor: grabbing; position: fixed !important; z-index: 9999 !important; 
        opacity: 0.95 !important; transform: scale(1.1);
        box-shadow: 0 15px 30px rgba(0,0,0,0.3) !important;
        border-color: #92A8D1; margin: 0 !important;
    }
    
    .ficha-drag.correcta { 
        background: #88B04B; color: white; border-color: #6a8f3d; 
        box-shadow: none; pointer-events: none; cursor: default; 
        width: 100%; height: 100%; margin: 0; border: none; border-radius: 0; 
        display: flex; align-items: center; justify-content: center; 
    }
    .ficha-drag.incorrecta { animation: shake 0.4s; background: #FF6B6B; color: white; border-color: #d32f2f; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }

    @media (max-width: 600px) {
        .zona-pregunta { flex-direction: column !important; gap: 20px; padding: 30px 15px; width: 100%; margin: 0 auto 30px auto; }
        .signo-igual { transform: rotate(90deg); margin: 0; font-size: 2rem; }
        .ficha-fija { width: 90%; max-width: 280px; font-size: 1.4rem; padding: 15px 10px; margin: 0 auto; }
        .slot-destino { width: 90%; max-width: 280px; height: 75px; margin: 0 auto; }
        .ficha-drag { font-size: 1.1rem; padding: 10px 15px; min-width: 100px; width: 45%; box-sizing: border-box; }
        
        /* Ajuste botones finales móvil */
        .winner-title { font-size: 2.5rem; }
        .btn-winner { padding: 15px; font-size: 1rem; }
    }
</style>

<div id="lienzo-texto" class="motor-texto"></div>
<audio id="reproductor-fx"></audio>

<script>
(function() {
    const lienzo = document.getElementById('lienzo-texto');
    const datos = <?php echo json_encode($contenido_manual); ?>;
    
    if(datos.length === 0) { lienzo.innerHTML = "<h3>Falta contenido.</h3>"; return; }

    let indice = 0;
    let dragItem = null;
    
    // Variables de posición (Lógica original)
    let offsetX = 0;
    let offsetY = 0;

    function cargarNivel() {
        // --- PANTALLA FINAL (FIESTA NUEVA) ---
        if(indice >= datos.length) {
            lienzo.innerHTML = ''; 
            
            const overlay = document.createElement('div');
            overlay.className = 'final-overlay';
            
            overlay.innerHTML = `
                <div class="winner-card">
                    <h1 class="winner-title">¡FELICITACIONES! 🎉</h1>
                    <div style="font-size:4rem; margin:10px 0; animation: bounceIn 2s infinite;">🏆</div>
                    <p class="winner-text">¡Sos un campeón! Completaste todo.</p>
                    
                    <button onclick="location.reload()" class="btn-winner btn-replay">
                        <i class="fa-solid fa-rotate-right"></i> Jugar de nuevo
                    </button>
                    <a href="juegos.php" class="btn-winner btn-menu">
                        <i class="fa-solid fa-gamepad"></i> Ir al Menú
                    </a>
                </div>
            `;
            
            document.body.appendChild(overlay);
            lanzarConfeti(overlay);
            playFx(true); 
            return;
        }

        const item = datos[indice];
        const pregunta = item.imagen; 
        const correcta = item.palabra_correcta;
        
        let opciones = [correcta, item.distractor1, item.distractor2, item.distractor3]
                        .filter(t => t && t.trim() !== "")
                        .sort(() => Math.random() - 0.5);

        lienzo.innerHTML = `
            <h3 style="color:#888; margin-bottom:20px; font-size:1.2rem; text-align:center;">Arrastra el opuesto:</h3>
            <div class="zona-pregunta">
                <div class="ficha-fija">${pregunta}</div>
                <div class="signo-igual"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="slot-destino" id="slot-meta" data-target="${correcta}"></div>
            </div>
            <div class="zona-opciones">
                ${opciones.map(op => `<div class="ficha-drag" data-valor="${op}">${op}</div>`).join('')}
            </div>
        `;

        activarEventos();
    }

    // --- FUNCIÓN CONFETI ---
    function lanzarConfeti(container) {
        const colores = ['#FFC107', '#FF5722', '#4CAF50', '#2196F3', '#E91E63', '#9C27B0'];
        for (let i = 0; i < 100; i++) { 
            const c = document.createElement('div');
            c.className = 'confetti';
            c.style.left = Math.random() * 100 + 'vw';
            c.style.backgroundColor = colores[Math.floor(Math.random() * colores.length)];
            c.style.animationDuration = (Math.random() * 2 + 2) + 's';
            c.style.animationDelay = (Math.random() * 2) + 's';
            container.appendChild(c);
        }
    }

    // --- LÓGICA DE ARRASTRE ORIGINAL (La que te gustaba antes) ---
    function activarEventos() {
        const fichas = document.querySelectorAll('.ficha-drag');
        fichas.forEach(f => {
            f.addEventListener('mousedown', iniciarArrastre);
            f.addEventListener('touchstart', iniciarArrastre, {passive: false});
        });
    }

    function iniciarArrastre(e) {
        if(e.type == 'touchstart') e.preventDefault();
        dragItem = e.target;

        const rect = dragItem.getBoundingClientRect();
        
        // Coordenadas del puntero
        const clienteX = e.clientX || e.touches[0].clientX;
        const clienteY = e.clientY || e.touches[0].clientY;

        // Calculamos offset para agarrar del centro (más estable)
        offsetX = clienteX - rect.left;
        offsetY = clienteY - rect.top;

        // Preparamos para mover
        dragItem.style.width = rect.width + 'px';
        dragItem.style.height = rect.height + 'px';
        dragItem.classList.add('arrastrando');

        // Mover inmediatamente al lugar
        moverItem(clienteX, clienteY);

        document.addEventListener('mousemove', moverArrastre);
        document.addEventListener('touchmove', moverArrastre, {passive: false});
        document.addEventListener('mouseup', finalizarArrastre);
        document.addEventListener('touchend', finalizarArrastre);
    }

    function moverArrastre(e) {
        if (!dragItem) return;
        e.preventDefault();
        const clienteX = e.clientX || e.touches[0].clientX;
        const clienteY = e.clientY || e.touches[0].clientY;
        moverItem(clienteX, clienteY);
    }

    function moverItem(x, y) {
        // Movimiento directo (sin clones)
        dragItem.style.left = (x - offsetX) + 'px';
        dragItem.style.top = (y - offsetY) + 'px';
    }

    function finalizarArrastre(e) {
        if (!dragItem) return;
        
        document.removeEventListener('mousemove', moverArrastre);
        document.removeEventListener('touchmove', moverArrastre);
        document.removeEventListener('mouseup', finalizarArrastre);
        document.removeEventListener('touchend', finalizarArrastre);

        // Detectar dónde soltó
        const x = e.clientX || (e.changedTouches ? e.changedTouches[0].clientX : 0);
        const y = e.clientY || (e.changedTouches ? e.changedTouches[0].clientY : 0);

        // Ocultar para ver abajo
        dragItem.style.display = 'none';
        let elemAbajo = document.elementFromPoint(x, y);
        dragItem.style.display = 'flex'; // Restaurar display original

        const slot = elemAbajo ? elemAbajo.closest('#slot-meta') : null;

        if (slot) {
            verificar(dragItem, slot);
        } else {
            resetFicha(dragItem);
        }
        
        dragItem.classList.remove('arrastrando');
        dragItem = null;
    }

    function verificar(ficha, slot) {
        if (ficha.dataset.valor === slot.dataset.target) {
            playFx(true);
            slot.innerHTML = '';
            // Reseteamos estilos para que entre en el slot
            ficha.style.position = 'static';
            ficha.style.width = '100%';
            ficha.style.height = '100%';
            ficha.style.left = ''; 
            ficha.style.top = '';
            ficha.style.margin = '0';
            
            ficha.classList.add('correcta');
            slot.appendChild(ficha);
            slot.classList.add('lleno');

            setTimeout(() => { indice++; cargarNivel(); }, 1000);
        } else {
            resetFicha(ficha);
            playFx(false);
            ficha.classList.add('incorrecta');
            setTimeout(() => { ficha.classList.remove('incorrecta'); }, 500);
        }
    }

    function resetFicha(el) {
        el.classList.remove('arrastrando');
        el.style.position = 'relative';
        el.style.left = '';
        el.style.top = '';
        el.style.width = ''; // Volver al ancho automático
        el.style.height = '';
    }

    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playFx(bien) {
        if(audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator(); const g = audioCtx.createGain();
        osc.connect(g); g.connect(audioCtx.destination);
        osc.frequency.value = bien ? 600 : 150; osc.type = bien ? 'sine' : 'square';
        osc.start(); g.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 0.3);
        osc.stop(audioCtx.currentTime + 0.3);
    }

    cargarNivel();
})();
</script>