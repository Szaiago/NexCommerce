
const carousel = document.querySelector('.carousel');
const indicators = document.querySelectorAll('.indicator');

let currentSlide = 0;

function updateCarousel(slideIndex) {
    carousel.style.transform = `translateX(-${slideIndex * 100}%)`;
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === slideIndex);
    });
}

indicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
        currentSlide = index;
        updateCarousel(currentSlide);
    });
});

// Auto slide functionality (optional)
setInterval(() => {
    currentSlide = (currentSlide + 1) % indicators.length;
    updateCarousel(currentSlide);
}, 5000);