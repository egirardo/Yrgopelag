(() => {
    const form = document.getElementById('selection');
    if (!form) return;

    const roomPrice = parseInt(form.dataset.roomPrice, 10);

    const start = document.getElementById('start_date');
    const end   = document.getElementById('end_date');
    const boxes = form.querySelectorAll('input[name="activities[]"]');
    const total = document.getElementById('totalCost');

    function nights() {
        if (!start.value || !end.value) return 0;
        return Math.max(
            (new Date(end.value) - new Date(start.value)) / 86400000,
            0
        );
    }

    function activityTotal() {
        let sum = 0;
        boxes.forEach(b => b.checked && (sum += +b.dataset.price));
        return sum;
    }

    function update() {
        const t = nights() * roomPrice + activityTotal();
        total.value = t ? `$${t}` : '';

        // Quick-Win #3: expose the computed total so the transferCode
        // offcanvas can auto-populate its "Amount" field.
        form.dataset.bookingTotal = t;
        document.dispatchEvent(new CustomEvent('bookingTotalUpdated', {
            detail: { total: t }
        }));
    }

    start.addEventListener('change', update);
    end.addEventListener('change', update);
    boxes.forEach(b => b.addEventListener('change', update));

    // Submit loading state: prevent double-clicks and give feedback
    // while the booking is being processed server-side.
    const submitBtn = document.getElementById('booking-submit-btn');
    const btnText = document.getElementById('booking-btn-text');
    const btnSpinner = document.getElementById('booking-btn-spinner');

    const setLoadingState = (isLoading) => {
        if (!submitBtn || !btnText || !btnSpinner) return;

        submitBtn.disabled = isLoading;
        btnText.textContent = isLoading ? 'Completing Booking...' : 'Complete Booking';

        if (isLoading) {
            btnSpinner.classList.remove('hidden');
        } else {
            btnSpinner.classList.add('hidden');
        }
    };

    form.addEventListener('submit', () => {
        // Guard against double submissions; the real submission is
        // allowed to proceed (no preventDefault) since this is a
        // plain form POST to process_booking.php.
        setLoadingState(true);
    });

    // If the form is restored from the back/forward cache (e.g. the
    // user hits "back" after an error redirect) make sure the button
    // isn't left stuck in its disabled/loading state.
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            setLoadingState(false);
        }
    });
})();