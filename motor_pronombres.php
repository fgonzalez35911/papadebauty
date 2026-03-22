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
            document.querySelector('.pronombre-wrapper').innerHTML = "<h2>¡Excelente trabajo!</h2><button onclick='location.reload()'>Repetir</button>";
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
        
        // Imágenes corregidas (sin el ../)
        const sujetoImg = data.imagen_extra ? data.imagen_extra.toLowerCase() : "default";
        document.getElementById('img-sujeto').src = "assets/img/sujetos/" + sujetoImg + ".png";
        document.getElementById('img-objeto').src = data.imagen || "assets/img/iconos/box.png";
        document.getElementById('txt-sujeto').innerText = data.imagen_extra || "DUEÑO";

        // Mezclar opciones
        let ops = [data.palabra_correcta, data.distractor1, data.distractor2, data.distractor3]
                   .filter(o => o && o.trim() !== "").sort(() => Math.random() - 0.5);

        grid.innerHTML = "";
        ops.forEach(txt => {
            const btn = document.createElement('button');
            btn.className = 'btn-pronombre';
            btn.innerText = txt;
            btn.onclick = () => {
                if(txt === data.palabra_correcta) {
                    btn.classList.add('correct');
                    setTimeout(() => { index++; loadLevel(); }, 1000);
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