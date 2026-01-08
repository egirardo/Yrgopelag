<?php require_once __DIR__ . '/app/autoload.php'; ?>
<?php require_once __DIR__ . '/views/header.php'; ?>

<?php $rooms = getAllRooms($db);  ?>
<?php $features = getAllFeatures($db);  ?>

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
                        <p class="author-name">-Samantha Pritchard, August 2025</p>
                    </div>
                    <div class="review-card">
                        <p class="review-text">"Sbargle was a gracious host and you can tell that he really cares for his guests."</p>
                        <p class="author-name">-Demi Moore, November 2025</p>
                    </div>
                    <div class="review-card">
                        <p class="review-text">"I really loved the casino. Beach by day, casino at night, what could be better?"</p>
                        <p class="author-name">-Stephen Thomas, December 2025</p>
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
    <div class="resort-img">
        <img src="assets/images/resort.png">
    </div>
</section>
<section class="on-offer">
    <div class="activities-title">
        <h3>Play while you stay!</h3>
        <p>Be sure to check out all that Sbargle has to offer during your stay at Sbargle's Luxury Building!</p>
    </div>

    <div class="activities-slider-container">
        <div class="activities-wrapper">
            <?php foreach ($features as $feature) : ?>
                <div class="activities">
                    <div class="activity-img">
                        <img src="<?= $feature['image']; ?>" alt="<?= $feature['feature']; ?>">
                    </div>
                    <div class="activity-info">
                        <h4><?= $feature['feature']; ?></h4>
                        <p><strong>Price:</strong> $<?= $feature['price']; ?></p>
                        <p><?= $feature['description']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="slider-controls">
        <button id="slider-prev" class="slider-btn" aria-label="Previous activity">
            &#8249;
        </button>
        <div class="slider-dots"></div>
        <button id="slider-next" class="slider-btn" aria-label="Next activity">
            &#8250;
        </button>
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