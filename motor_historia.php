<style>
    /* LÍNEA DE TIEMPO HORIZONTAL */
    .timeline-container {
        display: flex; gap: 40px; overflow-x: auto; padding: 40px 20px;
        background: #fdfbf7; white-space: nowrap; scroll-behavior: smooth;
    }
    
    /* EVENTO (CÍRCULO) */
    .evento-timeline {
        position: relative; min-width: 120px; text-align: center; cursor: pointer;
        transition: transform 0.3s;
    }
    .evento-timeline:hover { transform: scale(1.1); }
    
    .evento-circulo {
        width: 60px; height: 60px; background: #75AADB; border-radius: 50%; border: 4px solid white;
        margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: white; box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    /* LÍNEA CONECTORA */
    .evento-timeline::after {
        content: ''; position: absolute; top: 30px; right: -50%; width: 100%; height: 4px;
        background: #ccc; z-index: -1;
    }
    .evento-timeline:last-child::after { display: none; }

    /* VISOR DE ESCENA (DONDE SE CUENTA LA HISTORIA) */
    #visor-historia {
        display: none; flex-direction: column; align-items: center; text-align: center;
        margin-top: 20px; background: white; padding: 30px; border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 700px;
    }
    
    .escena-img {
        width: 100%; height: 300px; object-fit: cover; border-radius: 15px; margin-bottom: 20px;
    }
</style>

<h2 style="text-align:center; color: var(--color-primario);">Toca una fecha para conocer nuestra historia</h2>

<div class="timeline-container">
    
    <div class="evento-timeline" onclick="cargarEscena(0)">
        <div class="evento-circulo">1810</div>
        <strong>Revolución</strong>
    </div>

    <div class="evento-timeline" onclick="cargarEscena(1)">
        <div class="evento-circulo">1812</div>
        <strong>Bandera</strong>
    </div>

    <div class="evento-timeline" onclick="cargarEscena(2)">
        <div class="evento-circulo">1816</div>
        <strong>Libertad</strong>
    </div>
    
    <div class="evento-timeline" onclick="cargarEscena(3)">
        <div class="evento-circulo">1817</div>
        <strong>Cruce</strong>
    </div>

</div>

<div id="visor-historia">
    <h2 id="historia-titulo" style="color: var(--color-secundario);"></h2>
    <img id="historia-img" class="escena-img" src="">
    <p id="historia-texto" style="font-size: 1.2rem; color: #555; line-height: 1.6;"></p>
    
    <button onclick="leerHistoria()" class="btn-grande btn-jugar" style="margin-top: 20px;">
        <i class="fa-solid fa-volume-high"></i> Escuchar Relato
    </button>
</div>

<script>
    // DATOS DE HISTORIA (Se pueden editar aquí)
    const eventos = [
        {
            titulo: "25 de Mayo de 1810",
            img: "https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Cabildo_abierto_sublevacion.jpg/640px-Cabildo_abierto_sublevacion.jpg",
            texto: "Frente al Cabildo, la gente gritaba: ¡Queremos saber de qué se trata! Ese día nació nuestro Primer Gobierno Patrio."
        },
        {
            titulo: "Creación de la Bandera",
            img: "https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Jura_de_la_bandera_en_las_barrancas_del_Paran%C3%A1.jpg/640px-Jura_de_la_bandera_en_las_barrancas_del_Paran%C3%A1.jpg",
            texto: "Manuel Belgrano, a orillas del río Paraná, izó por primera vez la bandera celeste y blanca para identificarnos."
        },
        {
            titulo: "Independencia (1816)",
            img: "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Casa_de_la_Independencia_Arg.jpg/640px-Casa_de_la_Independencia_Arg.jpg",
            texto: "En una casita en Tucumán, representantes de todas las provincias firmaron que somos libres de España para siempre."
        },
        {
            titulo: "El Cruce de los Andes",
            img: "https://upload.wikimedia.org/wikipedia/commons/thumb/3/3d/San_Mart%C3%ADn_Cruce_de_los_Andes.jpg/640px-San_Mart%C3%ADn_Cruce_de_los_Andes.jpg",
            texto: "San Martín preparó un gran ejército y cruzó las montañas nevadas para ayudar a liberar a Chile y Perú."
        }
    ];

    function cargarEscena(index) {
        const ev = eventos[index];
        document.getElementById('visor-historia').style.display = 'flex';
        document.getElementById('historia-titulo').innerText = ev.titulo;
        document.getElementById('historia-img').src = ev.img;
        document.getElementById('historia-texto').innerText = ev.texto;
        
        // Scroll automático hacia abajo para ver la info
        document.getElementById('visor-historia').scrollIntoView({behavior: "smooth"});
        
        // Detener lectura anterior si la hubiera
        window.speechSynthesis.cancel();
    }

    function leerHistoria() {
        const texto = document.getElementById('historia-texto').innerText;
        const narracion = new SpeechSynthesisUtterance(texto);
        narracion.rate = 0.9; // Un poco más lento para que se entienda
        window.speechSynthesis.speak(narracion);
    }
</script>