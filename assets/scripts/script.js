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
