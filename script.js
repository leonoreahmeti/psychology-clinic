// ======================================================
// SLIDER SERVICES
// ======================================================

const cards = document.querySelectorAll('.slider-card');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let currentIndex = 0;


function updateSlider() {

    if (!cards.length) return;

    cards.forEach((card, index) => {

        card.classList.remove(
            'active',
            'blur-left',
            'blur-right'
        );

        card.style.opacity = 1;
        card.style.transform = 'scale(1)';
        card.style.filter = 'none';
        card.style.zIndex = 1;


        if (index === currentIndex) {

            card.classList.add('active');

            card.style.zIndex = 2;
            card.style.transform = 'scale(1.1)';
            card.style.opacity = 1;

        }

        else if (
            index ===
            (currentIndex - 1 + cards.length) % cards.length
        ) {

            card.classList.add('blur-left');

            card.style.transform = 'scale(0.85)';
            card.style.opacity = 0.5;
            card.style.filter = 'blur(3px)';
            card.style.zIndex = 0;

        }

        else if (
            index ===
            (currentIndex + 1) % cards.length
        ) {

            card.classList.add('blur-right');

            card.style.transform = 'scale(0.85)';
            card.style.opacity = 0.5;
            card.style.filter = 'blur(3px)';
            card.style.zIndex = 0;

        }

        else {

            card.style.opacity = 0.3;
            card.style.transform = 'scale(0.75)';
            card.style.filter = 'blur(4px)';
            card.style.zIndex = 0;

        }

    });

}


if (
    cards.length > 0 &&
    prevBtn &&
    nextBtn
) {

    cards.forEach(card => {

        card.addEventListener('click', () => {

            currentIndex =
                parseInt(
                    card.dataset.index,
                    10
                );

            updateSlider();

            window.location.href =
                'services.html';

        });

    });


    prevBtn.addEventListener('click', () => {

        currentIndex =
            (currentIndex - 1 + cards.length)
            % cards.length;

        updateSlider();

    });


    nextBtn.addEventListener('click', () => {

        currentIndex =
            (currentIndex + 1)
            % cards.length;

        updateSlider();

    });


    setInterval(() => {

        currentIndex =
            (currentIndex + 1)
            % cards.length;

        updateSlider();

    }, 4000);


    updateSlider();

}



// ======================================================
// CONTACT MODAL
// ======================================================

const openModal =
    document.getElementById('openModal');

const closeModal =
    document.getElementById('closeModal');

const contactModal =
    document.getElementById('contactModal');


if (
    openModal &&
    closeModal &&
    contactModal
) {

    openModal.addEventListener(
        'click',
        () => {

            contactModal.classList.add(
                'active'
            );

        }
    );


    closeModal.addEventListener(
        'click',
        () => {

            contactModal.classList.remove(
                'active'
            );

        }
    );


    window.addEventListener(
        'click',
        e => {

            if (e.target === contactModal) {

                contactModal.classList.remove(
                    'active'
                );

            }

        }
    );

}



// ======================================================
// BOOKING VARIABLES
// ======================================================

const calendarEl =
    document.getElementById('calendar');

const timeListEl =
    document.getElementById('time-list');


let bookedBookings = [];

let selectedDate = null;

let selectedTime = null;

let selectedService = null;

let currentDate = new Date();


// Prevent double booking request
let isSubmittingBooking = false;



// ======================================================
// AVAILABLE TIMES
// ======================================================

const allTimes = [

    '14:00',
    '14:30',
    '15:00',
    '15:30',
    '16:00',
    '16:30',
    '17:00',
    '17:30',
    '18:00',
    '18:30',
    '19:00',
    '19:30',
    '20:00',
    '20:30',
    '21:00'

];



// ======================================================
// LOAD BOOKINGS
// ======================================================

async function loadBookings() {

    try {

        const response =
            await fetch(
                'api/get_bookings.php',
                {
                    cache: 'no-store'
                }
            );


        if (!response.ok) {

            throw new Error(
                'Could not load bookings'
            );

        }


        const data =
            await response.json();


        if (Array.isArray(data)) {

            bookedBookings = data;

        }

        else {

            bookedBookings = [];

        }


        console.log(
            'Bookings from database:',
            bookedBookings
        );

    }

    catch (error) {

        console.error(
            'Error loading bookings:',
            error
        );

        bookedBookings = [];

    }

}



// ======================================================
// LIVE SUMMARY
// ======================================================

