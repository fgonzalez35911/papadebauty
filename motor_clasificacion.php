<style>
    .zona-juego-clasif { display: flex; flex-direction: column; align-items: center; width: 100%; overflow: hidden; }
    .instruccion { font-size: 1.2rem; color: #666; margin-bottom: 20px; text-align: center; }
    
    /* CANASTAS */
    .zona-canastas { display: flex; gap: 20px; width: 100%; justify-content: center; margin-bottom: 30px; }
    .canasta {
        width: 150px; height: 150px; border: 4px dashed #ccc; border-radius: 15px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        background: #f9f9f9; transition: all 0.3s;
    }
    .canasta.highlight { background: #e3f2fd; border-color: #2196F3; transform: scale(1.05); }
    .canasta-titulo { font-weight: bold; color: #555; margin-bottom: 10px; pointer-events: none; }
    .canasta-icon { font-size: 3rem; color: #ddd; pointer-events: none; }

    /* ITEM A ARRASTRAR */
    .item-draggable {
        width: 100px; height: 100px; background: white; border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2); cursor: grab;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; color: #444; touch-action: none; position: relative; z-index: 100;
        border: 2px solid #eee;
    }
    .item-draggable:active { cursor: grabbing; transform: scale(1.1); }
</style>

<div class="zona-juego-clasif">
    <h2 style="color: var(--color-primario);">¡A ordenar!</h2>
    <p class="instruccion">Arrastra cada objeto a su lugar correcto</p>

    <div class="zona-canastas" id="contenedor-canastas">
        </div>

    <div id="zona-spawn" style="height: 120px; display: flex; align-items: center; justify-content: center;">
        </div>
</div>

<script>
(function() {
    // DATOS DESDE PHP
    const datosJuego = <?php echo json_encode($juego['data']); ?>;
    // Estructura esperada: { grupos: ['Frutas', 'Verduras'], items: [['manzana', 'Frutas'], ...] }
    
    const contenedorCanastas = document.getElementById('contenedor-canastas');
    const zonaSpawn = document.getElementById('zona-spawn');
    
    // Mapeo simple de iconos para texto
    const iconMap = {
        'manzana': 'fa-apple-whole', 'banana': 'fa-carrot', // usar iconos genéricos si no hay
        'corazon': 'fa-heart', 'sol': 'fa-sun',
        'perro': 'fa-dog', 'gato': 'fa-cat', 'pez': 'fa-fish'
    };
    
    let itemActual = null;

    // 1. DIBUJAR CANASTAS
    datosJuego.grupos.forEach(grupo => {
        const div = document.createElement('div');
        div.className = 'canasta';
        div.dataset.grupo = grupo;
        div.innerHTML = `
            <div class="canasta-titulo">${grupo}</div>
            <i class="fa-solid fa-box-open canasta-icon"></i>
        `;
        contenedorCanastas.appendChild(div);
    });

    // 2. GENERAR ITEM
    function nuevoItem() {
        zonaSpawn.innerHTML = '';
        const random = datosJuego.items[Math.floor(Math.random() * datosJuego.items.length)];
        const nombre = random[0];
        const grupoCorrecto = random[1];

        const el = document.createElement('div');
        el.className = 'item-draggable';
        // Si tenemos icono en fontawesome lo usamos, sino texto
        if(iconMap[nombre]) {
            el.innerHTML = `<i class="fa-solid ${iconMap[nombre]}"></i>`;
        } else {
            el.innerText = nombre;
            el.style.fontSize = "1rem";
            el.style.fontWeight = "bold";
        }
        
        el.dataset.grupo = grupoCorrecto;
        
        zonaSpawn.appendChild(el);
        activarDrag(el);
    }

    // 3. LOGICA DRAG & DROP (Tactil y Mouse)
    function activarDrag(el) {
        let startX, startY, initialLeft, initialTop;

        const start = (e) => {
            const touch = e.touches ? e.touches[0] : e;
            const rect = el.getBoundingClientRect();
            
            // Fix para posición absoluta visual
            el.style.position = 'fixed';
            el.style.left = rect.left + 'px';
            el.style.top = rect.top + 'px';
            
            startX = touch.clientX;
            startY = touch.clientY;
        };

        const move = (e) => {
            if (el.style.position !== 'fixed') return;
            e.preventDefault(); // Evitar scroll
            const touch = e.touches ? e.touches[0] : e;
            const dx = touch.clientX - startX;
            const dy = touch.clientY - startY;
            el.style.transform = `translate(${dx}px, ${dy}px)`;
        };

        const end = (e) => {
            if (el.style.position !== 'fixed') return;
            
            el.style.display = 'none'; // Ocultar para ver qué hay abajo
            const touch = e.changedTouches ? e.changedTouches[0] : e;
            const elemBelow = document.elementFromPoint(touch.clientX, touch.clientY);
            el.style.display = 'flex';

            const canasta = elemBelow ? elemBelow.closest('.canasta') : null;

            if (canasta && canasta.dataset.grupo === el.dataset.grupo) {
                // CORRECTO
                canasta.style.borderColor = "#88B04B";
                canasta.style.transform = "scale(1.1)";
                setTimeout(() => {
                    canasta.style.borderColor = "#ccc";
                    canasta.style.transform = "scale(1)";
                }, 500);
                
                // Sonido Bien
                const audio = new AudioContext();
                const osc = audio.createOscillator();
                const g = audio.createGain();
                osc.connect(g); g.connect(audio.destination);
                osc.frequency.setValueAtTime(440, audio.currentTime);
                osc.start(); osc.stop(audio.currentTime + 0.1);

                nuevoItem();
            } else {
                // INCORRECTO - Volver
                el.style.position = 'relative';
                el.style.left = 'auto';
                el.style.top = 'auto';
                el.style.transform = 'none';
            }
        };

        el.addEventListener('mousedown', start);
        window.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);
        el.addEventListener('touchstart', start, {passive: false});
        window.addEventListener('touchmove', move, {passive: false});
        window.addEventListener('touchend', end);
    }

    nuevoItem();
})();
</script>
