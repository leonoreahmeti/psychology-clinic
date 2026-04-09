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

        if (index === currentIndex) {
            card.classList.add('active');
            card.style.zIndex = 2;
            card.style.transform = 'scale(1.1)';
            card.style.filter = 'none';
            card.style.opacity = 1;
        } else if (index === (currentIndex - 1 + cards.length) % cards.length) {
            card.classList.add('blur-left');
            card.style.transform = 'scale(0.85)';
            card.style.opacity = 0.5;
            card.style.filter = 'blur(3px)';
            card.style.zIndex = 0;
        } else if (index === (currentIndex + 1) % cards.length) {
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

/* Run slider only if slider exists on page */
if (cards.length > 0 && prevBtn && nextBtn) {
    cards.forEach(card => {
        card.addEventListener('click', () => {
            currentIndex = parseInt(card.dataset.index);
            updateSlider();
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
}

/* CONTACT MODAL */
const openModal = document.getElementById("openModal");
const closeModal = document.getElementById("closeModal");
const contactModal = document.getElementById("contactModal");

if (openModal && closeModal && contactModal) {
    openModal.addEventListener("click", () => {
        contactModal.classList.add("active");
    });

<<<<<<< HEAD
    closeModal.addEventListener("click", () => {
        contactModal.classList.remove("active");
    });

    window.addEventListener("click", (e) => {
        if (e.target === contactModal) {
            contactModal.classList.remove("active");
        }
    });
}
=======
updateSlider();

// Booking page
function nextStep(step) {

    if(step === 1) {
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;

        if(!date) {
            alert("Select a date");
            return;
        }

        if(!time) {
            alert("Select a time");
            return;
        }

        document.getElementById('sum-date').innerText = date;
        document.getElementById('sum-time').innerText = time;
    }

    if(step === 2) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const terms = document.getElementById('terms');

        if(!name) {
            alert("Enter your name");
            return;
        }

        if(!email || !email.includes("@")) {
            alert("Enter valid email");
            return;
        }

        if(!terms.checked) {
            alert("You must accept Terms");
            return;
        }

        const confirmEmail = confirm(`Confirm email:\n${email}`);
        if(!confirmEmail) return;
    }

    document.getElementById('step' + step).classList.remove('active');
    document.getElementById('step' + (step + 1)).classList.add('active');
}
const termsCheckbox = document.getElementById("terms");
const popup = document.getElementById("termsPopup");
const acceptBtn = document.getElementById("acceptTerms");
const scrollBox = document.getElementById("termsScroll");

// klik checkbox → hap popup
termsCheckbox.addEventListener("click", function(e) {
    if(!this.checked) {
        e.preventDefault();
        popup.style.display = "flex";
    }
});

// enable button kur scroll në fund
scrollBox.addEventListener("scroll", () => {
    if(scrollBox.scrollTop + scrollBox.clientHeight >= scrollBox.scrollHeight) {
        acceptBtn.disabled = false;
    }
});

// klik agree
acceptBtn.addEventListener("click", () => {
    termsCheckbox.checked = true;
    popup.style.display = "none";

    localStorage.setItem("termsAccepted", "true");
});

// auto-check nëse është pranu ma herët
window.addEventListener("DOMContentLoaded", () => {
    if(localStorage.getItem("termsAccepted") === "true") {
        termsCheckbox.checked = true;
    }
});

window.addEventListener("click", (e) => {
    const popup = document.getElementById("termsPopup");
    if(e.target === popup) {
        popup.style.display = "none";
    }
});
>>>>>>> c305991327c8d3d0b3872d29976b60f21560decf
