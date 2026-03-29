<style>
    /* VARIABLES DE TU PALETA REAL */
    :root {
        --c-sujeto: #92A8D1;
        --c-objeto: #DAF2ED;
        --c-relacion: #FDEFE8;
        --c-ok: #4ECDC4;
        --c-error: #F7CAC9;
    }

    .pronombre-wrapper { font-family: 'Nunito', sans-serif; padding: 10px; text-align: center; }
    
    /* ENCABEZADO COMPRIMIDO PARA MÓVIL */
    .header-identidad { display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 15px; }
    .badge-perspectiva { background: var(--c-sujeto); color: white; padding: 5px 15px; border-radius: 50px; font-weight: 800; font-size: 0.9rem; box-shadow: 0 4px 0 #8DA2CA; }

    /* ESCENARIO DE PERSPECTIVA (La clave del Yo/Tú) */
    .escenario-relacion {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        background: #fdfbf7; padding: 20px 10px; border-radius: 25px;
        margin-bottom: 20px; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
        border: 2px dashed #eee;
    }

    .entidad { width: 120px; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .entidad-img { width: 100px; height: 100px; border-radius: 20px; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); object-fit: cover; background: white; }
    
    .label-entidad { font-size: 0.8rem; font-weight: 900; text-transform: uppercase; padding: 3px 10px; border-radius: 10px; width: 100%; }
    .label-sujeto { background: var(--c-sujeto); color: white; }
    .label-objeto { background: var(--c-objeto); color: #557530; }

    .vinculo-central { font-size: 2rem; color: #ccc; animation: pulse 2s infinite; }

    /* BUBBLE DE DIÁLOGO (Instrucción dinámica) */
    .burbuja-habla {
        background: white; border: 3px solid var(--c-sujeto); padding: 12px;
        border-radius: 20px; position: relative; margin-bottom: 25px;
        font-weight: 800; color: #444; font-size: 1.1rem;
    }
    .burbuja-habla::after {
        content: ''; position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%);
        border-width: 15px 15px 0; border-style: solid; border-color: var(--c-sujeto) transparent transparent;
    }

    /* BOTONES DE OPCIÓN (Estilo Píldora) */
    .opciones-pronombres { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; max-width: 500px; margin: 0 auto; }
    .btn-pronombre {
        background: white; border: 3px solid var(--c-sujeto); color: var(--c-sujeto);
        padding: 15px; border-radius: 20px; font-size: 1.4rem; font-weight: 900;
        cursor: pointer; transition: all 0.2s; box-shadow: 0 5px 0 var(--c-sujeto);
    }
    .btn-pronombre:active { transform: translateY(4px); box-shadow: none; }
    .btn-pronombre.correct { background: var(--c-ok); color: white; border-color: #3e9e96; box-shadow: 0 5px 0 #2d7a74; }
    .btn-pronombre.wrong { background: var(--c-error); color: white; border-color: #d1abab; animation: shake 0.4s; }

    @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-5px)} 75%{transform:translateX(5px)} }
    @keyframes pulse { 0%{opacity:0.4} 50%{opacity:1} 100%{opacity:0.4} }

    /* ADAPTACIÓN QUIRÚRGICA MÓVIL */
    @media (max-width: 768px) {
        .entidad-img { width: 80px; height: 80px; }
        .entidad { width: 90px; }
        .vinculo-central { font-size: 1.5rem; }
        .btn-pronombre { font-size: 1.1rem; padding: 12px; }
        .burbuja-habla { font-size: 0.9rem; padding: 8px; }
    }
</style>

<div class="pronombre-wrapper">
    <div class="header-identidad">
        <div class="badge-perspectiva" id="display-nivel">NIVEL 1</div>
        <div id="perspectiva-actual" class="badge-perspectiva" style="background:#F7CAC9;">PERSPECTIVA: CARGANDO</div>
    </div>

    <div class="burbuja-habla" id="instruccion-habla">...</div>

    <div class="escenario-relacion">
        <div class="entidad">
            <img id="img-sujeto" class="entidad-img" src="assets/img/iconos/user.png">
            <span class="label-entidad label-sujeto" id="txt-sujeto">DUEÑO</span>
        </div>
        
        <div class="vinculo-central"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>

        <div class="entidad">
            <img id="img-objeto" class="entidad-img" src="assets/img/iconos/box.png">
            <span class="label-entidad label-objeto">OBJETO</span>
        </div>
    </div>

    <div class="opciones-pronombres" id="grid-pronombres"></div>
</div>

<script>
(function() {
    let index = 0;
    const items = <?php echo json_encode($contenido_manual); ?>;
    
    function loadLevel() {
        if(index >= items.length) {
            document.querySelector('.pronombre-wrapper').innerHTML = `
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px; width: 100%; box-sizing: border-box;">
                    <div style="background: white; padding: 30px 15px; border-radius: 30px; box-shadow: 0 10px 30px rgba(146, 168, 209, 0.4); text-align: center; border: 6px solid #FFD700; width: 100%; max-width: 400px; box-sizing: border-box;">
                        <i class="fa-solid fa-trophy" style="color:#FFD700; font-size:4rem;"></i>
                        <h2 style="font-size: 2rem; color: #92A8D1; margin: 10px 0 0 0; font-weight: 900;">¡Excelente! 🌟</h2>
                        <p style="font-size: 1.1rem; color: #666; margin: 10px 0 20px 0;">¡Lo hiciste muy bien!</p>
                        <button onclick="location.reload()" style="display: block; width: 100%; padding: 15px; border-radius: 50px; background: #88B04B; color: white; font-size: 1.1rem; font-weight: 800; border: none; cursor: pointer; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(136, 176, 75, 0.4); box-sizing: border-box;"><i class="fa-solid fa-rotate-right"></i> Jugar otra vez</button>
                        <a href="juegos.php" style="display: block; width: 100%; padding: 15px; border-radius: 50px; background: #f0f0f0; color: #666; font-size: 1rem; font-weight: 700; border: none; cursor: pointer; text-decoration: none; box-sizing: border-box;"><i class="fa-solid fa-house"></i> Volver al menú</a>
                    </div>
                </div>
            `;
            return;
        }

        const data = items[index];
        const grid = document.getElementById('grid-pronombres');
        
        // Seguro contra textos vacíos
        const txtPregunta = data.texto_pregunta || "";
        const habloYo = txtPregunta.toLowerCase().includes('bauti dice');

        // Interfaz
        document.getElementById('display-nivel').innerText = "PASO " + (index + 1);
        document.getElementById('perspectiva-actual').innerText = habloYo ? "HABLA BAUTI" : "HABLA PAPÁ";
        document.getElementById('perspectiva-actual').style.background = habloYo ? "#92A8D1" : "#F7CAC9";
        document.getElementById('instruccion-habla').innerText = txtPregunta;
        
        // Imagen subida desde el panel (Izquierda)
        let imgIzq = data.imagen_extra && data.imagen_extra.includes('assets') ? data.imagen_extra : "assets/img/iconos/user.png";
        document.getElementById('img-sujeto').src = imgIzq.replace('../', '');
        
        // Imagen Central del Objeto (Derecha)
        let imgDer = data.imagen ? data.imagen : "assets/img/iconos/box.png";
        document.getElementById('img-objeto').src = imgDer.replace('../', '');

        // Etiqueta inteligente según la pregunta
        let nombreP = "DUEÑO";
        if(txtPregunta.toLowerCase().includes('bauti')) nombreP = "BAUTI";
        if(txtPregunta.toLowerCase().includes('papá') || txtPregunta.toLowerCase().includes('papa')) nombreP = "PAPÁ";
        document.getElementById('txt-sujeto').innerText = nombreP;

        // Mezclar opciones
        let ops_crudas = [data.palabra_correcta, data.distractor1, data.distractor2, data.distractor3].filter(o => o && o.trim() !== "");
        let ops = ops_crudas.sort((a, b) => a.localeCompare(b));

        grid.innerHTML = "";
        ops.forEach(txt => {
            const btn = document.createElement('button');
            btn.className = 'btn-pronombre';
            btn.innerText = txt;
            btn.onclick = () => {
                if(txt === data.palabra_correcta) {
                    btn.classList.add('correct');
                    
                    // Sonido de acierto rápido (idéntico al de la granja)
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        if(audioCtx.state === 'suspended') audioCtx.resume();
                        const osc = audioCtx.createOscillator(); const g = audioCtx.createGain();
                        osc.connect(g); g.connect(audioCtx.destination);
                        osc.frequency.setValueAtTime(500, audioCtx.currentTime);
                        osc.frequency.exponentialRampToValueAtTime(1000, audioCtx.currentTime+0.1);
                        osc.type='sine'; g.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime+0.3);
                        osc.start(); osc.stop(audioCtx.currentTime+0.3);
                    } catch(e) {}

                    // Pasar rapidísimo a la siguiente (300 milisegundos en lugar de 1000)
                    setTimeout(() => { index++; loadLevel(); }, 300);
                } else {
                    btn.classList.add('wrong');
                    setTimeout(() => btn.classList.remove('wrong'), 500);
                }
            };
            grid.appendChild(btn);
        });
    }

    setTimeout(loadLevel, 100);
})();
</script>