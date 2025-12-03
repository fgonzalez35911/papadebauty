<canvas id="lienzo" style="width: 100%; height: 100%; display: block; touch-action: none;"></canvas>

<script>
(function() {
    const canvas = document.getElementById('lienzo');
    const ctx = canvas.getContext('2d');
    
    // Ajustar tamaño al contenedor
    let width, height;
    function resize() {
        width = canvas.width = canvas.parentElement.clientWidth;
        height = canvas.height = canvas.parentElement.clientHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    // Colores suaves (Extraídos de la paleta del sitio)
    const colores = ['#88B04B', '#92A8D1', '#F7CAC9', '#FFD700', '#FFB347'];

    // Variables del juego
    let burbujas = [];
    let frame = 0;

    class Burbuja {
        constructor() {
            this.r = Math.random() * 30 + 20; // Tamaño entre 20 y 50
            this.x = Math.random() * (width - this.r * 2) + this.r;
            this.y = height + this.r; // Nace abajo
            this.speed = Math.random() * 2 + 1; // Velocidad suave
            this.color = colores[Math.floor(Math.random() * colores.length)];
            this.oscilacion = Math.random() * 2; // Para que se mueva izquierda/derecha
            this.angulo = 0;
        }

        update() {
            this.y -= this.speed;
            this.angulo += 0.02;
            this.x += Math.sin(this.angulo) * this.oscilacion; // Movimiento ondulante
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.globalAlpha = 0.6; // Transparencia
            ctx.fill();
            ctx.strokeStyle = "white";
            ctx.lineWidth = 2;
            ctx.stroke();
            ctx.globalAlpha = 1.0;
            
            // Brillo de la burbuja
            ctx.beginPath();
            ctx.arc(this.x - this.r/3, this.y - this.r/3, this.r/4, 0, Math.PI * 2);
            ctx.fillStyle = "white";
            ctx.globalAlpha = 0.3;
            ctx.fill();
            ctx.globalAlpha = 1.0;
        }
    }

    // BUCLE DE ANIMACIÓN
    function animate() {
        ctx.clearRect(0, 0, width, height);
        
        // Crear burbujas cada 60 frames (1 segundo aprox)
        if (frame % 60 === 0) {
            burbujas.push(new Burbuja());
        }

        // Actualizar y dibujar
        for (let i = 0; i < burbujas.length; i++) {
            burbujas[i].update();
            burbujas[i].draw();

            // Eliminar si sale arriba
            if (burbujas[i].y + burbujas[i].r < 0) {
                burbujas.splice(i, 1);
                i--;
            }
        }

        frame++;
        requestAnimationFrame(animate);
    }

    animate();

    // INTERACCIÓN (Clic o Toque)
    function explotar(e) {
        // Obtener coordenadas correctas del mouse/dedo
        const rect = canvas.getBoundingClientRect();
        const clickX = (e.clientX || e.touches[0].clientX) - rect.left;
        const clickY = (e.clientY || e.touches[0].clientY) - rect.top;

        for (let i = 0; i < burbujas.length; i++) {
            const b = burbujas[i];
            const dist = Math.sqrt((clickX - b.x) ** 2 + (clickY - b.y) ** 2);
            
            // Si tocamos la burbuja
            if (dist < b.r + 10) { // +10 de margen para facilitar
                // EFECTO VISUAL (Simple)
                // Aquí podríamos agregar sonido
                
                burbujas.splice(i, 1); // Eliminar burbuja
                break; // Solo una a la vez
            }
        }
    }

    canvas.addEventListener('mousedown', explotar);
    canvas.addEventListener('touchstart', explotar);

})();
</script>