<style>
    .lenguaje-area { display: flex; flex-direction: column; align-items: center; width: 100%; }
    .contenedor-imagen-juego { 
        width: 300px; height: 300px; background: white; border-radius: 20px; 
        padding: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;
        display: flex; align-items: center; justify-content: center; border: 3px solid #eee;
    }
    .img-fluida { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 15px; }
    
    .grid-botones { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; width: 100%; max-width: 600px; }
    .btn-opcion {
        padding: 25px; font-size: 1.4rem; border: 2px solid #eee; border-radius: 15px;
        background: white; color: #555; font-weight: bold; cursor: pointer;
        box-shadow: 0 5px 0 #ddd; transition: 0.1s; text-transform: uppercase;
    }
    .btn-opcion:active { transform: translateY(5px); box-shadow: none; }
    .btn-opcion.correcto { background: #88B04B; color: white; border-color: #6a8f3d; box-shadow: 0 5px 0 #4e6b2c; }
    .btn-opcion.incorrecto { background: #FF6B6B; color: white; opacity: 0.5; border-color: #d32f2f; }
    
    #pantalla-final { text-align: center; padding: 50px; display: none; }
</style>

<div class="lenguaje-area" id="pantalla-juego">
    <h3 style="margin-bottom: 20px; color: #888;">¿Qué es esto?</h3>
    
    <div class="contenedor-imagen-juego">
        <img id="imagen-principal" src="" class="img-fluida" alt="Imagen a adivinar">
    </div>
    
    <div id="contenedor-botones" class="grid-botones">
        </div>

    <div id="feedback-positivo" style="display:none; margin-top:25px; color:#88B04B; font-size:2rem; font-weight:800;">
        ¡Excelente! 🎉
    </div>
</div>

<div id="pantalla-final" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.95); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; padding: 15px; box-sizing: border-box;">
    <div style="background: white; padding: 30px 15px; border-radius: 30px; box-shadow: 0 10px 30px rgba(146, 168, 209, 0.4); text-align: center; border: 6px solid #FFD700; width: 100%; max-width: 400px; box-sizing: border-box;">
        <i class="fa-solid fa-trophy" style="color:#FFD700; font-size:4rem;"></i>
        <h2 style="font-size: 2rem; color: #92A8D1; margin: 10px 0 0 0; font-weight: 900;">¡Excelente! 🌟</h2>
        <p style="font-size: 1.1rem; color: #666; margin: 10px 0 20px 0;">¡Lo hiciste muy bien!</p>
        <button onclick="location.reload()" style="display: block; width: 100%; padding: 15px; border-radius: 50px; background: #88B04B; color: white; font-size: 1.1rem; font-weight: 800; border: none; cursor: pointer; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(136, 176, 75, 0.4); box-sizing: border-box;"><i class="fa-solid fa-rotate-right"></i> Jugar otra vez</button>
        <a href="juegos.php" style="display: block; width: 100%; padding: 15px; border-radius: 50px; background: #f0f0f0; color: #666; font-size: 1rem; font-weight: 700; border: none; cursor: pointer; text-decoration: none; box-sizing: border-box;"><i class="fa-solid fa-house"></i> Volver al menú</a>
    </div>
</div>

<script>
(function() {
    // 1. Verificamos si hay contenido cargado manualmente
    if (!contenidoJuego || contenidoJuego.length === 0) {
        document.getElementById('pantalla-juego').innerHTML = 
            "<h3 style='color:red; text-align:center;'>Este juego aún no tiene contenido cargado.</h3><p style='text-align:center;'>Entrá al panel de admin para agregar imágenes.</p>";
        return;
    }

    let indiceActual = 0;
    let bloqueado = false;

    const imgEl = document.getElementById('imagen-principal');
    const btnsEl = document.getElementById('contenedor-botones');
    const feedbackEl = document.getElementById('feedback-positivo');
    const pantallaJuego = document.getElementById('pantalla-juego');
    const pantallaFinal = document.getElementById('pantalla-final');

    function cargarDiapositiva() {
        if (indiceActual >= contenidoJuego.length) {
            mostrarFinal();
            return;
        }

        bloqueado = false;
        feedbackEl.style.display = 'none';
        btnsEl.innerHTML = '';
        
        const datos = contenidoJuego[indiceActual];

        // Cargar Imagen (se usa la ruta guardada en BD)
        imgEl.src = datos.imagen;
        imgEl.onerror = function() { this.src = 'https://placehold.co/300x300/eee/999?text=Error+Imagen'; };

        // Preparar opciones (Correcta + Distractores)
        let opciones = [
            { texto: datos.palabra_correcta, esCorrecta: true },
            { texto: datos.distractor1, esCorrecta: false },
            { texto: datos.distractor2, esCorrecta: false },
            { texto: datos.distractor3, esCorrecta: false }
        ];
        
        // Mezclar opciones aleatoriamente
        opciones.sort(() => Math.random() - 0.5);

        // Crear botones
        opciones.forEach(opcion => {
            const btn = document.createElement('button');
            btn.className = 'btn-opcion';
            btn.innerText = opcion.texto;
            btn.onclick = () => verificar(btn, opcion);
            btnsEl.appendChild(btn);
        });
    }

    function verificar(btn, opcion) {
        if (bloqueado) return;

        if (opcion.esCorrecta) {
            bloqueado = true;
            btn.classList.add('correcto');
            feedbackEl.style.display = 'block';
            
            // Intentar leer la palabra
            if ('speechSynthesis' in window) {
                const voz = new SpeechSynthesisUtterance(opcion.texto);
                voz.lang = 'es-ES';
                window.speechSynthesis.speak(voz);
            }

            setTimeout(() => {
                indiceActual++;
                cargarDiapositiva();
            }, 1500);
        } else {
            btn.classList.add('incorrecto');
            // Opcional: hacer vibrar el celular si es incorrecto
            if (navigator.vibrate) navigator.vibrate(200);
        }
    }

    function mostrarFinal() {
        pantallaJuego.style.display = 'none';
        pantallaFinal.style.display = 'block';
        if (navigator.vibrate) navigator.vibrate([100, 50, 100, 50, 300]);
    }

    // Iniciar el juego
    cargarDiapositiva();
})();
</script>