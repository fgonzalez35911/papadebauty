<style>
    /* === ESTILOS GENERALES (Base Escritorio) === */
    .motor-area { 
        width: 100%; max-width: 800px; margin: 0 auto; 
        text-align: center; user-select: none; position: relative; 
        padding-bottom: 10px; /* Espacio para el botón siguiente en escritorio */
    }
    
    .instruccion-juego {
        font-size: 1.8rem; font-weight: 900; color: var(--color-primario);
        margin: 0 0 30px 0; text-shadow: 2px 2px 0px #fff;
        background: rgba(255,255,255,0.9); display: inline-block;
        padding: 10px 30px; border-radius: 50px;
        border: 3px solid var(--color-secundario);
        transition: opacity 0.3s; position: relative; z-index: 10;
        font-family: 'Nunito', sans-serif;
    }

    /* === JUEGO SELECCIÓN === */
    .caja-imagen-forzada {
        width: 100%; max-width: 400px; 
        height: 350px; /* Altura fija para escritorio */
        margin: 0 auto 30px;
        background-color: #fff; border: 4px solid #eee; border-radius: 20px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1); position: relative; z-index: 5;
        background-size: contain; background-position: center; background-repeat: no-repeat;
        transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .grid-respuestas { 
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px; 
        max-width: 700px; margin: 0 auto; position: relative; z-index: 5;
        transition: opacity 0.5s;
    }
    
    .btn-opcion {
        padding: 15px; background: white; border: 3px solid #e0e0e0; border-radius: 20px; 
        cursor: pointer; color: #555; box-shadow: 0 5px 0 #ccc; 
        transition: transform 0.1s, border-color 0.2s, opacity 0.3s; 
        position: relative; overflow: hidden;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;
        min-height: 120px; /* Altura mínima escritorio */
    }
    .btn-opcion:active { transform: translateY(4px); box-shadow: none; }
    .btn-opcion.ok { background: #88B04B; color: white; border-color: #557530; box-shadow: 0 5px 0 #3e5722; }
    .btn-opcion.error { background: #FF6B6B; color: white; border-color: #d32f2f; animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 75% { transform: translateX(10px); } }

    .img-opcion {
        width: 80px; height: 80px; /* Tamaño escritorio */
        object-fit: contain; border-radius: 10px; pointer-events: none;
    }
    
    .texto-opcion { 
        font-size: 1.3rem; font-weight: 800; text-transform: uppercase; color: #555;
        font-family: 'Nunito', sans-serif;
    }
    
    /* IMAGEN CLONADA (LA QUE VUELA) */
    .img-voladora {
        position: absolute; 
        z-index: 2000; border-radius: 20px; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        object-fit: contain; background: white; border: 4px solid #88B04B;
    }

    /* TEXTO DE REFUERZO (NOMBRE GIGANTE) */
    #texto-refuerzo {
        position: relative !important; 
        margin: 10px auto;
        transform: scale(0);
        font-size: 2.5rem; font-weight: 900; color: #444; 
        z-index: 2001; transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        opacity: 0; background: rgba(255,255,255,0.9); padding: 15px 40px; border-radius: 50px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #88B04B;
        white-space: nowrap; font-family: 'Nunito', sans-serif;
        max-width: 90%; text-align: center; width: fit-content;
    }
    #texto-refuerzo.visible { transform: scale(1) !important; opacity: 1; display: block; }

    /* BOTÓN SIGUIENTE (CONTROL MANUAL) */
    #btn-siguiente {
        display: none; 
        position: relative !important; 
        margin: 20px auto 40px auto;
        background: var(--color-primario); color: white; border: none;
        padding: 15px 50px; font-size: 1.5rem; font-weight: 800; border-radius: 50px;
        cursor: pointer; box-shadow: 0 10px 20px rgba(146, 168, 209, 0.4);
        z-index: 3000; animation: palpitarNuevo 1.5s infinite;
        font-family: 'Nunito', sans-serif; text-align: center;
    }
    @keyframes palpitarNuevo { 0%{transform: scale(1);} 50%{transform: scale(1.05);} 100%{transform: scale(1);} }

    
    /* ESTILOS RESTANTES */
    .btn-audio-float { position: absolute; bottom: 15px; right: 15px; width: 50px; height: 50px; border-radius: 50%; background: #92A8D1; color: white; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.2); cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; transition: transform 0.2s; }
    .btn-audio-float:hover { transform: scale(1.1); }
    .flash-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; opacity: 0; pointer-events: none; z-index: 9999; transition: opacity 0.2s ease-out; }
    .flash-active { opacity: 0.8; }
    
    /* PANTALLA FINAL */
    #pantalla-victoria { display: none; flex-direction: column; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 5000; animation: entradaSuave 0.5s ease-out; }
    .tarjeta-ganador { background: white; padding: 40px; border-radius: 40px; box-shadow: 0 20px 50px rgba(146, 168, 209, 0.4); text-align: center; border: 6px solid #FFD700; max-width: 90%; width: 450px; position: relative; z-index: 5001; }
    .titulo-ganador { font-size: 2.5rem; color: var(--color-primario); margin: 0; font-weight: 900; }
    .subtitulo-ganador { font-size: 1.2rem; color: #666; margin: 10px 0 30px 0; }
    .btn-reiniciar { display: block; width: 100%; padding: 18px; border-radius: 50px; background: #88B04B; color: white; font-size: 1.2rem; font-weight: 800; border: none; cursor: pointer; text-decoration: none; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(136, 176, 75, 0.4); transition: transform 0.2s; margin-top:20px; }
    .btn-reiniciar:hover { transform: scale(1.05); }
    .btn-salir { display: block; width: 100%; padding: 15px; border-radius: 50px; background: #f0f0f0; color: #666; font-size: 1rem; font-weight: 700; border: none; cursor: pointer; text-decoration: none; }
    #confeti-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 5000; }
    @keyframes entradaSuave { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

    /* MEMORIA Y SECUENCIA */
    .grid-memoria { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; max-width: 600px; margin: 0 auto; perspective: 1000px; }
    .carta { aspect-ratio: 1 / 1; cursor: pointer; position: relative; transform-style: preserve-3d; transition: transform 0.6s; }
    .carta.girada { transform: rotateY(180deg); }
    .carta-inner { position: relative; width: 100%; height: 100%; text-align: center; transition: transform 0.6s; transform-style: preserve-3d; }
    .cara { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border-radius: 15px; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
    .frente { background: #92A8D1; color: white; font-size: 2.5rem; }
    .dorso { background: white; transform: rotateY(180deg); border: 3px solid #92A8D1; padding: 10px; }
    .dorso img { width: 100%; height: 100%; object-fit: contain; }
    .zona-drop { display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap; min-height: 140px; padding: 20px; background: #eef4fa; border-radius: 20px; border: 3px dashed #ccc; }
    .slot { width: 110px; height: 110px; border: 3px solid #ddd; border-radius: 15px; background: rgba(255,255,255,0.7); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #ccc; font-weight: 900; position: relative; }
    .zona-fichas { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
    .ficha { width: 110px; height: 110px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); cursor: grab; padding: 5px; touch-action: none; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px solid #eee; }
    .ficha img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; pointer-events: none; }
    .ficha.arrastrando { opacity: 0.6; transform: scale(1.1); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }

    /* =========================================
       MEDIA QUERIES (ADAPTACIÓN CELULAR)
       ========================================= */
    @media (max-width: 768px) {
        /* Ajustes generales de espacio */
        .motor-area { padding-bottom: 100px; } /* Menos espacio abajo */
        .instruccion-juego { font-size: 1.3rem; padding: 10px 20px; margin-bottom: 15px; }

        /* JUEGO: Achicar la imagen principal para que entren las opciones */
        .caja-imagen-forzada {
            height: 180px !important; /* Mucho más petisa */
            margin-bottom: 15px;
        }
        .caja-imagen-forzada i { font-size: 3rem !important; } /* Ícono más chico si no hay foto */

        /* JUEGO: Opciones más compactas */
        .grid-respuestas { gap: 10px; }
        .btn-opcion {
            min-height: 90px; /* Botones más petisos */
            padding: 8px;
        }
        .img-opcion { width: 50px; height: 50px; } /* Fotos de opciones más chicas */
        .texto-opcion { font-size: 1rem; }

        /* ANIMACIÓN FINAL EN CELULAR (Formato Vertical) */
        .motor-area { padding-bottom: 20px; } 
        #texto-refuerzo {
            font-size: 1.5rem !important;
            white-space: normal !important;
        }
        #btn-siguiente {
            padding: 10px 30px !important;
            font-size: 1.2rem !important;
        }

        /* Otros juegos */

        /* Otros juegos */
        .grid-memoria { grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .slot, .ficha { width: 80px; height: 80px; font-size: 1.8rem; }
    }
</style>

<div id="flash-effect" class="flash-overlay"></div>
<div id="titulo-instruccion" class="instruccion-juego"></div>

<div id="lienzo-juego" class="motor-area"></div>

<div id="texto-refuerzo"></div>
<button id="btn-siguiente" onclick="avanzarNivel()">SIGUIENTE <i class="fa-solid fa-arrow-right"></i></button>

<audio id="reproductor-oculto"></audio>

<div id="pantalla-victoria">
    <canvas id="confeti-canvas"></canvas>
    <div class="tarjeta-ganador">
        <i class="fa-solid fa-trophy icono-copa" style="color:#FFD700; font-size:5rem;"></i>
        <h2 class="titulo-ganador">¡Excelente! 🌟</h2>
        <p class="subtitulo-ganador">¡Lo hiciste muy bien!</p>
        <button onclick="location.reload()" class="btn-reiniciar"><i class="fa-solid fa-rotate-right"></i> Jugar otra vez</button>
        <a href="juegos.php" class="btn-salir"><i class="fa-solid fa-house"></i> Volver al menú</a>
    </div>
</div>

<script>
(function() {
    const lienzo = document.getElementById('lienzo-juego');
    const tituloHtml = document.getElementById('titulo-instruccion');
    const audioPlayer = document.getElementById('reproductor-oculto');
    const pantallaVictoria = document.getElementById('pantalla-victoria');
    const flashDiv = document.getElementById('flash-effect');
    const textoRefuerzo = document.getElementById('texto-refuerzo');
    const btnSiguiente = document.getElementById('btn-siguiente');
    
    const datos = <?php echo json_encode($contenido_manual); ?>;
    const tipo = "<?php echo $juego['tipo_juego']; ?>"; 
    const tituloDefault = "<?php echo !empty($juego['instruccion_jugador']) ? $juego['instruccion_jugador'] : '¡A Jugar!'; ?>";
    
    // Detectar si es celular para cambiar la animación
    const esCelular = window.innerWidth <= 768;

    if(datos.length === 0 && tipo !== 'pintura') {
        lienzo.innerHTML = "<h3 style='color:#ccc; margin-top:50px;'>Falta cargar contenido en el panel.</h3>";
        tituloHtml.style.display = 'none';
        return;
    }

    let indice = 0;
    let bloqueado = false; 

    // --- ENRUTADOR ---
    if (tipo === 'seleccion') initSeleccion();
    else if (tipo === 'memoria') initMemoria();
    else if (tipo === 'secuencia') initSecuencia();
    else if (tipo === 'pintura') lienzo.innerHTML = "<h3>(Modo Pintura)</h3>"; 

    // FUNCIÓN MANUAL PARA AVANZAR
    window.avanzarNivel = function() {
        textoRefuerzo.classList.remove('visible');
        btnSiguiente.style.display = 'none';
        const voladores = document.querySelectorAll('.img-voladora');
        voladores.forEach(v => v.remove());
        const caja = document.getElementById('caja-principal');
        if(caja) { caja.style.transform = "none"; caja.style.opacity = "1"; }
        
        document.getElementById('lienzo-juego').style.height = 'auto'; // Resetea la altura
        
        indice++;
        initSeleccion();
    };

    /* =========================================
       MOTOR SELECCIÓN (Con Animación Adaptable)
       ========================================= */
    function initSeleccion() {
        if(indice >= datos.length) { finJuego(); return; }
        bloqueado = false;
        const item = datos[indice];
        
        tituloHtml.innerText = item.texto_pregunta ? item.texto_pregunta : tituloDefault;
        tituloHtml.style.opacity = '1';
        tituloHtml.style.display = 'inline-block';

        const grid = document.getElementById('grid-opciones');
        if(grid) grid.style.opacity = '1';

        if(item.audio && item.audio.length > 3) {
            audioPlayer.src = item.audio + "?t=" + Math.random();
            if(!item.imagen) setTimeout(() => playAudio(), 500);
        } else audioPlayer.src = "";

        // Imagen Central
        let estiloFondo = `background-color: #f8f9fa; display:flex; align-items:center; justify-content:center;`;
        let contenidoFondo = `<i class="fa-solid fa-image" style="font-size:4rem; color:#e0e0e0;"></i>`;
        if(item.imagen && item.imagen.length > 3) {
            let ruta = item.imagen;
            if(ruta.indexOf('http') === -1 && ruta.indexOf('assets') === -1) ruta = '../' + ruta;
            estiloFondo = `background-image: url('${ruta}?t=${Math.random()}');`; 
            contenidoFondo = ``; 
        }

        let ops = [
            {t: item.palabra_correcta, img: item.img_correcta, ok: true}, 
            {t: item.distractor1, img: item.img_distractor1, ok: false},
            {t: item.distractor2, img: item.img_distractor2, ok: false}, 
            {t: item.distractor3, img: item.img_distractor3, ok: false}
        ].filter(op => (op.t && op.t.trim() !== "") || (op.img && op.img.length > 3)).sort(() => Math.random() - 0.5);

        let btnAudioHtml = (item.audio) ? `<button onclick="playAudio()" class="btn-audio-float"><i class="fa-solid fa-volume-high"></i></button>` : '';

        lienzo.innerHTML = `
            <div id="caja-principal" class="caja-imagen-forzada" style="${estiloFondo}">
                ${contenidoFondo}
                ${btnAudioHtml}
            </div>
            <div class="grid-respuestas" id="grid-opciones">
                ${ops.map((o, i) => {
                    let rutaImg = o.img;
                    if(rutaImg && rutaImg.indexOf('http') === -1 && rutaImg.indexOf('assets') === -1) rutaImg = '../' + rutaImg;
                    let imgHtml = o.img ? `<img src="${rutaImg}" class="img-opcion" id="img-opt-${i}">` : '';
                    let txtHtml = o.t ? `<span class="texto-opcion">${o.t}</span>` : '';
                    return `<button id="btn-opt-${i}" class="btn-opcion" onclick="checkSel(this, ${o.ok}, ${i}, '${o.t}')">${imgHtml}${txtHtml}</button>`;
                }).join('')}
            </div>
        `;
    }

    window.checkSel = (btn, ok, idBtn, textoRespuesta) => {
        if(bloqueado) return;
        
        if(ok) { 
            bloqueado = true;
            btn.classList.add('ok'); 
            playFx(true);
            
            const cajaPrincipal = document.getElementById('caja-principal');
            const imgOpcion = document.getElementById(`img-opt-${idBtn}`);
            
            document.getElementById('grid-opciones').style.opacity = '0'; 
            setTimeout(() => { 
                document.getElementById('grid-opciones').style.display = 'none'; 
                // Esto mantiene la caja firme para que aloje la animación y empuje el texto abajo
                document.getElementById('lienzo-juego').style.height = esCelular ? '400px' : '350px';
            }, 400); 

            // --- LÓGICA DE ANIMACIÓN SEGÚN DISPOSITIVO ---
            // --- LÓGICA DE ANIMACIÓN SEGÚN DISPOSITIVO ---
            let destinoTopClon, destinoLeftClon, destinoWidthClon, destinoHeightClon;

            if(esCelular) {
                // === MODO CELULAR (Vertical) ===
                // 1. La caja principal sube un poquito y se achica
                cajaPrincipal.style.transform = "translate(0, -10px) scale(0.9)"; 
                
                // 2. Destino del clon: DEBAJO de la caja principal
                destinoTopClon = "200px"; // Justo debajo de la caja achicada
                destinoLeftClon = "10%"; // Centrado (con margen)
                destinoWidthClon = "80%"; 
                destinoHeightClon = "180px";
                
            } else {
                // === MODO ESCRITORIO (Lado a Lado) ===
                // 1. La caja principal se mueve a la izquierda
                cajaPrincipal.style.transform = "translate(-50%, -20px) scale(0.85)"; 
                
                // 2. Destino del clon: A la DERECHA
                destinoTopClon = "0px"; 
                destinoLeftClon = "52%"; 
                destinoWidthClon = "40%"; 
                destinoHeightClon = "300px";
            }

            // --- CREAR Y MOVER CLON ---
            if (imgOpcion) {
                const rectImg = imgOpcion.getBoundingClientRect();
                const rectContenedor = lienzo.getBoundingClientRect(); 
                const clon = imgOpcion.cloneNode(true);
                clon.className = 'img-voladora'; 
                clon.style.top = (rectImg.top - rectContenedor.top) + 'px';
                clon.style.left = (rectImg.left - rectContenedor.left) + 'px';
                clon.style.width = rectImg.width + 'px';
                clon.style.height = rectImg.height + 'px';
                lienzo.appendChild(clon); 
                clon.offsetWidth; // Reflow

                // Aplicar destino calculado arriba
                clon.style.top = destinoTopClon;
                clon.style.left = destinoLeftClon;
                clon.style.width = destinoWidthClon;
                clon.style.height = destinoHeightClon;
            }

            // Mostrar Texto y Botón
            textoRefuerzo.innerText = textoRespuesta;
            textoRefuerzo.classList.add('visible');
            setTimeout(() => { btnSiguiente.style.display = 'block'; }, 800);
            setTimeout(() => { flashDiv.classList.add('flash-active'); }, 500);
            setTimeout(() => { flashDiv.classList.remove('flash-active'); }, 1000);

        } else { 
            btn.classList.add('error'); playFx(false); if (navigator.vibrate) navigator.vibrate(200);
        }
    }
    
    window.playAudio = () => { if(audioPlayer.src) audioPlayer.play().catch(e=>{}); }

    // --- OTROS MOTORES (Sin cambios de lógica, solo CSS responsive) ---
    function initMemoria() {
        tituloHtml.innerText = tituloDefault;
        let cartas = []; datos.forEach(d => { if(d.imagen) { cartas.push({id: d.id, img: d.imagen}); cartas.push({id: d.id, img: d.imagen}); } });
        if(cartas.length == 0) { lienzo.innerHTML = "<h3>Falta imágenes</h3>"; return; }
        cartas.sort(() => Math.random() - 0.5); 
        let html = `<div class="grid-memoria" style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px;">`;
        cartas.forEach((c, i) => { let r = c.img; if(r.indexOf('http')==-1 && r.indexOf('assets')==-1) r = '../'+r; html += `<div class="carta" id="carta-${i}" onclick="girar(${i}, '${c.id}')"><div class="carta-inner"><div class="cara frente"><i class="fa-solid fa-star" style="color:white;"></i></div><div class="cara dorso"><img src="${r}"></div></div></div>`; });
        html += `</div>`; lienzo.innerHTML = html;
    }
    let volteadas=[], pares=0, block=false;
    window.girar = (i,id) => { if(block) return; let el = document.getElementById(`carta-${i}`).querySelector('.carta-inner'); if(el.style.transform) return; el.style.transform = 'rotateY(180deg)'; volteadas.push({i,id}); if(volteadas.length==2) { block=true; if(volteadas[0].id==volteadas[1].id) { playFx(true); pares++; volteadas=[]; block=false; if(pares==datos.length) setTimeout(finJuego,1000); } else { playFx(false); setTimeout(()=>{ document.getElementById(`carta-${volteadas[0].i}`).querySelector('.carta-inner').style.transform=''; document.getElementById(`carta-${volteadas[1].i}`).querySelector('.carta-inner').style.transform=''; volteadas=[]; block=false; },1000); } } }

    function initSecuencia() { 
        let html = `<div class="zona-drop">`; datos.forEach((d, i) => { html += `<div class="slot" id="slot-${i}" data-correcto="${d.id}">${i+1}</div>`; }); html += `</div><div class="zona-fichas">`; let fichas = [...datos].sort(() => Math.random() - 0.5); fichas.forEach(f => { let ruta = f.imagen; if(ruta.indexOf('http')==-1 && ruta.indexOf('assets')==-1) ruta = '../'+ruta; html += `<div class="ficha" id="ficha-${f.id}" data-id="${f.id}" draggable="true"><img src="${ruta}"><small style="font-size:0.7rem; margin-top:2px;">${f.palabra_correcta || ''}</small></div>`; }); html += `</div><div style="text-align:center;"><button class="btn-grande btn-jugar" style="margin-top:30px; padding:15px 40px;" onclick="validarSecuencia()">Comprobar</button></div>`; lienzo.innerHTML = html; activarDragDrop();
    }
    function activarDragDrop() { const fichas = document.querySelectorAll('.ficha'); const slots = document.querySelectorAll('.slot'); let fichaArrastrada = null; fichas.forEach(f => { f.addEventListener('dragstart', (e) => { fichaArrastrada = f; setTimeout(()=>f.classList.add('arrastrando'),0); }); f.addEventListener('dragend', () => { f.classList.remove('arrastrando'); fichaArrastrada=null; }); f.addEventListener('touchstart', (e) => { fichaArrastrada = f; f.classList.add('arrastrando'); }); f.addEventListener('touchend', (e) => { f.classList.remove('arrastrando'); let touch = e.changedTouches[0]; let elem = document.elementFromPoint(touch.clientX, touch.clientY); let slot = elem ? elem.closest('.slot') : null; if(slot && slot.children.length === 0) slot.appendChild(f); else document.querySelector('.zona-fichas').appendChild(f); }); }); slots.forEach(s => { s.addEventListener('dragover', (e) => e.preventDefault()); s.addEventListener('drop', () => { if(s.children.length === 0 && fichaArrastrada) s.appendChild(fichaArrastrada); }); }); }
    window.validarSecuencia = () => { let slots = document.querySelectorAll('.slot'); let aciertos = 0; slots.forEach(slot => { if(slot.children.length > 0) { if(slot.children[0].dataset.id == slot.dataset.correcto) { slot.style.borderColor = "#88B04B"; aciertos++; } else { slot.style.borderColor = "#FF6B6B"; } } }); if(aciertos === slots.length) { playFx(true); setTimeout(finJuego, 1000); } else playFx(false); }

    // --- UTILIDADES ---
    function finJuego() { tituloHtml.style.display = 'none'; lienzo.innerHTML = ''; pantallaVictoria.style.display = 'flex'; lanzarConfeti(); playVictorySound(); }
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playFx(bien) { if(audioCtx.state === 'suspended') audioCtx.resume(); const osc = audioCtx.createOscillator(); const g = audioCtx.createGain(); osc.connect(g); g.connect(audioCtx.destination); if(bien) { osc.frequency.setValueAtTime(500, audioCtx.currentTime); osc.frequency.exponentialRampToValueAtTime(1000, audioCtx.currentTime+0.1); osc.type='sine'; } else { osc.frequency.setValueAtTime(150, audioCtx.currentTime); osc.frequency.linearRampToValueAtTime(100, audioCtx.currentTime+0.2); osc.type='sawtooth'; } g.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime+0.3); osc.start(); osc.stop(audioCtx.currentTime+0.3); }
    function playVictorySound() { if(audioCtx.state === 'suspended') audioCtx.resume(); const now = audioCtx.currentTime; [261.6, 329.6, 392.0, 523.2, 659.2].forEach((freq, i) => { const osc = audioCtx.createOscillator(); const g = audioCtx.createGain(); osc.frequency.value = freq; osc.connect(g); g.connect(audioCtx.destination); osc.start(now + i*0.1); g.gain.exponentialRampToValueAtTime(0.0001, now + i*0.1 + 0.8); osc.stop(now + i*0.1 + 0.8); }); }
    function lanzarConfeti() { const canvas = document.getElementById('confeti-canvas'); const ctx = canvas.getContext('2d'); canvas.width = window.innerWidth; canvas.height = window.innerHeight; const col = ['#FF6B6B', '#4ECDC4', '#FFE66D', '#FF9F43']; let p = []; for(let i=0; i<150; i++) p.push({x:Math.random()*canvas.width, y:Math.random()*canvas.height-canvas.height, color:col[Math.floor(Math.random()*col.length)], d:Math.random()*5+2}); function draw() { ctx.clearRect(0,0,canvas.width,canvas.height); p.forEach((pt,i) => { pt.y+=pt.d; ctx.fillStyle=pt.color; ctx.fillRect(pt.x,pt.y,8,8); if(pt.y>canvas.height) pt.y=-10; }); requestAnimationFrame(draw); } draw(); }
})();
</script>