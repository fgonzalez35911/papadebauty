<style>
    .motor-area { width: 100%; max-width: 800px; margin: 0 auto; text-align: center; user-select: none; }
    
    /* --- ESTILOS SELECCIÓN (Ya conocidos) --- */
    .caja-imagen-forzada {
        width: 100%; max-width: 400px; height: 400px; margin: 0 auto 30px;
        background-color: #fff; border: 4px solid #eee; border-radius: 20px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1); position: relative;
        background-size: 100% 100%; background-position: center; background-repeat: no-repeat;
        image-rendering: pixelated;
    }
    .btn-audio-manual {
        position: absolute; bottom: 15px; right: 15px; width: 60px; height: 60px; border-radius: 50%;
        background: rgba(255,255,255,0.95); border: 3px solid #92A8D1; color: #92A8D1;
        font-size: 2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 100;
    }
    .grid-respuestas { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .btn-opcion {
        padding: 20px; font-size: 1.2rem; background: white; border: 2px solid #e0e0e0; border-radius: 15px; 
        cursor: pointer; font-weight: 800; color: #555; box-shadow: 0 5px 0 #ccc; text-transform: uppercase;
    }
    .btn-opcion:active { transform: translateY(4px); box-shadow: none; }
    .btn-opcion.ok { background: #88B04B; color: white; border-color: #6a8f3d; }
    .btn-opcion.error { background: #FF6B6B; color: white; opacity: 0.5; }

    /* --- ESTILOS MEMORIA (NUEVO) --- */
    .grid-memoria { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; perspective: 1000px; }
    .carta { 
        aspect-ratio: 1 / 1; background: transparent; cursor: pointer; position: relative;
        transform-style: preserve-3d; transition: transform 0.6s;
    }
    .carta.girada { transform: rotateY(180deg); }
    .cara {
        position: absolute; width: 100%; height: 100%; backface-visibility: hidden;
        border-radius: 10px; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .frente { background: #92A8D1; color: white; font-size: 2rem; }
    .dorso { 
        background: white; transform: rotateY(180deg); border: 2px solid #92A8D1; padding: 5px; 
    }
    .dorso img { width: 100%; height: 100%; object-fit: contain; }

    /* --- ESTILOS SECUENCIA (NUEVO) --- */
    .zona-drop { 
        display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; flex-wrap: wrap;
        min-height: 120px; padding: 10px; background: #eef4fa; border-radius: 15px; border: 2px dashed #ccc;
    }
    .slot { 
        width: 100px; height: 100px; border: 2px solid #ddd; border-radius: 10px; background: rgba(255,255,255,0.5);
        display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #ccc; font-weight: bold;
        position: relative;
    }
    .zona-fichas { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    .ficha { 
        width: 100px; height: 100px; background: white; border-radius: 10px; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: grab; padding: 5px; touch-action: none; /* Vital para móvil */
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .ficha img { width: 100%; height: 100%; object-fit: cover; border-radius: 5px; pointer-events: none; }
    .ficha.arrastrando { opacity: 0.5; transform: scale(1.1); }

    /* Estilos Móvil */
    @media (max-width: 600px) {
        .grid-memoria { grid-template-columns: repeat(3, 1fr); }
        .slot, .ficha { width: 80px; height: 80px; }
    }
</style>

<div id="lienzo-juego" class="motor-area"></div>
<audio id="reproductor-oculto"></audio>

<script>
(function() {
    const lienzo = document.getElementById('lienzo-juego');
    const audioPlayer = document.getElementById('reproductor-oculto');
    const datos = <?php echo json_encode($contenido_manual); ?>;
    const tipo = "<?php echo $juego['tipo_juego']; ?>"; // seleccion, memoria, secuencia, pintura

    if(datos.length === 0 && tipo !== 'pintura') {
        lienzo.innerHTML = "<h3>Juego vacío. Subí imágenes en el Admin.</h3>";
        return;
    }

    let indice = 0;

    // --- ROUTER DE JUEGOS ---
    if (tipo === 'seleccion') initSeleccion();
    else if (tipo === 'memoria') initMemoria();
    else if (tipo === 'secuencia') initSecuencia();
    else if (tipo === 'pintura') lienzo.innerHTML = "<h3>(Modo Pintura: usar código anterior)</h3>"; 

    /* =========================================
       1. MOTOR SELECCIÓN
       ========================================= */
    function initSeleccion() {
        if(indice >= datos.length) { finJuego(); return; }
        const item = datos[indice];
        
        // Audio
        let btnAudio = '';
        if(item.audio && item.audio.length > 3) {
            audioPlayer.src = item.audio + "?t=" + Math.random();
            btnAudio = `<div class="btn-audio-manual" onclick="playAudio()"><i class="fa-solid fa-volume-high"></i></div>`;
        } else audioPlayer.src = "";

        // Imagen
        let estiloFondo = `background-color: #f0f8ff; display:flex; align-items:center; justify-content:center;`;
        let contenidoFondo = `<i class="fa-solid fa-image" style="font-size:4rem; color:#ccc;"></i>`;
        if(item.imagen && item.imagen.length > 3) {
            estiloFondo = `background-image: url('${item.imagen}?t=${Math.random()}'); background-size: 100% 100%;`; 
            contenidoFondo = ``; 
        }

        let ops = [
            {t: item.palabra_correcta, ok: true}, {t: item.distractor1, ok: false},
            {t: item.distractor2, ok: false}, {t: item.distractor3, ok: false}
        ].sort(() => Math.random() - 0.5);

        lienzo.innerHTML = `
            <div class="caja-imagen-forzada" style="${estiloFondo}">${contenidoFondo}${btnAudio}</div>
            <h3 style="margin-bottom:20px; color:#666;">Selecciona:</h3>
            <div class="grid-respuestas">${ops.map(o => `<button class="btn-opcion" onclick="checkSel(this, ${o.ok})">${o.t}</button>`).join('')}</div>
        `;
    }
    window.checkSel = (btn, ok) => {
        if(ok) { btn.classList.add('ok'); playAudio(); setTimeout(() => { indice++; initSeleccion(); }, 1500); }
        else { btn.classList.add('error'); }
    }
    window.playAudio = () => { if(audioPlayer.src) audioPlayer.play().catch(e=>{}); }

    /* =========================================
       2. MOTOR MEMORIA
       ========================================= */
    function initMemoria() {
        // Duplicar cartas
        let cartas = [];
        datos.forEach(d => {
            // Solo usamos los que tienen imagen
            if(d.imagen) {
                cartas.push({id: d.id, img: d.imagen});
                cartas.push({id: d.id, img: d.imagen});
            }
        });
        cartas.sort(() => Math.random() - 0.5); // Mezclar

        if(cartas.length === 0) { lienzo.innerHTML = "Subí imágenes para jugar Memoria."; return; }

        let html = `<div class="grid-memoria">`;
        cartas.forEach((c, i) => {
            html += `
                <div class="carta" id="carta-${i}" onclick="girar(${i}, '${c.id}')">
                    <div class="carta-inner">
                        <div class="cara frente"><i class="fa-solid fa-question"></i></div>
                        <div class="cara dorso"><img src="${c.img}"></div>
                    </div>
                </div>`;
        });
        html += `</div>`;
        lienzo.innerHTML = html;
    }

    let volteadas = [];
    let paresEncontrados = 0;
    let bloqueo = false;

    window.girar = (i, id) => {
        if(bloqueo) return;
        let el = document.getElementById(`carta-${i}`);
        if(el.classList.contains('girada')) return; // Ya está girada

        el.classList.add('girada');
        volteadas.push({i, id});

        if(volteadas.length === 2) {
            bloqueo = true;
            if(volteadas[0].id === volteadas[1].id) {
                // Coinciden
                paresEncontrados++;
                volteadas = [];
                bloqueo = false;
                // Si terminaron todas (pares == total/2)
                if(paresEncontrados === document.querySelectorAll('.carta').length / 2) setTimeout(finJuego, 1000);
            } else {
                // No coinciden
                setTimeout(() => {
                    document.getElementById(`carta-${volteadas[0].i}`).classList.remove('girada');
                    document.getElementById(`carta-${volteadas[1].i}`).classList.remove('girada');
                    volteadas = [];
                    bloqueo = false;
                }, 1000);
            }
        }
    }

    /* =========================================
       3. MOTOR SECUENCIA (Drag & Drop Móvil)
       ========================================= */
    function initSecuencia() {
        // El orden correcto es el orden en que vienen de la BD (indice 0, 1, 2...)
        let html = `<h3 style="margin-bottom:20px;">Ordena la secuencia</h3>
                    <div class="zona-drop">`;
        
        // Crear slots (lugares vacíos 1, 2, 3...)
        datos.forEach((d, i) => {
            html += `<div class="slot" id="slot-${i}" data-correcto="${d.id}">${i+1}</div>`;
        });
        html += `</div><div class="zona-fichas">`;

        // Crear fichas desordenadas
        let fichas = [...datos].sort(() => Math.random() - 0.5);
        fichas.forEach(f => {
            html += `<div class="ficha" id="ficha-${f.id}" data-id="${f.id}">
                        <img src="${f.imagen}">
                     </div>`;
        });
        html += `</div><button class="btn-grande" style="margin-top:20px;" onclick="validarSecuencia()">Comprobar</button>`;
        lienzo.innerHTML = html;

        // Activar Drag & Drop táctil
        activarDragDrop();
    }

    function activarDragDrop() {
        const fichas = document.querySelectorAll('.ficha');
        const slots = document.querySelectorAll('.slot');
        let fichaArrastrada = null;

        // Eventos para cada ficha
        fichas.forEach(ficha => {
            // Touch (Móvil)
            ficha.addEventListener('touchstart', (e) => {
                fichaArrastrada = ficha;
                ficha.classList.add('arrastrando');
            });
            ficha.addEventListener('touchmove', (e) => {
                e.preventDefault();
                let touch = e.touches[0];
                ficha.style.position = 'fixed';
                ficha.style.left = (touch.clientX - 50) + 'px';
                ficha.style.top = (touch.clientY - 50) + 'px';
                ficha.style.zIndex = 1000;
            });
            ficha.addEventListener('touchend', (e) => {
                ficha.classList.remove('arrastrando');
                ficha.style.position = 'static'; // Volver a normal
                ficha.style.zIndex = 1;
                
                // Ver dónde cayó
                let touch = e.changedTouches[0];
                let elemAbajo = document.elementFromPoint(touch.clientX, touch.clientY);
                let slot = elemAbajo ? elemAbajo.closest('.slot') : null;
                
                if(slot && slot.children.length === 0) { // Si es un slot y está vacío
                    slot.appendChild(ficha); // Mover ficha al slot
                } else {
                    document.querySelector('.zona-fichas').appendChild(ficha); // Volver al inicio
                }
                fichaArrastrada = null;
            });

            // Mouse (PC) - Lógica simplificada similar...
            ficha.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text', e.target.id);
            });
        });

        slots.forEach(slot => {
            slot.addEventListener('dragover', (e) => e.preventDefault());
            slot.addEventListener('drop', (e) => {
                e.preventDefault();
                let id = e.dataTransfer.getData('text');
                let ficha = document.getElementById(id);
                if(slot.children.length === 0) slot.appendChild(ficha);
            });
        });
    }

    window.validarSecuencia = () => {
        let slots = document.querySelectorAll('.slot');
        let aciertos = 0;
        slots.forEach(slot => {
            if(slot.children.length > 0) {
                let ficha = slot.children[0];
                // Si el ID de la ficha coincide con el ID esperado del slot
                if(ficha.dataset.id == slot.dataset.correcto) {
                    slot.style.borderColor = "#88B04B";
                    aciertos++;
                } else {
                    slot.style.borderColor = "#FF6B6B";
                }
            }
        });
        if(aciertos === slots.length) finJuego();
    }

    function finJuego() {
        lienzo.innerHTML = "<h2>¡Felicidades! 🎉</h2><button onclick='location.reload()' class='btn-grande'>Jugar otra vez</button>";
    }

})();
</script>