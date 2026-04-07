const cards = document.querySelectorAll('.slider-card');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let currentIndex = 0;

// Funksioni për përditësimin e slider-it
function updateSlider() {
    cards.forEach((card, index) => {
        card.classList.remove('active', 'blur-left', 'blur-right');
        card.style.opacity = 1; // kthim në dukshmëri

        // karta aktive
        if(index === currentIndex) {
            card.classList.add('active');
        } 
        // karta anash majtas
        else if(index === (currentIndex - 1 + cards.length) % cards.length) {
            card.classList.add('blur-left');
        } 
        // karta anash djathtas
        else if(index === (currentIndex + 1) % cards.length) {
            card.classList.add('blur-right');
        } 
        // kartat larg qendres
        else {
            card.style.opacity = 0.3;
            card.style.transform = "scale(0.75)";
            card.style.filter = "blur(4px)";
            card.style.zIndex = 0;
        }
    });
}

// Navigimi me butona
prevBtn.addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + cards.length) % cards.length;
    updateSlider();
});

nextBtn.addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % cards.length;
    updateSlider();
});

// Auto-slide çdo 4 sekonda
setInterval(() => {
    currentIndex = (currentIndex + 1) % cards.length;
    updateSlider();
}, 4000);

// Inicializo slider
updateSlider();
