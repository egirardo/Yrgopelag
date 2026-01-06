<?php require_once __DIR__ . '/app/autoload.php'; ?>
<?php require_once __DIR__ . '/views/header.php'; ?>

<?php $rooms = getAllRooms($db);  ?>
<div class="hero">
    <div class="hero-image">
        <img src="assets/images/hero.png" alt="beautiful luxury resort in forest">
    </div>
    <div class="review-buttons-container">
        <div class="review-slider-container">
            <div class="review-slides-wrapper">
                <div class="review-slides-inner">
                    <div class="review-card">
                        <p class="review-text">"A fantastic place to stay! My kids loved the theme park and our family had a wonderful time at Sbargle's."</p>
                        <p class="author-name">Samantha Pritchard</p>
                    </div>
                    <div class="review-card">
                        <p class="review-text">"Sbargle was a gracious host and you can tell that he really cares for his guests."</p>
                        <p class="author-name">Demi Moore</p>
                    </div>
                    <div class="review-card">
                        <p class="review-text">"I really loved the casino. Beach by day, casino at night, what could be better?"</p>
                        <p class="author-name">Stephen Thomas</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="button-container">
            <a href="about.php"><button class="btn btn-info">Learn More</button></a>
            <a href="#booking-section"><button class="btn btn-primary">Book Your Stay</button></a>
        </div>
    </div>
</div>
<section class="overview">
    <div class="the-resort">
        <h3>The Resort</h3>
        <p>Located on the island of Humanitopia, Sbargle's Luxury Building is a resort that offers guests a variety of stay options and several activities that humans enjoy. With beachfront views, Sbargle's is a paradise sure to bring you relaxation and comfort. Designed for year-round escapes, the temperature on the island is on average a comfortable 24°, perfect for swimming in the ocean or enjoying our amenities no matter the season.</p>
    </div>
</section>
<section class="on-offer">
    <div class="activities">

    </div>
</section>
<div class="booking-title" id="booking-section">
    <h1 class="title">Book Your Stay Now!</h1>
</div>
<div class="rooms-display">
    <?php foreach ($rooms as $room) : ?>
        <div class="room">
            <div class="room-image-container">
                <img src="<?= $room['image']; ?>" alt="<?= $room['rank']; ?> Room">
            </div>
            <div class="room-info-container">
                <h2><?= $room['rank']; ?> Room</h2>
                <p><?= $room['description']; ?></p>
                <a href="book.php?room_id=<?= $room['id'] ?>"><button type="button" class="btn btn-primary button-grow">Book Now!</button></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . "/views/footer.php"; ?>