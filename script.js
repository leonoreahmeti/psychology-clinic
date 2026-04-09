// ---------------- SLIDER SERVICES ----------------
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
        } else if(index === (currentIndex - 1 + cards.length) % cards.length) {
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
cards.forEach(card => {
    card.addEventListener('click', () => {
        currentIndex = parseInt(card.dataset.index);
        updateSlider();
        window.location.href = 'services.html';
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

    closeModal.addEventListener("click", () => {
        contactModal.classList.remove("active");
    });

    window.addEventListener("click", (e) => {
        if (e.target === contactModal) {
            contactModal.classList.remove("active");
        }
    });
}
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

// ---------------- BOOKING + VISUAL CALENDAR ----------------
const calendarEl = document.getElementById('calendar');
const timeListEl = document.getElementById('time-list');

const bookedDates = ['2026-04-09', '2026-04-15'];
const allTimes = ['14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00'];

let selectedDate = null;
let selectedTime = null;

function generateCalendar() {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    calendarEl.innerHTML = '';
    const monthName = now.toLocaleString('default', { month: 'long' });
    const title = document.createElement('h3');
    title.textContent = `${monthName} ${year}`;
    title.style.textAlign = 'center';
    calendarEl.appendChild(title);

    const grid = document.createElement('div');
    grid.classList.add('calendar-grid');
    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = 'repeat(7, 1fr)';
    grid.style.gap = '5px';
    calendarEl.appendChild(grid);

    for(let day = 1; day <= daysInMonth; day++){
        const dateStr = `${year}-${(month + 1).toString().padStart(2,'0')}-${day.toString().padStart(2,'0')}`;
        const dayEl = document.createElement('button');
        dayEl.textContent = day;
        dayEl.style.padding = '10px';
        dayEl.style.border = '1px solid #ccc';
        dayEl.style.borderRadius = '5px';
        dayEl.style.cursor = 'pointer';

        if(bookedDates.includes(dateStr)){
            dayEl.disabled = true;
            dayEl.style.background = '#f8d7da';
            dayEl.style.color = '#721c24';
            dayEl.style.cursor = 'not-allowed';
        } else {
            dayEl.style.background = '#d4edda';
            dayEl.style.color = '#155724';
            dayEl.addEventListener('click', () => selectDate(dateStr, dayEl));
        }

        grid.appendChild(dayEl);
    }
}

function selectDate(dateStr, dayEl){
    selectedDate = dateStr;

    // Highlight selected day
    calendarEl.querySelectorAll('button').forEach(btn => btn.style.outline = 'none');
    dayEl.style.outline = '3px solid #007BFF';

    renderTimes();
}

function renderTimes(){
    timeListEl.innerHTML = '<h3>Available Times</h3>';
    allTimes.forEach(time => {
        const li = document.createElement('li');
        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'time';
        radio.value = time;
        radio.id = `time-${time.replace(':','')}`;
        radio.addEventListener('change', () => selectedTime = time);

        const label = document.createElement('label');
        label.htmlFor = radio.id;
        label.textContent = time;
        li.appendChild(radio);
        li.appendChild(label);
        timeListEl.appendChild(li);
    });
}

// ---------------- NEXT STEP ----------------
function nextStep(step){
    if(step === 1){
        if(!selectedDate){ alert('Please select a date'); return; }
        if(!selectedTime){ alert('Please select a time'); return; }

        document.getElementById('sum-date').innerText = selectedDate;
        document.getElementById('sum-time').innerText = selectedTime;
        document.getElementById('sum-service').innerText = 'Psychological Session';
        document.getElementById('sum-therapist').innerText = 'Nafie Sylejmani';
        document.getElementById('sum-duration').innerText = '50 minutes';

        showStep(2);
    } else if(step === 2){
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const terms = document.getElementById('terms').checked;

        if(!name){ alert('Enter your name'); return; }
        if(!email || !email.includes('@')){ alert('Enter valid email'); return; }
        if(!terms){ alert('You must accept Terms'); return; }

        document.getElementById('sum-name').innerText = name;
        document.getElementById('sum-email').innerText = email;

        showStep(3);
    }
}

function showStep(step){
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
    document.getElementById('step'+step).classList.add('active');
    document.getElementById('payment-section').style.display = (step===3)? 'block':'none';
}

generateCalendar();

// ---------------- TERMS POPUP ----------------
const termsCheckbox = document.getElementById("terms");
const popup = document.getElementById("termsPopup");
const acceptBtn = document.getElementById("acceptTerms");
const scrollBox = document.getElementById("termsScroll");

termsCheckbox.addEventListener("click", function(e){
    if(!this.checked){
        e.preventDefault();
        popup.style.display = "flex";
    }
});

scrollBox.addEventListener("scroll", () => {
    if(scrollBox.scrollTop + scrollBox.clientHeight >= scrollBox.scrollHeight){
        acceptBtn.disabled = false;
    }
});

acceptBtn.addEventListener("click", () => {
    termsCheckbox.checked = true;
    popup.style.display = "none";
    localStorage.setItem("termsAccepted", "true");
});

window.addEventListener("DOMContentLoaded", () => {
    if(localStorage.getItem("termsAccepted") === "true"){
        termsCheckbox.checked = true;
    }
});

window.addEventListener("click", (e) => {
    if(e.target === popup){ popup.style.display = "none"; }
});
window.addEventListener('DOMContentLoaded', () => {
    generateCalendar();
});
