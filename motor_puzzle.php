<style>
    .puzzle-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        user-select: none;
        padding: 20px;
    }

    /* Donde se arma el puzzle */
    .tablero-drop {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 300px;
        height: 300px;
        /* Borde más visible para saber dónde tirar */
        border: 4px dashed #88B04B; 
        background-color: rgba(0,0,0,0.03);
        border-radius: 10px;
    }

    .zona-drop {
        border: 1px solid rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        color: #ccc; /* Color suave para los números */
    }

    /* Las piezas desordenadas */
    .piezas-container {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
        min-height: 160px;
        padding: 10px;
    }

    .pieza {
        width: 148px; /* Un pelín más chico para que entre cómodo */
        height: 148px;
        cursor: grab;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        touch-action: none; /* Vital para celular */
        position: relative;
        border-radius: 5px;
    }

    .pieza.correcta {
        border: none;
        pointer-events: none;
    }
</style>

<div class="puzzle-area">
    <div id="mensaje-puzzle" style="font-weight: bold; color: var(--color-primario); font-size: 1.2rem;">
        Arrastra las piezas a su número
    </div>

    <div class="tablero-drop" id="destino">
        <div class="zona-drop" data-pos="0">1</div>
        <div class="zona-drop" data-pos="1">2</div>
        <div class="zona-drop" data-pos="2">3</div>
        <div class="zona-drop" data-pos="3">4</div>
    </div>

    <div class="piezas-container" id="origen">
        </div>
</div>

<script>
(function() {
    const origen = document.getElementById('origen');
    const mensaje = document.getElementById('mensaje-puzzle');
    
    // Imagen del juego actual
    const imagenSrc = "<?php echo $juego['imagen_portada']; ?>";

    // Creamos las 4 piezas (0, 1, 2, 3)
    let piezas = [0, 1, 2, 3];
    piezas.sort(() => Math.random() - 0.5); // Mezclar

    piezas.forEach(i => {
        const pieza = document.createElement('div');
        pieza.classList.add('pieza');
        pieza.setAttribute('data-id', i);
        
        // Asignar fondo (recorte de la imagen)
        pieza.style.backgroundImage = `url('${imagenSrc}')`;
        pieza.style.backgroundSize = "300px 300px"; 
        
        const col = i % 2;
        const row = Math.floor(i / 2);
        pieza.style.backgroundPosition = `${col * 100}% ${row * 100}%`;

        origen.appendChild(pieza);
        
        // Activar arrastre
        hacerArrastrable(pieza);
    });

    let aciertos = 0;

    function hacerArrastrable(el) {
        let startX, startY;

        const start = (e) => {
            e.preventDefault();
            const touch = e.touches ? e.touches[0] : e;
            startX = touch.clientX;
            startY = touch.clientY;
            
            const rect = el.getBoundingClientRect();
            
            // Preparar para mover
            el.style.position = 'fixed'; 
            el.style.left = rect.left + 'px';
            el.style.top = rect.top + 'px';
            el.style.zIndex = 1000;
            el.style.width = '150px';
            el.style.height = '150px';
            el.style.cursor = 'grabbing';
        };

        const move = (e) => {
            if (el.style.position !== 'fixed') return;
            e.preventDefault();
            const touch = e.touches ? e.touches[0] : e;
            
            const dx = touch.clientX - startX;
            const dy = touch.clientY - startY;

            el.style.transform = `translate(${dx}px, ${dy}px)`;
        };

        const end = (e) => {
            if (el.style.position !== 'fixed') return;
            el.style.zIndex = '';
            el.style.cursor = 'grab';
            
            // Detectar centro de la pieza
            const rect = el.getBoundingClientRect();
            const centroX = rect.left + rect.width / 2;
            const centroY = rect.top + rect.height / 2;
            
            // Ocultar pieza un instante para ver qué hay abajo
            el.style.display = 'none';
            let elementoAbajo = document.elementFromPoint(centroX, centroY);
            el.style.display = 'block';

            // --- AQUÍ ESTÁ LA MAGIA ---
            // Usamos .closest() para encontrar la caja aunque toquemos el número o el borde
            let zonaDestino = null;
            if (elementoAbajo) {
                zonaDestino = elementoAbajo.closest('.zona-drop');
            }

            // Verificamos si cayó en una zona válida
            if (zonaDestino) {
                const zonaId = parseInt(zonaDestino.getAttribute('data-pos'));
                const piezaId = parseInt(el.getAttribute('data-id'));

                // Si el ID coincide y la zona está vacía (o tiene solo texto)
                // Nota: chequeamos que no tenga ya una pieza (class .pieza) adentro
                if (zonaId === piezaId && !zonaDestino.querySelector('.pieza')) {
                    
                    // ¡CORRECTO! PEGAR LA PIEZA
                    el.style.position = 'relative';
                    el.style.left = '0';
                    el.style.top = '0';
                    el.style.transform = 'none';
                    el.classList.add('correcta');
                    
                    // Borrar el número de fondo para que quede limpia la imagen
                    zonaDestino.innerText = ''; 
                    zonaDestino.appendChild(el);
                    zonaDestino.style.border = "none";
                    
                    aciertos++;
                    
                    if (aciertos === 4) {
                        mensaje.innerText = "¡Excelente! 🎉";
                        mensaje.style.fontSize = "2rem";
                    }
                    return; // Salimos, no reseteamos posición
                }
            }

            // Si falló, vuelve al origen
            el.style.position = 'relative';
            el.style.left = 'auto';
            el.style.top = 'auto';
            el.style.transform = 'none';
        };

        // Mouse
        el.addEventListener('mousedown', start);
        window.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);

        // Touch (Celular)
        el.addEventListener('touchstart', start, {passive: false});
        window.addEventListener('touchmove', move, {passive: false});
        window.addEventListener('touchend', end);
    }
})();
</script>