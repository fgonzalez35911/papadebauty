<style>
    .mapa-container { position: relative; width: 100%; max-width: 500px; margin: 0 auto; background: #e6f4f9; border-radius: 20px; padding: 20px; }
    
    /* El Mapa SVG se adapta al ancho */
    svg { width: 100%; height: auto; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.1)); }
    
    /* Provincias interactivas */
    .provincia { fill: #fff; stroke: #88B04B; stroke-width: 1; cursor: pointer; transition: fill 0.3s; }
    .provincia:hover { fill: #88B04B; }

    /* Puntos interactivos (Círculos sobre el mapa) */
    .punto-geo {
        position: absolute; width: 20px; height: 20px; background: #FF6B6B;
        border: 2px solid white; border-radius: 50%; cursor: pointer;
        animation: latido 1.5s infinite;
    }
    @keyframes latido { 0%{transform:scale(1)} 50%{transform:scale(1.3)} 100%{transform:scale(1)} }

    /* MODAL */
    #info-modal {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center;
    }
    .info-card {
        background: white; width: 90%; max-width: 400px; border-radius: 20px;
        padding: 20px; text-align: center; position: relative;
    }
    .info-img { width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 15px; background: #eee; }
    .btn-cerrar {
        position: absolute; top: 10px; right: 10px; background: #FF6B6B; color: white;
        border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;
    }
</style>

<h2 style="text-align:center; color: var(--color-primario);">Toca los puntos rojos</h2>

<div class="mapa-container">
    <svg viewBox="0 0 300 600">
        <path d="M100,20 L120,20 L140,50 L130,100 L150,120 L140,200 L120,300 L130,400 L110,550 L90,580 L70,550 L80,400 L60,300 L70,200 L60,100 L80,50 Z" fill="white" stroke="#92A8D1" stroke-width="2"/>
    </svg>
    
    <div class="punto-geo" style="top: 10%; left: 35%;" 
         onclick="mostrarInfo('Norte Argentino', 'Cerro de los 7 Colores', 'https://media.istockphoto.com/id/1344698667/es/foto/colina-de-los-siete-colores-purmamarca-jujuy-argentina.jpg?s=612x612&w=0&k=20&c=w2e5Zq4yqXy_Zq4yqXy_Zq4yqXy_Zq4yqXy=', 'Llama')"></div>

    <div class="punto-geo" style="top: 15%; left: 55%;" 
         onclick="mostrarInfo('Cataratas del Iguazú', 'Misiones: Agua grande', 'https://media.istockphoto.com/id/497556684/es/foto/cataratas-del-iguaz%C3%fa.jpg?s=612x612&w=0&k=20&c=4yqXy_Zq4yqXy_Zq4yqXy_Zq4yqXy_Zq4yqXy=', 'Yaguareté')"></div>

    <div class="punto-geo" style="top: 45%; left: 45%;" 
         onclick="mostrarInfo('Buenos Aires', 'El Obelisco y la Ciudad', 'https://media.istockphoto.com/id/1160957733/es/foto/obelisco-de-buenos-aires-argentina.jpg?s=612x612&w=0&k=20&c=4yqXy_Zq4yqXy_Zq4yqXy=', 'Hornero')"></div>

    <div class="punto-geo" style="top: 80%; left: 30%;" 
         onclick="mostrarInfo('Patagonia', 'Glaciares y Frío', 'https://media.istockphoto.com/id/521976196/es/foto/glaciar-perito-moreno.jpg?s=612x612&w=0&k=20&c=4yqXy_Zq4yqXy=', 'Pingüino')"></div>
</div>

<div id="info-modal">
    <div class="info-card">
        <button class="btn-cerrar" onclick="cerrarInfo()">X</button>
        <img id="modal-img" class="info-img" src="">
        <h2 id="modal-titulo" style="color: var(--color-secundario); margin-bottom: 5px;"></h2>
        <p id="modal-desc" style="color: #666; margin-bottom: 20px;"></p>
        
        <div style="background: #f9f9f9; padding: 10px; border-radius: 10px;">
            <i class="fa-solid fa-paw" style="color: #88B04B;"></i> Fauna: <strong id="modal-animal"></strong>
        </div>
        
        <button onclick="leerTexto()" class="btn-grande btn-jugar" style="margin-top: 15px; font-size: 1rem;">
            <i class="fa-solid fa-volume-high"></i> Escuchar
        </button>
    </div>
</div>

<script>
    function mostrarInfo(titulo, desc, img, animal) {
        document.getElementById('modal-titulo').innerText = titulo;
        document.getElementById('modal-desc').innerText = desc;
        // Usamos imagen real o un color si falla
        document.getElementById('modal-img').src = img; 
        document.getElementById('modal-animal').innerText = animal;
        document.getElementById('info-modal').style.display = 'flex';
    }

    function cerrarInfo() {
        document.getElementById('info-modal').style.display = 'none';
        window.speechSynthesis.cancel();
    }

    function leerTexto() {
        const t = document.getElementById('modal-titulo').innerText;
        const d = document.getElementById('modal-desc').innerText;
        const msg = new SpeechSynthesisUtterance(t + ". " + d);
        window.speechSynthesis.speak(msg);
    }
</script>