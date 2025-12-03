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

<div id="pantalla-final">
    <i class="fa-solid fa-trophy" style="font-size: 5rem; color: #FFD700; margin-bottom: 20px;"></i>
    <h2 style="font-size: 2.5rem; color: var(--color-primario);">¡Juego Terminado!</h2>
    <p style="font-size: 1.2rem; color: #666;">¡Lo hiciste muy bien!</p>
    <a href="juegos.php" class="btn-grande" style="margin-top: 20px;">Volver al Menú</a>
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