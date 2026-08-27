document.addEventListener("DOMContentLoaded", () => {
 
// Reviews slider on index page
const innerContainer = document.querySelector('.review-slides-inner');
const cards = document.querySelectorAll('.review-card');

    if (innerContainer && cards.length > 0) {
        let currentIndex = 0;
        const totalCards = cards.length;

    function scrollToNextReview() {
        currentIndex = (currentIndex + 1) % totalCards;
        const offset = currentIndex * -100;
        const gapOffset = currentIndex * -10;
        innerContainer.style.transform = `translateX(calc(${offset}% + ${gapOffset}px))`;
        }

    setInterval(scrollToNextReview, 4000);
  }

// Date validation (Quick-Win #2)
const startDateInput = document.getElementById("start_date");
const endDateInput = document.getElementById("end_date");
const bookingForm = document.getElementById("selection");
const formErrorContainer = document.getElementById("form-error-container");
const formErrorMessage = document.getElementById("form-error-message");

if (startDateInput && endDateInput) {

    function showFormError(message) {
        if (!formErrorContainer || !formErrorMessage) return;
        formErrorMessage.textContent = message;
        formErrorContainer.classList.remove("hidden");
        formErrorContainer.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function clearFormError() {
        if (!formErrorContainer) return;
        formErrorContainer.classList.add("hidden");
    }

    // The end date must be strictly after the start date (no 0/negative night stays).
    function updateEndMin() {
        if (!startDateInput.value) return;
        const nextDay = new Date(startDateInput.value + "T00:00:00");
        nextDay.setDate(nextDay.getDate() + 1);
        endDateInput.min = nextDay.toISOString().slice(0, 10);
    }

    startDateInput.addEventListener("change", function() {
        updateEndMin();

        if (endDateInput.value && endDateInput.value <= this.value) {
            endDateInput.value = "";
        }

        clearFormError();
    });

    endDateInput.addEventListener("change", clearFormError);

    if (startDateInput.value) {
        updateEndMin();
    }

    if (bookingForm) {
        bookingForm.addEventListener("submit", function(event) {
            const requiredFields = bookingForm.querySelectorAll("[required]");
            let hasMissingField = false;

            requiredFields.forEach((field) => {
                if (!field.value || !field.value.trim()) {
                    hasMissingField = true;
                }
            });

            if (hasMissingField) {
                event.preventDefault();
                event.stopImmediatePropagation();
                showFormError("Please fill in all required fields before submitting.");
                return;
            }

            if (endDateInput.value <= startDateInput.value) {
                event.preventDefault();
                event.stopImmediatePropagation();
                showFormError("End date must be after the start date.");
                return;
            }

            clearFormError();
        });
    }
}
});