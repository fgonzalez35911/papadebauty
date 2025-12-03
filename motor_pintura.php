<style>
    /* MODO PANTALLA COMPLETA */
    :fullscreen {
        background-color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px;
    }
    
    /* Mensaje de rotación (oculto por defecto) */
    #aviso-rotar {
        display: none;
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.9); z-index: 9999;
        color: white; flex-direction: column; align-items: center; justify-content: center;
        text-align: center;
    }
    
    /* Ajustes de la barra de herramientas */
    .barra-herramientas {
        display: flex; gap: 10px; margin-bottom: 10px;
        justify-content: center; align-items: center; flex-wrap: wrap;
        background: #f9f9f9; padding: 10px 20px; border-radius: 50px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        max-width: 100%;
    }

    .color-btn {
        width: 35px; height: 35px; border-radius: 50%;
        border: 2px solid white; cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        transition: transform 0.2s;
    }
    .color-btn:hover, .color-btn.activo { transform: scale(1.2); border-color: #333; }

    /* Inputs especiales */
    input[type="color"] { width: 40px; height: 40px; border: none; background: none; cursor: pointer; }
    input[type="range"] { width: 100px; cursor: pointer; }

    .accion-btn {
        padding: 8px 15px; border-radius: 20px; border: none;
        font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 5px;
        background: #eee; color: #555; font-size: 0.9rem;
    }
    .btn-fullscreen { background: var(--color-primario); color: white; }
    .btn-borrar { background: #FF6B6B; color: white; }
    .btn-guardar { background: #92A8D1; color: white; }

    #lienzo-pintura {
        background-color: white;
        border: 2px dashed #ccc; border-radius: 15px;
        touch-action: none; cursor: crosshair;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.05);
    }
</style>

<div id="aviso-rotar">
    <i class="fa-solid fa-mobile-screen-button fa-rotate-270" style="font-size: 4rem; margin-bottom: 20px;"></i>
    <h2>¡Gira tu celular!</h2>
    <p>Para dibujar mejor, poné la pantalla en horizontal.</p>
    <button class="accion-btn" onclick="cerrarAvisoRotar()" style="margin-top: 20px; background: white; color: #333;">Listo</button>
</div>

<div id="app-pintura" style="width:100%; display:flex; flex-direction:column; align-items:center;">
    
    <div class="barra-herramientas">
        <button class="accion-btn btn-fullscreen" onclick="toggleFullScreen()" title="Pantalla Completa">
            <i class="fa-solid fa-expand"></i> <span style="display:none; @media(min-width:600px){display:inline;}">Full</span>
        </button>

        <button class="accion-btn btn-guardar" onclick="guardarDibujo()" title="Guardar en tu dispositivo">
            <i class="fa-solid fa-download"></i> <span style="display:none; @media(min-width:600px){display:inline;}">Guardar</span>
        </button>

        <div class="color-btn activo" style="background:#4A4A4A" data-color="#4A4A4A"></div>
        <div class="color-btn" style="background:#88B04B" data-color="#88B04B"></div>
        <div class="color-btn" style="background:#92A8D1" data-color="#92A8D1"></div>
        
        <input type="color" id="color-picker" value="#FF0000" title="Más colores">

        <i class="fa-solid fa-circle" style="font-size: 0.6rem; color:#ccc; margin-left: 10px;"></i>
        <input type="range" id="grosor" min="2" max="30" value="5">
        
        <button class="accion-btn btn-borrar" onclick="borrarLienzo()" title="Borrar todo">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>

    <canvas id="lienzo-pintura"></canvas>

</div>

<script>
(function() {
    const contenedorApp = document.getElementById('app-pintura');
    const canvas = document.getElementById('lienzo-pintura');
    const ctx = canvas.getContext('2d');
    const avisoRotar = document.getElementById('aviso-rotar');
    
    let pintando = false;
    let colorActual = '#4A4A4A';
    let grosorActual = 5;

    // --- 1. AJUSTAR TAMAÑO Y ORIENTACIÓN ---
    function resizeCanvas() {
        let anchoDisponible, altoDisponible;

        if (document.fullscreenElement) {
            anchoDisponible = window.innerWidth;
            altoDisponible = window.innerHeight;
            
            // Si está en vertical (Portrait) en fullscreen, sugerimos rotar
            if (window.innerWidth < window.innerHeight && /Mobi|Android/i.test(navigator.userAgent)) {
                avisoRotar.style.display = 'flex';
            } else {
                avisoRotar.style.display = 'none';
            }

            canvas.width = anchoDisponible - 20;
            canvas.height = altoDisponible - 80; // Restar barra herramientas
        } else {
            // Modo normal dentro de la web
            anchoDisponible = contenedorApp.parentElement.clientWidth;
            canvas.width = anchoDisponible - 20;
            canvas.height = 450;
            avisoRotar.style.display = 'none';
        }
        
        // Restaurar contexto (al redimensionar se pierde)
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.lineWidth = grosorActual;
        ctx.strokeStyle = colorActual;
        
        // Fondo blanco (para que al guardar no sea transparente)
        ctx.fillStyle = "white";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }
    
    // Ejecutar al inicio y al cambiar tamaño
    setTimeout(resizeCanvas, 100);
    window.addEventListener('resize', resizeCanvas);
    
    // --- 2. PANTALLA COMPLETA Y ROTACIÓN ---
    window.toggleFullScreen = function() {
        if (!document.fullscreenElement) {
            contenedorApp.requestFullscreen().then(() => {
                // Intentar bloquear orientación en Landscape (Horizontal)
                if (screen.orientation && screen.orientation.lock) {
                    screen.orientation.lock('landscape').catch(err => {
                        console.log('El dispositivo no permite bloquear rotación automática.');
                    });
                }
            }).catch(err => {
                alert(`Error: ${err.message}`);
            });
        } else {
            if (screen.orientation && screen.orientation.unlock) {
                screen.orientation.unlock();
            }
            document.exitFullscreen();
        }
    }
    
    document.addEventListener('fullscreenchange', resizeCanvas);
    
    window.cerrarAvisoRotar = function() {
        avisoRotar.style.display = 'none';
    }

    // --- 3. DIBUJAR ---
    function empezarDibujo(e) {
        pintando = true;
        dibujar(e);
    }
    function terminarDibujo() {
        pintando = false;
        ctx.beginPath();
    }
    function dibujar(e) {
        if (!pintando) return;
        e.preventDefault();
        
        const rect = canvas.getBoundingClientRect();
        let x, y;
        
        if(e.touches) {
            x = e.touches[0].clientX - rect.left;
            y = e.touches[0].clientY - rect.top;
        } else {
            x = e.clientX - rect.left;
            y = e.clientY - rect.top;
        }

        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    canvas.addEventListener('mousedown', empezarDibujo);
    canvas.addEventListener('mouseup', terminarDibujo);
    canvas.addEventListener('mousemove', dibujar);
    canvas.addEventListener('touchstart', empezarDibujo, {passive: false});
    canvas.addEventListener('touchend', terminarDibujo);
    canvas.addEventListener('touchmove', dibujar, {passive: false});

    // --- 4. HERRAMIENTAS ---
    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('activo'));
            btn.classList.add('activo');
            colorActual = btn.getAttribute('data-color');
            ctx.strokeStyle = colorActual;
        });
    });

    document.getElementById('color-picker').addEventListener('input', (e) => {
        colorActual = e.target.value;
        ctx.strokeStyle = colorActual;
    });

    document.getElementById('grosor').addEventListener('input', (e) => {
        grosorActual = e.target.value;
        ctx.lineWidth = grosorActual;
    });

    window.borrarLienzo = function() {
        if(confirm('¿Borrar dibujo?')) {
            ctx.fillStyle = "white";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
    };

    // --- 5. GUARDAR DIBUJO ---
    window.guardarDibujo = function() {
        // Crear un enlace temporal
        const link = document.createElement('a');
        // Poner nombre al archivo con la fecha
        const fecha = new Date().toISOString().slice(0,10);
        link.download = `dibujo-bauti-${fecha}.png`;
        // Convertir canvas a imagen
        link.href = canvas.toDataURL("image/png");
        link.click();
    };

})();
</script>