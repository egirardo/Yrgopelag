// Quick-Win #1: Clickable calendar
// Lets a guest pick their stay by clicking days directly on the availability
// grid instead of only using the native date inputs.
document.addEventListener('DOMContentLoaded', () => {
    const calendar = document.getElementById('booking-calendar');
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const feedback = document.getElementById('calendar-feedback');
    const monthTitle = document.getElementById('calendar-month-title');

    if (!calendar || !startInput || !endInput) return;

    let days = Array.from(calendar.querySelectorAll('.day[data-date]'));

    // Quick-Win #7: track which month is currently rendered so we know
    // when the availability grid needs to be swapped out for another one.
    let currentYear = parseInt(calendar.dataset.year, 10);
    let currentMonth = parseInt(calendar.dataset.month, 10);

    function setFeedback(message) {
        if (feedback) feedback.textContent = message;
    }

    function refreshCalendarDisplay() {
        const startValue = startInput.value;
        const endValue = endInput.value;

        days.forEach((day) => {
            day.classList.remove('selected-start', 'selected-end', 'in-range');
            const date = day.dataset.date;

            if (startValue && date === startValue) {
                day.classList.add('selected-start');
            }

            if (endValue && date === endValue) {
                day.classList.add('selected-end');
            }

            if (startValue && endValue && date > startValue && date < endValue) {
                day.classList.add('in-range');
            }
        });
    }

    function flashUnavailable(dayEl) {
        dayEl.classList.add('shake');
        setFeedback('That date is already booked and unavailable.');
        window.setTimeout(() => dayEl.classList.remove('shake'), 500);
    }

    function fireChange(input) {
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function selectDate(date) {
        if (!startInput.value) {
            // Nothing selected yet: this click becomes the start date.
            startInput.value = date;
            setFeedback('Start date set to ' + date + '. Choose an end date.');
        } else if (!endInput.value) {
            if (date <= startInput.value) {
                // Clicked a day before (or the same as) the chosen start date;
                // treat it as picking a new start date rather than erroring out.
                startInput.value = date;
                setFeedback('Start date updated to ' + date + '. Choose an end date.');
            } else {
                endInput.value = date;
                setFeedback('End date set to ' + date + '.');
            }
        } else {
            // Both dates were already set: start a fresh selection.
            startInput.value = date;
            endInput.value = '';
            setFeedback('Selection reset. Start date set to ' + date + '. Choose an end date.');
        }

        fireChange(startInput);
        fireChange(endInput);
        refreshCalendarDisplay();
    }

    function bindDay(day) {
        const isBooked = day.classList.contains('booked');

        if (!isBooked) {
            day.setAttribute('role', 'button');
            day.setAttribute('tabindex', '0');
        }

        day.addEventListener('click', () => {
            if (day.classList.contains('booked')) {
                flashUnavailable(day);
                return;
            }

            selectDate(day.dataset.date);
        });

        day.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                day.click();
            }
        });
    }

    days.forEach(bindDay);
    refreshCalendarDisplay();

    // Quick-Win #7: when the start_date field ends up pointing at a month
    // other than the one currently drawn on the grid (whether the guest
    // typed/picked a date in the native input, or code elsewhere changed
    // it), fetch that month's availability and redraw the grid in place.
    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function buildDayElement(dayNumber, year, month, bookedDays) {
        const dateStr = year + '-' + pad(month) + '-' + pad(dayNumber);
        const dayEl = document.createElement('div');
        dayEl.className = bookedDays.includes(dayNumber) ? 'day booked' : 'day';
        dayEl.dataset.date = dateStr;
        dayEl.textContent = String(dayNumber);
        return dayEl;
    }

    function loadMonth(year, month) {
        const roomId = calendar.dataset.roomId;
        if (!roomId) return;

        const url = '/app/users/get_booked_days.php'
            + '?room_id=' + encodeURIComponent(roomId)
            + '&year=' + encodeURIComponent(year)
            + '&month=' + encodeURIComponent(month);

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                if (!data || !data.success) return;

                calendar.innerHTML = '';

                // Add weekday headers
                const headerHTML = `
                    <div class="weekday-headers">
                        <div class="weekday">Sun</div>
                        <div class="weekday">Mon</div>
                        <div class="weekday">Tue</div>
                        <div class="weekday">Wed</div>
                        <div class="weekday">Thu</div>
                        <div class="weekday">Fri</div>
                        <div class="weekday">Sat</div>
                    </div>
                `;
                calendar.innerHTML = headerHTML;

                // Get first day of month (0=Sunday, 1=Monday, etc.)
                const firstDate = new Date(data.year, data.month - 1, 1);
                const firstDayOfWeek = firstDate.getDay();

                // Add placeholder cells for days before the 1st
                for (let i = 0; i < firstDayOfWeek; i++) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'day placeholder';
                    calendar.appendChild(placeholder);
                }

                const newDays = [];
                for (let i = 1; i <= data.daysInMonth; i++) {
                    const dayEl = buildDayElement(i, data.year, data.month, data.bookedDays);
                    calendar.appendChild(dayEl);
                    newDays.push(dayEl);
                }

                days = newDays;
                days.forEach(bindDay);

                currentYear = data.year;
                currentMonth = data.month;
                calendar.dataset.year = String(data.year);
                calendar.dataset.month = String(data.month);

                if (monthTitle) monthTitle.textContent = data.monthName;

                refreshCalendarDisplay();
            })
            .catch(() => {
                // Network/endpoint hiccup: leave the previously rendered
                // month in place rather than breaking the page.
            });
    }

    function maybeSwitchMonth() {
        if (!startInput.value) return;

        const parts = startInput.value.split('-').map(Number);
        const year = parts[0];
        const month = parts[1];

        if (!year || !month) return;

        if (year !== currentYear || month !== currentMonth) {
            loadMonth(year, month);
        }
    }

    startInput.addEventListener('change', maybeSwitchMonth);

    // Month navigation buttons
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            let newMonth = currentMonth - 1;
            let newYear = currentYear;
            if (newMonth < 1) {
                newMonth = 12;
                newYear -= 1;
            }
            loadMonth(newYear, newMonth);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            let newMonth = currentMonth + 1;
            let newYear = currentYear;
            if (newMonth > 12) {
                newMonth = 1;
                newYear += 1;
            }
            loadMonth(newYear, newMonth);
        });
    }
});
