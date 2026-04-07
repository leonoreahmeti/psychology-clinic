const cards = document.querySelectorAll('.slider-card');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let currentIndex = 0;

function updateSlider() {
    cards.forEach((card, index) => {
        card.classList.remove('active', 'blur-left', 'blur-right');
        card.style.opacity = 1;
        card.style.transform = 'scale(1)';
        card.style.filter = 'none';
        card.style.zIndex = 1;

        if(index === currentIndex) {
            card.classList.add('active');
            card.style.zIndex = 2;
            card.style.transform = 'scale(1.1)';
            card.style.filter = 'none';
            card.style.opacity = 1;
        } else if(index === (currentIndex - 1 + cards.length) % cards.length) {
            card.classList.add('blur-left');
            card.style.transform = 'scale(0.85)';
            card.style.opacity = 0.5;
            card.style.filter = 'blur(3px)';
            card.style.zIndex = 0;
        } else if(index === (currentIndex + 1) % cards.length) {
            card.classList.add('blur-right');
            card.style.transform = 'scale(0.85)';
            card.style.opacity = 0.5;
            card.style.filter = 'blur(3px)';
            card.style.zIndex = 0;
        } else {
            card.style.opacity = 0.3;
            card.style.transform = 'scale(0.75)';
            card.style.filter = 'blur(4px)';
            card.style.zIndex = 0;
        }
    });
}

// Kliko për të vendosur kartën aktive dhe për të hapur services.html
cards.forEach(card => {
    card.addEventListener('click', () => {
        currentIndex = parseInt(card.dataset.index);
        updateSlider();
        // Hap faqen e shërbimeve
        window.location.href = 'services.html';
    });
});

prevBtn.addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + cards.length) % cards.length;
    updateSlider();
});

nextBtn.addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % cards.length;
    updateSlider();
});

setInterval(() => {
    currentIndex = (currentIndex + 1) % cards.length;
    updateSlider();
}, 4000);

updateSlider();
