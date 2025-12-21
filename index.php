<?php require __DIR__ . '/app/autoload.php'; ?>
<?php require __DIR__ . '/views/header.php'; ?>

<div class="rooms-display">
    <?php foreach ($rooms as $room) : ?>
        <div class="room">
            <div class="room-image-container">
                <img src="<?= $room['image']; ?>" alt="<?= $room['rank']; ?> Room">
            </div>
            <div class="room-info-container">
                <h2><?= $room['rank']; ?> Room</h2>
                <?php if ($room['rank'] == 'Budget') : ?>
                    <p>Sbargle's budget room features all of the basic needs that a human could need to stay here, a bed to sleep in and a communal bathroom available at the end of the hall. (But Sbargle is nice and at least gave you a TV. Humans really seem to enjoy those things.)</p>
                <?php elseif ($room['rank'] == 'Standard') : ?>
                    <p>Our standard room features a larger, more comfy bed than our budget option. Alongside the comfy bed, you will find your own personal bathroom. This room also includes a complimentary bottle of fermented grape juice, chilled and waiting for you on the table.</p>
                <?php elseif ($room['rank'] == 'Luxury') : ?>
                    <p>Our luxury room is the height of human experience that one can have at a luxury building. A beachfront view, accompanied by the Grand Vividus mattress from Hästens for optimum sleeping conditions. Sbargle has observed that humans are often lacking on rest, so Sbargle wants to create the most comfortable place for humans to rest. The room has plenty of space for social interactions as well, so feel free to invite companions to spend time with you in our luxury room.</p>
                <?php endif; ?>
                <a href="book.php"><button type="button" class="btn btn-primary">Book Now!</button></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . "/views/footer.php"; ?>