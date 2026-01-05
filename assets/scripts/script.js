// Reviews slider on index page
const innerContainer = document.querySelector('.review-slides-inner');
const cards = document.querySelectorAll('.review-card');
let currentIndex = 0;
const totalCards = cards.length;

function scrollToNextReview() {
  currentIndex = (currentIndex + 1) % totalCards;
  const offset = -currentIndex * 102;
  innerContainer.style.transform = `translateX(${offset}%)`;
}

setInterval(scrollToNextReview, 4000);

// Helper to ensure that end date cannot come before start date
document.addEventListener("DOMContentLoaded", () => {
  const startDateInput = document.getElementById("start_date");
  const endDateInput = document.getElementById("end_date");

  startDateInput.addEventListener("change", function() {
    endDateInput.min = this.value;

    if (endDateInput.value && endDateInput.value < this.value) {
      endDateInput.value = "";
    }
  });

  if (startDateInput.value) {
    endDateInput.min = startDateInput.value;
  }
});