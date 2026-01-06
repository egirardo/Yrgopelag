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

// Date validation
const startDateInput = document.getElementById("start_date");
const endDateInput = document.getElementById("end_date");

if (startDateInput && endDateInput) {
    startDateInput.addEventListener("change", function() {
        endDateInput.min = this.value;

        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = "";
        }
    });

        if (startDateInput.value) {
            endDateInput.min = startDateInput.value;
        }
        }
});