function updateLiveSummary() {

    const liveDate =
        document.getElementById(
            'live-date'
        );

    const liveTime =
        document.getElementById(
            'live-time'
        );

    const liveService =
        document.getElementById(
            'live-service'
        );

    if (liveDate) {

        liveDate.innerText =
            selectedDate ||
            'Not selected';

    }


    if (liveTime) {

        liveTime.innerText =
            selectedTime ||
            'Not selected';

    }


    if (liveService) {

        liveService.innerText =
            selectedService ||
            'Not selected';

    }

}



// ======================================================
// FINAL SUMMARY
// ======================================================

function updateFinalSummary() {

    const clientName =
        document.getElementById(
            'client-name'
        );

    const clientEmail =
        document.getElementById(
            'client-email'
        );


    const sumDate =
        document.getElementById(
            'sum-date'
        );

    const sumTime =
        document.getElementById(
            'sum-time'
        );

    const sumService =
        document.getElementById(
            'sum-service'
        );

    const sumName =
        document.getElementById(
            'sum-name'
        );

    const sumEmail =
        document.getElementById(
            'sum-email'
        );

    const sumPrice =
        document.getElementById(
            'sum-price'
        );


    if (sumDate) {

        sumDate.innerText =
            selectedDate || '-';

    }


    if (sumTime) {

        sumTime.innerText =
            selectedTime || '-';

    }


    if (sumService) {

        sumService.innerText =
            selectedService || '-';

    }


    if (sumName) {

        sumName.innerText =
            clientName
                ? clientName.value.trim() || '-'
                : '-';

    }


    if (sumEmail) {

        sumEmail.innerText =
            clientEmail
                ? clientEmail.value.trim() || '-'
                : '-';

    }


    if (sumPrice) {

        sumPrice.innerText = '€50';

    }

}



// ======================================================
// CALENDAR
// ======================================================

