<style>
    /* ESTILOS DEL MEMOTEST */
    .tablero-memoria {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        padding: 20px;
        max-width: 700px; /* Un poco más ancho para que entre el texto */
        margin: 0 auto;
    }

    .carta {
        background-color: transparent;
        aspect-ratio: 3 / 4; /* Un poco más alta que ancha para el texto */
        perspective: 1000px;
        cursor: pointer;
    }

    .carta-inner {
        position: relative;
        width: 100%; height: 100%;
        text-align: center;
        transition: transform 0.6s;
        transform-style: preserve-3d;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        border-radius: 15px;
    }

    .carta.girada .carta-inner { transform: rotateY(180deg); }

    .cara-frente, .cara-atras {
        position: absolute; width: 100%; height: 100%;
        backface-visibility: hidden;
        display: flex; flex-direction: column; /* Para apilar icono y texto */
        align-items: center; justify-content: center;
        border-radius: 15px;
    }

    .cara-frente {
        background-color: var(--color-secundario);
        color: white; font-size: 2.5rem;
        background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);
    }

    .cara-atras {
        background-color: white;
        transform: rotateY(180deg);
        border: 3px solid var(--color-secundario);
    }

    /* TEXTO QUE APARECE AL GIRAR */
    .texto-carta {
        margin-top: 10px;
        font-size: 1rem;
        font-weight: bold;
        color: #444;
        text-transform: uppercase;
    }
</style>

<div id="tablero" class="tablero-memoria"></div>

<div id="mensaje-ganador" style="display:none; text-align:center; margin-top:20px;">
    <h2 style="color: var(--color-primario);">¡Excelente! 🎉</h2>
    <button onclick="location.reload()" class="btn-grande btn-jugar">Jugar otra vez</button>
</div>

<script>
(function() {
    const tablero = document.getElementById('tablero');
    const mensaje = document.getElementById('mensaje-ganador');
    
    const cantidadCartas = gameConfig.cartas || 12; 
    const tema = gameConfig.tema || 'general'; 

    if (cantidadCartas <= 6) tablero.style.gridTemplateColumns = "repeat(3, 1fr)";
    else if (cantidadCartas <= 12) tablero.style.gridTemplateColumns = "repeat(4, 1fr)";
    else tablero.style.gridTemplateColumns = "repeat(5, 1fr)";

    // --- AQUÍ ESTÁ LA MEJORA: AGREGAMOS EL NOMBRE ---
    const recursos = {
        'granja': [
            ['fa-cat', '#FFB347', 'Gato'],
            ['fa-dog', '#88B04B', 'Perro'],
            ['fa-horse', '#964B00', 'Caballo'],
            ['fa-frog', '#4CAF50', 'Rana'],
            ['fa-crow', '#555555', 'Pájaro'],
            ['fa-fish', '#00BFFF', 'Pez']
        ],
        'emociones': [
            ['fa-face-smile', '#FFD700', 'Feliz'],
            ['fa-face-sad-tear', '#4682B4', 'Triste'],
            ['fa-face-angry', '#FF4500', 'Enojado'],
            ['fa-face-surprise', '#DA70D6', 'Sorpresa'],
            ['fa-face-tired', '#808080', 'Cansado'],
            ['fa-face-laugh-beam', '#32CD32', 'Risa']
        ],
        'default': [
            ['fa-star', '#FFD700', 'Estrella'], 
            ['fa-heart', '#FF69B4', 'Corazón'], 
            ['fa-bolt', '#FFA500', 'Rayo'], 
            ['fa-cloud', '#87CEEB', 'Nube']
        ]
    };

    let itemsBase = recursos[tema] || recursos['default'];
    let paresNecesarios = cantidadCartas / 2;
    itemsBase = itemsBase.slice(0, paresNecesarios);
    let mazo = [...itemsBase, ...itemsBase];
    mazo.sort(() => Math.random() - 0.5);

    let cartasVolteadas = [];
    let bloqueoTablero = false;
    let parejasEncontradas = 0;

    mazo.forEach(item => {
        const [icono, color, texto] = item; 
        
        const carta = document.createElement('div');
        carta.classList.add('carta');
        
        carta.innerHTML = `
            <div class="carta-inner">
                <div class="cara-frente"><i class="fa-solid fa-question"></i></div>
                <div class="cara-atras">
                    <i class="fa-solid ${icono}" style="font-size: 3rem; color: ${color};"></i>
                    <div class="texto-carta">${texto}</div>
                </div>
            </div>
        `;

        carta.addEventListener('click', () => {
            if (bloqueoTablero || carta.classList.contains('girada')) return;
            
            // Girar
            carta.classList.add('girada');
            cartasVolteadas.push({ elemento: carta, valor: icono }); // Usamos el icono como ID

            // LEER EN VOZ ALTA (Opcional - Usa el navegador)
            // const utterance = new SpeechSynthesisUtterance(texto);
            // window.speechSynthesis.speak(utterance);

            if (cartasVolteadas.length === 2) {
                bloqueoTablero = true;
                const [c1, c2] = cartasVolteadas;

                if (c1.valor === c2.valor) {
                    parejasEncontradas++;
                    cartasVolteadas = [];
                    bloqueoTablero = false;
                    if (parejasEncontradas === paresNecesarios) setTimeout(() => mensaje.style.display = 'block', 500);
                } else {
                    setTimeout(() => {
                        c1.elemento.classList.remove('girada');
                        c2.elemento.classList.remove('girada');
                        cartasVolteadas = [];
                        bloqueoTablero = false;
                    }, 1200); // Un poco más de tiempo para leer la palabra
                }
            }
        });

        tablero.appendChild(carta);
    });

})();
</script>