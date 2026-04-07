const cards = document.querySelectorAll('.slider-card');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let currentIndex = 0;

function updateSlider() {
    cards.forEach((card, index) => {
        card.classList.remove('active', 'blur-left', 'blur-right');

        if(index === currentIndex) {
            card.classList.add('active');
        } else if(index === currentIndex - 1 || (currentIndex === 0 && index === cards.length - 1)) {
            card.classList.add('blur-left');
        } else if(index === currentIndex + 1 || (currentIndex === cards.length - 1 && index === 0)) {
            card.classList.add('blur-right');
        } else {
            card.style.opacity = 0; // kartat larg qendres nuk duken
        }
    });
}

// Event për arrow
prevBtn.addEventListener('click', () => {
    currentIndex--;
    if(currentIndex < 0) currentIndex = cards.length - 1;
    updateSlider();
});

nextBtn.addEventListener('click', () => {
    currentIndex++;
    if(currentIndex >= cards.length) currentIndex = 0;
    updateSlider();
});

// Auto-slide çdo 3 sekonda
setInterval(() => {
    currentIndex++;
    if(currentIndex >= cards.length) currentIndex = 0;
    updateSlider();
}, 3000);

// Inicializo slider
updateSlider();
