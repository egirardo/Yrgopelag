<?php require_once __DIR__ . '/app/autoload.php'; ?>
<?php require_once __DIR__ . '/app/functions.php'; ?>
<?php require_once __DIR__ . '/views/header.php'; ?>

<?php $rooms = getAllRooms($db);  ?>

<div class="hero">
    <img src="/assets/images/hero.png" alt="beautiful luxury resort in forest">
</div>
<div class=" rooms-display">
    <?php foreach ($rooms as $room) : ?>
        <div class="room">
            <div class="room-image-container">
                <img src="<?= $room['image']; ?>" alt="<?= $room['rank']; ?> Room">
            </div>
            <div class="room-info-container">
                <h2><?= $room['rank']; ?> Room</h2>
                <p><?= $room['description']; ?></p>
                <a href="book.php?room_id=<?= $room['id'] ?>"><button type="button" class="btn btn-primary">Book Now!</button></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . "/views/footer.php"; ?>