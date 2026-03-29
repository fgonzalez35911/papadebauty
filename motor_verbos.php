<style>
    .verb-container { text-align: center; font-family: 'Arial', sans-serif; padding: 20px; width: 100%; }
    .verb-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 10px 20px; background: #fff; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .level-badge { background: #92A8D1; color: white; padding: 10px 20px; border-radius: 50px; font-weight: bold; font-size: 1.2rem; }
    .progress-bar { width: 200px; height: 15px; background: #eee; border-radius: 10px; overflow: hidden; margin-left: 15px; }
    .progress-fill { height: 100%; background: #06D6A0; width: 0%; transition: width 0.3s ease; }
    
    .verb-timeline { display: flex; justify-content: center; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
    .time-box { padding: 15px 25px; border-radius: 15px; font-weight: 900; font-size: 1.2rem; opacity: 0.3; transition: all 0.4s ease; border: 3px solid transparent; }
    .time-box.active { opacity: 1; transform: scale(1.1); box-shadow: 0 10px 20px rgba(0,0,0,0.15); border-color: #333; }
    
    .time-ayer { background: #FDEFE8; color: #8A3B00; } 
    .time-hoy { background: #DAF2ED; color: #026B50; } 
    .time-manana { background: #FDF7E5; color: #003F5C; }
    
    .verb-equation { display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap; margin-bottom: 40px; background: #f8f9fa; padding: 30px; border-radius: 20px; box-shadow: inset 0 3px 10px rgba(0,0,0,0.05); }
    .verb-subject { background: #F7CAC9; color: #555; padding: 15px 30px; border-radius: 15px; font-size: 2rem; font-weight: bold; }
    .verb-action { background: white; border: 4px dashed #ccc; padding: 15px 30px; border-radius: 15px; min-width: 180px; font-size: 2rem; font-weight: bold; color: #ccc; transition: all 0.3s; }
    
    .verb-options { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
    .verb-btn { background: white; border: 4px solid #92A8D1; color: #92A8D1; font-size: 1.8rem; font-weight: bold; padding: 15px 40px; border-radius: 50px; cursor: pointer; transition: all 0.2s; box-shadow: 0 5px 0 #8DA2CA; }
    .verb-btn:hover { background: #118AB2; color: white; transform: translateY(-3px); box-shadow: 0 8px 0 #005F80; }
    .verb-btn:active { transform: translateY(3px); box-shadow: 0 2px 0 #005F80; }
    
    .celebration { display: none; font-size: 3rem; color: #06D6A0; margin-top: 20px; animation: bounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .level-up-screen { display: none; flex-direction: column; align-items: center; justify-content: center; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 100; border-radius: 25px; }
    
    @keyframes bounceIn { 0% { transform: scale(0); opacity: 0; } 50% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(1); } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 75% { transform: translateX(10px); } }
    .shake-anim { animation: shake 0.4s ease-in-out; }
    /* FIX CELULARES: Todo compacto para matar el scroll */
    @media (max-width: 768px) {
        .verb-container { padding: 5px; }
        /* Compresión del Nivel y Progreso */
        .verb-header { margin-bottom: 8px; padding: 5px 12px; justify-content: center; gap: 10px; }
        .level-badge { background: #92A8D1; font-size: 0.8rem; padding: 4px 10px; }
        .progress-bar { width: 100px; height: 10px; margin-left: 8px; }
        #progress-text { font-size: 0.8rem; }
        .progress-fill { background: #4ECDC4; }
        
        /* Línea de tiempo compacta */
        .verb-timeline { margin-bottom: 10px; gap: 5px; }
        .time-box { padding: 6px 10px; font-size: 0.8rem; flex: 1; text-align: center; }
        
        /* Ecuación comprimida */
        .verb-equation { margin-bottom: 10px; padding: 15px 8px; gap: 8px; }
        .verb-subject { padding: 8px 15px; font-size: 1.2rem; border-radius: 10px; }
        #verb-infinitive { font-size: 1.2rem !important; padding: 3px 10px !important; }
        .verb-action { padding: 8px 15px; font-size: 1.2rem; min-width: 90px; border-radius: 10px; }
        .fa-plus, .fa-equals { font-size: 0.9rem !important; }
        
        /* Opciones pegadas */
        .verb-options { gap: 6px; }
        .verb-btn { padding: 8px 20px; font-size: 1.1rem; margin: 2px; border-width: 3px; box-shadow: 0 3px 0 #8DA2CA; }
        
        .level-up-screen h1 { font-size: 2rem; }
    }
</style>

<div class="verb-container" style="position:relative;">
    
    <div class="level-up-screen" id="level-up-screen">
        <h1 style="color:#FFB347; font-size:4rem; margin:0;">🌟 ¡NIVEL COMPLETADO! 🌟</h1>
        <p style="font-size:1.5rem; color:#555;">¡Lograste 10 verbos seguidos!</p>
        <button class="verb-btn" onclick="startNextLevel()" style="margin-top:30px; background:#06D6A0; border-color:#026B50; color:white; box-shadow:0 5px 0 #026B50;">Siguiente Nivel <i class="fa-solid fa-arrow-right"></i></button>
    </div>

    <div class="verb-header">
        <div class="level-badge" id="level-badge">NIVEL 1</div>
        <div style="display:flex; align-items:center;">
            <span style="font-weight:bold; color:#888;" id="progress-text">0/10</span>
            <div class="progress-bar"><div class="progress-fill" id="progress-fill"></div></div>
        </div>
    </div>

    <div class="verb-timeline">
        <div class="time-box time-ayer" id="box-ayer"><i class="fa-solid fa-arrow-rotate-left"></i> AYER</div>
        <div class="time-box time-hoy" id="box-hoy"><i class="fa-solid fa-play"></i> HOY</div>
        <div class="time-box time-manana" id="box-manana"><i class="fa-solid fa-arrow-right"></i> MAÑANA</div>
    </div>

    <div class="verb-equation">
        <div class="verb-subject" id="verb-subject">YO</div>
        <i class="fa-solid fa-plus" style="color:#bbb; font-size: 2rem;"></i>
        <div style="display:flex; flex-direction:column; align-items:center;">
            <span style="font-size:1rem; color:#888; text-transform:uppercase;">Acción principal</span>
            <strong id="verb-infinitive" style="font-size:1.8rem; color:#333; background:#e2e2e2; padding:5px 15px; border-radius:10px;">COMER</strong>
        </div>
        <i class="fa-solid fa-equals" style="color:#bbb; font-size: 2rem;"></i>
        <div class="verb-action" id="verb-target">???</div>
    </div>

    <div class="celebration" id="verb-celebration">
        <i class="fa-solid fa-star"></i> ¡MUY BIEN! <i class="fa-solid fa-star"></i>
    </div>

    <div class="verb-options" id="verb-options"></div>
</div>

<script>
    let globalIndex = 0;
    let currentLevel = 1;
    let correctInCurrentLevel = 0;
    const VERBS_PER_LEVEL = 10;

    function initVerbGame() {
        if(globalIndex >= contenidoJuego.length) {
            document.querySelector('.verb-container').innerHTML = `
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

        const item = contenidoJuego[globalIndex];
        
        // UI Reset
        document.getElementById('verb-celebration').style.display = 'none';
        document.getElementById('verb-options').style.display = 'flex';
        const target = document.getElementById('verb-target');
        target.innerText = '???';
        target.style.background = 'white';
        target.style.color = '#ccc';
        target.style.borderColor = '#ccc';
        target.style.transform = 'scale(1)';
        
        // Actualizar UI de progreso
        document.getElementById('level-badge').innerText = 'NIVEL ' + currentLevel;
        document.getElementById('progress-text').innerText = correctInCurrentLevel + '/' + VERBS_PER_LEVEL;
        document.getElementById('progress-fill').style.width = ((correctInCurrentLevel / VERBS_PER_LEVEL) * 100) + '%';
        
        const sujeto = (item.imagen && item.imagen.trim() !== '') ? item.imagen.toUpperCase() : 'YO';
        const tiempo = (item.imagen_extra && item.imagen_extra.trim() !== '') ? item.imagen_extra.toUpperCase() : 'HOY';
        const infinitivo = (item.audio && item.audio.trim() !== '') ? item.audio.toUpperCase() : 'ACCIÓN';

        document.getElementById('verb-subject').innerText = sujeto;
        document.getElementById('verb-infinitive').innerText = infinitivo;

        document.querySelectorAll('.time-box').forEach(el => el.classList.remove('active'));
        if(tiempo.includes('AYER') || tiempo.includes('PASADO')) {
            document.getElementById('box-ayer').classList.add('active');
        } else if(tiempo.includes('MAÑANA') || tiempo.includes('FUTURO')) {
            document.getElementById('box-manana').classList.add('active');
        } else {
            document.getElementById('box-hoy').classList.add('active');
        }

        let options = [item.palabra_correcta, item.distractor1, item.distractor2, item.distractor3];
        options = options.filter(o => o && o.trim() !== '');
        options.sort(() => Math.random() - 0.5);

        const optionsContainer = document.getElementById('verb-options');
        optionsContainer.innerHTML = '';

        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'verb-btn';
            btn.innerText = opt;
            btn.onclick = () => checkVerbAnswer(opt, item.palabra_correcta, btn);
            optionsContainer.appendChild(btn);
        });
    }

    function checkVerbAnswer(selected, correct, btnElement) {
        if(selected === correct) {
            const target = document.getElementById('verb-target');
            target.innerText = correct;
            target.style.background = '#06D6A0';
            target.style.color = 'white';
            target.style.borderColor = '#06D6A0';
            target.style.transform = 'scale(1.1)';
            
            document.getElementById('verb-options').style.display = 'none';
            document.getElementById('verb-celebration').style.display = 'block';

            correctInCurrentLevel++;
            globalIndex++;

            setTimeout(() => {
                if(correctInCurrentLevel >= VERBS_PER_LEVEL && globalIndex < contenidoJuego.length) {
                    document.getElementById('level-up-screen').style.display = 'flex';
                } else {
                    initVerbGame();
                }
            }, 1500);
        } else {
            btnElement.style.background = '#EF476F';
            btnElement.style.color = 'white';
            btnElement.style.borderColor = '#EF476F';
            btnElement.style.boxShadow = '0 5px 0 #B92B4C';
            btnElement.classList.add('shake-anim');
            setTimeout(() => {
                btnElement.style.background = 'white';
                btnElement.style.color = '#118AB2';
                btnElement.style.borderColor = '#118AB2';
                btnElement.style.boxShadow = '0 5px 0 #118AB2';
                btnElement.classList.remove('shake-anim');
            }, 600);
        }
    }

    function startNextLevel() {
        document.getElementById('level-up-screen').style.display = 'none';
        currentLevel++;
        correctInCurrentLevel = 0;
        initVerbGame();
    }

    setTimeout(initVerbGame, 100);
</script>