function generateCalendar() {

    if (!calendarEl) return;


    const year =
        currentDate.getFullYear();

    const month =
        currentDate.getMonth();


    const daysInMonth =
        new Date(
            year,
            month + 1,
            0
        ).getDate();


    const today = new Date();

    today.setHours(
        0,
        0,
        0,
        0
    );


    calendarEl.innerHTML = '';


    // ---------------- HEADER ----------------

    const header =
        document.createElement('div');

    header.classList.add(
        'calendar-header'
    );


    const prev =
        document.createElement('button');

    prev.type = 'button';

    prev.textContent = '<';


    prev.addEventListener(
        'click',
        () => {

            currentDate.setMonth(
                currentDate.getMonth() - 1
            );

            generateCalendar();

        }
    );


    const next =
        document.createElement('button');

    next.type = 'button';

    next.textContent = '>';


    next.addEventListener(
        'click',
        () => {

            currentDate.setMonth(
                currentDate.getMonth() + 1
            );

            generateCalendar();

        }
    );


    const title =
        document.createElement('span');


    title.textContent =
        currentDate.toLocaleString(
            'default',
            {
                month: 'long',
                year: 'numeric'
            }
        );


    header.appendChild(prev);

    header.appendChild(title);

    header.appendChild(next);

    calendarEl.appendChild(header);



    // ---------------- DAYS ----------------

    const daysRow =
        document.createElement('div');

    daysRow.classList.add(
        'calendar-days'
    );


    [
        'Mon',
        'Tue',
        'Wed',
        'Thu',
        'Fri',
        'Sat',
        'Sun'
    ].forEach(day => {

        const el =
            document.createElement('div');

        el.textContent = day;

        daysRow.appendChild(el);

    });


    calendarEl.appendChild(daysRow);



    // ---------------- GRID ----------------

    const grid =
        document.createElement('div');

    grid.classList.add(
        'calendar-grid'
    );


    calendarEl.appendChild(grid);



    // ---------------- DAYS ----------------

    for (
        let day = 1;
        day <= daysInMonth;
        day++
    ) {

        const dateStr =
            `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;


        const btn =
            document.createElement('button');


        btn.type = 'button';

        btn.textContent = day;


        const thisDate =
            new Date(
                year,
                month,
                day
            );


        thisDate.setHours(
            0,
            0,
            0,
            0
        );


        // Past date

        if (thisDate < today) {

            btn.disabled = true;

        }

        else {

            btn.addEventListener(
                'click',
                () => {

                    selectedDate =
                        dateStr;

                    selectedTime =
                        null;


                    // Remove previous date selection

                    calendarEl
                        .querySelectorAll(
                            '.calendar-grid button'
                        )
                        .forEach(button => {

                            button.classList.remove(
                                'selected'
                            );

                        });


                    // Select this date

                    btn.classList.add(
                        'selected'
                    );


                    renderTimes();

                    updateLiveSummary();

                }
            );

        }


        // Keep selected date selected

        if (
            selectedDate === dateStr
        ) {

            btn.classList.add(
                'selected'
            );

        }


        grid.appendChild(btn);

    }

}



// ======================================================
// RENDER TIMES
// ======================================================

function renderTimes() {

    if (!timeListEl) return;


    timeListEl.innerHTML = '';


    if (!selectedDate) {

        timeListEl.innerHTML =
            '<p>Please select a date first.</p>';

        return;

    }


    const now =
        new Date();


    const today =
        new Date();


    const todayString =
        `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;


    allTimes.forEach(time => {


        // ==========================================
        // CHECK DATABASE BOOKING
        // ==========================================

        const isBooked =
            bookedBookings.some(
                booking => {

                    return (
                        String(booking.date)
                        === String(selectedDate)

                        &&

                        String(booking.time)
                        .substring(0, 5)
                        === String(time)
                    );

                }
            );



        // ==========================================
        // CHECK PAST TIME
        // ==========================================

        let isPastTime = false;


        if (
            selectedDate === todayString
        ) {

            const [
                hours,
                minutes
            ] =
                time
                    .split(':')
                    .map(Number);


            const slotTime =
                new Date();


            slotTime.setHours(
                hours,
                minutes,
                0,
                0
            );


            if (
                slotTime <= now
            ) {

                isPastTime = true;

            }

        }



        // ==========================================
        // CREATE BUTTON
        // ==========================================

        const btn =
            document.createElement('button');


        btn.type = 'button';

        btn.className =
            'time-option';



        // ==========================================
        // BOOKED
        // ==========================================

        if (isBooked) {

            btn.textContent =
                `${time} - Booked`;

            btn.classList.add(
                'booked'
            );

            btn.disabled = true;

        }



        // ==========================================
        // PASSED
        // ==========================================

        else if (isPastTime) {

            btn.textContent =
                `${time} - Passed`;

            btn.classList.add(
                'past'
            );

            btn.disabled = true;

        }



        // ==========================================
        // AVAILABLE
        // ==========================================

        else {

            btn.textContent =
                time;


            if (
                selectedTime === time
            ) {

                btn.classList.add(
                    'selected'
                );

            }


            btn.addEventListener(
                'click',
                () => {

                    selectedTime =
                        time;


                    timeListEl
                        .querySelectorAll(
                            '.time-option'
                        )
                        .forEach(option => {

                            option.classList.remove(
                                'selected'
                            );

                        });


                    btn.classList.add(
                        'selected'
                    );


                    updateLiveSummary();

                }
            );

        }


        timeListEl.appendChild(btn);

    });

}



// ======================================================
// SELECT SESSION
// ======================================================

function selectSession(
    service,
    element
) {

    selectedService =
        service;


    document
        .querySelectorAll(
            '.session-card'
        )
        .forEach(card => {

            card.classList.remove(
                'selected'
            );

        });


    if (element) {

        element.classList.add(
            'selected'
        );

    }


    updateLiveSummary();

}



// ======================================================
// STEP NAVIGATION
// ======================================================

function goToStep(step) {


    // ==========================================
    // STEP 2 = DATE REQUIRED
    // ==========================================

    if (
        step === 2 &&
        !selectedDate
    ) {

        alert(
            'Please select a date first.'
        );

        return;

    }



    // ==========================================
    // STEP 3 = TIME REQUIRED
    // ==========================================

    if (
        step === 3 &&
        !selectedTime
    ) {

        alert(
            'Please select a time first.'
        );

        return;

    }



    // ==========================================
    // STEP 4 = SERVICE REQUIRED
    // ==========================================

    if (
        step === 4 &&
        !selectedService
    ) {

        alert(
            'Please select a session type first.'
        );

        return;

    }



    // ==========================================
    // STEP 5 = VALIDATE DETAILS
    // ==========================================

    if (step === 5) {

        const clientName =
            document.getElementById(
                'client-name'
            );

        const clientEmail =
            document.getElementById(
                'client-email'
            );

        const terms =
            document.getElementById(
                'terms'
            );


        const nameValue =
            clientName
                ? clientName.value.trim()
                : '';


        const emailValue =
            clientEmail
                ? clientEmail.value.trim()
                : '';


        const acceptedTerms =
            terms
                ? terms.checked
                : false;



        if (!nameValue) {

            alert(
                'Please enter your name.'
            );

            if (clientName) {

                clientName.focus();

            }

            return;

        }



        if (
            !emailValue ||
            !emailValue.includes('@')
        ) {

            alert(
                'Please enter a valid email.'
            );

            if (clientEmail) {

                clientEmail.focus();

            }

            return;

        }



        if (!acceptedTerms) {

            alert(
                'Please accept the Terms & Conditions.'
            );

            return;

        }


        updateFinalSummary();

    }



    // ==========================================
    // SHOW STEP
    // ==========================================

    document
        .querySelectorAll('.step')
        .forEach(section => {

            section.classList.remove(
                'active'
            );

        });


    const nextStepSection =
        document.getElementById(
            `step${step}`
        );


    if (nextStepSection) {

        nextStepSection.classList.add(
            'active'
        );

    }

}



// ======================================================
// TERMS POPUP
// ======================================================

const termsCheckbox =
    document.getElementById(
        'terms'
    );

const popup =
    document.getElementById(
        'termsPopup'
    );

const acceptBtn =
    document.getElementById(
        'acceptTerms'
    );

const scrollBox =
    document.getElementById(
        'termsScroll'
    );


if (
    termsCheckbox &&
    popup &&
    acceptBtn &&
    scrollBox
) {


    // Open popup when trying to check

    termsCheckbox.addEventListener(
        'click',
        function (e) {

            if (!this.checked) {

                e.preventDefault();

                popup.style.display =
                    'flex';

            }

        }
    );



    // Enable accept after scrolling

    scrollBox.addEventListener(
        'scroll',
        () => {

            const reachedBottom =
                scrollBox.scrollTop +
                scrollBox.clientHeight >=
                scrollBox.scrollHeight - 5;


            if (reachedBottom) {

                acceptBtn.disabled =
                    false;

            }

        }
    );



    // Accept terms

    acceptBtn.addEventListener(
        'click',
        () => {

            termsCheckbox.checked =
                true;

            popup.style.display =
                'none';

            localStorage.setItem(
                'termsAccepted',
                'true'
            );

        }
    );



    // Close popup outside

    window.addEventListener(
        'click',
        e => {

            if (
                e.target === popup
            ) {

                popup.style.display =
                    'none';

            }

        }
    );



    // Restore accepted terms

    if (
        localStorage.getItem(
            'termsAccepted'
        ) === 'true'
    ) {

        termsCheckbox.checked =
            true;

    }

}



// ======================================================
// HAMBURGER MENU
// ======================================================

const hamburger =
    document.getElementById(
        'hamburger'
    );

const navMenu =
    document.getElementById(
        'nav-menu'
    );


if (
    hamburger &&
    navMenu
) {

    hamburger.addEventListener(
        'click',
        e => {

            e.stopPropagation();

            hamburger.classList.toggle(
                'active'
            );

            navMenu.classList.toggle(
                'active'
            );

        }
    );


    navMenu
        .querySelectorAll('a')
        .forEach(link => {

            link.addEventListener(
                'click',
                () => {

                    hamburger.classList.remove(
                        'active'
                    );

                    navMenu.classList.remove(
                        'active'
                    );

                }
            );

        });


    document.addEventListener(
        'click',
        e => {

            if (
                !navMenu.contains(e.target) &&
                !hamburger.contains(e.target)
            ) {

                hamburger.classList.remove(
                    'active'
                );

                navMenu.classList.remove(
                    'active'
                );

            }

        }
    );

}



// ======================================================
// SUBMIT BOOKING
// ======================================================

async function submitBooking() {


    // ==========================================
    // PREVENT DOUBLE CLICK
    // ==========================================

    if (isSubmittingBooking) {

        console.log(
            'Booking already being submitted.'
        );

        return;

    }



    // ==========================================
    // GET ELEMENTS
    // ==========================================

    const clientName =
        document.getElementById(
            'client-name'
        );

    const clientEmail =
        document.getElementById(
            'client-email'
        );

    const terms =
        document.getElementById(
            'terms'
        );

    const paymentBtn =
        document.getElementById(
            'continuePaymentBtn'
        );



    // ==========================================
    // VALIDATE BOOKING
    // ==========================================

    if (
        !selectedDate ||
        !selectedTime ||
        !selectedService
    ) {

        alert(
            'Please complete your booking first.'
        );

        return;

    }



    if (
        !clientName ||
        !clientEmail
    ) {

        alert(
            'Client details are missing.'
        );

        return;

    }



    const name =
        clientName.value.trim();

    const email =
        clientEmail.value.trim();



    if (!name) {

        alert(
            'Please enter your name.'
        );

        clientName.focus();

        return;

    }



    if (
        !email ||
        !email.includes('@')
    ) {

        alert(
            'Please enter a valid email.'
        );

        clientEmail.focus();

        return;

    }



    if (
        terms &&
        !terms.checked
    ) {

        alert(
            'Please accept the Terms & Conditions.'
        );

        return;

    }



    // ==========================================
    // SAVE VALUES BEFORE FETCH
    // ==========================================

    const bookingDate =
        selectedDate;

    const bookingTime =
        selectedTime;

    const bookingService =
        selectedService;



    // ==========================================
    // LOCK BUTTON
    // ==========================================

    isSubmittingBooking =
        true;


    if (paymentBtn) {

        paymentBtn.disabled =
            true;

        paymentBtn.textContent =
            'Processing...';

    }



    try {


        // ==========================================
        // SEND TO PHP
        // ==========================================

        const response =
            await fetch(
                'api/booking.php',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({

                        client_name:
                            name,

                        client_email:
                            email,

                        booking_date:
                            bookingDate,

                        booking_time:
                            bookingTime,

                        service:
                            bookingService

                    })

                }
            );



        // ==========================================
        // READ RESPONSE
        // ==========================================

        const result =
            await response.json();


        console.log(
            'Booking response:',
            result
        );



        // ==========================================
        // SUCCESS
        // ==========================================

        if (
            result.success
        ) {


            console.log(
                'Booking ID:',
                result.booking_id
            );



            // Add newly booked time locally

            bookedBookings.push({

                date:
                    bookingDate,

                time:
                    bookingTime,

                service:
                    bookingService

            });



            // ==========================================
            // SUCCESS POPUP DATA
            // ==========================================

            const successDate =
                document.getElementById(
                    'success-date'
                );

            const successTime =
                document.getElementById(
                    'success-time'
                );

            const successService =
                document.getElementById(
                    'success-service'
                );

            const successEmail =
                document.getElementById(
                    'success-email'
                );



            if (successDate) {

                successDate.innerText =
                    bookingDate;

            }


            if (successTime) {

                successTime.innerText =
                    bookingTime;

            }


            if (successService) {

                successService.innerText =
                    bookingService;

            }


            if (successEmail) {

                successEmail.innerText =
                    email;

            }



            // ==========================================
            // SHOW SUCCESS POPUP
            // ==========================================

            const successPopup =
                document.getElementById(
                    'bookingSuccessPopup'
                );


            if (successPopup) {

                successPopup.classList.add(
                    'active'
                );

            }



            // ==========================================
            // KEEP DATE & SERVICE
            // ONLY CLEAR SELECTED TIME
            // ==========================================

            selectedTime =
                bookingTime;


            // Render again so booked time
            // becomes unavailable

            renderTimes();

            updateLiveSummary();



            console.log(
                'Booking successfully completed.'
            );

        }

        else {


            // ==========================================
            // BOOKING FAILED
            // ==========================================

            alert(
                result.message ||
                'Booking failed.'
            );

        }

    }

    catch (error) {


        console.error(
            'Booking error:',
            error
        );


        alert(
            'Could not connect to the server.'
        );

    }

    finally {


        // ==========================================
        // UNLOCK BUTTON
        // ==========================================

        isSubmittingBooking =
            false;


        if (paymentBtn) {

            paymentBtn.disabled =
                false;

            paymentBtn.textContent =
                'Continue to Payment';

        }

    }

}



// ======================================================
// CLOSE SUCCESS POPUP
// ======================================================

function closeBookingSuccess() {

    const successPopup =
        document.getElementById(
            'bookingSuccessPopup'
        );


    if (successPopup) {

        successPopup.classList.remove(
            'active'
        );

    }

}



// ======================================================
// PAGE LOAD
// ======================================================

window.addEventListener(
    'DOMContentLoaded',
    async () => {

        await loadBookings();

        generateCalendar();

        renderTimes();

        updateLiveSummary();

    }
);