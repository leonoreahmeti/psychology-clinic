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

if (cards.length > 0 && prevBtn && nextBtn) {
    cards.forEach(card => {
        card.addEventListener('click', () => {
            currentIndex = parseInt(card.dataset.index, 10);
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

// ---------------- CONTACT MODAL ----------------
const openModal = document.getElementById('openModal');
const closeModal = document.getElementById('closeModal');
const contactModal = document.getElementById('contactModal');

if (openModal && closeModal && contactModal) {
    openModal.addEventListener('click', () => {
        contactModal.classList.add('active');
    });

    closeModal.addEventListener('click', () => {
        contactModal.classList.remove('active');
    });

    window.addEventListener('click', (e) => {
        if (e.target === contactModal) {
            contactModal.classList.remove('active');
        }
    });
}

// ---------------- BOOKING FLOW ----------------
const calendarEl = document.getElementById('calendar');
const timeListEl = document.getElementById('time-list');

const bookedDates = ['2026-04-09', '2026-04-15'];
const allTimes = [
    '14:00', '14:30', '15:00', '15:30',
    '16:00', '16:30', '17:00', '17:30',
    '18:00', '18:30', '19:00', '19:30',
    '20:00', '20:30', '21:00'
];

let selectedDate = null;
let selectedTime = null;
let selectedService = null;
let currentDate = new Date();

function updateLiveSummary() {
    const liveDate = document.getElementById('live-date');
    const liveTime = document.getElementById('live-time');
    const liveService = document.getElementById('live-service');

    if (liveDate) liveDate.innerText = selectedDate || 'Not selected';
    if (liveTime) liveTime.innerText = selectedTime || 'Not selected';
    if (liveService) liveService.innerText = selectedService || 'Not selected';
}

function updateFinalSummary() {
    const clientName = document.getElementById('client-name');
    const clientEmail = document.getElementById('client-email');

    const sumDate = document.getElementById('sum-date');
    const sumTime = document.getElementById('sum-time');
    const sumService = document.getElementById('sum-service');
    const sumName = document.getElementById('sum-name');
    const sumEmail = document.getElementById('sum-email');

    if (sumDate) sumDate.innerText = selectedDate || '-';
    if (sumTime) sumTime.innerText = selectedTime || '-';
    if (sumService) sumService.innerText = selectedService || '-';
    if (sumName) sumName.innerText = clientName ? clientName.value.trim() || '-' : '-';
    if (sumEmail) sumEmail.innerText = clientEmail ? clientEmail.value.trim() || '-' : '-';
}

function generateCalendar() {
    if (!calendarEl) return;

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    calendarEl.innerHTML = '';

    const header = document.createElement('div');
    header.classList.add('calendar-header');

    const prev = document.createElement('button');
    prev.type = 'button';
    prev.textContent = '<';
    prev.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar();
    });

    const next = document.createElement('button');
    next.type = 'button';
    next.textContent = '>';
    next.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar();
    });

    const title = document.createElement('span');
    title.textContent = currentDate.toLocaleString('default', {
        month: 'long',
        year: 'numeric'
    });

    header.appendChild(prev);
    header.appendChild(title);
    header.appendChild(next);
    calendarEl.appendChild(header);

    const daysRow = document.createElement('div');
    daysRow.classList.add('calendar-days');

    ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(day => {
        const el = document.createElement('div');
        el.textContent = day;
        daysRow.appendChild(el);
    });

    calendarEl.appendChild(daysRow);

    const grid = document.createElement('div');
    grid.classList.add('calendar-grid');
    calendarEl.appendChild(grid);

    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = day;

        const thisDate = new Date(dateStr);

        if (thisDate < today || bookedDates.includes(dateStr)) {
            btn.disabled = true;
        } else {
            btn.addEventListener('click', () => {
                selectedDate = dateStr;

                calendarEl.querySelectorAll('.calendar-grid button').forEach(button => {
                    button.classList.remove('selected');
                });

                btn.classList.add('selected');
                updateLiveSummary();
            });
        }

        grid.appendChild(btn);
    }
}

function renderTimes() {
    if (!timeListEl) return;

    timeListEl.innerHTML = '';

    allTimes.forEach(time => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'time-option';
        btn.textContent = time;

        btn.addEventListener('click', () => {
            selectedTime = time;

            document.querySelectorAll('.time-option').forEach(option => {
                option.classList.remove('selected');
            });

            btn.classList.add('selected');
            updateLiveSummary();
        });

        timeListEl.appendChild(btn);
    });
}

function selectSession(service, element) {
    selectedService = service;

    document.querySelectorAll('.session-card').forEach(card => {
        card.classList.remove('selected');
    });

    element.classList.add('selected');
    updateLiveSummary();
}

function goToStep(step) {
    if (step === 2 && !selectedDate) {
        alert('Please select a date first.');
        return;
    }

    if (step === 3 && !selectedTime) {
        alert('Please select a time first.');
        return;
    }

    if (step === 4 && !selectedService) {
        alert('Please select a session type first.');
        return;
    }

    if (step === 5) {
        const clientName = document.getElementById('client-name');
        const clientEmail = document.getElementById('client-email');
        const terms = document.getElementById('terms');

        const nameValue = clientName ? clientName.value.trim() : '';
        const emailValue = clientEmail ? clientEmail.value.trim() : '';
        const acceptedTerms = terms ? terms.checked : false;

        if (!nameValue) {
            alert('Please enter your name.');
            return;
        }

        if (!emailValue || !emailValue.includes('@')) {
            alert('Please enter a valid email.');
            return;
        }

        if (!acceptedTerms) {
            alert('Please accept the Terms & Conditions.');
            return;
        }

        updateFinalSummary();
    }

    document.querySelectorAll('.step').forEach(section => {
        section.classList.remove('active');
    });

    const nextStepSection = document.getElementById(`step${step}`);
    if (nextStepSection) {
        nextStepSection.classList.add('active');
    }
}

// ---------------- TERMS POPUP ----------------
const termsCheckbox = document.getElementById('terms');
const popup = document.getElementById('termsPopup');
const acceptBtn = document.getElementById('acceptTerms');
const scrollBox = document.getElementById('termsScroll');

if (termsCheckbox && popup && acceptBtn && scrollBox) {
    termsCheckbox.addEventListener('click', function (e) {
        if (!this.checked) {
            e.preventDefault();
            popup.style.display = 'flex';
        }
    });

    scrollBox.addEventListener('scroll', () => {
        if (scrollBox.scrollTop + scrollBox.clientHeight >= scrollBox.scrollHeight) {
            acceptBtn.disabled = false;
        }
    });

    acceptBtn.addEventListener('click', () => {
        termsCheckbox.checked = true;
        popup.style.display = 'none';
        localStorage.setItem('termsAccepted', 'true');
    });

    window.addEventListener('click', (e) => {
        if (e.target === popup) {
            popup.style.display = 'none';
        }
    });

    window.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('termsAccepted') === 'true') {
            termsCheckbox.checked = true;
        }
    });
}

// ---------------- PAGE LOAD ----------------
window.addEventListener('DOMContentLoaded', () => {
    generateCalendar();
    renderTimes();
    updateLiveSummary();
});
