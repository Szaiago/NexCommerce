document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('.carousel');
    const indicators = document.querySelectorAll('.indicator');
    let currentSlide = 0;

    // Função para atualizar o carrossel
    function updateCarousel(slideIndex) {
        // Atualiza a posição do carrossel
        carousel.style.transform = `translateX(-${slideIndex * 100}%)`;

        // Atualiza o estado dos indicadores (active)
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === slideIndex);
        });
    }

    // Evento de clique nos indicadores para mudar o slide
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentSlide = index;
            updateCarousel(currentSlide);
        });
    });

    // Função de auto slide (alternância automática entre os slides)
    setInterval(() => {
        currentSlide = (currentSlide + 1) % indicators.length;
        updateCarousel(currentSlide);
    }, 10000); // Troca o slide a cada 5 segundos
});
