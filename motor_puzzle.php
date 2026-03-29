<?php
// Obtenemos la configuración si existe, o forzamos 20 piezas por defecto
$conf = isset($juego['configuracion']) ? json_decode($juego['configuracion'], true) : [];
$piezas_default = isset($conf['piezas']) ? intval($conf['piezas']) : 20; 
?>
<style>
    .super-puzzle-area { 
        display: flex; flex-direction: column; align-items: center; 
        width: 100%; padding: 10px; user-select: none; box-sizing: border-box; 
    }

    /* MENÚ SUPERIOR (HUD) */
    .hud-puzzle { 
        display: flex; justify-content: space-between; width: 100%; max-width: 400px; 
        background: white; padding: 10px 20px; border-radius: 50px; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 15px; 
        align-items: center; box-sizing: border-box; border: 2px solid #eee; 
    }
    .btn-cambiar { 
        background: var(--color-primario); color: white; border: none; 
        padding: 8px 15px; border-radius: 20px; font-weight: bold; cursor: pointer; 
        transition: transform 0.2s;
    }
    .btn-cambiar:active { transform: scale(0.95); }

    .selector-dificultad { 
        display: none; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; 
        justify-content: center; background: white; padding: 15px; 
        border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
        border: 2px dashed #92A8D1; max-width: 400px; animation: fadeIn 0.3s;
    }
    .btn-dif { 
        padding: 10px 20px; border: 2px solid #92A8D1; background: white; 
        color: #92A8D1; border-radius: 10px; font-weight: bold; cursor: pointer; 
        transition: all 0.2s;
    }
    .btn-dif.activa { background: #92A8D1; color: white; box-shadow: 0 4px 0 #7a8fb8; }

    /* TABLERO (DONDE CAEN LAS PIEZAS) */
    .tablero-super { 
        display: grid; width: 100%; max-width: 400px; aspect-ratio: 1/1; 
        border: 4px solid #88B04B; border-radius: 10px; background: rgba(0,0,0,0.03); 
        box-sizing: border-box; overflow: hidden; box-shadow: inset 0 0 20px rgba(0,0,0,0.1); 
    }
    .celda-super { border: 1px dashed rgba(0,0,0,0.1); display: flex; justify-content: center; align-items: center; }
    
    /* ORIGEN (DONDE ESPERAN LAS PIEZAS) */
    .piezas-origen { 
        display: flex; flex-wrap: wrap; justify-content: center; gap: 5px; 
        margin-top: 20px; max-width: 450px; min-height: 120px; padding: 10px; 
        background: #fdfbf7; border-radius: 15px; border: 2px solid #eee; 
    }

    /* PIEZAS INDIVIDUALES */
    .super-pieza { 
        cursor: grab; box-shadow: 0 3px 8px rgba(0,0,0,0.3); touch-action: none; 
        border-radius: 3px; position: relative; z-index: 50; 
    }
    .super-pieza.arrastrando { 
        opacity: 0.8; transform: scale(1.1); box-shadow: 0 15px 30px rgba(0,0,0,0.4); 
        z-index: 1000 !important; cursor: grabbing !important; 
    }
    .super-pieza.colocada { 
        border: none !important; box-shadow: none !important; border-radius: 0; 
        pointer-events: none; width: 100% !important; height: 100% !important; 
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="super-puzzle-area">
    
    <div class="hud-puzzle">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fa-solid fa-puzzle-piece" style="color:var(--color-primario); font-size:1.5rem;"></i>
            <span id="lbl-dif" style="font-weight:900; color:#555; font-size:1.1rem;">Cargando...</span>
        </div>
        <button class="btn-cambiar" onclick="toggleSelector()">Cambiar</button>
    </div>

    <div class="selector-dificultad" id="selector-dif">
        <button class="btn-dif" onclick="armarPuzzle(9)">9</button>
        <button class="btn-dif" onclick="armarPuzzle(12)">12</button>
        <button class="btn-dif" onclick="armarPuzzle(16)">16</button>
        <button class="btn-dif" onclick="armarPuzzle(20)">20</button>
        <button class="btn-dif" onclick="armarPuzzle(30)">30</button>
        <button class="btn-dif" onclick="armarPuzzle(48)">48</button>
    </div>

    <div class="tablero-super" id="tablero"></div>
    <div class="piezas-origen" id="origen"></div>

</div>

<script>
(function() {
    const imagenSrc = "<?php echo $juego['imagen_portada']; ?>";
    let piezasActuales = <?php echo $piezas_default; ?>;
    
    const tablero = document.getElementById('tablero');
    const origen = document.getElementById('origen');
    const selector = document.getElementById('selector-dif');
    let aciertos = 0;

    // Mapa lógico de cuadrículas (Columnas x Filas)
    const configuraciones = {
        9: { c: 3, r: 3 },
        12: { c: 4, r: 3 },
        16: { c: 4, r: 4 },
        20: { c: 5, r: 4 },
        30: { c: 6, r: 5 },
        48: { c: 8, r: 6 }
    };

    window.toggleSelector = function() {
        selector.style.display = selector.style.display === 'flex' ? 'none' : 'flex';
    };

    window.armarPuzzle = function(cantidad) {
        piezasActuales = cantidad;
        aciertos = 0;
        selector.style.display = 'none';
        document.getElementById('lbl-dif').innerText = cantidad + " Piezas";
        tablero.innerHTML = '';
        origen.innerHTML = '';

        // Si la cantidad no está en el mapa, forzamos la más cercana (20 por seguridad)
        let config = configuraciones[cantidad] || configuraciones[20];
        let cols = config.c;
        let rows = config.r;

        tablero.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
        tablero.style.gridTemplateRows = `repeat(${rows}, 1fr)`;

        let total = cols * rows;
        let arrayPiezas = [];

        for (let i = 0; i < total; i++) {
            // Dibujar las celdas vacías del tablero
            const celda = document.createElement('div');
            celda.className = 'celda-super';
            celda.dataset.pos = i;
            tablero.appendChild(celda);
            arrayPiezas.push(i);
        }

        // Mezclar el orden en el que aparecen las piezas abajo
        arrayPiezas.sort(() => Math.random() - 0.5);

        // Achicar las piezas en el contenedor si son muchísimas
        let sizePiezaAbajo = cantidad > 20 ? 45 : 60;

        arrayPiezas.forEach(i => {
            const pieza = document.createElement('div');
            pieza.className = 'super-pieza';
            pieza.dataset.id = i;

            // Matemática pura para el corte automático
            let col = i % cols;
            let row = Math.floor(i / cols);

            pieza.style.backgroundImage = `url('${imagenSrc}')`;
            pieza.style.backgroundSize = `${cols * 100}% ${rows * 100}%`;
            
            // Posicionar la textura sin que se rompa
            let posX = cols > 1 ? (col / (cols - 1)) * 100 : 0;
            let posY = rows > 1 ? (row / (rows - 1)) * 100 : 0;
            pieza.style.backgroundPosition = `${posX}% ${posY}%`;

            // Darle tamaño físico para poder agarrarla
            pieza.style.width = `${sizePiezaAbajo}px`;
            pieza.style.height = `${sizePiezaAbajo * (rows/cols)}px`;

            origen.appendChild(pieza);
            activarDrag(pieza);
        });

        // Marcar el botón visualmente
        document.querySelectorAll('.btn-dif').forEach(b => {
            b.classList.remove('activa');
            if(parseInt(b.innerText) === cantidad) b.classList.add('activa');
        });
    };

    function activarDrag(el) {
        let startX, startY;

        const start = (e) => {
            e.preventDefault();
            const touch = e.touches ? e.touches[0] : e;
            startX = touch.clientX;
            startY = touch.clientY;

            const rect = el.getBoundingClientRect();
            // Clonamos el tamaño visual para que no colapse
            let currentWidth = rect.width;
            let currentHeight = rect.height;

            el.style.position = 'fixed';
            el.style.left = rect.left + 'px';
            el.style.top = rect.top + 'px';
            el.style.width = currentWidth + 'px';
            el.style.height = currentHeight + 'px';
            el.classList.add('arrastrando');
        };

        const move = (e) => {
            if (!el.classList.contains('arrastrando')) return;
            e.preventDefault();
            const touch = e.touches ? e.touches[0] : e;
            const dx = touch.clientX - startX;
            const dy = touch.clientY - startY;
            el.style.transform = `translate(${dx}px, ${dy}px) scale(1.1)`;
        };

        const end = (e) => {
            if (!el.classList.contains('arrastrando')) return;
            el.classList.remove('arrastrando');

            const rect = el.getBoundingClientRect();
            const centroX = rect.left + rect.width / 2;
            const centroY = rect.top + rect.height / 2;

            // Escondemos la pieza un milisegundo para ver el tablero que quedó abajo
            el.style.display = 'none';
            let elemAbajo = document.elementFromPoint(centroX, centroY);
            el.style.display = 'block';

            let zonaDestino = elemAbajo ? elemAbajo.closest('.celda-super') : null;

            if (zonaDestino) {
                const zonaId = parseInt(zonaDestino.dataset.pos);
                const piezaId = parseInt(el.dataset.id);

                if (zonaId === piezaId && zonaDestino.children.length === 0) {
                    // ¡CORRECTO!
                    el.style.position = 'relative';
                    el.style.left = '0';
                    el.style.top = '0';
                    el.style.transform = 'none';
                    el.classList.add('colocada');

                    zonaDestino.appendChild(el);
                    aciertos++;
                    playFx(true);

                    if (aciertos === piezasActuales) {
                        setTimeout(mostrarVictoria, 500);
                    }
                    return;
                }
            }

            // Falló, vuelve a la base
            playFx(false);
            el.style.position = 'relative';
            el.style.left = 'auto';
            el.style.top = 'auto';
            el.style.transform = 'none';
        };

        el.addEventListener('mousedown', start);
        window.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);
        el.addEventListener('touchstart', start, {passive: false});
        window.addEventListener('touchmove', move, {passive: false});
        window.addEventListener('touchend', end);
    }

    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playFx(bien) {
        if(audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator(); const g = audioCtx.createGain();
        osc.connect(g); g.connect(audioCtx.destination);
        if(bien) {
            osc.frequency.setValueAtTime(600, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime+0.1);
            osc.type='sine';
        } else {
            osc.frequency.setValueAtTime(150, audioCtx.currentTime);
            osc.frequency.linearRampToValueAtTime(100, audioCtx.currentTime+0.1);
            osc.type='sawtooth';
        }
        g.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime+0.2);
        osc.start(); osc.stop(audioCtx.currentTime+0.2);
    }

    function mostrarVictoria() {
        document.querySelector('.super-puzzle-area').innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px; width: 100%; box-sizing: border-box; position: fixed; top: 0; left: 0; height: 100%; background: rgba(255,255,255,0.95); z-index: 10000;">
                <div style="background: white; padding: 30px 15px; border-radius: 30px; box-shadow: 0 10px 30px rgba(146, 168, 209, 0.4); text-align: center; border: 6px solid #FFD700; width: 100%; max-width: 400px; box-sizing: border-box;">
                    <i class="fa-solid fa-trophy" style="color:#FFD700; font-size:4rem;"></i>
                    <h2 style="font-size: 2rem; color: #92A8D1; margin: 10px 0 0 0; font-weight: 900;">¡Excelente! 🌟</h2>
                    <p style="font-size: 1.1rem; color: #666; margin: 10px 0 20px 0;">¡Rompecabezas armado a la perfección!</p>
                    <button onclick="location.reload()" style="display: block; width: 100%; padding: 15px; border-radius: 50px; background: #88B04B; color: white; font-size: 1.1rem; font-weight: 800; border: none; cursor: pointer; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(136, 176, 75, 0.4); box-sizing: border-box;"><i class="fa-solid fa-rotate-right"></i> Jugar otra vez</button>
                    <a href="juegos.php" style="display: block; width: 100%; padding: 15px; border-radius: 50px; background: #f0f0f0; color: #666; font-size: 1rem; font-weight: 700; border: none; cursor: pointer; text-decoration: none; box-sizing: border-box;"><i class="fa-solid fa-house"></i> Volver al menú</a>
                </div>
            </div>
        `;
    }

    // Inicializar todo al arrancar
    setTimeout(() => armarPuzzle(piezasActuales), 100);

})();
</script>